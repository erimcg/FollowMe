<?php

/**
Test the program
 */
require_once 'DBCred.php'; //Get the credentials for database
$debug = FALSE; //If i need to debug the program
$returnCode = 0;

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database); //Connect to the database server

if ($db -> connect_errno) //See if we have connected to the database
{
	//echo "No connection" . mysql_error(); //Will be deleted later
    echo json_encode(array('returnCode' => 1));
    exit();
}

$obj = file_get_contents('php://input'); //Get the Json object passed in
$loginInfo = json_decode($obj); //Decode it

$username = $db -> real_escape_string($loginInfo -> username); //Strip any special characters or magical quotes that
$password = $db -> real_escape_string($loginInfo->password); //Could be an attempt to hack the database*/

/*$username = $db -> real_escape_string("rmcgregor"); //For test purposes
$password = $db -> real_escape_string("pass"); //*/
//echo "username: " .$username ." password: " .$password;

if($username == "" || $password == "")
{
    echo json_encode(array('returnCode' => 7, 'errorMessage' => $db -> error));
    exit();
}

/*$username = $loginInfo -> username;
$password = $loginInfo -> password;*/

$query = "SELECT password, username FROM `users` WHERE `username` = '$username'"; //Our query statement
$result = $db -> query($query); //Query the database*/

//echo mysqli_num_rows($result);

if($result && mysqli_num_rows($result) == 0)
{
    echo json_encode(array('returnCode' => 7, 'errorMessage' => $db -> error));
    exit();
}

$row = mysqli_fetch_assoc($result); //Get the response in an associative array
$salt1 = "&X2^(A"; //Salts must remain the same across all of the files
$salt2 = "%U*38M";
$encryptedPas = sha1("$salt1$password$salt2"); //Salt and encrypt the password

if($debug)
{
    echo $username ."<br />";
    echo $row['username'] ."<br />";
    echo $encryptedPas ."<br />";
    echo $row['password'] ."<br />";
}

if ($encryptedPas == $row['password']) //Check if the match and there is a connection
{
	//echo "They match!";
	$returnCode = 0; //For success
}
else
{
	//echo "They are different";
	$returnCode = 2; //For different
}

mysqli_close($db);

echo json_encode(array("returnCode" => $returnCode)); //Send back a Json Object
?>