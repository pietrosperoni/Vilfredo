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


	$uid = $_GET['u'];

	$sql = "SELECT users.username FROM users WHERE users.id = " . $uid;
	$response = mysql_query($sql);
	$user = mysql_fetch_array($response);
	$user = $user[0];

	echo '<h2>' . $user . '</h2>';

	echo '<div id="leftfloatbox">';
	echo '<div id="endorsingbox2">';

	$RoomsI=RoomsUsed($userid);
	$RoomsThee=RoomsUsed($uid);
	$WhereHaveWeMet=array();
	$WhereHaveWeMet=WhereHaveWeMet($RoomsI,$RoomsThee);
	if ($WhereHaveWeMet){
		echo "You met in the following rooms:<br />";

		foreach ($WhereHaveWeMet as $room)
		{
			$urlquery = CreateRoomURL($room);
			if($room)
			{
				echo '<a href="viewquestions.php'.$urlquery.'">'.$room.'</a>; ';				
			}
			else
			{
				echo '<a href="viewquestions.php'.$urlquery.'">The Common Room</a>; ';								
			}
		}
	}

	echo '</br>Note: You can only see activity that happened in rooms you have participated in</br>';
	
	// **
	// Set user access filter
	// **
	$user_access = GetUserAccessFilter($uid);

	echo '</div">';
	echo '</div">';

	foreach ($WhereHaveWeMet as $r)
	{
		echo '<h2><center>';
		$urlquery = CreateRoomURL($r);
		if($r)
		{
			echo 'Room: <a href="viewquestions.php'.$urlquery.'">'.$r.'</a>; ';				
		}
		else
		{
			echo '<a href="viewquestions.php'.$urlquery.'">The Common Room</a>; ';								
		}
		echo '</center></h2>';
		
		
		
		echo '<h3><center>Questions asked by '.$user.' ';
		echo '</center></h3>';
		
		
		
		$questions=QuestionsAskedInRoom($uid,$r);
		if($questions)
		{
			foreach($questions as $q)
			{
				echo '<table border=1 width="100%"><tr><td width="50%">';

				$lastgen=GetQuestionGeneration($q);
				if($lastgen>1)
				{
					$g=1;
					echo "<table border=1><tr>";
					echo "<td></td>";

					while($g<$lastgen)
					{
						echo "<td>Gen $g</td>";
						$g++;
					}
					echo "</tr><tr>";

					$g=1;
					echo '<td >';
					echo WriteUserVsReaderInQuestion($uid,$userid,$q,$r);
					echo '</td >';

					while($g<$lastgen)
					{
						echo ShowActivity($uid,$q,$g);
						$g++;
					}
					echo "</tr></table>";
					}
				echo '</td><td width="50%">';
				echo WriteQuestion($q,$userid);
				echo "</td></tr></table>";
			}	
		}
		else
		{
			echo "<p>None</p>";
		}
		
		echo '<h3><center> Questions Participated in';
		echo '</center></h3>';
		
		
		
		$questions=ActivityInRoom($uid,$r);	
		if($questions)
		{
			foreach($questions as $q)
			{
				echo '<table border=1 width="100%"><tr><td width="50%">';
				$lastgen=GetQuestionGeneration($q);
				if($lastgen>1)
				{
					$g=1;
					echo "<table border=1><tr>";
					echo "<td></td>";

					while($g<$lastgen)
					{
						echo "<td>Gen $g</td>";
						$g++;
					}
					echo "</tr><tr>";
					$g=1;
					echo '<td >';
					echo WriteUserVsReaderInQuestion($uid,$userid,$q,$r);
					echo '</td >';

					while($g<$lastgen)
					{
						echo ShowActivity($uid,$q,$g);
						$g++;
					}
					echo "</tr></table>";
				}
				echo '</td><td width="50%">';
				echo WriteQuestion($q,$userid);
				echo "</td></tr></table>";		
			}
		}
		else
		{
			echo "<p>None</p>";	
		}
	}

	// echo "<a href=logout.php>Logout</a>";
}
else
{
		DoLogin();
}

include('footer.php');

?>