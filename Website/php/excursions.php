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
				{
            				echo "There has been an error";
					exit;
				}	
        			$search = '%' .$search .'%';

        			$stmt -> bind_param('s', $search);
        			$stmt -> execute();
    			}
    		else
    		{
        		$stmt = $db -> prepare("SELECT excursionId, title, description, publisher, creationDate FROM `excursion` WHERE `publisher` LIKE ?");

        		if(!$stmt)
			{
            			echo "There has been an error";
				exit;
			}
        		$search = '%' .$search .'%';

        		$stmt -> bind_param('s', $search);
        		$stmt -> execute(); 
    		}

    		$stmt -> bind_result($excursionId, $title, $description, $publisher, $creationDate);

	    	$i = 0;

	    	$excursion = array();

	    	while($i < 10 && $stmt -> fetch())
		{
	    		echo "<label class='excursion'” .$excursionId .">" .$publisher .", " .$title .", " .$description .", " .$creationDate ."<br /></label>";
    
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
    
	    		echo "<ol class='observation'” .$excursionId .">";

    			while($result && $row = mysqli_fetch_assoc($result))
    			{
       		 		//echo "<li class='observation'” .$excursionId .">" .$row['author'] .", " .$row['title'] .", " .$row['description'] .", " .$row['creationDate'] ."<br />";
    			}
    			echo "</ol>";
    
    			$i++;
		}

		//echo "<br />" .$loops "Results";

