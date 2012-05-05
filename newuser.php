<?php
require_once 'config.inc.php';
require_once('lib/recaptcha-php-1.11/recaptchalib.php');

session_start();
if (isset($_SESSION["locale"]) and ($_SESSION["locale"] == 'en' or $_SESSION["locale"] == 'it' ))
{
	$locale = $_SESSION["locale"];
}
else
{
	$locale = fetch_preferred_language_from_client();
}
include getLanguage($locale);

function registerfbuser()
{
	global $VGA_CONTENT;
	
	if (!empty($_POST['usernameok']))
	{
		$testname = GetEscapedPostParam('usernameok');
		$check = mysql_query("SELECT username FROM users WHERE username = '$testname'");
		if (!$check)
		{
			handle_db_error($check);
			echo '' . $VGA_CONTENT['req_timeout_txt'] . '';
			exit();
		}

		$check2 = mysql_num_rows($check);
		//if the name exists it gives an error
		if ($check2 != 0) 
		{
			echo '' . $VGA_CONTENT['already_reg_txt'] . '';
			exit();
		}
	}
	
	//This makes sure they did not leave any fields blank
	$fields = "";
	if (empty($_POST['usernameok']))
	{
		$fields .= $VGA_CONTENT['username_label'];
	}
	
	if (!empty($fields))
	{
		echo 'Required fields:' . $fields;
		exit();
	}
	
	if (validEmail($_POST['usernameok']))
	{
		echo $VGA_CONTENT['not_email_txt'];
		exit();
	}
	
	if (!empty($_POST['email'])) 
	{
		if (!validEmail($_POST['email']))
		{
			echo "{$VGA_CONTENT['email_invalid_txt']}";
			exit();
		}
	}
	
	$newuser = GetEscapedPostParam('usernameok');
	$fbuserid = GetEscapedPostParam('fbuserid');
	$email = GetEscapedPostParam('email');
	
	$insert = "INSERT INTO users (username, password, email, fb_userid, registered, lastlogin) 
	VALUES ('$newuser', '', '$email', '$fbuserid', NOW(), NOW())";
	
	$add_member = mysql_query($insert);
	
	if (!$add_member)
	{
		handle_db_error($add_member);
		echo "0";
		exit();
	}
	
	// Log user in
	$userid = mysql_insert_id();
	// start a user session
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'FB';
	
	// success
	echo "1";
	exit();
}

function registeruser()
{
	global $VGA_CONTENT;
	
	set_log(implode(", ", $_POST));
	
	if (!empty($_POST['usernameok']))
	{
		$testname = GetEscapedPostParam('usernameok');
		$check = mysql_query("SELECT username FROM users WHERE username = '$testname'");
		if (!$check)
		{
			set_log($VGA_CONTENT['req_timeout_txt']);
			handle_db_error($check);
			echo $VGA_CONTENT['req_timeout_txt'];
			exit();
		}

		$check2 = mysql_num_rows($check);
		//if the name exists it gives an error
		if ($check2 != 0) 
		{
			//set_log($VGA_CONTENT['already_reg_txt']);
			echo $VGA_CONTENT['already_reg_txt'];
			exit();
		}
	}
	
	if (!empty($_POST['email']))
	{
		if (isEmailRegistered($_POST['email']))
		{
			echo "The email address you entered is already registered. You can reset your password from the login page.";
			exit();
		}
	}
	
	global $recaptcha_private_key;
	// Validate recaptcha
	$resp = recaptcha_check_answer ($recaptcha_private_key,
					$_SERVER["REMOTE_ADDR"],
					$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
	
	if (!$resp->is_valid) 
	{
		set_log($VGA_CONTENT['captcha_error_txt']);
		if (!empty($VGA_CONTENT['captcha_error_txt'])) 
		{
			echo $VGA_CONTENT['captcha_error_txt'];
		}
		else
		{
			echo "Recapcha returned un unspecified error.";
		}
		exit();
	}
	
	
	//This makes sure they did not leave any fields blank
	$fields = "";
	if (empty($_POST['usernameok']))
	{
		$fields .= $VGA_CONTENT['username_label'];
	}
	
	if (empty($_POST['email']))
	{
		$fields .= $VGA_CONTENT['email_txt'];
	}
	
	if (empty($_POST['pass']) || empty($_POST['pass2'] ))
	{
		$fields .= " Password ";
	}
	
	if (!empty($fields))
	{
		set_log($fields);
		echo $VGA_CONTENT['req_flds_txt'] . ' ' . $fields;
		exit();
	}
	
	// this makes sure both passwords entered match
	if ($_POST['pass'] != $_POST['pass2']) 
	{
		set_log("Passwords don't match");
		echo $VGA_CONTENT['pwds_err_txt'] ;
		exit();
	}
	
	if (validEmail($_POST['usernameok']))
	{
		set_log($VGA_CONTENT['not_email_txt']);
		echo $VGA_CONTENT['not_email_txt'];
		exit();
	}
	
	if (!empty($_POST['email'])) 
	{
		if (!validEmail($_POST['email']))
		{
			set_log($VGA_CONTENT['email_invalid_txt']);
			echo $VGA_CONTENT['email_invalid_txt'];
			exit();
		}
	}
	
	// encrypt the password and add slashes if needed
	$_POST['pass'] = encryptPWD($_POST['pass']);
	
	$newuser = GetEscapedPostParam('usernameok');
	$password = GetEscapedPostParam('pass');
	$email = GetEscapedPostParam('email');
	
	$insert = "INSERT INTO users (username, password, email, registered, lastlogin) 
	VALUES ('$newuser', '$password', '$email', NOW(), NOW())";
	
	set_log($insert);
	
	$add_member = mysql_query($insert);
	$userid = mysql_insert_id();
	
	if (!$add_member)
	{
		set_log('DB INSERT Error');
		handle_db_error($add_member);
		echo "0";
		exit();
	}
	
	// Log user in
	$userid = mysql_insert_id();
	// start a user session
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'VGA';
	
	// success
	set_log('success');
	echo "1";
	exit();
}

function checkusername()
{
	global $VGA_CONTENT;
	
	if (empty($_POST['username']))
	{
		echo '' . $VGA_CONTENT['not_enter_un_txt'] . '';
		exit();
	}
	
	$testname = GetEscapedPostParam('username');
	$check = mysql_query("SELECT username FROM users WHERE username = '$testname'");
	if (!$check)
	{
		handle_db_error($check);
		echo '' . $VGA_CONTENT['req_timeout_txt'] . '.';
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
		if (isset($_POST['fbuserid'])) 
		{
			return registerfbuser();
		}
		else 
		{
			return registeruser();
		}
		break;
	default:
		return "Action not recognised.";
}
?>