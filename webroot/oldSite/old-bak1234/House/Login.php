<?php
ob_start();
$_SESSION['username'] = "";
$_SESSION['password'] = "";
$_SESSION['loggedin'] = 0;

include 'header.php';
?>

<?php
	if (isset($_POST['username']) && isset($_POST['password'])) {
	
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['password'] = $_POST['password'];

		$DBConnect = server_connect("Unable to connect to server!");
		database_connect("Unable to connect to database!");

		$username = $_SESSION['username'];
		$password = $_SESSION['password'];
		
		$sql = "SELECT *
        		FROM   users
        		WHERE username = '$username'";
		$QueryResult = mysql_query($sql)
			Or die("<p>User does not exist. Please go back and try again.</p>");
		
		if (mysql_num_rows($QueryResult) == 0) {
			echo "<p>User does not exist. Please go back and try again, or contact an administrator</p>";
			exit;
		}

		$Row = mysql_fetch_assoc($QueryResult);

		$obtainedPassword = $Row['password'];
		if($obtainedPassword==$password) {
			$_SESSION['loggedin']=1;
			header('Location: loginSuccess.php');
		}
		else {
			$_SESSION['username'] = "";
			$_SESSION['password'] = "";
			echo "<p>Incorrect password</p>";
		}
	}

	elseif (empty($_POST['username']) || empty($_POST['password'])) {
		echo "<p>Please enter both your username and password!</p>";
	}

	mysql_free_result($QueryResult);
	mysql_close($DBConnect);

?>

<form action="Login.php" method="post" enctype="application/x-www-form-urlencoded">
	<p><label>User Name: </label><input type="text" name="username" /><p>
	<p><label>Password: </label><input type="password" name="password" /<p>
	<p><input type="submit" value="Login" /></p>
</form>

<?php
include 'footer.php';
?>
