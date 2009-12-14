<?php

function register_user()
{
	global $FACEBOOK_ID;
	
	//This makes sure they did not leave any fields blank
	if (!$_POST['username'] | !$_POST['pass'] | !$_POST['pass2'] ) 
	{
		set_message("error", "You did not fill in all the required fields");
		return false;
	}

	$UsernameIsEmail=validEmail($_POST['username']);
	if ($UsernameIsEmail)
	{
		$msg = "Your username will be public and as such it cannot be an email address (for security reasons)";
		set_message("error", $msg);
		return false;
	}

	if ($_POST['email'] ) 
	{
		$EmailIsValid=validEmail($_POST['email']);
		if (!$EmailIsValid)
		{
			set_message("error", 'The Email address you inserted is not a valid email address.');
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
		set_message("error", "System error");
		return false;
	}
	
	$check2 = mysql_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) 
	{
		$msg = 'Sorry, the username '.$_POST['username'].' is already in use.';
		set_message("error", $msg);
		return false;
	}

	// this makes sure both passwords entered match
	if ($_POST['pass'] != $_POST['pass2']) 
	{
		set_message("error", "Your passwords did not match");
		return false;
	}

	// here we encrypt the password and add slashes if needed
	$_POST['pass'] = md5($_POST['pass']);
	if (!get_magic_quotes_gpc()) 
	{
		$_POST['pass'] = addslashes($_POST['pass']);
		$_POST['username'] = addslashes($_POST['username']);
		$_POST['email'] = addslashes($_POST['email']);
	}

	// now we insert it into the database
	$insert = "INSERT INTO users (username, password, email) VALUES ('".$_POST['username']."', '".$_POST['pass']."', '".$_POST['email']."')";//'
	$add_member = mysql_query($insert);
	
	if (!$add_member)
	{
		handle_db_error($add_member);
		set_message("error", "System error");
		return false;
	}
	
	// success
	return true;
}

function login_user()
{
	global $FACEBOOK_ID;
	
	// makes sure they filled it in
	if(!$_POST['username'] | !$_POST['pass']) 
	{
		set_message("error", "You did not fill in a required field");
		return false;
	}
	// checks it against the database

	if (!get_magic_quotes_gpc()) 
	{
		$_POST['email'] = addslashes($_POST['email']);
	}

	$check = mysql_query("SELECT * FROM users WHERE username = '".$_POST['username']."'");
	
	if (!$check)
	{
		handle_db_error($check);
		set_message("error", "System error");
		return false;
	}

	//Gives error if user dosen't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) 
	{
		$msg = "That user does not exist in our database. <a href=register.php>Click Here to Register</a>";
		set_message("error", $msg);
		return false;
	}
	while($info = mysql_fetch_array( $check ))
	{
		$_POST['pass'] = stripslashes($_POST['pass']);
		$info['password'] = stripslashes($info['password']);
		$_POST['pass'] = md5($_POST['pass']);

		//gives error if the password is wrong
		if ($_POST['pass'] != $info['password']) 
		{
			set_message("error", "Incorrect password, please try again");
			return false;
		}
		else
		{
			// if login is ok then we start a user session
			$_SESSION[USER_LOGIN_ID] = $info['id'];
			$_SESSION[USER_LOGIN_MODE] = 'VGA';

			// if Keep Logged In is checked then we add a cookie
			if (isset($_POST['remember']) && $_POST['remember'] == 'on')
			{
				$_POST['username'] = stripslashes($_POST['username']);
				$expire = time() + COOKIE_LIFETIME;
				setcookie(COOKIE_USER, $_POST['username'], $expire);
				setcookie(COOKIE_PASSWORD, $_POST['pass'], $expire);
			}

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
		}
	}
}

function fb_register_user()
{
	global $FACEBOOK_ID;
	
	//This makes sure they did not leave any fields blank
	if (!$_POST['username']) 
	{
		set_message("error", "You did not fill in a required field");
		return false;
	}

	$UsernameIsEmail=validEmail($_POST['username']);
	if ($UsernameIsEmail)
	{
		$msg = 'Your username will be public and as such it cannot be an email address (for security reasons)';
		set_message("error", $msg);
		return false;
	}

	if ($_POST['email'] ) 
	{
		$EmailIsValid=validEmail($_POST['email']);
		if (!$EmailIsValid)
		{
			set_message("error", 'The Email address you inserted is not a valid email address.');
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
		set_message("error", "System error");
		return false;
	}
	
	$check2 = mysql_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) 
	{
		$msg = 'Sorry, the username '.$_POST['username'].' is already in use.';
		set_message("error", $msg);
		return false;
	}

	if (!get_magic_quotes_gpc()) 
	{
		$_POST['username'] = addslashes($_POST['username']);
		$_POST['email'] = addslashes($_POST['email']);
	}

	// now we insert it into the database
	$insert = "INSERT INTO users (username, password, email, fb_userid) VALUES ('".$_POST['username']."', '', '".$_POST['email']."', '".$FACEBOOK_ID."')";
	$add_member = mysql_query($insert);
	
	if (!$add_member)
	{
		handle_db_error($add_member);
		set_message("error", "System error");
		return false;
	}
	
	// Success
	return true;
}

function fb_connect_user()
{
	global $FACEBOOK_ID;
	
	// makes sure they filled it in
	if(empty($_POST['username']) || empty($_POST['pass'])) 
	{
		set_message("error", "You did not fill in a required field");
		return false;
	}

	// checks it against the database
	$username = $_POST['username'];
	$check = mysql_query("SELECT * FROM users WHERE username = '$username'");
	if (!$check)
	{
		handle_db_error($check);
		set_message("error", "System error");
		return false;
	}

	//Gives error if user dosen't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) 
	{
		$msg = "Sorry, that user does not exist in our database.";
		set_message("error", $msg);
		return false;
	}

	$info = mysql_fetch_assoc( $check );

	$_POST['pass'] = stripslashes($_POST['pass']);
	$info['password'] = stripslashes($info['password']);
	$_POST['pass'] = md5($_POST['pass']);

	//gives error if the password is wrong
	if ($_POST['pass'] != $info['password']) 
	{
		set_message("error", "Incorrect password, please try again");
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
			set_message("error", "System error");
			return false;
		}
		
		// Success
		return true;
	}
}
?>