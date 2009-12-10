<?php
include("header.php");

set_error("deauthorizing Facebook account... Verbose");

$fb_userid = $_POST['fb_sig_user'];
$info = fb_getuserdetails($fb_userid);
set_error(date(DATE_RFC822) . ": Deauthorizing Facebook account for user: " . $fb_userid, true);

$verified = fb_verify_ping($facebook_secret);

if ($verified) 
{ 
	if ($_POST['fb_sig_uninstall'] == 1) 
	{ 	// The user has deauthorized Vilfredo
		set_error("The user has deauthorized Vilfredo");
	
		if (empty($info['password']))
		{	// Delete Facebook-only account
			set_error("Delete Facebook-only account", true);
			$sql = "DELETE FROM users WHERE fb_userid = $fb_userid";
		}
		else 
		{	// Unconnect accounts
			set_error("Unconnect account", true);
			$sql = "UPDATE users SET fb_userid = '' WHERE fb_userid = $fb_userid";
		}
	
		$ret = mysql_query($sql);
		 if (!$ret) 
		 {
			set_error("Could not delete/disconnect Facebook account", true);
		 } 
	}
} 
else 
{ 
	// Log the IP and request for future reference?
	set_error("Sigs didn't match!", true);
} 
?>