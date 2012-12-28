<?php

function register_user()
{
	require_once('lib/recaptcha-php-1.11/recaptchalib.php');
	global $FACEBOOK_ID, $recaptcha_private_key;
	global $VGA_CONTENT;
	$errors = false;
	
	$resp = recaptcha_check_answer ($recaptcha_private_key,
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) {
		$msg = "{$VGA_CONTENT['captcha_err_txt']}";
		set_message("error", $msg);
		$errors = true;
	}
	
	$username = GetEscapedPostParam('username');
	$email = GetEscapedPostParam('email');
	$pass = GetEscapedPostParam('pass');
	$pass2 = GetEscapedPostParam('pass2');

	//This makes sure they did not leave any fields blank
	if (!$username || !$pass || !$pass2 || !$email) 
	{
		$msg = "{$VGA_CONTENT['req_flds_err_txt']}";
		set_message("error", $msg);
		$errors = true;
		return false;
	}
	
	if (hasTags($username) || hasTags($pass) || hasTags($pass2) || hasTags($email)) 
	{
		$msg = "Your form contains invalid characters.";
		set_message("error", $msg);
		$errors = true;
		unset($_POST);
		return false;
	}
	
	if ($pass != $_POST['pass']) 
	{
		$msg = "Passwords cannot begin or end with spaces.";
		set_message("error", $msg);
		$errors = true;
	}
	
	// Check field lengths
	/*
	define("MAX_LEN_EMAIL", 60);
	define("MAX_LEN_USERNAME", 60);
	define("MAX_LEN_PASSWORD", 70);
	define("MIN_LEN_PASSWORD", 6);
	*/
	if (!checkMaxStringLength($username, MAX_LEN_USERNAME))
	{
		$msg = "Your username should be less than ".MAX_LEN_USERNAME." characters.";
		set_message("error", $msg);
		$errors = true;
	}
	if (!checkMaxStringLength($email, MAX_LEN_EMAIL))
	{
		$msg = "Your email should be less than ".MAX_LEN_EMAIL." characters.";
		set_message("error", $msg);
		$errors = true;
	}
	if (!checkMaxStringLength($pass, MAX_LEN_PASSWORD))
	{
		$msg = "Your password should be less than ".MAX_LEN_PASSWORD." characters.";
		set_message("error", $msg);
		$errors = true;
	}
	if (!checkMinStringLength($pass, MIN_LEN_PASSWORD))
	{
		$msg = "Your password should be at least ".MIN_LEN_PASSWORD." characters.";
		set_message("error", $msg);
		$errors = true;
	}
	
	if (validEmail($username))
	{
		$msg = "{$VGA_CONTENT['useremail_err_txt']}";
		set_message("error", $msg);
		$errors = true;
	}

	if (!validEmail($email))
	{
		$msg = "{$VGA_CONTENT['email_err_txt']}";
		set_message("error", $msg);
		$errors = true;
	}
	
	if (isEmailRegistered($email))
	{
		$msg = "The email address you entered is already registered. If you have forgotten your password you can reset your password from the login page.";
		set_message("error", $msg);
		$errors = true;
	}
		
	//if the name exists it gives an error
	if (isUsernameRegistered($username)) 
	{
		$format = "{$VGA_CONTENT['user_unav_err_txt']}";
		$msg = sprintf($format, $username);
		set_message("error", $msg);
		$errors = true;
	}

	// this makes sure both passwords entered match
	if ($pass != $pass2) 
	{
		$msg = "{$VGA_CONTENT['pwds_err_txt']}";
		set_message("error", $msg);
		$errors = true;
		//return false;
	}
	
	if ($errors)
	{
		return false;
		exit;
	}

	// here we encrypt the password and add slashes if needed
	$username = GetMySQLEscapedString($username);
	$email = GetMySQLEscapedString($email);
	//$password = encryptPWD($password);
	$password = encryptUserPassword($pass);

	// now we insert it into the database
	$insert = "INSERT INTO `users` (`username`, `password`, `email`, `registered`, `lastlogin`) 
		VALUES ('$username', '$password', '$email', NOW(), NOW())";
	
	$add_member = mysql_query($insert);
	
	if (!$add_member)
	{
		handle_db_error($add_member, $insert);
		$msg = "{$VGA_CONTENT['sys_err_txt']}";
		set_message("error", $msg);
		return false;
	}
	
	// Log user in
	$userid = mysql_insert_id();
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'VGA';
	// log time
	setlogintime($userid); 
	
	// success
	return true;
}

