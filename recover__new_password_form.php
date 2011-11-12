<!-- enter new pwd -->
<h2>Password Reset</h2>
<p><span class="errorMessage"><?=$error_message?></span></p>
<?php if (isset($_SESSION['recoveruser'])) : ?>
<p>Hello <span class="user"><?=$_SESSION['recoveruser']?></span></p>
<?php endif; ?>
<p>To complete your password reset, enter your new password below.</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<table border="0">
		<tr>
			<td><?=$VGA_CONTENT['password_label']?></td>
			<td>
				<input type="password" name="pass">
			</td>
		</tr>
		<tr>
			<td><?=$VGA_CONTENT['pass_conf_label']?></td>
			<td>
				<input type="password" name="pass2">
			</td>
		</tr>
		<tr>
			<th colspan=2>
				<input type="submit" name="submit-pwd" value="Submit New Password">
			</th>
		</tr>
	</table>
</form>