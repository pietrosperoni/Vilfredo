<?php
include('header.php');

#if (isloggedin())
if ($userid)
{
	echo "You have already registered, and are logged in. Would you like to <a href=logout.php>Logout</a>?<p>";
}
else
{
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{

		//This makes sure they did not leave any fields blank
		if (!$_POST['username'] | !$_POST['pass'] | !$_POST['pass2'] ) 
		{
			die('You did not complete all of the required fields');
		}
		
		$UsernameIsEmail=validEmail($_POST['username']);
		if ($UsernameIsEmail)
		{
			die('Your username will be public and as such it cannot be an email address (for security reasons)');
		}

		if ($_POST['email'] ) 
		{
			$EmailIsValid=validEmail($_POST['email']);
			if (!$EmailIsValid)
			{
				die('The Email address you inserted is not a valid email address.');
			}
		}


		if (!$_POST['username'] | !$_POST['pass'] | !$_POST['pass2'] ) 
		{
			die('You did not complete all of the required fields');
		}

		// checks if the username is in use
		if (!get_magic_quotes_gpc()) 
		{
			$_POST['username'] = addslashes($_POST['username']);
		}
		$usercheck = $_POST['username'];
		$check = mysql_query("SELECT username FROM users WHERE username = '$usercheck'") or die(mysql_error());
		$check2 = mysql_num_rows($check);

		//if the name exists it gives an error
		if ($check2 != 0) 
		{
			die('Sorry, the username '.$_POST['username'].' is already in use.');
		}

		// this makes sure both passwords entered match
		if ($_POST['pass'] != $_POST['pass2']) 
		{
			die('Your passwords did not match. ');
		}

		// here we encrypt the password and add slashes if needed
		$_POST['pass'] = md5($_POST['pass']);
		if (!get_magic_quotes_gpc()) 
		{
			$_POST['pass'] = addslashes($_POST['pass']);
			$_POST['username'] = addslashes($_POST['username']);
			$_POST['email'] = addslashes($_POST['email']);
		}

		// now we insert it into the database
		$insert = "INSERT INTO users (username, password, email) VALUES ('".$_POST['username']."', '".$_POST['pass']."', '".$_POST['email']."')";//'
		$add_member = mysql_query($insert);
		?>

		<h1>Registered</h1>
		<p>Thank you, you have registered - you may now login.</p>
		<?php
	}
	else
	{
		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table border="0">
				<tr>
					<td>Username: (note this will be published on the site, so don't use your email address, unless you want that to be public and potentially taken by spiders)</td>
					<td>
						<input type="text" name="username" maxlength="60">
					</td>
				</tr>
				<tr>
					<td>Email:</td>
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