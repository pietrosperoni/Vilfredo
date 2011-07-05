<?php
include('header.php');

$question = $_POST['question'];

if (IsQuestionWriting($question))
{
	//set_message("user", "Sorry, question $question now in writing stage.");
	//header("Location: messagepage.php?q=$question");
	header("Location: viewquestion.php?q=$question");
	exit;
}

// User is anonymous if anon checkbox has been clicked (is defined)
$is_anon = isset($_POST['anon']);

$userid=isloggedin();

if ($is_anon)
{
	//set_log("Form submitted anonymously");
	// userid should be false
	if ($userid)
	{
		set_log(" User $userid submitted anonymously whilst logged in!");
	}
}


$is_subscribe = isset($_POST['subscribe']);

if ($userid) {
	$sql = "SELECT * FROM updates WHERE question = ".$question." AND  user = ".$userid." LIMIT 1 ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	if ($row)
	{
		if(!$is_subscribe)
		{
			$sql = "DELETE FROM updates WHERE  updates.question = " . $question . " AND updates.user = " . $userid . "  ";
			if (!mysql_query($sql)) error("Database update failed");				
		}
	}
	else
	{
		if($is_subscribe)
		{
			$how="asap";
			$sql = 'INSERT INTO `updates` (`user`, `question`, `how`) VALUES (\'' . $userid . '\', \'' . $question . '\', \'' . $how . '\');';
			if (!mysql_query($sql)) error("Database update failed");				
		}
	}
}



$endorsedproposals = $_POST['proposal'];
if(!$endorsedproposals){$endorsedproposals=array();}

$sql2 = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1 ";
$response2 = mysql_query($sql2);
while ($row2 = mysql_fetch_array($response2))
{		
	$roundid =	$row2[0];
}
$nEndorsers=CountEndorsers($question,$roundid);

$allproposals=array();	
$sql = "SELECT  id FROM proposals WHERE  proposals.experimentid = " . $question . " AND proposals.roundid= ".$roundid." ";
$response = mysql_query($sql);
while ($row = mysql_fetch_array($response))	
{	
	array_push($allproposals,$row[0]);
}

if ($is_anon)
{
	//$userid = getAnonymousUserForVoting($allproposals);
	$userid = getAnonymousUser($question);
	
	//$wait = getDelayForRemoteIP();
	//set_log("Delay for this user should be $wait seconds");
	//logUser($userid);
}

if (!$userid)
{
	printbrx("Error: Could not create anonymous user!");
}

foreach ($allproposals as $p)
{
	$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $p . " LIMIT 1";
	if(mysql_fetch_array(mysql_query($sql)))
	{
		if (!in_array($p,$endorsedproposals))
		{
			$sql = "DELETE FROM `endorse` WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $p . " ";
			mysql_query($sql);		
		}
	}
	else
	{
		if (in_array($p,$endorsedproposals))
		{
			$sql = 'INSERT INTO `endorse` (`userid`, `proposalid`, `endorsementdate` ) VALUES (\'' . $userid . '\', \'' . $p. '\',NOW() );';
			mysql_query($sql);
		}
	}
}

$NEWnEndorsers=CountEndorsers($question,$roundid);
if(	$nEndorsers< $NEWnEndorsers)
{
	AwareAuthorOfNewEndorsement($question);
}

$room = GetRoom($question);
$urlquery = CreateQuestionURL($question, $room);

if ($is_anon)
{
	$urlquery = "?anon=$userid&query=viewquestion.php".$urlquery;
	header("Location: anonvotesfeedback.php".$urlquery);
}
else
{
	header("Location: viewquestion.php".$urlquery);
}
?> 