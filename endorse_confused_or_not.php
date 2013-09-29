<?php
include('header.php');

//set_log(__FILE__." called....");

//set_log($_POST);

//print_array($_POST);


if (isset($_POST['proposal']) and isset($_POST['prev_proposal']))
{
	foreach ($_POST['proposal'] as $key => $value)
	{
		if ($_POST['proposal'][$key] != $_POST['prev_proposal'][$key])
		{
			set_log("Vote for $key changed...");
			$_SESSION["updatemap"] = true;
			break;
		}
	}
}

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

$generation = (int)$_POST['generation'];

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

$origids = getOriginalIDs($allproposals);
//set_log('$origids');
//set_log($origids);

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
	set_log("Error: Could not create anonymous user!");
}

$currentuserendorsements = getUserEndorsedFromList($userid, $allproposals);
//set_log("currentuserendorsements");
//set_log($currentuserendorsements);
$user_comment = (isset($_POST['user_comment'])) ? $_POST['user_comment'] : array();
$select_comment = (isset($_POST['select_comment'])) ? $_POST['select_comment'] : array();
$prev_proposal = (isset($_POST['prev_proposal'])) ? $_POST['prev_proposal'] : array();

$endorsedproposals = (isset($_POST['proposal'])) ? $_POST['proposal'] : array();
$prev_commentid = (isset($_POST['prev_commentid'])) ? $_POST['prev_commentid'] : array();


// Set endorse, oppoed and comments
//
foreach ($allproposals as $p)
{
	//set_log("Processing proposal $p....");
	if 
	( 
		( isset($prev_proposal[$p]) && ($endorsedproposals[$p] != $prev_proposal[$p]) ) ||
		( isset($user_comment[$p]) && empty($user_comment[$p]) == false ) ||
		( isset($prev_commentid[$p]) && isset($select_comment[$p]) && $prev_commentid[$p] != $select_comment[$p] ) ||
		( isset($select_comment[$p]) )
	)
	{
		// 
		// User endorses
		//
		
		//set_log("prev_proposal is " . $prev_proposal[$p]);
		
		if ($endorsedproposals[$p] == "1" && !in_array($p, $currentuserendorsements))
		{
			//set_log("Adding endorsement for proposal $p...");
			addEndorsement($userid, $p);
			
			if ($prev_proposal[$p] == '2' || $prev_proposal[$p] == '3')
			{	// Delete previous oppose entry
				//set_log("Delete previous oppose entry for proposal $p...");
				deleteUserOppose($userid, $p, $generation);
				// Delete comment if no-one else supports it
				if (isset($prev_commentid[$p]))
				{
					//set_log("Try to delete comment {$prev_commentid[$p]} for proposal $p...");
					deleteComment((int)$prev_commentid[$p]);
				}
			}
		}
		// User Opposes
		elseif ($endorsedproposals[$p] == "2" || $endorsedproposals[$p] == "3")
		{
			//set_log("User added or changed Oppose and/or Comment...");
		
			if (in_array($p, $currentuserendorsements))
			{
				deleteEndorsement($userid, $p);
			}
		
			// Set oppose type
			$type;
			if ($endorsedproposals[$p] == "2")
			{
				$type = 'dislike';
			}
			else
			{
				$type = 'confused';
			}
			$type_change = $type != $prev_proposal[$p];
		
			// Check for comment
			// ( No comment set to 0 )
			$previous_commentid = (int)$prev_commentid[$p];
			$commentid = 0;
		
			if (array_key_exists($p, $select_comment))
			{
				$commentid = (int)$select_comment[$p];
			}
			elseif (array_key_exists($p, $user_comment) && !empty($user_comment[$p]))
			{
				$new_comment = $user_comment[$p];
			
				$commentid = commentExists($p, $generation, $type, $new_comment);
				//set_log("ID of comment search = $commentid");
				if (!$commentid)
				{
					//set_log("Adding new comment {$user_comment[$p]}");
					//set_log("New comment not found - adding to comments for prop ID $p and origid {$origids[$p]}");
					$commentid = addComment($userid, $p, $type, $generation, $user_comment[$p], $origids[$p]); // addorigid
				}
			}
		
			//set_log("Set user oppose: ");
			// Add new oppose entry or update type and commentid of existing one
			setUserOppose($userid, $p, $type, $generation, $origids[$p], $commentid); // addorigid
			
			if ($commentid != $previous_commentid)
			{
				// Delete comment if no-one else supports it
				//set_log("comment id = $commentid and previous comment id = {$previous_commentid} - try and delete comment...");
				deleteComment($previous_commentid);
			}
		}
	}
	else
	{
		if ($endorsedproposals[$p] == "1")
		{
			//set_log("No change for endorsed proposal $p - continue...");
		}
		else
		{
			//set_log("No change for opposed proposal $p - continue...");
		}
		continue;
	}	
}

/*
$hasvoted = (int)$_POST['hasvoted'];
if (!$hasvoted)
{
	setuservoted($userid, $question, $roundid);
}	*/
	
$NEWnEndorsers=CountEndorsers($question,$roundid);
//DeleteGraph($question,$roundid);
if(	$nEndorsers < $NEWnEndorsers)
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