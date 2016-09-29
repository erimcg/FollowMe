<?php
    
/**
@author Trevor Bostic
For info email trb004@eagles.bridgewater.edu
This program is for registering users and creating accounts in the db
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

$username = $db -> real_escape_string($info -> username); //Clean up the passed in strings
$title = $db -> real_escape_string($info -> title);//*/


/*$username = "Haroueolbthey";
$title = "Test Ex222";*/

$query = "SELECT excursionId FROM `excursion` WHERE `publisher` = '$username' AND `title` = '$title'"; //Get the userId we are looking for
$result = $db-> query($query);

if(!result)
{
    dberror();
}

$row = mysqli_fetch_assoc($result);
$exId = $row['excursionId'];

$coordinates = array();
$observations = array();

$query = "SELECT longitude, latitude, counter FROM `location` WHERE `excursionId` = '$exId'";
$result = $db -> query($query);

if(!result)
{
    dberror();
}

$num_rows = mysqli_num_rows($result);

while($row = mysqli_fetch_assoc($result))
{
    $coordinates[$row['counter']] = array($row['latitude'], $row['longitude']);
}

$query = "SELECT * FROM `observation` WHERE `excursionId` = '$exId'";
$result = $db -> query($query);

if(!result)
{
    dberror();
}

$i = 0;

while($row = mysqli_fetch_assoc($result))
{
    $observations[$i++] = array($row['title'], $row['description'], $row['latitude'], $row['longitude'], $row['creationDate'], $row['author']);
}

mysqli_close($db);

echo json_encode(array('returnCode' => $returnCode, 'coordinates' => $coordinates, 'observations' => $observations));

function dberror()
{
    echo json_encode(array('returnCode' => 1));
    exit();
}

?>