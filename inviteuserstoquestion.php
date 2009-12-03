<?php
include('header.php');

#$userid=isloggedin();
if ($userid)
{

	$userstoinvite = $_POST['users'];
	if(!$userstoinvite){$userstoinvite=array();}
	$question = $_POST['question'];
	$room = $_POST['room'];

	foreach ($userstoinvite as $user)
	{
		InviteUserToQuestion($user,$question,$room,$userid);
		// Add entry to invites table so user can see the question
		// in his To-Do List page
		SendInvite($userid, $user, $question);
	}
	#echo "Invitations Sent";

        // Only show room id if not empty
        $question_url = CreateQuestionURL($question,$room);

	header("Location: viewquestion.php".$question_url);
}
else
{
		header("Location: login.php");
}
?>