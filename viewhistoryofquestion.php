<?php

$headcommands='
<link type="text/css" href="css/theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript">
	$(function() {
		$(".expandbtn").click(function () {	
		     var fulltxt = $(this).siblings("div.paretoabstract");
		      if (fulltxt.is(":hidden")) {
		        fulltxt.slideDown("slow");
		        $(this).text("Hide Full Text");
		      } else {
		        fulltxt.slideUp("slow");
		        $(this).text("Show Full Text");
		      }
		    });
	});
</script>';

include('header.php');

if ($userid)
{
	// Check if user has room access.
	if (!HasQuestionAccess())
	{
		header("Location: index.php");
	}
	
	$question = GetParamFromQuery(QUERY_KEY_QUESTION);
	$room = GetParamFromQuery(QUERY_KEY_ROOM);

	echo "<h2>Question:</h2>";
	$graph=StudyQuestion($question);
	
	echo "<img src='".$graph."'>";


	$sql = "SELECT * FROM questions WHERE id = ".$question." LIMIT 1 ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{
		$content=$row[1];
		$generation=$row[2];
		$creatorid=$row[4];
		$title=$row[5];
		$room=$row[9];
		$urlquery = CreateQuestionURL($question, $room);
		echo '<h4 id="question">' . $title . '</h2>';
		echo '<div id="question">' . $content . '</div>';

		$sql2 = "SELECT users.username, users.id FROM questions, users WHERE questions.id = ".$question." and users.id = questions.usercreatorid LIMIT 1 ";
		$response2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_row($response2))
		{
			echo '<p id="author"><cite>asked by <a href="user.php?u=' . $row2[1] . '">'.$row2[0].'</a></cite></p>';
		}

#		echo '<div id="actionbox">';
		echo "<h3>Current Generation: ".$generation." </h3>";
		echo '<a href="' . SITE_DOMAIN . '/viewquestion.php'.$urlquery.'" >Question Page</a>';
#		echo '</div>';
	}

	echo "<h1>History of past Proposals:</h1>";
