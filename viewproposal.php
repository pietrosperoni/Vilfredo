<?php
$headcommands='
<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
<script type="text/javascript" src="js/svg/jquery.svg.min.js"></script>
<script type="text/javascript" src="js/vilfredo.php"></script>
';

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
	
	$question=GetProposalsQuestion($proposal);
	WriteQuestionInfo($question,$userid);

	$QuestionInfo=GetQuestion($question);
	$title=$QuestionInfo['title'];
	$content=$QuestionInfo['question'];
	$room=$QuestionInfo['room'];
	$phase=$QuestionInfo['phase'];
	$generation=$QuestionInfo['roundid'];
	$author=$QuestionInfo['usercreatorid'];

	$ProposalInfo=GetProposalValues($proposal);
	$proposaltext=$ProposalInfo['blurb'];
	$proposalround=$ProposalInfo['roundid'];
	$proposalabstract=$ProposalInfo['abstract'];
	$proposalauthor=$ProposalInfo['usercreatorid'];
	

	$Disabled="DISABLED";
	$Tootip="{$VGA_CONTENT['wait_to_write_tooltip']}";
	if($phase==0)	
	{
		$Disabled="";
		$Tootip="{$VGA_CONTENT['reprop_this_title']}";
	}


	echo '<h1 id="question">Proposal</h1>';

	?>
		<form method="get" action="npv.php" target="_blank">
	<?php	echo '<h3>'.WriteProposalPage($proposal,$room)." ";?>	
			<input type="hidden" name="p" id="p" value="<?php echo $proposal; ?>" />
			<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
			<input type="submit" name="submit" title="This proposal is already present, but you can click here to modify the text and propose an alternative" id="submit" value="Mutate" /></form>
			<?php	echo '</h3>';
	WriteProposalOnlyContent($proposal,$question);#,$generation,$room,$userid);
	echo '<h2>History of the Proposal</h2>';
	$OriginalProposal=GetOriginalProposal($proposal);
	$OPropID=$OriginalProposal["proposalid"];
	$OPropGen=$OriginalProposal["generation"];
	#		echo "The Original Proposal was ";
	#		$urlquery = CreateProposalURL($OPropID, $room);
	#		echo '<a href="viewproposal.php'.$urlquery.'">'.$OPropID.'</a>';
	#		echo "proposed on generation ".$OPropGen.". <br>";

	echo "The proposal was written by ";
	echo WriteUserVsReader($proposalauthor,$userid);
	echo "in ".WriteGenerationPage($question,$OPropGen,$room).".<br>";

	$ProposalToStudy=$OPropID;
	$GenerationToStudy=$OPropGen;
	
	while($ProposalToStudy)
	{		
		if ($generation<=$GenerationToStudy){break;}
		echo '<h4>'.WriteGenerationPage($question,$GenerationToStudy,$room).'</h4>';
		echo '<table border="1" class="historytable">';
		echo '<tr><td>';
		WriteEndorsersToAProposal($ProposalToStudy,$userid);
		echo '<br />';

		echo WriteProposalRelation($ProposalToStudy,$question,$GenerationToStudy,$userid,$room);
		$ProposalToMap=$ProposalToStudy;
		$ProposalToStudy=GetProposalDaughter($ProposalToStudy);			
		$GenerationToStudy++;					
		echo '</td><td>';
		InsertMap($question,$GenerationToStudy-1,0,"S",$ProposalToMap);
		echo '</td></tr></table>';
	}
include('footer.php');

?>