<?php

class Player {
	public $steamid;
	public $name;
	public $killcounter =0;
	public $killstreaks = array();
	public $biggest_streak=0;
	public $total_kills =0;
	public $total_headshots =0;
	public $total_deaths =0;
	
	function __construct() {
		for( $i = 0; $i <= 10; $i++ ) {
			$this->killstreaks[$i] = 0;
		}
	}
	
	function EndStreak() {
		if( $this->killcounter > 0 ) {
			if( !isset($this->killstreaks[$this->killcounter]) )
				$this->killstreaks[$this->killcounter] = 0;
			$a = $this->killcounter;
			if( $a >= 10 ) $a = 10;
			$this->killstreaks[$a]++;
			if( $this->killcounter > $this->biggest_streak ) 
				$this->biggest_streak = $this->killcounter;
			$this->killcounter = 0;
		}
	}
}

class MatchData {
	public $players=array();
	public $scores;
	public $output;
	public $chat;
	
	public $start;
	public $duration;
	public $ticks;
	
	function __construct( $start, $duration, $ticks ) {
		$this->start = $start;
		$this->duration = $duration;
		$this->ticks = $ticks;
	}
	
	function AddPlayer( $steamid, $name ) {
		$steamid[6] = '1';
		if( !isset($this->players[$steamid]) ) { 
			$this->players[$steamid] = new Player;
			$this->players[$steamid]->steamid = $steamid;
			
		}
		$this->players[$steamid]->name = $name;
	}
	
	function FormatReltime( $t ) {	
		$t -= $this->start;
		return sprintf( "%02d:%02d", $t / 60, $t % 60 );
	}
	
	function FormatTicks( $t ) {	
		$t -= $this->start;
		$t /= $this->duration;
		$t *= $this->ticks;
		return sprintf( "[%.1fK]", $t/1000  );
	}
	
	function PrintLog( $time, $text, $class="", $self=FALSE ) {
		$this->output .= "<tr class='".($self?"self":"notself")." logentry $class'><td class='time_segment'><span class='time_absolute'>".strftime( "%H:%M:%S", $time )."</span> <span class='time_relative'>(".$this->FormatReltime($time).")</span> <span class='time_ticks'>".$this->FormatTicks($time)."</span></td><td class='logtext'>$text</td></tr>\n";
	}


	function PrintLogDivider( $text, $class="" ) {
		$this->output .= "<tr class='logentry divider $class'><td colspan='2'>$text</td></tr>\n";
	}
  
	function RecordChat( $text ) {
		$this->chat .= ' ' . $text;
	}
	
	function CloseLog() {
		$this->output = '<table>' . $this->output . '</table>';
	}
}


