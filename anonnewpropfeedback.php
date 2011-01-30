<?php
include 'header.php';
$userid = $_GET['anon'];
$propid = $_GET['prop'];
$urlquery = $_GET['query'];

if (isset($userid))
{
	echo "<h3>Your proposal was successfully created!</h3>";
	echo "<p>Your userid in the results for this round is <strong>$userid</strong></p>";
	echo "<p>Your proposal ID is <strong>$propid</strong></p>";
}
else
{
	echo "<p>We're sorry but there was a problem creating your proposal at this time. Please try again later.</p>";
}

echo "<p><a href=\"" . SITE_DOMAIN. "/" . $urlquery . "\">Click here to return to the question</a></p>";

include 'footer.php';
?>