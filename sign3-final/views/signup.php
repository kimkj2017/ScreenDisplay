<!--DOCTYPE html-->
<?php

$userName = getVar("uid");
$displayName = getVar("displayName");
$password = getVar("pass");
$adminUser = getVar("adminUser");

// if (isset($_POST['uid']) && isset($_POST['pass'])) {
if ($userName != "" && $password != "") {
  require_once('cse383-f16-screen/models/userModel.php');
  if ($adminUser == "") {
    $adminUser = 0;
  }
  $result = createUser($userName, $displayName, $password, $adminUser);
  if ($result) {
    header("Location: ?cmd=signupsuccess");
  } else {
    header("Location: ?cmd=signup&err=signupfail");
  }
}
//  exit;  
 // $username = htmlspecialchars($_POST['uid']);
 // $password = hash('sha256', htmlspecialchars($_POST['pass']));
 // $pk = rand(10000, 100000000);
 // $conn = new mysqli('localhost', 'ostkklm', 'servicewebs', 'uids');
 // if ($conn->connect_errno) {
 //   die('Failed to connect the db...');
 // }
//  $pks = $conn->query("SELECT pk FROM usertbl where pk = $pk;");
//  while ($pks->num_rows != 0) {
//    $pk = rand(10000, 1000000000);
//    $pks = $conn->query("SELECT pk FROM usertbl where pk = $pk;");
//  }
//  $uids = $conn->prepare("SELECT uid FROM usertbl where uid=?");
//  $uids->bind_param("s", $username);
//  $uids->execute();
//  $uids->bind_result($uidss);
//  if ($uids->fetch()) {
//    die("The username $uidss exists already, so choose different one.");
//  }
  //header("Location: process.php?pk=$pk&uid=$username&pass=$password");
  //if ($insert_query = $conn->prepared("INSERT INTO usertbl VALUES (?, ?, ?)")) {
  //  $insert_query->bind_param('s', checkEntry($entry));
  //  $insert_query->bind_param('s', $password);
  //  $insert_query->execute();
  //  $result = $insert_query->get_result();
  //}
//  $stmt = $conn->prepare("INSERT INTO usertbl (pk, uid, password) VALUES (?, ?, ?)");
//  $stmt->bind_param("iss", $pk, $username, $password);
//  $result = $stmt->execute();
//  if ($result) {
//    die("Successfully registered you! <a href='./login.php'>Go to login page</a>");
//  }
//  else {
//    die("Failed to register.");
//  }
//}
?>
<!--html>
<head>
<title>Kimk3 Openstack signup sheet</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
.row.content {height: 550px}

/* Set gray background color and 100% height */
.sidenav {
	background-color: #f1f1f1;
height: 100%;
}

/* On small screens, set height to 'auto' for the grid */
@media screen and (max-width: 767px) {
	.row.content {height: auto;}
}
</style>
</head>
<body-->
<div class='container' style='max-width: 40%;'>
  <h1>Sign Up</h1>
<?php

$signuperr = getVar("err");
if ($signuperr === "signupfail") {
  echo('<div class=\'alert alert-danger\'>An unexpected error occurred during signup. Please try again.</div>');
} elseif ($signuperr != "") {
  echo('<div class=\'alert alert-danger\'>Unauthorized access.</div>');
}

?>
  <div class='well'>
    <form action='' method='post'>
      <div class='form'>
        <label for='uid'>Username</label>
        <input type='text' name='uid' id='uid' class='form-control'>
      </div>
      <div class='form'>
        <label for='displayName'>Display Name (Optional)</label>
        <input type='text' name='displayName' id='displayName' class='form-control'>
      </div>
      <div class='form'>
        <label for='pass'>Password</label>
        <input type='password' name='pass' id='pass' class='form-control'>
      </div>
      <input type='hidden' name='adminUser' id='adminUser' value='0'>
      <div class='form-inline'>
        <input type='submit' class='btn btn-primary' style='margin-top: 10px;'>
    </form>
  </div>
</div>
<!--/body>
</html-->
