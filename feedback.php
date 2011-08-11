<?php
include('config.inc.php');
if (defined('FEEDBACK_PAGE'))
	//header('Location: viewbubbles.php?q='.FEEDBACK_PAGE);
	header('Location: viewquestions.php?qb='.FEEDBACK_PAGE);
else
	echo 'Sorry, the feedback system is not currently operating.';

?>