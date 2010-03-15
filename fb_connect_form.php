<?php
require_once 'config.inc.php';

if (!$FACEBOOK_ID)
{
	echo '2';
	exit();
}

else
{	
	$pic = facebook_profile_pic();
	$user = facebook_username();

	$connect =<<< _HTML_
	<div id="connect" title="Connect with Facebook">
	$pic
	<p>Hello <strong>$user!</strong></p>

	<p><strong>Don't have a Vilfredo account?</strong> <a id="fbregister" href="#">Register with your Facebook ID</a></p>

	<p>Otherwise, please log in to connect your accounts:</p>

		<form action="#" method="post">
		<fieldset>
			<label for="username">Name</label>
			<input class="text ui-widget-content ui-corner-all" type="text" name="username" id="username" maxlength="60">
			<label for="pass">Password</label>
			<input class="text ui-widget-content ui-corner-all" type="password" name="pass" id="pass" maxlength="10">
			<input type="hidden" name="fbuserid" id="fbuserid" value="$FACEBOOK_ID">
		</fieldset>
		</form>
	</div>
_HTML_;

	$connect .= facebook_fbconnect_init_js(); 

	echo $connect;
	exit();
}
?>