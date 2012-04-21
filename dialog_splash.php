<?php 
require_once 'config.inc.php';

if (isset($_SESSION["locale"]) and ($_SESSION["locale"] == 'en' or $_SESSION["locale"] == 'it' ))
{
	$locale = $_SESSION["locale"];
}
else
{
	$locale = fetch_preferred_language_from_client();
}
@include getLanguage($locale);

$str = '<h2>' . getVGAContent('splash_txt') . '</h2>';
//$str .= facebook_connect_for_dialog(DISPLAY_FACEBOOK_LOGIN); 
//$str .= facebook_fbconnect_init_js(); 

echo $str;
?>