<?php
include('header.php');


#$userid=isloggedin();
if ($userid)
{
	
	$blurb = $_POST['blurb'];
	$abstract = $_POST['abstract'];
	
	if ($abstract == '<br>') $abstract = '';
	if ($blurb == '<br>') $blurb = '';
	
	if (!get_magic_quotes_gpc())
	{
		$blurb = addslashes($blurb);
		$abstract = addslashes($abstract);
	}

	$question = $_POST['question'];

	$room = GetRoom($question);
	$urlquery = CreateQuestionURL($question, $room);

	$sql2 = "SELECT roundid FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response2 = mysql_query($sql2);
	while ($row2 = mysql_fetch_array($response2))
	{
		$roundid =	$row2[0];
	}
	$nAuthorsNewProposals=count(AuthorsOfNewProposals($question,$roundid));
	
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

	header("Location: viewquestion.php".$urlquery."");
}
else
{
		header("Location: login.php");
}
?>