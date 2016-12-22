<?
/*
   Scott Campbell
   383-f16

   Data model for Screens
 */

require_once("models/db.php");

function getScreenList() {
	global $conn;
	$stmt = $conn->prepare("select pk,title from screen order by pk");
	if (!$stmt) 
	{
		error_log("Error on getScreenList statement " . $conn->error);
		return false;
	}
	$stmt->execute();
	$stmt->bind_result($pk,$title);
	$list = array();
	while ($stmt->fetch()) {
		$list[] = array('pk'=>$pk,'title'=>$title);
	}
	return $list;

}

// get single screen 
function getScreen($screenPK) {
	global $conn;
	$stmt = $conn->prepare("select title,location,destination,lastHeard,ipAddress,width,height,pk from screen where pk=?");
	if (!$stmt) 
	{
		error_log("Error on getScreen statement " . $conn->error);
		return false;
	}

	$stmt->bind_param("s", $screenPK);
	if (!$stmt->execute()) 
	{
		error_log("Error on getScreen statement " . $conn->error);
		return false;
	}
	$stmt->bind_result($title,$location,$destination,$lastHeard,$ipAddress,$width,$height,$pk);
	if ($stmt->fetch()) {
		$result['title'] = $title;
		$result['location'] = $location;
		$result['destination'] = $destination;
		$result['lastHeard']  = $lastHeard;
		return $result;
	} else
	{
		return null;
	}
}

//add a to the database
function addScreen($title,$location,$destination,$ipAddress,$width,$height){
	global $conn;

	$stmt = $conn->prepare("insert into screen (`title`,`location`,`destination`,`ipAddress`,`width`,`height`) values (?,?,?,?,?,?)");
	if (!$stmt) 
	{
		error_log("Error on addScreen statement " . $conn->error);
		return false;
	}

	$stmt->bind_param("ssssss", $title,$location,$destination,$ipAddress,$width,$height);
	if (!$stmt->execute())
	{
		error_log("Error on addScreen statement " . $conn->error);
		return false;
	}
	return "ok";
}

function updateScreen() {
}

function deleteScreen($pk) {
	global $conn;

	if ($pk>0) {
		$stmt = $conn->prepare("delete from screen where pk=?");
		if (!$stmt)
		{
			error_log("Error on delete statement " . $conn->error);
			return false;
		}

		$stmt->bind_param("s", $pk);
		if (!$stmt->execute())
		{
			error_log("Error on delete statement " . $conn->error);
			return false;
		}
		return TRUE;
	} else
		return FALSE;

}

?>
