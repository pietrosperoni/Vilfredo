<?php
include('header.php');
include('process_recover.php');

$error_message = "";

if ($userid)
{
	header("Location: viewquestions.php");
	exit;
}

elseif (isset($_GET['resetpwd']))
{
	// enter email form
	include 'recover__enter_email_form.php';
}

elseif (isset($_POST['cancel-pwd-request']))
{
	header('Location: login.php');
	exit;
}

elseif (isset($_POST['submit-email']))
{
	// If email address found generate token and email user
	$email_found = check_email();
	
	if (!$email_found)
	{
		$error_message = get_message_string();
		clear_messages();
	
		// redisplay email form
		include 'recover__enter_email_form.php';
	}
	else
	{
		include 'recover__enter_reset_code_form.php';
	}
}

elseif (isset($_GET['t']) && isset($_GET['u']))
{
	$validate = validate_recover_code_link();
	if (!$validate)
	{
		$error_message = get_message_string();
		clear_messages();
		// display enter reset code form
		include 'recover__enter_reset_code_form.php';
	}
	elseif ($validate === 2)
	{
		include 'recover__expired.php';
	}
	else
	{
		include 'recover__new_password_form.php';
	}
}

elseif (isset($_POST['submit-code']))
{
	$validate = validate_recover_code();
	
	if (!$validate)
	{
		$error_message = get_message_string();
		clear_messages();
		// display enter reset code form
		include 'recover__enter_reset_code_form.php';
	}
	elseif ($validate === 2)
	{
		include 'recover__expired.php';
	}
	else
	{
		include 'recover__new_password_form.php';
	}
}

elseif (isset($_POST['submit-pwd']))
{
	$user = reset_user_password();
	
	if (!$user)
	{
		$error_message = get_message_string();
		clear_messages();
		// display enter reset code form
		include 'recover__new_password_form.php';
	}
	else
	{
		// display the complete page
		include 'recover__complete.php';
	}
}

// Default. When user clicks on the link to manually enter a token
else
{
	include 'recover__enter_reset_code_form.php';
}

include('footer.php');
?>