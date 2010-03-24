<?php
include('header.php');
#$userid=isloggedin();
if ($userid)
{
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{
		if ($_POST['email'] ) 
		{
			$emailstr = strip_tags($_POST['email']);
			
			$EmailIsValid=validEmail($emailstr);
			if (!$EmailIsValid)
			{
				die('The Email address you inserted is not a valid email address.');
			}
		
			if (!get_magic_quotes_gpc()) 
			{
				$emailstr = addslashes($emailstr);
			}
			$email=$emailstr;
			$sql = "UPDATE `users` SET `email` = '".$email."'  WHERE `users`.`id` = ".$userid." ";
			mysql_query($sql);
		}
		
		if (isset($_POST['request']) && empty($_POST['request']) == false)
		{
			$location = $_POST['request'];
		}

		$userinfo = getuserdetails($userid);
		?>
		<h1>email modified</h1>
		<p><?php echo $userinfo['email']; ?></p>
		<?php
		
		if (!empty($location))
		echo '<p><a href="'.$location.'">Click to continue</a></p>';
		?>
		
		<?php
	}
	else
	{
		$userinfo = getuserdetails($userid);
		?>
		<p>Your current email : <?php echo $userinfo['email'];?></p>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<table border="0">
				<tr>
					<td>New Email:</td>
					<td>
						<input type="text" name="email" maxlength="60">
					</td>
				</tr>
				<tr>
					<th colspan=2>
						<input type="submit" name="submit" value="Update Email">
					</th>
				</tr>
			</table>
		</form>
		<p><b>Note:</b> the email is needed to receive updates on the questions you are working on. <br/>But you will be able to chose, for each question, if you want to be updated.</p> 
		<?php
	}
}
else
{
		header("Location: login.php");
}

include('footer.php');
?> 