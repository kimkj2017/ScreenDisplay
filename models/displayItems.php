<?php
/*
   Scott Campbell and class
   Keep track of display items
 */

require_once("db.php");

if (!$conn)
{
	die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$displayError="";
$displayItemsList = array('textValue','startDate','endDate','creator','fileName','imgValue','content');


//  return a list of item pk's
function getDisplayItemList($limit=100000,$offset=0,$order='pk') {
	global $conn;
	$stmt = $conn->prepare("select pk from DisplayItems order by $order limit $limit offset $offset");
	if (!$stmt)
	{
		error_log("error on getScreenList" .$conn->error);
		return false;
	}
	if (!$stmt->execute()) {
		error_log("Error on execute");
		return false;
	}
	$list = array();
	$stmt->bind_result($pk);
	while ($stmt->fetch()) {
		$list[] = array('pk'=>$pk);
	}
	return $list;

}


// get total count of images
function getDisplayItemCount() {
	global $conn;
	$stmt = $conn->prepare("select count(*) from DisplayItems");
	if (!$stmt) {
		error_log("error on getCount".$conn->error);
		return false;
	}
	if (!$stmt->execute()) {
		error_log("error on getCount exec");
	}
	$stmt->bind_result($count);
	if ($stmt->fetch()) {
		return $count;
	}
	return 0;
}

// get single item
function getDisplayItem($PK) {
	global $conn;
	$stmt = $conn->prepare("select pk,textValue,startDate,endDate,creator,fileName,imgValue,version,content from DisplayItems where pk=?");
	if (!$stmt)
	{
		error_log("error on getScreen prepare" .$conn->error);
		return false;
	}
	$stmt->bind_param("s", $PK);
	if (!$stmt->execute()) 
	{
		error_log("error on execute get displayitem");
		return false;
	}
	$stmt->bind_result($pk,$textValue,$startDate,$endDate,$creator,$fileName,$imgValue,$version,$content);

	$result=array();
	if ($stmt->fetch()) {
		$result=array('pk'=>$pk,'textValue'=>$textValue,'startDate'=>$startDate,'endDate'=>$endDate,'creator'=>$creator,'fileName'=>$fileName,'imgValue'=>$imgValue,'version'=>$version,'content'=>$content);
	} else
	{
		return null;
	}
	return $result;
}

// get random display item
function getRandomDisplayItem() {
	$itemlist = getDisplayItemList();
	$count = getDisplayItemCount();
	$idx = rand(0, $count - 1);
	$pk = $itemlist[$idx]['pk'];
	return getDisplayItem($pk);
}

//add a to the database
function addDisplayItem($textValue,$startDate,$endDate,$creator,$fileName,$imgValue,$content=""){
	global $conn;

	$stmt = $conn->prepare("insert into DisplayItems (`textValue`,`startDate`,`endDate`,`creator`,`fileName`,`imgValue`,`content`) values (?,?,?,?,?,?,?)");
	if (!$stmt) 
	{
		error_log("error on addDIsplayItem " .$conn->error);
		$displayError=$conn->error;
		return false;
	}

	$stmt->bind_param("sssssss", $textValue,$startDate,$endDate,$creator,$fileName,$imgValue,$content);
	if (!$stmt->execute())
	{
		error_log("error on addDIsplayItem " .$conn->error);
		$displayError=$conn->error;
		return false;
	}

	return true;
}

//update item in database
//data is in array $data -> only updates given data
/*
   this uses a "cute" technique to pass variable data to bind_param. Create a REFERENCE based array
   to the data and then use the call_user_func_array function to invoke bind_param with the variable length data
 */
function updateDisplayItem($pk,$data) {
	global $conn;
	global $displayItemsList;
	//list of available items to update
	$sqlList = "";	//used to create the update sql statement
	$comma = "";	//used to put comma ONLY after first item is added
	$bindParams=array(); //dynamic length array holding data for update
	$bindTypes="";	//creates the bind param types
	//must call this first since the first array elmenet is the types of the bind params
	foreach ($displayItemsList as $i) {
		if (isset($data[$i])) {
			$bindTypes .= "s";
		}
	}

	$bindParams[] = &$bindTypes;	//append the bind types to the bind params array
	//create rest of the update statement - for every element provided create sql and bindparam entries
	foreach ($displayItemsList as $i) {
		if (isset($data[$i])) {
			$sqlList .= $comma . "$i=?";
			$comma=",";
			$bindParams[] = &$data[$i];
		}
	}

	//do the actual update
	$sql = "update DisplayItems set " . $sqlList . " where pk='" . $pk . "'";
	$stmt = $conn->prepare($sql);
	if (!$stmt) 
	{
		error_log("error on addDIsplayItem " .$conn->error);
		return false;
	}

	//$stmt->bind_param("ssssss", $textValue,$startDate,$endDate,$creator,$fileName,$imgValue);
	call_user_func_array(array($stmt,'bind_param'),$bindParams);
	if (!$stmt->execute())
	{
		error_log("error on addDIsplayItem " .$conn->error);
		return false;
	}

	return "ok";
}

//update item in database with version check
//data is in array $data -> only updates given data
/*
   this uses a "cute" technique to pass variable data to bind_param. Create a REFERENCE based array
   to the data and then use the call_user_func_array function to invoke bind_param with the variable length data
 */
function updateDisplayItemVersionCheck($pk,$data) {
	global $conn;
	global $displayItemsList;

	//see if versions match
	$old = getDisplayItem($pk);
	if ($data['version'] != $old['version']) {
		$displayError = "Version Error";
		return false;
	}
	$data['version']++;

	//list of available items to update
	$sqlList = "";	//used to create the update sql statement
	$comma = "";	//used to put comma ONLY after first item is added
	$bindParams=array(); //dynamic length array holding data for update
	$bindTypes="";	//creates the bind param types
	//must call this first since the first array elmenet is the types of the bind params
	foreach ($displayItemsList as $i) {
		if (isset($data[$i])) {
			$bindTypes .= "s";
		}
	}
	$bindTypes .= "s";	//add for version

	$bindParams[] = &$bindTypes;	//append the bind types to the bind params array
	//create rest of the update statement - for every element provided create sql and bindparam entries
	foreach ($displayItemsList as $i) {
		if (isset($data[$i])) {
			$sqlList .= $comma . "$i=?";
			$comma=",";
			$bindParams[] = &$data[$i];
		}
	}
	$sqlList .= $comma . "version=?";
	$bindParams[] = &$data['version'];

	//do the actual update
	$sql = "update DisplayItems set " . $sqlList . " where pk='" . $pk . "'";
	$stmt = $conn->prepare($sql);
	if (!$stmt) 
	{
		error_log("error on addDIsplayItem " .$conn->error);
		return false;
	}

	//$stmt->bind_param("ssssss", $textValue,$startDate,$endDate,$creator,$fileName,$imgValue);
	call_user_func_array(array($stmt,'bind_param'),$bindParams);
	if (!$stmt->execute())
	{
		error_log("error on addDIsplayItem " .$conn->error);
		return false;
	}

	return "ok";
}


function deleteDisplayItem($pk) {
	global $conn;

	if ($pk>0) {
		$stmt = $conn->prepare("delete from DisplayItems where pk=?");
		if (!$stmt)
		{
			error_log("error on addDIsplayItem " .$conn->error);
			return false;
		}

		$stmt->bind_param("s", $pk);
		if (!$stmt->execute())
		{
			error_log("error on addDIsplayItem " .$conn->error);
			return false;
		}
		return TRUE;
	} else
		return FALSE;

}

function displayValidate($data) {
	$err="";
	if (!isset($data['textValue'])) {
		$err .= "No textValue Present";
	}
	if (!isset($data['startDate'])) {
		$err .= "No start Date Present";
	}
	if (!isset($data['endDate'])) {
		$err .= "No end Date Present";
	}
	if (!isset($data['creator'])) {
		$err .= "No creator Present";
	}

	if ($err != "")
		return $err;

	if ($data['textValue'] == "")
		$err = "<p>Please supply a Title</p>";

	if (!strtotime($data['startDate']))
		$err .= "<p>Invalid Start Date</p>";

	if (!strtotime($data['endDate']))
		$err .= "<p>Invalid End Date</p>";

	return $err;

}


?>
