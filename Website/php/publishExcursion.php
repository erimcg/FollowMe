<?php

/**
@author Trevor Bostic
For info email trb004@eagles.bridgewater.edu
This program saves user created excursions to the database
*/
require_once 'DBCred.php';

$returnCode = 0;

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database); //Connect to the database server

if ($db -> connect_errno) //See if we have connected to the database
{
	//echo "No connection" . mysql_error(); //Will be deleted later
    db_error(0);
}

$json = file_get_contents('php://input'); //Get the Json object passed
$info = json_decode($json); //Decode it

$excursion = ($info -> excursion);
$observations = ($info -> observations);
$coordinates = ($info -> coordinates);

/*Excursion[] = 
0 - title char(40)
1 - description char(250)
2 - creationDate DateTime
3 – originalAuthor char
4 – publisher
5 – travelMode
6 - shareMode*/

$title = $db -> real_escape_string($excursion[0]);
$description = $db -> real_escape_string($excursion[1]);
$creationDate = $db -> real_escape_string($excursion[2]);
$author = $db -> real_escape_string($excursion[3]);
$publisher = $db -> real_escape_string($excursion[4]);
$travelMode = $db -> real_escape_string($excursion[5]);
$shareMode = $db -> real_escape_string($excursion[6]);//*/

/*
echo json_encode(array("returnCode" => $title));
exit();*/

/* Test input
$username = "Haroueolbthey";
$title = "Test Ex222";
$description = "This may be the first excursion recorded in the database";
$creationDate = "11/21/2014 11:00PM";
$author = "Trevor Bostic";
$publisher = "WebTeam";
$travelMode = 0;
$shareMode = 1;

$observation = array(
    array("TestOb1", "Description of testOb1", 35.432, 54.6453, "date1", "Ob author"),
    array("Testob2", "Description of test ob2", 43.43, 53.75, "Date2", "ob author"),
);

$coordinate = array(
    array(23.633, 532, 0),
    array(23.633, 532, 1),
    array(23.633, 532, 2),
    array(23.633, 532, 3),
    array(23.633, 532, 4),
    array(23.633, 532, 5),
    array(23.633, 532, 6),
    array(23.633, 532, 7),
);
*/

$query = ("Select userId FROM `users` WHERE `username` = '$publisher'");
$result = $db -> query($query);

if(!$result)
{
   dberror(1);
}

$row = mysqli_fetch_assoc($result);
$userId = $row['userId'];

$query = "Select * FROM `excursion` WHERE `userId` = '$userId' AND `title` = '$title'";
$result = $db -> query($query);

if($result && mysqli_num_rows($result) > 0) //Delete what already exists, this should be replaced in later versions, it is used now for simplicity
{
    $row = mysqli_fetch_assoc($result);
    $excursionId = $row['excursionId'];

    $query = "DELETE FROM `excursion` WHERE `excursionId` = '$excursionId'";
    $result = $db -> query($query);

    if(!$result)
    {
        //echo $db -> error;
        dberror(2);
    }

}

//For some reason it is not putting the user id into the table

$stmt = $db -> prepare("INSERT INTO `excursion` VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");

if($stmt)
{
    if(!($stmt -> bind_param('isssssii', $userId, $title, $description, $creationDate, $author, $publisher, $travelMode, $shareMode)))
        dberror(3);

    if(!($stmt -> execute()))
        dberror(4, $db -> error);
}
else
{
    dberror(5);
}

$excursionId = $db -> insert_id;

if(!$result)
{
    dberror(6);
}

$stmt = $db -> prepare("INSERT INTO observation VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)");//Prepare the statement, used for security

if(!$stmt)
{
    dberror(7);
}

/*Observation[] = 
0 - title char(40)
1 - description char(250)
2 - latitude FLOAT
3 - longitude FLOAT
4 – creationDate DateTime
5 – author char(25)*/



foreach($observations as $info) //Put in each observation
{
    $title = $db -> real_escape_string($info[0]);
    $description = $db -> real_escape_string($info[1]);
    $latitude = $db -> real_escape_string($info[2]);
    $longitude = $db -> real_escape_string($info[3]);
    $date = $db -> real_escape_string($info[4]);
    $author = $db -> real_escape_string($info[5]);


    if(!($stmt -> bind_param("issddss", $excursionId, $title, $description, $latitude, $longitude, $date, $author)))
        dberror(8);
    
    if(!($stmt -> execute()))
        dberror(9, $db -> error);
}
$stmt -> close();

$stmt = $db -> prepare("INSERT INTO location VALUES (NULL,?,?,?,?)"); //Makes it faster to execute since there will by many rows for this

foreach($coordinates as $info)
{
    $latitude = $db -> real_escape_string($info[0]);
    $longitude = $db -> real_escape_string($info[1]);
    $index = $db -> real_escape_string($info[2]);

    if(!($stmt -> bind_param("iddi", $excursionId, $latitude, $longitude, $index)))
        dberror(10);

    if(!$stmt -> execute())
        dberror(11);

}

$stmt -> close();
mysqli_close($db);

echo json_encode(array('returnCode' => $returnCode));

function dberror($errorCode, $errorMessage)
{
    echo json_encode(array('returnCode' => 1, 'errorCode' => $errorCode, 'errorMessage' => $errorMessage));
    exit();
}
?>