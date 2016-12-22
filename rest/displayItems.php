<?php
/*
   Scott Campbell and class
 provide REST interface to display items

 */

//security check
$index_check=1;

require_once("../models/displayItems.php");

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
		$response = getDisplayItemList();
		print json_encode($response);
	} else if (count($pathInfo)==1) {
		if ($pathInfo[0] == 'random') {
			$response = getRandomDisplayItem();
			print json_encode($response);
		} else {//GET DISPLAY CONTENT
			$response = getDisplayItem($pathInfo[0]);
			print json_encode($response);
		}
	} else {
		// RETURN 400
		invalidCall('Invalid request');
	}
} else if ($_SERVER['REQUEST_METHOD']=="POST") {
	if (count($pathInfo) == 0) {
		//validate all data is present
		$ok=true;
		foreach($displayItemsList as $i) {
			if (!isset($data[$i]))
				$ok=false;
		}
		if (!$ok)
			invalidCall("Required JSON elements not present");

		$response = addDisplayItem($data['textValue'], $data['startDate'], $data['endDate'], $data['creator'], $data['fileName'],$data['imgValue'],$data['content']);
		if (!$response) {
			invalidCall('Error creating displayItem');
		} else {
			$r["status"]="ok";
			print json_encode($r);
			exit;
		} 
	} else {
		// RETURN 400
		invalidCall('Invalid request'); 
	}
} else if ($_SERVER['REQUEST_METHOD']=="PUT") {
	if (count($pathInfo) == 1) {
		//Update
		$response = updateDisplayItem($pathInfo[0], $data);
		if (!$response) {
			invalidCall('Error updating displayitem');
		} else {
			sendStatus("ok");
		}
	} else {
		// RETURN 400
		invalidCall('Invalid request - no item specified');
	}
} else if ($_SERVER['REQUEST_METHOD']=="DELETE") {
	if (count($pathInfo) == 1) {
		// DELETE item
		if (!deleteDisplayItem($pathInfo[0])) {
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
