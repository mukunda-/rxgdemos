<?php

require_once "sql.php";
require_once "main.php";

function GetTimeQuery( $name ) {
	if( !isset($_GET[$name]) ) return 0;
	$a = strtotime( $_GET[$name] );
	if( $a === FALSE ) {
		echo "<p>Invalid Time String: \"".$_GET[$name]."\"</p>";
		return 0;
	}
	return $a;
}


$today_start		= mktime( 0, 0, 0, date("n"), date("j"), date("Y") );
$now				= time(); 
$yesterday_start	= $today_start - 24*60*60;//mktime( date("H"), date("i"), date("s"), date("n"), date("j"), date("Y") );
$windows			= strtolower(substr(PHP_OS,0,3)) == "win";

function DemoListTime( $time ) {
	global $today_start, $now, $yesterday_start, $windows;
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
	
	if( !$windows ) {
		if( $time >= $today_start ) {
			return strftime( "Today, %l:%M %p", $time ) . " ($a $unit ago)";
		} else if( $time >= $yesterday_start ) {
			return strftime( "Yesterday, %l:%M %p", $time ) . " ($a $unit ago)";
		} 
		return strftime( "%a %b %e %Y, %l:%M %p", $time ) . " ($a $unit ago)";
	} else {
		if( $time >= $today_start ) {
			return strftime( "Today, %#I:%M %p", $time ) . " ($a $unit ago)";
		} else if( $time >= $yesterday_start ) {
			return strftime( "Yesterday, %#I:%M %p", $time ) . " ($a $unit ago)";
		} 
		return strftime( "%a %b %#d %Y, %#I:%M %p", $time ) . " ($a $unit ago)";	
	}
}


$month = 0;
$day = 0;
$map = "";
$moment = GetTimeQuery("moment");
$names="";
$chat="";
$steams="";
$server="";
$starttime=GetTimeQuery("starttime");
$endtime=GetTimeQuery("endtime");
$streak=0;
$user=0;

//print_r( $_GET  ); echo '<br>';

if( isset($_GET['server']) ) $server = (int)$_GET['server'];
if( isset($_GET['month']) ) $month = (int)$_GET['month'];
if( isset($_GET['day']) ) $day = (int)$_GET['day'];
if( isset($_GET['map']) ) $map = $_GET['map']; 
if( isset($_GET['names']) ) $names = $_GET['names'];
if( isset($_GET['chat']) ) $chat = $_GET['chat'];
if( isset($_GET['steams']) ) $steams = $_GET['steams'];
if( isset($_GET['user']) ) {
	$user = $_GET['user'];
	if( !preg_match( "/^[0-9]+$/", $user ) ) {
		$user = "";
	}
}

if( isset($_GET['streak']) ) $streak = (int)$_GET['streak'];
$start = isset($_GET['start'])?(int)$_GET['start']:0;

$names = substr($names,0,256);
$chat = substr($chat,0,256);
$steams = substr($steams,0,512);

$sql = GetSQL();
$conditions = array();
if( $server != "" ) {
	$conditions[] = "SERVER=$server";
}
if( $month != 0 ) {
	$conditions[] = "MONTH(FROM_UNIXTIME(`TIME`))=$month";
}
if( $day != 0 ) {
	$conditions[] = "DAYOFMONTH(FROM_UNIXTIME(`TIME`))=$day";
}
if( $map != "" ) {
	$a = $sql->real_escape_string($map);
	$conditions[] = "MAP LIKE '%$a%'";
}
if( $moment != 0 ) {
	$conditions[] = "`TIME`<=$moment AND (`TIME`+DURATION)>$moment";
} 
if( $starttime != 0 ) {
	$conditions[] = "`TIME`>=$starttime";
}
if( $endtime != 0 ) {
	$conditions[] = "`TIME`<=$endtime";
}
if( $names != "" ) {
	$a = $sql->real_escape_string($names);
	$conditions[] = "MATCH(NAMES) AGAINST ('$a' IN BOOLEAN MODE)";
}
if( $chat != "" ) {
	$a = $sql->real_escape_string($chat);
	$conditions[] = "MATCH(CHAT) AGAINST ('$a' IN BOOLEAN MODE)";
}
 


$query = "SELECT INFO.ID, FILE, SERVER, GAME, MAP, `TIME`, DURATION, SCORE1, SCORE2";
if( $user != "" ) {
	$query .= ", (SelfCheck.ACCOUNT IS NOT NULL) AS OWNMATCH";
}
if( $streak > 0 ) {
	$query .= ", MAX(PLAYERS.BIGSTREAK) AS MAXSTREAK"; 
}
$query .= " FROM INFO";
if( $user != "" ) {
	$query .= " LEFT JOIN PLAYERS AS SelfCheck ON INFO.ID=SelfCheck.MATCHID AND SelfCheck.ACCOUNT=$user";
}

