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

<script language="Javascript">
<!--
function ShowPicture(id,Source) {
if (Source=="1"){
if (document.layers) document.layers[''+id+''].visibility = "show"
else if (document.all) document.all[''+id+''].style.visibility = "visible"
else if (document.getElementById) document.getElementById(''+id+'').style.visibility = "visible"
}
else
if (Source=="0"){
if (document.layers) document.layers[''+id+''].visibility = "hide"
else if (document.all) document.all[''+id+''].style.visibility = "hidden"
else if (document.getElementById) document.getElementById(''+id+'').style.visibility = "hidden"
}
}
//-->
</script>
<style type="text/css">
#Style {
position:absolute;
visibility:hidden;

padding:5px;

</style>

</head>

<body>
<div class="container-fluid">
	<div class="row-fluid">
     	<h1><a class="one" href="/house">PA House System</a></h1>
	</div>
	<a href="#" onMouseOver="ShowPicture('Style',1)" onMouseOut="ShowPicture('Style',0)">Mouse Here To Show Image</a>
	<div id="Style" style='display: block; width: 32%; margin-left: auto; margin-right:auto'><img src="roomPlan.jpg"></div> 
</div>
</body>
</html>