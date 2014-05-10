<?php

// SQL wrapper with safe query function
// safe query throws an exception on failure
//
// also provides global instance of sql database
// requried

require_once "sql_login.php";

$g_sqldb = null;

//-----------------------------------------------------------------------------------
class sql_wrapper extends mysqli {
	public function safequery( $query ) {
		$result = $this->query( $query );
		if( !$result ) throw new Exception( "SQL Error: ". $this->error );
		return $result;
	}
	
}

//---------------------------------------------------------------------------------------------
function GetSQL() {
	global $g_sqldb;
	if( !$g_sqldb ) {
		$g_sqldb = new sql_wrapper( $GLOBALS["sql_addr"], $GLOBALS["sql_user"],$GLOBALS["sql_password"],$GLOBALS["sql_database"] );
		if( $g_sqldb->connect_errno ) {
			$g_sqldb = null;
			throw new Exception( "SQL Connection Error: ". (int)$g_sqldb->connect_error );
		}
		$g_sqldb->reconnect = 1;
	} else {
		//if( !$g_sqldb->ping() ) {
		//	throw new Exception( "SQL Connection Error: ". $g_sqldb->error );
		//}
	}
	return $g_sqldb;
}

//---------------------------------------------------------------------------------------------
function CloseSQL() {
	global $g_sqldb;
	if( $g_sqldb ) {
		$g_sqldb->close();
		$g_sqldb = null;
	}
}

?>