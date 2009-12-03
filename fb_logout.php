<?php
include('header.php');

if (IsAuthenticated() && $_SESSION[USER_LOGIN_MODE] == 'FB')
{
	unset($_SESSION[USER_LOGIN_ID]);
	unset($_SESSION[USER_LOGIN_MODE]);
}

header("Location: login.php");
?>