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
<script type="text/javascript" src="js/vilfredo.php"></script>';


include('header.php');

?>
<script type="text/javascript">
//Assumes id is passed in the URL
var recaptcha_public_key = '<?php echo $recaptcha_public_key;?>';
</script>
<?php

//if ($userid)
//{
	// Check if user has room access.
	if (!HasQuestionAccess())
	{
		header("Location: viewquestions.php");
	}

	$question = $_GET[QUERY_KEY_QUESTION];

	$room = isset($_GET[QUERY_KEY_ROOM]) ? $_GET[QUERY_KEY_ROOM] : "";
	
	//WriteQuestionInfo($question,$userid);
	
	
	//****
	$QuestionInfo=GetQuestion($question);
	$title=$QuestionInfo['title'];
	$content=$QuestionInfo['question'];
	$room=$QuestionInfo['room'];
	$phase=$QuestionInfo['phase'];
	$generation=$QuestionInfo['roundid'];
	$author=$QuestionInfo['usercreatorid'];
	$bitlyhash = $QuestionInfo['bitlyhash'];
	$shorturl = '';
	$permit_anon_votes = $QuestionInfo['permit_anon_votes'];
	$permit_anon_proposals = $QuestionInfo['permit_anon_proposals'];

	if (!empty($bitlyhash)) 
	{
		$shorturl = BITLY_URL.$bitlyhash;
	}
	else
	{
		$longurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if ($hash = make_bitly_hash($longurl, $bitly_user, $bitly_key))
		{
			SetBitlyHash($question, $hash);
			$shorturl = BITLY_URL.$hash;
		}
	}
	set_log('$shorturl = ' . $shorturl);

	echo '<div class="questionbox">';
	echo "<h2>{$VGA_CONTENT['question_txt']}</h2>";
	?>
		<h2 id="question">
		<form method="post" action="changeupdate.php">
			<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
			<input type="hidden" name="room" id="room" value="<?php echo $room; ?>" />
		<?php
		echo  $title;
	if ($userid) {
		if ($subscribed==1)
		{
			?> <input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['email_sub_link']?>unsubscribe" /> <?php
		}else{
			?> <input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['email_unsub_link']?>subscribe" /> <?php
		}
	}
		?>
		</form>
		</h2>
	<?php
	echo "<br />";
	echo '<div id="question">' . $content . '</div>';


	//echo WriteUserVsReader($author,$userid);
	echo "<br />";
#	$author = WriteUserVsReader($author,$userid);
#	echo '<p id="author"><cite>' . $VGA_CONTENT['cite_txt']. ' ' . $author . '</cite></p>';
	$authorstring = WriteUserVsReader($author,$userid);
	echo '<p id="author"><cite>' . $VGA_CONTENT['cite_txt']. ' ' . $authorstring . '</cite></p>';

	echo '<table id="social-buttons"><tr><td>';

	// Only display twit button if shorturl found in DB or generated from bitly
	if (!empty($shorturl))
	{
		if (false)
		{
			$retweetprefix = "RT @Vg2A";
			$tweet = urlencode($retweetprefix." ".$title." ".$shorturl);
			$tweetaddress = "http://twitter.com/home?status=$tweet";
			echo "<a class=\"tweet\" href=\"$tweetaddress\"><span>{$VGA_CONTENT['tweet_link']}</span></a>";
		}
		else
		{
			//set_log('Tweet Button lang = ' . $locale);
			echo '<a href="http://twitter.com/share" class="twitter-share-button" data-url="'. $shorturl .'" data-text="'. $title .'" data-count="none" data-via="Vg2A" data-lang="'.$locale.'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
		}
	}

	echo '</td><!-- <td><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="" send="false" layout="button_count" width="450" show_faces="true" font=""></fb:like></td> --></tr></table>';

	if($generation>2)
	{
		$graph=StudyQuestion($question);
		echo "<img src='".$graph."'>";
	}

	echo '</div>';//---extended questionbox	

	if($generation>1)
	{		
		//echo '<div class="elementcontainer">'; 
		
		echo '<p><span id="show_table_link" class="question_panel_link"><span>'.$VGA_CONTENT['show_hist_table_txt'].'</span> <img src="images/voting.gif" width="30" height="20" alt="" /></span></p>';
		
		echo '<div id="questionmap">';
		MakeQuestionMap($userid,$question,$room,$generation,$phase);
		echo '</div> <!-- questionmap -->';	
		
		//echo '</div> <!-- elementcontainer -->';	
	}

	$QuestionInfo=GetQuestion($question);
	$title=$QuestionInfo['title'];
	$content=$QuestionInfo['question'];
