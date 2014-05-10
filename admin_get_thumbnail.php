<?php
 
require_once 'main.php';


if( !$_SESSION['loggedin'] ) die( "Not Logged In." );
if( !$_SESSION['admin'] ) die( "Not Admin." );

if( isset($_GET['workshopid']) ) {
	echo( "workshop thumbnail ripper v1.0.0<hr>" );
	$id = (int)$_GET['workshopid'];
	if( $id == 0 ) die("invalid ID");
	
	echo "workshopid=$id<br>";
	echo "retrieving from workshop...<br>";
	
	$a = json_decode(GetMapInfo( $id ));
	 
	if( $a === FALSE ) die( "<error>" );
	$a = $a->response;
	if( $a->result != 1 ) die( "borked" );
	$a = $a->publishedfiledetails[0];
	echo $a->description . '<br>';
	
	preg_match( "/^.*\/(.*)\.bsp$/", $a->filename, $poop );
	$mapname = $poop[1];
	PerformMagicOnURL( $mapname, $a->preview_url );
	
	
} else if( isset($_GET["url"])) {
	$mapname = trim($_GET["mapname"]);
	if( $mapname == "" ) die("pooped");
	$sourceurl = trim($_GET["url"]);
	if( $sourceurl == "" ) die("pooped");
	
	echo( "thumbnail ripper thing v1.0.0<hr>" );
	echo( "map=$mapname, url=$sourceurl<hr>" );
	
	PerformMagicOnURL( $mapname, $sourceurl );
}

function PerformMagicOnURL( $mapname, $imageurl ) {
	
	echo "<h2>$mapname</h2>";
	echo '<img src="'.$imageurl.'"><br>';
	echo '<hr>';
	echo 'raping image...<br>';
	
	$source = imagecreatefromjpeg( $imageurl );
	$sx = 0;
	$sy = 0;
	$sw =  imagesx($source); // source size
	$sh =  imagesy($source);
	
	
	// crop to 21:12 ratio
	$ratio = $sw/$sh;
	if( $ratio > 1.75 ) {
		$c = round($sh * 1.75);
		$sx = round(($sw-$c)/2);
		$sw = $c;
	} else if( $ratio < 1.75 ) {
		$c = round( $sw / 1.75 );
		$sy = ($sh-$c)/2;
		$sh = $c;
	}
	
	// resize to 84x48
	$dest = imagecreatetruecolor( 84,48 );
	imagecopyresampled( $dest, $source, 0, 0, $sx, $sy, 84, 48, $sw, $sh );
	
	echo 'hnnng,hnnng,hnnng,...<br>';
	
	echo "saving result (IN HIGH QUALITY 100/100) maps/$mapname.jpg...<br>";
	imagejpeg( $dest , "maps/$mapname.jpg", 100 );
	echo '<img src="'."maps/$mapname.jpg".'">';
	
	// thumbnail size=76x40
	// target = 84x48 (21:12/1.75 ratio)
	echo '<h3>DONE.</h3>';
	
}

//---------------------------------------------------------------------------------------------
function GetMapInfo( $id ) {
	
	global $steamapikey;
	$postdata = array( 
		"itemcount" => 1, 
		"publishedfileids" => array($id) 
		);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "http://api.steampowered.com/ISteamRemoteStorage/GetPublishedFileDetails/v1/?key=$steamapikey&format=json");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($postdata) ); //"publishedfileids[0]=aaa" );
	$data = curl_exec($ch); 
	curl_close($ch);
 

	return $data;
}


?>