<?php
include('header.php');

	// Logout if in loggedin in VGA mode
	if (IsAuthenticated() && $_SESSION[USER_LOGIN_MODE] == 'VGA')
	{
		unset($_SESSION[USER_LOGIN_ID]);
		unset($_SESSION[USER_LOGIN_MODE]);
		
		// Unset cookies
		$past = time() - 100;
		setcookie(ID_my_site, gone, $past);
		setcookie(Key_my_site, gone, $past);
	}
	
	$_SESSION['logout'] = true;
	
	header("Location: login.php");

?> 