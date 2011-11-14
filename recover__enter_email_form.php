<p><span class="errorMessage"><?=$error_message?></span></p>
<p>Enter the email address you registered with Vilfredo.</p>
<p>Please note that if you did not register an email address then you will be unable to reset your password.</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<table border="0">
		<tr>
			<td><?=$VGA_CONTENT['email_label']?></td>
			<td>
				<input type="text" name="email" maxlength="60" value="<?=$_POST['email']?>">
			</td>
		</tr>
		<tr>
			<th colspan=2>
				<input type="submit" name="submit-email" value="Send Password Reset Request">
				<input type="submit" name="cancel-pwd-request" value="Cancel">
			</th>
		</tr>
	</table>
</form>