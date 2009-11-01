<?php
include('header.php');
$userid=isloggedin();
if ($userid)
{

$minimumtime=(int)$_POST['minimumtime'];
$maximumtime=(int)$_POST['maximumtime'];

	if (!get_magic_quotes_gpc()) 
	{
		$blurb = addslashes($_POST['question']);
		$title = addslashes($_POST['title']);
	}
	else
	{
		$blurb = $_POST['question'];
		$title = $_POST['title'];
	}
#	$blurb=nl2br($blurb);

	
	if($blurb and $title)
	{
		$sql = 'INSERT INTO `questions` (`question`, `roundid`, `phase` , `usercreatorid`, `title`, `lastmoveon`, `minimumtime`, `maximumtime`   ) VALUES (\'' . $blurb . '\', \'1\', \'0\', \''.$userid.'\',\'' . $title . '\', NOW(),\''.$minimumtime.'\',\''.$maximumtime.'\' );';
		mysql_query($sql);
		
		$sql = "SELECT id FROM questions WHERE usercreatorid = ".$userid." ORDER BY questions.id DESC LIMIT 1 ";
		$response = mysql_query($sql);
		$row = mysql_fetch_row($response);
		header("Location: invitetoquestion.php?q=".$row[0]."");

	}
	else
	{
		header("Location: viewquestions.php");
	}
}
else
{
	header("Location: login.php");
}
?> 