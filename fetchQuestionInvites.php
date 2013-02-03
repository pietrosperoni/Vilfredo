<?php
require_once 'config.inc.php';

if (empty($_POST['tv_userid']) || !ctype_digit($_POST['tv_userid']))
{
	set_log("userid not set - returning");
	echo "0";
	exit;
}

$userid = (int)$_POST['tv_userid'];

$invites = getQuestionInvites($userid);

if ($invites)
{	
	set_log("invites returned with ". count($messages)." elements...");
	echo json_encode($invites);
	exit();
}
else
{
	set_log(__FILE__." failed to return invites");
	echo '0';
	exit();
}

?>