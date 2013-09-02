<?php

require_once 'vga_functions.php';

if (!isset($_POST['question']))
{
	set_log(__FILE__ . ' called without parameters');
	echo "0";
	exit;
}

if ((int)$_POST['question'] == 0)
{
	set_log(__FILE__ . ' called with question set to zero');
	set_log("question ".$_POST['question']);
	echo "0";
	exit;
}

$question = (int)$_POST['question'];

$exists = checkQuestionExists($question);

if ($exists == 0)
{
	set_log("Question $question not found - returning code 3");
	echo "3";
	exit;
}

$phase = getQuestionPhase($question);

if ($phase === false)
{
	echo '0';
	exit();
}
else
{
	echo $phase;
	exit();
}
?>