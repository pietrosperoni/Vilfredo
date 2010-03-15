<?php 
require_once 'config.inc.php';
/*
echo '<h2>Please log in or register</h2>';
echo facebook_connect_for_dialog(DISPLAY_FACEBOOK_LOGIN); 
echo facebook_fbconnect_init_js(); 
*/

$str = '<h2>Please log in or register</h2>';
$str .= '<div id="fb_button">';
$str .= facebook_connect_for_dialog(DISPLAY_FACEBOOK_LOGIN); 
$str .= '</div>';
$str .= facebook_fbconnect_init_js(); 

echo $str;
?>