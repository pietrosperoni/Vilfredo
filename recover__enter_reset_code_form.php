<!-- enter pwd reset code -->
<p><span class="errorMessage"><?=$error_message?></span></p>
<h3>Recover Password</h3>
<p>Please enter the 10 character reset code we emailed to you.</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<table border="0">
		<tr>
			<td>Reset Code</td>
			<td>
				<input type="text" name="reset-code" maxlength="10">
			</td>
		</tr>
		<tr>
			<td colspan=2 class="form_buttons">
				<input type="submit" name="submit-code" value="Submit Code">
			</td>
		</tr>
	</table>
</form>