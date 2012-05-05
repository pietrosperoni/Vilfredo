<?php
require_once 'config.inc.php';
	
if (empty($_POST['username']) || empty($_POST['pass'] ))
{
	echo $VGA_CONTENT['enter_user_pwd_txt'];
	exit();
}

if (empty($_POST['fbuserid'] ))
{
	echo $VGA_CONTENT['fbid_prob_txt'];
	exit();
}

$username = GetEscapedPostParam('username');
$password = GetEscapedPostParam('pass');
$fb_userid = GetEscapedPostParam('fbuserid');

// checks it against the database
$sql = "SELECT * FROM users WHERE username = '$username'";
$check = mysql_query($sql);

if (!$check)
{
	handle_db_error($check);
	echo "0";
	exit();
}

//Gives error if user dosen't exist
if (mysql_num_rows($check) == 0) 
{
	//echo "User $username not registered";
	$format = $VGA_CONTENT['user_not_reg_txt'];
	$str = sprintf($format, $username);
	echo $str;
	exit();
}

$info = mysql_fetch_assoc($check);

$userid = $info['id'];

$password = encryptPWD($password);

//gives error if the password is wrong
if ($password != $info['password']) 
{
	//echo "Incorrect password for $username";
	$format = $VGA_CONTENT['wrong_user_pwd_txt'];
	$str = sprintf($format, $username);
	echo $str;
	exit();
}
else
{
	
	// Add FB User ID to user record	
	$sql = "UPDATE users SET fb_userid = '$fb_userid' WHERE id = $userid";

	$result = mysql_query($sql);

	if (!$result)
	{
		handle_db_error($result);
		echo "0";
		exit();
	}
	
	// log user in
	$_SESSION[USER_LOGIN_ID] = $info['id'];
	$_SESSION[USER_LOGIN_MODE] = 'FB';
	// log time
	setlogintime($info['id']);
	echo '1';
	exit();
}

?>