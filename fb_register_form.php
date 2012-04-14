<?php
require_once 'config.inc.php';

$userid = false;

if ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	$_SESSION[USER_LOGIN_ID] = $userid;
	$_SESSION[USER_LOGIN_MODE] = 'FB';
	// log time
	setlogintime($userid);
	echo '1';
	exit();
}
elseif (!$FACEBOOK_ID)
{
	echo '2';
	exit();
}
// Or present the FB register form
else
{
	$pic = facebook_profile_pic();
	$user = facebook_username();

	$firstName = "";
	if ($FACEBOOK_ID)
	{
		//$user_details=$fb->api_client->users_getInfo($FACEBOOK_ID, array('first_name'));  
		//$firstName=$user_details[0]['first_name'];
		
		// V3
		$user_details = $fb->api('/me');
		$firstName=$user_details['first_name'];
	}

	$register =<<< _HTML_
	<div id="register" title="Vilfredo Register">
	$pic
	<p>Hello <strong>$user!</strong></p>
	
	<p><strong>Already have a Vilfredo account?</strong> <a id="fbconnect" href="#">Connect your accounts</a></p>

	<p>Otherwise, please select a username.</p>
		<form action="#" method="post">
		<fieldset>
			<label for="username">Choose a User Name</label>
			<input class="text ui-widget-content ui-corner-all" type="text" name="username" id="username" value="$firstName" maxlength="60">
		<div class="reg_form">
			<label for="email">Email</label>
			<input class="text ui-widget-content ui-corner-all" type="text" name="email" id="email" maxlength="60">
			<input type="hidden" name="usernameok" id="usernameok" value="">
			<input type="hidden" name="fbuserid" id="fbuserid" value="$FACEBOOK_ID">
		</div>
		</fieldset>
		</form>
	</div>
_HTML_;

	$register .= facebook_fbconnect_init_js(); 

	echo $register;
	exit();
}
?>