<?php

require_once 'config.inc.php';

//echo '0';
//exit;

set_log(__FILE__.": called...");

if (!isset($_POST['tv_vote']))
{
	set_log(__FILE__ . ' no vote passed');
	echo '0';
	exit();
}
elseif (empty($_POST['tv_userid']) || empty($_POST['tv_pid']) || empty($_POST['question']))
{
	set_log(__FILE__ . ' parameters not set');
	echo "0";
	exit();
}

$question = (int)$_POST['question'];
$phase = getQuestionPhase($question);
set_log("Question phase = $phase");
if ($phase == 'closed')
{
	set_log(__FILE__ . ' question now closed');
	echo "closed";
	exit();
}
elseif ($phase == 'evaluating')
{
	set_log(__FILE__ . ' question now evaluating');
	echo "evaluating";
	exit();
}

$userid = (int)GetEscapedPostParam('tv_userid');
$pid = (int)GetEscapedPostParam('tv_pid');
$vote = (int)GetEscapedPostParam('tv_vote');

$hasvoted = hasUserVotedForPropFinal($pid, $userid);

if ($hasvoted === FALSE)
{
	set_log(__FILE__ . ' hasUserVotedFinal returned FALSE');
	echo "0";
	exit();
}

$submitVote = false;

if ($hasvoted == 1)
{
	set_log("User $userid has already voted for question $question. Updating votes...");
	$submitVote = updateUserFinalVote($userid, $pid, $vote);
}
else
{
	set_log("User $userid has not yet voted for question $question. Adding votes...");
	$submitVote = setUserFinalVote($userid, $pid, $vote);
}

if ($submitVote)
{	
	echo '1';
	exit();
}
else
{
	set_log(__FILE__." Could not submit vote - check for DB error");
	echo '0';
	exit();
}
?>