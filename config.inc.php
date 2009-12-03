<?php
//******************************************
// DOMAIN SPECIFIC SETTINGS
require_once "loc/config.domain.php";
require_once "priv/dbdata.php";
require_once "priv/social.php";
//******************************************
//
// FACEBOOK CONNECT
//******************************************
require_once 'config.facebook.php';
require_once 'lib/facebook/php/facebook.php';
//******************************************
// Set domain for email links
define("SITE_DOMAIN", "http://" . $_SERVER['HTTP_HOST']);
// Query string parameters
define("QUERY_KEY_TODO", "todo");
define("QUERY_KEY_USER", "u");
define("QUERY_KEY_QUESTION", "q");
define("QUERY_KEY_ROOM", "room");
define("RANDOM_ROOM_CODE_LENGTH", 16);
//
define("USER_LOGIN_ID", 'vilfredo_user_id');
define("USER_LOGIN_MODE", 'vilfredo_login_mode');
//
define('1-YEAR', 3600*24*365);
//
define('COOKIE_LIFETIME', 1-YEAR);
//
// Get rid off this
#define("USE_PRIVACY_FILTER", TRUE);
// Use Facebook Connect
define("USE_FACEBOOK_CONNECT", TRUE);
//******************************************
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
//******************************************
/*
function checkdnsrr($host, $type)
{
	return true;
}
*/
?>