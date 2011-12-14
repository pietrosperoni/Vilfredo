<?php
include('header.php');
include(BUBBLES_DIR.'/velocity_time.php');

if ($userid)
{
	$minimumtime=(int)$_POST['minimumtime'];
	$maximumtime=(int)$_POST['maximumtime'];
	
	$permit_anon_votes = isset($_POST['permit_anon_votes']) ? 1 : 0;
	$permit_anon_proposals = isset($_POST['permit_anon_proposals']) ? 1 : 0;

	// Equals empty string if field lift blank
	$room = '';
	// Alpha-numeric characters and underscores only.
	$room = FormatRoomId($_POST['room_id']);
	
	$questiontype = isset($_POST['questiontype']) ? $_POST['questiontype'] : 'question';

	$title = strip_tags($_POST['title']);
	
	if (!get_magic_quotes_gpc())
	{
		$blurb = addslashes($_POST['question']);
		$title = addslashes($title);
	}
	else
	{
		$blurb = $_POST['question'];
	}

	//*** Filter user HTML input
	//$htmlpurifierconfig = HTMLPurifier_Config::createDefault();
	//$htmlpurifierconfig->set('HTML.Doctype', 'HTML 4.01 Transitional');
	//$htmlpurifier = new HTMLPurifier($htmlpurifierconfig);

	//set_log($blurb);

	//$blurb = $htmlpurifier->purify($blurb);
	
	//set_log($blurb);

	if($blurb and $title)
	{
		if ($questiontype == "question")
		{
			$sql = "INSERT INTO `questions` (`question`, `roundid`, `phase` , `usercreatorid`, `title`, `lastmoveon`, `minimumtime`, `maximumtime`, `room`, `permit_anon_votes`, `permit_anon_proposals`) 
			VALUES ('$blurb', 1, 0, $userid, '$title', NOW(), $minimumtime, $maximumtime , '$room', $permit_anon_votes, $permit_anon_proposals)";

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
		else // Create new Bubble
		{
			$submitQuestion = submitQuestionWithoutGridUpdate($userid, $blurb, $title, $room);
			// Should now invite users to answer the new question bubble
			$newquestionid = mysql_insert_id();
			//set_log(__FILE__.' New question id = '.$newquestionid);
			
			updateQGrid($room);
			
			$urlquery = CreateQuestionBubbleQuery($newquestionid, $room);
			//set_log(__FILE__.' New question urlquery = '.$urlquery);
			//header("Location: invitetobubblequestion.php".$urlquery);
			header("Location: viewquestions.php".$urlquery);
			exit;
			//$roomparam = ($room == '') ? '' : '?room='.$room;
			//header("Location: viewquestions.php".$roomparam);
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