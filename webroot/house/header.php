<!DOCTYPE html>
<html>
<?php
	session_cache_expire(10);
	session_start();
?>
    	<head>
        	<title>PA House System</title>
        	<!-- Bootstrap -->
        	<link href="bootstrapmin.css" rel="stylesheet" media="screen">
        	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
        	<!-- <link href="assets/css/bootstrap-responsive.css" rel="stylesheet"> -->
		<!-- <link rel="stylesheet" type="text/css" href="stylesheets/iphonestyle.css"> -->

		<?php
			include 'database_connect.php';
		?>

	

    	</head>
    	<body>
    		<div class="container-fluid">
            		<div class="row-fluid">
                		<h1><a class="one" href="/house">PA House System</a></h1>
            		</div>