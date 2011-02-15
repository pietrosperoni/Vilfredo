<?php

$headcommands='';


include('header.php');
#$userid=isloggedin();
if ($userid)
{
	$question = $_GET[QUERY_KEY_QUESTION];
	$room = $_GET[QUERY_KEY_ROOM];

	$sql = 'SELECT id, username FROM users WHERE email != "" ';
	$response = mysql_query($sql);
        
    if (mysql_num_rows($response) > 0)
	{

		echo "<h2>{$VGA_CONTENT['invite_users_txt']}</h2>";
		echo "<h3>" . $VGA_CONTENT['inviteusers_exp_txt'] . "</h3><br />";

		$sql2 = 'SELECT questions.title FROM questions WHERE questions.id = '.$question;
		$response2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($response2);
		echo "<h3>".$row2[0]."</h3>";

		echo "<p>{$VGA_CONTENT['invite_msg_txt']}</p>";
		echo "<h3>{$VGA_CONTENT['users_txt']}</h3>";
			?>
			<form method="POST" action="inviteuserstoquestion.php">
                        <input type="hidden" name="question" value="<?php echo $question; ?>" />
                        <input type="hidden" name="room" value="<?php echo $room; ?>" />
			<table border="1">
			<?php

			$RoomsI=RoomsUsed($userid);

			while ($row = mysql_fetch_array($response))
			{
				$RoomsThee=RoomsUsed($row[0]);
				$WhereHaveWeMet=array();
				$WhereHaveWeMet=WhereHaveWeMet($RoomsI,$RoomsThee);
				if ($WhereHaveWeMet) #	if (1)
				{
					echo '<tr><td><p>';
					echo WriteUserVsReader($row[0],$userid);
					echo '</p></td><td>';
					echo '<Input type = "Checkbox" Name ="users[]" title="'.$VGA_CONTENT['check2invite_title'] . '" value="'.$row[0].'" /></td></tr>';
				}
			}
			?>
			</tr>
			<tr><td></td><td></td><td></td><td><input type = "Submit" Name = "Submit" title="<?=$VGA_CONTENT['submit2invite_title']?>" VALUE = "<?=$VGA_CONTENT['submit_button']?>"></td>
			</tr></table>
			</form>
			<?php

	}
	echo "<a href=logout.php>Logout</a>";
}
else
{
		DoLogin();
}

include('footer.php');

?>







