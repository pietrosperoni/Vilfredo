<?php
// Start output buffer
ob_start();
// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';

if (isset($_GET["locale"]) and ($_GET["locale"] == 'en' or $_GET["locale"] == 'it' ))
{
	$locale = $_GET["locale"];
	$_SESSION['locale'] = $locale;
}
elseif (isset($_SESSION["locale"]) and ($_SESSION["locale"] == 'en' or $_SESSION["locale"] == 'it' ))
{
	$locale = $_SESSION["locale"];
}
else
{
	$locale = fetch_preferred_language_from_client();
	$_SESSION['locale'] = $locale;
}

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
@include getLanguage($locale);

$facebookapp = TRUE;

// Get user ID if logged in
//$userid=isloggedin();
$userid=fbloggedin();
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<meta http-equiv="expires" content="Thu, 16 Mar 2000 11:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="stylesheet" type="text/css" href="../style.css" media="screen, print" >
</head>