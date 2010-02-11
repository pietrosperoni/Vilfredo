<?php
if ($userid)
{
	$sql = "SELECT email FROM users WHERE id = ".$userid." ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	if ($row['email'] == '')
	{
?>	
	<form action="editdetails.php" method="post">
		<table border="0">
			<tr>
				<td>Please insert your email address (<a href="FAQ.php#email">why</a>?):</td>
				<td>
					<input type="text" name="email" maxlength="60">
					<input type="hidden" name="request" value="<?php echo GetRequestString(); ?>"
				</td>
			</tr>
			<tr>
				<th colspan=2>
					<input type="submit" name="submit" value="Update Email">
				</th>
			</tr>
		</table>
	</form>
<?php
	}
}
?>