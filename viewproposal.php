<?php
$headcommands='
<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/svg/jquery.svg.min.js"></script>';

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

	$sql = "SELECT  questions.question, questions.roundid, questions.phase, proposals.blurb, proposals.roundid, questions.id, questions.room, questions.title, proposals.abstract, proposals.usercreatorid FROM proposals, questions WHERE proposals.id = " . $proposal . " and proposals.experimentid = questions.id";

	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		$questiontext=$row[0];
		$questionround=$row[1];
		$questionphase=$row[2];
		$proposaltext=$row[3];
		$proposalround=$row[4];
		$questionid=$row[5];
		$room=$row[6];
		$questiontitle=$row[7];
		$proposalabstract=$row[8];
		$author=$row[9];
		$urlquery = CreateQuestionURL($questionid, $room);

		echo '<h1 id="question">Proposal</h1>';

		echo '<h2 id="question">Question</h2>';

		echo '<h4 id="question">"<a href="viewquestion.php' . $urlquery . '">' . $questiontitle . '</a>"</h4>';
		echo '<div id="question">' . $questiontext . '</div>';		
		echo 'now on Generation ' . $questionround . '<br>';
		
		$Disabled="DISABLED";
		$Tootip="It is only permitted to propose during the writing phase. Please wait until the next writing phase to propose something";
		if($questionphase==0)	
		{
			$Disabled="";
			$Tootip="Click here to either re-propose this proposal, or propose an alternative proposal inspired by this one. The form will automatically be filled with this proposal.";
		}
		
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
		echo WriteUserVsReader($author,$userid);
		echo "in ".WriteGenerationPage($questionid,$OPropGen,$room).".<br>";

		$ProposalToStudy=$OPropID;
		$GenerationToStudy=$OPropGen;
		while($ProposalToStudy)
		{
			if ($questionround<=$GenerationToStudy){break;}			
			echo '<h4>'.WriteGenerationPage($questionid,$GenerationToStudy,$room).'</h4>';
			echo '<table border="1" class="historytable">';
			echo '<tr><td>';
			WriteEndorsersToAProposal($ProposalToStudy,$userid);
			echo '<br />';
			
			echo WriteProposalRelation($ProposalToStudy,$questionid,$GenerationToStudy,$userid,$room);
			$ProposalToMap=$ProposalToStudy;
			$ProposalToStudy=GetProposalDaughter($ProposalToStudy);			
			$GenerationToStudy+=1;					
			echo '</td><td>';
			//InsertMap($questionid,$GenerationToStudy-1,0,"S",$ProposalToMap);
			//			
			$graphsize = 'smallgraph';
			if ($filename = InsertMap2($questionid,$GenerationToStudy-1,0,"S",$ProposalToMap))
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
			//
			echo '</td></tr></table>';
		}
		
	}
/*
}
else
{
		header("Location: login.php");
}*/

include('footer.php');

?>