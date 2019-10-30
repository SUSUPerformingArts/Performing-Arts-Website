<?php
	include 'header.php';
?>

<?php
	$name=str_replace("'", "\'", $_POST['name']);
	$description=str_replace("'", "\'", $_POST['description']);
	$added_by=$_POST['added_by'];
	$itemType = $_POST['type'];
	$room = $_POST['room'];
	$containertype = $_POST['containertype'];
	$container = $_POST['container'];
	if($containertype == "other") {
		$location = 'In room ' .$room; 
	}
	else {
		$location = 'In room '.$room.' ' . $containertype . ' '.$container;
	}	

	if(!empty($_POST)) {
		if (isset($_FILES['image']['error']) || is_array($_FILES['image']['error'])) {
			// Check $_FILES['image']['error'] value.
			switch ($_FILES['image']['error']) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					echo "<p>No file sent.</p>";
					break;
				case UPLOAD_ERR_INI_SIZE:
					break;
				case UPLOAD_ERR_FORM_SIZE:
					echo "<p>Exceeded fileseize limit.</p>";
					break;
				default:
					echo "<p>Unknown errors.</p>";
					break;
			}
		}

		if ($_FILES['image']['size'] > 2000000) {
			echo 'Exceeded filesize limit. Please reduce picture quality or size, or compress the file.';
			die();
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);

		if (false === $ext = array_search($finfo->file($_FILES['image']['tmp_name']),
		array(
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'gif' => 'image/gif',
		),
		true)) {
			throw new RuntimeException('Invalid file format.');
			echo 'Invalid file format';
			die();
		}
		
		$rand = rand(100,2000);
		$name1 = str_replace(" ", "_", $name);
		$filename = $name1."_".$rand.$_FILES[file][name];
		
		if (!move_uploaded_file($_FILES['image']['tmp_name'],sprintf('./uploaded/%s.%s',$filename,$ext))) {
			echo 'Failed to move uploaded file.';
		}
		
		//echo 'File is uploaded successfully.';

	}
	
	else
		echo "<p>Submitted form was empty.</p>";

	$imageURL = "/uploaded/".$filename. "." .$ext;

	$DBConnect = server_connect("<p>Unable to connect to server</p>");
	database_connect($DBConnect, "<p>Unable to connect to database.</p>");

	$SQLstring = "INSERT INTO `house`(`id`, `name`, `description`, `checkStatus`, `Retired`, `added_by`, `containerID`, `imageURL`, `type`) VALUES (NULL,'$name','$description',1,0,'$added_by','$location','$imageURL','$itemType')";

	$QueryResult = mysql_query($SQLstring)
		Or die("<p>Unable to add item.</p>"
		. "<p>Error code " . mysql_errno($DBConnect)
		. ": " . mysql_error($DBConnect)) . "</p>";

	mysql_free_result($QueryResult);
	mysql_close($DBConnect);
	
	echo '<form action="addItem.php"><input type="submit" value="Back"></form>';
?>


<?php
	include 'footer.php';
?>