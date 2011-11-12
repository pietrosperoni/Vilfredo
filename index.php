<?php
header("Location: viewquestions.php");
exit;

include('header.php');

#$userid=isloggedin();
if ($userid)
{
?>

<div id="recentproposal">

	<h2>Recent Questions</h2>
	<p>Please add your answer</p>

<?php

	// **
	// Set room access filter
	// **
	$room_access = GetRoomAccessFilter($userid);
	
	// Get Recent Questions
	//
	$sql = "SELECT questions.id, questions.question, questions.roundid, questions.phase, users.username, users.id, questions.room FROM questions, users WHERE questions.phase = 0 AND users.id = questions.usercreatorid " . $room_access . " ORDER BY questions.id DESC LIMIT 4";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$phase=$row[3];
		$generation=$row[2];
		$thatusername=$row[4];
		$thatuserid=$row[5];
		$proposalsid=$row[6];
		$questionid=$row[0];
		$room=$row[6];
		$urlquery = CreateQuestionURL($questionid, $room);

		if ($phase)
		{
		}
		else
		{
			$sql2 = "SELECT id  FROM proposals WHERE experimentid = ".$questionid." ";
			$response2 = mysql_query($sql2);
			$nrecentproposals=CountProposals($questionid,$generation-1);
			$nrecentparetofront=count(ParetoFront($questionid,$generation-1));
			$nAuthorsNewProposals=count(AuthorsOfNewProposals($questionid,$generation));
			$nactualproposals=CountProposals($questionid,$generation);

			if($nactualproposals==0)
			{
				echo '<p>';


				#echo '('.$generation.') <img src="images/writing.jpg" height=32> ('.$nAuthorsNewProposals.':'.$nactualproposals.')('.$nrecentparetofront.'/'.$nrecentproposals.')"<a href="viewquestion.php' . $urlquery . '">' . $row[1] . '</a>" by <a href="user.php?u=' . $thatuserid . '">'.$thatusername.'</a>.';
				echo ' "<a href="viewquestion.php' . $urlquery . '">' . $row[1] . '</a>" by <a href="user.php?u=' . $thatuserid . '">'.$thatusername.'</a>.';

				echo '</p>';
			}
		}
	}

	$sql = "SELECT questions.id, questions.title, questions.roundid, questions.phase, users.username, users.id, MAX(proposals.id) AS latest, questions.room FROM questions, users, proposals WHERE questions.phase = 0 AND users.id = questions.usercreatorid AND proposals.experimentid = questions.id " . $room_access . " GROUP BY questions.id ORDER BY latest DESC LIMIT 5";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		echo '<p>';
		$phase=$row[3];
		$generation=$row[2];
		$thatusername=$row[4];
		$thatuserid=$row[5];
		$proposalsid=$row[6];
		$questionid=$row[0];
		$room=$row[7];
		$urlquery = CreateQuestionURL($questionid, $room);

		if ($phase)
		{
		}
		else
		{
			$sql2 = "SELECT id  FROM proposals WHERE experimentid = ".$questionid." ";
			$response2 = mysql_query($sql2);
			$nrecentproposals=CountProposals($questionid,$generation-1);
			$nEndorsersRecentProposals=CountEndorsers($questionid,$generation-1);
			$nrecentparetofront=count(ParetoFront($questionid,$generation-1));
			$nAuthorsNewProposals=count(AuthorsOfNewProposals($questionid,$generation));
			$nAuthorsRecentProposals=count(AuthorsOfNewProposals($questionid,$generation-1));
			$nactualproposals=CountProposals($questionid,$generation);

			echo '"<a href="viewquestion.php' . $urlquery . '">' . $row[1] . '</a>" by <a href="user.php?u=' . $thatuserid . '">'.$thatusername.'</a>.';

		}

		echo '</p>';
	}
	echo '</div>';
?>


</div><div id="recentvoting">
	<h2>Recent Endorsing</h2>
	<p>Please endorse the answers you agree with</p>

