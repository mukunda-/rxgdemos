<?php

header( "Content-Type: text/plain" );

$color1 = "#ccc";
$color2 = "#444";
$color3 = "#333";
$color4 = "#444";
$color4_text = "#ccc";

$hltext = "#ad1f16";
$lightshadow = "1px 1px 2px rgba(255,255,255,0.15)";
$purple = "#6c297f";

$content1 = "rgba(255,255,255,0.0)";
$content2 = "rgba(144,119,111,1.0)";

$table_bg = "#e8d2ad";
$table_text = "#333";

//------------------------------------------------
echo " 
body {
	background-color:#eee;/*test*/
	font-family: roboto, sans;
	font-weight:300;
	font-size:15px;
	margin-top: 32px;
	color:#444;
	
}

.backdrop {
	width: 100%;
	height:100%;
	box-shadow:  inset 0px 0px 16px rgba(0,0,0,0.55);
	background: url('soft_wallpaper.png') repeat fixed;
	background: linear-gradient( rgba(255,255,255,0.0), rgba(0,0,0,0.5)),url('soft_wallpaper.png') repeat fixed; 
	/*, linear-gradient( #999, #666) no-repeat fixed*/
	z-index:-1;
	position:fixed;
	left:0px;
	top:0px;
}

h3 a {
	color: inherit;
	font-size: 120%;
	text-decoration: inherit;
	letter-spacing: 2px;
}

.contentbody hr {
	color:$color1;
}

body>center>h3 {
	/*text-shadow: 2px 2px #fff,-2px -2px #fff,2px -2px #fff,-2px 2px #fff;*/
	
	/*text-shadow: 0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff,0px 0px 2px #fff;*/
}

.login {
	position:absolute;
	right: 11px;
	top: 14px;
}


.login a {
	color:inherit;
	text-decoration:inherit;
	font-size: 100%;
}

.login img.avatar {
	vertical-align:bottom;width:20px;height:20px;
	border-radius:2px;
}

.login .logout {
	background-color: #ccc;
	padding:0px 4px;
	border-radius:2px;
}
.login .logout:hover {
	background-color: #888; 
}

.window {
	width: 750px;
	margin:auto;
	position:relative;
}
/*
.window_left {
	float:left; 
}

.window_right {
	float:right; 
}*/

.content {	
	box-shadow: 0px 0px 4px rgba(0,0,0,0.25), 8px 8px 8px rgba(0,0,0,0.15), inset 0px 0px 16px rgba(0,0,0,0.15);
	/*background-color:$color1;*/
	background: url('snow.png') repeat;
	color: $color2;
	width: 100%; 
	/*border: 1px solid #999;*/
	/*border: 1px solid #999;*/
	padding: 1px;
	border-radius: 4px;
	border: 1px solid $hltext;
	
    /*box-shadow: inset 0px 0px 4px rgba(255,255,255,0.25);*/
}

.content a {
	color: $color2;
	text-decoration:none;
}

.content a:hover {
	color: $color3;
	text-decoration:underline;
}

.contentheader {
	font-size: 150%;
	font-weight: bold;
	/*background-color: $color4;*/
	/*border-bottom: 1px solid #aaa;*/
	color: $hltext;
	text-shadow: $lightshadow;
	padding: 6px 12px 2px;
}
/*
.content.sidebar {
	width: 250px;
}*/

.contentbody {
	padding: 0px 12px;
}

