<?php
// Start output buffer
ob_start();
// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';
?>
<script type="text/javascript">
	function gotomainsite()
	{
		top.window.location='<?= SITE_DOMAIN ?>';
	}
	function gotoURL(url)
	{
		url = typeof url !== 'undefined' ? url : '<?= SITE_DOMAIN ?>';
	    //console.log("Redirect to "+url);
		top.window.location = url;
	}
</script>
<?php

set_log("***************** facebookheader called *********************");

$canvas_page = $vga_facebook['canvas'];
$FACEBOOK_PAGE_ID = NULL;
$FACEBOOK_PAGE = NULL;
$FACEBOOK_PAGE_ADMIN = NULL;
$fb_page_app_link = NULL;

// Get signed request (should always be present within Facebook)
$FB_REQUEST = $fb->getSignedRequest();

// Test if from Page - get Page ID
if (isset($FB_REQUEST['page']))
{
	$FACEBOOK_PAGE = $FB_REQUEST['page'];
	$FACEBOOK_PAGE_ID = $FB_REQUEST['page']['id'];
	$FACEBOOK_PAGE_ADMIN = $FB_REQUEST['page']['admin'];
	set_log("Called from Page $FACEBOOK_PAGE_ID");
	//print_r($FB_REQUEST);
	//echo "<br/><br/><br/>";
	//print_r($fb->api('/'.$FACEBOOK_PAGE_ID));
}

// If not authenticated, send to authentication after storing page ID and redirect URL (Facebook broken)
if (!isset($FB_REQUEST['user_id']))
{
	// If not authenticated and error set assume user failed to authenticate
	if (isset($_GET['error'])) 
	{
		header('Location: fb_authenticated.php');
		exit;
	}
	// If from Page then store Page App URL in Session for redirect (if not already stored)
	elseif ($FACEBOOK_PAGE_ID)
	{
		// Store Page App URL in Session for redirection (Facebook broken)
		if (!isset($_SESSION['FACEBOOK_PAGE_LINK']))
		{
			// Try and fetch the Page URL from Graph
			try
			{
				$fb_page = $fb->api('/'.$FACEBOOK_PAGE_ID);
				set_log("Page link = $fb_page_link");
				$fb_page_app_link = $fb_page['link']."/app_".$vga_facebook['app_id'];//==>SET LINK
				$_SESSION['FACEBOOK_PAGE_LINK'] = $fb_page_app_link;
				set_log("Page app link = ".$fb_page_app_link);
			}
			// else check if Page ID registered with Bubbles
			catch (FacebookApiException $e) 
			{
				set_log("Error reading signed request $e");
				set_log("Checking if Page $FACEBOOK_PAGE_ID registered with Bubbles");
				// set link to stored setting
				if (array_key_exists($FACEBOOK_PAGE_ID, $facebook_pages))
				{
					$fb_page_link = $facebook_pages[$FACEBOOK_PAGE_ID]['link'];
					$fb_page_app_link = $fb_page_link."/app_".$vga_facebook['app_id'];//==>SET LINK
					$_SESSION['FACEBOOK_PAGE_LINK'] = $fb_page_app_link;
					set_log("Page App link set to $fb_page_app_link");
				}
			}
		}
	}
	
	// Send to authentication
	$authorize = "https://www.facebook.com/dialog/oauth?client_id=$facebook_key&redirect_uri=$canvas_page&scope=$facebook_permissions";
	//echo("<script> top.location.href='" . $authorize . "'</script>");
	echo("<script> window.top.location = '" . $authorize . "'</script>");
	exit;
}
else
{	
	// User authorized, redirect to Page App if redirect link stroed in Session
	/*
	if (isset($_SESSION['FACEBOOK_PAGE_LINK']))
	{
		$page_app_url = $_SESSION['FACEBOOK_PAGE_LINK'];
		unset($_SESSION['FACEBOOK_PAGE_LINK']);
		set_log("Redirecting back to Page App URL $page_app_url");
		echo("<script> window.top.location = '" . $page_app_url . "'</script>");
		exit;
	}*/
}

if (isset($FB_REQUEST['user']['locale']))
{
	$fb_locale = substr($FB_REQUEST['user']['locale'],0,2);
	set_log('Locale from signed request = '.$fb_locale);
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
elseif ($FACEBOOK_USER_PROFILE && isset($FACEBOOK_USER_PROFILE["locale"]))
{
	// Get the language without country, en or it
	$fb_locale = substr($FACEBOOK_USER_PROFILE["locale"],0,2);
	set_log('Locale from user profile = '.$fb_locale);
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

// Get user ID if connected otherwise send to vga registration page
$userid = fbloggedin();


// Clear Registration details stored in Session
if (isset($_SESSION['FACEBOOK_APP']))
{
	unset($_SESSION['FACEBOOK_APP']);
}

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
@include getLanguage($locale);

?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<meta http-equiv="expires" content="Thu, 16 Mar 2000 11:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="stylesheet" type="text/css" href="../style.css" media="screen, print" >
</head>