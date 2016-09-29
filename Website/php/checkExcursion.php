<?php

/**
@author: Trevor Bostic
For info email trb004@eagles.bridgewater.edu
This will check to see if an excursion already exists
*/
require_once 'DBCred.php';

$returnCode = 0;
$db = new mysqli($db_hostname, $db_username, $db_password, $db_database); //Connect to the database server

if ($db -> connect_errno) //See if we have connected to the database
{
	//echo "No connection" . mysql_error(); //Will be deleted later
	dberror();
}


$json = file_get_contents('php://input'); //Get the Json object passed
$info = json_decode($json); //Decode it

$username = $db -> real_escape_string($info -> username);
$title = $db -> real_escape_string($info -> title);

/*$username = "Haroueolbthey";
$title = "Test Ex2322";*/

$query = "SELECT userId FROM `users` WHERE `username` = '$username'";
$result = $db-> query($query);

if(!$result)  
{ 
    $db -> error;
    dberror();
}

$row = mysqli_fetch_assoc($result);
$userId = $row['userId'];

$query = "SELECT * FROM `excursion` WHERE `userId` = '$userId' AND `title` = '$title'";
$result = $db -> query($query);

if(!$result)  
{ 
    dberror();
}

if($result && mysqli_num_rows($result) > 0)
{
    $returnCode = 5;
}
else
{
    $returnCode = 6;
}

echo json_encode(array('returnCode' =>$returnCode));

function dberror()
{
    echo json_encode(array('returnCode' => 1));
    exit();
}
?>