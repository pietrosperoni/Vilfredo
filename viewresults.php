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



	
echo '<h2><a href="vhq.php' .CreateQuestionURL($question, $room). '">' . $VGA_CONTENT['history_link'] . '</a></h2>';
	
	
if ($generation>0)
{
	$ParetoFrontEndorsersTimeless=ShowCommunityMap($question,$generation,$phase);
	
	$ParetoFront=array_keys($ParetoFrontEndorsersTimeless);
	$ParetoFrontEndorsers=$ParetoFrontEndorsersTimeless;

	
	echo '<div id="paretofrontbox">';
	$proposals=$ParetoFront;
						
	foreach ($ParetoFront as $p)
	{
		$originalname=GetOriginalProposal($p);
		
		echo '<div class="paretoproposal"><a name="proposal'.$originalname['proposalid'].'"></a>';
	?>	
			<form method="get" action="npv.php" target="_blank">
		<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
				<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
				<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
				<input type="submit" name="submit" title="<?=$VGA_CONTENT['click_rep_mod_title']?>" id="submit" value="<?=$VGA_CONTENT['reprop_mutate_button']?>" /></form>
				<?php	echo '</h3>';
		WriteProposalOnlyContent($p,$question);#,$generation,$room,$userid);
		WriteAuthorOfAProposal($p,$userid,$generation,$question,$room);
		#WriteEndorsersToAProposal($p,$userid);
		
		echo '<br />Endorsed by: ';
		foreach($ParetoFrontEndorsers[$p] as $e)
		{
			echo WriteUserVsReader($e,$userid);
		}		
		
		echo "<br>";
		echo "<br>";
		echo '</div>';
	}

	echo '<br />';	
		
	echo '</div>';	

}

echo '</div>';

include('footer.php');

?>
