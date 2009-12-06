<?php
include('header.php');
$error_message = "";
$connected = false;
//This code runs if the form has been submitted
if (isset($_POST['submit'])) 
{
	$connected = fb_connect_user();
	$m = get_messages();
	$error_message = $m['error'][0];
}

if ($connected)
{
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
<p><span class="errorMessage"><?php echo $error_message; ?></span></p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
Username: <input type="text" name="username"><br/>
Password: <input type="password" name="pass">
<input type="submit" value="submit" name="submit">
</form>
<?php
}

include('footer.php');
?>