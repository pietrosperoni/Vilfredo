<?php
require_once 'config.inc.php';

if (empty($_POST['tv_invite_id']) || !ctype_digit($_POST['tv_invite_id']))
{
	set_log("invite_id not set - returning");
	echo "0";
	exit;
}

$invite_id = (int)$_POST['tv_invite_id'];

$ignore = ignoreInvite($invite_id);

if ($ignore)
{	
	echo '1';
	exit();
}
else
{
	set_log(__FILE__." failed to ignore invite");
	echo '0';
	exit();
}

?>