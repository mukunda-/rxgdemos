<?php

require_once "sql.php";
require_once "main.php";
require_once "logparser2.php";

function sort_players( $a, $b ) {
	if( $a->total_kills == $b->total_kills ) return 0;
	return $a->total_kills > $b->total_kills ? -1 : 1;
}

function Content() {
	global $servers;
	
	if( !isset( $_GET['demo'] ) ) {
		echo 'Invalid Demo ID';
		return;
	}
	$id = (int)$_GET['demo'];
	if( isset($_SESSION['viewed'][$id]) ) {
		$_SESSION['viewed'][$id]++;
	} else {
		$_SESSION['viewed'][$id] = 1;
	}
	$sql = GetSQL();
	$result = $sql->safequery( "SELECT SERVER,GAME,FILE,MAP,TIME,DURATION,TICKS,SCORE1,SCORE2,NAMES,CHAT FROM INFO WHERE ID=$id" );
	$row = $result->fetch_array();
	if( !$row ) {
		echo 'Invalid Demo ID';
		return;
	}
	
	//Tue Feb 5 00:45:10 2009
	echo '<p><table class="demoinfo">';
		echo '<tr><td>Match ID:</td><td>' . $id . '</td></tr>';
		echo '<tr><td>Server:</td><td>' . GetServerName( $row['SERVER'] ) . '</td></tr>';
		echo '<tr><td>Game:</td><td>' . $row['GAME'] . '</td></tr>';
		echo '<tr><td>Score:</td><td>' . $row['SCORE1'] . ' - ' . $row['SCORE2'] . '</td></tr>';
		echo '<tr><td>Start Time:</td><td>' . strftime( "%a %b %#d %Y, %#I:%M %p", $row['TIME'] ) . '</td></tr>';
		echo '<tr><td>Runtime:</td><td>' . FormatDuration( $row['DURATION'] ) . '</td></tr>';
		echo '<tr><td>Ticks:</td><td>' . $row['TICKS'] . '</td></tr>';
		
	echo '</table></p>'; 
	//echo '<hr>';
	echo '<div class="viewdemo downloadbutton"><a href="demos/' . $row['FILE'] . '.zip">Download</a></div><br>';
	echo '<hr>';
	
	$chat = $row['CHAT'];
	unset($row['CHAT']);
	$chat = str_replace( array( '!', '(', ')', '-' , '"', ',', '.', '/', '\\','?' ), ' ',  $chat );
	$chat = explode( ' ', $chat );
	$cloud = array();
	$max=0;
	
	foreach( $chat as $word ) {
		$word = trim($word);
		if($word == "") continue;
		$word = strtolower($word);
		if( !isset($cloud[$word]) ) $cloud[$word] = 0;
		$cloud[$word]++;
		if( $cloud[$word] > $max ) $max = $cloud[$word];
	}
	
	$own_steamid = "";
	if( $_SESSION['loggedin'] ) {
		$a = AccountFromSteamID64($_SESSION['steamid']);
		$own_steamid = "STEAM_1:" . bcmod($a,"2") . ":" . bcdiv($a,"2",0);
	}
	$match = ParseLog( "logs/" . $row['FILE'] . '.log', false, $row['TIME'], $row['DURATION'], $row['TICKS'], $own_steamid );
	usort( $match->players, "sort_players" );
	echo '<h3>Scorecard</h3>';// . str_replace(",", ", ",$row['NAMES']);
	echo '<table class="viewplayers">';
	echo '<tr><th title="Name used in the match; hover over to reveal Steam ID.">Name</th>
		<th title="Total kills in the match.">Kills</th>
		<th title="Total deaths in the match.">Deaths</th>
		<th title="Percent of kills that are headshots.">HS</th>
		<th title="Kills divided by deaths.">K/D</th>
		<th title="Rounds with 3 kills.">3K</th>
		<th title="Rounds with 4 kills.">4K</th>
		<th title="Rounds with 5 kills.">5K</th>
		<th title="Rounds with 6 kills.">6K</th>
		<th title="Rounds with 7+ kills.">7K+</th> 
		<th title="Largest kill streak">LS</th></tr>';
		
	foreach( $match->players as $p ) {
		
		echo '<tr>'.
			'<td style="text-align:left;" title="'.$p->steamid.'">' . htmlspecialchars($p->name) . '</td>'.
			'<td>' . $p->total_kills . '</td>'.
			'<td>' . $p->total_deaths . '</td>'.
			'<td>' . round(($p->total_headshots / max($p->total_kills,1))*100) . '%</td>'.
			'<td>' . round(($p->total_kills/max($p->total_deaths,1)),2) . '</td>';
		
		for( $i = 3; $i <= 6; $i++ ) {
			$val = $p->killstreaks[$i];
			if($val == 0) $val = "-";
			echo '<td>'.$val.'</td>';
		}
		$val = $p->killstreaks[7] + $p->killstreaks[8] + $p->killstreaks[9] + $p->killstreaks[10];
		if($val == 0) $val = "-";
		echo '<td>'.$val.'</td>';
		
		echo '<td>'.$p->biggest_streak.'</td>';
		echo '</tr>';
	}
	echo '</table>';
	//echo '<hr>';
	echo '<h3>Word cloud</h3>';
	foreach( $cloud as $word => $num ) {
		if( $num > 1 )
			echo '<span style="font-size:' . round(70 + ($num/$max)*100) . '%">'.$word.'</span> ';
	}
	echo '<hr>';
	echo '<h3>Match Log</h3>';
	echo '<input type="checkbox" id="check_timestamp" class="logfilter" checked="on"><label for="check_timestamp">Time</label> 
		<input type="checkbox" id="check_ticks" class="logfilter" ><label for="check_ticks">Ticks</label>
		<input type="checkbox" id="check_teamchange" class="logfilter" checked="on"><label for="check_teamchange">Joins/Leaves</label>
		<input type="checkbox" id="check_chat" class="logfilter" checked="on"><label for="check_chat">Chat</label>
		<input type="checkbox" id="check_kills" class="logfilter" checked="on"><label for="check_kills">Kills</label>
		<input type="checkbox" id="check_self" class="logfilter" checked="on"><label for="check_self">Your actions only</label>
		';
	echo '<div class="matchlog">';
	
	echo $match->output;
	
	echo '</div></p>';
	
}

