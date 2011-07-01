<?php
include('config.inc.php');
if (defined('FEEDBACK_PAGE'))
	header('Location: viewbubbles.php?q='.FEEDBACK_PAGE);
else
	echo 'FEEDBACK_PAGE not defined in conf.domain.php. Set to bubble question ID corresponding to feedback question.<br/><br/>eg define("FEEDBACK_PAGE", 2);';

?>