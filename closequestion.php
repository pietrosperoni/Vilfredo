<?php
include('header.php');

if ($userid)
{
	$question = $_POST['question'];
	CloseQuestion($userid, $question);

	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);

	header("Location: viewvotingresults.php".$urlquery);
	exit;
}
else
{
	header("Location: login.php");
	exit;
}
?>