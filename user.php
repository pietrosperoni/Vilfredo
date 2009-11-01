<?php
$headcommands='
<link rel="stylesheet" href="js/jquery/tooltip/jquery.tooltip.css" />
<script src="js/jquery/jquery.js" type="text/javascript"></script>
<script src="js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script src="js/jquery/jquery.dimensions.js" type="text/javascript"></script>
<script src="js/jquery/tooltip/jquery.tooltip.js" type="text/javascript"></script>

<script src="js/jquery/tooltip/chili-1.7.pack.js" type="text/javascript"></script>
<title>VgtA: User Page</title>
';

include('header.php');
$userid=isloggedin();
if ($userid)
{
?>
<script type="text/javascript">
$(function() {
$(".foottip a").tooltip({
	bodyHandler: function() {
		return $($(this).attr("tooltip")).html();
	},
	showURL: false
});

});
</script>
<?php


	$uid = $_GET['u'];
	
	$sql = "SELECT users.username FROM users WHERE users.id = " . $uid;
	$response = mysql_query($sql);
	$user = mysql_fetch_row($response);
	$user = $user[0];
	
	echo '<h2>' . $user . '</h2>';
	
	
	echo '<div id="leftfloatbox">';
	echo '<div id="endorsingbox2">';

	echo '<h3>'. $user . ' has asked the following questions:</h3>';	

	$sql = "SELECT questions.id, questions.question, questions.roundid, questions.roundid, questions.title FROM questions WHERE questions.usercreatorid = ".$uid." ORDER BY  questions.id DESC ";

	$response = mysql_query($sql);

	
	while ($row = mysql_fetch_row($response))
	{
		$questionid = $row[0];
		$questiontext = $row[1];
		$generation = $row[2];
		$phase = $row[3];
		$title = $row[4];
		
		echo '<fieldset class="foottip">';
		echo '<p><a title="This is a new question. Be the first to suggest an answer!" href="viewquestion.php?q=' . $row[0] . '" tooltip="#footnote' . $questionid . '" >' . $title . '</a></p>';	
		echo '<div class="invisible" id="footnote' . $row[0] . '"><br/>QUESTION: '.$questiontext.'.<br/>Generation:'.$generation.'</div>';
		echo '</fieldset>';
	}

	echo '</div">';
	echo '</div">';

	echo '<h3>'. $user . ' endorses the following proposals:</h3>';	

	$sql = "SELECT endorse.userid, endorse.proposalid, proposals.blurb, proposals.id, proposals.roundid, proposals.experimentid FROM endorse, proposals WHERE endorse.userid = ".$uid." AND proposals.id = endorse.proposalid ORDER BY proposals.experimentid DESC, proposals.roundid DESC ";

	$response = mysql_query($sql);

	
	while ($row = mysql_fetch_row($response))
	{
		$proposal = $row[2];
		$proposalid = $row[1];
		
		echo '<p class="endorsed"><a href="viewproposal.php?p=' . $proposalid . '">' . $proposal . '</a></p>';
		echo $row[5];
		echo '.';
		echo $row[4];

	}

	// echo "<a href=logout.php>Logout</a>";
}
else
{
		header("Location: login.php");
}

include('footer.php');

?>