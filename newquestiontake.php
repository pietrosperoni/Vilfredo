<?php
include('header.php');

if ($userid)
{
	if (hasTags($_POST['title']) || hasTags($_POST['room_id']))
	{
		header("Location: error_page.php");
		exit;
	}
	
	$minimumtime=(int)$_POST['minimumtime'];
	$maximumtime=(int)$_POST['maximumtime'];
	
	$permit_anon_votes = isset($_POST['permit_anon_votes']) ? 1 : 0;
	$permit_anon_proposals = isset($_POST['permit_anon_proposals']) ? 1 : 0;

	// Equals empty string if field lift blank
	$room = '';
	// Alpha-numeric characters and underscores only.
	$room = FormatRoomId($_POST['room_id']);
	$title = GetMySQLEscapedPostParam('title');
	$blurb = GetMySQLEscapedPostParam('question');
	
	$mysql = array();
	$mysql['room'] = mysql_real_escape_string($room);
	
	//*** Filter user HTML input
	//$htmlpurifierconfig = HTMLPurifier_Config::createDefault();
	//$htmlpurifierconfig->set('HTML.Doctype', 'HTML 4.01 Transitional');
	//$htmlpurifier = new HTMLPurifier($htmlpurifierconfig);

	//set_log($blurb);

	//$blurb = $htmlpurifier->purify($blurb);
	
	//set_log($blurb);

	if($blurb and $title)
	{
		$sql = "INSERT INTO `questions` (`question`, `roundid`, `phase` , `usercreatorid`, `title`, `lastmoveon`, `minimumtime`, `maximumtime`, `room`, `permit_anon_votes`, `permit_anon_proposals`) 
		VALUES ('$blurb', 1, 0, $userid, '$title', NOW(), $minimumtime, $maximumtime , '{$mysql['room']}', $permit_anon_votes, $permit_anon_proposals)";

		if (!mysql_query($sql))
		{
			db_error($sql);
			set_log("Failed to create new question titled $title");
		}
		else
		{
			$newquestionid = mysql_insert_id();
			$urlquery = CreateQuestionURL($newquestionid, $room);
			header("Location: invitetoquestion.php".$urlquery);
		}
		
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