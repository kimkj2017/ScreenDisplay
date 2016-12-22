<?php
/*
Scott Campbell
383-f16

Database connector
*/

require_once("config.php");
if (!isset($config)) {
    die ("config file not loaded");
}

// Create connection
$conn = new mysqli($config['host'],$config['user'],$config['password'],$config['db']);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

?>
