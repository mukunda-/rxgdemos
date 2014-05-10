<?php

require_once "main.php";


StartPage();


?>
 
	<div class="contentheader">
		Find a match
	</div>
	<div class="contentbody">
	<p>Click on a match to view more info.</p>
		<div class="demolist_window">
		
			<div id="demolist_content">
			<!--
				<table class="demolist">
				
					<tr class="headerrow"><td><img src="loading.gif" style="display:none" id="demolist_loading"></td><td>Server</td><td>Map</td><td>Time</td><td class="centered">Duration</td><td class="centered">Score</td><td class="centered">DL</td></tr>
					<tr class="demoentry" onclick="ViewDemo(this,20)"> 
						<td class="mapimage" title="Office" style="background: url(http://cloud-2.steampowered.com/ugc/884099387639747869/C3724AB6908CA9DD37365B2391209AAF1951566D/200x112.resizedimage) no-repeat center center; width:64px">&nbsp;</td>
						<td>OFFICE</td>
						<td>cs_office</td>
						<td>Tue Jan 5 2014, 5:01 PM (25 days ago)</td>
						<td  class="centered" >29:14</td><td  class="centered" >11-0</td>
						<td  class="downloadlink" style="width:32px" ><a href="paowiejwopfaie"><div></div></a></td>
						<td class="tab"><div>&nbsp;</div></td>
					</tr>
				 
				</table>
			-->
			<table class="no_matches_found" ><tr><td><img src="loading.gif" style="display:none" id="demolist_loading"></td></tr></table>
			</div>
		</div>
		
		<form action="javascript:SubmitQuery()"> 
			<div style="clear:both"></div>
			<!--<div class="legend"><span class="color purple"></span> Recently Viewed <span class="color red"></span> Your Match</div>-->
			<p style="float:left" class="simplefilters">Server&nbsp;
			<select id="server_filter"><option value="0">ANY</option>
				<?php
					foreach( $servers as $s ) {
						echo '<option value="'.$s['id'].'">['.$s['game'].'] '.$s['name'].'</option>';
					}
				?> 
			</select> 
			  
			&nbsp;&nbsp;Month&nbsp;&nbsp;
				<select  id="month_filter" onchange="">
					<option value="0">ANY</option>
					<option value="1">January</option>
					<option value="2">February</option>
					<option value="3">March</option>
					<option value="4">April</option>
					<option value="5">May</option>
					<option value="6">June</option>
					<option value="7">July</option>
					<option value="8">August</option>
					<option value="9">September</option>
					<option value="10">October</option>
					<option value="11">November</option>
					<option value="12">December</option>
				</select> 
			&nbsp;&nbsp;Day&nbsp;&nbsp;
				<select id="day_filter" onchange="">
					<option value="0" >ANY</option>
					<option value="1" >01</option>
					<option value="2" >02</option>
					<option value="3" >03</option>
					<option value="4" >04</option>
					<option value="5" >05</option>
					<option value="6" >06</option>
					<option value="7" >07</option>
					<option value="8" >08</option>
					<option value="9" >09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
					<option value="13">13</option>
					<option value="14">14</option>
					<option value="15">15</option>
					<option value="16">16</option>
					<option value="17">17</option>
					<option value="18">18</option>
					<option value="19">19</option>
					<option value="20">20</option>
					<option value="21">21</option>
					<option value="22">22</option>
					<option value="23">23</option>
					<option value="24">24</option>
					<option value="25">25</option>
					<option value="26">26</option>
					<option value="27">27</option>
					<option value="28">28</option>
					<option value="29">29</option>
					<option value="30">30</option>
					<option value="31">31</option>
				</select>
			
			<!--&nbsp;&nbsp;Map&nbsp; <input type="text" id="map_filter" placeholder="ANY" >-->
			</p>
			<p style="float:right">
			<input type="submit" value="Search" class="searchbutton" id="demo_search">
			</p> 
			<div style="clear:both"></div>
			
			<input type="checkbox" id="ownmatches" <?php if(!$_SESSION['loggedin']) echo 'disabled="disabled"' ?>><label for="ownmatches">Your matches only<?php if(!$_SESSION['loggedin']) echo ' (must be signed in)';?></label><br>
			<input type="checkbox" id="advanced_search"><label for="advanced_search">More options</label>
			<div id="advanced_search_window" style="display:none">
				<hr>
				<p>Map&nbsp; <input type="text" id="map_filter" placeholder="ANY" ></p>
				<div class="filter" id="filterbymoment"><div class="thing"></div>Filter by time</div> 
				<div class="subsection hidden" style="display:none"  id="filterbymoment_window">
					<p>Default time zone is CST/CDT. All fields are optional. Time can be given in various formats. Examples:
					<ul>
						<li>"31 october 1993"</li>
						<li>"1:56 PM 3/18/2014"</li>
						<li>"mar 15 2014, 23:48 PDT"</li>
						<li>"-1 week"</li>
						<li>"last monday"</li>
						<li>any other format that <a href="http://php.net/manual/en/datetime.formats.php" target="_blank">PHP supports</a></li>
					</ul>
					</p>
					
					<p>Show only demos that start after this time:</p><input type="text" id="starttime_filter"> 
					<p>Show only demos that start before this time:</p><input type="text" id="endtime_filter"> 
					<p>Show only demos that contain this moment in time:</p><input type="text" id="moment_filter"> 
					
				</div>
				<div class="filter" id="filterbyname"><div class="thing"></div>Filter by name(s)</div> 
				<div class="subsection hidden" id="filterbyname_window">
					<p>Only return demos including these player names. Player names do not need to be exact. 
					"pray" will match "pray and spray". The Steam ID filter is more accurate in finding players.</p>
					<textarea rows="4" cols="50" id="name_filter"></textarea><br>
				</div>
				<div class="filter" id="filterbysteam"><div class="thing"></div>Filter by players</div> 
				<div class="subsection hidden" id="filterbysteam_window">
					<p>Show only demos that contain these players. Separate entries on new lines. Entries may be steam community profile URLs, custom profile names, or a Steam ID (64 or 32bit).
					</p>
					<p>Examples:<ul>
						<li>http://steamcommunity.com/id/prayspray OR prayspray (vanity URL)</li>
						<li>http://steamcommunity.com/profiles/7656119xxxxxxxxxx OR 7656119xxxxxxxxxx</li>
						<li>STEAM_0:X:XXXXXX (number of digits will vary)</li>
						</ul></p>

					<textarea rows="4" cols="50" id="steam_filter"></textarea><br>
				</div>
				<div class="filter" id="filterbychat"><div class="thing"></div>Filter by chat</div> 
				<div class="subsection hidden" id="filterbychat_window">
					<p>Only show demos where players say any of these words. Prefix words with + to make them non-optional.</p>
					<textarea rows="4" cols="50" id="chat_filter"></textarea><br>
				</div>
				
				<div class="filter" id="filterbystreak"><div class="thing"></div>Find kill streaks</div> 
				<div class="subsection hidden" id="filterbystreak_window">
					<p>Only show demos where a selected player kills at least this many people in one round. Selected players are yourself if "Your matches only" is checked, plus anyone in the "Filter by players" tab. If neither of those options are used, then this searches for demos where anyone has achieved one of these kill streaks.</p>
					
					<input type="text" id="killstreak_filter" value="7"><br>
				</div><!--<br>
				<input type="submit" value="Search">-->
			</div>
			<br>
			
		</form>
		
	</div>
	
