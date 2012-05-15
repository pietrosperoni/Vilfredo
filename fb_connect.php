<?php
include('header.php');

if(!$FACEBOOK_ID)
{
	header("Location: login.php");
}
else
{
	$error_message = "";
	$connected = false;
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
			$connected = fb_connect_user();
			$error_message = get_message_string();
			clear_messages();
		}
	}

	if ($connected)
	{
		$location = getpostloginredirectlink();
	?>	
		<h2><?=$VGA_CONTENT['connected_ok_txt']?></h2>
		<p><a href="<?php echo $location; ?>"><?=$VGA_CONTENT['click_cont_txt']?></a></p>
	<?php
	}

	else {
	?>
	<!-- Greet the currently logged-in user! --> 
	<?php echo facebook_profile_pic(); ?>
	<p><?=$VGA_CONTENT['greeting_txt']?> <?php echo facebook_username(); ?>!</p>

	<p><?=$VGA_CONTENT['link_or_connect_txt']?> <a href="fb_register.php"><?=$VGA_CONTENT['new_account_via_facebook_link']?></a></p>
	<br/>
	<?=$VGA_CONTENT['link_account_txt']?>
	<p><span class="errorMessage"><?php echo $error_message; ?></span></p>
	<div class = "login_fields">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<fieldset>
	<label for="username"><?=$VGA_CONTENT['username_label']?></label>
	<input class="text ui-widget-content ui-corner-all" type="text" id="username" name="username"><br />
	<label for="pass"><?=$VGA_CONTENT['password_label']?></label>
	<input type="password" name="pass"><br />
	<input type="submit" name="submit" value="<?=$VGA_CONTENT['cancel_button']?>">
	<input type="submit" value="<?=$VGA_CONTENT['connect_button']?>" name="submit">
	</fieldset>
	</form>
	<div>
	<?php
	}
}

include('footer.php');
?>