<?php

require_once('db.php');


if (!$conn) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

function validate($userName, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT count(*) FROM user WHERE userName = ? AND password = ?");
    $stmt->bind_param("ss", $userName, $password);
    if (!$stmt->execute()) {
        error_log("error on validate ".$conn->error);
        return false;
    }
    $stmt->bind_result($count);
    while ($stmt->fetch());
    if ($count !== 1) {
       return false;
    }
    return true;
}



?>
