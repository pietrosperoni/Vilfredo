<?php
include('header.php');

set_log($_POST);

$question = fetchValidIntValFromPostWithKey('question');
if ($question === false)
{
	header("Location: error_page.php");
	exit;
}

//set_log("Question $question");

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
	$sql = "SELECT * FROM updates WHERE question = ".$question." AND user = ".$userid." LIMIT 1 ";
	$response = mysql_query($sql);
	$row = mysql_fetch_array($response);
	if ($row)
	{
		if(!$is_subscribe)
		{
			$sql = "DELETE FROM updates WHERE updates.question = " . $question . " AND updates.user = " . $userid . "  ";
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


$prev_endorsedproposals = (isset($_POST['prev_proposal'])) ? $_POST['prev_proposal'] : array();

$endorsedproposals = $_POST['proposal'];
if(!$endorsedproposals)
{
	$endorsedproposals = array();
}

$roundid;
$sql2 = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1 ";
$response2 = mysql_query($sql2);
while ($row2 = mysql_fetch_array($response2))
{		
	$roundid =	$row2[0];
}

DeleteGraph($question,$roundid);

$nEndorsers=CountEndorsers($question,$roundid);
$allproposals = getCurrentProposalIDs($question, $roundid);

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
	set_logx("Error: Could not create anonymous user!");
}

$currentuserendorsements = getUserEndorsedFromList($userid, $allproposals);
set_log("currentuserendorsements");
set_log($currentuserendorsements);

$user_comment = (isset($_POST['user_comment'])) ? $_POST['user_comment'] : array();

foreach ($allproposals as $p)
{
	//set_log("Proposal $p ==> {$endorsedproposals[$p]}");
	
	if (isset($prev_endorsedproposals[$p]) && $endorsedproposals[$p] == $prev_endorsedproposals[$p])
	{
		set_log("No change for proposal $p - continue...");
		continue;
	}
	
	if ($endorsedproposals[$p] == "1" && !in_array($p, $currentuserendorsements))
	{
		set_log("Adding endorsement for proposal $p...");
		addEndorsement($userid, $p);
		set_log("Deleting comment for proposal $p...");
		deleteComment($userid, $p);
	}
	elseif ($endorsedproposals[$p] == "2")
	{
		if (in_array($p, $currentuserendorsements))
		{
			set_log("Deleting endorsement for proposal $p...");
			deleteEndorsement($userid, $p);
		}
		
		$comment = (isset($user_comment[$p])) ? $user_comment[$p] : '';
		set_log("Dislike comment for proposal $p is $comment...");
		//if (!empty($comment))
		//{
			addComment($userid, $p, $comment, 'dislike');
		//}
	}
	elseif ($endorsedproposals[$p] == "3")
	{
		if (in_array($p, $currentuserendorsements))
		{
			deleteEndorsement($userid, $p);
		}		
		
		$comment = (isset($user_comment[$p])) ? $user_comment[$p] : '';
		set_log("Dislike comment for proposal $p is $comment...");
		//if (!empty($comment))
		//{
			addComment($userid, $p, $comment, 'confused');
		//}
	}	
}

$hasvoted = (int)$_POST['hasvoted'];

if (!$hasvoted)
{
	setuservoted($userid, $question, $roundid);
}	
	
$NEWnEndorsers=CountEndorsers($question,$roundid);
//DeleteGraph($question,$roundid);
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
	header("Location: viewquestion.php".$urlquery."#Voted");
}
?>