<?php
function check_email()
{
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	
	if (is_null($email) || !$email)
	{
		set_message("error", "Please enter your registered email address");
		return false;
	}
	
	// get user details or false if not found
	 $user = getUserFromEmail($email);
	 if (!$user)
	 {
		set_message("error", "Sorry, that email address is not registered");
		return false;
	 }

	 // generate reset code
	 $token = set_recover_pwd_token($user['id']);
	 if (!token)
	 {
		set_message("error", "Sorry, there was a system problem. Please wait while we sort it out. The email should arrive shortly.");
		return false;
	 }

	 // email user reset code
	 $expires = time() + PWD_RESET_LIFETIME;
	 $send_email = send_recover_email($user, $token, $expires);
	 if (!send_email)
	 {
		set_message("error", "Sorry, there was a system problem. Please wait while we sort it out. The email should arrive shortly.");
		return false;
	 }

	 return true;
}

function validate_recover_code_link()
{			
	$invalid_token_msg = "Sorry, your token appears to be invalid. Please contact support for help.";
	$invalide_link_msg = "Sorry, there appeared to be a problem with validating your password reset link. Please enter the code manually below or contact support. Thanks.";
	
	if (!isset($_GET['t']) || !isset($_GET['u']) 
		|| !ctype_alnum($_GET['t']) || !ctype_alnum($_GET['u']))
	{		
		set_message("error", $invalide_link_msg);
		return false;
	}
	
	$token = $_GET['t'];
	$userid = (int)$_GET['u'];

	$sql = "SELECT userid, token, timeout		
		    FROM pwd_reset
		    WHERE  userid = $userid 
		    AND token = '$token'";

	$result = mysql_query($sql);

	if (!$result)
	{
		handle_db_error($result, $sql);
		set_log('Retrieving password reset token failed!');
		set_message("error", $invalide_link_msg);
		return false;
	}

	if (mysql_num_rows($result) > 0)
	{
		// fetch user details
		$resetdata = mysql_fetch_assoc($result);
		
		// Check if token expired
		if (time() > (int)$resetdata['timeout'])
		{
			// Delete expired recover entry
			$userid = (int)$resetdata['userid'];
			delete_recover_pwd_token($userid, $token);
			return 2;
		}
		else
		{
			$user = getuserdetails($resetdata['userid']);
			$_SESSION['recoveruser'] = $user['username'];
			$_SESSION['recoverid'] = $user['id'];
			$_SESSION['recovertoken'] = $token;
			return true;
		}
	}
	else 
	{
		set_message("error", $invalid_token_msg);
		return false;
	}		
}

function validate_recover_code()
{
	$invalid_token_msg = "Sorry, your reset code appears to be invalid. Please contact support for help.";
	$invalide_link_msg = "Sorry, there appeared to be a problem with validating your reset code. Please enter the code manually below or contact support. Thanks.";

	$token = filter_input(	INPUT_POST, 'reset-code', FILTER_SANITIZE_STRING);
	
	if (is_null($token))
	{
		set_message("error", "Please enter the token you were emailed");
		return false;
	}
	elseif (!$token)
	{
		set_message("error", $invalid_token_msg);
		return false;
	}
	
	$sql = "SELECT userid, token, timeout		
		    FROM pwd_reset
		    WHERE token = '$token'";

	$result = mysql_query($sql);

	if (!$result)
	{
		handle_db_error($result, $sql);
		set_log('Retrieving password reset token failed!');
		set_message("error", $invalide_link_msg);
		return false;
	}

	if (mysql_num_rows($result) > 0)
	{
		// fetch user details
		$resetdata = mysql_fetch_assoc($result);
		
		// Check if token expired
		if (time() > (int)$resetdata['timeout'])
		{
			// Delete expired recover entry
			$userid = (int)$resetdata['userid'];
			delete_recover_pwd_token($userid, $token);
			return 2;
		}
		else
		{
			$user = getuserdetails($resetdata['userid']);
			$_SESSION['recoveruser'] = $user['username'];
			$_SESSION['recoverid'] = $user['id'];
			$_SESSION['recovertoken'] = $token;
			return true;
		}
	}
	else 
	{
		set_message("error", $invalid_token_msg);
		return false;
	}
}

