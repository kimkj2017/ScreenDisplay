<?php
/*
CSE383 F16 User Model
*/

require_once("db.php");

$userError="";

// Validate a user - return true if username and password
// are in the database and false otherwise
function validate($username, $password) {
  global $conn;
  $stmt = $conn->prepare('SELECT id FROM user WHERE userName=? AND password=?');
  $hash = hash('sha256', $password);
  $stmt->bind_param('ss', $username, $hash);
  $stmt->execute();
  $stmt->bind_result($id);
  if($stmt->fetch()) {
	$result = array('id' => $id);
  } else {
	$result = array();
  }
  $stmt->close();
  if(sizeof($result) == 0) {
    return false;
  } else {
    return true;
  } 
}

// Gets the userName and displayName of each user
function getUserList() {
  global $conn;
  $stmt = $conn->prepare('SELECT id, userName, displayName, adminUser FROM user');
  $stmt->execute();
  $stmt->bind_result($id, $userName, $displayName, $adminUser);
  $result = array();
  // Need double array -> array of users which are arrays
  while($stmt->fetch()) {
    $result[] = array('userName' => $userName, 'displayName' => $displayName, 'adminUser' => $adminUser);
  }
  $stmt->close();
  return $result;
  
}

// Gets the user record for given user primary key, userName, displayName, and adminUser for one user
function getUser($userName) {
  global $conn;
  $stmt = $conn->prepare('SELECT id, userName, displayName, adminUser FROM user WHERE userName=?');
  $stmt->bind_param('s', $userName);
  $stmt->execute();
  $stmt->bind_result($id, $userName, $displayName, $adminUser);
  if($stmt->fetch()) {
	  $result = array('id' => $id, 'userName' => $userName, 'displayName' => $displayName, 'adminUser' => $adminUser);
  } else
	  $result = array();
  $stmt->close();
  return $result;
}

// Creates a user with the provided information
function createUser($userName, $displayName, $password, $adminUser) {
  global $conn;
  // Prepare the statement - error reporting help from 
  // http://stackoverflow.com/questions/2552545/mysqli-prepared-statements-error-reporting
  $stmt = $conn->prepare('INSERT INTO user (userName, displayName, password, adminUser) VALUES (?,?,?,?)');
  if($stmt === false) {
   error_log('prepare() failed: ' . htmlspecialchars($stmt->error));
   $userError = "prepare failed";
   return false;
  } 

  // Hash the password, then bind the parameters
  $hash = hash('sha256', $password);
  $a = $stmt->bind_param("sssi", $userName, $displayName, $hash, $adminUser);
  if($a === false) {
   error_log('bind_param() failed: ' . htmlspecialchars($stmt->error));
   $userError="error on bind";
   return false;
  }

  // Execute the statement
  $result = $stmt->execute();
  if($result === false) {
   error_log('execute() failed: ' . htmlspecialchars($stmt->error));
   $userError="error on execute";
   return false;
  } 
  $stmt->close();
  return true;
}

// Update a user with the current user name to have the new user name,
// display name, and admin status
function updateUser($currUserName, $newUserName, $displayName, $password, $adminUser) {
 global $conn;
 $stmt = $conn->prepare('UPDATE user SET userName=?, displayName=?, password=?, adminUser=? WHERE userName=?');
 $hash = hash('sha256', $password);
 $stmt->bind_param('sssis', $newUserName, $displayName, $hash, $adminUser, $currUserName);
 $stmt->execute();
 $stmt->close();
}

// Deletes a user with the given userName
function deleteUser($userName) {
 global $conn;
 $stmt = $conn->prepare('DELETE FROM user WHERE userName=?');
 $stmt->bind_param('s', $userName);
 $stmt->execute();
 $stmt->close();
}
?>
