<?php
include('header.php');

	user_logout();

	// Logout if in loggedin in VGA mode
	/*
	if (IsAuthenticated() && $_SESSION[USER_LOGIN_MODE] == 'VGA')
	{
		unset($_SESSION[USER_LOGIN_ID]);
		unset($_SESSION[USER_LOGIN_MODE]);
		
		// Unset cookies
		$past = time() - TWO_DAYS;
		setcookie(COOKIE_USER, 'DELETED!', $past);
		setcookie(COOKIE_PASSWORD, 'DELETED!', $past);
		set_log("Calling vga_cookie_logout()...");
		vga_cookie_logout();
	}
	*/
	
	$_SESSION['logout'] = true;
	
	
	$redirect = getpostloginredirectlink();
	header("Location: " . $redirect);
	
	//header("Location: login.php");

?> 