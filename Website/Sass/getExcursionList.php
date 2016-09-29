<?php

require_once 'DBCred.php';

echo "Talking<br />";
$db = new mysqli($db_hostname, $db_username, $db_password, $db_database); //Connect to the database server

if ($db -> connect_errno) //See if we have connected to the database
{
	echo "No connection" . mysql_error(); //Will be deleted later
	$returnCode = 1; //Predefined error code
	$db_connection = FALSE; //Connection not made
}


?>