<?php
include('header.php');

	// Logout if in loggedin in VGA mode
	if (IsAuthenticated() && $_SESSION[USER_LOGIN_MODE] == 'VGA')
	{
		unset($_SESSION[USER_LOGIN_ID]);
		unset($_SESSION[USER_LOGIN_MODE]);
		
		// Unset cookies
		$past = time() - 100;
		setcookie(COOKIE_USER, gone, $past);
		setcookie(COOKIE_PASSWORD, gone, $past);
	}
	
	$_SESSION['logout'] = true;
	
	header("Location: login.php");

?> 