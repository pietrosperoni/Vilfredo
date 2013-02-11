<?php
require_once 'config.inc.php';

if (empty($_POST['tv_userid']) || !ctype_digit($_POST['tv_userid']) 
|| empty($_POST['tv_question']) || !ctype_digit($_POST['tv_question']))
{
	set_log("userid or question not set - returning");
	echo "0";
	exit;
}

$userid = (int)$_POST['tv_userid'];
$question = (int)$_POST['tv_question'];

$unsubscribed = questionUnsubscribe($userid, $question);

if ($unsubscribed)
{	
	echo '1';
	exit();
}
else
{
	set_log(__FILE__." failed to unsubscribe");
	echo '0';
	exit();
}

?>