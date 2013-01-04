<?php
//******************************************
//	FACEBOOK_APPLICATION is set in 
//
//	config.domain.php
//
//******************************************

switch (FACEBOOK_APPLICATION) #you probably will have several applications on facebook connected with your vilfredo. Here is where you chose which one is active
{
case "MY_FACEBOOK_APP_1":
 $facebook_key="";
 $facebook_secret="";
 break;
case "MY_FACEBOOK_APP_2":
 $facebook_key="";
 $facebook_secret="";
 break;
default: 
 exit('No Application defined.');
}
 
switch (FACEBOOK_APPLICATION)
{
case "MY_FACEBOOK_APP_1":
	$vga_facebook = array(
		"key" => "",
		"secret" => "",
		"canvas" => urlencode(""),
		"app_id" => ""
	);
	break;
case "MY_FACEBOOK_APP_1":
	$vga_facebook = array(
		"key" => "",
		"secret" => "",
		"canvas" => urlencode(""),
		"app_id" => ""
	);
	break;
default: 
	exit('No Application defined.');
}
?>