#	$room=$QuestionInfo['room'];
	$phase=$QuestionInfo['phase'];
	$generation=$QuestionInfo['roundid'];
	$author=$QuestionInfo['usercreatorid'];

	$lastmoveonTime=TimeLastProposalOrEndorsement($question, $phase, $generation);
	if (!$lastmoveonTime)
	{
		$lastmoveonTime=strtotime( $row[6] );
	}
	$minimumtime= $row[7] ;

	$timeelapsed=time()-$lastmoveonTime;
	if ($timeelapsed>=$minimumtime)
	{
		$tomoveon=1;
	}
	else
	{
		$tomoveon=0;
	}
	$dayselapsed=(int)($timeelapsed/(60*60*24));
	$timeelapsed=$timeelapsed-$dayselapsed*60*60*24;
	$hourseselapsed=(int)($timeelapsed/(60*60));
	$timeelapsed=$timeelapsed-$hourseselapsed*60*60;
	$minuteselapsed=(int)($timeelapsed/60);


	$minimumdays=(int)($minimumtime/(60*60*24));
	$minimumtime=$minimumtime-$minimumdays*60*60*24;
	$minimumhours=(int)($minimumtime/(60*60));
	$minimumtime=$minimumtime-$minimumhours*60*60;
	$minimumminutes=(int)($minimumtime/60);


	echo "<br />";

	if (($phase==0) && ($generation>1))
	{
		echo '<div class = "container_large">';
		
		InsertMap($question,$generation-1, 0, 'M');		
		/*
		$graphsize = 'mediumgraph';
		if ($filename = InsertMap2($question,$generation-1))
		{
			$filename .= '.svg';
			?>
			<script type="text/javascript">
			
				function loadDone() {
					//alert('loaded');
				}

				$(document).ready(function() {
					var svgfile = '<?= $filename; ?>';
					$('#svggraph1').svg({loadURL: svgfile, onLoad: loadDone});
					//var svg_graph = $('#svggraph1').svg('get');  
					//resetSize(svg_graph);  
				});
			</script>
			<?php
			echo '<div id="svggraph1" class="' . $graphsize . '"></div>';
		}*/ 
		
		echo '</div>';
		
		echo '<div id="paretofrontbox">';

		$VisibleProposalsGenerations=PreviousAgreementsStillVisible($question,$generation);

		echo '<h3>' . $VGA_CONTENT['prev_agree_txt'] . ' (<a href="vhq.php?' . $_SERVER['QUERY_STRING'] . '">' . $VGA_CONTENT['history_link'] . '</a>)</h3>';
		sort($VisibleProposalsGenerations);
		
		if(empty($VisibleProposalsGenerations)) echo "{$VGA_CONTENT['none_txt']}<br />";
		
		foreach($VisibleProposalsGenerations as $vpg)
		{
			$endorsersAgreement=Endorsers($question,$vpg);
			if($generation==$vpg+1){echo '<h4><a href="vg.php'.CreateGenerationURL($question,$vpg,$room).'">Last Generation</a> ';}
			else		{	echo '<h4><a href="vg.php'.CreateGenerationURL($question,$vpg,$room).'">'.($generation-$vpg). ' Generations ago</a> ';}
			
			echo " {$VGA_CONTENT['agree_found_txt']} ".Count($endorsersAgreement)." people ";
			foreach($endorsersAgreement as $ea)	{	echo '<img src="images/a_man.png">'; }
			echo "</h4>";
			
			$ProposalsToSee=ParetoFront($question,$vpg);
				foreach($ProposalsToSee as $p)
				{
					?><form method="get" action="npv.php" target="_blank">
						<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
							<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
							<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
							<input type="submit" name="submit" title="<?=$VGA_CONTENT['reprop_this_title']?>" id="submit" value="<?=$VGA_CONTENT['reprop_mutate_button']?>" /></form>
							<?php	echo '</h3>';
					WriteProposalOnlyContent($p,$question,$generation,$room,$userid);
				}
				
		}
		$lastgeneration=$generation-1;
		if (! in_array($lastgeneration,$VisibleProposalsGenerations))
		{
			echo "<h3>{$VGA_CONTENT['alt_props_txt']}</h3>";
			echo "<p><i>{$VGA_CONTENT['pareto_txt']}</i></p>";

			$ParetoFront=ParetoFront($question,$generation-1);
			foreach ($ParetoFront as $p)
			{
					echo '<div class="paretoproposal">';
				
				?>
				<form method="get" action="npv.php" target="_blank">
				<h3>
					<?php	echo WriteProposalPage($p,$room);?>	
						<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
						<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
						<input type="submit" name="submit" title="<?=$VGA_CONTENT['prop_pres_title']?>" id="submit" value="<?=$VGA_CONTENT['mutate_button']?>" />
						</h3>
						</form>
				
				<?php
				WriteProposalOnlyContent($p,$question);
				
				$OriginalProposal=GetOriginalProposal($p);
				$OPropGen=$OriginalProposal["generation"];

				echo '<br />' . $VGA_CONTENT['written_by_txt'] . ': '.WriteUserVsReader(AuthorOfProposal($p),$userid);
				if ($OPropGen!=$generation)		{	echo "in ".WriteGenerationPage($question,$OPropGen,$room).".<br>";	}
				$endorsers=EndorsersToAProposal($p);
				echo '<br />' . $VGA_CONTENT['endorsed_by_txt'] . ': ';
				foreach($endorsers as $e)		{	echo WriteUserVsReader($e,$userid);}					
				echo '</div>';
				#}
			}
			echo '</div>';
		}		
	}
	
	echo '</div>';  //MISSING DIV ?

	//****** PASTE HERE
	echo '<div id="actionbox">';
		echo "<h3>{$VGA_CONTENT['gen_txt']} ".$generation.": ";
		if ( $phase==0)
		{
			echo "{$VGA_CONTENT['writing_phase_txt']}</h3>";
			if ($generation==1)
			{
				echo "<p><i>{$VGA_CONTENT['what_to_do_txt']}</i></p>";
			}
			else
			{
					echo "<p><i>{$VGA_CONTENT['what_to_do_2_txt']}</i></p>";
			}

	$NProposals=CountProposals($question,$generation);
	echo "<p>{$VGA_CONTENT['num_authors_txt']}: ".count(AuthorsOfNewProposals($question,$generation))."</p>";
	echo "<p>{$VGA_CONTENT['num_props_txt']}: ".$NProposals."</p>";

		}
		if ( $phase==1)
		{
			echo "{$VGA_CONTENT['eval_phase_txt']}</h3>";
	//			echo "<i>The list of proposals that follow should <br/>(a) give the possibility to endorse all of them, and <br/>(b)be all and only the proposals of this generation plus the winning proposals of the previous generation</i><br/><br/>";
				echo "<p>{$VGA_CONTENT['click_all_txt']}</p>";
	
	}
	//*****

