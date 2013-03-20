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
$proposal = (int)$_POST['proposal'];

$abstract = trim($abstract);

if ($abstract == '<br>')
{
	$abstract = '';
}

$blurb = trim($blurb);

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


$previousProposal=HasProposalBeenSuggested($proposal, $question,$blurb,$abstract);
if($previousProposal)
{
	echo "sorry, it looks like the proposal has already been suggested.";
}
else
{
	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);
	
	if ($is_anon)
	{
		
		//$wait = getDelayForRemoteIP();
		//$wait_str = formatSeconds($wait);
		//set_log("Time remaining before next request for this IP is $wait_str");
		$userid = getAnonymousUser($question);
		//logUser($userid);
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
		printbrx("Error: Could not create anonymous user!");
	}
	
	$sql = "UPDATE `proposals` SET `blurb` = '$blurb', `abstract` = '$abstract' WHERE `id` = $proposal";
	
	$edit_proposal = mysql_query($sql);

	if (!$edit_proposal)
	{
		handle_db_error($add_proposal);
		set_message("error", "System error");
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