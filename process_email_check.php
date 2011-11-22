<?php
// DONE
function confirm_email($userid, $username, $email)
{
	 // generate confirm code
	 $token = set_confirm_email_token($userid);
	 if (!token)
	 {
		//set_log(__FILE__. "::" . __FUNCTION__."::");
		return false;
	 }

	 // email user reset code
	 $expires = time() + CHECK_EMAIL_LIFETIME;
	 $send_email = send_confirmation_email($username, $email, $token);
	 
	 if (!send_email)
	 {
		set_message("error", "Sorry, there was a system problem. Please wait while we sort it out. The email should arrive shortly.");
		return false;
	 }

	 return true;
}

function validate_email_confirm_code_link()
{			
	$invalid_token_msg = "Sorry, your token appears to be invalid. Please contact support for help.";
	$invalide_link_msg = "Sorry, there appeared to be a problem with validating your password reset link. Please enter the code manually below or contact support. Thanks.";
	
	if (!isset($_GET['t']) || !ctype_alnum($_GET['t']))
	{		
		set_message("error", $invalide_link_msg);
		return false;
	}
	
	$token = $_GET['t'];

	$sql = "SELECT userid, token, timeout		
		    FROM email_check
		    WHERE  token = '$token'";

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
		$confirmdata = mysql_fetch_assoc($result);
		
		// Check if token expired
		if (time() > (int)$confirmdata['timeout'])
		{
			// Delete expired recover entry
			$userid = (int)$confirmdata['userid'];
			delete_confirm_email_token($userid, $token);
			return 2;
		}
		else
		{
			$user = getuserdetails($resetdata['userid']);
			return true;
		}
	}
	else 
	{
		set_message("error", $invalid_token_msg);
		return false;
	}		
}

function activate_user_account()
{
	if (!isset($_SESSION['activateid']) || !isset($_SESSION['activatetoken']))
	{
		set_log(__FUNCTION__." Session variables userid and token not set");
		$msg = "There was a system problem. Your password could not be reset at this time. Please try again later or contact support.";
		set_message("error", $msg);
		return false;
	}
	
	$sql = "UPDATE users 
		SET active = 1
		WHERE id = $userid";
	
	$activate_account = mysql_query($sql);

	if (!$activate_account)
	{
		handle_db_error($activate_account, $sql);
		$msg = "Sorry there was a system error. Please try again.";
		set_message("error", $msg);
		return false;
	}
	else
	{
		// Delete table entry
		delete_recover_pwd_token($userid, $token);
		return true;
	}
}
//DONE
function get_confirm_email_token($userid, $token)
{
	$sql = "SELECT userid, token, timeout		
		    FROM email_check
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
//DONE
function set_confirm_email_token($userid)
{	
	$token = gen_uuid();
	$expire = time() + CHECK_EMAIL_LIFETIME;

	$sql = "INSERT INTO email_check (userid, token, timeout)
		VALUES ($userid, '$token', $expire)";

	$request = mysql_query($sql);

	if ($request)
	{
		return $token;
	}
	else
	{
		handle_db_error($addpwdresetrequest, $sql);
		return false;
	}
}
// DONE
function delete_unconfirmed_user_account($userid)
{
	$sql = "DELETE FROM users
		    WHERE id = $userid
		    AND active = 0";
		
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
// DONE
function delete_confirm_email_token($userid, $token)
{
	$sql = "DELETE FROM  email_check
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
// DONE
function send_confirmation_email($username, $email, $token)
{
	$subject = "Vilfredo Account Verification";
	$to = $username . " <" . $email . ">";
	$domain = SITE_DOMAIN;
	
	$message = <<<_HTML_
Hi {$user['username']},

To validate your account, you must visit the URL below within 24 hours

$domain/confirm_email.php?t=$token

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