#	echo '<quote>"History, teach us nothing": Sting</quote><br /><br />';


	$sql = "SELECT * FROM proposals WHERE experimentid = ".$question." and roundid < ".$generation." ORDER BY `roundid` DESC, `dominatedby` ASC  ";
	$response = mysql_query($sql);
	if ($response)
	{
		echo '<div id="historybox">';
		echo '<table border="1">';
		echo '<tr><td>link</td><td><strong>Proposal</strong></td><td><strong>Author</strong></td><td><strong>Endorsers</strong></td><td><strong>Result</strong></td><td><strong>You</strong></td></tr>';
		$genshowing=$generation;
		while ($row = mysql_fetch_array($response))
		{
			if ($row[3]!=$genshowing)
			{
				$genshowing=$row[3];
				echo '<tr><td colspan="2"><h4> Generation '.$genshowing.' ';
				$proposers=AuthorsOfNewProposals($question,$genshowing);
#				echo "Proposers:";
#				foreach ($proposers as $p)
#				{
#					$sql5 = "SELECT username FROM users WHERE id = ".$p." ";
#					$response5 = mysql_query($sql5);
#					$row5 = mysql_fetch_row($response5);
#					echo " ".$row5[0]." ";
#				}
				$endorsers=Endorsers($question,$genshowing);
#				echo "<br />";
#				echo "Endorsers:";
#				foreach ($endorsers as $e)
#				{
#					$sql5 = "SELECT username FROM users WHERE id = ".$e." ";
#					$response5 = mysql_query($sql5);
#					$row5 = mysql_fetch_row($response5);
#					echo " ".$row5[0]." ";
#				}
				
				$PreviousAuthors=AuthorsOfInheritedProposals($question,$genshowing);

				$NVoters=count($endorsers); #B
				$NOldAuthors=count($PreviousAuthors);
				$NAuthors=count($proposers); #A

				$IntersectionAP=array_intersect($endorsers,$proposers);
				$IntersectionPO=array_intersect($proposers,$PreviousAuthors);
				$IntersectionAO=array_intersect($proposers,$PreviousAuthors);
				
				$SizeIntersectionAP=count($IntersectionAP);
				$SizeIntersectionAO=count($IntersectionAO);
				$SizeIntersectionPO=count($IntersectionPO);
				
				$IntersectionAPO=array_intersect($endorsers,$proposers,$PreviousAuthors);
				$SizeIntersectionAPO=count($IntersectionAPO);

				$VenGraph="http://chart.apis.google.com/chart?cht=v&chs=350x150&chd=t:".$NAuthors.",".$NVoters.",".$NOldAuthors.",".$SizeIntersectionAP.",".$SizeIntersectionAO.",".$SizeIntersectionPO.",".$SizeIntersectionAPO."&chco=FF0000,0000FF,FDD017&chdl=".$NAuthors." Authors|".$NVoters." Voters|".$NOldAuthors." Inherited Authors&chtt=Authors+Vs+Voters+Relationship";
#				$VenGraph="http://chart.apis.google.com/chart?cht=v&chs=300x150&chd=t:".$NAuthors.",".$NVoters.","."0".",".$SizeIntersectionAP.","."0".","."0".","."0"."&chco=FF0000,0000FF,FFFFFF&chdl=Authors|Voters|&chtt=Authors+Vs+Voters+Relationship";

				$ToolTipGraph=" ".$NAuthors." Authors, ".$NVoters." Voters, ".$NOldAuthors." Inherited Authors, Author ? Voters= ".$SizeIntersectionAP.", Author ? Inherited Authors= ".$SizeIntersectionAO.", Voters ? Inherited Authors= ".$SizeIntersectionPO.", Authors ? Voters ? Inherited Authors= ".$SizeIntersectionAPO." ";

				echo '</h4></td> <td colspan="4"><img Title="'.$ToolTipGraph.'" src="'.$VenGraph.'">';
#				echo "<br /> ".$NAuthors." Authors: ".implode(", ",$proposers)."<br />";
#				echo " ".$NVoters." Voters:".implode(", ",$endorsers)."<br />";
#				echo " ".$NOldAuthors." Inherited:".implode(", ",$PreviousAuthors)."<br />";
								
#				echo " ".$SizeIntersectionAP." Authors Intersection Voters: ".implode(", ",$IntersectionAP)."<br />";
#				echo " ".$SizeIntersectionAO." Authors Intersection Inherited: ".implode(", ",$IntersectionAO)."<br />";
#				echo " ".$SizeIntersectionPO." Inherited Intersection Voters: ".implode(", ",$IntersectionPO)."<br />";
#				echo " ".$SizeIntersectionAPO." Full Intersection: ".implode(", ",$IntersectionAPO)."<br />";

				echo '</td></tr>';
			}


			$dominatedby=$row[6];
			$source=$row[5];

			$sql6 = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $row[0] . " LIMIT 1";
			if(mysql_fetch_row(mysql_query($sql6)))
			{$Endorsed=1;}
			else
			{$Endorsed=0;}
			
			$urlquery = CreateProposalURL($row[0], $room);
			
			echo '<td><a href="viewproposal.php'.$urlquery.'">link</a></td>';
			
			echo '<td>';
			$has_abstract = false;
			if (!empty($row['abstract'])) {
				$has_abstract = true;
				echo '<h3>Abstract</h3>';
				echo $row['abstract'];
				if ($has_abstract) {
					echo '<span class="expandbtn">Show Full Text</span>';
					echo '<div class="clear"></div>';
				}
			}
			else {
				echo $row['blurb'];
			}
			
			if ($has_abstract)
			{
				echo '<div class="paretoabstract">';
				echo '<h3>Full Text</h3>';
				echo $row['blurb'];
				echo '</div>';
			}
			echo '</td>';
			

			echo '<td>';
			if ($row[5])
			{
				echo "<h6>Inherited;<br />";
				echo "originally written by: ";
				echo WriteUserVsReader($row[2],$userid);
				echo "</h6>";
			}
			else
			{
				echo WriteUserVsReader($row[2],$userid);
				#echo " New";
			}
			echo '</td>';

			$endorsers=EndorsersToAProposal($row[0]);
			echo '<td>';

			foreach ($endorsers as $user)
			{
			echo WriteUserVsReader($user,$userid);
			}
#			echo '<a title="The list might not be complete, due to a recent Bug" href="FAQ.php#bugendorsmen"><sup>*</sup></a>';

			echo '&nbsp;</td>';
			echo '<td>';

			if($row[6])
			{
				echo '<img src="images/thumbsdown.jpg" title="The community rejected this proposal" height="45">';
			}
			else
			{
				echo '<img src="images/thumbsup.jpg" title="The community accepted this proposal"  height="48">';
			}
			echo '</td>';
			echo '<td>';
			if($Endorsed)
			{
				echo ' <img src="images/thumbsup.jpg" title="You endorsed this proposal"  height="28">';
			}
			else
			{
				echo ' <img src="images/thumbsdown.jpg" title="You ignored this proposal" height="25">';
			}
			echo '<a title="results are not consistent, due to a recent Bug" href="FAQ.php#bugendorsmen"><sup>*</sup></a>';
			echo '</td>';

			echo '</tr> ';
		}
		echo '</table>';

		echo '</div>';
	}
	// echo "<a href=logout.php>Logout</a>";
}
else
{
		DoLogin();
}

include('footer.php');

?>







