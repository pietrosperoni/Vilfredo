<?php
// Start output buffer
//ob_start();
// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';

echo $headcommands;

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

// Get user ID if logged in
$userid=isloggedin();
?>