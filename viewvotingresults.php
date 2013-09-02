<?php
$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='
<link rel="stylesheet" href="css/velocity.css" />
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
include('vga_timeless.php');


?>
<script type="text/javascript">
//Assumes id is passed in the URL
var recaptcha_public_key = '<?php echo $recaptcha_public_key;?>';
</script>
<?php

// sanitize url
$question = fetchValidQuestionFromQuery();
$room = fetchValidRoomFromQuery();

// Return false if bad query parameters passed
if ($question === false || $room === false )
{
	header("Location: error_page.php");
	exit;
}

if (!HasQuestionAccess())
{
	header("Location: viewquestions.php");
	exit;
}

#WriteQuestionInfo($question,$userid);

WriteQuestionInfoFromData($question,$userid);

$QuestionInfo = GetQuestion($question);
$title=$QuestionInfo['title'];
$content=$QuestionInfo['question'];
$room=$QuestionInfo['room'];
$phase=$QuestionInfo['phase'];
$generationnow=$QuestionInfo['roundid'];
$author=$QuestionInfo['usercreatorid'];
#$bitlyhash = $row['bitlyhash'];
#$shorturl = '';


$generation=$generationnow;

$pids = CalculatePareto($question, $generation);
$results = array();

if (!empty($pids))
{
	$results = getFinalVotes($pids);
}

$eval_phase =  getQuestionPhase($question);

if ($eval_phase == 'voting')
	{
		$url = "voting.php?q=".$question;
		$url .= ($room != '') ? '&room='.$room : '';
		header("Location: ".$url);
		exit;
	}
	elseif ($eval_phase == 'evaluation')
	{
		$url = "viewquestion.php?q=".$question;
		$url .= ($room != '') ? '&room='.$room : '';
		header("Location: ".$url);
		exit;
	}


?>

<div class="bubblescontent">
<h2><?=$VGA_CONTENT['welcome_usr_txt']?> <?=$username?></h2>

<?php if ($userid and $userid == $author) {?>
<form autocomplete="off" method="post" action="movebacktoevaluation.php">
Return this question to evaluation:
	<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
	<input type="submit" name="submit" id="submit" value="Move Back to Evaluation" />
</form>
<form autocomplete="off" method="post" action="movetofinalvoting.php">
Or move this question back to final voting:
	<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
	<input type="submit" name="submit" id="submit" value="Move Back to Final Voting" />
</form>
<?php } ?>
</div> <!-- bubblescontent -->


<?php
if (empty($results))
{ ?>
	<p>There are no final voting results for this question!</p
		
<?php
}
else
{
	//$ParetoFrontEndorsersTimeless=ShowCommunityMap($question,$generation,$phase);
	
	//$ParetoFront = array_keys($ParetoFrontEndorsersTimeless);
	//$ParetoFrontEndorsers = $ParetoFrontEndorsersTimeless;

	echo '<br>';
	
	echo '<div id="paretofrontbox">';
	$proposals=$ParetoFront;
						
	foreach ($results as $result)
	{
		$p = $result['proposalid'];
		$winnerclass = ($p == $results[0]['proposalid']) ? 'winner' : '';
		$originalname=GetOriginalProposal($p);
		
		echo '<div class="paretoproposal ' . $winnerclass . '"><a name="proposal'.$originalname['proposalid'].'"></a>';
		
		if ($p == $results[0]['proposalid'])
		{
			echo "<div class=\"finalvotingwinnertxt\">This proposal was the winner in the final voting round, with {$results[0]['votes']} votes!</div>";
		}
		
		WriteProposalOnlyContent($p,$question);#,$generation,$room,$userid);
		WriteAuthorOfAProposal($p,$userid,$generation,$question,$room);
		
		echo "<br /><br /><b>Final Votes: {$result['votes']}</b>";
		
		
		echo "<br>";
		echo "<br>";
		echo '</div>';
	}

	echo '<br />';	
		
	echo '</div>';	

}

echo '</div>';

echo '<h2><a href="vhq.php' .CreateQuestionURL($question, $room). '">' . $VGA_CONTENT['history_link'] . '</a></h2>';

include('footer.php');

?>
