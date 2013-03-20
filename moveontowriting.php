<?php
include('header.php');

if ($userid)
{
	$question = $_POST['question'];
	MoveOnToWriting($question);

	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);

	header("Location: viewquestion.php".$urlquery);
	exit;
}
else
{
	header("Location: login.php");
	exit;
}
?>