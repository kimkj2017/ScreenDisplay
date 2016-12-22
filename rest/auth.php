<?php
/*
   Scott Campbell
   CSE383
   Digitital Sign Project

   REST API for authenticating rest calls

 */

require_once('../models/auth.php');

session_start();

header('content-type: application/json');

function fail($msg) 	//send back failure
{
	$result['status'] = 'fail';
	$result['error'] = $msg;
	print json_encode($result);
	exit;
}


$method = $_SERVER['REQUEST_METHOD'];
$result=array();
//see if json body present
$content = file_get_contents('php://input');
$data = null;
if ($content)
	$data = json_decode($content,true);

	if ($method==="POST") {
		if (isset($_SESSION['userid'])) 
			fail("already logged in");
		if ($data == null) {
			fail("no json body present");
		} 
		if ((!isset($data['user'])) || (!isset($data['password']))) {
			fail("username or password not present");
		}

		if (validate($data['user'],$data['password'])) {
			$_SESSION['userid'] = $data['user'];
			$result['status'] = 'ok';
		} else
			fail("invalid credentials");
	} 
	//// DELETE Method
	else if ($method === "DELETE") {
		if (isset($_SESSION['userid'])) 
		{
			$result['status'] = 'ok';
			unset($_SESSION['userid']);
		}
		else 
			fail("not logged in");
	} else {
		fail("invalid request type");
	}

print json_encode($result);
?>

