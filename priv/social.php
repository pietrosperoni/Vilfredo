<?php
//******************************************
//	FACEBOOK_APPLICATION is set in 
//
//	config.domain.php
//
//******************************************

switch (FACEBOOK_APPLICATION) #you probably will have several applications on facebook connected with your vilfredo. Here is where you chose which one is active
{
case "TEST VGA":
 $facebook_key="";
 $facebook_secret="";
 break;
case "SURF VGA":
 $facebook_key="";
 $facebook_secret="";
 break;
case "VILFREDO":
 $facebook_key="";
 $facebook_secret="";
 break;
case "TEST VILFREDO":
 $facebook_key="";
 $facebook_secret="";
 break;
default: 
 error('No Application defined.');
}
 

?>