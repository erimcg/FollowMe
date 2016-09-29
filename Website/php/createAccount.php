<?php

/**
@author: Trevor Bostic
For info email trb004@eagles.bridgewater.edu
This program is designed to create accounts for the BC-FollowMe android application
*/
require_once 'DBCred.php';

$returnCode = 0;

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database); //Connect to the database server

if ($db -> connect_errno) //See if we have connected to the database
{
	//echo "No connection" . mysql_error(); //Will be deleted later
    echo json_encode(array('returnCode' => 1));
    exit();
}

$json = file_get_contents('php://input'); //Get the Json object passed
$info = json_decode($json); //Decode it

$primaryKey = NULL; //When binding the params it wants a variable, dont know why
$username = $db -> real_escape_string($info->username); //Purify and get all of the values
$password = $db -> real_escape_string($info->password);
$email = $db -> real_escape_string($info->email);
$first = $db -> real_escape_string($info->firstName);
$last = $db -> real_escape_string($info->lastName);//*/

/*$username = $info -> username;
$password = $info -> password;
$email = $info -> email;
$first = $info -> firstName;
$last = $info -> lastName;*/

/*$username = "chuck";//These values are for test
$password = "yey";
$email = "htec@you.com";
$first = "Bobbyao";
$last = "Jones";//*/

$query = "SELECT username FROM `users` WHERE `username` = '$username'";
$result = $db -> query($query);

if($result && mysqli_num_rows($result) > 0) //Check to see if the username is taken
    $returnCode = 3;

$query = "SELECT * FROM `users` WHERE `email` = '$email'";
$result = $db -> query($query);

if($result && mysqli_num_rows($result) > 0) //Check to see if the email is taken
    $returnCode = 4;

$salt1 = "&X2^(A"; //Salts must remain the same across all of the files
$salt2 = "%U*38M";
$encryptedPas = sha1("$salt1$password$salt2"); //Salt and encrypt the password

$stmt = $db -> prepare("INSERT INTO users VALUES (NULL,?,?,?,?,?)");//Prepare the statement, used for security

if($stmt && $returnCode == 0) //If the prepare statement was successful and the return code is still 0
{
    if(!$stmt -> bind_param("sssss", $username, $encryptedPas, $email, $first, $last))
        $returnCode = 1;

    if(!$stmt -> execute())
        $returnCode = 1;

    //echo $stmt -> sqlstate;
    $stmt -> close();
}
else if($returnCode == 0) //Return code must still be 0 or this would always run
{
    $returnCode = 1;
}

mysqli_close($db);

echo json_encode(array('returnCode' => $returnCode)); //Return the json object with return code
?>