StartPage();

?>
 
	<div class="contentheader">
		Match Information
	</div>
	<div class="contentbody">
	
		<?php Content(); ?>

	</div>



<script>
/*
function SetupCheckbox( box, classname ) {

	var shit=function() {
		if( $("#check_"+box).is(":checked") ) {
			$(classname).show();
		} else {
			$(classname).hide();
		};
	};
	
	shit();
	$("#check_"+box).click( shit );
}

$("#check_self").click( function() {
	if( this.checked ) {
		$(".matchlog .notself").css( "display", "none" );
	} else {
		$(".matchlog .notself").css( "display", "" );
		
	}
});
*/

function UpdateFilters() {
	if( $("#check_timestamp").is(":checked") ) {
		$("#check_ticks").removeAttr("disabled");
	} else {
		$("#check_ticks").attr("disabled","disabled");
	}
	
	if( $("#check_timestamp").is(":checked") ) {
		$(".matchlog .time_segment").show();
	} else {
		$(".matchlog .time_segment").hide();
	}
	
	if( $("#check_ticks").is(":checked") ) {
		$(".matchlog .time_ticks").show();
	} else {
		$(".matchlog .time_ticks").hide();
	}
	
	if( $("#check_teamchange").is(":checked") ) {
		$(".matchlog .teamchange").show();
	} else {
		$(".matchlog .teamchange").hide();
	}
	
	if( $("#check_chat").is(":checked") ) {
		$(".matchlog .say").show();
	} else {
		$(".matchlog .say").hide();
	}
	
	if( $("#check_kills").is(":checked") ) {
		$(".matchlog .kill").show();
	} else {
		$(".matchlog .kill").hide();
	}
	
	if( $("#check_self").is(":checked") ) {
		$(".matchlog .notself").hide();
	}
}

$(".logfilter").click( UpdateFilters );

$().ready( function() {
	$("#check_self").removeAttr( "checked" );
	UpdateFilters();
	/*
	SetupCheckbox( "timestamp", ".matchlog .time_segment" );
	SetupCheckbox( "ticks", ".matchlog .time_ticks" );
	SetupCheckbox( "teamchange", ".matchlog .teamchange" );
	SetupCheckbox( "chat", ".matchlog .say" );
	SetupCheckbox( "kills", ".matchlog .kill" ); 
	
	
	var disable_ticks = function() {
		if( $("#check_timestamp").is(":checked") ) { 
			$("#check_ticks").removeAttr("disabled");
		} else { 
			$("#check_ticks").attr("disabled","disabled");
		}
	}
	disable_ticks();
	$("#check_timestamp").click( disable_ticks );
	*/
	
	/*SetupCheckbox( "#timestamp", ".matchlog .time_segment" );*/
	/*
	if( !$("#check_timestamp").is(":checked") ) {
		$(".matchlog .time_segment").hide();
		$("#check_ticks").attr("disabled","disabled");
	} else {
	}
	if( $("#check_ticks").is(":checked") ) {
		$(".matchlog .time_ticks").show();
	}*/
});
 

</script>
	
<?php

EndPage();

?>