<script>

function DownloadLinkStopProp( event ) {
	event.stopPropagation();
}

$(".downloadlink").click( DownloadLinkStopProp );

function ViewDemo(elem,index) {
	$(elem).addClass( "recentlyviewed" );
	open(  index );
}

function SetupToggleID( check, id ) {

	var shit=function() {
		if( $(check).is(":checked") ) {
			$(id).show();
		} else {
			$(id).hide();
		};
	};
	
	shit();
	$(check).click( shit );
}
function SetupFilter( tab, id ) {
	
	var shit=function() {
		if( !$(tab).hasClass("selected") ) {
			$(tab).addClass("selected")
			$(id).show();
			 
		} else {
			$(tab).removeClass("selected")
			$(id).hide();
		};
	};
	$(tab).click(shit);
}

$().ready( function() { 
 
	SetupToggleID( "#advanced_search", "#advanced_search_window" );
	SetupFilter( "#filterbymoment", "#filterbymoment_window" );
	SetupFilter( "#filterbyname", "#filterbyname_window" );
	SetupFilter( "#filterbysteam", "#filterbysteam_window" );
	SetupFilter( "#filterbychat", "#filterbychat_window" );
	SetupFilter( "#filterbystreak", "#filterbystreak_window" );
	
	$("#demo_search").removeAttr("disabled");
	
	SubmitQuery();
/*	if( $("#filterbymoment").is(":checked") ) $("#filterbymoment_window").show();
	if( $("#filterbyname")is(":checked") ) $("#filterbyname_window").show();*/
});

