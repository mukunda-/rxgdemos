<?php

require_once "logparser2.php";

$match = ParseLog( "stage/test.log", false, 0, 10000, 10000 );

?>