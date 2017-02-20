<?php
	$dbc=mysqli_connect('localhost','root',NULL,'pentagas');

	if (!$dbc) {
	 die('Could not connect: '.mysql_error());
	}
?>