.contentbody .viewdemo.downloadbutton {
	
	background: #60a34f;
	background: linear-gradient( #60a34f, #477428 );
	color:#eee;
	width: 200px;
	text-align:center;
	font-size: 150%;
	font-weight: 700;
	height: 40px;
	padding-top:0px;
	vertical-align:bottom;
	border-radius: 4px;
	box-shadow: inset 0px 0px 3px rgba(0,0,0,0.25), 2px 2px 7px rgba(0,0,0,0.35);
	border:1px solid #161;
}
.contentbody .viewdemo.downloadbutton:hover {
	background: linear-gradient( #60a34f, #8ebb6e );
	
}

.contentbody  .viewdemo.downloadbutton a {
	padding: 4px;
	color: #eee;
	text-decoration: none;
	display:block;
}

input, textarea, select {
	
	font-family: roboto, sans;
	font-weight:300;
	
}

.footer {
	margin: auto;
	text-align:center;
	font-size: 9px;
}

.footer a {
	
	color:#ccc;
	color:#000;
	text-decoration:none;
}

.content .filter {
	cursor: pointer;
	padding: 4px 8px;
	font-size:100%; 
	font-weight:bold;
	margin-top:1px;
	box-shadow: inset 0px 0px 3px rgba(0,0,0,0.2); 
	border-radius: 4px 4px 4px 4px;
	background-color: rgba(0,0,0,0.06);
	transition: background-color 0.25s,color 0.4s ;
	border: 1px solid #888;
}
.content .filter.selected {
	border: 1px solid $hltext;
}

.content .filter .thing {
	width:16px;
	height:16px;
	margin-right: 8px;
	position:relative;
	top:2px;
	display:inline-block;
	background: url('check.png') no-repeat top left transparent;
	background-position: 0px 0px;
	
	
}
.content .filter.selected .thing {
	background-color:red;
	background: url('check.png') no-repeat top left transparent;
	background-position: -16px 0px;
}

.content .filter:hover {
	
	background-color: rgba(0,0,0,0.03);
	color: $hltext;
	text-shadow: 0px 0px 3px rgba(255,255,255,1.0);
	transition: background-color 0.25s,color 0.4s ;
}

.content .demolist_window { 
	height:300px; 
	background-color:transparent; 
	margin-top:24px; 
	box-shadow:inset 0px 0px 1px rgba(0,0,0,0.45);
	padding: 4px;
	padding-top: 2px;
	overflow-y:auto;
	border-radius: 4px 4px 4px 4px;
}

.content .demolist { 
	width: 100%;
	border-spacing:0px 2px;
}
.content .demolist .space { 
	width: 12px;  
	
	border: 8px solid #333;
	border-width: 0px 0px 0px 8px; 
	
}

.content .demolist tr.demoentry {
	cursor:pointer;
	font-size: 13px;
	/*border-width: 1px 0px 1px 0px;*/
	/*background-color: rgba(0,0,0,0.1);*/
	height:40px;
	background-color: rgba(0,0,0,0.06);
	transition: background-color 0.25s,color 0.4s ;
	
	box-shadow: inset 0px 0px 3px rgba(0,0,0,0.2); 
	border-radius: 4px 4px 4px 4px;
	
}
/*
.content .demolist tr.demoentry.recentlyviewed {
	box-shadow: inset 0px 0px 3px rgba(0,0,0,0.2),
		inset 0px 0px 1px #6c297f; 
	
}*/
/*
.content .demolist tr.demoentry.recentlyviewed .mapimage {
	border-left-color: $purple;
}
*/

.content .demolist tr.demoentry.recentlyviewed  {
	color: #888;
}

.content .demolist tr.demoentry td:first-child {
	border-radius: 4px 0px 0px 4px;
}
.content .demolist tr.demoentry td:last-child {
	border-radius: 0px 4px 4px 0px;
}
.content .demolist tr.demoentry .tab {
	width: 12px;
	padding: 0px;
}
.content .demolist tr.demoentry .tab>div {
	display:block;
	width: 5px;
	height: 40px;
	padding: 0px;
	background-color: #ccc;
	/*border-left: 5px solid #ccc;/*$hltext;*/
}

.content .demolist tr.demoentry .tab.self>div {
	background-color: $hltext;
	/*border-left-color: $hltext;*/
}

.content .demolist tr.headerrow {
	height: 16px;
	font-size: 12px;
	font-weight:700;
	color: $hltext;
	text-shadow: $lightshadow;
	text-align:center;
}

.content .demolist tr.headerrow td {
	height: 16px;
}

.content .demolist tr.demoentry:hover {
	
	/*border-width: 1px 0px 1px 0px;*/
	/*background-color: rgba(0,0,0,0.1);*/
	background-color: rgba(0,0,0,0.03);
	color: $hltext;
	text-shadow: 0px 0px 3px rgba(255,255,255,1.0);
	/*transition: background-color 0.25s,color 0.4s ;*/
}

.content .demolist .downloadlink {
	padding:0px; 
	
	
}
.content .demolist .downloadlink>a {
	display:block;
	height:27px;
	padding-top:9px;
	
}
.content .demolist .downloadlink>a>div {
	margin: auto;
	width: 18px;
	height: 18px;
	text-align:center;
	background: transparent;
	background-image: url('download.png');
	background-position: 0px;
} 

.content .demolist .downloadlink:hover>a>div {
	width: 18px;
	height: 18px;
	background-image: url('download.png');
	background-position: 18px;
}

.content .demolist td {
	height:36px;
}

.content .demolist td {
	padding: 0px 6px;
}

.content .demolist img {
	vertical-align:bottom;
}

.content .demolist .centered {
	text-align:center;
}

.content .demolist .mapimage {
/*	border:1px solid #000;*/
	box-shadow: 0px 0px 5px #000 inset;
	color:white;
	text-shadow: 2px 2px 0px #000;
	font-size: 24px;
	border:4px solid #ccc;
	/*border:4px solid #f81;*/
	border-width: 0px 0px 0px 3px; 
}

.content .demolist_window .no_matches_found {
	width:100%; 
	height:100%; 
	font-weight:300; 
	text-align:center;
	font-size: 150%;
}

.content .legend {
	margin: 0px;
	margin-top: 3px;
	font-size: 80%;
}

.content .legend .color {
	width: 20px;
	height: 13px;
	background-color:#0f0;
	display: inline-block;
	position:relative;
	top:1px;
	margin-left:4px;
}

.content .legend .color.red {
	background-color: $hltext;
}

.content .legend .color.purple {
	background-color: $purple;
}

.content p.simplefilters, .content p.simplefilters select {
	font-size: 17px;
}

.content .searchbutton {
	font-size: 17px;
	width: 100px;
}

.content .subsection {
	border: 1px solid #ccc;
	border-radius:4px;
	padding: 8px;
	font-size:90%;
	margin-top:3px;
	margin-bottom:3px;
}

.content .subsection p:first-child {
	margin-top: 0px;
}

.content .subsection.hidden {
	display:none;
}

table.demoshort {
	width:100%;
	
	border-spacing: 1px;
}

table.demoshort td {
	font-size: 70%;
	background-color: $table_bg;
	color: $table_text;
	padding: 0px 2px;
}

table.demoinfo {
	border-spacing: 3px;
}

table.demoinfo td {
	 
	padding: 2px 4px;
}

.viewplayers {	
	
	width: 100%;
	border-spacing: 2px;
	border-collapse:collapse;
	text-align:center;
}

.viewplayers td,.viewplayers th  {
	border: 1px solid #bbb;
	border-width: 1px 0px 1px;
	padding:1px 2px;
}

.contentbody .matchlog {
}

.matchlog {
	font-family: roboto;
	font-size: 14px;
	color: #000;
	background-color: #fff;
	padding: 2px;
	border: 1px solid #aaa;
	border-radius: 4px;
	min-height: 100px;
	max-height: 800px;
	overflow: auto;
}
.matchlog .steamid {
	font-size:80%;
	
	border: 1px dotted #9ccbff;
	border-radius:3px;
	background-color: #cae3ff;
	
	/*vertical-align:center;*/
	padding: 0px 2px;
	position:relative;
	top:-1px;
}

.matchlog table {
	border-collapse: collapse;
	width: 100%;
}

.matchlog tr {
	padding:0px;
	margin:0px;
	/*margin-top: -1px;*/
	padding: 6px 6px;
	box-shadow: inset 0px 0px 1px rgba(0,0,0,0.10);
	border-left: 3px solid rgba(0,0,0,0.25);
	border-width: 0px 0px 0px 3px;
	
	/*border-radius:3px;*/
}

.matchlog tr:nth-child(even) {
}

.matchlog tr.self {
	border-left-color:  $hltext;
}

.matchlog td.logtext {
	width:100%;
	padding-left:8px;
	word-break:break-all;
}

.matchlog td {
	
	padding: 7px 6px 8px;
}

.matchlog .time_segment {
	font-weight: 400;
	font-size: 80%;
	background-color: #ccdbe5;
	box-shadow: inset 0px 0px 1px rgba(0,0,0,0.10);
	/*border: 1px dashed rgba( 0,0,0, 0.2 );*/
	border-width: 0px 1px 0px 1px;
	/*border-radius: 2px;*/
	/*margin-right: 10px;*/
	/*position:relative;
	top:-1px;*/
	white-space:nowrap;
}
.matchlog .time_absolute { 
}
.matchlog .time_relative { 
	
} 
.matchlog .time_divider { 
	
}
.matchlog .time_ticks {
	display:none;
}

.matchlog .terr {
	color:#cc3e37;
	font-weight:400;
}

.matchlog .ct {
	color:#0052e0;
	font-weight:400;
}

.matchlog .spec{ 
	color:gray;
	font-weight:400;
}

.matchlog .action {
	font-weight:bold;
}

.matchlog img {
	vertical-align:bottom;
	
	height: 16px;
}

.matchlog .logentry {
	/*background-color: #fafaff;*/
	
}

.matchlog .logentry:nth-child(even) {
	
	/*background-color: #f7f7f7;*/
}

.matchlog .logentry.teamchange {
	/*background-color: #f5f1ed;*/
}

.matchlog .logentry.kill {
	background-color: #FFFAD4;
}

.matchlog .logentry.divider {
	
	background-color: #e6e6e6;
	padding: 4px;
	text-align:center;
	
	font-weight: bold;
	font-size: 120%;
}
";

?>