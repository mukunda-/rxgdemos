<?php

require_once 'openid.php';
require_once "config.php";

$domain = $_SERVER['HTTP_HOST'];
$apath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\').'/'; 
$gpath = "http://$domain$apath";

require_once "sql.php";

Create_htaccess();

$servers = array();
OpenSession();
ProcessLogin();

GetServerList();

function Create_htaccess() {
	if( !file_exists('.htaccess') ) {
		file_put_contents( '.htaccess',
			"
Options +FollowSymLinks
RewriteEngine On 
RewriteRule ^([0-9]+)$ ".$GLOBALS['apath']."view.php?demo=$1 [L,QSA]

			"
		);
	}
}


function OpenSession( ) {
	global $apath;
	$session_timeout = 1440;
	session_set_cookie_params( $session_timeout, $apath );
	session_start(); 
	setcookie(session_name(),session_id(),time()+$session_timeout,$apath);
	if(isset($_GET['logout'])) {
		$_SESSION['loggedin'] = 0; 
	}
	if(isset($_SESSION['remotehost'])) { 
		if( $_SESSION['remotehost'] != $_SERVER['REMOTE_ADDR'] ) { 
			$_SESSION = array(); // ip mismatch, erase session
			$_SESSION['remotehost'] = $_SERVER['REMOTE_ADDR'];
		}
	} else { 
		$_SESSION = array(); // no ip, erase session
		$_SESSION['remotehost'] = $_SERVER['REMOTE_ADDR'];
	}
	if(!isset($_SESSION['sessionstart'])) { 
		$_SESSION['sessionstart'] = 1;
		$_SESSION['loggedin'] = 0; 
		$_SESSION['viewed'] = array();
	}
}


//---------------------------------------------------------------------------------------------
function GetUserData( $steamid ) {
	global $steamapikey;
	$data = GetContents( "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$steamapikey&steamids=$steamid" );
	$list =json_decode( $data )->response->players;
	if( !isset( $list[0] ) ) return FALSE;
	return $list[0];
}

//---------------------------------------------------------------------------------------------
function LogInUser( $stid ) { 
	$userdata = GetUserData( $stid );
	if( $userdata == FALSE ) {
		echo 'error logging in.';
	} else {
		$_SESSION['loggedin'] = 1;
		$_SESSION['steamid'] = $stid;
		$_SESSION['loggedin_name'] = $userdata->personaname;
		$_SESSION['avatar'] = $userdata->avatar;
		
		$_SESSION['admin'] = 0;
		
		foreach( $GLOBALS['admins'] as $key => $admin ) {
			if( $stid == $admin['id'] ) {
				$_SESSION['adminid'] = $key;
				$_SESSION['admin'] = 1;
			}
		}
	}
}

//---------------------------------------------------------------------------------------------
function SteamIdFromOpenId( $openid ) {
	return substr( $openid, strrpos($openid, "/")+1 );
}
//---------------------------------------------------------------------------------------------
function ProcessLogin() {
	try {
		# Change 'localhost' to your domain name.
		$openid = new LightOpenID($GLOBALS['domain']);
		if(!$openid->mode) {
			if(isset($_GET['login'])) { 
				$openid->identity = 'http://steamcommunity.com/openid';
				header('Location: ' . $openid->authUrl());
			}
			
			
		} elseif($openid->mode == 'cancel') {
			//echo 'User has canceled authentication!';
		} else {
			//echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.<br>';
			if( $openid->validate() ) {
				$stid = SteamIdFromOpenId( $openid->identity );
				LogInUser( $stid  );
			}
		}
	} catch(ErrorException $e) {
		echo 'auth error: ' . $e->getMessage();
	}
}
 

//---------------------------------------------------------------------------------------
function GetServerList() {
	
	global $servers;
	$servers = array();
	$sql = GetSQL();
	
	$result = $sql->safequery( "SELECT * FROM SERVERS" );
	while( $row = $result->fetch_array() ) {
		$servers[$row['ID']] = array(
			'id' => $row['ID'],
			'name' => $row['NAME'],
			'game' => $row['GAME']
		);
	} 
}

function GetServerName( $index ) {
	global $servers;
	return isset($servers[$index]) ? $servers[$index]['name'] : "UNKNOWN";
	
}

//---------------------------------------------------------------------------------------------
function GetContents($url) {
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);

	$data = curl_exec($ch); 
	
	curl_close($ch);

	return $data;
}

//---------------------------------------------------------------------------------------------
function ResolveVanityURL( $id ) {
	global $steamapikey;
	$data = json_decode( GetContents( "http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?vanityurl=$id&key=$steamapikey" ) )->response;
	if( !isset($data->success) || $data->success != 1 ) return FALSE;
	return $data->steamid;
}

function FormatDuration( $duration, $short=false ) {
	$minutes = floor($duration / 60);
	$seconds = $duration - $minutes*60.0;
	
	if( $short ) return sprintf( "%02d:%02d", $minutes,(int)$seconds );
	return sprintf( "%02d:%05.2f", $minutes,$seconds );
 
}

//---------------------------------------------------------------------------------------------
function FormatDemoTime( $time ) {
	$a = time() - $time;
	$unit=0;
	if( $a < 60 ) { // 0-59 seconds
		$unit = $a == 1 ? "second":"seconds";
	} else if( $a < (60*60) ) { // 1-59 minutes
		$a = round($a / 60);
		$unit = $a == 1 ? "minute":"minutes";
	} else if( $a < (60*60*48) ) { // 1-47 hours
		$a = round($a/(60*60));
		$unit = $a == 1 ? "hour":"hours";
	} else if( $a < (60*60*24*90)) { // 2-90 days
		$a = round($a/(60*60*24));
		$unit = $a == 1 ? "day":"days";
	} else { // months
		$a = round($a/(60*60*24*30.4368));
		$unit = $a == 1 ? "month":"months";
	}
	return strftime( "%a %b %#d %Y, %#I:%M %p", $time ) . " ($a $unit ago)";
}

