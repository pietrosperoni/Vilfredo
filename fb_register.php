<?php
include('header.php');

if ($userid)
{
	$redirect = getpostloginredirectlink();
	header("Location: " . $redirect);
}
elseif ($FACEBOOK_ID != null && ($userid = fb_isconnected($FACEBOOK_ID)))
{
	if ($userid)
	{
		$_SESSION[USER_LOGIN_ID] = $userid;
		$_SESSION[USER_LOGIN_MODE] = 'FB';
		// log time
		setlogintime($userid);
	}
	
	//header("Location: viewquestions.php");
	$redirect = getpostloginredirectlink();
	header("Location: " . $redirect);
	exit;
} 
elseif(!$FACEBOOK_ID)
{
	header("Location: login.php");
}
else
{
	$error_message = "";
	$registered = false;
	
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{
		if ($_POST['submit'] == "Cancel")
		{
			//$fb->api_client->auth_revokeAuthorization($FACEBOOK_ID);
			//$fb->api("/me", "DELETE");  // ???
			$fb->api("/me?method=delete");
			$FACEBOOK_ID = null;
			header("Location: login.php");
			exit;
		}
		else
		{
			$registered = fb_register_user();
			$error_message = get_message_string();
			clear_messages();
		}
	}
	
	if ($registered)
	{
		//then redirect them to the members area
		$location = getpostloginredirectlink();
		?>

		<h1><?=$VGA_CONTENT['registered_txt']?></h1>
		<p><?=$VGA_CONTENT['welcome_register_txt']?></p>
		<p><a href="<?php echo $location; ?>"><?=$VGA_CONTENT['click_cont_txt']?></a></p>
		<?php
	}
	else
	{
		?>
		<!-- Greet the currently logged-in user! --> 
		<?php echo facebook_profile_pic(); ?>
		<p><?=$VGA_CONTENT['greeting_txt']?> <?php echo facebook_username(); ?>!</p>
		
		<p><strong><?=$VGA_CONTENT['have_account_quest_txt']?></strong> <a href="fb_connect.php"><?=$VGA_CONTENT['click_connect_link']?></a></p>
		
		<p><?=$VGA_CONTENT['otherwise_choose_txt']?></p>
		
		<?php
		$firstName = "";
		$email = "";
		
		if ($FACEBOOK_ID)
		{
			/*
			$user_details=$fb->api_client->users_getInfo($FACEBOOK_ID, array('first_name', 'email'));  
			$firstName=$user_details[0]['first_name'];
			$email=$user_details[0]['email'];
			*/
			
			// V3
			$user_details = $fb->api('/me');
			//var_dump($user_details);
			$firstName=$user_details['first_name'];
			$email=$user_details['email'];
		}

		?>
		
<p><span class="errorMessage"><?php echo $error_message; ?></span></p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table border="0">
		<tr>
			<td><?=$VGA_CONTENT['user_caution_txt']?></td>
			<td>
				<input type="text" value="<?php echo $firstName; ?>" name="username" maxlength="60">
			</td>
		</tr>
		<tr>
			<td><?=$VGA_CONTENT['email_label']?></td>
			<td>
				<input type="text" name="email" value="<?php echo $email; ?>" maxlength="60">
			</td>
		</tr>
		<tr>
			<th colspan=2>
				<input type="submit" name="submit" value="Cancel">
				<input type="submit" name="submit" value="Register">
			</th>
		</tr>
	</table>
</form>
		<p><?=$VGA_CONTENT['email_exp_txt']?></p> 
		<?php
	}
}

include('footer.php');
?> 