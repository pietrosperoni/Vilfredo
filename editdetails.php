<?php

/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || 
 ↪checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}



include('header.php');

if ($userid)
{
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{

		if ($_POST['email'] ) 
		{
		
			$EmailIsValid=validEmail($_POST['email']);
			if (!$EmailIsValid)
			{
				die('The Email address you inserted is not a valid email address.');
			}
		
			if (!get_magic_quotes_gpc()) 
			{
				$_POST['email'] = addslashes($_POST['email']);
			}
			$email=$_POST['email'];
			$sql = "UPDATE `users` SET `email` = '".$email."'  WHERE `users`.`id` = ".$userid." ";
			mysql_query($sql);
		}
		?>
		<h1>email modified</h1>
		<?php
	}
	else
	{
		?>
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