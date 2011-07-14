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


if (!HasQuestionAccess())
{
	header("Location: viewquestions.php");
}

$question = $_GET[QUERY_KEY_QUESTION];
$generation = $_GET[QUERY_KEY_GENERATION];

$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";

//$room = ucfirst($room);


WriteQuestionInfo($question,$userid);

$QuestionInfo=GetQuestion($question);
$title=$QuestionInfo['title'];
$content=$QuestionInfo['question'];
$room=$QuestionInfo['room'];
$phase=$QuestionInfo['phase'];
$generationnow=$QuestionInfo['roundid'];
$author=$QuestionInfo['usercreatorid'];
#$bitlyhash = $row['bitlyhash'];
#$shorturl = '';


if($generation>=GetQuestionGeneration($question))
{
	header("Location: viewquestion.php".CreateQuestionURL($question,$room));
}

	
echo '<h2><a href="vhq.php' .CreateQuestionURL($question, $room). '">' . $VGA_CONTENT['history_link'] . '</a></h2><h1>' . $VGA_CONTENT['gen_txt'] . ' '.$generation.'</h1>';
	
	
if ($generation>0)
{
	
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

echo '</div>';

include('footer.php');

?>
