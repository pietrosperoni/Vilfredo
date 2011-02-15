<?php
include 'header.php';
$userid = $_GET['anon'];
$urlquery = $_GET['query'];

if (isset($userid))
{
	echo "<h3>{$VGA_CONTENT['succ_vote_txt']}</h3>";
	echo "<p>{$VGA_CONTENT['succ_prop_create_id_txt']} <strong>$userid</strong></p>";
}
else
{
	echo "<p>{$VGA_CONTENT['err_anon_vote_txt']}</p>";
}

echo "<p><a href=\"" . SITE_DOMAIN. "/" . $urlquery . "\">{$VGA_CONTENT['click_ret_quest_link']}</a></p>";

include 'footer.php'; 
?>