//---------------------------------------------------------------------------------------------
function AccountFromSteamID64( $steamid64 ) {
	$account = bcsub( $steamid64, "76561197960265728" ); 
	return $account;
}

function ParseSteamID( $str ) {
	 
	// parses ID and returns valid steam community url
	$str = trim($str);
	
	if( strtolower($str) == 'me' ) {
		if( isset( $_SESSION['account'] ) ) {
			return $_SESSION['account'];
		} 	
		return FALSE;
	}
	
	if( preg_match( '/^(http:\/\/)*(www.)*steamcommunity.com\/profiles\/(?P<idmatch>[0-9]+)\/?$/', $str, $matches ) == 1 ) {
		// community ID match 
		return AccountFromSteamID64($matches['idmatch']);
	}
	
	if( preg_match( '/^765(?P<idmatch>[0-9]+)\/?$/', $str, $matches ) == 1 ) {
		// short community ID match
		return AccountFromSteamID64("765".$matches['idmatch']);
	}
	
	if( preg_match( '/^((http:\/\/)*(www.)*steamcommunity.com\/id\/)*(?P<idmatch>[a-zA-Z0-9_]+)\/?$/', $str, $matches ) == 1 ) {
		// custom URL match
		$a = ResolveVanityURL( $matches['idmatch'] );
		if( $a === FALSE ) return FALSE;
		return AccountFromSteamID64( $a );
	}
	
	if( preg_match( '/^STEAM_[0-1]:(?P<Y>[0-1]):(?P<Z>[0-9]+)$/', $str, $matches ) ) {
		// Steam ID match
		
		$b = $matches['Z'];
		$b = bcmul( $b, 2 );
		$b = bcadd( $b, $matches['Y'] );
		$b = bcadd( $b, "76561197960265728" );
		return AccountFromSteamID64( $b ); 
	}
	
	return FALSE; 
}


function RecentDemos() {
	echo '<table class="demoshort">';
	global $servers; 
	$sql = GetSQL();
	$result = $sql->safequery( "SELECT SERVER,DURATION,TIME,SCORE1,SCORE2 FROM INFO ORDER BY TIME DESC LIMIT 10" );
	$time = time();
	while( $r = $result->fetch_array() ) {
		$serv = $servers[$r['SERVER']];
		$name = $serv['name'];
		if( strlen($name) > 6 ) $name = substr($name,0,4).".";
		
		$dtime = $time - $r['TIME'];
		if( $dtime < 60 ) {
			$dtime = $dtime . " secs ago";
		} else if( $dtime < 60*60 ) {
			$dtime = round($dtime/60) . " mins ago";
		} else {
			$dtime = round($dtime/(60*60),1) . " hrs ago";
		}
		$icon = $serv['game'];
		if( file_exists( "$icon.png" ) ) {
			$icon = "<img src='$icon.png' height='16px'>";
		}
		echo '<tr><td style="width:16px;height:16px;padding:0px">'.$icon.'</td><td>'.$name.'</td><td>'.$dtime.'</td><td style="text-align:center">'.$r['SCORE1'].' - '.$r['SCORE2'].'</td></tr>';
		
	}
	echo '</table>';
}

function StartPage() {

	?><html>
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300,400italic,300italic,700italic' rel='stylesheet' type='text/css'>
		<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>-->
		<link rel="stylesheet" type="text/css" href="style.php"> 
		<title>RXG Demo Archive</title>
	</head>

	<body>
		<div class="backdrop"></div>
		
		<center>
			<h3><a href="."><img src="rxg.png"><br>DEMO ARCHIVE</a></h3>
		</center>
		<div class="window">
		<div class="login">
		
			<?php
			if( !$_SESSION['loggedin'] ) {
				echo '<form action="?login" method="post">
					<input class="normal" type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_noborder.png" alt="Sign in through Steam">
				</form>';
			} else {
				
				echo '<div><a href="http://steamcommunity.com/profiles/"><img class="avatar" src="'.$_SESSION['avatar'].'" > '.$_SESSION['loggedin_name'].'</a>';
				echo ' <a class="logout" href="?logout">Log out</a></div>';
			}
			//<img src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_border.png">
			?>
			
		</div>
			<!--<div class="window_left">-->
			<!--
				<div class="content sidebar">
					<div class="contentheader">
						Navigation
					</div>
					<div class="contentbody">
					
						<?php
							
							echo '<a href="index.php">Find a Match</a><br>';
							
							
						?>
					</div>
					<div class="contentheader">
						Recent Demos
					</div>
					<div class="contentbody"><?php
				
						RecentDemos();
						
					
					
					?></div>
				</div>
			</div>
			<div class="window_right">
			-->
				<div class="content">
	<?php
	
}

function EndPage() {
	?>
		<!--		</div>-->
			</div>
		</div>
		<div style="clear:both"></div>
		<div class="footer">
			<p>
				<a href="http://reflex-gamers.com">www.reflex-gamers.com</a> | 
				<a href="http://steampowered.com">Powered by Steam</a>
				<?php
				
				if( $_SESSION['loggedin'] && $_SESSION['admin'] ) {
					echo " | <a href=\"admin.php\">Admin</a>";
					
				}
				
				?>
			</p>
			<p>&nbsp;</p>
		</div>
	</body>
	</html>
	<?php
}
