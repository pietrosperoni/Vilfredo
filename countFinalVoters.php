<?php

require_once 'config.inc.php';

set_log(__FILE__." called....");


if (!isset($_POST['pids']))
{
	set_log(__FILE__ . ' called without parameters');
	echo "0";
	exit;
}

$pids = $_POST['pids'];
set_log('pids array passed = ');
set_log($pids);

$voters = countFinalVoters($pids);

if ($voters === false)
{
	echo 'error';
	exit();
}
else
{
	echo $voters;
	exit();
}
?>