<?php
include('header.php');


$userid=isloggedin();
if ($userid)
{	
	
	if (!get_magic_quotes_gpc()) 
	{
		$blurb = addslashes($_POST['blurb']);
	}
	else
	{
		$blurb = $_POST['blurb'];
	}
#	$blurb=nl2br($blurb);
	
	$question = $_POST['question'];
	$sql2 = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_row($response2))
	{		
		$roundid =	$row2[0];
	}
	$nAuthorsNewProposals=count(AuthorsOfNewProposals($question,$roundid));

	$sql = 'INSERT INTO `proposals` (`blurb`, `usercreatorid`, `roundid`, `experimentid`,`source`,`dominatedby`,`creationtime` ) VALUES (\'' . $blurb . '\', \'' . $userid . '\', \'' . $roundid . '\', \'' . $question . '\', \'0\',\'0\', NOW() );';
	mysql_query($sql);

	$NEWnAuthorsNewProposals=count(AuthorsOfNewProposals($question,$roundid));
	if(	$nAuthorsNewProposals< $NEWnAuthorsNewProposals)
	{
		AwareAuthorOfNewProposal($question);
	}

	header("Location: viewquestion.php?q=".$question."");
}
else
{
		header("Location: login.php");
}
?> 