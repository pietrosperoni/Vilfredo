<?php
include('header.php');


#$userid=isloggedin();
if ($userid)
{
	$question = $_POST['question'];
	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);

	$sql2 = "SELECT roundid, phase FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_array($response2))
	{
		$phase =	$row2[1];
	}

	if($phase==0)
	{

		$sql = "UPDATE questions SET phase = '1' WHERE id = ".$question." ";
		mysql_query($sql);
	}

	$sql = "UPDATE questions SET lastmoveon = NOW() WHERE id = ".$question." ";
	mysql_query($sql);

	SendMails($question);
	header("Location: viewquestion.php".$urlquery."");
}
else
{
		header("Location: login.php");
}
?>