<?php
		// Get Recent Endorsments
		//
		$sql = "SELECT questions.id, questions.title, questions.roundid, questions.phase, users.username, users.id, questions.room FROM questions, users WHERE questions.phase = 1 AND users.id = questions.usercreatorid " . $room_access . " ORDER BY questions.roundid DESC, questions.phase DESC, questions.id DESC LIMIT 6";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		echo '<p>';
		$phase=$row[3];
		$thatusername=$row[4];
		$thatuserid=$row[5];
		$room=$row[6];
		$urlquery = CreateQuestionURL($questionid, $room);

			echo '"<a href="viewquestion.php' . $urlquery . '">' . $row[1] . '</a>" by <a href="user.php?u=' . $thatuserid . '">'.$thatusername.'</a>.';

		echo '</p>';

	}

	$sql = "SELECT questions.id, questions.question, questions.roundid, questions.phase, users.username, users.id, MAX(proposals.id) AS latest, questions.room FROM questions, users, proposals WHERE questions.phase = 1 AND users.id = questions.usercreatorid AND proposals.experimentid = questions.id GROUP BY questions.id ORDER BY latest DESC LIMIT 4";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		echo '<p>';
		$phase=$row[3];
		$generation=$row[2];
		$thatusername=$row[4];
		$thatuserid=$row[5];
		$proposalsid=$row[6];
		$questionid=$row[0];
		$room=$row[7];
		$urlquery = CreateQuestionURL($questionid, $room);

		if ($phase)
		{
		}
		else
		{
			$sql2 = "SELECT id  FROM proposals WHERE experimentid = ".$questionid." ";
			$response2 = mysql_query($sql2);
			$nrecentproposals=CountProposals($questionid,$generation-1);
			$nEndorsersRecentProposals=CountEndorsers($questionid,$generation-1);
			$nrecentparetofront=count(ParetoFront($questionid,$generation-1));
			$nAuthorsNewProposals=count(AuthorsOfNewProposals($questionid,$generation));
			$nAuthorsRecentProposals=count(AuthorsOfNewProposals($questionid,$generation-1));
			$nactualproposals=CountProposals($questionid,$generation);

			echo '"<a href="viewquestion.php' . $urlquery . '">' . $row[1] . '</a>" by <a href="user.php?u=' . $thatuserid . '">'.$thatusername.'</a>.';

		}

		echo '</p>';
	}
	echo '</div>';
?>


</div>

<h2>Online democracy</h2>

<p>You can use this system to make decisions amongst groups of people. </p>


		<div class="endorsingbox2">
			<h2><a href="newquestion.php">Ask Your Question</a></h2>
		</div>




<!--
<blockquote>

<p>Pareto efficiency, or Pareto optimality, is an important concept in economics with broad applications in game theory, engineering and the social sciences. The term is named after Vilfredo Pareto, an Italian economist who used the concept in his studies of economic efficiency and income distribution.</p>

<p>Given a set of alternative allocations of, say, goods or income for a set of individuals, a change from one allocation to another that can make at least one individual better off without making any other individual worse off is called a Pareto improvement. An allocation is Pareto efficient or Pareto optimal when no further Pareto improvements can be made. This is often called a strong Pareto optimum (SPO).</p>

<p>Given a set of choices and a way of valuing them, the Pareto frontier or Pareto set is the set of choices that are Pareto efficient.</p>
</blockquote>
-->
<!--
<h2>Latin</h2>
<p><b></b></p>
<p></p>

<p>Aliquam pretium. Nam lorem. Morbi adipiscing venenatis tellus. Donec quis urna porta lectus fringilla bibendum. Maecenas vitae felis. Cras tristique blandit nisl. Nam fringilla, nisl vitae gravida luctus, lacus nisi placerat urna, sed volutpat purus libero tempus erat. Suspendisse ligula. Praesent sed leo. Ut et felis et purus iaculis posuere. Curabitur sit amet mi. Morbi pellentesque ullamcorper libero. Ut fringilla eros. Phasellus in sapien. Mauris et erat a ipsum placerat pulvinar. Vestibulum eu est vitae urna auctor adipiscing.</p>

<p>Curabitur consectetur. Nullam et arcu. Aliquam rhoncus imperdiet nisi. Vestibulum enim eros, porta ac, rhoncus sed, sollicitudin nec, ligula. Nullam vel mi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed quis ipsum quis elit fringilla condimentum. Nunc leo ante, iaculis a, condimentum in, venenatis ac, dolor. Donec sit amet metus. Maecenas lobortis pellentesque eros. Nulla commodo urna vel libero. Phasellus rutrum erat eu leo. Sed pulvinar, nunc sit amet consequat bibendum, dolor lectus aliquet nunc, eu pulvinar orci orci vitae felis. Integer hendrerit, felis nec dapibus viverra, purus enim venenatis sem, a tincidunt lorem felis in justo. Etiam porttitor.</p>

<p>Proin tempor dui. Nam nisl eros, faucibus vitae, pharetra et, sodales sed, turpis. Morbi sagittis erat id est. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse potenti. Nunc ac orci non eros eleifend malesuada. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In augue eros, interdum eu, rutrum viverra, ultricies sed, sapien. Suspendisse tincidunt interdum leo. In non ante. Sed suscipit enim quis sem. Maecenas varius posuere lorem. Fusce dapibus dictum dolor. Aenean mollis purus et dui.</p>

<p>Etiam consequat. Vestibulum ullamcorper nulla sed ligula. Nam vel lacus quis massa volutpat interdum. Phasellus in justo. Suspendisse aliquet rhoncus ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi placerat. In mattis dictum metus. Vestibulum nibh metus, sodales a, facilisis non, vestibulum vel, augue. Vestibulum dui. Integer sed risus. Quisque id nunc ut velit rhoncus rutrum. Donec ultricies pharetra diam.</p>
-->

<?php



}
else
{
		header("Location: login.php");
}

include('footer.php');

?>