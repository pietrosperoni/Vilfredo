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
<script type="text/javascript" src="js/vilfredo.php"></script>';


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
	$generation = $_GET[QUERY_KEY_GENERATION];

	$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";
	//$room = ucfirst($room);
	if($generation>=GetQuestionGeneration($question))
	{
		header("Location: viewquestion.php".CreateQuestionURL($question,$room));
	}

if ($userid) 
{
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
		$generationnow=$row['roundid'];
		
		$creatorid=$row['usercreatorid'];
		$title=$row['title'];
		$bitlyhash = $row['bitlyhash'];
		$shorturl = '';
		
		
		echo '<div class="questionbox">';
		
		
		#$generation
		
		echo "<h2>{$VGA_CONTENT['question_txt']}</h2>";

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
				?> <input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['email_unsub_link']?>" /> <?php
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
			echo '<p id="author"><cite>' . $VGA_CONTENT['cite_txt'] . ' <a href="user.php?u=' . $row2[1] . '">'.$row2[0].'</a></cite></p>';
			 echo '</div>';
		}


		echo '<table border="1" class="historytable"><tr>';
		if ($generation>=2){ echo '<td width="30%"><strong>'.WriteGenerationPage($question,$generation-1,$room).'</strong></td>';	}
		else {	echo '<td width="30%"><strong></strong></td>';	}
		echo '<td><h2><a href="vhq.php' .CreateQuestionURL($question, $room). '">' . $VGA_CONTENT['history_link'] . '</a></h2><h1>' . $VGA_CONTENT['gen_txt'] . ' '.$generation.'</h1></td>';
		if ($generation+1<$generationnow)	{ echo '<td width="30%"><strong>'.WriteGenerationPage($question,$generation+1,$room).'</strong></td>';}
		else		{echo '<td width="30%"><strong></strong></td>';	}		
		echo '</tr></table>';

		if ($generation>0)
		{
			InsertMap($question,$generation,$userid,"L",0);
			/*
			$graphsize = 'largegraph';
			if ($filename = InsertMap2($question,$generation,$userid,"L",0))
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
			echo "<h3>{$VGA_CONTENT['pareto_front_txt']}</h3>";
			$ParetoFront=ParetoFront($question,$generation);
			$proposals=GetProposalsInGeneration($question,$generation);
			$NonParetoProposals=array_diff($proposals,$ParetoFront);
			
			foreach ($ParetoFront as $p)
			{
				echo '<div class="paretoproposal">';
			?>	
					<form method="get" action="npv.php" target="_blank">
				<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
						<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
						<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
						<input type="submit" name="submit" title="<?=$VGA_CONTENT['click_rep_mod_title']?>" id="submit" value="<?=$VGA_CONTENT['reprop_mutate_button']?>" /></form>
						<?php	echo '</h3>';
				WriteProposalOnlyContent($p,$question);#,$generation,$room,$userid);				
				WriteAuthorOfAProposal($p,$userid,$generation,$question,$room);
				WriteEndorsersToAProposal($p,$userid);
				echo "<br>";			
				echo "<br>";			
				
				echo WriteProposalRelation($p,$question,$generation,$userid,$room);
				
				echo '</div>';
			}
			$PropOrdered = array();
			foreach ($NonParetoProposals as $p)
			{
				$PropOrdered[$p]=CountEndorsersToAProposal($p);
			}
			arsort($PropOrdered);
			foreach (array_keys($PropOrdered) as $p)
			{
				#echo '<div class="paretoproposal">';
				echo '<div class="nonparetoproposal">';
				?>	
						<form method="get" action="npv.php" target="_blank">
					<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
							<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
							<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
							<input type="submit" name="submit" title="<?=$VGA_CONTENT['click_rep_mod_title']?>" id="submit" value="<?=$VGA_CONTENT['reprop_mutate_button']?>" /></form>
							<?php	echo '</h3>';
					WriteProposalOnlyContent($p,$question);#,$generation,$room,$userid);				
					WriteAuthorOfAProposal($p,$userid,$generation,$question,$room);
					WriteEndorsersToAProposal($p,$userid);
					echo "<br>";			
					echo "<br>";			

					echo WriteProposalRelation($p,$question,$generation,$userid,$room);
				
				#echo WriteProposalText($p,$question,$generation,$room,$userid);
				#echo "<br>";	
				#echo "<br>";			
				#echo WriteProposalRelation($p,$question,$generation,$userid,$room);
				
				echo '</div>';
			}
			
			echo '<br />';	
			
			
			#from the person to person point of view
			$endorsers=Endorsers($question,$generation);
			foreach ($endorsers as $e)
			{
				echo WriteUserVsReader($e,$userid)."{$VGA_CONTENT['has_voted_for_txt']} ";
				$proposers=ProposalsToAnEndorser($e,$question,$generation);
				foreach ($proposers as $p)	   {	echo WriteProposalNumber($p,$room);}
				echo '<br />';	
				echo " {$VGA_CONTENT['of_those_txt']} ";
				$proposersInPF=array_intersect($proposers,$ParetoFront);
				foreach ($proposersInPF as $p)	{	echo WriteProposalNumber($p,$room);}
				echo $VGA_CONTENT['in_pf_txt'] . '<br />';	
				echo '<br />';	
			}
			echo '<br />';	
			echo '</div>';	
					
			echo '<h3>' . $VGA_CONTENT['alt_history_txt'] . '</h3><br />';
			
			echo '' . $VGA_CONTENT['alt_events_txt'] . '<br />';
			echo '<h4>' . $VGA_CONTENT['key_players_txt'] . '</h4><br />';			
			
			$ProposalsCouldDominate=CalculateKeyPlayers($question,$generation);
			if (count($ProposalsCouldDominate) > 0)
			{
				$KeyPlayers=array_keys($ProposalsCouldDominate);
				foreach ($KeyPlayers as $KP)
				{
					foreach ($ProposalsCouldDominate[$KP] as $PCD)
					{
						/*
						echo WriteUserVsReader($KP,$userid).' did not support '.WriteProposalNumber($PCD,$room).', but IF she or he did, then the Pareto Front would have be simpler.<br />We say that '.WriteUserVsReader($KP,$userid).' is a Key Player for this proposal for Generation '.$generation.'.<br />We emailed '.WriteUserVsReader($KP,$userid).' asking him or her to rewrite '.WriteProposalNumber($PCD,$room).' in a format acceptable to him or her.<br/>';*/
						
						$keyPlayer = WriteUserVsReader($KP,$userid);
						$proposalNumber = WriteProposalNumber($PCD,$room);
						$format = $VGA_CONTENT['key_player_exp_txt'];
						echo sprintf($format, $keyPlayer, $proposalNumber, $generation);
						echo '<br/>';
					}
					echo '<br/>';
				}
				echo '</p>';
			}	

			echo '<h4>' . $VGA_CONTENT['each_part_txt'] . '</h4><br />';

			foreach ($endorsers as $e)
			{
				
				$PFE=CalculateFullParetoFrontExcluding($proposals,$e);
				$ParetoFrontPlus=array_diff($PFE,$ParetoFront);
				$ParetoFrontMinus=array_diff($ParetoFront,$PFE);
#				echo "ParetoFrontPlus=",$ParetoFrontPlus."<br />";
#				print_r($ParetoFrontPlus);
#				echo '<br />';
#				echo '<br />';
				
#				echo "ParetoFrontMinus=",$ParetoFrontMinus."<br />";
#				print_r($ParetoFrontMinus);
#				echo '<br />';
#				echo '<br />';
				
#				echo "PFE=",$PFE."<br />";
#				print_r($PFE);
#				echo '<br />';
#				echo '<br />';
				
#				echo "ParetoFront=",$ParetoFront."<br />";
#				print_r($ParetoFront);
#				echo '<br />';
#				echo '<br />';
				
				if (sizeof($ParetoFrontPlus) OR sizeof($ParetoFrontMinus))
				{
					echo "By voting ".WriteUserVsReader($e,$userid)." have changed the resulting Pareto Front.<br />";
					if (sizeof($ParetoFrontPlus))
					{
						foreach ($ParetoFrontPlus as $p)	{	echo WriteProposalNumber($p,$room);}						
						echo "would have been in the Pareto Front,<br />while ".WriteUserVsReader($e,$userid)." vote helped us to simplify the solution and exclude them.<br />";						
					}
					if (sizeof($ParetoFrontMinus))
					{
						foreach ($ParetoFrontMinus as $p)	{	echo WriteProposalNumber($p,$room);}						
						echo "would NOT have been in the Pareto Front,<br />while ".WriteUserVsReader($e,$userid)." vote helped us to see their importance, and thus consider them for the next generation.<br />";						
					}
					echo "Without ".WriteUserVsReader($e,$userid)." the Pareto Front would have been";
					foreach ($PFE as $p)	{	echo WriteProposalNumber($p,$room);}						
					echo "<br />";						
					echo "<br />";						
				}
				else
				{
					echo "By voting ".WriteUserVsReader($e,$userid)." did not change the resulting Pareto Front.<br />But we love ".WriteUserVsReader($e,$userid)." anyway :-). Maybe, even more so ;-)!<br /><br />";
				}
			}
			echo $VGA_CONTENT['combined_effect_txt'];
			
			
		}
	}
	echo '</div>';

include('footer.php');

?>
