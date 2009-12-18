<?php

$headcommands='
<link rel="stylesheet" href="js/jquery/tooltip/jquery.tooltip.css" />
<script src="js/jquery/jquery.js" type="text/javascript"></script>
<script src="js/jquery/jquery.bgiframe.js" type="text/javascript"></script>
<script src="js/jquery/jquery.dimensions.js" type="text/javascript"></script>
<script src="js/jquery/tooltip/jquery.tooltip.js" type="text/javascript"></script>

<script src="js/jquery/tooltip/chili-1.7.pack.js" type="text/javascript"></script>
<title>VgtA: Questions</title>
';



include('header.php');
#$userid=isloggedin();
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


	echo '<div id="leftfloatbox">';

	echo '<div id="writingbox2">';
	echo '<h2> ALL QUESTIONS</h2><p>';

#	$sql = "SELECT questions.id, questions.title, questions.roundid, questions.phase, users.username, users.id  FROM questions, users WHERE questions.phase = 1 AND users.id = questions.usercreatorid ORDER BY questions.roundid DESC, questions.phase DESC, questions.id DESC ";
	$sql = "SELECT questions.id FROM questions ORDER BY questions.id DESC ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		echo WriteQuestion($row[0],$userid);
	}	
 echo '</div>';
 echo '</div>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';
	echo '<br/>';

	// echo "<a href=logout.php>Logout</a>";  tip: 'tooltip',

}
else
{
		header("Location: login.php");
}

include('footer.php');


?> 