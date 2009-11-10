<?php

$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='
<link rel="Stylesheet" type="text/css" href="js/jquery/RichTextEditor/css/jqrte.css" />
<link type="text/css" href="js/jquery/RichTextEditor/css/jqpopup.css" rel="Stylesheet"/>
<link rel="stylesheet" href="js/jquery/RichTextEditor/css/jqcp.css" type="text/css"/>

<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>';


include('header.php');
$userid=isloggedin();
if ($userid)
{
	// Check if user has room access.
	if (!HasQuestionAccess())
	{
		header("Location: index.php");
	}

	$question = $_GET[QUERY_KEY_QUESTION];

	$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";

	$sql = "SELECT * FROM updates WHERE question = ".$question." AND  user = ".$userid." LIMIT 1 ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	if ($row)
	{
		echo "Automatic Email Update On: You will receive an email every time the question moves on, even when you do not participate.";
	}
	else
	{
		echo "Automatic Email Update Off: You will only receive an update from the question if you have participated recently.";
	}
	?>
	<form method="POST" action="changeupdate.php">
		<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
		<input type="hidden" name="room" id="room" value="<?php echo $room; ?>" />
		<input type="submit" name="submit" id="submit" value="Switch the update On/Off" />
	</form>
	<?php
	$sql = "SELECT * FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$content=$row[1];
		$phase=$row[3];
		$generation=$row[2];
		$creatorid=$row[4];
		$title=$row[5];
		$lastmoveonTime=TimeLastProposalOrEndorsement($question, $phase, $generation);
		if (!$lastmoveonTime)
		{
			$lastmoveonTime=strtotime( $row[6] );
		}
		$minimumtime= $row[7] ;

		$timeelapsed=time()-$lastmoveonTime;
		if ($timeelapsed>=$minimumtime)
		{
			$tomoveon=1;
		}
		else
		{
			$tomoveon=0;
		}
		$dayselapsed=(int)($timeelapsed/(60*60*24));
		$timeelapsed=$timeelapsed-$dayselapsed*60*60*24;
		$hourseselapsed=(int)($timeelapsed/(60*60));
		$timeelapsed=$timeelapsed-$hourseselapsed*60*60;
		$minuteselapsed=(int)($timeelapsed/60);


		$minimumdays=(int)($minimumtime/(60*60*24));
		$minimumtime=$minimumtime-$minimumdays*60*60*24;
		$minimumhours=(int)($minimumtime/(60*60));
		$minimumtime=$minimumtime-$minimumhours*60*60;
		$minimumminutes=(int)($minimumtime/60);


		echo '<h2 id="question">' . $title . '</h2>';
		echo '<div id="question">' . $content . '</div>';

		$sql2 = "SELECT users.username, users.id FROM questions, users WHERE questions.id = ".$question." and users.id = questions.usercreatorid LIMIT 1 ";
		$response2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_row($response2))
		{
			echo '<p id="author"><cite>asked by <a href="user.php?u=' . $row2[1] . '">'.$row2[0].'</a></cite></p>';
		}

		if (($phase==0) && ($generation>1))
		{
			echo '<div id="paretofrontbox">';
			echo "<h3>Alternative proposals, that emerged from last endorsing round</h3>";
			echo "<p><i>The different proposals do not represent the best proposals, nor the more popular.
Strictly speaking they are the pareto front of the set of proposals.
You can think of it as the smallest set of proposals such that every participant is represented.</i></p>

";
			$ParetoFront=ParetoFront($question,$generation-1);
			foreach ($ParetoFront as $p)
			{
				$sql3 = "SELECT blurb FROM proposals WHERE id = ".$p." LIMIT 1 ";
				$response3 = mysql_query($sql3);
				while ($row3 = mysql_fetch_row($response3))
				{
					echo '<p class="paretoproposal">' . $row3[0] ;


					$sql4 = "SELECT  users.username, users.id FROM endorse, users WHERE  endorse.userid = users.id and endorse.proposalid = " .$p. " ";
					$response4 = mysql_query($sql4);
					echo '<br>Endorsed by: ';
					while ($row4 = mysql_fetch_row($response4))
					{
						echo '<a href="user.php?u='.$row4[1].'">' . $row4[0] . '</a> ';
					}
					echo '</br>';
					echo '</p>';


				}
			}

			echo '</div>';
		}

		echo '<div id="actionbox">';
		echo "<h3>Generation ".$generation.": ";
		if ( $phase==0)
		{
			echo "Phase: Writing New Proposals</h3>";
			if ($generation==1)
			{
				echo "<p><i>What should you do now?
You should now answer the question. Giving your best shot. Later, after everybody has written their answer, you will all be given the possibility to endorse each other question, and try to find common denominators.</i></p>
";
			}
			else
			{
					echo "<p><i>What should you do now? Write new proposals. How?
You can insert brand new ideas;
rewrite previous ideas (maybe trying to explain them better);
recover old ideas from the history of the question;
try to write a proposal that represent an acceptable compromise between different winning proposals. If you do this well, the new proposal will be endorsed by both the proponent of the first and of the second proposal, and you will have effectively joined those proposals.</i></p>
";
			}

	$NProposals=CountProposals($question,$generation);
	echo "<p>Number of authors of new proposals: ".count(AuthorsOfNewProposals($question,$generation))."</p>";
	echo "<p>Number of proposals written so far: ".$NProposals."</p>";
#	echo "<p>Number of proposals written by you: ".."</p>";
#	echo "<p>Number of proposals inherited from the past geeration: ".."</p>";
#	echo "<p>Number of new proposals written by others: ".."</p>";




		}
		if ( $phase==1)
		{
			echo "Phase: Evaluating Existing Proposals</h3>";
//			echo "<i>The list of proposals that follow should <br/>(a) give the possibility to endorse all of them, and <br/>(b)be all and only the proposals of this generation plus the winning proposals of the previous generation</i><br/><br/>";
			echo "<p>Please click on ALL the answers of the question that you agree with.</p>";

		}
	}


	if ( $phase==0 and CountProposals($question,$generation) and $userid==$creatorid and $tomoveon==1)
	{


		?>
		<form method="POST" action="moveontoendorse.php">
		If everybody has written their proposals, you can:
			<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
			<input type="submit" name="submit" id="submit" value="Move On to the Next Phase" />
		</form>
		<?php
	}
	if ( $phase==1)
	{
		$nEndorsers=CountEndorsers($question,$generation);
		echo "<p>Number of people who have endorsed at least one proposal: ".$nEndorsers."</p>";
		echo "<p>Time passed since first endorsement: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: ";
		if ($minimumdays){ echo $minimumdays." days ";}
		if ($minimumhours){ echo $minimumhours." hours ";}
		if ($minimumminutes){ echo $minimumminutes." minutes ";}
		echo "must have passed between the first endorsement and the moment when the questioner can move the question on. </p>";

		if ($nEndorsers and $userid==$creatorid and $tomoveon==1)
		{
			?>
			<form method="POST" action="moveontowriting.php">
			If everybody has endorsed the proposals they wanted to endorse, you can:
				<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="submit" name="submit" id="submit" value="Move On to the Next Phase" />
			</form>
			<?php
		}
	}

	if ( $phase==0)
	{
		?>
			<h2>Propose an answer</h2>
		<?php	#echo "<p>Time since last moveon: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: 1 day need to pass between one moveon and the next</p>";
		echo "<p>Time since first proposal on this generation: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: ";
		if ($minimumdays)
		{ 			echo $minimumdays." days ";}
		if ($minimumhours)		{			echo $minimumhours." hours ";		}
		if ($minimumminutes)		{			echo $minimumminutes." minutes ";		}
		echo "must have passed between the first proposal and the moment when the questioner can move the question on.</p>";

?>
			<form method="POST" action="newproposaltake.php">
      <textarea id="content" name="blurb" class="jqrte_popup" rows="500" cols="70"></textarea>
      <?php
         include_once("js/jquery/RichTextEditor/content_editor_proposal.php");
         include_once("js/jquery/RichTextEditor/editor.php");
      ?>
      			<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="submit" name="submit" id="submit" value="Create proposal" />
			</form>
<script type="text/javascript">
   window.onload = function(){
      try{
         $("#content_rte").jqrte();
      }
      catch(e){}
   }

   $(document).ready(function() {
         $("#content_rte").jqrte_setIcon();
         $("#content_rte").jqrte_setContent();
   });
</script>
		<?php

		$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  and roundid = ".$generation." and usercreatorid = ".$userid." and source = 0 ORDER BY `id` DESC  ";
		$response = mysql_query($sql);
		if ($response)
		{
			echo "<h3>Proposals You have written:</h3>";
			echo '<table border="1">';
			while ($row = mysql_fetch_row($response))
			{
				?>
						<tr>
						<td><p><?php echo $row[1];?></p>
						</td>
						<td>
							<form method="POST" action="deleteproposal.php">
								<input type="hidden" name="p" id="p" value="<?php echo $row[0]; ?>" />
								<input type="submit" name="submit" id="submit" value="Edit or Delete" />
							</form>
						</td>
						</tr>
				<?php
			}
			echo '</table>';

		}
		else
		{
			echo "Sorry no proposals yet";
		}
	}


	if ( $phase==1)
	{
		$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  and roundid = ".$generation."  ORDER BY `id` DESC  ";
		//they should be randomly sorted!
		$response = mysql_query($sql);
		if ($response)
		{
			echo "<h3>Proposals:</h3>";
			?>
			<form method="POST" action="endorse_or_not.php">
					<input type="hidden" name="question" value="<?php echo $question; ?>" />
			<table border="1">
			<tr><td><h4>Proposed Solution</h4></td><td><b>Check all the ones you endorse</b></td></tr>

			<?php

			while ($row = mysql_fetch_row($response))
			{
				echo '<tr>';
				#echo '<td><p><a href="viewproposal.php?p='.$row[0].'">link</a></td><td>';
				echo '<td><p>' . $row[1] . '</td><td>';
				echo '<Input type = "Checkbox" Name ="proposal[]" title="Check this box if you endorse the proposal" value="'.$row[0].'"';

				$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $row[0] . "  LIMIT 1";
				if(mysql_fetch_row(mysql_query($sql)))
				{
					echo ' checked="checked" ';
				}

				echo ' /></p> </td></tr>';
			}
			?>
			</tr>
			<tr><td></td><td><input type = "Submit" Name = "Submit" title="Votes are not counted unless submitted." VALUE = "Submit!"></td>
			</tr></table>
			</form>
			<?php

		}
		else
		{
			echo "Sorry no proposals yet";
		}
	}

	echo '</div>';
	if ($generation>1)
	{
		echo '<div>';
		echo '<a href="viewhistoryofquestion.php?q=' . $question . '">View History of The Question</a>. Here you can see who voted for what, what proposals were eliminated. You can recover past proposals that you think should not be lost. Maybe explaining them better.';
		echo '</div>';
	}

	// echo "<a href=logout.php>Logout</a>";
}
else
{
		DoLogin();
}

include('footer.php');

?>
