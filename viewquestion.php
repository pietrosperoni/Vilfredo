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
<link type="text/css" href="css/theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery/retweet.js"></script>
<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>
	<script type="text/javascript">
	$(function() {
		$("#abstract_panel").accordion({
			collapsible: true,
			autoHeight: false,
			active: false
		});

		$(".expandbtn").click(function () {
		     var fulltxt = $(this).siblings("div.paretoabstract");
		      if (fulltxt.is(":hidden")) {
		        fulltxt.slideDown("slow");
		        $(this).text("Hide Full Text");
		      } else {
		        fulltxt.slideUp("slow");
		        $(this).text("Show Full Text");
		      }
		    });

	});
	</script>';


include('header.php');

if ($userid)
{
	// Check if user has room access.
	if (!HasQuestionAccess())
	{
		header("Location: viewquestions.php");
	}

	$question = $_GET[QUERY_KEY_QUESTION];

	$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";

	$sql = "SELECT * FROM updates WHERE question = ".$question." AND  user = ".$userid." LIMIT 1 ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	if ($row)
		{$subscribed=1;}
	else
		{$subscribed=0;}

	$sql = "SELECT * FROM questions WHERE id = $question";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$content=$row['question'];
		$phase=$row['phase'];
		$generation=$row['roundid'];
		$creatorid=$row['usercreatorid'];
		$title=$row['title'];
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

		?>
			<h2 id="question">
			<form method="POST" action="changeupdate.php">
				<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="hidden" name="room" id="room" value="<?php echo $room; ?>" />
			<?php
			echo  $title;
			if ($subscribed==1)
			{
				?> <input type="submit" name="submit" id="submit" value="email Unsubscribe" /> <?php
			}else{
				?> <input type="submit" name="submit" id="submit" value="email Subscribe" /> <?php
			}
			?>
			</form>
			</h2>
		<?php


		echo '<div id="question">' . $content . '</div>';

		$sql2 = "SELECT users.username, users.id FROM questions, users WHERE questions.id = ".$question." and users.id = questions.usercreatorid LIMIT 1 ";
		$response2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_array($response2))
		{
			echo '<p id="author"><cite>asked by <a href="user.php?u=' . $row2[1] . '">'.$row2[0].'</a></cite></p>';
			
			// Add retweet button
			$twitter_title = htmlentities($title);
			$loc = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			echo "<p><a class=\"retweet\" href=\"$loc\" title=\"RT @Vg2A $twitter_title\"></a></p>";
			
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
				$sql3 = "SELECT blurb, abstract FROM proposals WHERE id = ".$p." LIMIT 1 ";
				$response3 = mysql_query($sql3);
				while ($row3 = mysql_fetch_array($response3))
				{
					$has_abstract = false;
					echo '<div class="paretoproposal">';
					if (!empty($row3['abstract'])) {
						$has_abstract = true;
						echo '<h3>Abstract</h3>';
						echo $row3['abstract'] ;
					}
					else {
						echo $row3['blurb'] ;
					}


					$sql4 = "SELECT  users.username, users.id FROM endorse, users WHERE  endorse.userid = users.id and endorse.proposalid = " .$p. " ";
					$response4 = mysql_query($sql4);
					echo '<br />Endorsed by: ';
					while ($row4 = mysql_fetch_array($response4))
					{
						echo '<a href="user.php?u='.$row4[1].'">' . $row4[0] . '</a> ';
					}

					if ($has_abstract)
					{
						echo '<span class="expandbtn">Show Full Text</span>';
					}

					echo '<br />';

					if ($has_abstract)
					{
						echo '<div class="paretoabstract">';
						echo '<h3>Full Text</h3>';
						echo $row3['blurb'];
						echo '</div>';
					}

					echo '</div>';



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


	if ( $phase==0 and $userid==$creatorid and $tomoveon==1)
	{
		if ($generation==1)
		{
			if (CountProposals($question,$generation)>1)
			{
				?>
					<form method="POST" action="moveontoendorse.php">
					If everybody has written their proposals, you can:
						<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
						<input type="submit" name="submit" id="submit" value="Move On to the Next Phase" />
					</form>
				<?php
			}
		}
		else
		{
			if (CountProposals($question,$generation))
			{
				?>
					<form method="POST" action="moveontoendorse.php">
					If everybody has written their proposals, you can:
						<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
						<input type="submit" name="submit" id="submit" value="Move On to the Next Phase" />
					</form>
				<?php
			}
		}
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

		if ($nEndorsers>1 and $userid==$creatorid and $tomoveon==1)
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
			
		<p><strong>Note:</strong> Proposals can be of any length and may include an abstract of up to 500 characters in length if you wish. When proposals are listed at the voting stage the abstract will be displayed if one exists, otherwise the full proposal will be displayed. For proposals longer than 1000 characters the abstract is mandatory.</p>
		
		<?php	#echo "<p>Time since last moveon: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: 1 day need to pass between one moveon and the next</p>";
		echo "<p>Time since first proposal on this generation: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: ";
		if ($minimumdays)
		{ 			echo $minimumdays." days ";}
		if ($minimumhours)		{			echo $minimumhours." hours ";		}
		if ($minimumminutes)		{			echo $minimumminutes." minutes ";		}
		echo "must have passed between the first proposal and the moment when the questioner can move the question on.</p>";
?>

	<form method="POST" action="newproposaltake.php">

	<div id="editor_panel">
	<!-- Input Proposal start -->
	<div id="abstract_panel">
		<h3><span></span><a href="#" id="abstract_title">Abstract (optional)</a></h3>
		<div id="abstract_RTE">
			<?php require_once("abstract.php"); ?>
		</div>
	</div>

	
       <div id="proposal_RTE">
       <textarea id="content" name="blurb" class="jqrte_popup" rows="500" cols="70"></textarea>
      <?php
         $RTE_TextLimit_content = 1000;
         include_once("js/jquery/RichTextEditor/content_editor_proposal.php");
         include_once("js/jquery/RichTextEditor/editor.php");
      ?>
	<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
	<input type="submit" name="submit" id="submit" value="Create proposal" disabled="disabled"/>
	</div><!-- proposal_RTE -->
	
	</div><!-- editor_panel -->

<!-- </form> -->
<script type="text/javascript">
$(document).ready(function() {
	var checklength = function (len) {
		var title = $("#abstract_title");
		var abstract_length = $("#abstract_rte").data('content_length');
		var content_length =  $("#content_rte").data('content_length');

		if ((content_length  > 0 && content_length <= limit) || (content_length  > 0 && abstract_length > 0))
		{
			$("input[value=Create proposal]").removeAttr("disabled");
		}
		else
		{
			$("input[value=Create proposal]").attr("disabled","disabled");
		}

		if (content_length  > limit)
		{
			title.html("Abstract Required: Enter up to 500 characters below:");
			title.css("color", "green");
			title.css("font-style", "bold");
			$("#content_rte_chars_msg").html("Abstract Required");
		}
		else if ( content_length  <= limit )
		{
			title.html("Abstract (Optional)");
			title.css("color", "black");
			title.css("font-style", "none");
			$("#content_rte_chars_msg").html("");
		}
	}

	try{
		$("#content_rte").jqrte();
		$("#content_rte").jqrte_setIcon();
		$("#content_rte").jqrte_setContent();
		$("#content_rte").data('content_length', 0);
		var limit = <?= empty($RTE_TextLimit_content) ? 'null' : $RTE_TextLimit_content; ?>;
		if (limit) {
			$("#content_rte").data('maxlength', limit);
			$("#content_rte").data('callback', checklength);
			$("#abstract_rte").data('callback', checklength);
		}
	}
	catch(e){}
});
</script>
<!-- Input Proposal end -->
</form>

<br /><br />
		<?php
		$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  and roundid = ".$generation." and usercreatorid = ".$userid." and source = 0 ORDER BY `id` DESC  ";
		$response = mysql_query($sql);
		if ($response)
		{
			echo "<h3>Proposals You have written:</h3>";
			echo '<table border="1">';
			while ($row = mysql_fetch_array($response))
			{
				?>
						<tr>
						
						<?php
						
						
						$has_abstract = false;
						echo '<td>';
						/* echo '<h3>' . $row['id'] . '</h3>'; */
						if (!empty($row['abstract'])) {
							$has_abstract = true;
							echo '<h3>Abstract</h3>';
							echo $row['abstract'] ;
						}
						else {
							echo $row['blurb'] ;
						}

						if ($has_abstract)
						{
							echo '<span class="expandbtn">Show Full Text</span>';
						}

						echo '<br />';

						if ($has_abstract)
						{
							echo '<div class="paretoabstract">';
							echo '<h3>Full Text</h3>';
							echo $row['blurb'];
						}
						
						echo '</td>';
	
						
					/* 	if (!empty($row['abstract'])) {
							echo '<td>' . $row['abstract'] . '</td>';
						}
						else {
							echo '<td>' . $row['blurb'] . '</td>';
						} */
						?>

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
			<tr><td><h4>Voting</br>History</h4></td>
			<td><h4>Proposed Solution</h4></td><td><b>Check all the ones you endorse</b></td></tr>

			<?php

			while ($row = mysql_fetch_array($response))
			{
				echo '<tr>';
				echo '<td class="vote_list">';
				if ($row['source'] != 0)
				{
					// Display voting history for aesexual parents
					$proposal = $row['id'];
					$source = $row['source'];
					$ancestors = GetAncestorEndorsements($source, $userid, $question, $generation);
					foreach($ancestors as $ancestor)
					{
						if ($ancestor['endorsed'] == -1)
						{
							#echo "<span>" . $ancestor[generation] . "</span>";
							#echo ' <img src="images/novote.jpg" title="You did not participate in the voting on generation '.$ancestor[generation].'"  height="30">';
							echo ' <img src="images/tick_empty.png" title="You did not participate in the voting on generation '.$ancestor[generation].'"  height="30">';
						}
						elseif ($ancestor['endorsed'] == 1)
						{
							#echo "<span>$ancestor[generation] </span>";
							echo ' <img src="images/thumbsup.jpg" title="You endorsed this proposal on generation '.$ancestor[generation].'"  height="30">';
						}
						elseif ($ancestor['endorsed'] == 0)
						{
							#echo "<span>$ancestor[generation] </span>";
							echo ' <img src="images/thumbsdown.jpg" title="You ignored this proposal  on generation '.$ancestor[generation].'" height="30">';
						}
						echo '</br>';
					}
				}
				else{ echo '&nbsp;'; }
				echo '</td>';
				
				
				$has_abstract = false;
				echo '<td>';
				/* echo '<h3>' . $row['id'] . '</h3>'; */
				if (!empty($row['abstract'])) {
					$has_abstract = true;
					echo '<h3>Abstract</h3>';
					echo $row['abstract'] ;
				}
				else {
					echo $row['blurb'] ;
				}
				
				if ($has_abstract)
				{
					echo '<span class="expandbtn">Show Full Text</span>';
				}

				echo '<br />';

				if ($has_abstract)
				{
					echo '<div class="paretoabstract">';
					echo '<h3>Full Text</h3>';
					echo $row['blurb'];
				}
				
				echo '</td><td>';
				
				echo '<Input type = "Checkbox" Name ="proposal[]" title="Check this box if you endorse the proposal" value="'.$row[0].'"';

				$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $row[0] . "  LIMIT 1";
				if(mysql_fetch_array(mysql_query($sql)))
				{
					echo ' checked="checked" ';
				}

				echo ' /></p> </td></tr>';
			}
			?>

			<tr><td colspan="2">&nbsp;</td><td><input type = "Submit" Name = "Submit" title="Votes are not counted unless submitted." VALUE = "Submit!"></td>
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
		echo '<a href="viewhistoryofquestion.php?' . $_SERVER['QUERY_STRING'] . '">View History of The Question</a>. Here you can see who voted for what, what proposals were eliminated. You can recover past proposals that you think should not be lost. Maybe explaining them better.';
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
