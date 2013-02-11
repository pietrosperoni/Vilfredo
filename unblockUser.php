<?php
require_once 'config.inc.php';

if (empty($_POST['tv_from_user']) || !ctype_digit($_POST['tv_from_user']) 
|| empty($_POST['tv_to_user']) || !ctype_digit($_POST['tv_to_user']))
{
	set_log("userid or question not set - returning");
	echo "0";
	exit;
}

$from_user = (int)$_POST['tv_from_user'];
$to_user = (int)$_POST['tv_to_user'];

$unblocked = unblockUserInvites($from_user, $to_user);

if ($unblocked)
{	
	echo '1';
	exit();
}
else
{
	set_log(__FILE__." failed to unblock user");
	echo '0';
	exit();
}

?>