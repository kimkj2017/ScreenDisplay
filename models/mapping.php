<?php
/*
   Scott Campbell and Class
   CSE383 f16
   Mapping file
   creates map entries between DisplayItems and Screens
 */

include('db.php');

class Mapping {

	function getDisplayContent($dId) {
		global $conn;
		$sql = "SELECT * FROM Mapping WHERE displayId = $dId";
		$result = $conn->query($sql);
		$response = array();
		if ($result) {
			while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
				$response[] = $row;
			}
		}
		return $response;
	}

	function deleteMap($dId, $cId) {
		global $conn;
		$sql = "DELETE FROM Mapping WHERE displayId = $dId AND contentId = $cId";
		$result = $conn->query($sql);
		return $result;
	}

	function createMap($dId, $cId, $sD, $eD, $ord) {
		global $conn;
		$sql = "INSERT INTO Mapping (displayId, contentId, startDate, endDate, priority) VALUES ($dId, $cId, '$sD', '$eD', $ord)";
		$result = $conn->query($sql);
		return $result;
	}

	function getAll() {
		global $conn;
		$sql = "SELECT * FROM Mapping";
		$result = $conn->query($sql);
		$response = array();
		if ($result) {
			while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
				$response[] = $row;
			}
		}
		return $response;
	}

	function updateContent($id, $dId, $cId, $sD, $eD, $ord) {
		global $conn;
		$sql = "UPDATE Mapping SET displayId=$dId,contentId=$cId,startDate='$sD',endDate='$eD',priority=$ord WHERE id=$id";
		$result = $conn->query($sql);
		return $result;;
	}
}  
?>
