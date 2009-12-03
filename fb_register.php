<?php
include('header.php');

#if (isloggedin())
if ($userid)
{
	$redirect = postloginredirect();
	header("Location: " . $redirect);
}
else
{
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{

		//This makes sure they did not leave any fields blank
		if (!$_POST['username']) 
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

		if (!get_magic_quotes_gpc()) 
		{
			$_POST['username'] = addslashes($_POST['username']);
			$_POST['email'] = addslashes($_POST['email']);
		}

		// now we insert it into the database
		$insert = "INSERT INTO users (username, email, fb_userid) VALUES ('".$_POST['username']."', '".$_POST['email']."', '".$FACEBOOK_ID."')";
		$add_member = mysql_query($insert);
		
		//then redirect them to the members area
		$location = "viewquestions.php";
		if (isset($_SESSION['request'] )) 
		{
			// Now send the user to his desired page
			$location = $_SESSION['request'];
			unset($_SESSION['request']);
		}
		?>

		<h1>Registered</h1>
		<p>Thank you, you have registered - you may now use Vilfredo!</p>
		<p><a href="<?php echo $location; ?>">Click to continue</a></p>
		<?php
	}
	else
	{
		?>
		<!-- Greet the currently logged-in user! --> 
		<?php echo facebook_profile_pic(); ?>
		<p>Hello <?php echo facebook_username(); ?>!</p>
		
		<p><strong>Already have a Vilfredo account?</strong> <a href="fb_connect.php">Click here to connect your account to Facebook.</a></p>
		
		<p>Otherwise, please select a username.</p>
		
		<?php
		$firstName = "";
		if ($FACEBOOK_ID)
		{
			$user_details=$fb->api_client->users_getInfo($FACEBOOK_ID, array('first_name'));  
			$firstName=$user_details[0]['first_name'];
		}

		?>
		
		
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<table border="0">
		<tr>
			<td>Username: (note this will be published on the site, so don't use your email address, unless you want that to be public and potentially taken by spiders)</td>
			<td>
				<input type="text" value="<?php echo $firstName; ?>" name="username" maxlength="60">
			</td>
		</tr>
		<tr>
			<td>Email: (optional)</td>
			<td>
				<input type="text" name="email" maxlength="60">
			</td>
		</tr>
		<tr>
			<th colspan=2>
				<input type="submit" name="submit" value="Register">
			</th>
		</tr>
	</table>
</form>
		<p><b>Note:</b> the email is needed to receive updates on the questions you are working on. <br/>But you will be able to chose, for each question, if you want to be updated. You may add this later if you wish.</p> 
		<?php
	}
}

include('footer.php');
?> 