/*
$("#filterbymoment").click( function () {
	if( this.checked ) {
		$("#filterbymoment_window").show();
	} else {
		$("#filterbymoment_window").hide();
	}
});

$("#filterbyname").click( function () {
	if( this.checked ) {
		$("#filterbyname_window").show();
	} else {
		$("#filterbyname_window").hide();
	}
}
*/

var ajax_req;

function SubmitQuery() {
	
	if( $("#demo_search").is(":disabled") ) return;
	
	var query = new Object;
	var a;
	a = $("#server_filter").val();
	if( a != 0 ) query.server=a;
	a = $("#month_filter").val();
	if( a != 0 ) query.month=a;
	a = $("#day_filter").val();
	if( a != 0 ) query.day = a;
	if( $("#ownmatches").is(":checked") ) {
		query.steams = "<?php 
		
			if( $_SESSION['loggedin'] ) echo $_SESSION['steamid']; 
			
			?>";
	}
	a = $("#ownmatches").val();
	
	if( $("#advanced_search").is(":checked") ) {
		a = $("#map_filter").val();
		if( a != 0 ) query.map =a;
		if( $("#filterbymoment").hasClass("selected") ) {
			a = $( "#moment_filter" ).val();
			if( a != "" ) query.moment=a;
			a = $( "#starttime_filter" ).val();
			if( a != "" ) query.starttime=a;
			a = $( "#endtime_filter" ).val();
			if( a != "" ) query.endtime=a;
		}
		if( $("#filterbyname").hasClass("selected") ) {
			a = $( "#name_filter" ).val();
			if( a != "" ) query.names=a;
		}
		if( $("#filterbychat").hasClass("selected") ) {
			a = $( "#chat_filter" ).val();
			if( a != "" ) query.chat=a;
		}
		if( $("#filterbysteam").hasClass("selected") ) {
			a = $( "#steam_filter" ).val();
			if( a != "" )  {
				if( query.steams == null ) query.steams = ""
				query.steams+="\n" + a;
			}
		}
		if( $("#filterbystreak").hasClass("selected") ) {
			a = $( "#killstreak_filter" ).val();
			if( a != "" ) query.streak=  a;
		}
	    
	}
	
	<?php
		if( $_SESSION['loggedin'] ) {
			
			echo 'query.user = '. AccountFromSteamID64( $_SESSION['steamid'] ) .';';
			
		}
	?>
	/*window.location.href = "search.php" + encodeURI(query);*/
	
	//if( ajax_req != null ) ajax_req.abort();
	$("#demolist_loading").show();
	$("#demo_search").attr("disabled","disabled");
	$.get( "search.php", query )
		.done( function( data ) {
			//ajax_req = null;
			$("#demolist_content").html( data );
			$(".downloadlink").click( DownloadLinkStopProp );
		})
		.fail( function(xhr, text_status, error_thrown) {
			if (text_status == "abort") return;
			//ajax_req = null;
			$("#demolist_content").html( 
				'<table class="no_matches_found" ><tr><td>Query failed. Please try again later.<br><img src="loading.gif" style="display:none" id="demolist_loading"></td></tr></table>'
			);
				
		})
		.always( function() {
			$("#demolist_loading").hide();
			$("#demo_search").removeAttr("disabled"); 
			$(".demolist_window").animate({ scrollTop: 0 }, "slow");
		});
}

</script>
				
<?php

EndPage();

?>	