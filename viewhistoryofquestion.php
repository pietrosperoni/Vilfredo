<?php

$headcommands='
';

include('header.php');
$userid=isloggedin();
if ($userid)
{
	$question = $_GET['q'];

	echo "<h2>Question:</h2>";

	$sql = "SELECT * FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$content=$row[1];
		$generation=$row[2];
		$creatorid=$row[4];
		$title=$row[5];
		$room=$row[9];
		$urlquery = CreateQuestionURL($question, $room);
		echo '<h4 id="question">' . $title . '</h2>';
		echo '<div id="question">' . $content . '</div>';

		$sql2 = "SELECT users.username, users.id FROM questions, users WHERE questions.id = ".$question." and users.id = questions.usercreatorid LIMIT 1 ";
		$response2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_row($response2))
		{
			echo '<p id="author"><cite>asked by <a href="user.php?u=' . $row2[1] . '">'.$row2[0].'</a></cite></p>';
		}

#		echo '<div id="actionbox">';
		echo "<h3>Current Generation: ".$generation." </h3>";
		echo '<a href="http://pareto.ironfire.org/viewquestion.php'.$urlquery.'" >Question Page</a>';
#		echo '</div>';
	}

	echo "<h1>History of past Proposals:</h1>";
#	echo '<quote>"History, teach us nothing": Sting</quote><br /><br />';


	$sql = "SELECT * FROM proposals WHERE experimentid = ".$question." and roundid < ".$generation." ORDER BY `roundid` DESC, `dominatedby` ASC  ";
	$response = mysql_query($sql);
	if ($response)
	{
		echo '<div id="historybox">';
		echo '<table border="1">';
		echo '<tr><td>link</td><td><strong>Proposal</strong></td><td><strong>Author</strong></td><td><strong>Endorsers</strong></td><td><strong>Result</strong></td><td><strong>You</strong></td></tr>';
		$genshowing=$generation;
		while ($row = mysql_fetch_row($response))
		{
			if ($row[3]!=$genshowing)
			{
				$genshowing=$row[3];
				echo '<tr><td colspan="6"><h4> Generation '.$genshowing.'</h4></td></tr>';
				$proposers=AuthorsOfNewProposals($question,$genshowing);
#				echo "Proposers:";
#				foreach ($proposers as $p)
#				{
#					$sql5 = "SELECT username FROM users WHERE id = ".$p." ";
#					$response5 = mysql_query($sql5);
#					$row5 = mysql_fetch_row($response5);
#					echo " ".$row5[0]." ";
#				}
				$endorsers=Endorsers($question,$genshowing);
#				echo "<br />";
#				echo "Endorsers:";
#				foreach ($endorsers as $e)
#				{
#					$sql5 = "SELECT username FROM users WHERE id = ".$e." ";
#					$response5 = mysql_query($sql5);
#					$row5 = mysql_fetch_row($response5);
#					echo " ".$row5[0]." ";
#				}

			}


			$dominatedby=$row[6];
			$source=$row[5];

			$sql6 = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $row[0] . " LIMIT 1";
			if(mysql_fetch_row(mysql_query($sql6)))
			{$Endorsed=1;}
			else
			{$Endorsed=0;}
			echo '<td><a href="viewproposal.php?p='.$row[0].'">link</a></td>';
			echo '<td>' . $row[1] . '</td>';

			echo '<td>';
			if ($row[5])
			{
				echo "<h6>Inherited</h6>";
			}
			else
			{
				echo WriteUserVsReader($row[2],$userid);
				#echo " New";
			}
			echo '</td>';

			$endorsers=EndorsersToAProposal($row[0]);
			echo '<td>';

			foreach ($endorsers as $user)
			{
			echo WriteUserVsReader($user,$userid);
			}
			echo '<a title="The list might not be complete, due to a recent Bug" href="FAQ.php#bugendorsmen"><sup>*</sup></a>';

			echo '&nbsp;</td>';
			echo '<td>';

			if($row[6])
			{
				echo '<img src="images/thumbsdown.jpg" title="The community rejected this proposal" height="45">';
			}
			else
			{
				echo '<img src="images/thumbsup.jpg" title="The community accepted this proposal"  height="48">';
			}
			echo '</td>';
			echo '<td>';
			if($Endorsed)
			{
				echo ' <img src="images/thumbsup.jpg" title="You endorsed this proposal"  height="28">';
			}
			else
			{
				echo ' <img src="images/thumbsdown.jpg" title="You ignored this proposal" height="25">';
			}
			echo '<a title="results are not consistent, due to a recent Bug" href="FAQ.php#bugendorsmen"><sup>*</sup></a>';
			echo '</td>';

			echo '</tr> ';
		}
		echo '</table>';

		echo '</div>';
	}
	// echo "<a href=logout.php>Logout</a>";
}
else
{
		header("Location: login.php");
}

include('footer.php');

?>







