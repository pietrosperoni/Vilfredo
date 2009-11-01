<?php
include('header.php');

$userid=isloggedin();
if ($userid)
{	
	
	$userstoinvite = $_POST['users'];
	if(!$userstoinvite){$userstoinvite=array();}
	$question = $_POST['question'];

	foreach ($userstoinvite as $user)
	{
		InviteUserToQuestion($user,$question,$userid);
	}
	echo "Invitations Sent";	
	header("Location: viewquestion.php?q=".$question."");
}
else
{
		header("Location: login.php");
}
?> 