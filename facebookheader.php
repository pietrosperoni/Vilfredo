<?php
// Start output buffer
ob_start();
// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';
require_once BUBBLES_DIR.'/bubble_functions.php';
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
	function gotoURLNotIframe(url)
	{
		url = typeof url !== 'undefined' ? url : '<?= SITE_DOMAIN ?>';
	    //console.log("Redirect to "+url);
		window.location = url;
	}
</script>
<?php

set_log("***************** facebookheader V2 called *********************");

$canvas_page = $vga_facebook['canvas'];
$FACEBOOK_PAGE_ID = NULL;
$FACEBOOK_PAGE_ADMIN = NULL;
$fb_page_app_link = NULL;
$vga_facebook_page = NULL;

// Get signed request (should always be present within Facebook)
$FB_REQUEST = $fb->getSignedRequest();
set_log("FB Request Object Follows");
set_log($FB_REQUEST);

// Test if from Page - get Page ID
if (isset($FB_REQUEST['page']))
{
	$FACEBOOK_PAGE_ID = $FB_REQUEST['page']['id'];
	$FACEBOOK_PAGE_ADMIN = $FB_REQUEST['page']['admin'];
	
	// Fetch page info from DB if any
	$vga_facebook_page = getFacebookPage($FACEBOOK_PAGE_ID);
	
	//set_log("Called from Page $FACEBOOK_PAGE_ID");
	//print_r($FB_REQUEST);
	//echo "<br/><br/><br/>";
	//print_r($fb->api('/'.$FACEBOOK_PAGE_ID));
	//print_r($vga_facebook_page);
}
else
{
	set_log("SignedRequest not set");
}

// If not authenticated, send to authentication after storing page ID and redirect URL (Facebook broken)
if (!isset($FB_REQUEST['user_id']))
{
	set_log("User not set in Signed Request! Not authenticated?");
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
				set_log('$fb_page Info follows:');
				set_log($fb_page);
				
				// Check if Graph returned page info (could be nothng if page not published)
				if ($fb_page)
				{
					set_log("Page link = " . $fb_page['link']);
					$fb_page_app_link = $fb_page['link']."/app_".$vga_facebook['app_id'];//==>SET LINK
					$_SESSION['FACEBOOK_PAGE_LINK'] = $fb_page_app_link;
				
					if (!$vga_facebook_page)
					{
						// For a new Page entry set default room to common and question to 0
						addFacebookPage($FACEBOOK_PAGE_ID, $fb_page_app_link, '', 0);
						set_log('Adding Facebook Page to DB');
					}
					elseif ($fb_page_app_link != $vga_facebook_page['link'])
					{
						updateFacebookPageLink($FACEBOOK_PAGE_ID, $fb_page_app_link);
						set_log('DB Link different - Updating Facebook Page link in DB');
					}
				}
				else // Page may not be published
				{ ?>
					<div class="htmlerrormsg">
					<h2>Sorry, there was a problem</h2>
					<p>Facebook returned no information about this Page, possibly because it is not yet published.</p>
					<p>This seems to happen even if you are an Admin for that page.</p>
					<p>Either click on the App button at the top of the page to return to your Page or click the link 
					below to go the application's canvas page.</p>
					<a onclick="gotoURLNotIframe('<?=$canvas_page?>')" href="">Go to the Bubbles application page</a>
					</div>
					<?php
					//echo("<script> window.top.location = '" . $redirect_uri . "'</script>");
					exit;
				}
			}
			// else check if Page ID registered with Bubbles
			catch (FacebookApiException $e) 
			{
				set_log("Error reading signed request $e");
				set_log("Checking if Page $FACEBOOK_PAGE_ID registered with Bubbles");
				// set link to stored setting
				if ($vga_facebook_page && $vga_facebook_page['link'])
				{
					$fb_page_app_link = $vga_facebook_page['link']."/app_".$vga_facebook['app_id'];//==>SET LINK
					$_SESSION['FACEBOOK_PAGE_LINK'] = $fb_page_app_link;
					set_log('Failed to get link from Graph. Using link from DB');
				}
				/*
				if (array_key_exists($FACEBOOK_PAGE_ID, $facebook_pages))
				{
					$fb_page_link = $facebook_pages[$FACEBOOK_PAGE_ID]['link'];
					$fb_page_app_link = $fb_page_link."/app_".$vga_facebook['app_id'];//==>SET LINK
					$_SESSION['FACEBOOK_PAGE_LINK'] = $fb_page_app_link;
					set_log("Page App link set to $fb_page_app_link");
				}
				*/
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