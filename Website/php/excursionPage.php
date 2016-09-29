<html>
	<head>
		<title>Here is the title, love it</title>
        <link rel="stylesheet" href="../css/excursion.css">
        <script src="http://code.jquery.com/jquery-1.11.1.js"></script>
        <script src="../js/excursion.js"></script>
	</head>

	<body>
		<form method='post' action ='excursionPage.php'>
			<input type="text" name="search" id="searchBar" value="Search">
			Search by: Excursion Title
			<input type="radio" name="type" value="title" checked="checked">
			Publisher
			<input type="radio" name="type" value="publisher">
			<input type="submit" value="Search">
		</form>

<?php 
/********
 This page is meant to display a the excursions in the table. You can see the h3 headings down below, you
 can do whatever you want with formatting. I am trying to figure out how to make it so that when a user clicks on an
 excursion that it shows the observations of that excursion.
 */
require_once 'DBCred.php';

$DEBUG = FALSE;
$formSubmit = FALSE;

$db = new mysqli($db_hostname, $db_username, $db_password, $db_database);
//Connect to the database server

if ($db -> connect_errno)//See if we have connected to the database
{
	echo "No connection to database";
	//echo $db -> error;
}

if(isset($_POST['search']))
{
    $search = $db -> real_escape_string($_POST['search']);
    $type = $_POST['type'];

    if($type == "title")
    {
        $stmt = $db -> prepare("SELECT excursionId, title, description, publisher, creationDate FROM `excursion` WHERE `title` LIKE ?");

        if(!$stmt)
            echo "There has been an error";

        $search = '%' .$search .'%';

        $stmt -> bind_param('s', $search);
        $stmt -> execute();
    }
    else
    {
        $stmt = $db -> prepare("SELECT excursionId, title, description, publisher, creationDate FROM `excursion` WHERE `publisher` LIKE ?");

        if(!$stmt)
            echo "There has been an error";

        $search = '%' .$search .'%';

        $stmt -> bind_param('s', $search);
        $stmt -> execute(); 
    }

    $stmt -> bind_result($excursionId, $title, $description, $publisher, $creationDate);
    $formSubmit = TRUE;
}

if(!$formSubmit)
{
$query = "SELECT * FROM `excursion`";
$result = $db -> query($query);

if(!$result)
{
$db -> error;
//dberror();
}

$i = 0;

$stmt = $db -> prepare("SELECT title, description, author, creationDate  FROM `observation` WHERE `excursionId` = ?");

while($i++ < 10 && $row = mysqli_fetch_assoc($result))
{
    $excursionId = $row['excursionId'];

    echo "<label class='excursion' id='" .$excursionId ."'>" .$row['publisher'] .", " .$row['title'] .", " .$row['description'] .", " .$row['creationDate'] ."<br /></label>";

    if(!($stmt -> bind_param('i', $excursionId)))
        continue;

    if(!($stmt -> execute()))
        continue;

    if(!($stmt -> bind_result($title, $description, $author, $creationDate)))
        continue;

    echo "<ol class='observation " .$excursionId ."'>";
    while($stmt -> fetch())
    {
        echo "<li> " .$author .", " .$title .", " .$description .", " .$creationDate ."<br />";
    }
    echo "</ol>";
    $stmt -> close();
}
}
else
{
    $i = 0;

    $excursion = array();

    while($i < 10 && $stmt -> fetch())
{
    echo "<label class='excursion' id=" .$excursionId .">" .$publisher .", " .$title .", " .$description .", " .$creationDate ."<br /></label>";
    
    $excursion[$i] = $excursionId;
    $i++;
}

$stmt -> close();

$i = 0;
$loops = count($excursion);

while($i < $loops)
{
    $excursionId = $excursion[$i];

    $query = "SELECT * FROM `observation` WHERE `excursionId` = '$excursionId'";
    $result = $db -> query($query);
    
    if(!$result)
        echo $db -> error;
    
    echo "<ol class='observation " .$excursionId ."'>";
    while($result && $row = mysqli_fetch_assoc($result))
    {
        echo "<li class='observation " .$excursionId ."'> " .$row['author'] .", " .$row['title'] .", " .$row['description'] .", " .$row['creationDate'] ."<br />";
    }
    echo "</ol>";
    
    $i++;
}

echo "<br />$loops Results";
}
?>
	</body>
</html>

