<?php
/*
Scott Campbell
Display Sign System
Main component - CONTROLLER
Controller handles all incoming calls

Right now to login simply issued cmd=login
*/

session_start();

function getVar($name) {
	if (isset($_REQUEST[$name]))
		return htmlspecialchars($_REQUEST[$name]);
	else
		return "";
}

//set security information
$index_check=1;

$cmd = getVar('cmd');
if (!isset($_SESSION['user'])) {
	if ($cmd=="login") {
		$password = getVar('password');
		if ($password !=="") {
		//	$password = hash('sha256',$password);
		} else {
			header('Location: index.php?cmd=error');
			exit;
		}
		$username = getVar('user');
		if ($username === "") {
			header('Location: index.php?cmd=error');
			exit;
		}
		require_once('./cse383-f16-screen/models/userModel.php');
		if(!validate($username, $password)) {
			header('Location: index.php?cmd=fail');
			exit;
		}
		$_SESSION['user'] = $username;
		header('Location: index.php?msg=Welcome,%20'.$_SESSION['user']);//.$_SESSION['user']);
        } elseif ($cmd==='signup') {
		$viewContent="views/signup.php";
		include "views/template.php";
//		header('Location: index.php?cmd=error&error=Under construction');
		exit;
        } else {
		$viewContent="views/login.php";
		include "views/template.php";
		exit;
	}
}

switch ($cmd) {
	case "logout":
		unset($_SESSION['user']);
		header("Location: index.php");
		exit;
	case "about":
		$viewContent="about.php";
		break;
	case "displayItems":
		if (isset($_REQUEST['pk'])) {
			$viewContent="views/displayItemsIndiv.php";
		}
		else {
			//default view
			$viewContent="views/displayItemsView.php";
		}
		//called by template.php to set head items
		function displayStyles() {
			print "<link rel='stylesheet' href='css/displayItems.css'>";
			include "views/displayHead.php";
		}
		//set myStles as callback for template.php
		$viewHeader="displayStyles";
		break;
	case "displayItemSubmit":
		//
	case "addItem":
		$viewContent = 'views/addDisplayItems.php';
		break;
	case "editItem":
		$viewContent = 'views/addDisplayItems.php';
		break;
	case "delItem":
		$pk=getVar("pk");
		if ($pk==="") {
			header("location: index.php?cmd=displayItems&errormsg=Invalid Delete Request");
		} else
		{
			require_once("cse383-f16-screen/models/displayItems.php");
			$result = deleteDisplayItem($pk);
			if (!$result)
				header("location: index.php?cmd=displayItems&errormsg=Delete Request Failed");
			else
				header("location: index.php?cmd=displayItems&msg=Item Deleted");
		}
	default:
	$viewContent = "default.php";
	break;
}
//include base template
include "views/template.php";
?>
