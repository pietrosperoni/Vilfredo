<?php
include("header.php");

$dotrace = true;

$fb_userid = $_POST['fb_sig_user'];
$info = fb_getuserdetails($fb_userid);
set_trace("Deauthorizing Facebook account for user: $fb_userid", $dotrace);

$verified = fb_verify_ping($facebook_secret);

if ($verified) 
{ 
	if ($_POST['fb_sig_uninstall'] == 1) 
	{ 	// The user has deauthorized Vilfredo
		set_trace("Request verified", $dotrace);
	
		if (empty($info['password']))
		{	// Delete Facebook-only account
			set_trace("Deleting Facebook-only account", $dotrace);
			//$sql = "DELETE FROM users WHERE fb_userid = $fb_userid";
			$sql = "UPDATE users SET active = 0 WHERE fb_userid = $fb_userid";
		}
		else 
		{	// Unconnect accounts
			set_trace("Unconnecting account", $dotrace);
			$sql = "UPDATE users SET fb_userid = '' WHERE fb_userid = $fb_userid";
		}
	
		$ret = mysql_query($sql);
		 if (!$ret) 
		 {
			set_trace("Could not delete/disconnect Facebook account", $dotrace);
		 } 
	}
} 
else 
{ 
	// Log the IP and request for future reference?
	set_trace("Request Unverified: Sigs didn't match!", $dotrace);
} 
?>