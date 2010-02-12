<?php
//require_once 'vgta_init.php';
//require_once 'lib/php_lib.php';
//require_once 'vga_functions.php';
require_once 'config.inc.php';

function registeruser()
{
	if (!empty($_POST['usernameok']))
	{
		$testname = GetEscapedPostParam('usernameok');
		$check = mysql_query("SELECT username FROM users WHERE username = '$testname'");
		if (!$check)
		{
			handle_db_error($check);
			echo 'Sorry, request timed out.';
			exit();
		}

		$check2 = mysql_num_rows($check);
		//if the name exists it gives an error
		if ($check2 != 0) 
		{
			echo 'Already registered!';
			exit();
		}
	}
	
	//This makes sure they did not leave any fields blank
	$fields = "";
	if (empty($_POST['usernameok']))
	{
		$fields .= " Username ";
	}
	
	if (empty($_POST['pass']) || empty($_POST['pass2'] ))
	{
		$fields .= " Password ";
	}
	
	if (!empty($fields))
	{
		echo 'Required fields:' . $fields;
		exit();
	}
	
	// this makes sure both passwords entered match
	if ($_POST['pass'] != $_POST['pass2']) 
	{
		echo "Passwords don't match";
		exit();
	}
	
	if (validEmail($_POST['usernameok']))
	{
		echo "Username must not be your email";
		exit();
	}
	
	if (!empty($_POST['email'])) 
	{
		if (!validEmail($_POST['email']))
		{
			echo "Email not valid";
			exit();
		}
	}
	
	// encrypt the password and add slashes if needed
	$_POST['pass'] = md5($_POST['pass']);
	
	$newuser = GetEscapedPostParam('usernameok');
	$password = GetEscapedPostParam('pass');
	$email = GetEscapedPostParam('email');
	
	$insert = "INSERT INTO users (username, password, email) 
	VALUES ('$newuser', '$password', '$email')";
	
	$add_member = mysql_query($insert);
	$userid = mysql_insert_id();
	
	if (!$add_member)
	{
		handle_db_error($add_member);
		set_message("error", "System error");
		echo "0";
		exit();
	}
	
	set_log("User $newuser registered");
	
	// Log user in
	$userid = mysql_insert_id();
	// start a user session
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'VGA';
	
	// success
	echo "1";
	exit();
}

function checkusername()
{
	if (empty($_POST['username']))
	{
		echo 'You did not enter a username.';
		exit();
	}
	
	$testname = GetEscapedPostParam('username');
	$check = mysql_query("SELECT username FROM users WHERE username = '$testname'");
	if (!$check)
	{
		handle_db_error($check);
		echo 'Sorry, request timed out.';
		exit();
	}
	
	$check2 = mysql_num_rows($check);
	//if the name exists it gives an error
	if ($check2 != 0) 
	{
		//return 'Username not available.';
		echo 0;
		exit();
	}
	
	//return 'Username available.';
	echo 1;
	exit();
}

$action = $_POST['action'];

switch ($action) {
	case 'checkname':
		// Check if username already used
		return checkusername();
		break;
	case 'register':
		// Register new user
		return registeruser();
		break;
	default:
		return "Action not recognised.";
}
?>