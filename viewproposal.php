<?php
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
		
		if (!empty($proposalabstract))
		{
			echo '<h3>Abstract:</h3>';
			echo "<p> $proposalabstract</p>";
		}
		
		echo '<h2>Proposed Answer</h2>';
		echo '<p>' . $proposaltext . '</p>';
		
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
		echo "in ".WriteGenerationPage($questionid,$OPropGen).".<br>";

		$ProposalToStudy=$OPropID;
		$GenerationToStudy=$OPropGen;
		while($ProposalToStudy)
		{
			if ($questionround<=$GenerationToStudy){break;}			
			echo '<h4>'.WriteGenerationPage($questionid,$GenerationToStudy).'</h4>';
			echo '<table border="1" class="historytable">';
			echo '<tr><td>';
			WriteEndorsersToAProposal($ProposalToStudy,$userid);
			echo '<br />';
			
			echo WriteProposalRelation($ProposalToStudy,$questionid,$GenerationToStudy,$userid,$room);
			$ProposalToMap=$ProposalToStudy;
			$ProposalToStudy=GetProposalDaughter($ProposalToStudy);			
			$GenerationToStudy+=1;					
			echo '</td><td>';
			InsertMap($questionid,$GenerationToStudy-1,0,"S",$ProposalToMap);
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