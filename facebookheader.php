<?php
// Start output buffer
ob_start();
// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';

$canvas_page = urlencode("http://apps.facebook.com/test_vga/");


if (!$FACEBOOK_ID)
{
	$authorize = "https://www.facebook.com/dialog/oauth?client_id=$facebook_key&redirect_uri=$canvas_page&scope=$facebook_permissions";
	//header("Location: $authorize");	
	echo("<script> top.location.href='" . $authorize . "'</script>");
	exit;
}

if ($FACEBOOK_USER_PROFILE && isset($FACEBOOK_USER_PROFILE["locale"]))
{
	// Get the language without country, en or it
	$fb_locale = substr($FACEBOOK_USER_PROFILE["locale"],0,2);
	set_log('$fb_locale = '.$fb_locale);
	if ( $fb_locale == 'en' or $fb_locale == 'it' )
	{
		$_SESSION['locale'] = $fb_locale;
		$locale = $fb_locale;
	}
	else
	{
		$_SESSION['locale'] = "en";
		$locale = $fb_locale;
	}
}
else
{
	$locale = fetch_preferred_language_from_client();
	$_SESSION['locale'] = $locale;
}

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
@include getLanguage($locale);

// Get user ID if logged in
$userid = fbloggedin();
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<meta http-equiv="expires" content="Thu, 16 Mar 2000 11:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="stylesheet" type="text/css" href="../style.css" media="screen, print" >
</head>