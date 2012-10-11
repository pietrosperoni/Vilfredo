<?php
// Start session for login and redirects
session_start();
//******************************************/
// DOMAIN SPECIFIC SETTINGS
require_once "priv/config.domain.php";
require_once "priv/dbdata.php";
require_once "priv/sys.php";
require_once "priv/social.php";
require_once 'priv/bubbles.config.php';

require_once 'process_input.php';
require_once 'graphs.php';
require_once 'vga_functions.php';
include_once 'lib/php_lib.php';
require_once 'lib/htmlpurifier-4.0.0-live/HTMLPurifier.standalone.php';
require_once "lib/feedcreator-1.7.2-ppt/include/feedcreator.class.php";
require_once "vga_bubble_functions.php";

// Set error logs if log directory is defined (in config.domain.php)
if (defined('LOG_DIRECTORY'))
{
	ini_set('log_errors', 'On');
	ini_set('error_log', LOG_DIRECTORY.'vga_runtime_errors.log');

	define("ERROR_FILE", LOG_DIRECTORY."vga_error.log");
	define("LOG_FILE", LOG_DIRECTORY."vga.log");
}

ini_set('error_reporting', E_ALL & ~E_NOTICE);


// ******************************************
// Connects to the Database
mysql_connect($dbaddress, $dbusername, $dbpassword) or die(mysql_error());
mysql_set_charset('utf8');
mysql_select_db($dbname) or die(mysql_error());
//******************************************
//
// FACEBOOK CONNECT
//******************************************
require_once 'config.facebook.php';
require_once 'lib/facebook_v3/src/facebook.php';

// V3
$fb = new Facebook(array(
  'appId'  => $facebook_key,
  'secret' => $facebook_secret,
  'cookie' => true
));
/*
	If $FACEBOOK_ID != NULL then current user is Facebook Authroized
*/
$FACEBOOK_ID = null;
$FACEBOOK_USER_PROFILE = null;


if (USE_FACEBOOK_CONNECT)
{
	// Get User ID
	$FACEBOOK_ID = $fb->getUser();
	
	// Get user profile - if session valid
	if ($FACEBOOK_ID) 
	{
		try 
		{
			$FACEBOOK_USER_PROFILE = $fb->api('/me');
			//set_log("Facebook ID = ".$FACEBOOK_ID);
			//set_log("FB Profile locale = ".$FACEBOOK_USER_PROFILE['locale']);
		} 
		catch (FacebookApiException $e) 
		{
			//set_log("FB Profile locale not set");
			set_log("Error fetching user profile: ".$e);
			$FACEBOOK_ID = null;
		}
	}
	else
	{
		//set_log('$FACEBOOK_ID not set');
	}
}

/*
if (USE_FACEBOOK_CONNECT)
{
	$FACEBOOK_USER_PROFILE = get_current_facebook_userid_v3_profile($fb);
	if (!is_null($FACEBOOK_USER_PROFILE))
	{
		$FACEBOOK_ID = $FACEBOOK_USER_PROFILE['id'];
		set_log("Facebook ID = ".$FACEBOOK_ID);
		set_log("FB Profile locale = ".$FACEBOOK_USER_PROFILE['locale']);
	}
	else
	{
		set_log("FB Profile locale not set");
	}
}*/
//******************************************/
define("COOKIE_USER", "ID_my_site");
define("COOKIE_PASSWORD", "Key_my_site");
define("VGA_PL", "vgapl");
//
define("SHOW_QICON_ROOMS", TRUE);
//
// Query string parameters
define("QUERY_KEY_TODO", "todo");
define("QUERY_KEY_USER", "u");
define("QUERY_KEY_QUESTION", "q");
define("QUERY_KEY_ROOM", "room");
define("QUERY_KEY_PROPOSAL", "p");
define("QUERY_KEY_GENERATION", "g");
define("QUERY_KEY_QUESTION_BUBBLE", "qb");
define("COOKIE_KEY_QUESTION_BUBBLE", "qb");
define("RANDOM_ROOM_CODE_LENGTH", 16);
//
define("USER_LOGIN_ID", 'vilfredo_user_id');
define("USER_LOGIN_MODE", 'vilfredo_login_mode');
//
if (!defined('PWD_RESET_LIFETIME'))
{
	define('PWD_RESET_LIFETIME', 3600*24*2);
}
if (!defined('CHECK_EMAIL_LIFETIME'))
{
	define('CHECK_EMAIL_LIFETIME', 3600*24*2);
}
if (!defined('COOKIE_LIFETIME'))
{
	define('COOKIE_LIFETIME', 3600*24*2);
}
//
// Get rid off this
#define("USE_PRIVACY_FILTER", TRUE);
//******************************************/
// TEMP WIN/PHP FIX
// Use a dummy function to return true if no checkdnsrr()
// --  This function not available on Windows platforms
//      before PHP version 5.3.0. For live windows platforms without
//	checkdnsrr() another function could be substituted.
//
//	Eg. From PHP Manual:  http://php.net/manual/en/function.checkdnsrr.php
//	For compatibility with Windows before this was implemented, 
//	then try the » PEAR class » Net_DNS. 
//	
//******************************************/
/*
function checkdnsrr($host, $type)
{
	return true;
}
*/
?>