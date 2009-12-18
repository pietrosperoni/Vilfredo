<?php
include('header.php');
$sql = "SELECT *  FROM questions; ";
$response = mysql_query($sql);
while ($row = mysql_fetch_array($response))
{
	$question=$row[0];
	$generation=$row[2];
	$phase=$row[3];
	$ReadyToAutoMoveOn=IsQuestionReadyToAutoMoveOn($question,$phase,$generation);
	if($ReadyToAutoMoveOn)
	{	if($phase)	{MoveOnToWriting($question);}
		else		{MoveOnToEndorse($question);}
		break;
	}
}
?> 