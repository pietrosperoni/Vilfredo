<?php
include('header.php');
#$userid=isloggedin();
//if ($userid)
//{
	// Check if user has room access.
	if (!HasProposalAccess())
	{
		header("Location: viewquestions.php");
	}
	
	$proposal = $_GET[QUERY_KEY_PROPOSAL];

	$sql = "SELECT  questions.question, questions.roundid, questions.phase, proposals.blurb, proposals.roundid, questions.id, questions.room, questions.title, proposals.abstract FROM proposals, questions WHERE proposals.id = " . $proposal . " and proposals.experimentid = questions.id";

	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$questiontext=$row[0];
		$questionround=$row[1];
		$questionphase=$row[2];
		$proposaltext=$row[3];
		$proposalround=$row[4];
		$questionid=$row[5];
		$room=$row[6];
		$questiontitle=$row[7];
		$proposalabstract=$row[8];
		$urlquery = CreateQuestionURL($questionid, $room);

		echo '<h2 id="question">"<a href="viewquestion.php' . $urlquery . '">' . $questiontitle . '</a>"</h2>';
		echo '<div id="question">' . $questiontext . '</div>';		
		echo 'now on Generation ' . $questionround . '<br>';
		
		if (!empty($proposalabstract))
		{
			echo '<h3>Abstract:</h3>';
			echo "<p> $proposalabstract</p>";
		}
		
		echo '<h3>Proposal:</h3>';
		echo '<p>"' . $proposaltext . '"</p>';
		echo 'active on Generation ' . $proposalround . '<br>';

		if ( $userid and $questionphase==1 and $questionround==$proposalround)
		{
			$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $proposal . " LIMIT 1";
			if(mysql_fetch_array(mysql_query($sql)))
			{
				?>
				<br/><br/>You are endorsing.<br/><br/>
				<?php
			}
			else
			{
				?>
				<br/><br/>You are Ignoring<br/><br/>
				<?php
			}
		}
		if ( $questionround>$proposalround)
		{
			echo "<h3>Endorses</h3>";

			$sql2 = "SELECT  users.username, users.id FROM endorse, users WHERE  endorse.userid = users.id and endorse.proposalid = " . $proposal . " ";
			$response2 = mysql_query($sql2);
			while ($row2 = mysql_fetch_array($response2))
			{
				echo '<li><a href="user.php?u='.$row2[1].'">' . $row2[0] . '</a></li>';
			}
		}
	}
/*
}
else
{
		header("Location: login.php");
}*/

include('footer.php');

?>