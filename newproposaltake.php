<?php
include('header.php');

$question = $_POST['question'];

// User is anonymous if anon checkbox has been clicked (is defined)
$is_anon = isset($_POST['anon']);

$userid=isloggedin();

if ($is_anon and $userid)//DEBUG START
{
	// userid should be false
	set_log(" User $userid submitted anonymously whilst logged in!");
}// DEBUG END

$blurb = $_POST['blurb'];
$abstract = $_POST['abstract'];

if ($abstract == '<br>') $abstract = '';
if ($blurb == '<br>') $blurb = '';

if (!IsQuestionWriting($question))
{
	/*
	set_message("user", "Sorry, question $question now in voting stage.");
	$proposal_str = '<h3>Abstract</h3>' .
					"<p>$abstract</p>" .
					'<h3>Proposal</h3>' .
					"<p>$proposal</p>";
	set_message("user", $proposal_str);
	header("Location: messagepage.php?q=$question");*/
	header("Location: viewquestion.php?q=$question");
	exit;
}


if (!get_magic_quotes_gpc())
{
	$blurb = addslashes($blurb);
	$abstract = addslashes($abstract);
}

//*** Filter user input
//$config = HTMLPurifier_Config::createDefault();
#	$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
//$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
//$purifier = new HTMLPurifier($config);

//$xsstest= '<SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT> <p>Hi there from the XSS Test. Is it safe?</p>';

//$abstract = $purifier->purify($abstract);
//$blurb = $purifier->purify($blurb);


$previousProposal=HasProposalBeenSuggested($question,$blurb,$abstract);
if($previousProposal)
{
	echo "sorry, it looks like the proposal has already been suggested.";
}
else
{
	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);

	$sql2 = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_array($response2))
	{
		$roundid =	$row2[0];
	}
	$authors = AuthorsOfNewProposals($question,$roundid);
	$nAuthorsNewProposals=count($authors);
	
	if ($is_anon)
	{
		
		//$wait = getDelayForRemoteIP();
		//$wait_str = formatSeconds($wait);
		//set_log("Time remaining before next request for this IP is $wait_str");
		$userid = getAnonymousUser($question);
		logUser($userid);
		/*
		if ($wait > 0)
		{
			set_message("user", "Posting quota is in place. You are limited from posting again for a short while: $wait_str");
			$urlquery = "?query=viewquestion.php".$urlquery;
			header("Location: messagepage.php".$urlquery);
			exit;
		}
		else
		{
			$userid = getAnonymousUser($question);
			logUser($userid);
		}*/
	}
	
	if (!$userid)
	{
		printbrx("Error: Could not creat anonymous user!");
	}

	$sql = "INSERT INTO `proposals` (`blurb`, `usercreatorid`, `roundid`, `experimentid`,`source`,`dominatedby`,`creationtime`, `abstract` ) 
	VALUES ('$blurb', $userid, $roundid, $question, 0, 0, NOW(), '$abstract')";
	
	$add_proposal = mysql_query($sql);

	if (!$add_proposal)
	{
		handle_db_error($add_proposal);
		set_message("error", "System error");
	}
	
	$newpropid = mysql_insert_id();

	$NEWnAuthorsNewProposals=count(AuthorsOfNewProposals($question,$roundid));
	if(	$nAuthorsNewProposals< $NEWnAuthorsNewProposals)
	{
		AwareAuthorOfNewProposal($question);
	}

	if ($is_anon)
	{		
		$urlquery = "?anon=$userid&prop=$newpropid&query=viewquestion.php".$urlquery;
		header("Location: anonnewpropfeedback.php".$urlquery);
	}
	else
	{
		header("Location: viewquestion.php".$urlquery);
	}
}
	
?>