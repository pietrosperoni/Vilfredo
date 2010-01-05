<?php
include('header.php');


#$userid=isloggedin();
if ($userid)
{
	$question = $_POST['question'];
	$room = $_POST['room'];
	
	
	if ($room==GetQuestionRoom($question))
	{
	
		$urlquery = CreateQuestionURL($question, $room);

		$sql = "SELECT * FROM updates WHERE question = ".$question." AND  user = ".$userid." LIMIT 1 ";
		$response = mysql_query($sql);
		$row = mysql_fetch_array($response);

		if ($row)
		{
			$sql = "DELETE FROM updates WHERE  updates.question = " . $question . " AND updates.user = " . $userid . "  ";
			if (!mysql_query($sql)) error("Database update failed");
			#mysql_query($sql);
		}else{
			$how="asap";
			$sql = 'INSERT INTO `updates` (`user`, `question`, `how`) VALUES (\'' . $userid . '\', \'' . $question . '\', \'' . $how . '\');';
			if (!mysql_query($sql)) error("Database update failed");
			#mysql_query($sql);
		}
	}
	header("Location: viewquestion.php".$urlquery);
}
else
{
		header("Location: login.php");
}
?>