function login_user_noredirect()
{
	global $FACEBOOK_ID;
	global $VGA_CONTENT;
	
	set_log(__FUNCTION__);
	
	// makes sure they filled it in
	if(empty($_POST['username']) || empty($_POST['pass'])) 
	{
		$msg = "{$VGA_CONTENT['required_field_txt']}";
		set_message("error", $msg);
		return false;
	}
	$usercheck = GetMySQLEscapedPostParam("username");
	// checks it against the database
	$sql = "SELECT * FROM `users` WHERE `username` = '$usercheck'";
	$check = mysql_query($sql);
	
	//set_log($sql);
	
	if (!$check)
	{
		handle_db_error($check);
		$msg = "{$VGA_CONTENT['goin_err_txt']}";
		set_message("error", $msg);
		return false;
	}

	//Gives error if user dosen't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) 
	{
		$msg = "{$VGA_CONTENT['user_exist_err_txt']}";
		set_message("error", $msg);
		return false;
	}
			
	$info = mysql_fetch_assoc( $check );
	$password = GetEscapedPostParam('pass');
	//$password = encryptPWD($password);
	//gives error if the password is wrong
	//if ($password != $info['password']) 
	if (!checkUserPassword($info['id'], $password, $info['password']))
	{
		$msg = "{$VGA_CONTENT['wrong_pwd_txt']}";
		set_message("error", $msg);
		return false;
	}
	else
	{
		// if login is ok then we start a user session
		$_SESSION[USER_LOGIN_ID] = $info['id'];
		$_SESSION[USER_LOGIN_MODE] = 'VGA';
		
		// Log time
		setlogintime($info['id']);

		// if Keep Logged In is checked then we add a cookie
		if (isset($_POST['remember']) && $_POST['remember'] == 'on')
		{
			setpersistantcookie($info['id']);
		}

		return true;
	}

}

function login_user()
{
	//set_log(__FUNCTION__.":: Logging in user");
	//set_log($_POST['username']);
	
	global $FACEBOOK_ID;
	global $VGA_CONTENT;
	
	// makes sure they filled it in
	if(empty($_POST['username']) || empty($_POST['pass'])) 
	{
		$msg = "{$VGA_CONTENT['required_field_txt']}";
		set_message("error", $msg);
		return false;
	}
	
	$username = GetMySQLEscapedPostParam('username');
	//set_log("Username = $username");
	// checks it against the database
	$sql = "SELECT * FROM `users` WHERE `username` = '$username'";
	$check = mysql_query($sql);
	
	//set_log($sql);
	
	if (!$check)
	{
		handle_db_error($check);
		$msg = "{$VGA_CONTENT['goin_err_txt']}";
		set_message("error", $msg);
		return false;
	}

	//Gives error if user dosen't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) 
	{
		$msg = "{$VGA_CONTENT['user_exist_err_txt']}";
		set_message("error", $msg);
		return false;
	}

	$info = mysql_fetch_assoc( $check );
	$password = GetEscapedPostParam('pass');
	//$password = encryptPWD($password);
	//gives error if the password is wrong
	//if ($password != $info['password']) 
	if (!checkUserPassword($info['id'], $password, $info['password']))
	{
		$msg = "{$VGA_CONTENT['wrong_pwd_txt']}";
		set_message("error", $msg);
		return false;
	}
	else
	{
		// if login is ok then we start a user session
		$_SESSION[USER_LOGIN_ID] = $info['id'];
		$_SESSION[USER_LOGIN_MODE] = 'VGA';
		
		// Log time
		setlogintime($info['id']);

		// if Keep Logged In is checked then we add a cookie
		if (isset($_POST['remember']) && $_POST['remember'] == 'on')
		{
			set_log('Setting persistant cookie...');
			setpersistantcookie($info['id']);
		}
		
		return true;
	}
}