#	if ( $userid and $phase==0 and $userid==$creatorid and $tomoveon==1) #creatorid was wrong so the button never appeared
	if ( $userid and $phase==0 and $userid==$author and $tomoveon==1)
	{
		if ($generation==1)
		{
			if (CountProposals($question,$generation)>1)
			{
				?>
					<form method="post" action="moveontoendorse.php">
					<?=$VGA_CONTENT['you_can_txt']?>:
						<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
						<input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['move_next_button']?>" />
					</form>
				<?php
			}
		}
		else
		{
			if (CountProposals($question,$generation))
			{
				?>
					<form method="post" action="moveontoendorse.php">
					<?=$VGA_CONTENT['you_can_txt']?>:
						<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
						<input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['move_next_button']?>" />
					</form>
				<?php
			}
		}
	}

	if ( $phase==1)
	{
		$nEndorsers=CountEndorsers($question,$generation);
		
		echo "<p>{$VGA_CONTENT['num_endors_txt']}: ".$nEndorsers."</p>";
		echo "<p>";
		$format = $VGA_CONTENT['time_since_first_txt'];
		echo sprintf($format, $dayselapsed, $hourseselapsed, $minuteselapsed);
		echo '<br />';
		echo $VGA_CONTENT['note_txt'];
		
		//echo "<p>{$VGA_CONTENT['time_since_first_txt']}: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> {$VGA_CONTENT['note_txt']} ";
		
		if ($minimumdays){ echo $minimumdays." days ";}
		if ($minimumhours){ echo $minimumhours." hours ";}
		if ($minimumminutes){ echo $minimumminutes." minutes ";}
		echo "{$VGA_CONTENT['time_passed_txt']} </p>";


#		if ($userid and $nEndorsers>1 and $userid==$creatorid and $tomoveon==1) #creatorid was wrong so the button never appeared
		if ($userid and $nEndorsers>1 and $userid==$author and $tomoveon==1) 
		{
			?>
			<form method="post" action="moveontowriting.php">
			If everybody has endorsed the proposals they wanted to endorse, you can:
				<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
				<input type="submit" name="submit" id="submit" value="Move On to the Next Phase" />
			</form>
			<?php
		}
	}

	if ( $phase==0)
	{
		?>
			
		<h2><?=$VGA_CONTENT['prop_ans_txt']?></h2>
			
		<p><strong><?=$VGA_CONTENT['note_txt']?></strong> <?=$VGA_CONTENT['prop_expl_txt']?></p>
		
		<?php	#echo "<p>Time since last moveon: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: 1 day need to pass between one moveon and the next</p>";
		/*echo "<p>Time since first proposal on this generation: ".$dayselapsed." days, ".$hourseselapsed." hours and ".$minuteselapsed." minutes.<br /> NOTE: ";
		if ($minimumdays)
		{ 			echo $minimumdays." days ";}
		if ($minimumhours)		{			echo $minimumhours." hours ";		}
		if ($minimumminutes)		{			echo $minimumminutes." minutes ";		}
		echo "{$VGA_CONTENT['time_passed_prop_txt']}</p>";*/
		//********
		echo '<p>';
		$format = '' . $VGA_CONTENT['time_since_txt'] . '';
		echo sprintf($format, $dayselapsed, $hourseselapsed, $minuteselapsed);
		
		echo '<br />';
		echo '' . $VGA_CONTENT['note_txt'] . ' '; 
		if ($minimumdays)
		{ 			
			echo $minimumdays.' ' . $VGA_CONTENT['days_txt'] . ' ';
		}
		if ($minimumhours)		
		{			
			echo $minimumhours.' ' . $VGA_CONTENT['hours_txt'] . ' ';		
		}
		if ($minimumminutes)		
		{			
			echo $minimumminutes.' ' . $VGA_CONTENT['mins_txt'] . ' ';		
		}
		echo $VGA_CONTENT['time_passed_prop_txt'];
		echo '</p>';		
		//********
?>

	
	<?php 
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
			<?php require_once("abstract.php"); ?>
		</div>
	</div>

	
       <div id="proposal_RTE">
       <textarea id="content" name="blurb" class="jqrte_popup" rows="500" cols="70"></textarea>
      <?php
         $RTE_TextLimit_content = 1000;
         include_once("js/jquery/RichTextEditor/content_editor_proposal.php");
         include_once("js/jquery/RichTextEditor/editor.php");
      ?>
	<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
	
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
	
	</div><!-- proposal_RTE -->
	</div><!-- editor_panel -->
	<!--translate-->
	</br></br><p>subscribe to question<?=$VGA_CONTENT['subscribe_to_question_txt']?>
	<input type = "Checkbox" name="subscribe" id="subscribe" title="Receives exciting and unexpected emails every time the question goes from one generation to the other<?=$VGA_CONTENT['suscribe_to_question_title']?>" 
	
	<?php
	if($subscribed or !$userid or !isUserActiveInQuestion($userid, $question))	
		{echo " checked ";}
	else	
		{echo " ";}		
	?>
	/>
	</p>

<!-- </form> -->
<script type="text/javascript">
$(document).ready(function() {	
	function checklengths() {
		var abstract_txt = $("#abstract_rte").contents().find("body").text();
		var proposal_txt = $("#content_rte").contents().find("body").text();
		var abstract_length = abstract_txt.length;
		var content_length = proposal_txt.length;
		var title = $("#abstract_title");
		var content_msg = $("#content_rte_chars_msg");
	
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
			if (abstract_length == 0)
			{
				title.html("<?=$VGA_CONTENT['abstract_req_ex_txt']?>")
					.css({"color": "red", "font-weight" : "bold"});
				content_msg.html("<?=$VGA_CONTENT['abstract_req_txt']?>")
					.css({'color' : 'red', 'font-weight' : 'bold'});
			}
			else
			{
				title.html("<?=$VGA_CONTENT['abstract_req_ex_txt']?> OK!")
					.css({"color" : "green", "font-weight" : "bold"}); 
				content_msg.html("Abstract OK!")
					.css({'color' : 'green', 'font-weight' : 'bold'});
			}
		}
		else if ( content_length  <= limit )
		{
			title.html("<?=$VGA_CONTENT['abs_opt_link']?>");
			title.css("color", "black"); 
			title.css("font-weight", "normal"); 
			$("#content_rte_chars_msg").html("");
		}
		// Set Abstract indicator
		var abs_remaining = limit_abs - abstract_length;
		var abs_indicator = $("#abstract_rte" + "_chars_remaining");
		abs_indicator.text(abs_remaining);
		if (abs_remaining < 0) {
			abs_indicator.addClass("length_not_ok");
		} else {
			abs_indicator.removeClass("length_not_ok");
		} 
		// Set Proposal Content indicator
		var prop_remaining = limit - content_length;
		var prop_indicator = $("#content_rte" +"_chars_remaining");
		prop_indicator.text(prop_remaining);
		if (prop_remaining < 0 && abstract_length == 0) {
			prop_indicator.addClass("length_not_ok");
		} else {
			prop_indicator.removeClass("length_not_ok");
		}
	}
	 var checklength = function (len) {
		var title = $("#abstract_title");
		var abstract_length = $("#abstract_rte").data('content_length');
		var content_length =  $("#content_rte").data('content_length');
		var logged_in = <?= $userid ? 'true' : 'false'; ?>;

		if ((content_length  > 0 && content_length <= limit && abstract_length <= limit_abs) || (content_length  > 0 && abstract_length > 0 && abstract_length <= limit_abs))
		{
			$("#submit_p").removeAttr("disabled");
		}
		else
		{
			$("#submit_p").attr("disabled","disabled");
		}

		if (content_length  > limit)
		{
			title.html("<?=$VGA_CONTENT['abstract_req_ex_txt']?>:");
			title.css("color", "green");
			title.css("font-weight", "bold");
			$("#content_rte_chars_msg").html("<?=$VGA_CONTENT['abstract_req_txt']?>");
		}
		else if ( content_length  <= limit )
		{
			title.html("<?=$VGA_CONTENT['abs_opt_link']?>");
			title.css("color", "black");
			title.css("font-weight", "normal");
			$("#content_rte_chars_msg").html("");
		}
	} 
 
	try{
		$("#content_rte").jqrte();
		$("#content_rte").jqrte_setIcon();
		$("#content_rte").jqrte_setContent();
		$("#content_rte").data('content_length', 0);
		
		var limit_abs = <?= empty($RTE_TextLimit_abstract) ? 'null' : $RTE_TextLimit_abstract; ?>;
		var limit = <?= empty($RTE_TextLimit_content) ? 'null' : $RTE_TextLimit_content; ?>;
		if (limit) {
			$("#abstract_rte").data('maxabslength', limit_abs);
			$("#content_rte").data('maxlength', limit);
			$("#content_rte").data('callback', checklength);
			$("#abstract_rte").data('callback', checklength);
		}
	}
	catch(e){}
});
</script>
<!-- Input Proposal end -->
</form>

