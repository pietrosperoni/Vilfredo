<?php
include('header.php');

#if (isloggedin())
if ($userid)
{
	echo "You have already registered, and are logged in. Would you like to <a href=logout.php>Logout</a>?<p>";
}
else
{
	$registered = false;
	$error_message = "";
	
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{
		$registered = register_user();
		$m = get_messages();
		$error_message = $m['error'][0];
	}
	
	if ($registered)
	{
		?>

		<h1>Registered</h1>
		<p>Thank you, you have registered - you may now <a href="login.php">login</a></p>
		<?php
	}
	else
	{
		?>
		<p><span class="errorMessage"><?php echo $error_message; ?></span></p>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table border="0">
				<tr>
					<td>Username: (note this will be published on the site, so don't use your email address, unless you want that to be public and potentially taken by spiders)</td>
					<td>
						<input type="text" name="username" maxlength="60">
					</td>
				</tr>
				<tr>
					<td>Email: (Optional)</td>
					<td>
						<input type="text" name="email" maxlength="60">
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>
						<input type="password" name="pass" maxlength="10">
					</td>
				</tr>
				<tr>
					<td>Confirm Password:</td>
					<td>
						<input type="password" name="pass2" maxlength="10">
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