<?php

/**
@author: Trevor Bostic
For info email trb004@eagles.bridgewater.edu
This program will return 20 excursions
*/
require_once 'DBCred.php';

$returnCode = 0;

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database); //Connect to the database server

if ($db -> connect_errno) //See if we have connected to the database
{
    //echo "no connection";
    dberror();
}

$excursions = array();

$query = "Select * FROM `excursion`";
$result = $db -> query($query);

if(!$result)  
{ 
    dberror();
}

/*Excursion[] = 
0 - title char(40)
1 - description char(250)
2 - creationDate DateTime
3 – originalAuthor char
4 – publisher
5 – travelMode
6 - shareMode*/

$i = 0;
while($i < 20 && $row = mysqli_fetch_assoc($result))
{   //The travelMode and share mode are ints and may need to be made into string
    $excursions[$i++] = array($row['title'], $row['description'], $row['creationDate'], $row['originalAuthor'], $row['publisher'], $row['travelMode'], $row['shareMode']);
}

mysqli_close($db);

echo json_encode(array('returnCode' => $returnCode, 'excursion' => $excursions)); //Send back the excursions

function dberror()
{
    echo json_encode(array('returnCode' => 1));
    exit();
}
?>