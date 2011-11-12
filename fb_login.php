<?php
include('config.inc.php');

if ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	if ($userid)
	{
		$_SESSION[USER_LOGIN_ID] = $userid;
		$_SESSION[USER_LOGIN_MODE] = 'FB';
		// log time
		setlogintime($userid);
	}
	
	header("Location: viewquestions.php");
}  
else
{
	header("Location: login.php");
}
?>