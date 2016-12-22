<?php
 include 'userModel.php';

 // Get the last part of the url (after last / )
 function getLastUrlToken() {
  if(isset($_SERVER['REQUEST_URI'])) {
    $url = $_SERVER['REQUEST_URI'];
    $tokens = explode('/', $url);
    return $tokens[sizeof($tokens)-1];
  } else {
    return 'Error in getLastUrlToken() in user.php';
  }
 }

 // Get the body of an HTTP requrest as an associative array
 /*function getBody() {
  $content = file_get_contents('php://input');
  $data = $json_decode($content, true);
  return $data;
 }*/

 header('Content-Type: application/json');

 // Process the request if present
 if(isset($_SERVER['REQUEST_METHOD'])) {
  $method = $_SERVER['REQUEST_METHOD'];
  
  // If this is a GET method, figure out if the request
  // is for the list of users (rest/user.php/) or for
  // a specific user (rest/user.php/userName)
  if($method == 'GET') {
   // Get the url and split it into tokens
//   if ($_REQUEST['cmd'] === 'login') {
//     if (validate($_REQUEST['user'], $_REQUEST['password'])) {
//       echo json_encode(array('status'=>'ok'));
//     } else {
//       echo json_encode(array('status'=>'fail'));
//     }
//     exit;
//   }
   $lastToken = getLastUrlToken();
   if($lastToken == 'user.php' || $lastToken == '') {
    // Get the whole list of users
    echo json_encode(getUserList());
   } else {
    // Get the information for the specified user
    echo json_encode(getUser($lastToken));
   }
  } 

  // Udpate a user with the given userid and the information
  // contained in the json body
  elseif($method == 'PUT') {
   // Get which user we are updating
   $userName = getLastUrlToken();
   // Get the data for this user - should be just the displayName
   // and adminUser
   $content = file_get_contents('php://input');
   $data = json_decode($content, true);
   if($data != null) {
    updateUser($userName, $data['userName'], $data['displayName'], $data['password'], $data['adminUser']);
   } else {
    echo 'error';
   }
  } 

  elseif($method == 'POST') {
  // If the method is a PUT, we should create a user with
  // the information contained in the body
   $content = file_get_contents('php://input');
   $data = json_decode($content, true);
   //$data = getBody();
   if($data != null) {
    createUser($data['userName'], $data['displayName'], $data['password'], $data['adminUser']);
   } else {
    echo 'error';
   }
  } 

  // If the method is DELETE, then get which user we are removing
  elseif($method == 'DELETE') {
   $userName = getLastUrlToken();
   deleteUser($userName);
  } 

  else {
   header('HTTP/1.1 500 Invalid Request');
   exit();
  }
 }
?>
