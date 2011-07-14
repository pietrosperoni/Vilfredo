<?php
$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='
	<link rel="Stylesheet" type="text/css" href="js/jquery/RichTextEditor/css/jqrte.css">
	<link type="text/css" href="js/jquery/RichTextEditor/css/jqpopup.css" rel="Stylesheet">
	<link rel="stylesheet" href="js/jquery/RichTextEditor/css/jqcp.css" type="text/css">
	<!-- <script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script> -->
	<!-- <script type="text/javascript" src="js/jquery-1.4.2.js"></script> -->
	<script type="text/javascript" src="js/jquery-1.6.min.js"></script>
	<script type="text/javascript" src="js/svg/jquery.svg.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
	<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
	<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
	<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>
	<script type="text/javascript" src="js/vilfredo.php"></script>

<title>VgtA: User Page</title>
';

include('header.php');
#$userid=isloggedin();
if (!HasQuestionAccess())
{
	header("Location: viewquestions.php");
}

#if ($userid)
#{
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

$question = $_GET[QUERY_KEY_QUESTION];

$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";



WriteQuestionInfo($question,$userid);

$QuestionInfo=GetQuestion($question);
$title=$QuestionInfo['title'];
$content=$QuestionInfo['question'];
#	$room=$QuestionInfo['room'];
$phase=$QuestionInfo['phase'];
$generation=$QuestionInfo['roundid'];
$author=$QuestionInfo['usercreatorid'];



echo "<h1>Activity of User ";
echo WriteUserVsReader($uid,$userid);
echo " in:</h1>";

	$g=1;
	echo '<table>';
	echo '<tr>';
	
	while($g < $generation)
	{
		echo '<td colspan="2"><center>';
		
		echo "<h2>Generation ".$g.'</h2>';
		echo '</center></td>';
		echo '</tr>';
		
		$proposalsSupported=ProposalsToAnEndorser($uid,$question,$g);
		if($proposalsSupported)
		{
			echo '<tr>';
			echo '<td>';
			echo "<center><h3>proposals Supported</h3></center>";
			
			echo '</td>';
			echo '<td>';
			echo "<center><h3>proposals Ignored</h3></center>";
			echo '</td>'; 
			echo '</tr>';
			echo '<tr>';
			echo '<td valign="top">';
			

			echo '<div class="proposalssupported">';


			foreach ($proposalsSupported as $p)
			{
				$authorid=AuthorOfProposal($p);
				if ($authorid==$uid)
				{
					echo '<table border="1" style="border:red">';
				}
				else
				{
					echo '<table>';
					
					
				}				
				echo "<tr><td>";
				
				WriteProposalOnlyText($p,$question,$generation,$room,$userid);
				echo "</td></tr>";
				echo "</table>";

			}
			echo '</div>';
			echo '</td>';
			echo '<td valign="top">';
			
			echo '<div class="proposalsignored">';
			
			$proposalsIgnored=array_diff(ProposalsInGeneration($question,$g),$proposalsSupported);
			foreach ($proposalsIgnored as $p)
			{
				$authorid=AuthorOfProposal($p);
				if ($authorid==$uid)
				{
					echo '<table border="1" style="border:red">';
				}
				else
				{
					echo '<table>';
				}				
				
				echo "<tr><td>";
				
				WriteProposalOnlyText($p,$question,$generation,$room,$userid);
				echo "</td></tr>";
				echo "</table>";
				
			}
			echo '</div>';			
			echo '</td>';
			echo '</tr>';
			
		}
		else
		{
			$authoredProposals=ProposalsOfAnAuthorWrittenInAGeneration($uid,$question,$g);
			if($authoredProposals)
			{
				echo '<tr>';
				echo "<td colspan=2><center><h3>Proposals Authored</h3></center></td>";
				echo '</tr>';								
				foreach ($authoredProposals as $p)
				{
					echo '<tr>';					
					echo '<td colspan=2><center>';
					echo '<table border="1" style="border:red">';
					echo "<tr><td>";

					WriteProposalOnlyText($p,$question,$generation,$room,$userid);
					echo "</td></tr>";
					echo "</table>";
					
					echo '</center></td>';
					echo '</tr>';				
					
				}
			}
			else
			{
				echo '<tr>';
				echo '<td colspan=2>';
				echo 'No Proposals Voted in Generation '.$g;
				echo '</td>';
				echo '</tr>';
				
			}
						
		}
		
		$g=$g+1;
		echo '</tr>';
	}
	echo '</table>';
	

	// echo "<a href=logout.php>Logout</a>";

include('footer.php');

?>