<?php

require_once "config.php";
require_once "sql.php";
require_once "logparser2.php";
require_once "demoheader.php";
 
$param_server = "";
$param_demo = "";
$param_score = "";
$param_time = "";
$param_duration = "";

$server_id = 0;
$server_game = "";

if( !VerifyData() ) die();

date_default_timezone_set( "America/Chicago" );

if( !GetServerInfo() ) die();
//-------------------------------------------------------------------------------------------------
function CreateLogAccessRestriction() {
	if( file_exists("logs/.htaccess") ) return;
	file_put_contents( "logs/.htaccess", "deny from all" );
}

//-------------------------------------------------------------------------------------------------
$folder = strftime( "%y%m%d", $param_time );
if( !file_exists( "demos/$folder" ) ) mkdir( "demos/$folder", 0777, true );
if( !file_exists( "logs/$folder" ) ) mkdir( "logs/$folder", 0777, true );
CreateLogAccessRestriction();


// zip file to archive and delete
$zip = new ZipArchive;
$zip->open( "demos/$folder/$param_demo.zip", ZipArchive::CREATE );
$zip->addFile( "stage/$param_demo.dem", "$param_demo.dem" );
$zip->close();

// copy log file
copy( "stage/$param_demo.log", "logs/$folder/$param_demo.log" );

// register in database
RegisterDemo();

unlink( "stage/$param_demo.dem" );
unlink( "stage/$param_demo.log" );

echo "OK";

//-------------------------------------------------------------------------------------------------
function GetServerInfo() {
	$sql = GetSQL();
	$param = $sql->real_escape_string( strtoupper($GLOBALS['param_server']) );

	$result = $sql->safequery( "SELECT ID,GAME FROM SERVERS WHERE NAME='$param'" );
	$row = $result->fetch_array();
	if( !$row ) return false;

	global $server_id, $server_game;
	$server_id = $row['ID'];
	$server_game = $row['GAME'];
	return true;
}

//-------------------------------------------------------------------------------------------------
function SteamIDto32bit( $steamid ) {
	// STEAM_X:Y:ZZZZZ
	$group = substr($steamid,8,1);
	$id = substr( $steamid, 10 );
	
	$id = bcmul( $id, "2" );
	$id = bcadd( $id, $group );
	return $id;
}

//-------------------------------------------------------------------------------------------------
function StripMap( $map ) {
	$map = str_replace( '\\', '/', $map );
	$a = strrpos( $map, '/' );
	if( $a === FALSE ) return $map;
	return substr( $map, $a+1 );
}

//-------------------------------------------------------------------------------------------------
function RegisterDemo() {

	global $server_game, $server_id, $folder, 
		$param_demo, $param_time,$param_score;
	
	$demoinfo = new DemoHeader( "stage/$param_demo.dem" );
	if( !$demoinfo->valid ) {
		die("Invalid demo file.");
	} 
	
	$sql = GetSQL();
	
	$game = $sql->real_escape_string($GLOBALS['server_game']);
	$file = $sql->real_escape_string("$folder/$param_demo");
	
	$match = ParseLog( "logs/$file.log", true );
	
	$name_lump = "";
	$player_lump = "";
	
	foreach( $match->players as $p ) {
		$name_lump .= str_replace(","," ",$p->name) . ',';
		$player_lump .= $p->steamid . ',';
	}
	// strip trailing commas
	if( $player_lump != "" ) $player_lump = substr( $player_lump, 0, strlen($player_lump)-1 );
	if( $name_lump != "" ) $name_lump = substr( $name_lump, 0, strlen($name_lump)-1 );
	
	$chat_lump = $sql->real_escape_string( $match->chat ); 
	$name_lump = $sql->real_escape_string( $name_lump ); 
	
	$result = $sql->safequery( 
		"INSERT INTO INFO (SERVER,GAME,FILE,MAP,TIME,DURATION,TICKS,SCORE1,SCORE2,NAMES,CHAT)".
		" VALUES ($server_id,'$server_game','$file','".$sql->real_escape_string(StripMap($demoinfo->map))."',$param_time,".$demoinfo->duration.",".$demoinfo->ticks.",".$param_score[0].",".$param_score[1].",'$name_lump','$chat_lump')" 
		);
	
	$result = $sql->safequery( "SELECT LAST_INSERT_ID()" );
	$matchid = $result->fetch_array();
	$matchid = $matchid['LAST_INSERT_ID()'];
	
	foreach( $match->players as $p ) { 
		$account = SteamIDto32bit( $p->steamid );
		$result = $sql->safequery( "INSERT INTO PLAYERS (MATCHID,ACCOUNT,BIGSTREAK) VALUES ".
			"($matchid,$account,".$p->biggest_streak.")" );
	}
	
}

//-------------------------------------------------------------------------------------------------
function VerifyData() {
	if( !isset( $_GET['key'] ) || !isset( $_GET['server']) || !isset( $_GET['demo'] ) 
		|| !isset( $_GET['score'] ) || !isset( $_GET['time'] ) 
		|| !isset( $_GET['duration'] ) ) {
		
		return false;
	}
	global $param_key,$param_server,$param_demo,$param_score,$param_time ;
	$param_key = $_GET['key'];
	if( $param_key != $GLOBALS['apikey'] ) return false;
	$param_server = $_GET['server'];
	if( $param_server == "" ) return false;
	$param_demo = $_GET['demo'];
	if( $param_demo == "" ) return false;
	$param_score = explode('-', $_GET['score'], 2);
	$param_score[0] = (int)$param_score[0];
	$param_score[1] = (int)$param_score[1];
	$param_time = (int)$_GET['time'];
	//$param_duration = (float)$_GET['duration'];
	
	if( !file_exists( "stage/" . $param_demo . ".dem" ) || 
		!file_exists( "stage/" . $param_demo . ".log" ) ) {
		
		return false;
	}
	return true;

}
//-------------------------------------------------------------------------------------------------

?>