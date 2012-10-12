<?php

define("PWD_HASH_PREFIX", ""); #define the prefix for the password autentication
define("ID_SALT", ""); #define the salt for the password autentication

function encryptPWD3($password, $prefix=PWD_HASH_PREFIX)
{
	return hash('sha256', $prefix . $password);
}

function encryptPWD2($password, $prefix=PWD_HASH_PREFIX)
{
	return sha1($hash . $password);
}

function encryptPWD1($password, $prefix=PWD_HASH_PREFIX)
{
	return md5($hash . $password);
}

function encryptPWD($password)
{
	return md5($password);
}

function generateTOKEN()
{
	return md5(uniqid(rand(), TRUE));
}

function generateIDENTIFIER($userid, $salt = ID_SALT)
{
	return md5($salt . md5($userid . $salt));
}

// Returns true if ping comes from Facebook
function fb_verify_ping($secret)
{
	$sig = ''; 
	ksort($_POST); 
	foreach ($_POST as $key => $val) 
	{ 
		if (substr($key, 0, 7) == 'fb_sig_') 
		{ 
			$sig .= substr($key, 7) . '=' . $val; 
		} 
	} 
	$sig .= $secret; 
	$verify = md5($sig); 
	return $verify == $_POST['fb_sig'];
}

function getUniqueRoomCode()
{
	$code = md5(uniqid(rand(), true));
	return '_' . substr($code, 0, RANDOM_ROOM_CODE_LENGTH);
}
?>