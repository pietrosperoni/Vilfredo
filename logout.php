<?php
include('header.php');

if (isloggedin())
{
	$past = time() - 100;
	setcookie(ID_my_site, gone, $past);
	setcookie(Key_my_site, gone, $past);
	header("Location: login.php");
}
else
{
		header("Location: login.php");
}
?> 