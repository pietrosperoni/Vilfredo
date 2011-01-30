<?php
include('header.php');

#if (isloggedin())
if ($userid)
{
	$redirect = getpostloginredirectlink();
	header("Location: " . $redirect);
}
else
{
	$logged_in = false;
	$error_message = "";
	
	//if the login form is submitted
	if (isset($_POST['submit'])) 
	{ // if form has been submitted

		$logged_in = login_user();
		$m = get_messages();
		$error_message = $m['error'][0];
	}
	
	if (!$logged_in)
	{

	// if they are not logged in
	?>
	
	<p><span class="errorMessage"><?php echo $error_message; ?></span></p>
	<div class="login_sector">
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
		<table border="0">
		<tr>
			<td colspan=2>
				<h1>Login</h1>
			</td>
		</tr>
		<tr>
			<td>Username:</td>
			<td>
				<input type="hidden" name="user_login_action">
				<input type="text" name="username" maxlength="40">
			</td>
		</tr>
		<tr>
			<td>Password:</td>
			<td>
				<input type="password" name="pass" maxlength="50">
			</td>
		</tr>
		<tr>
			<td>Keep me logged in:</td>
			<td>
				<input name="remember" type="checkbox">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" name="submit" value="Login">
			</td>
		</tr>
		</table>
	</form>
	</div>
	
	<div class="login_sector_soc">
	<?php
	$display_fb_login = true;
	if (ADMIN_ACCESS_ONLY)
	{
		echo "<strong>Sorry: Vilfredo is currently closed for maintenance.</strong></p>";
	}
	?>
	<!-- <?php echo facebook_login_button_refresh("fb_register.php", DISPLAY_FACEBOOK_LOGIN); ?> -->
	<?php echo facebook_login_button_refresh_2(DISPLAY_FACEBOOK_LOGIN); ?>
	</div>
	<div class="clear"></div>
	<?php
	}
}

include('footer.php');
?>