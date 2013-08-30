<?php
include('header.php');

?>
<style type="text/css">
input[type='submit'].registerbutton {
	font-size: 1.2em;
	width: 125px;
	height: 60px;
	margin-left: 0;
	margin-top: 25px;
}
input[type='text'].register, input[type='password'].register {
	height: 25px;
}
</style>
<?php

if (USE_CAPTCHA) {
	require_once('lib/recaptcha-php-1.11/recaptchalib.php');
}

if ($userid)
{
	//echo "You have already registered, and are logged in. Would you like to <a href=logout.php>Logout</a>?<p>";
	$redirect = getpostloginredirectlink();
	header("Location: " . $redirect);
}
else
{
	$registered = false;
	//$recaptcha_error = null;
	$error_message = "";
	
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{
		/*
		$resp = recaptcha_check_answer ($recaptcha_private_key,
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
		
		if (!$resp->is_valid) {
			// What happens when the CAPTCHA was entered incorrectly
			$registered = false;
			$recaptcha_error = $resp->error;
			$error_message = "Sorry, you did not enter the captcha words correctly.";
		} 
		else
		{*/
			$registered = register_user();
			$error_message = get_message_string();
			clear_messages();
		//}
	}
	
	if ($registered)
	{
		//then redirect them to the members area
		if (isset($_SESSION['request']) && !empty($_SESSION['request']))
		{
			// Now send the user to his desired page
			$request = $_SESSION['request'];
			unset($_SESSION['request']);
			header("Location: " . $request);
		}
		else {
			header("Location: viewquestions.php");
		}
	}
	else
	{
		?>
		 <script type="text/javascript">
		 var RecaptchaOptions = {
		    theme : 'clean'
		 };
 		</script>
		<p><span class="errorMessage"><?php echo $error_message; ?></span></p>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table border="0">
				<tr>
					<td><?=$VGA_CONTENT['username_label']?> <?=$VGA_CONTENT['user_caution_txt']?></td>
					<td>
						<input class="register" type="text" name="username" size="40" maxlength="<?php echo MAX_LEN_USERNAME ?>" value="<?php echo $_POST['username']?>">
					</td>
				</tr>
				<tr>
					<td><?=$VGA_CONTENT['email_label']?></td>
					<td>
						<input class="register" type="text" name="email" size="40" maxlength="<?php echo MAX_LEN_EMAIL ?>" value="<?php echo $_POST['email']?>">
					</td>
				</tr>
				<tr>
					<td><?=$VGA_CONTENT['password_label']?></td>
					<td>
						<input class="register" type="password" size="20" maxlength="<?php echo MAX_LEN_PASSWORD ?>" name="pass">
					</td>
				</tr>
				<tr>
					<td><?=$VGA_CONTENT['pass_conf_label']?></td>
					<td>
						<input class="register" type="password" size="20" maxlength="<?php echo MAX_LEN_PASSWORD ?>" name="pass2">
					</td>
				</tr>
				<?php if (USE_CAPTCHA) { ?>
				<tr>
					<td><?=$VGA_CONTENT['captch_req_label']?></td>
					<td>
						<?php echo recaptcha_get_html($recaptcha_public_key); ?>
					</td>
				</tr>
				<?php } ?>
			</table>
			<input class="registerbutton" type="submit" name="submit" value="<?=$VGA_CONTENT['register_link']?>">
		</form>
		<p><?=$VGA_CONTENT['email_exp_txt']?></p> 
		<?php
	}
}

include('footer.php');
?> 