<br />
	
<?php echo LoadLoginRegisterLinks($userid, 'submit_p'); ?>
	
<?php
if ($userid) {
		$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  and roundid = ".$generation." and usercreatorid = ".$userid." and source = 0 ORDER BY `id` DESC  ";
		$response = mysql_query($sql);
		if ($response)
		{
			//****
			echo "<h3>{$VGA_CONTENT['props_you_wrote_txt']}</h3>";
			echo '<table class="your_proposals userproposal">';
			while ($row = mysql_fetch_array($response))
			{
				echo '<tr><td>';
				echo '<div class="paretoproposal">';
				if (!empty($row['abstract'])) {
					echo '<div class="paretoabstract">';
					echo display_fulltext_link();
					echo '<h3>' . $VGA_CONTENT['prop_abstract_txt'] . '</h3>';
					echo $row['abstract'] ;
					echo '</div>';
					echo '<div class="paretotext">';
					echo '<h3>' . $VGA_CONTENT['proposal_txt'] . '</h3>';
					echo $row['blurb'];
					echo '</div>';
				}
				else {
					echo '<div class="paretofulltext">';
					echo '<h3>' . $VGA_CONTENT['proposal_txt'] . '</h3>';
					echo $row['blurb'] ;
					echo '</div>';
				}
				
				?>
				<form method="post" action="deleteproposal.php">
				<input type="hidden" name="p" id="p" value="<?php echo $row[0]; ?>" />
				<input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['edit_delete_button']?>" title="<?=$VGA_CONTENT['click_ed_del_title']?>"/>
				</form>
				
				<?php
				
				echo '</div>';
				
				/*
				echo '</td><td class="button_cell">';
				?>
				
				<form method="post" action="deleteproposal.php">
					<input type="hidden" name="p" id="p" value="<?php echo $row[0]; ?>" />
					<input type="submit" name="submit" id="submit" value="Edit or Delete" title="Click here to edit or delete your proposal"/>
				</form>
				
				<?php
				*/
				echo '</td></tr>';
			}
			echo '</table>';
		}
		else
		{
			echo "{$VGA_CONTENT['no_props_txt']}";
		}
	}
}

	if ( $phase==1)
	{
		$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  and roundid = ".$generation."  ORDER BY `id` DESC  ";
		//they should be randomly sorted!
		$response = mysql_query($sql);
		if ($response)
		{
			$userhasvoted = false;
			if ($userid)
			{
				$userhasvoted = hasUserEndorsed($userid, $question, $generation);
			}
			
			echo "<h3>{$VGA_CONTENT['proposals_txt']}:</h3>";
			
			if ($userhasvoted)
			{
				echo "<div class=\"feedback\">Your votes have been registered for this round <img src=\"images/grn_tick_trans.gif\" width=\"20\" height=\"20\" alt=\"\" /><div>(<u>Hint</u>: You can change you votes by voting again below)</div></div>";
			}
			?>
			<form method="post" action="endorse_or_not.php">
			<input type="hidden" name="question" value="<?php echo $question; ?>" />
			<table border="1" class="your_endorsements userproposal">
			<tr>
			<td class="history_cell"><h4><?=$VGA_CONTENT['voting_hist_txt']?></h4></td>
			<td><h4><?=$VGA_CONTENT['prop_sol_txt']?></h4></td>
			<td class="endorse_cell"><b><?=$VGA_CONTENT['check_all_endorse_txt']?></b></td>
			</tr>

			<?php

			while ($row = mysql_fetch_array($response))
			{
				echo '<tr>';
				echo '<td class="vote_list">';
				if ($userid and $row['source'] != 0)
				{
					
					// Display voting history for aesexual parents
					$proposal = $row['id'];
					$source = $row['source'];
					$ancestors = GetAncestorEndorsements($source, $userid, $question, $generation);
					foreach($ancestors as $ancestor)
					{
						if ($ancestor['endorsed'] == -1)
						{
							#echo "<span>" . $ancestor[generation] . "</span>";
							#echo ' <img src="images/novote.jpg" title="You did not participate in the voting on generation '.$ancestor[generation].'"  height="30">';
							echo ' <img src="images/tick_empty.png" title="' . $VGA_CONTENT['not_part_title'] . ' '.$ancestor[generation].'"  height="30" alt="empty tick box">';
						}
						elseif ($ancestor['endorsed'] == 1)
						{
							#echo "<span>$ancestor[generation] </span>";
							echo ' <img src="images/thumbsup.gif" title="' . $VGA_CONTENT['you_endorsed_title'] . ' '.$ancestor[generation].'"  height="30" alt="thumbs up">';
						}
						elseif ($ancestor['endorsed'] == 0)
						{
							#echo "<span>$ancestor[generation] </span>";
							echo ' <img src="images/thumbsdown.gif" title="' . $VGA_CONTENT['you_ignored_title'] . ' '.$ancestor[generation].'" height="30" alt="thumbs down">';
						}
						echo '</br>';
					}
				}
				else{ echo '&nbsp;'; }
				echo '</td>';
				
				echo '<td>';
				echo '<div class="paretoproposal">';
				if (!empty($row['abstract'])) {
					echo '<div class="paretoabstract">';
					echo display_fulltext_link();
					echo '<h3>' . $VGA_CONTENT['prop_abstract_txt'] . '</h3>';
					echo $row['abstract'] ;
					echo '</div>';
					echo '<div class="paretotext">';
					echo '<h3>' . $VGA_CONTENT['proposal_txt'] . '</h3>';
					echo $row['blurb'];
					echo '</div>';
				}
				else {
					echo '<div class="paretofulltext">';
					echo '<h3>' . $VGA_CONTENT['proposal_txt'] . '</h3>';
					echo $row['blurb'] ;
					echo '</div>';
				}
				echo '</div>';
				echo '</td><td>';
				
				echo '<Input type = "Checkbox" Name ="proposal[]" title="' . $VGA_CONTENT['check_to_endorse_title'] . '" value="'.$row[0].'"';

			if ($userid) {
				$sql = "SELECT  id FROM endorse WHERE  endorse.userid = " . $userid . " and endorse.proposalid = " . $row[0] . "  LIMIT 1";
				if(mysql_fetch_array(mysql_query($sql)))
				{
					echo ' checked="checked" ';
				}
			}
				echo ' /></p> </td></tr>';
		}
		
	// Anonymous Submit
	//set_log('permit_anon: ' . $permit_anon);
	if (!$userid && $permit_anon_votes) :
	?>	
	<tr><td colspan="2"><p><strong><?=$VGA_CONTENT['click_to_vote_anon_txt']?></strong></p></td><td>
	<input type = "Checkbox" name="anon" id="anon" title="<?=$VGA_CONTENT['check_anon_title']?>" value="" />		
	</td></tr>
	<?php 
	endif ?>
	
	<!--translate-->
	<tr><td colspan="2"><p>subscribe to question<?=$VGA_CONTENT['subscribe_to_question_txt']?></p></td><td>
	<input type = "Checkbox" name="subscribe" id="subscribe" title="Receives exciting and unexpected emails every time the question goes from one generation to the other<?=$VGA_CONTENT['suscribe_to_question_title']?>" 
	
	<?php
	if($subscribed or !$userid or !isUserActiveInQuestion($userid, $question))	
	{echo " checked ";}
	else	{echo " ";}
	?>
	
	/>		
	</td></tr>
	
	
	
	<tr><td colspan="2">&nbsp;</td><td>
	<?php
	// Submit button
		if ($userid) {
			$regclass = "submit_ok";
		} else {
			$regclass = "reg_submit";
		}
	?>
	<input class="<?= $regclass; ?>" type="button" name="submit_e" id="submit_e" value="<?=$VGA_CONTENT['submit_button']?>"/>			
	</td></tr>
	</table>
	</form>
	<?php

		}
		else
		{
			echo "{$VGA_CONTENT['no_props_txt']}";
		}
	}

	echo '</div>';
	if ($generation>1)
	{
		echo '<div>';
		echo '<a href="vhq.php?' . $_SERVER['QUERY_STRING'] . '">' . $VGA_CONTENT['view_hist_link'] . '</a> ' . $VGA_CONTENT['view_hist_txt'];
		echo '</div>';
	}

	// echo "<a href=logout.php>Logout</a>";
//}
//else

if (!$userid)
{
	set_log('Not logged in - storing request');
	SetRequest();
}

include('footer.php');

?>
