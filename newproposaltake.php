<?php
include('header.php');

// User is anonymous if anon checkbox has been clicked (is defined)
$is_anon = isset($_POST['anon']);
$userid=isloggedin();

if ($is_anon)
{
	set_log("Form submitted anonymously");
	// userid should be false
	if ($userid)
	{
		set_log(" User $userid submitted anonymously whilst logged in!");
	}
}

$blurb = $_POST['blurb'];
$abstract = $_POST['abstract'];

if ($abstract == '<br>') $abstract = '';
if ($blurb == '<br>') $blurb = '';

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

$question = $_POST['question'];

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
		$userid = getAnonymousUserForNewProposal($authors);
	}

	$sql = "INSERT INTO `proposals` (`blurb`, `usercreatorid`, `roundid`, `experimentid`,`source`,`dominatedby`,`creationtime`, `abstract` ) 
	VALUES ('$blurb', $userid, $roundid, $question, 0, 0, NOW(), '$abstract')";
	$add_proposal = mysql_query($sql);

	if (!$add_proposal)
	{
		handle_db_error($add_proposal);
		set_message("error", "System error");
	}

	$NEWnAuthorsNewProposals=count(AuthorsOfNewProposals($question,$roundid));
	if(	$nAuthorsNewProposals< $NEWnAuthorsNewProposals)
	{
		AwareAuthorOfNewProposal($question);
	}

	
	if ($is_anon)
	{
		$urlquery = "?anon=$userid&query=viewquestion.php".$urlquery;
		header("Location: anonnewpropfeedback.php".$urlquery);
	}
	else
	{
		header("Location: viewquestion.php".$urlquery);
	}
}
	
?>