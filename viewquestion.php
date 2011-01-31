<?php
$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='
<link rel="Stylesheet" type="text/css" href="js/jquery/RichTextEditor/css/jqrte.css">
<link type="text/css" href="js/jquery/RichTextEditor/css/jqpopup.css" rel="Stylesheet">
<link rel="stylesheet" href="js/jquery/RichTextEditor/css/jqcp.css" type="text/css">
<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/svg/jquery.svg.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>
<script type="text/javascript" src="js/vilfredo.js"></script>';


include('header.php');

?>
<script type="text/javascript">
//Assumes id is passed in the URL
var recaptcha_public_key = '<?php echo $recaptcha_public_key;?>';
</script>
<?php

//if ($userid)
//{
	// Check if user has room access.
	if (!HasQuestionAccess())
	{
		header("Location: viewquestions.php");
	}

	$question = $_GET[QUERY_KEY_QUESTION];

	$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";
	//$room = ucfirst($room);

if ($userid) {
	$sql = "SELECT * FROM updates WHERE question = ".$question." AND  user = ".$userid." LIMIT 1 ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	if ($row)
		{$subscribed=1;}
	else
		{$subscribed=0;}
}

	$sql = "SELECT * FROM questions WHERE id = $question";
	$response = mysql_query($sql);
	if (!$response)
	{
		db_error($sql);
	}
	
	while ($row = mysql_fetch_array($response))
	{
		$content=$row['question'];
		$phase=$row['phase'];
		$generation=$row['roundid'];
		$creatorid=$row['usercreatorid'];
		$title=$row['title'];
		$bitlyhash = $row['bitlyhash'];
		$shorturl = '';
		$permit_anon_votes = $row['permit_anon_votes'];
		$permit_anon_proposals = $row['permit_anon_proposals'];
		
		if (!empty($bitlyhash)) 
		{
			$shorturl = BITLY_URL.$bitlyhash;
		}
		else
		{
			$longurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			if ($hash = make_bitly_hash($longurl, $bitly_user, $bitly_key))
			{
				SetBitlyHash($question, $hash);
				$shorturl = BITLY_URL.$hash;
			}
		}
		
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
		
		echo '<div class="questionbox">';
		
		echo "<h2>Question</h2>";

		?>
			<h2 id="question">
			<form method="post" action="changeupdate.php">
				<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="hidden" name="room" id="room" value="<?php echo $room; ?>" />
			<?php
			echo  $title;
		if ($userid) {
			if ($subscribed==1)
			{
				?> <input type="submit" name="submit" id="submit" value="email Unsubscribe" /> <?php
			}else{
				?> <input type="submit" name="submit" id="submit" value="email Subscribe" /> <?php
			}
		}
			?>
			</form>
			</h2>
		<?php

		echo "<br />";
		
		echo '<div id="question">' . $content . '</div>';
		
		echo "<br />";

		$sql2 = "SELECT users.username, users.id FROM questions, users WHERE questions.id = ".$question." and users.id = questions.usercreatorid LIMIT 1 ";
		$response2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_array($response2))
		{
			echo '<p id="author"><cite>asked by <a href="user.php?u=' . $row2[1] . '">'.$row2[0].'</a></cite></p>';
			
			 echo '</div>';
			 
			 //echo '<div class="social-buttons">';
			 
			 echo '<table id="social-buttons"><tr><td>';
			
			// Only display twit button if shorturl found in DB or generated from bitly
			if (!empty($shorturl))
			{
				$retweetprefix = "RT @Vg2A";
				$urlshortservice = BITLY_URL;
				$tweet = urlencode($retweetprefix." ".$title." ".$shorturl);
				$tweetaddress = "http://twitter.com/home?status=$tweet";
				echo "<p><a class=\"tweet\" href=\"$tweetaddress\"><span>Tweet This Question</span></a></p>";
			}
			
			echo '</td></tr></table>';
			
		}

		if (($phase==0) && ($generation>1))
		{
			InsertMap($question,$generation-1);
			/*
			$graphsize = 'mediumgraph';
			if ($filename = InsertMap2($question,$generation-1))
			{
				$filename .= '.svg';
				?>
				<script type="text/javascript">
				$(document).ready(function() {
					var svgfile = '<?= $filename; ?>';
					$('#svggraph1').svg({loadURL: svgfile});
				});
				</script>
				<?php
			}
			echo '<div id="svggraph1" class="'.$graphsize.'"></div>';
			*/
			echo '<div id="paretofrontbox">';

			$VisibleProposalsGenerations=PreviousAgreementsStillVisible($question,$generation);

			echo '<h3>Previous agreements, Still Visible (<a href="vhq.php?' . $_SERVER['QUERY_STRING'] . '">History</a>)</h3>';
			sort($VisibleProposalsGenerations);
			
			if(empty($VisibleProposalsGenerations)) echo "None<br />";
			
			foreach($VisibleProposalsGenerations as $vpg)
			{
				$endorsersAgreement=Endorsers($question,$vpg);
				if($generation==$vpg+1){echo '<h4><a href="vg.php'.CreateGenerationURL($question,$vpg,$room).'">Last Generation</a> ';}
				else		{	echo '<h4><a href="vg.php'.CreateGenerationURL($question,$vpg,$room).'">'.($generation-$vpg). ' Generations ago</a> ';}
				
				echo " an Agreement was found between ".Count($endorsersAgreement)." people ";
				foreach($endorsersAgreement as $ea)	{	echo '<img src="images/a_man.png">'; }
				echo "</h4>";
				
				$ProposalsToSee=ParetoFront($question,$vpg);
					foreach($ProposalsToSee as $p)
					{
						?><form method="get" action="npv.php" target="_blank">
							<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
								<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
								<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
								<input type="submit" name="submit" title="Click here to either re-propose this proposal, or propose an alternative proposal inspired by this one. The form will automatically be filled with this proposal." id="submit" value="Repropose or Mutate" /></form>
								<?php	echo '</h3>';
						WriteProposalOnlyContent($p,$question,$generation,$room,$userid);
					}
					
			}
			$lastgeneration=$generation-1;
			if (! in_array($lastgeneration,$VisibleProposalsGenerations))
			{
				echo "<h3>Alternative proposals, that emerged from last endorsing round</h3>";
				echo "<p><i>The different proposals do not represent the best proposals, nor the more popular.
				Strictly speaking they are the Pareto Front of the set of proposals.
				You can think of it as the smallest set of proposals such that every participant is represented.</i></p>";

				$ParetoFront=ParetoFront($question,$generation-1);
				foreach ($ParetoFront as $p)
				{
						echo '<div class="paretoproposal">';
					
					?><form method="get" action="npv.php" target="_blank">
						<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
							<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
							<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
							<input type="submit" name="submit" title="This proposal is already present, but you can click here to modify the text and propose an alternative" id="submit" value="Mutate" /></form>
							<?php	echo '</h3>';
					WriteProposalOnlyContent($p,$question);#,$generation,$room,$userid);
					
					$OriginalProposal=GetOriginalProposal($p);					#$OPropID=$OriginalProposal["proposalid"];
					$OPropGen=$OriginalProposal["generation"];

					echo '<br />Written by: '.WriteUserVsReader(AuthorOfProposal($p),$userid);
					if ($OPropGen!=$generation)		{	echo "in ".WriteGenerationPage($question,$OPropGen,$room).".<br>";	}
					$endorsers=EndorsersToAProposal($p);
					echo '<br />Endorsed by: ';
					foreach($endorsers as $e)		{	echo WriteUserVsReader($e,$userid);}					
					echo '</div>';
					#}
				}
				echo '</div>';
			}
			
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
Write proposals you did not like, in a format acceptable to you (this is a biggie);
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


	if ( $userid and $phase==0 and $userid==$creatorid and $tomoveon==1)
	{
		if ($generation==1)
		{
			if (CountProposals($question,$generation)>1)
			{
				?>
					<form method="post" action="moveontoendorse.php">
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
					<form method="post" action="moveontoendorse.php">
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

		if ($userid and $nEndorsers>1 and $userid==$creatorid and $tomoveon==1)
		{
			?>
			<form method="post" action="moveontowriting.php">
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

	
	<?php 
		if ($userid) {//open 
		?>
		<form method="post" action="newproposaltake.php">
		<?php } else { ?>
		<form method="post" action="newproposaltake.php" class="reg-only">
	<?php } ?>

	<div id="editor_panel">
	<!-- Input Proposal start -->
	
	<div id="abstract_panel">
		<h3><span></span><a href="#" id="abstract_title">Abstract (Optional)</a></h3>
		<div id="p_abstract_RTE">
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
	
	<?php 
	if ($userid) {
		$regclass = "submit_ok";
	} else {
		$regclass = "reg_submit";
	}
	?>
	
	<input class="rte_submit <?= $regclass; ?>" type="button" name="submit_p" id="submit_p" value="Create proposal" disabled="disabled"/>
	
	<?php
	// Anonymous Submit
	//set_log('permit_anon: ' . $permit_anon);
	if (!$userid && $permit_anon_proposals) :
	?>	
	Click this checkbox to submit your proposal anonymously
	<Input type = "Checkbox" Name ="anon" id="anon" title="Check this box if you wish to remain anonymous" value="" />
	<?php 
	endif ?>
	
	</div><!-- proposal_RTE -->
	</div><!-- editor_panel -->

<!-- </form> -->
<script type="text/javascript">
$(document).ready(function() {
	var checklength = function (len) {
		var title = $("#abstract_title");
		var abstract_length = $("#abstract_rte").data('content_length');
		var content_length =  $("#content_rte").data('content_length');
		var logged_in = <?= $userid ? 'true' : 'false'; ?>;

		if ((content_length  > 0 && content_length <= limit && abstract_length <= limit_abs) || (content_length  > 0 && abstract_length > 0 && abstract_length <= limit_abs))
		{
			$("#submit_p").removeAttr("disabled");
		}
		else
		{
			$("#submit_p").attr("disabled","disabled");
		}

		if (content_length  > limit)
		{
			title.html("Abstract Required: Enter up to 500 characters below:");
			title.css("color", "green");
			title.css("font-weight", "bold");
			$("#content_rte_chars_msg").html("Abstract Required");
		}
		else if ( content_length  <= limit )
		{
			title.html("Abstract (Optional)");
			title.css("color", "black");
			title.css("font-weight", "normal");
			$("#content_rte_chars_msg").html("");
		}
	}

	try{
		$("#content_rte").jqrte();
		$("#content_rte").jqrte_setIcon();
		$("#content_rte").jqrte_setContent();
		$("#content_rte").data('content_length', 0);
		
		var limit_abs = <?= empty($RTE_TextLimit_abstract) ? 'null' : $RTE_TextLimit_abstract; ?>;
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

<br />
	
<?php echo LoadLoginRegisterLinks($userid, 'submit_p'); ?>
	
<?php
if ($userid) {
		$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  and roundid = ".$generation." and usercreatorid = ".$userid." and source = 0 ORDER BY `id` DESC  ";
		$response = mysql_query($sql);
		if ($response)
		{
			//****
			echo "<h3>Proposals You have written:</h3>";
			echo '<table class="your_proposals userproposal">';
			while ($row = mysql_fetch_array($response))
			{
				echo '<tr><td>';
				echo '<div class="paretoproposal">';
				if (!empty($row['abstract'])) {
					echo '<div class="paretoabstract">';
					echo display_fulltext_link();
					echo '<h3>Proposal Abstract</h3>';
					echo $row['abstract'] ;
					echo '</div>';
					echo '<div class="paretotext">';
					echo '<h3>Proposal</h3>';
					echo $row['blurb'];
					echo '</div>';
				}
				else {
					echo '<div class="paretofulltext">';
					echo '<h3>Proposal</h3>';
					echo $row['blurb'] ;
					echo '</div>';
				}
				
				?>
				<form method="post" action="deleteproposal.php">
				<input type="hidden" name="p" id="p" value="<?php echo $row[0]; ?>" />
				<input type="submit" name="submit" id="submit" value="Edit or Delete" title="Click here to edit or delete your proposal"/>
				</form>
				
				<?php
				
				echo '</div>';
				
				/*
				echo '</td><td class="button_cell">';
				?>
				
				<form method="post" action="deleteproposal.php">
					<input type="hidden" name="p" id="p" value="<?php echo $row[0]; ?>" />
					<input type="submit" name="submit" id="submit" value="Edit or Delete" title="Click here to edit or delete your proposal"/>
				</form>
				
				<?php
				*/
				echo '</td></tr>';
			}
			echo '</table>';
		}
		else
		{
			echo "Sorry no proposals yet";
		}
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
			<form method="post" action="endorse_or_not.php">
					<input type="hidden" name="question" value="<?php echo $question; ?>" />
			<table border="1" class="your_endorsements userproposal">
			<tr>
			<td class="history_cell"><h4>Voting</br>History</h4></td>
			<td><h4>Proposed Solution</h4></td>
			<td class="endorse_cell"><b>Check all the ones you endorse</b></td>
			</tr>

			<?php

			while ($row = mysql_fetch_array($response))
			{
				echo '<tr>';
				echo '<td class="vote_list">';
				if ($userid and $row['source'] != 0)
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
							echo ' <img src="images/tick_empty.png" title="You did not participate in the voting on generation '.$ancestor[generation].'"  height="30" alt="empty tick box">';
						}
						elseif ($ancestor['endorsed'] == 1)
						{
							#echo "<span>$ancestor[generation] </span>";
							echo ' <img src="images/thumbsup.gif" title="You endorsed this proposal on generation '.$ancestor[generation].'"  height="30" alt="thumbs up">';
						}
						elseif ($ancestor['endorsed'] == 0)
						{
							#echo "<span>$ancestor[generation] </span>";
							echo ' <img src="images/thumbsdown.gif" title="You ignored this proposal  on generation '.$ancestor[generation].'" height="30" alt="thumbs down">';
						}
						echo '</br>';
					}
				}
				else{ echo '&nbsp;'; }
				echo '</td>';
				
				
				$has_abstract = false;
				echo '<td>';
				echo '<div class="paretoproposal">';
				if (!empty($row['abstract'])) {
					echo '<div class="paretoabstract">';
					echo display_fulltext_link();
					echo '<h3>Proposal Abstract</h3>';
					echo $row['abstract'] ;
					echo '</div>';
					echo '<div class="paretotext">';
					echo '<h3>Proposal</h3>';
					echo $row['blurb'];
					echo '</div>';
				}
				else {
					echo '<div class="paretofulltext">';
					echo '<h3>Proposal</h3>';
					echo $row['blurb'] ;
					echo '</div>';
				}
				echo '</div>';
				echo '</td><td>';
				
				echo '<Input type = "Checkbox" Name ="proposal[]" title="Check this box if you endorse the proposal" value="'.$row[0].'"';

			if ($userid) {
				$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $row[0] . "  LIMIT 1";
				if(mysql_fetch_array(mysql_query($sql)))
				{
					echo ' checked="checked" ';
				}
			}
				echo ' /></p> </td></tr>';
		}
		
	// Anonymous Submit
	//set_log('permit_anon: ' . $permit_anon);
	if (!$userid && $permit_anon_votes) :
	?>	
	<tr><td colspan="2"><p><strong>Click this checkbox to vote anonymously</strong></p></td><td>
	<Input type = "Checkbox" Name ="anon" id="anon" title="Check this box if you wish to remain anonymous" value="" />		
	</td></tr>
	<?php 
	endif ?>
	
	<tr><td colspan="2">&nbsp;</td><td>
	<?php
	// Submit button
		if ($userid) {
			$regclass = "submit_ok";
		} else {
			$regclass = "reg_submit";
		}
	?>
	<input class="<?= $regclass; ?>" type="button" name="submit_e" id="submit_e" value="Submit!"/>			
	</td></tr>
	</table>
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
		echo '<a href="vhq.php?' . $_SERVER['QUERY_STRING'] . '">View History of The Question</a>. Here you can see who voted for what, what proposals were eliminated. You can recover past proposals that you think should not be lost. Maybe explaining them better.';
		echo '</div>';
	}

	// echo "<a href=logout.php>Logout</a>";
//}
//else
//{
//		DoLogin();
//}

include('footer.php');

?>
