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
	//if the login form is submitted
	if (isset($_POST['submit'])) 
	{ // if form has been submitted

		// makes sure they filled it in
		if(!$_POST['username'] | !$_POST['pass']) 
		{
			die('You did not fill in a required field.');
		}
		// checks it against the database

		if (!get_magic_quotes_gpc()) 
		{
			$_POST['email'] = addslashes($_POST['email']);
		}
		
		$check = mysql_query("SELECT * FROM users WHERE username = '".$_POST['username']."'")or die(mysql_error());

		//Gives error if user dosen't exist
		$check2 = mysql_num_rows($check);
		if ($check2 == 0) 
		{
			die('That user does not exist in our database. <a href=register.php>Click Here to Register</a>');
		}
		while($info = mysql_fetch_array( $check ))
		{
			$_POST['pass'] = stripslashes($_POST['pass']);
			$info['password'] = stripslashes($info['password']);
			$_POST['pass'] = md5($_POST['pass']);

			//gives error if the password is wrong
			if ($_POST['pass'] != $info['password']) 
			{
				die('Incorrect password, please try again.');
			}
			else
			{
				// if login is ok then we start a user session
				$_SESSION[USER_LOGIN_ID] = $info['id'];
				$_SESSION[USER_LOGIN_MODE] = 'VGA';
				
				// if Keep Logged In is checked then we add a cookie
				if (isset($_POST['remember']) && $_POST['remember'] == 'on')
				{
					$_POST['username'] = stripslashes($_POST['username']);
					$hour = time() + COOKIE_LIFETIME;
					setcookie(ID_my_site, $_POST['username'], $hour);
					setcookie(Key_my_site, $_POST['pass'], $hour);
				}

				//then redirect them to the members area
				if (isset($_SESSION['request'] )) 
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
		}
	}
	else
	{

	// if they are not logged in
	?>
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
	<?php echo facebook_login_button_refresh("fb_register.php"); ?>
	</div>
	<div class="clear"></div>
	<?php
	}
}

include('footer.php');
?>