function ParseLog( $filepath, $simple=false, $starttime=0, $duration=0, $ticks=0, $highlight="" ) {
	
	$file = fopen( $filepath, "r" );
	if( !$file  ) return FALSE;
	
	$target_regex = '"(.+?)<.+?><(.+?)>(<.*?>)?"';

	$regex_playeraction = '/^'.$target_regex.' (?:\[.+?\] )?(.+?) (.*)/S';
	$regex_switched = '/^from team <(.*?)> to (<.*?>)/S';
	$regex_killed = '/^'.$target_regex.'(?: \[.*?\])? with "(.+?)" ?(\(headshot\))?/S';
	$regex_say = '/^"(.*?)"?\s*$/S';
	$regex_teamtrigger = '/^Team "(.+?)" triggered "(.+?)" \(CT "(.+?)"\) \(T "(.+?)"\)/S';
	$regex_disconnect = '/^\(reason "(.+?)"\)/S';
	
	
	$killstreak_names = array (
		"No Kills",
		"One Kill",
		"Double Kill",
		"Triple Kill",
		"Dominating",
		"Rampage",
		"Mega Kill",
		"Unstoppable",
		"Ultra Kill",
		"Godlike"
	);
	$endreasons = array (
		"SFUI_Notice_All_Hostages_Rescued" => "All Hostages Rescued | Counter-Terrorists Win",
		"SFUI_Notice_Bomb_Defused" => "Bomb Defused | Counter-Terrorists Win",
		"SFUI_Notice_CTs_Win" => "All Terrorists Eliminated | Counter-Terrorists Win",
		"SFUI_Notice_Hostages_Not_Rescued" => "Time Expired | Terrorists Win",
		"SFUI_Notice_Target_Bombed" => "Target Bombed | Terrorists Win",
		"SFUI_Notice_Terrorists_Win" => "All Counter-Terrorists Eliminated | Terrorists Win"
	);

	$weapons = array (
		"glock" => "Glock",					"hegrenade" => "HE Grenade",	"xm1014" => "XM1014",
		"c4" => "C4",						"mac10" => "MAC-10",			"aug" => "AUG",
		"smokegrenade" => "Smoke Grenade",	"elite" => "Dual Berettas",		"fiveseven" => "Five-SeveN",
		"ump45" => "UMP-45",				"famas" => "FAMAS",				"usp" => "USP",
		"awp" => "AWP",						"mp5navy" => "MP5",				"m249" => "M249",
		"m4a1" => "M4A4",					"g3sg1" => "G3SG1",				"flashbang" => "Flashbang",	
		"deagle" => "Deagle",				"ak47" => "AK-47",				"knife" => "Knife",
		"knife_t" => "Knife",				"knife_ct" => "Knife",
		"p90" => "P90",						"galilar" => "Galil AR",		"bizon" => "PP-Bizon",
		"mag7" => "MAG-7",					"negev" => "Negev",				"sawedoff",	"Sawed-Off",
		"tec9" => "Tec-9",					"taser" => "Zeus x27",			"hkp2000" => "P2000",
		"mp7" => "MP7",						"mp9" => "MP9",					"nova" => "Nova",
		"p250" => "P250",					"scar20" => "SCAR-20",			"sg556" => "SG 553",
		"ssg08" => "Scout",					"knifegg" => "Golden Knife",	"molotov" => "Molotov (the object, not the fire!!)",
		"decoy" => "Decoy Grenade",			"incgrenade" => "Incendiary Grenade (the object, not the fire!!)",
		"inferno" => "Fire",				"m4a1_silencer" => "M4A1-S",	"usp_silencer" => "USP-S",
		"cz75a" => "CZ75-Auto"
	);
		
		
	$nameclasses = array( "<TERRORIST>" => "terr", "<Spectator>" => "spec", "<Unassigned>" => "spec", "<CT>" => "ct" );
	$teamnames = array( "<TERRORIST>" => "<span class='terr'>Terrorist</span>", "<Spectator>" => "<span class='spec'>Spectator</span>", "<Unassigned>" => "<span class='spec'>Spectator</span>", "<CT>" => "Counter-Terrorist" );
 
	$match = new MatchData($starttime, $duration,$ticks);
	$last_roundend=0;
	while( !feof($file) ) {
		$line = fgets( $file );
		$line = trim($line);
		if( $line == "" ) continue;
		if( $line[0] != 'L' ) continue;
		$time = strtotime(substr( $line, 2, 10 ) . ' ' . substr( $line, 15, 8 ) . ' America/Chicago'); 
		//L DD/MM/YYYY - HH:MM:SS
		$line = substr( $line, 25 );
	 
		if( preg_match( $regex_playeraction , $line, $matches ) ) {
		 
			$name = htmlspecialchars($matches[1]);
			$steamid = $matches[2];
			$team = $matches[3];
			$action = $matches[4];
			$data = $matches[5];
			$self = $highlight==""?FALSE:($steamid == $highlight);
			
			switch( $action ) {
				case "entered":
					if( $simple ) break;
					if( $data == 'the game' ) {
						$match->PrintLog( $time, "$name <span class='steamid'>$steamid</span> entered the game", "teamchange", $self );
					}
					break;
				case "disconnected":
					if( $simple ) break;
					if( preg_match( $regex_disconnect, $data, $matches ) ) {
						$reason = $matches[1];
						$match->PrintLog( $time, "$name left the game ($reason)", "teamchange", $self );
					}
					break;
				case "switched":
					if( $simple ) break;
					if( preg_match( $regex_switched, $data, $matches ) ) {
						 
						if( $matches[2] == '<Unassigned>' ) break;
						if( $matches[1] == 'Unassigned' && $matches[2] == '<Spectator>' ) break;
						$match->PrintLog( $time, "$name joined " . $teamnames[$matches[2]], "teamchange", $self );
					}
					break;
				case "killed":
					
					if( preg_match( $regex_killed, $data, $matches ) ) {
					
						$victimid = $matches[2];
						$killstreak = "";
						if( $steamid != 'BOT' ) {
							$match->AddPlayer( $steamid, $name );
							
							$p = &$match->players[$steamid];
							$p->total_kills++;
							if( isset($matches[5]) ) $p->total_headshots++;
							$p->killcounter++;
							if( $p->killcounter >= 3 ) {
								$killstreak = " | Killstreak: " . $p->killcounter . "K (" . $killstreak_names[min($p->killcounter,9)] .")";
							}
							unset($p);
						}
						if( $victimid != 'BOT' ) {
							$match->AddPlayer( $victimid, $matches[1] );
							$p = &$match->players[$victimid];
							$p->total_deaths++;
							$p->EndStreak();
							unset($p);
						}
						
						$target = htmlspecialchars($matches[1]);
						$weapon = $matches[4];
						if( isset( $weapons[$weapon] ) ) $weapon = $weapons[$weapon];
						$headshot = isset($matches[5]) ? " <img src='hs.png'>":"";
						
						$nameclass = $nameclasses[$team];
						$targetclass = $nameclasses[$matches[3]];
						$self = $highlight==""?FALSE:($steamid == $highlight || $victimid == $highlight);
			
						if( !$simple ) $match->PrintLog( $time, "<span class='name $nameclass'>$name</span> killed <span class='name $targetclass'>$target</span> with $weapon$headshot$killstreak", "kill", $self );
					} 
					break;
				case "say":
					if( preg_match( $regex_say, $data, $matches ) ) { 
						if( $team == '<>' ) break;
						$text = htmlspecialchars($matches[1]);
						$nameclass = $nameclasses[$team]; 
						$match->RecordChat( $text );
						if( !$simple ) $match->PrintLog( $time, "<span class='name $nameclass'>$name</span>: $text", "say", $self );
					} 
					break;
				
				case "say_team":
					if( preg_match( $regex_say, $data, $matches ) ) { 
						$text = htmlspecialchars($matches[1]);
						$nameclass = $nameclasses[$team]; 
						$match->RecordChat( $text );
						if( !$simple ) $match->PrintLog( $time, "<span class='name $nameclass'>(TEAM) $name</span>: $text", "say", $self );
					} 
					break;
			}
			
			//echo "$count / $name / $steamid / $team / $action / $data<br>\n";
		} else if( strncmp( $line, 'Team', 4 ) == 0 ) {
			if( !$simple ) {
				if( preg_match( $regex_teamtrigger, $line, $matches ) ) {
					$reason = $matches[2];
					$score_ct = $matches[3];
					$score_t = $matches[4];
					if( isset( $endreasons[$reason] ) ) {
						$match->PrintLogDivider( $endreasons[$reason] . "<br>CT:$score_ct | T:$score_t" );
					} else {
						$match->PrintLogDivider( $reason . "<br>CT:$score_ct | T:$score_t" );
					}
					$last_roundend=$time;
				}
			}
			//echo $line . "\n";
		} else if( strncmp( $line, "World trigg", 11 ) == 0 ) {
			//echo $line . "\n";
			$data = substr($line,16);
			if( $data == '"Round_Start"' ) {
				if( $time - $last_roundend >= 2 ) {
					if( !$simple ) $match->PrintLogDivider(  "ROUND START" );
				}
				foreach( $match->players as $p ) {
					$p->EndStreak();
				}
			} else if( $data == '"Game_Commencing"' ) {
				if( !$simple ) $match->PrintLogDivider(  "NEW MATCH" );
				foreach( $match->players as $p ) {
					$p->EndStreak();
				}
			}
		}
	}
	
	$match->CloseLog();
	
	return $match;
}

?>