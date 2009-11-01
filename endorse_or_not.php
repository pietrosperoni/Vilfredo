<?php
include('header.php');


$userid=isloggedin();
if ($userid)
{	
	$proposal = $_POST['proposal'];
	
	$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $proposal . " LIMIT 1";
	if(mysql_fetch_row(mysql_query($sql)))
	{
		$sql = "DELETE FROM `endorse` WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $proposal . " ";
		mysql_query($sql);
	}
	else
	{
		$sql = 'INSERT INTO `endorse` (`userid`, `proposalid`) VALUES (\'' . $userid . '\', \'' . $proposal. '\');';
		mysql_query($sql);
	}
	$sql = "SELECT  experimentid FROM proposals WHERE  id = " . $proposal . " LIMIT 1";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		header("Location: viewquestion.php?q=".$row[0]."");
		
	}
//	header("Location: viewproposal.php?p=".$proposal."");
//	echo $row ;
}
else
{
		header("Location: login.php");
}
?> 