<?php
include('header.php');


#$userid=isloggedin();
if ($userid)
{
	$question = $_POST['question'];
	MoveOnToWriting($question);

	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);

#	$sql2 = "SELECT roundid, phase FROM questions WHERE id = ".$question." LIMIT 1 ";
#	$response2 = mysql_query($sql2);
#	while ($row2 = mysql_fetch_row($response2))
#	{
#		$phase =	$row2[1];
#		$generation = $row2[0];
#	}


#	if($phase==1)
#	{
#		$sql = "UPDATE questions SET phase = '0'  WHERE id = ".$question." ";
#		mysql_query($sql);
#		$sql = "UPDATE questions SET roundid = roundid+1 WHERE id = ".$question." ";
#		mysql_query($sql);
#		$sql = "UPDATE questions SET lastmoveon = NOW() WHERE id = ".$question." ";
#		mysql_query($sql);
#		SelectParetoFront($question);
#	}

#	SendMails($question);

	header("Location: viewquestion.php".$urlquery."");
}
else
{
		header("Location: login.php");
}
?>