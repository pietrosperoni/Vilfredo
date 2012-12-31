<?php
if (!function_exists('checkUserPassword')) 
{
	function checkUserPassword($userid, $password, $dbhash)
	{
		// Check against hash
		if (encryptPWD($password) == $dbhash)
		{
			return true;
		}
		else
		{
			return false;
		}
	};
}

if (!function_exists('encryptUserPassword')) 
{
	function encryptUserPassword($password)
	{
		return encryptPWD($password);
	};
}

function encryptPWD3($password, $prefix=PWD_HASH_PREFIX)
{
	return hash('sha256', $prefix . $password);
}

function createEmailVerificationKey($username, $email)
{
	$key = $username . $email  . date('mY');
	return md5($key);
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

function gen_uuid($len=10)
{
    $hex = md5(ID_SALT . uniqid("", true));

    $pack = pack('H*', $hex);

    $uid = base64_encode($pack);        // max 22 chars

    $uid = ereg_replace("[^A-Za-z0-9]", "", $uid);    // mixed case

    while (strlen($uid)<$len)
        $uid = $uid . gen_uuid(22);     // append until length achieved

    return substr($uid, 0, $len);
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