<?php

require_once('./cse383-f16-screen/models/displayItems.php'); 


// Upload file function is from W3Schools,
// http://www.w3schools.com/php/php_file_upload.asp
function uploadFile($id) {
  //var_dump($_FILES);
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($_FILES[$id]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image
  //if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES[$id]["tmp_name"]);
  if($check !== false) {
      //echo "File is an image - " . $check["mime"] . ".";
      $uploadOk = 1;
  } else {
      //echo "File is not an image.";
      //$uploadOk = 0;
  }
  //}
  // Check if file already exists
  //if (file_exists($target_file)) {
  //    echo "Sorry, file already exists.";
  //    $uploadOk = 0;
  //   $target_file = $target_dir.'copy_'.basename($_FILES[$id]["name"]); 
  //}
  // Check file size
  //if ($_FILES[$id]["size"] > 500000) {
  //    echo "Sorry, your file is too large.";
  //    $uploadOk = 0;
  //}
  // Allow certain file formats
  if(strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "png"
  && strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "gif" ) {
      //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed. Your img ext: $imageFileType";
      $uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
      //echo "Sorry, your file was not uploaded.";
  // if everything is ok, try to upload file
  } else {
      echo $target_file;
      if (move_uploaded_file($_FILES[$id]["tmp_name"], $target_file)) {
          //echo "The file ". basename( $_FILES[$id]["name"]). " has been uploaded.";
      } else {
          //echo "Sorry, there was an error uploading your file.";
          $uploadOk = 0;
      }
  }
  return $uploadOk;
}


function printForm($data=array('textValue'=>'','startDate'=>'','endDate'=>'','fileName'=>'','imgValue'=>'', 'version'=>0, 'content'=>''), $mode='addItem') {
    echo("<div class='form-group'>\r\n");
    echo("<form action='' method='post'");
    if ($mode === 'addItem') {
      echo(' enctype=\'multipart/form-data\'');
    }
    echo(">\r\n");
    echo("<label for='textValue'>Text Value</label>\r\n");
    echo("<input type='text' class='form-control' id='textValue' name='textValue' value='".$data['textValue']."'>\r\n");
    echo("<label for='startDate'>Start Date</label>\r\n");
    echo("<input type='text' class='form-control' id='startDate' name='startDate' value='".$data['startDate']."'>\r\n");
    echo("<label for='endDate'>End Date</label>\r\n");
    echo("<input type='text' class='form-control' id='endDate' name='endDate' value='".$data['endDate']."'>\r\n");
    // echo("<label for='creator'>Creator</label>\r\n");
    echo("<input type='hidden' id='creator' name='creator' value='".$_SESSION['user']."'>\r\n"); // Automatically gets username from SESSION
    echo("<label for='fileName'>File Name</label>\r\n");
    echo("<input type='text' class='form-control' id='fileName' name='fileName' value='".$data['fileName']."'>\r\n");
    if ($mode !== 'editItem') {
        echo("<label for='imgValue'>Image Upload</label>\r\n");
        echo("<input type='file' id='imgValue' name='imgValue'>\r\n");
    }
    echo("<label>Text Content (Please use HTML code)</label>\r\n");
    echo("<textarea class='form-control' id='content' name='content' style='font-family: \"Courier New\"; height: 300px;'>".$data['content']."</textarea>");
    echo("<input type='hidden' id='version' name='version' value='".($data['version'])."'>\r\n");
    echo("<input type='hidden' id='cmd' name='cmd' value='displayItemSubmit'>\r\n");
    echo("<input type='hidden' id='mode' name='mode' value='$mode'>\r\n");
    echo("<input type='submit' style='margin-top: 10px;' class='btn btn-primary'>\r\n");
    echo("</form>\r\n</div>\r\n");
    exit();
}

$cmd = getVar('cmd');

// block unauthorized access
if ($cmd == '') {
    printForm();
    exit();
}

$imgPk = getVar('pk');

// if it is an edit item, it should reflect the previous data
if ($cmd === 'editItem') {
    $imgData = getDisplayItem($imgPk);
    printForm($imgData, 'editItem');
    exit();
}

// if it is an add item,
if ($cmd !== 'displayItemSubmit') {
    if ($cmd !== 'addItem') {
        // using bogus way to access php, we will block here
        echo('<div class=\'alert alert-warning\'>Unauthorized access.</div>');
    }
    printForm();
    exit();
}

$textValue = getVar('textValue');

// FOrmat the start date
$startDate = strtotime(getVar('startDate'));
if (!$startDate) {
    echo('<div class=\'alert alert-danger\'>Unrecognized date: '.getVar('startDate').'</div>');
    printForm();
    exit();
}
$startDate = date('Y-m-d', $startDate);

// Format the end date
$endDate = strtotime(getVar('endDate'));
if (!$endDate) {
    echo('<div class=\'alert alert-danger\'>Unrecognized date: '.getVar('endDate').'</div>');
    printForm();
    exit();
}

$endDate = date('Y-m-d', $endDate);

$creator = getVar('creator');
$fileName = getVar('fileName');
//$imgValue = getVar('imgValue');
$version = getVar('version'); // get version
$content = getVar('content');

// Get submit mode
$submitMode = getVar('mode');

// If the submit mode is editItem
if ($submitMode === 'editItem') {
  $dataSubmit = array('textValue'=>$textValue, 'startDate'=>$startDate, 'endDate'=>$endDate, 'creator'=>$creator, 'fileName'=>$fileName, 'version'=>$version, 'content'=>$content);
  $addEditItem = updateDisplayItemVersionCheck($imgPk, $dataSubmit); // Blocking the race condition
  if (!$addEditItem) {
    echo('<div class=\'alert alert-danger\'>Error on updateDisplayItemVersionCheck(). Invalid form data error OR version expired.</div>');
    printForm($dataSubmit, 'editItem');
    exit;
  }
}
else {
  //require_once('upload.php');
  if ($_FILES['imgValue']['name'] != '') {
    $imgValue = $_FILES['imgValue']['name'];
    $upload = uploadFile('imgValue'); // save file into the server
    if ($upload == 0) {
      echo('<div class=\'alert alert-danger\'>Failed to upload the image file '.$imgValue.'<br>Make sure you upload the VALID image file.</div>');
      //var_dump($_FILES['imgValue']);
      exit();
    }
  }
  $addEditItem = addDisplayItem($textValue, $startDate, $endDate, $creator, $fileName, $imgValue, $content);
  if (!$addEditItem) { // failure to add the item into the database
    echo('<div class=\'alert alert-danger\'>Error on addDisplayItem() => '.json_encode(array($textValue, $startDate, $endDate, $creator, $fileName, $imgValue, $content)).'</div>');
    printForm();
    exit();
  }
}

$successcode = 0;

if ($submitMode === 'editItem') {
  $successcode = 2; // edit successful
}
else {
  $successcode = 1; // add successful
}

header('Location: index.php?cmd=displayItems&result='.$successcode);
?>
