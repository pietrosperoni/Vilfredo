<?php
include('header.php');
#$userid=isloggedin();
if ($userid)
{
	$minimumtime=(int)$_POST['minimumtime'];
	$maximumtime=(int)$_POST['maximumtime'];

	// Equals empty string if field lift blank
	$room = '';
	// Alpha-numeric characters and underscores only.
	$room = FormatRoomId($_POST['room_id']);

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
		$sql = 'INSERT INTO `questions` (`question`, `roundid`, `phase` , `usercreatorid`, `title`, `lastmoveon`, `minimumtime`, `maximumtime`, `room`   ) VALUES (\'' . $blurb . '\', \'1\', \'0\', \''.$userid.'\',\'' . $title . '\', NOW(),\''.$minimumtime.'\',\''.$maximumtime.'\',\''.$room.'\' );';
                mysql_query($sql);
                set_log($sql);

		$sql = "SELECT id FROM questions WHERE usercreatorid = ".$userid." ORDER BY questions.id DESC LIMIT 1 ";
		$response = mysql_query($sql);
		$row = mysql_fetch_array($response);

                $urlquery = CreateQuestionURL($row[0], $room);

                header("Location: invitetoquestion.php".$urlquery);
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