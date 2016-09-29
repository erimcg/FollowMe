<!DOCTYPE HTML>
<!--
	Arcana by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>About</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />

<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.dropotron.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script> 
        <script src="js/excursion.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>

<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
	</head>
	<body>

		<!-- Header -->
			<div id="header">
						
				<!-- Logo-->
					
                    <h2>
                        <a href="index.html" id="logo">BC-FollowME<img alt="Logo" src="images/FootLogo.png" id="logo_image" height="300px"></a>
                    </h2>

				<!-- Nav -->
					<nav id="nav">
						<ul>
							<li><a href="index.html">Homepage</a></li>
							<li ><a href="about.html">About</a></li>
							<li><a href="help.html">Help</a></li>
							<li><a href="downloads.html">Downloads</a></li>
                            <li class="current"><a href="excursion.php">Search Excursions</a></li>
                            <!--<li><a href="Teams.html">Meet The Developers</a></li>-->
                        </ul>
					</nav>
                	</div>
			
		<form method='post' action ='excursion.php'>
			<input type="text" name="search" id="searchBar" value="SEARCH BY EXCURSION HERE">
            <input type="submit" value="Search"></br></br>
			
            <table id="menuTable">
               <tr>
                    <td>Search By <b>Excursion:</b><input type="radio" name="type" value="title" checked="checked"></td>
                    <td>Search By <b>Publisher:</b><input type="radio" name="type" value="publisher"></td>
                    <!--<td>Search By <b>Date:*</b><input type="radio" name="type" value="date"></td>-->
               </tr>
            </table>           
		 </form></br>
        <?php 
/********
 This page is meant to display a the excursions in the table. You can see the h3 headings down below, you
 can do whatever you want with formatting. I am trying to figure out how to make it so that when a user clicks on an
 excursion that it shows the observations of that excursion.
 */
require_once 'php/DBCred.php';

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
<!-- Footer -->
			<div id="footer">
				<div class="container">
					<div class="row ">
						<div class="6u">
							<div class="row collapse-at-2">
								<section class="6u">
									<!--<h2>Helpful Links</h2>
									<ul class="links">
										<li><a href="http://www.allaboutbirds.org/Page.aspx?pid=1189">Cornell Lab: Birds</a></li>
									    <li><a href="http://www.nature-discovery.com/bird-identification-charts">Birds: Identification Chart</a></li>
                                        <li><a href="http://dof.virginia.gov/edu/index.htm">VA Department of Forestry</a></li>
                                        <li><a href="http://www.naturedetectives.org.uk/download/id_leaves.htm">Leafs: Identification Chart</a></li>
									</ul>-->
								</section>
							</div>
						</div>
						<section class="6u">
							<!--<h3>Get In Touch</h3>
							<form>
								<div class="row half collapse-at-2">
									<div class="6u">
										<input type="text" name="name" id="name" placeholder="Name" />
									</div>
									<div class="6u">
										<input type="email" name="email" id="email" placeholder="Email" />
									</div>
								</div>
								<div class="row half">
									<div class="12u">
										<textarea name="message" id="message" placeholder="Message" rows="5"></textarea>
									</div>
								</div>
								<div class="row half">
									<div class="12u">
										<ul class="actions">
											<li><input type="submit" class="button alt" value="Send Message" /></li>-->
										</ul>
									</div>
								</div>
							</form>
						</section>
					</div>
				</div>

				<!-- Icons 
					<ul class="icons">
						<li><a href="#" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
						<li><a href="#" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
						<li><a href="#" class="icon fa-github"><span class="label">GitHub</span></a></li>
						<li><a href="#" class="icon fa-linkedin"><span class="label">LinkedIn</span></a></li>
						<li><a href="#" class="icon fa-google-plus"><span class="label">Google+</span></a></li>
					</ul> -->

				<!-- Copyright -->
					<div class="copyright">
						<ul class="menu">
							<li>&copy; Untitled. All rights reserved</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
						</ul>
					</div>

			</div>

	</body>
</html>