<?php
require_once 'config.inc.php';

if (empty($_POST['tv_userid']) || !ctype_digit($_POST['tv_userid']))
{
	set_log("userid not set - returning");
	echo "0";
	exit;
}

$userid = (int)$_POST['tv_userid'];

$messages = getSystemMessages($userid);

if ($messages)
{	
	//set_log("Messages returned with ". count($messages)." elements...");
	echo json_encode($messages);
	exit();
}
else
{
	set_log(__FILE__." failed to return messages");
	echo '0';
	exit();
}

?>