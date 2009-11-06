<?php

$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='
<link rel="Stylesheet" type="text/css" href="js/jquery/RichTextEditor/css/jqrte.css" />
<link type="text/css" href="js/jquery/RichTextEditor/css/jqpopup.css" rel="Stylesheet"/>
<link rel="stylesheet" href="js/jquery/RichTextEditor/css/jqcp.css" type="text/css"/>

<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>';

include('header.php');


$userid=isloggedin();
#if (isAdmin($userid))
if ($userid)
{	
	
	$proposal = $_POST['p'];
	
	$sql = "SELECT proposals.blurb, proposals.experimentid FROM proposals, questions WHERE proposals.id = ".$proposal." and proposals.experimentid = questions.id and questions.roundid = proposals.roundid and questions.phase = 0  ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_row($response))
	{		
		$blurb =	$row[0];
		$question =	$row[1];
				
		$sql = "DELETE FROM proposals WHERE  proposals.id = " . $proposal . " ";
		mysql_query($sql);

		?>
		<div id="actionbox">
					<h2>Propose an answer</h2>
			<p>Your proposal has been <B>DELETED</B>, please if necessary post the new version.</p>
		
			<form method="POST" action="newproposaltake.php">			
      <textarea id="content" name="blurb" class="jqrte_popup" rows="500" cols="70"><?php echo $blurb; ?></textarea>
      <?php
         include_once("js/jquery/RichTextEditor/content_editor_proposal.php");
         include_once("js/jquery/RichTextEditor/editor.php");
      ?>
      			<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="submit" name="submit" id="submit" value="Create proposal" />
			</form>
<script type="text/javascript">
   window.onload = function(){ 
      try{
         $("#content_rte").jqrte();
      }
      catch(e){}
   } 

   $(document).ready(function() {
         $("#content_rte").jqrte_setIcon();
         $("#content_rte").jqrte_setContent();
   });
</script>
		</div>

		
		<?php
	}
	
}
else
{
		header("Location: login.php");
}
?> 