if( $steams != "" ) {
	$steams = str_replace( "\r","\n",$steams);
	$steams = explode("\n",$steams);
	$steams2 = array();
	$dups = array();
	foreach( $steams as $k => $i ) {
		$i = trim($i);
		if( $i == "" ) continue;
		$a = ParseSteamID($i);
		if( $a !== FALSE && !in_array($a,$steams2) ) {
			$steams2[] = $a;
			if( count( $steams2 ) > 32 ) break; // something fishy is going on
		} else {
			echo "<p>Invalid Steam User: \"$i\"</p>";
		}
	}
	if( count( $steams2 ) > 0 ) {
		$steams2 = implode( ",", $steams2 );
		$query .= " INNER JOIN `PLAYERS` ON INFO.ID = PLAYERS.MATCHID AND PLAYERS.ACCOUNT IN ($steams2)";
		if( $streak > 0 ) {
			$query .= " AND PLAYERS.BIGSTREAK>=$streak";
		}
		//$groupclause = "GROUP BY `INFO`.ID";
		//$conditions[] = "PLAYERS.ACCOUNT IS NOT NULL";
	} else if( $streak > 0 ) {
		// full player scan
		$query .= " INNER JOIN `PLAYERS` ON INFO.ID = PLAYERS.MATCHID AND PLAYERS.BIGSTREAK>=$streak";
	}
} else if( $streak > 0 ) {
	// full player scan
	$query .= " INNER JOIN `PLAYERS` ON INFO.ID = PLAYERS.MATCHID AND PLAYERS.BIGSTREAK>=$streak";
}
 
$conditions = trim(implode( " AND " , $conditions ));
if( $conditions != "" ) $conditions = " WHERE $conditions";
$query .= $conditions;
$query .= " GROUP BY `INFO`.ID ORDER BY `TIME` DESC LIMIT $start,26";
 
//$query = "SELECT `INFO`.ID,`SERVER`,`GAME`,`MAP`,`TIME`,`DURATION`,`SCORE1`,`SCORE2` FROM `INFO` $joinclause $conditions $groupclause ORDER BY `TIME` DESC LIMIT $start,26";
//echo '<span style="font-size:10px">';
//echo "QUERY=$query<br>";
$result = $sql->safequery( $query );

$content = '<table class="demolist">';
$content .= '<tr class="headerrow"><td><img src="loading.gif" style="display:none" id="demolist_loading"></td><td>Server</td><td>Map</td><td>Time</td><td class="centered">Duration</td><td class="centered">Score</td><td class="centered">DL</td></tr>';

$count = 0;
$more = FALSE;
while( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
	if( $count == 25 ) {
		$more = TRUE;
		break;
	}

//	print_r( $row );
$score = $row['SCORE1'] . ' - ' . $row['SCORE2'];
$tabstyle = 'tab';
if( isset($row['OWNMATCH']) ) {
	if( $row['OWNMATCH'] != 0 ) {
		$tabstyle = 'tab self';
	}
}
$mapimage = 'maps/unknown_map.jpg';
if( file_exists('maps/'. $row['MAP'] .'.jpg') ) {
	$mapimage = 'maps/'. $row['MAP'] .'.jpg';
} else if( file_exists('maps/'. $row['MAP'] .'.png') ) {
	$mapimage = 'maps/'. $row['MAP'] .'.png';
}
$recentlyviewed="";
if( isset($_SESSION['viewed'][$row['ID']]) && $_SESSION['viewed'][$row['ID']] > 0 ) {
	$recentlyviewed = " recentlyviewed";
}

$content .= '
	<tr class="demoentry'.$recentlyviewed.'" onclick="ViewDemo(this,'.$row['ID'].')"> 
		<td class="mapimage" title="'.$row['MAP'].'" style="background: url(\''.$mapimage.'\') no-repeat center center; width:64px">&nbsp;</td>
		<td>'.GetServername( $row['SERVER'] ).'</td>
		<td>'.$row['MAP'].'</td>
		<td>'.DemoListTime($row['TIME']).'</td>
		<td class="centered">'.FormatDuration($row['DURATION'],true).'</td>
		<td class="centered">'.$score.'</td>
		<td class="downloadlink" style="width:32px" ><a class="fastdl_link" href="demos/'.$row['FILE'].'.zip"><div></div></a></td>
		<td class="'.$tabstyle.'"><div>&nbsp;</div></td>
	</tr>';
	
	$count++;
}
$content .= '</table>';

if( $count == 0 ) {
	$content = '
	
		<table class="no_matches_found" ><tr><td>No matches found.<br><img src="loading.gif" style="display:none" id="demolist_loading"></td></tr></table>
	';
}
echo $content;

//echo '</span">';



if( $more ) {
	$start += 25;
	
	/*
	$params = array();
	$params[] = array( "start=$start" );
	if( $month != 0 ) $params[] = array( "month=$month" );
	if( $day != 0 ) $params[] = array( "day=$day" );
	if( $map != "" ) $params[] = array( "map=$map" );
	if( $moment != 0 ) $params[] = array( "moment=$moment" );
	if( $names != "" ) $params[] = array( "names=$names" );
	if( $chat != "" ) $params[] = array( "chat=$chat" );
	echo '<p><a href="' . implode( "&", $params ) . '">More...</a></p>';*/
}

?>