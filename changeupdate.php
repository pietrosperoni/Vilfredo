<?php
include('header.php');


$userid=isloggedin();
if ($userid)
{	
	$question = $_POST['question'];
	
	$sql = "SELECT * FROM updates WHERE question = ".$question." AND  user = ".$userid." LIMIT 1 ";
	$response = mysql_query($sql);
	$row = mysql_fetch_row($response);
	
	if ($row)
	{
	
		$sql = "DELETE FROM updates WHERE  updates.question = " . $question . " AND updates.user = " . $userid . "  ";
#		echo $sql;
		mysql_query($sql);
	}else{
		$how="asap";
		$sql = 'INSERT INTO `updates` (`user`, `question`, `how`) VALUES (\'' . $userid . '\', \'' . $question . '\', \'' . $how . '\');';
#		echo $sql;
		mysql_query($sql);
	}
	header("Location: viewquestion.php?q=".$question."");
}
else
{
		header("Location: login.php");
}
?> 