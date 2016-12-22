<?
/*
Scott Campbell

This is the main template - it is the only included from main.
It should never be directly called
*/

//security check to ensure it is being called from index
if (!isset($index_check))
	die("invalid invocation");
?>
<!DOCTYPE html>
<html lang="en">
<!--modified from https://blackrockdigital.github.io/startbootstrap-simple-sidebar/-->

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title>Screens</title>

<!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- Custom CSS -->
<link href="css/simple-sidebar.css" rel="stylesheet">
<script   src="https://code.jquery.com/jquery-3.1.1.min.js"   integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="   crossorigin="anonymous"></script>

<?php
//code to include view header
if (isset($viewHeader)) 
	call_user_func($viewHeader);
?>

</head>

<body>
<?php

if (!isset($_SESSION['user'])) {
  echo('<script src=\'js/bootstrap.min.js\'></script>');
  include $viewContent;
  exit;
}


?>
<div id="wrapper">

<!-- Sidebar -->
<div id="sidebar-wrapper">
<ul class="sidebar-nav">
<li class="sidebar-brand">
<a href="#">
Signage System
</a>
</li>
<li>
<a href="?cmd=displayItems"">DisplayItems</a>
</li>
<li>
<a href="#">Shortcuts</a>
</li>
<li>
<a href="#">Overview</a>
</li>
<li>
<a href="#">Events</a>
</li>
<li>
<a href="index.php?cmd=about">About</a>
</li>
<li>
<a href="index.php?cmd=logout">Logout</a>
</li>
</ul>
</div>
<!-- /#sidebar-wrapper -->

<!-- Page Content -->
<div id="page-content-wrapper">
<a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Toggle Menu</a>
<hr>
<div class="container-fluid">
<div class="row">
<div class="col-lg-12">
<?php 
$errormsg = getVar("errormsg");
if ($errormsg!="") {
		print '<div class="alert alert-danger"><strong>' . getVar("errormsg") . '</strong></div>';
}
$msg = getVar("msg");
if ($msg!="") {
		print '<div class="alert alert-info"><strong>' . getVar("msg") . '</strong></div>';
}

include $viewContent;
?>
<br/>
</div>
</div>
</div>
</div>
<!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>

<!-- Menu Toggle Script -->
<script>
$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
		});
</script>

</body>

</html>

