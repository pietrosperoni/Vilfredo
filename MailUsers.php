<?php

$headcommands='';


include('header.php');
#$userid=isloggedin();
if ($userid)
{
	if (isAdmin($userid) == true)
	{
		?>
		<form method="POST" action="mailuserstext.php">
		Subject:<INPUT NAME="subject" COL=70><br/>
		Email Text (write [username] to insert the name of the person):<br/>
		<TEXTAREA NAME="EmailText" ROWS=20 COLS=30></TEXTAREA>
		<table border="1">
		<tr><td><p>Name</p></td><td>Questions</td><td>Proposals</td><td>Votes</td><td>Facebook</td><td>Spammer?</td><td>Send eMail?</td></tr>	
		<?php
		$sql = 'SELECT id, username, active, fb_userid FROM users WHERE email != "" ';
		$response = mysql_query($sql);
		while ($row = mysql_fetch_array($response))
		{
				echo '<tr><td><p>';
				echo WriteUserVsReader($row[0],$userid);
				echo '</p></td>';
				$sqlIn = 'SELECT id FROM questions WHERE usercreatorid ='.$row[0].' ';
				$responseIn = mysql_query($sqlIn);
				if (mysql_num_rows($responseIn)==0)
				{
					echo '<td bgcolor="#FFFF00">';
				}
				else
				{
					echo '<td>';
				}				
				echo mysql_num_rows($responseIn);
				echo '</td>';

				$sqlIn = 'SELECT id FROM proposals WHERE usercreatorid ='.$row[0].' ';
				$responseIn = mysql_query($sqlIn);
				if (mysql_num_rows($responseIn)==0)
				{
					echo '<td bgcolor="#FFFF00">';
				}
				else
				{
					echo '<td>';
				}				
				echo mysql_num_rows($responseIn);
				echo '</td>';


				$sqlIn = 'SELECT id FROM endorse WHERE userid ='.$row[0].' ';
				$responseIn = mysql_query($sqlIn);
				if (mysql_num_rows($responseIn)==0)
				{
					echo '<td bgcolor="#FFFF00">';
				}
				else
				{
					echo '<td>';
				}				
				echo mysql_num_rows($responseIn);
				echo '</td>';
				
				if ($row[3]=="")
				{
					echo '<td bgcolor="#FFFF00">';
				}
				else
				{
					echo '<td bgcolor="#0000FF">';
				}				
				if ($row[3]=="")
				{
					echo "NO";					
				}
				else
				{
					echo "YES";					
				}
				echo '</td>';

				if ($row[2]==0)
				{
					echo '<td bgcolor="#FF0000">';
				}
				else
				{
					echo '<td>';
				}				
				if ($row[2]==0)
				{
					echo "WARNING";					
				}
				else
				{
					echo "no";					
				}
				echo '</td>';

				
				echo '<td><Input type = "Checkbox" Name ="users[]" title="'.$VGA_CONTENT['check2invite_title'] . '" value="'.$row[0].'" /></td></tr>';
		}
		?>
		</tr>
		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td><input type = "Submit" Name = "Submit" title="<?=$VGA_CONTENT['submit2invite_title']?>" VALUE = "<?=$VGA_CONTENT['submit_button']?>"></td>
		</tr></table>
		</form>
		<?php
				
	}
	
        
}
else
{
		DoLogin();
}

include('footer.php');

?>







