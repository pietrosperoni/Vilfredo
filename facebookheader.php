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
	    console.log("Redirect to "+url);
		top.window.location = url;
	}
</script>
<?php

//$canvas_page = urlencode("http://apps.facebook.com/test_vga/");
$canvas_page = urlencode("http://apps.facebook.com/vilfredo/");
//$canvas_page = urlencode("http://facebook.com/pages/null/365839056784908/app_179697104018");
//$canvas_page = urlencode("http://derek2.pietrosperoni.it");
//$canvas_page = urlencode("http://www.facebook.com/pages/null/365839056784908/app_179697104018");
//$canvas_page = urlencode("http://facebook.com/pages/null/351090528288864/app_179697104018");
//$canvas_page = urlencode("https://www.facebook.com/TestvilleCouncil/app_179697104018");

set_log("***************** facebookheader called *********************");

$fb_request = null;
$fb_page_id = null;
$fb_page = null;
$fb_page_link = null;
$fb_page_app_link = null;

try
{
	$fb_request = $fb->getSignedRequest();
	if ($fb_request && isset($fb_request['page']))
	{
		$fb_page_id = $fb_request['page']['id'];
		set_log("Page $fb_page_id");
	
		if (!isset($_SESSION['FACEBOOK_PAGE_LINK']))
		{
			$fb_page = $fb->api('/'.$fb_page_id);
			$fb_page_link = $fb_page['link'];
			set_log("Page link = $fb_page_link");
			//$fb_page_app_link = $fb_page_link."/app_179697104018";
			$fb_page_app_link = $fb_page_link."/app_162694727825";
			set_log("Page app link = ".$fb_page_app_link);
		}
	}
}
catch (FacebookApiException $e) 
{
	set_log("Error reading signed request to find link $e");
	if ($fb_page_id == "440183516008944")
	{
		$fb_page_link = "http://www.facebook.com/AMATrastevere";
		$fb_page_app_link = $fb_page_link."/app_162694727825";
	}
}

if (!$FACEBOOK_ID)
{
	if (isset($_GET['error'])) 
	{
	  header('Location: fb_authenticated.php');
	exit;
	} 
	else 
	{
	  	if ($fb_page_app_link)
		{
			$_SESSION['FACEBOOK_PAGE_LINK'] = $fb_page_app_link;
			set_log("Setting page link to ".$fb_page_app_link);
		}
		$authorize = "https://www.facebook.com/dialog/oauth?client_id=$facebook_key&redirect_uri=$canvas_page&scope=$facebook_permissions";
		echo("<script> top.location.href='" . $authorize . "'</script>");
		exit;
	}
}

//'http://www.facebook.com/TestvilleCouncil/app_179697104018';
/*
if (isset($_SESSION['FACEBOOK_PAGE_LINK']))
{
	set_log("Redirecting to Page URL {$_SESSION['FACEBOOK_PAGE_LINK']}");
	$page_url = $_SESSION['FACEBOOK_PAGE_LINK'];
	unset($_SESSION['FACEBOOK_PAGE_LINK']);
	?>
	<script type="text/javascript">
	    var page_url = '<?= $page_url ?>';
		gotoURL(page_url);
	</script>
	<?php
}
*/


/*
if (!$FACEBOOK_ID)
{
	$authorize = "https://www.facebook.com/dialog/oauth?client_id=$facebook_key&redirect_uri=$canvas_page&scope=$facebook_permissions";
	echo("<script> top.location.href='" . $authorize . "'</script>");
	exit;
}
*/

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