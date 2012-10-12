<?php
//******************************************
// CONSTANTS
//
//******************************************
// Set domain for email links
define("SITE_DOMAIN", "http://" . $_SERVER['HTTP_HOST']);
//
define("USE_FACEBOOK_CONNECT", TRUE);
define("USE_TWIT_THIS", TRUE);
define("USE_GOOGLE_ANALYTICS", TRUE);
//
// Set cookie lifetime.
if (!defined('ONE_WEEK'))
{
	define('ONE_WEEK', 3600*24*7);
}
define('COOKIE_LIFETIME', ONE_WEEK);
//
define("CLOSE_SITE", FALSE);
//
if (CLOSE_SITE)
{
	define("ADMIN_ACCESS_ONLY", TRUE);
	// Hide Facebook Connect Login
	define("DISPLAY_FACEBOOK_LOGIN", FALSE);
}
else
{
	define("ADMIN_ACCESS_ONLY", FALSE);
	// Display Facebook Connect Login
	define("DISPLAY_FACEBOOK_LOGIN", TRUE);
}
//  Site specific: Paths and Errors
//
define("ERROR_FILE", "/logs/vga_errors.log");
//
//  Error Handling
//
#define("VILFREDO_ERROR", E_USER_NOTICE);
define("VILFREDO_ERROR", E_USER_ERROR);
//
//  Error logging
define("VERBOSE", FALSE);
//
define('DEBUG', false);
//
//******************************************
//	Set Facebook Application Here :
//
#define('FACEBOOK_APPLICATION', 'TEST VILFREDO');
define('FACEBOOK_APPLICATION', '');
//
//******************************************
?>