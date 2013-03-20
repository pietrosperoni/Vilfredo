<?php
require_once 'config.inc.php';

// Check that users is logged in
$userid = isloggedin();
if (!$userid)
{
	set_log(__FILE__ . ': User not logged in');
	echo "0";
	exit();
}

if (empty($_POST['pid']) || empty($_POST['question']) || empty($_POST['generation']))
{
	set_log(__FILE__ . ': parameters not set');
	echo "0";
	exit();
}

$pid = (int)GetEscapedPostParam('pid');
$question = (int)GetEscapedPostParam('question');
$generation = (int)GetEscapedPostParam('generation');

$deleteproposal = deleteProposal($pid, $userid);

if ($deleteproposal)
{	
	$results = array();
	$results['numauthors'] = CountAuthorsOfNewProposals($question,$generation);
	$results['numproposals'] = CountProposals($question,$generation);
	echo json_encode($results);
	//echo "1";
	exit();
}
else 
{
	set_log(__FILE__." Failed to delete proposal");
	echo "0";
	exit();
}
?>