function reset_user_password()
{
	if (!isset($_SESSION['recoverid']) || !isset($_SESSION['recovertoken']))
	{
		set_log(__FUNCTION__." Session variables userid and token not set");
		$msg = "There was a system problem. Your password could not be reset at this time. Please try again later or contact support.";
		set_message("error", $msg);
		return false;
	}
	
	if (empty($_POST['pass']) || empty($_POST['pass2'])) 
	{
		$msg = "You did not complete the required fields";
		set_message("error", $msg);
		return false;
	}
	
	if ($_POST['pass'] != $_POST['pass2']) 
	{
		$msg = "Your passwords must match.";
		set_message("error", $msg);
		return false;
	}
	
	$userid = (int)$_SESSION['recoverid'];
	$token = $_SESSION['recovertoken'];
	
	$password = $_POST['pass'];
	
	$password = encryptPWD($password);
	if (!get_magic_quotes_gpc()) 
	{
		$password = addslashes($password);
	}
	
	$sql = "UPDATE users 
		SET password = '$password'
		WHERE id = $userid";
	
	$reset_pwd = mysql_query($sql);

	if (!$reset_pwd)
	{
		handle_db_error($reset_pwd, $sql);
		$msg = "Sorry there was a system error. Please try again.";
		set_message("error", $msg);
		return false;
	}
	
	// Delete table entry
	delete_recover_pwd_token($userid, $token);
	
	unset($_SESSION['recoverid']);
	unset($_SESSION['recovertoken']);
	unset($_SESSION['recoveruser']);
	
	// log user in
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'VGA';
	// Log time
	setlogintime($userid);
	
	$user = getuserdetails($userid);
	
	return $user;
}

function get_recover_pwd_token($userid, $token)
{
	$sql = "SELECT userid, token, timeout		
		    FROM pwd_reset
		    WHERE  userid = $userid 
		    AND token = '$token'";

	$result = mysql_query($sql);

	if (!$result)
	{
		handle_db_error($result, $sql);
		return false;
	}

	if (mysql_num_rows($result) > 0)
	{
		$resetdata = mysql_fetch_assoc($result);
		return $resetdata;
	}
	else 
	{
		return false;
	}
}

function set_recover_pwd_token($userid)
{	
	$token = gen_uuid();
	$expire = time() + PWD_RESET_LIFETIME;

	$sql = "INSERT INTO pwd_reset (userid, token, timeout)
		VALUES ($userid, '$token', $expire)";

	$addpwdresetrequest = mysql_query($sql);

	if ($addpwdresetrequest)
	{
		return $token;
	}
	else
	{
		handle_db_error($addpwdresetrequest, $sql);
		return false;
	}
}

function delete_recover_pwd_token($userid, $token)
{
	$sql = "DELETE FROM  pwd_reset
		    WHERE  userid = $userid 
		    AND token = '$token'";
		
	if (!mysql_query($sql))
	{
		db_error(__FUNCTION__ . " SQL: " . $sql);
		return false;
	}
	else
	{
		return true;
	}
}

function send_recover_email($user, $token, $expires)
{
	$subject = "You requested a new Vilfredo password";
	$to = $user['username'] . " <" . $user['email'] . ">";
	$domain = SITE_DOMAIN;
	$expiredate = date(DateTime::RFC850 , $expires);
	
	$message = <<<_HTML_
Hi {$user['username']},

You recently asked to reset your Vilfredo password. To complete your request, please follow this link:

$domain/recover.php?t=$token&u={$user['id']}

Alternately, you may go to $domain/recover.php and enter the following password reset code:

$token

This code will expire on $expiredate, at which time you will need to make another request.

Thanks,
Vilfredo
_HTML_;

	// wrap message to required 70 char width
	$message = wordwrap($message, 70, "\n", false);
	
	file_put_contents($subject.'.txt', $message);
	return true;
	// return true or false
	//return @mail($to, $subject, $message);
}
?>