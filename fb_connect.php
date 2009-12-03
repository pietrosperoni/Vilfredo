<?php
include('header.php');

	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{
		// makes sure they filled it in
		if(!$_POST['username'] || !$_POST['pass']) 
		{
			die('You did not fill in a required field.');
		}
		// checks it against the database
		
		$username = $_POST['username'];

		$check = mysql_query("SELECT * FROM users WHERE username = '$username'") or die(mysql_error());

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
				// now we insert it into the database
				$userid = $info['id'];
				$fb_userid = $FACEBOOK_ID;
					    
				$sql = "UPDATE users SET fb_userid = '$fb_userid' WHERE id = $userid";
					
				$connect = mysql_query($sql) or die($sql);
			}
		}
		
		$location = getpostloginredirectlink();
?>	
		<h2>OK. Your Facebook account has been connected to Vilfredo.</h2>
		<p><a href="<?php echo $location; ?>">Click to continue</a></p>
<?php
	}
	
else {
?>
<!-- Greet the currently logged-in user! --> 
<?php echo facebook_profile_pic(); ?>
<p>Hello <?php echo facebook_username(); ?>!</p>

<p>Link to existing account or create new ? <a href="fb_register.php">Create new account using Facebook Connect</a></p>
<br/>
<p>
Link to an existing account by entering your username/password below. This will let us connect your Facebook Identity to your 
identity on our site, so that you don't loose any data you've stored here.</p>
<p>You will only have to do this one time.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
Username: <input type="text" name="username"><br/>
Password: <input type="password" name="pass">
<input type="submit" value="submit" name="submit">
</form>

<?php
}
include('footer.php');
?>