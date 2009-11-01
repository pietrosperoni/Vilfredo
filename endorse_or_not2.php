<?php
include('header.php');

$userid=isloggedin();
if ($userid)
{	
	

	$endorsedproposals = $_POST['proposal'];
	if(!$endorsedproposals){$endorsedproposals=array();}
	$question = $_POST['question'];

	$sql2 = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_row($response2))
	{		
		$roundid =	$row2[0];
	}
	$nEndorsers=CountEndorsers($question,$roundid);

	$allproposals=array();	
	$sql = "SELECT  id FROM proposals WHERE  proposals.experimentid = " . $question . " AND proposals.roundid= ".$roundid." ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))	
	{	
		array_push($allproposals,$row[0]);
	}

	foreach ($allproposals as $p)
	{
		$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $p . " LIMIT 1";
		if(mysql_fetch_row(mysql_query($sql)))
		{
			if (!in_array($p,$endorsedproposals))
			{
				$sql = "DELETE FROM `endorse` WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $p . " ";
				mysql_query($sql);		
			}
		}
		else
		{
			if (in_array($p,$endorsedproposals))
			{
				$sql = 'INSERT INTO `endorse` (`userid`, `proposalid`, `endorsementdate` ) VALUES (\'' . $userid . '\', \'' . $p. '\',NOW() );';
				mysql_query($sql);
			}
		}
	}
	
	$NEWnEndorsers=CountEndorsers($question,$roundid);
	if(	$nEndorsers< $NEWnEndorsers)
	{
		AwareAuthorOfNewEndorsement($question);
	}
	
	
	
	header("Location: viewquestion.php?q=".$question."");
}
else
{
		header("Location: login.php");
}
?> 