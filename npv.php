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
<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>
<script type="text/javascript" src="js/vilfredo.php"></script>';

include('header.php');

?>
<script type="text/javascript">
//Assumes id is passed in the URL
var recaptcha_public_key = '<?php echo $recaptcha_public_key;?>';
</script>
<?php

#$userid=isloggedin();
#if (isAdmin($userid))
//if ($userid)
//{	
	
$proposal = GetParamFromQuery(QUERY_KEY_PROPOSAL);
if (!HasProposalAccess())
{
	header("Location: viewquestions.php");
	//echo "no access";
}

$question_id = GetProposalQuestion($proposal);
$is_writing = IsQuestionWriting($question_id);
$whenfrom=GetProposalGeneration($proposal);
$whofrom=GetProposalAuthor($proposal);
$generationNow=GetQuestionGeneration($question_id);

// Check if question is in the writing state before allowing new proposal version
if (!$is_writing)
{
	echo $VGA_CONTENT['now_voting_err_txt'];
	echo '<br />';
}
elseif ($whenfrom==$generationNow AND $userid!=$whofrom)
{
	echo $VGA_CONTENT['wrong_user_err_txt'];
	echo '<br />';
}
else
{
	
	$sql = "SELECT proposals.blurb, proposals.experimentid, proposals.abstract, 
	questions.permit_anon_proposals
	FROM proposals, questions 
	WHERE proposals.id = $proposal
	AND proposals.experimentid = questions.id";


	$response = mysql_query($sql);
	
	while ($row = mysql_fetch_array($response))
	{		
		$blurb = $row['blurb'];
		$question = $row['experimentid'];
		$abstract = $row['abstract'];
		$permit_anon_proposals = $row['permit_anon_proposals'];
		?>
		<div id="actionbox">
			<h2><?=$VGA_CONTENT['prop_ans_txt']?></h2>
			<?php 
			echo '<p>';
			$format = $VGA_CONTENT['new_mutate_txt'];
			echo sprintf($format, $proposal);
			echo '</p>';
			
			if ($userid) {//open 
			?>
			<form method="post" action="newproposaltake.php">
			<?php } else { ?>
			<form method="post" action="newproposaltake.php" class="reg-only">
			<?php } ?>		
     				
     				<div id="editor_panel">
				<!-- Input Proposal start -->
				<div id="abstract_panel">
					<h3><span></span><a href="#" id="abstract_title"><?=$VGA_CONTENT['abs_opt_link']?></a></h3>
					<div id="p_abstract_RTE">
						      <textarea id="abstract" name="abstract" class="jqrte_popup" rows="250" cols="70"></textarea>
						      <?php
							$RTE_TextLimit_abstract = 500;
							 include_once("js/jquery/RichTextEditor/content_editor_abstract.php");
							?>
<script type="text/javascript">
$(document).ready(function() {
try{
	    $("#abstract_rte").jqrte();
	    $("#abstract_rte").jqrte_setIcon();
	    $("#abstract_rte").jqrte_setContent();
	    var limit = <?= empty($RTE_TextLimit_abstract) ? 'null' : $RTE_TextLimit_abstract; ?>;
	    if (limit) {
		$("#abstract_rte").data('maxlength', limit);
	    }
	    var prop_abstract = <?= empty($abstract) ? 'null' : json_encode($abstract); ?>;

	    if (prop_abstract)
	    {
	    	$("#abstract_panel").accordion("activate", 0);
	    }
	    
	    if (prop_abstract) {
		setTimeout(function() {
			$("#abstract_rte").contents().find("body").html(prop_abstract);
		}, 250);
		setTimeout(function() {
		    $("#abstract_rte").jqrte_updateContent();
		}, 250);
	    }
	    else
	    {
		$("#abstract_rte").data('content_length', 0)
	    }
  }
  catch(e){}
 });
</script>
					</div> <!-- p_abstract_RTE -->
				</div> <!-- abstract_panel -->
				
			     <div id="proposal_RTE">
			       <textarea id="content" name="blurb" class="jqrte_popup" rows="500" cols="70"></textarea>
			      <?php
			         $RTE_TextLimit_content = 1000;
			         include_once("js/jquery/RichTextEditor/content_editor_proposal.php");
			         include_once("js/jquery/RichTextEditor/editor.php");
			      ?>
				<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="hidden" name="mutate" value="" />
				<input type="hidden" name="origpid" value="<?=$proposal?>" />
				
				
				<?php 
				if ($userid) {
					$regclass = "submit_ok";
				} else {
					$regclass = "reg_submit";
				}
				?>

				<input class="rte_submit <?= $regclass; ?>" type="button" name="submit_p" id="submit_p" value="<?=$VGA_CONTENT['create_proposal_button']?>" disabled="disabled"/>
				
				<?php
					// Anonymous Submit
					//set_log('permit_anon: ' . $permit_anon);
					if (!$userid && $permit_anon_proposals) :
					?>	
					<?=$VGA_CONTENT['click_anon_txt']?>
					<Input type = "Checkbox" Name ="anon" id="anon" title="<?=$VGA_CONTENT['check_anon_title']?>" value="" />
					<?php 
					endif ?>
			
				</div> <!-- proposal_RTE -->
				</div> <!-- editor_panel -->
				
			<!-- </form> -->
			<script type="text/javascript">
			$(document).ready(function() {
				
				var checklength = function (len) {
					var title = $("#abstract_title");
					var abstract_length = $("#abstract_rte").data('content_length');
					var content_length =  $("#content_rte").data('content_length');
					
					if ((content_length  > 0 && content_length <= limit && abstract_length <= limit_abs) || (content_length  > 0 && abstract_length > 0 && abstract_length <= limit_abs))
					{
						$("#submit_p").removeAttr("disabled");
					}
					else 
					{
						$("#submit_p").attr("disabled");
					}
					
					if (content_length  > limit)
					{
						title.html("<?=$VGA_CONTENT['abstract_req_ex_txt']?>:");
						title.css("color", "green"); 
						title.css("font-weight", "bold"); 
						$("#content_rte_chars_msg").html("<?=$VGA_CONTENT['abstract_req_txt']?>");
					}
					else if ( content_length <= limit )
					{
						title.html("<?=$VGA_CONTENT['abs_opt_link']?>");
						title.css("color", "black"); 
						title.css("font-weight", "normal"); 
						$("#content_rte_chars_msg").html("");
					}
				}
				
				try{
					var content_box = $("#content_rte");
					content_box.jqrte();
					content_box.jqrte_setIcon();
					var limit_abs = <?= empty($RTE_TextLimit_abstract) ? 'null' : $RTE_TextLimit_abstract; ?>;
					var limit = <?= empty($RTE_TextLimit_content) ? 'null' : $RTE_TextLimit_content; ?>;
					if (limit) {
						content_box.data('maxlength', limit);
						content_box.data('callback', checklength);
						$("#abstract_rte").data('callback', checklength);
					}
					var prop_content = <?= empty($blurb) ? 'null' : json_encode($blurb); ?>;
					

					if (prop_content) {
						setTimeout(function() {
							content_box.contents().find("body").html(prop_content);
						}, 250);

						setTimeout(function() {
						    content_box.jqrte_updateContent();
						}, 250);
					}
				}
				catch(e){
					alert("An exception occurred in the script. Error name: " + e.name  + ". Error message: " + e.message);
				}
			});
			</script>
		<!-- Input Proposal end -->
			</form>
			
			<br /><br /><br />
		</div>

		
		<?php
	}
	
}
//else
//{
//		DoLogin();
//}
include('footer.php');
?> 