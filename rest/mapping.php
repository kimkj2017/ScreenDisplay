<?php
/*

 */

include('../models/mapping.php');
$maps = new Mapping();


function invalidCall($msg) {
	header("HTTP/1.1 400 $msg");
	$r=array('status'=>'fail','error'=>$msg);
	print json_encode($r);
	exit();
}
function sendStatus($status) {
	header("HTTP/1.1 200 OK");
	$r=array('status'=>$status);
	print json_encode($r);
	exit();
}


header("content-type: application/json");

//parse path info
$pathInfo = array();
if (isset($_SERVER['PATH_INFO'])) {
	$pathInfo = explode("/",$_SERVER['PATH_INFO']);
	//remove first element which is always blank
	array_shift($pathInfo);
	//if first element is empty then remove it
	if ($pathInfo[0] === "") 
	{
		array_shift($pathInfo);
	}
}


//read json body
$content = file_get_contents('php://input');
$data = json_decode($content, true);


//handle GET
if ($_SERVER['REQUEST_METHOD']=="GET") {
	if (count($pathInfo)==0){
		//GET ALL
		$response = $maps->getAll();
		print json_encode($response);
	} else if (count($pathInfo)==1) {
		//GET DISPLAY CONTENT
		$response = $maps->getDisplayContent($pathInfo[0]);
		print json_encode($response);
	} else {
		// RETURN 400
		invalidCall('Invalid request');
	}
} else if ($_SERVER['REQUEST_METHOD']=="POST") {
	if (count($pathInfo) == 0) {
		//CREATE NEW
		if (!(isset($data['displayId'])) || !(isset($data['contentId'])) || !(isset($data['startDate'])) || !(isset($data['endDate'])) || !(isset($data['priority']))) {
			invalidCall("Required JSON elements not present");
		}

		$response = $maps->createMap($data['displayId'], $data['contentId'], $data['startDate'], $data['endDate'], $data['priority']);
		if (!$response) {
			invalidCall('Error creating mapping');
		} else {
		} 
	} else {
		// RETURN 400
		invalidCall('Invalid request'); 
	}
} else if ($_SERVER['REQUEST_METHOD']=="PUT") {
	if (count($pathInfo) != 1) {
		//Update
		$response = $maps->updateContent($pathInfo[0], $data['displayId'], $data['contentId'], $data['startDate'], $data['endDate'], $data['priority']);
		if (!$response) {
			invalidCall('Error updating mapping');
		} else {
			sendStatus("ok");
		}
	} else {
		// RETURN 400
		invalidCall('Invalid request');
	}
} else if ($_SERVER['REQUEST_METHOD']=="DELETE") {
	if (count($pathInfo) == 2) {
		// DELETE MAP
		if (!$maps->deleteMap($pathInfo[0], $pathInfo[1])) {
			invalidCall('Could not delete ID');
		}
		else
			sendStatus("ok");
	} else {
		//RETURN 400
		invalidCall('Invalid request');
	}
} else {
	invalidCall("Error - invalid method");
}
?>
