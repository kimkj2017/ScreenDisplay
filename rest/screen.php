<!DOCTYPE html>
<html lang="en">

<head>
<title>Screen</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<?php
include 'config.php';

$name;
$ipAddress;
$destination;
$title;
$location;
$lastHeard;
$method;
$dataToReturn;
//Update
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	foreach ($_GET as $key=>$value) {
		switch ($key) {
		case "name":
			$name = urldecode($value);
		case "ipAddress":
			$ipAddress = urldecode($value);
		case "desc":
			$destination = urldecode($value);
		}
  	}

	if(isset($ipAddress) && isset($name)){
		setIPAddress($name, $ipAddress);
	}
	else if(isset($destination) && isset($name)){
		setDesc($name, $destination);
	}
}
else if ($_SERVER['REQUEST_METHOD'] == "GET") {
	foreach ($_GET as $key=>$value) {
		if($key == "name"){
                        $name = urldecode($value);
                }
                else if($key == "method"){
                        $method = urldecode($value);
                }
  	}
	if(isset($name)){
		switch ($method) {
		case "desc":
			$dataToReturn = getDesc($name);
		case "lastHeard":
			$dataToReturn = getLastHeard($name);
		case "location":
			$dataToReturn = getLocation($name);
		case "ipAddress":
			$dataToReturn = getIPAddress($name);
                }
	}
	else {
		$dataToReturn = getAllScreens();
	}
	header("HTTP/1.1 200 Ok");
	header("content-type: application/json");
	print json_encode($dataToReturn);
	exit();
}
//Create
else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	foreach ($_GET as $key=>$value) {
		switch ($key) {
		case "name":
                        $name = urldecode($value);
                case "ipAddress":
                        $ipAddress = urldecode($value);
                case "destination":
                        $destination = urldecode($value);
		case "title":
                        $title = urldecode($value);
		case "location":
                        $location = urldecode($value);
		case "lastHeard":
                        $lastHeard = urldecode($value);
		}
  	}
	Screen($title, $location, $destination, $lastHeard, $ipAddress);
}
else {
	header("HTTP/1.1 400 Method not recognized");
	exit();
}

function getAllScreens() {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
	if ($conn->connect_error) {
        	echo "Connection failed " . $conn->connect_error;
		header("HTTP/1.1 400 Invalid connection to database");
        	exit();
	}

	$sql = "SELECT title FROM Screen";

	$result = $conn->query($sql);
	$returnData;
        if ($result->num_rows > 0) {
		echo "Got here";
                $num = 0;
                while($row = $result->fetch_assoc()) {
                        $returnData[$num] =  $row['title'];
			$num = $num + 1;
                }
		echo $num;
        }
	echo "Here too";
        $conn->close();
        return $returnData;
}

function getDesc($screen) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
        if ($conn->connect_error) {
	        header("HTTP/1.1 400 Invalid connection to database");
        	exit();
	}

	$sql = "SELECT destination FROM Screen WHERE title='$screen'";
        $result = $conn->query($sql);
        $returnData;
        if ($result->num_rows > 0) {
                $num = 0;
                while($row = $result->fetch_assoc()) {
                        $returnData[$num] =  $row['destination'];
                        $num = $num + 1;
                }
        }
        $conn->close();
        return $returnData;
}

function getLastHeard($screen) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
        if ($conn->connect_error) {
                header("HTTP/1.1 400 Invalid connection to database");
                exit();
        }

	$sql = "SELECT lastHeard FROM Screen WHERE title='$screen'";
        $result = $conn->query($sql);
        $returnData;
        if ($result->num_rows > 0) {
                $num = 0;
                while($row = $result->fetch_assoc()) {
                        $returnData[$num] =  $row['lastHeard'];
                        $num = $num + 1;
                }
        }
        $conn->close();
        return $returnData;
}

function getLocation($screen) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
        if ($conn->connect_error) {
                header("HTTP/1.1 400 Invalid connection to database");
                exit();
        }

	$sql = "SELECT location FROM Screen WHERE title='$screen'";
        $result = $conn->query($sql);
        $returnData;
        if ($result->num_rows > 0) {
                $num = 0;
                while($row = $result->fetch_assoc()) {
                        $returnData[$num] =  $row['location'];
                        $num = $num + 1;
                }
        }
        $conn->close();
        return $returnData;
}

function getIPAddress($screen) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
        if ($conn->connect_error) {
                header("HTTP/1.1 400 Invalid connection to database");
                exit();
        }

	$sql = "SELECT ipAddress FROM Screen WHERE title='$screen'";
        $result = $conn->query($sql);
        $returnData;
        if ($result->num_rows > 0) {
                $num = 0;
                while($row = $result->fetch_assoc()) {
                        $returnData[$num] =  $row['ipAddress'];
                        $num = $num + 1;
                }
        }
        $conn->close();
        return $returnData;
}

function setIPAddress($screen, $IPAddress) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
	if ($conn->connect_error) {
                header("HTTP/1.1 400 Invalid connection to database");
                exit();
        }

	$sql = "UPDATE Screen SET ipAddress='$IPAddress' WHERE title='$screen'";
        $conn->query($sql);
        $conn->close();
}

function setDesc($screen, $desc) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
        if ($conn->connect_error) {
                header("HTTP/1.1 400 Invalid connection to database");
                exit();
        }

	$sql = "UPDATE Screen SET destination='$desc' WHERE title='$screen'";
        $conn->query($sql);
        $conn->close();
}

function Screen($title, $location, $desc, $lastHeard, $ipAddress) {
	include 'config.php';
	$conn = new mysqli("localhost", $user, $password, "projectData");
        if ($conn->connect_error) {
                header("HTTP/1.1 400 Invalid connection to database");
                exit();
        }

	$sql = "INSERT INTO Screen (title, location, destination, lastHeard, ipAddress) VALUES ('$title', '$location', '$desc', '$lastHeard', 'ipAddress')";
	$conn->query($sql);
        $conn->close();
}
?>

</html>

