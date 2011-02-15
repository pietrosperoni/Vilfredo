<?php
include 'header.php';

$urlquery = $_GET['query'];
$msg = $_GET['msg'];
$question = $_GET['q'];

/*
if (isset($msg))
{
	echo "<h3>Vilfredo says...</h3>";
	echo "<p>$msg</p>";
}*/
if (messages_set())
{
	echo "<h3>Vilfredo says...</h3>";
	
	if (countMessages('user') > 0)
	{
		$messages = get_messages('user');
		foreach($messages as $msg)
		{
			echo "<p>$msg</p>";
		}
	}

	if (countMessages('error') > 0)
	{
		$errors = get_messages('error');
		foreach($messages as $msg)
		{
			echo "<p>$msg</p>";
		}
	}
	clear_messages();
}

/*
else
{
	echo "<p>We're sorry but there was a problem with your request. Please try again later.</p>";
}*/

if (isset($urlquery))
{
	echo "<p><a href=\"" . SITE_DOMAIN. "/" . $urlquery . "\">Click here to continue</a></p>";
}

elseif (isset($question))
{
	echo "<p><a href=\"" . SITE_DOMAIN. "/viewquestion?q=" . $question . "\">Click here to continue</a></p>";
}

include 'footer.php';
?>