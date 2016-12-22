<?php
/*
   Scott Campbell and class
   provide REST interface to  adds and messages

 */

$index_check=1;
require_once("../models/message.php");
$message = new Message();


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type,X-SESSION');

function sendError($error,$msg) {
	header("HTTP/1.1 200 OK");
	$r=array('status'=>$error,'errormsg'=>$msg);
	print json_encode($r);
	exit();
}

function sendStatus($status) {
	header("HTTP/1.1 200 OK");
	$r=array('status'=>$status);
	print json_encode($r);
	exit();
}

function getData($data,$name) {
	if (isset($data[$name]))
		return $data[$name];
	else
		false;
}

function e($msg) {
	$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");

	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

# creates a cipher text compatible with AES (Rijndael block size = 128)
# to keep the text confidential 
# only suitable for encoded input that never ends with value 00h
# (because of default zero padding)
	$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
			$msg, MCRYPT_MODE_CBC, $iv);

# prepend the IV for it to be available for decryption
	$ciphertext = $iv . $ciphertext;

# encode the resulting cipher text so it can be represented by a string
	return base64_encode($ciphertext);
}


function d($ciphertext_base64) {
	try {
		$ciphertext_dec = base64_decode($ciphertext_base64);
		$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");


# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv_dec = substr($ciphertext_dec, 0, $iv_size);
		if (strlen($iv_dec)!=$iv_size)
			return FALSE;

# retrieves the cipher text (everything except the $iv_size in the front)
		$ciphertext_dec = substr($ciphertext_dec, $iv_size);

# may remove 00h valued characters from end of plain text
		$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
				$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

		return $plaintext_dec;
	} catch (Exception $e) {
		return FALSE;
	}
}

header("content-type: application/json");

//parse path info
$pathInfo = array();
if (isset($_SERVER['PATH_INFO'])) {
	$pathInfo = explode("/",$_SERVER['PATH_INFO']);
	//remove first element which is always blank
	array_shift($pathInfo);
	//if first element is empty then remove it
	if ($pathInfo[0] === "") 
	{
		array_shift($pathInfo);
	}
}


//read json body
$content = file_get_contents('php://input');
$content = stripslashes($content);
error_log("content " . print_r($content,true));
$data = json_decode($content, true);
error_log("data " . print_r($data,true));



//handle GET
if ($_SERVER['REQUEST_METHOD']=="GET") {
	if (sizeof($pathInfo) != 1)
	{
		sendError("FORMATFAIL","NO AUTH");
	}

	$auth = $pathInfo[0];
	if (!isset($_SERVER['HTTP_X_SESSION']))
		sendError("FORMATFAIL","NO X-SESSION HEADER");

	$session=d($_SERVER['HTTP_X_SESSION']);
	$session=json_decode($session,true);
	$auth_check = $session['auth'];
	if ($auth_check!=$auth)
		sendError("AUTHERROR","");

	if (!isset($session['num']) || !is_numeric($session['num']))
		sendError("FAIL","INVALID SESSION");
	$num=$session['num'];

	error_log(print_r($session,true));

	$messageText = $message->getMessage($num+1);
	error_log("message = " . print_r($messageText,true));
	if ($messageText==null)
		sendError("AUTHEXPIRED","Need to re-authenticate");

	$session_new=array('num'=>($num+1),'auth'=>$session['auth']);
	error_log(print_r($session_new,true));
	$session_new = json_encode($session_new,JSON_HEX_QUOT|JSON_HEX_APOS);
	$result = array('status'=>'OK','message'=>$messageText['message'],'session'=>e($session_new));
	error_log("send back " . print_r($result,true));
	error_log("send back " . json_encode($result,true));
	$q=json_encode($result);
	if ($q===false)
		sendError("FAIL","UNABLE TO ENCODE RESPONSE");
	print $q;
	exit;

} else if ($_SERVER['REQUEST_METHOD']=="POST") {
	require_once("../cse383-f16-screen/models/userModel.php");
	error_log("auth " . print_r($data,true));
	if (!isset($data['uid']) || !isset($data['password']))
		sendError("FORMATFAIL","");
	if (!validate($data['uid'],$data['password']))
		sendError("AUTHFAIL","");
	$auth=rand(1000000,2000000);
	$session=array('num'=>0,'auth'=>$auth);
	$session = json_encode($session);

	$response = array('status'=>'OK','auth'=>$auth,'session'=>e($session));
	print json_encode($response);
	exit;
} else if ($_SERVER['REQUEST_METHOD']=="OPTIONS") {
	error_log("options request");

} else {
	sendError("Error - invalid method");
}
?>
