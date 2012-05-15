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

	//This makes sure they did not leave any fields blank
	if (!$_POST['username'] | !$_POST['pass'] | !$_POST['pass2'] | !$_POST['email']) 
	{
		$msg = "{$VGA_CONTENT['req_flds_err_txt']}";
		set_message("error", $msg);
		$errors = true;
		return false;
	}

	$UsernameIsEmail=validEmail($_POST['username']);
	if ($UsernameIsEmail)
	{
		$msg = "{$VGA_CONTENT['useremail_err_txt']}";
		set_message("error", $msg);
		$errors = true;
		//return false;
	}

	if ($_POST['email'] ) 
	{
		$EmailIsValid=validEmail($_POST['email']);
		if (!$EmailIsValid)
		{
			$msg = "{$VGA_CONTENT['email_err_txt']}";
			set_message("error", $msg);
			$errors = true;
			//return false;
		}
		
		if (isEmailRegistered($_POST['email']))
		{
			$msg = "The email address you entered is already registered. You can reset your password from the login page.";
			set_message("error", $msg);
			$errors = true;
		}
	}

	// checks if the username is in use
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['username'] = addslashes($_POST['username']);
	}
	$usercheck = $_POST['username'];
	$check = mysql_query("SELECT username FROM users WHERE username = '$usercheck'");
	
	if (!$check)
	{
		handle_db_error($check);
		$msg = "{$VGA_CONTENT['sys_err_txt']}";
		set_message("error", $msg);
		$errors = true;
		//return false;
	}
	
	$check2 = mysql_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) 
	{
		$format = "{$VGA_CONTENT['user_unav_err_txt']}";
		$msg = sprintf($format, $_POST['username']);
		set_message("error", $msg);
		$errors = true;
		//return false;
	}

	// this makes sure both passwords entered match
	if ($_POST['pass'] != $_POST['pass2']) 
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
	$_POST['pass'] = encryptPWD($_POST['pass']);
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['pass'] = addslashes($_POST['pass']);
		$_POST['username'] = addslashes($_POST['username']);
		$_POST['email'] = addslashes($_POST['email']);
	}

	// now we insert it into the database
	$insert = "INSERT INTO users (username, password, email, registered, lastlogin) VALUES ('".$_POST['username']."', '".$_POST['pass']."', '".$_POST['email']."', NOW(), NOW())";
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
	
	// makes sure they filled it in
	if(empty($_POST['username']) || empty($_POST['pass'])) 
	{
		$msg = "{$VGA_CONTENT['required_field_txt']}";
		set_message("error", $msg);
		return false;
	}
	
	// checks it against the database
	$sql = "SELECT * FROM users WHERE username = '".$_POST['username']."'";
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
	while($info = mysql_fetch_array( $check ))
	{
		$_POST['pass'] = stripslashes($_POST['pass']);
		$info['password'] = stripslashes($info['password']);
		$_POST['pass'] = encryptPWD($_POST['pass']);

		//gives error if the password is wrong
		if ($_POST['pass'] != $info['password']) 
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
}

function login_user()
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
	$sql = "SELECT * FROM users WHERE username = '".$_POST['username']."'";
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
	while($info = mysql_fetch_array( $check ))
	{
		$_POST['pass'] = stripslashes($_POST['pass']);
		$info['password'] = stripslashes($info['password']);
		$_POST['pass'] = encryptPWD($_POST['pass']);

		//gives error if the password is wrong
		if ($_POST['pass'] != $info['password']) 
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
				//***
				set_log('Setting persistant cookie...');
				setpersistantcookie($info['id']);
				//***
				//set_log('Setting old style cookies');
				//$_POST['username'] = stripslashes($_POST['username']);
				//$expire = time() + COOKIE_LIFETIME;
				//setcookie(COOKIE_USER, $_POST['username'], $expire);
				//setcookie(COOKIE_PASSWORD, $_POST['pass'], $expire);
			}
			
			return true;
			/*
			//then redirect them to the members area
			if (isset($_SESSION['request']) && !empty($_SESSION['request']))
			{
				// Now send the user to his desired page
				$request = $_SESSION['request'];
				unset($_SESSION['request']);
				header("Location: " . $request);
			}
			else {
				header("Location: viewquestions.php");
			}
			*/
		}
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
		$EmailIsValid=validEmail($_POST['email']);
		if (!$EmailIsValid)
		{
			$msg = $VGA_CONTENT['email_err_txt'];
			set_message("error", $msg);
			//set_log(__FUNCTION__." - Invalid email address: ".$_POST['email']);
			return false;
		}
	}

	// checks if the username is in use
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['username'] = addslashes($_POST['username']);
	}
	$usercheck = $_POST['username'];
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

	if (!get_magic_quotes_gpc()) 
	{
		$_POST['username'] = addslashes($_POST['username']);
		$_POST['email'] = addslashes($_POST['email']);
	}

	// now we insert it into the database
	$insert = "INSERT INTO users (username, password, email, fb_userid, registered, lastlogin) VALUES ('".$_POST['username']."', '', '".$_POST['email']."', '".$FACEBOOK_ID."', NOW(), NOW())";
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
	$username = $_POST['username'];
	$check = mysql_query("SELECT * FROM users WHERE username = '$username'");
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

	$_POST['pass'] = stripslashes($_POST['pass']);
	$info['password'] = stripslashes($info['password']);
	$_POST['pass'] = encryptPWD($_POST['pass']);

	//gives error if the password is wrong
	if ($_POST['pass'] != $info['password']) 
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

		$sql = "UPDATE users SET fb_userid = '$fb_userid' WHERE id = $userid";

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