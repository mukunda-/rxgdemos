<?php

class DemoHeader {
	public $valid = false;
	public $demo_protocol;
	public $network_protocol;
	public $hostname;
	public $client;
	public $map;
	public $game;
	public $duration;
	public $ticks;
	public $frames;
	
	function __construct( $filename ) {
		$file = fopen( $filename, "rb" );
		$data = trim(fread( $file, 8 ));
		echo "{ $data }";
		if( $data != "HL2DEMO" ) return;
		echo "{ $data }";
		$data = unpack( "idp/inp", fread( $file, 8 ) ); 
		$this->demo_protocol = $data['dp'];
		$this->network_protocol = $data['np'];
		$this->hostname = trim(fread($file,260));
		$this->client = trim(fread($file,260));
		$this->map = trim(fread($file,260));
		$this->game = trim(fread($file,260));
		$this->duration = unpack( "f", fread($file,4))[1];
		$this->ticks = unpack( "i", fread($file,4) )[1];
		$this->frames = unpack( "i", fread($file,4) )[1];
		$this->valid = true;
	}
}

?>