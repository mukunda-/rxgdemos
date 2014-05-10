<html>

<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<style>
body,input,button {
	background-color:#008;
	color: white;
	font-size: 32px;
	font-family: courier new,monospace;
}
</style>
</head>
<body>

<?php


require_once 'main.php';

if( !$_SESSION['loggedin'] ) die( "Not Logged In." );
if( !$_SESSION['admin'] ) die( "Not Admin." );

?>

<h1>administrative tools</h1>
<hr>
<h2>stats</h2>
<?php

PrintStats();
function PrintStats() {
	$sql = GetSQL();
	$result = $sql->safequery( "SELECT SUM(1) AS DemoCount FROM INFO" );
	$row = $result->fetch_array();
	echo "<p>Total demos: " . $row['DemoCount'] . '</p>';
	echo '<p>Disk usage: <span id="diskusage"><button id="diskusage_button">compute</button></span></p>';
}

?>

<script>

$("#diskusage_button").click( function() {
	$("#diskusage_button").attr( "disabled" , "disabled" );
	$("#diskusage").html( '<span style="font-size:72px">COMPUTING...</span>' );
	$.get( "admin_diskusage.php" )
		.done( function( data ) {
			$("#diskusage").html( data );
		})
		.fail( function() {
			$("#diskusage").html( "<error>" );
		});
});

</script>
<hr>
<h2>thumbnails</h2>
copy thumbnail for workshop map. this will replace the thumbnail for the mapname associated with the workshop ID with the picture from workshop<br>
<form action="admin_get_thumbnail.php">
Workshop ID: <input type="text" name="workshopid"><br>
<input type="submit" value="do">
</form>
<hr>
copy thumbnail from URL:<br>
<form action="admin_get_thumbnail.php">
Map Name: <input type="text" name="mapname"><br>
Image URL: <input type="text" name="url"><br>
<input type="submit" value="do">
</form>
<hr>
copy thumbnail from computer:<br>
sorry borked
<hr>
</form>


</body>
</html>

