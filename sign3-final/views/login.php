<!--
KWANGJU KIM
CSE 383
FALL 16
Openstack PHP assignment
-->
<?php
//if (isset($_POST['uid']) && isset($_POST['pass'])) {
// require_once('cse383-f16-screen/models/userModel.php'); 
//}
// $request = file_get_contents('php://input');
// var_dump($request);
// die();
//header('Location: ?cmd=login');
?>
<!--DOCTYPE HTML>
<html>
<head>
<title>Kimk3 Openstack Portal</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script-->
<style>
/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
.row.content {height: 550px}

/* Set gray background color and 100% height */
.sidenav {
	background-color: #f1f1f1;
height: 100%;
}

.form-margin {
  padding: 10px;
}

.forty-perc-lim {
  max-width: 40%;
}

/* On small screens, set height to 'auto' for the grid */
@media screen and (max-width: 767px) {
	.row.content {height: auto;}
}
</style>
<!--/head>
<body-->
<div class='container forty-perc-lim'>
<h1>Screen Management System</h1>
<?php
if (isset($_REQUEST['cmd'])) {
  if ($_REQUEST['cmd'] === 'error') {
    echo('<div class="alert alert-danger">The unexpected error has occurred');
    if (isset($_REQUEST['error'])) {
      echo(': '.$_REQUEST['error']);
    }
    echo('. Please try again.</div>');
  } elseif ($_REQUEST['cmd'] === 'fail') {
    echo('<div class="alert alert-danger">The username or password doesn\'t match with our record. Please submit the valid username and password.</div>');
  } elseif ($_REQUEST['cmd'] === 'signupsuccess') {
    echo('<div class="alert alert-success">Signed up successfully. Please log in with your credentials.</div>');
  }
  session_destroy();
}
?>
<div class='well'>
<form id='loginform' method='post' action=''>
  <div class='form form-margin'>
    <label for='uid'>Username</label>
    <input type='text' class='form-control' id='user' name='user'>
  </div>
  <div class='form form-margin'>
    <label for='pass'>Password</label>
    <input type='password' class='form-control' id='password' name='password'>
  </div>
  <div class='form-inline form-margin'>
    <input type='hidden' id='cmd' name='cmd' value='login'>
    <input type='submit' class='btn btn-primary'> 
  </div>
</form>
<div class='form-inline'>
<button class='btn btn-link' onclick="window.location.href='?cmd=signup';">Don't have a username? Sign up here!</button>
</div>
</div>
</script>
<!--footer>
(C) 2016 Kwangju Kim. All Rights Reserved.
</footer>
</div>
</body>
</html-->
