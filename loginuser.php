<?php
require_once 'config.inc.php';
	
if (empty($_POST['username']) || empty($_POST['pass'] ))
{
	echo "0";
	exit();
}

$username = GetEscapedPostParam('username');
$password = GetEscapedPostParam('pass');

// checks it against the database
$sql = "SELECT * FROM users WHERE username = '$username'";
$check = mysql_query($sql);

if (!$check)
{
	handle_db_error($check, $sql);
	echo "0";
	exit();
}

//Gives error if user dosen't exist
if (mysql_num_rows($check) == 0) 
{
	echo "User $username not registered";
	exit();
}

$info = mysql_fetch_array( $check );
$password = encryptPWD($password);

//gives error if the password is wrong
if ($password != $info['password']) 
{
	echo "Incorrect password for $username";
	exit();
}
else
{
	// log user in
	$_SESSION[USER_LOGIN_ID] = $info['id'];
	$_SESSION[USER_LOGIN_MODE] = 'VGA';
	
	// log time
	setlogintime($info['id']);
	
	// Set persistant cookie if requested
	if (isset($_POST['remember']) && $_POST['remember'] == 'on')
	{
		setpersistantcookie($info['id']);			
	}
	
	echo '1';
	exit();
}

?>