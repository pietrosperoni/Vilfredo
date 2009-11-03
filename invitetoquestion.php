<?php

$headcommands='';


include('header.php');
$userid=isloggedin();
if ($userid)
{
	$question = $_GET[QUERY_KEY_QUESTION];
	$room = $_GET[QUERY_KEY_ROOM];

	$sql = 'SELECT id, username FROM users WHERE email != "" ';
	$response = mysql_query($sql);
        
        if (mysql_num_rows($response) > 0)
	{

		echo "<h2>Invite Users to answer your question:</h2>";

		$sql2 = 'SELECT questions.title FROM questions WHERE questions.id = '.$question;
		$response2 = mysql_query($sql2);
		$row2 = mysql_fetch_row($response2);
		echo "<h3>".$row2[0]."</h3>";

		echo "<p>Now that you have created a question, you can invite some users to answer it</p>";
		echo "<h3>Users:</h3>";
			?>
			<form method="POST" action="inviteuserstoquestion.php">
                        <input type="hidden" name="question" value="<?php echo $question; ?>" />
                        <input type="hidden" name="room" value="<?php echo $room; ?>" />
			<table border="1">
			<?php

			while ($row = mysql_fetch_row($response))
			{
				echo '<tr><td><p>';
				echo WriteUserVsReader($row[0],$userid);
				echo '</td><td>';
				echo '<Input type = "Checkbox" Name ="users[]" title="Check this box if you want to invite this user" value="'.$row[0].'" /></td></tr>';
			}
			?>
			</tr>
			<tr><td></td><td></td><td></td><td><input type = "Submit" Name = "Submit" title="Votes are not counted unless submitted." VALUE = "Submit!"></td>
			</tr></table>
			</form>
			<?php

	}
	echo "<a href=logout.php>Logout</a>";
}
else
{
		header("Location: login.php");
}

include('footer.php');

?>







