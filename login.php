<?php
include('header.php');

//print_array($_SESSION['messages']);

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
		$error_message = get_message_string();
		clear_messages();
	}
	
	if ($logged_in)
	{
		$redirect = getpostloginredirectlink();
		//set_log(__FILE__.' :: getpostloginredirectlink = '.$redirect);
		header("Location: " . $redirect);
		exit;
	}
	
	else
	{

	// if they are not logged in
	?>
	
	<p><span class="errorMessage"><?= $error_message ?></span></p>
	<div class="login_sector">
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
		<table border="0">
		<tr>
			<td colspan=2>
				<h1><?=$VGA_CONTENT['login_link']?></h1>
			</td>
		</tr>
		<tr>
			<td><?=$VGA_CONTENT['username_label']?></td>
			<td>
				<input type="hidden" name="user_login_action">
				<input type="text" name="username" maxlength="40" value="<?=$_POST['username']?>">
			</td>
		</tr>
		<tr>
			<td><?=$VGA_CONTENT['password_label']?></td>
			<td>
				<input type="password" name="pass" maxlength="50">
			</td>
		</tr>
		<tr>
			<td><?=$VGA_CONTENT['keep_logged_label']?></td>
			<td>
				<input name="remember" type="checkbox">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<input type="submit" name="submit" value="<?=$VGA_CONTENT['login_link']?>"> 
			</td>
		</tr>
		</table>
	</form>
	<a href="recover.php?resetpwd=1">Forgotten your password?</a>
	</div>
	
	<div class="login_sector_soc">
	<?php
	$display_fb_login = true;
	if (ADMIN_ACCESS_ONLY)
	{
		echo "<strong>Sorry: Vilfredo is currently closed for maintenance.</strong></p>";
	}
	?>
	<?php echo facebook_login_button_refresh_2(DISPLAY_FACEBOOK_LOGIN); ?>
	</div>
	<div class="clear"></div>
	<?php
	}
}

include('footer.php');
?>