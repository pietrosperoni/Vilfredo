<?php
include('header.php');

require_once('lib/recaptcha-php-1.11/recaptchalib.php');

#if (isloggedin())
if ($userid)
{
	//echo "You have already registered, and are logged in. Would you like to <a href=logout.php>Logout</a>?<p>";
	$redirect = getpostloginredirectlink();
	header("Location: " . $redirect);
}
else
{
	$registered = false;
	$recaptcha_error = null;
	$error_message = "";
	
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{
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
		{
			$registered = register_user();
			$m = get_messages();
			$error_message = $m['error'][0];
		}
	}
	
	if ($registered)
	{
		$userid = mysql_insert_id();
		// start a user session
		$_SESSION[USER_LOGIN_ID] = $userid;
		$_SESSION[USER_LOGIN_MODE] = 'VGA';
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
		
		?>
		<!-- <h1>Registered</h1>
		<p>Thank you, you have registered - you may now <a href="login.php">login</a></p> -->
		<?php
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
					<td>Username: (note this will be published on the site, so don't use your email address, unless you want that to be public and potentially taken by spiders)</td>
					<td>
						<input type="text" name="username" maxlength="60" value="<?php echo $_POST['username']?>">
					</td>
				</tr>
				<tr>
					<td>Email: (Optional)</td>
					<td>
						<input type="text" name="email" maxlength="60" value="<?php echo $_POST['email']?>">
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>
						<input type="password" name="pass">
					</td>
				</tr>
				<tr>
					<td>Confirm Password:</td>
					<td>
						<input type="password" name="pass2">
					</td>
				</tr>
				<tr>
					<td>Please enter the words in the image:</td>
					<td>
						<?php echo recaptcha_get_html($recaptcha_public_key, $recaptcha_error); ?>
					</td>
				</tr>
				<tr>
					<th colspan=2>
						<input type="submit" name="submit" value="Register">
					</th>
				</tr>
			</table>
		</form>
		<p><b>Note:</b> the email is needed to receive updates on the questions you are working on. <br/>But you will be able to chose, for each question, if you want to be updated.</p> 
		<?php
	}
}

include('footer.php');
?> 