function fb_register_user()
{
	global $FACEBOOK_ID;
	global $VGA_CONTENT;
	
	//This makes sure they did not leave any fields blank
	if (!$_POST['username']) 
	{
		$msg = "{$VGA_CONTENT['required_field_txt']}";
		set_message("error", $msg);
		return false;
	}

	$UsernameIsEmail=validEmail($_POST['username']);
	if ($UsernameIsEmail)
	{
		$msg = $VGA_CONTENT['not_email_err_txt'];
		set_message("error", $msg);
		return false;
	}

	if ($_POST['email'] ) 
	{
		$EmailIsValid = validEmail($_POST['email']);
		if (!$EmailIsValid)
		{
			$msg = $VGA_CONTENT['email_err_txt'];
			set_message("error", $msg);
			//set_log(__FUNCTION__." - Invalid email address: ".$_POST['email']);
			return false;
		}
	}

	// checks if the username is in use
	$usercheck = GetMySQLEscapedPostParam("username");
	$check = mysql_query("SELECT username FROM users WHERE username = '$usercheck'");
	
	if (!$check)
	{
		handle_db_error($check);
		$msg = "{$VGA_CONTENT['sys_err_txt']}";
		set_message("error", $msg);
		return false;
	}
	
	$check2 = mysql_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) 
	{
		$format = "{$VGA_CONTENT['user_unav_err_txt']}";
		$msg = sprintf($format, $_POST['username']);
		set_message("error", $msg);
		return false;
	}
	
	$username = GetMySQLEscapedPostParam('username');
	$email = GetMySQLEscapedPostParam('email');

	// now we insert it into the database
	$insert = "INSERT INTO `users` (`username`, `password`, `email`, `fb_userid`, `registered`, `lastlogin`) VALUES ('$username', '', '$email', '".$FACEBOOK_ID."', NOW(), NOW())";
	$add_member = mysql_query($insert);
	
	if (!$add_member)
	{
		handle_db_error($add_member, $insert);
		$msg = "{$VGA_CONTENT['sys_err_txt']}";
		set_message("error", $msg);
		return false;
	}
	
	// Log user in
	$userid = mysql_insert_id();
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'FB';
	// log time
	setlogintime($userid); 
	
	// Success
	return true;
}

function fb_connect_user()
{
	global $FACEBOOK_ID;
	global $VGA_CONTENT;
	
	// makes sure they filled it in
	if(empty($_POST['username']) || empty($_POST['pass'])) 
	{
		$msg = "{$VGA_CONTENT['required_field_txt']}";
		set_message("error", $msg);
		return false;
	}

	// checks it against the database
	$username = GetMySQLEscapedPostParam('username');
	$check = mysql_query("SELECT * FROM `users` WHERE `username` = '$username'");
	if (!$check)
	{
		handle_db_error($check);
		$msg = "{$VGA_CONTENT['sys_err_txt']}";
		set_message("error", $msg);
		return false;
	}

	//Gives error if user dosen't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) 
	{
		$msg = "{$VGA_CONTENT['no_user_err_txt']}";
		set_message("error", $msg);
		return false;
	}

	$info = mysql_fetch_assoc( $check );

	$password = GetEscapedPostParam('pass');
	//$password = encryptPWD($password);
	//gives error if the password is wrong
	//if ($password != $info['password']) 
	if (!checkUserPassword($info['id'], $password, $info['password']))
	{
		$msg = "{$VGA_CONTENT['wrong_pwd_txt']}";
		set_message("error", $msg);
		return false;
	}
	else
	{
		// now we insert it into the database
		$userid = $info['id'];
		$fb_userid = $FACEBOOK_ID;

		$sql = "UPDATE `users` SET `fb_userid` = '$fb_userid' WHERE `id` = $userid";

		$result = mysql_query($sql);

		if (!$result)
		{
			handle_db_error($result);
			$msg = "{$VGA_CONTENT['sys_err_txt']}";
			set_message("error", $msg);
			return false;
		}
		
		// Log user in if not already
		if (!IsAuthenticated())
		{
			$_SESSION[USER_LOGIN_ID] = $userid;
			$_SESSION[USER_LOGIN_MODE] = 'FB';
			// log time
			setlogintime($userid); 
		}
		// Success
		return true;
	}
}
?>