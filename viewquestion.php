<?php
$language="enus";
include_once("js/jquery/RichTextEditor/locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}

$headcommands='';
include('header.php');
include('vga_timeless.php');

set_log(__FILE__." called....");

?>

<link rel="Stylesheet" type="text/css" href="js/jquery/RichTextEditor/css/jqrte.css">
<link type="text/css" href="js/jquery/RichTextEditor/css/jqpopup.css" rel="Stylesheet">
<link rel="stylesheet" href="js/jquery/RichTextEditor/css/jqcp.css" type="text/css">
<!--
<script type="text/javascript" src="js/jquery-1.6.min.js"></script>
<script type="text/javascript" src="js/svg/jquery.svg.min.js"></script>
-->
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery.svg.min.js"></script>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery.svgdom.min.js"></script>
<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery.svganim.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
<script type="text/javascript" src="/js/sprintf-0.6.js"></script>
<script type="text/javascript" src="/js/cookies/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/underscore.js"></script>  
<script type="text/javascript" src="/js/charCount.js"></script>
<script type="text/javascript" src="js/jquery/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery/RichTextEditor/jquery.jqrte.min.js"></script>
<script type="text/javascript" src="js/vilfredo.php"></script>

<script type="text/javascript">
//Assumes id is passed in the URL
var recaptcha_public_key = '<?php echo $recaptcha_public_key;?>';
var votingcommentslist;
var userid = <?=json_encode($userid)?>;
var require_voting_comments = <?=json_encode($voting_settings['require_voting_comments'])?>;
var user_commentid;

var NOT_VOTED = <?=json_encode(NOT_VOTED)?>;
var AGREE = <?=json_encode(AGREE)?>;
var DISAGREE = <?=json_encode(DISAGREE)?>;
var NOT_UNDERSTAND = <?=json_encode(NOT_UNDERSTAND)?>;


function closeCommentsList(comments)
{	
	if (comments.is(':visible') == false)
	{
		return;
	}
	
	if (comments.siblings('.commentform').is(':visible'))
	{
		return;
	}
	
	var dislikecommentslist = comments.find('.dislikecommentslist');
	var confusedcommentslist = comments.find('.confusedcommentslist');
	
	dislikecommentslist.empty();
	confusedcommentslist.empty();
	comments.slideUp(500);
}

function toggleCommentsList(comments)
{
	if (comments.is(':visible') == false)
	{
		openCommentsList(comments);
	}
	else
	{
		closeCommentsList(comments);
	}
}

function openCommentsList(comments)
{	
	var dislikecommentslist = comments.find('.dislikecommentslist');
	var confusedcommentslist = comments.find('.confusedcommentslist');
	
	if (comments.is(':visible'))
	{
		return;
	}
	else
	{
		var pid;
		var el_with_id = comments.parents('tr.user_vote'); // FIXME
		if (el_with_id.length == 0)
		{
			el_with_id = comments.parents('.paretoproposal'); 
			if (el_with_id.length == 0)
			{
				return;
			}
			pid = parseInt(el_with_id.attr('id').replace(/[^0-9]/g, ''));
		}
		else
		{
			pid = parseInt(el_with_id.attr('id').replace(/[^0-9]/g, ''));
		}
		
		//alert(pid);
		
		if (typeof votingcommentslist[pid] != 'undefined')
		{
			var createLists = $.each(votingcommentslist[pid], function(i, usercomment) 
			{					
				var selected = ''
				if (user_commentid != null && 
					typeof user_commentid[pid] != 'undefined' && 
					user_commentid[pid] == usercomment['id'])
				{
					selected = 'checked="checked"';
				}
				if (usercomment['comment'] != '')
				{
					if (usercomment['type'] == 'dislike')
					{
						dislikecommentslist.append('<div class="comment">' + 
						'<div class="select_comment"><input type="radio" name="select_comment[' + pid + ']" value="' +
						usercomment["id"] + '"' + selected + '></div>' + 
						'<div class="text">' + usercomment['comment'] +
						'</div></div>');
					}
					if (usercomment['type'] == 'confused')
					{
						confusedcommentslist.append('<div class="comment">' + 
						'<div class="select_comment"><input type="radio" name="select_comment[' + pid + ']" value="' +
						usercomment["id"] + '"' + selected + '></div>' + 
						'<div class="text">' + usercomment['comment'] +
						'</div></div>');
					}
				}	
			});
			
			$.when(createLists).done(function()
			{
				comments.slideDown(500);
			});
		}
	}
}


$(function() {
	
	$('.kpreadcomment').live("click", function(event){
		var kpcomment = $(this).siblings('.kpcomment');
		
		if (kpcomment.is(":visible"))
		{
			kpcomment.slideUp(500);
			$(this).html("(Show Comment)");
		}
		else
		{
			kpcomment.slideDown(500);
			$(this).html("(Hide Comment)");
		}
	});
	
	// Display messages on icon click
	//
	$('.usrmsgpic').live("click", function(event){
		
		var paretoproposal = $(this).parent('.paretoproposal');
		var the_comments = paretoproposal.siblings('.comments');
		
		var comments = $(this).parent('.paretoproposal').siblings('.comments');
		if (comments.length == 0)
		{
			comments = $(this).parent('.paretoproposal').find('.comments');
		}

		toggleCommentsList(comments);
		return;
		
		var dislikecommentslist = comments.find('.dislikecommentslist');
		var confusedcommentslist = comments.find('.confusedcommentslist');
		
		if (comments.is(':visible'))
		{
			dislikecommentslist.empty();
			confusedcommentslist.empty();
			comments.slideUp(500);
		}
		else
		{
			var el_with_id = $(this).parents('tr.user_vote');
			if (el_with_id.length == 0)
			{
				el_with_id = $(this).parents('.pfbox');
			}
			var pid = parseInt(el_with_id.attr('id').replace(/[^0-9]/g, ''));
			
			//alert(pid);
			
			if (typeof votingcommentslist[pid] != 'undefined')
			{
				var createLists = $.each(votingcommentslist[pid], function(i, usercomment) 
				{					
					var selected = ''
					if (typeof user_commentid != 'undefined' && typeof user_commentid[pid] != 'undefined' && user_commentid[pid] == usercomment['id'])
					{
						selected = 'checked="checked"';
					}
					if (usercomment['comment'] != '')
					{
						if (usercomment['type'] == 'dislike')
						{
							dislikecommentslist.append('<div class="comment">' + 
							'<div class="select_comment"><input type="radio" name="select_comment[' + pid + ']" value="' +
							usercomment["id"] + '"' + selected + '></div>' + 
							'<div class="text">' + usercomment['comment'] +
							'</div></div>');
						}
						if (usercomment['type'] == 'confused')
						{
							confusedcommentslist.append('<div class="comment">' + 
							'<div class="select_comment"><input type="radio" name="select_comment[' + pid + ']" value="' +
							usercomment["id"] + '"' + selected + '></div>' + 
							'<div class="text">' + usercomment['comment'] +
							'</div></div>');
						}
					}	
				});
				
				$.when(createLists).done(function()
				{
					comments.slideDown(500);
				});
			}
		}
	});
	
	$('.select_comment').live('click', function(event){
		var commentformtextarea = $(this).parents('.comments').siblings('.commentform').find('textarea');
		if (commentformtextarea.val() != '')
		{
			var ok = confirm("You sure you want to clear your comment text?");
			if (ok)
			{
				commentformtextarea.val('');
			}
		}
	});
	$('.commentform textarea').live('click', function(event){
		var form = $(this).parents('.commentform');
		var comments = form.siblings('.comments');
		var radios = comments.find('.select_comment input');
		
		var selectcomments = $(this).parents('.commentform').siblings('.comments').find('.select_comment input');
		selectcomments.each(function(){
			$(this).prop('checked', false);
		});
	});
	
	$('.voting_choices img').live('click', function(event){
		var img = $(this).prop('src');
		var choice = $(this).parents('.voting_choices').siblings('.voting_choice');
		choice.css('background-image', 'url('+img+')');
		var setval = choice.siblings('.voting_choice_val');
		var prev_val = choice.siblings('.prev_voting_choice_val');
		
		var comments = $(this).parents('.votes').siblings('.proposalcontent').find('.comments');
		
		if ($(this).hasClass('1'))
		{
			setval.val(1);
			
			closeCommentsList(comments);
			
			$(this).parents('td').siblings('td.proposalcontent').find('.commentform').slideUp(500);
			/*
			// Remove comment select buttons
			comments.find('.dislikecommentslist .select_comment').each(function(i){
				$(this).fadeOut(1000);
				$(this).prop('checked', false);
			});
			comments.find('.confusedcommentslist .select_comment').each(function(i){
				$(this).fadeOut(1000);
				$(this).prop('checked', false);
			});
			comments.slideUp(1000);
			*/
		}
		else if ($(this).hasClass('2') || $(this).hasClass('3'))
		{
			if ($(this).hasClass('2'))
			{
				setval.val(2);
			}
			else
			{
				setval.val(3);
			}
			
			var comments = $(this).parents('.votes').siblings('.proposalcontent').find('.comments');
			openCommentsList(comments);
						
			if ($(this).hasClass('2'))
			{
				comments.find('.dislikecommentslist .select_comment').each(function(i){
					$(this).fadeIn(1000);
				});
				comments.find('.confusedcommentslist .select_comment').each(function(i){
					$(this).fadeOut(1000);
					$(this).prop('checked', false);
				});
			}
			else if ($(this).hasClass('3'))
			{
				comments.find('.dislikecommentslist .select_comment').each(function(i){
					$(this).fadeOut(1000);
					$(this).prop('checked', false);
				});
				comments.find('.confusedcommentslist .select_comment').each(function(i){
					$(this).fadeIn(1000);
				});
			}
			else
			{
				comments.find('.dislikecommentslist .select_comment').each(function(i){
					$(this).fadeOut(1000);
					$(this).prop('checked', false);
				});
				comments.find('.confusedcommentslist .select_comment').each(function(i){
					$(this).fadeOut(1000);
					$(this).prop('checked', false);
				});
			}
			
			var pid = parseInt($(this).parents('tr.user_vote').attr('id').replace(/[^0-9]/g, ''));
			//if ( (setval.val() == 2 || setval.val() == 3) && (prev_val.val() != setval.val()) )
			if ( (setval.val() == 2 || setval.val() == 3) )
			{
				var commentform = $(this).parents('td').siblings('td.proposalcontent').find('.commentform');
				if (setval.val() == "2")
				{
					commentform.find('.intro').html("<p>Please tell us why you don\'t like this proposal.</p> <p>Select a comment you agree with (if there are any) or write your own below.</p>");
				}
				else
				{
					commentform.find('.intro').html("<p>Please tell us why you don\'t understand this proposal.</p> <p>Select a comment you agree with (if there are any) or write your own below.</p>");
				}
				commentform.find('textarea').trigger('setcharcount');
				
				if (!commentform.is(':visible'))
				{
					commentform.slideDown(500, function(){
						$(this).find('.textbox').fadeIn(500);
					});
				}
			}
			else
			{
				$(this).parents('td').siblings('td.proposalcontent').find('.commentform').slideUp(500);
			}
		}
	});
	
	
	$('.user_vote').each(function(i){
		var vote = $(this).find('.voting_choice_val').val();
		switch (vote)
		{
			case "2":
				$(this).find('.voting_choice').css('background-image', 'url(images/thumbdown.png)');
				break;	
			case "3":
				$(this).find('.voting_choice').css('background-image', 'url(images/confused.png)');
				break;
			case "0":
			case "1":
				$(this).find('.voting_choice').css('background-image', 'url(images/thumbup.png)');
		}	
	});
	
	// Commet textarea counter
	$(".commentform textarea").charCount({
		allowed: 100,		
		warning: 20,
		counterText: "<?=$VGA_CONTENT['char_count_label']?>" + " "
	});

	// Check voting info before submitting
	$('form#votingform').submit(function() 
	{	
		var comments_done = true;
		
		if (require_voting_comments)
		{
			$('tr.user_vote').each(function(){
				//var pid = parseInt($(this).attr('id').replace(/[^0-9]/g, ''));
							
				var vote = parseInt($(this).find('.voting_choice_val').val())
				var prev_vote = parseInt($(this).find('.prev_voting_choice_val').val())
				
				if (vote == prev_vote)
				{
					return true;
				}
				
				//var prev_commentid = parseInt($(this).find('td[name^=prev_commentid]').val())

				var selected_comments = $(this).find('input[name^=select_comment]:checked').length; // 0 or 1
				var comment_box = $(this).find('.commentform textarea');
				var new_comment = '';
				if (comment_box)
				{
					new_comment = comment_box.val();
				}
				
				if (vote != prev_vote && vote != 1 && selected_comments == 0 && new_comment == '')
				{
					comment_box.css('border', '5px solid red');
					comments_done = false;
				}
				else
				{
					comment_box.css('border', 'none');
				}
			});

			/*
			$('.commentform textarea').each(function(){ // FIXME
				if ($(this).is(":visible") && $(this).val() == '')
				{
					$(this).css('border', '5px solid red');
					comments_done = false;
				}
				else
				{
					$(this).css('border', 'none');
				}
			});
			*/
		}
		
		if (!comments_done)
		{
			alert("You need to complete the comment boxes for the proposals you are not endorsing");
			return false;
		}
		
		var has_endorsed = false;
		// User can only vote if they have made at least one endorsement - today
		$('.voting_choice_val').each(function(){
			if ($(this).val() == 1)
			{
				has_endorsed = true;
			}
		});
		if (!has_endorsed)
		{
			alert("You must endorse at least one proposal in order to submit your votes");
			return false;
		}
		
		// Everything OK - submit votes
		return true;
	});
	
	$('.deletebtn').click(function(){
		var ok = confirm("<?=$VGA_CONTENT['delete_yr_prop_txt']?>");
		if (ok)
		{
			var pid = $(this).attr('id').replace(/[^0-9]/g, '');
			var qid = $(this).siblings('.qid').val();
			var gen = $(this).siblings('.qgen').val();
			deleteproposal(pid, qid, gen, this);
		}
	});
	
	$('span.submit')
	.live("mouseover", function(event){
		$(this).addClass("over");
	})
	.live("mouseout", function(event){
		$(this).removeClass("over");
	})
	.live("click", function(event){
		var props = $(this).siblings(".plist").data('props');
		//alert("Submitting equalities: "+ props.toString());
		addProposalRelations(props, "equivalent");		
	});
	
	$('span.cancel')
	.live("mouseover", function(event){
		$(this).addClass("over");
	})
	.live("mouseout", function(event){
		$(this).removeClass("over");
	})
	.live("click", function(event){
		var plist = $(this).siblings(".plist");
		$('table.your_endorsements').find('tr.user_vote').die().removeClass('selected over');
		plist.html('');
		$('#select_same').removeClass("active");
		$('span.submit').remove();
		plist.data('props', []);
		$(this).remove();		
	});
	
	$('#select_same')
	.data('active', "false")
	.mouseenter(function(event){
		$(this).addClass("over");
	})
	.mouseleave(function(event){
		$(this).removeClass("over");
	});
		
	$('#select_same').click(function(event){
		
		// <div>Identify proposal relations: <span id="select_same">Equivalent Proposals</span><span class="plist"></span><span class="submit">Submit</span><span class="submit">Cancel</span></div>
		
		var plist = $(this).siblings(".plist");
		
		if (isArray(plist.data('props')) == false)
		{
			plist.data('props', []);
		}
				
		var active = $('#select_same').data('active');
		
		if (active == 'true')
		{
			active = 'false';
		}
		else
		{
			active = 'true';
		}
		
		$(this).data('active', active);
		
		if (active == 'true')
		{		
			$(this).addClass("active");
		
			$('table.your_endorsements').find('tr.user_vote')
			.live('mouseover', function(event) {
				$(this).addClass("over");
			})
			.live('mouseout', function(event) {
				$(this).addClass("over");
			})
			.live('click', function(event) 
			{
				if ($(this).hasClass("selected") == false)
				{
					$(this).addClass("selected");
					var pid = $(this).find('input[name="proposal[]"]').val();
					
					plist.data('props').push(parseInt(pid));
					
					if ($('span.pbox').length > 0)
					{
						plist.append('<span class="mathop"> = </span><span class="pbox">'+pid+'</span>');
					}
					else
					{
						plist.append('<span class="pbox">'+pid+'</span>');
					}
					
					if ($('span.pbox').length == 2)
					{
						plist.after('<span class="submit">Submit</span><span class="cancel">Cancel</span>');
					}
				}
				else
				{
					$(this).removeClass("selected");
					var pid = $(this).find('input[name="proposal[]"]').val();
					
					
					$.each(plist.data('props'), function(i, id) {
					    if (id == parseInt(pid))
					    {
					        plist.data('props').splice(i, 1);
					    }
					});		
					
					var pboxes = plist.find('span.pbox');
					var mathops = plist.find('span.mathop');
					pboxes.each(function(i) {
						if ($(this).text() == pid)
						{
							$(this).fadeOut('slow').remove();
							if (mathops.length > 0)
							{
								mathops.eq(i-1).fadeOut('slow').remove();
							}
						}
					});
					if ($('span.pbox').length == 1)
					{
						$('span.submit, span.cancel').fadeOut('fast', function(){
							$(this).remove();
						});
					}
				}
			});
		}
		else
		{
			$('table.your_endorsements').find('tr.user_vote').die().removeClass('selected over');
			plist.html('');
			$(this).removeClass("active");
			$('span.submit, span.cancel').remove();
			plist.data('props', []);
		}
	});
});


function addProposalRelations(pids, relation, node)
{
	$.ajax({
		type: "POST",
		url: "addProposalRelationsx.php",
		data: ({pids : pids, relation : relation}),
		cache: false,
		error: ajax_error,
		dataType: 'html',
		success: function(response, status)
		{
			response = jQuery.trim(response);
			switch(response)
			{
			case "0": 
			   	alert('There was a problem adding the proposal relations');
			    break;
			case "1": 
			   	alert('Proposal relations successfully added');		
			}
		}
	});
}

function deleteproposal(pid, question, generation, node)
{
	$.ajax({
		type: "POST",
		url: "deletePropx.php",
		data: ({pid : pid, question : question, generation : generation}),
		cache: false,
		error: ajax_error,
		dataType: 'json',
		success: function(response, status)
		{
			if ((typeof response) == 'number')
			{
				if (jQuery.trim(response) == '0')
				{
					alert('There was a problem deleting that proposal');
				}
			}
			else
			{
				$(node).parents('tr').fadeOut(1000, function(){
					$(node).remove();
				});	
				
				// Update new author and proposal count from DB
				if (response['numauthors'] !== false)
				{
					$('#anp').text(response['numauthors']);
				}
				if (response['numproposals'] !== false)
				{
					$('#np').text(response['numproposals']);
				}
			}
		}
	});
}

function deleteproposal_v1(pid, node)
{
	$.ajax({
		type: "POST",
		url: "ajax/deleteProp.php",
		data: ({pid : pid}),
		cache: false,
		error: ajax_error,
		dataType: 'html',
		success: function(response, status)
		{
			response = jQuery.trim(response);
			switch(response)
			{
			case "0": 
			   	alert('There was a problem deleting that proposal');
			case "1": 
			   	$(node).parents('tr').fadeOut(1000, function(){
					$(node).remove();
				});	
				// Update new author and proposal count (not from DB as yet)
				var newprops = parseInt($('#np').text()) - 1;
				var newauth = parseInt($('#anp').text());
				$('#np').text(newprops);
				if (newprops == 0)
				{
					newauth--;
				}
				$('#anp').text(newauth);		
			}
		}
	});
}

//error(jqXHR, textStatus, errorThrown)
// status: "timeout", "error", "abort", and "parsererror"
function ajax_error(jqxhr, status, error)
{
	//displayAjaxErrorMsg("<?=$VGA_CONTENT['oops_internet_txt']?>");
}

</script>
<?php

//if ($userid)
//{
	// sanitize url
	$question = fetchValidQuestionFromQuery();
	$room = fetchValidRoomFromQuery();
	
	// Return false if bad query parameters passed
	if ($question === false || $room === false)
	{
		header("Location: error_page.php");
		exit;
	}
	
	//WriteQuestionInfo($question,$userid);
	
	// Check if user has room access.
	if (!HasQuestionAccess())
	{
		header("Location: viewquestions.php");
		exit;
	}
	
	$QuestionInfo = GetQuestion($question);
	
	if (!$QuestionInfo)
	{
		header("Location: error_page.php");
		exit;
	}
	
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
	
	$question_url = SITE_DOMAIN."/viewquestion.php".CreateQuestionURL($question,$room);
		
	$subscribed=IsSubscribed($question,$userid);

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
	?>
	
	<div id="questionbox" class="questionbox">
	<h2><?=$VGA_CONTENT['question_txt']?></h2>
	<h2 id="question">
	<form autocomplete="off" method="post" action="changeupdate.php">
		<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
		<input type="hidden" name="room" id="room" value="<?php echo $room; ?>" />
		<?php
		echo  $title;
	if ($userid) {
		if ($subscribed==1)
		{
			?> <input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['email_sub_link']?>" /> <?php
		}else{
			?> <input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['email_unsub_link']?>" /> <?php
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

	// Only display tweet button if shorturl found in DB or generated from bitly
	/*
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
	}*/

	echo '</td><!-- <td><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="" send="false" layout="button_count" width="450" show_faces="true" font=""></fb:like></td> --></tr></table>';

	if($generation>2)
	{
		$graph=StudyQuestion($question);
		echo "<img src='".$graph."'>";
	}

	echo '</div>';//---extended questionbox	



	if($generation>1)
	{
		#ShowCommunityMap($question,$generation,$phase);
		
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
	$minimumtime= $QuestionInfo['minimumtime'] ;

	$lastmoveonTime=TimeLastProposalOrEndorsement($question, $phase, $generation);
	if (!$lastmoveonTime)
	{
		$lastmoveonTime = strtotime( $QuestionInfo['usercreatorid'] );
	}

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
		$pastgeneration=$generation-1;
		$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$pastgeneration); 
		$ParetoFront=CalculateParetoFrontFromProposals($proposalsEndorsers);
		$ParetoFrontEndorsers=	array_intersect_key($proposalsEndorsers, array_flip($ParetoFront));		
				
		echo '<div class = "container_large">';
#		InsertMapFromArray($question,$pastgeneration,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"M",0,/*$InternalLinks=*/true);
		#InsertMapFromArray($question,$pastgeneration,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"M",0,$question_url,"Layers","Layers");
		InsertMapFromArray($question,$pastgeneration,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"M",0,$question_url,"NVotes","Layers",/*$Anonymize=*/ false);
		#		InsertMapFromArray($question,$generation,$ParetoFrontEndorsers,$ParetoFront,$room,$userid,"S",0,$question_url,"Layers","Layers");
		
		
		//InsertMap($question,$generation-1, 0, 'M',/*$InternalLinks=*/false);		
		/*
		$graphsize = 'mediumgraph';
		if ($filename = InsertMap2($question,$generation-1))
		{
			$filename .= '.svg';
			?>
			<script type="text/javascript">
				$(function() {
					var svgfile = '<?=$filename?>';
					$('#svggraph1').svg({loadURL: svgfile, onLoad: initGraph});
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
					$originalname=GetOriginalProposal($p);
					echo '<div id="proposal'.$originalname['proposalid'].'">';
					?><form autocomplete="off" method="get" action="npv.php" target="_blank">
						<?php	echo '<h3>'.WriteProposalPage($p,$room)." ";?>	
							<input type="hidden" name="p" id="p" value="<?php echo $p; ?>" />
							<?php	if($room) { ?><input type="hidden" name="room" id="room" value="<?php echo $room; ?>" /><?php	}	?>
							<input type="submit" name="submit" title="<?=$VGA_CONTENT['reprop_this_title']?>" id="submit" value="<?=$VGA_CONTENT['reprop_mutate_button']?>" /></form>
							<?php	echo '</h3>';
					WriteProposalOnlyContent($p,$question,$generation,$room,$userid);
					echo '</div>';
				}
		}
		$lastgeneration=$generation-1;
		if (! in_array($lastgeneration,$VisibleProposalsGenerations))
		{
			echo "<h3>{$VGA_CONTENT['alt_props_txt']}</h3>";
			echo "<p><i>{$VGA_CONTENT['pareto_txt']}</i></p>";

			//set_log("Calling ParetoFront() with $question and $generation minus 1");
			$ParetoFront=ParetoFront($question,$generation-1);
			
			//$allconfusedcomments = getCommentsByProposals($ParetoFront);
			$commentslist = getCommentsList($ParetoFront);
			set_log('$commentslist');
			set_log($commentslist);
			
			?>
			<script>
			votingcommentslist = <?=json_encode($commentslist)?>;
			</script>
			<?php

			foreach ($ParetoFront as $p)
			{
				//set_log("Processing prop $p");
				$originalname=GetOriginalProposal($p);
				echo '<div class="pfbox" id="proposal'.$originalname['proposalid'].'">'; // FIX
				
				echo '<div class="paretoproposal" id="real_proposal'.$p.'">';

				if (isset($commentslist[$p]))
				{
					echo '<img class="usrmsgpic" src="images/hascomments.jpg" title="'.count($commentslist[$p]).' comments">';
				}
				
				?>
				<form autocomplete="off" method="get" action="npv.php" target="_blank">
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
				
				?>
				
				<div class="comments">
					
				<div class="space"></div>
				
				<div class="commentsleft disagree">
				<h3>Disagree</h3>
				<div class="dislikecommentslist commentslist"></div>
				</div>
				
				<div class="commentsright confused">
				<h3>Don't Understand</h3>
				<div class="confusedcommentslist commentslist"></div>
				</div>
				
				<div class="clear"></div>
				
				</div> <!-- comments -->
				<?php
								
				echo '</div>';
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
		$NAuthors=CountAuthorsOfNewProposals($question,$generation);
	
		echo "<p>{$VGA_CONTENT['num_authors_txt']}: <span id=\"anp\">".CountAuthorsOfNewProposals($question,$generation)."</span></p>";
		echo "<p>{$VGA_CONTENT['num_props_txt']}: <span id=\"np\">".$NProposals."</span></p>";
	}
	if ( $phase==1)
	{
		echo "{$VGA_CONTENT['eval_phase_txt']}</h3>";
		echo "<p>{$VGA_CONTENT['click_all_txt']}</p>";
	}

#	if ( $userid and $phase==0 and $userid==$creatorid and $tomoveon==1) #creatorid was wrong so the button never appeared
	if ( $userid and $phase==0 and $userid==$author and $tomoveon==1)
	{
		if ($generation==1)
		{
#			if (CountProposals($question,$generation)>1)
			if ($NProposals>1)
			{
				?>
					<form autocomplete="off" method="post" action="moveontoendorse.php">
					<?=$VGA_CONTENT['you_can_txt']?>:
						<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
						<input type="submit" name="submit" id="submit" value="<?=$VGA_CONTENT['move_next_button']?>" />
					</form>
				<?php
			}
		}
		else
		{
#			if (CountProposals($question,$generation))
			if ($NAuthors)
			{
				?>
					<form autocomplete="off" method="post" action="moveontoendorse.php">
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
			<form autocomplete="off" method="post" action="moveontowriting.php">
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
		<form autocomplete="off" method="post" action="newproposaltake.php">
		<?php } else { ?>
		<form autocomplete="off" method="post" action="newproposaltake.php" class="reg-only">
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
         $RTE_TextLimit_content = MAX_LEN_PROPOSAL_BLURB;//1000;
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
				echo "<div class=\"paretoproposal\">";
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
				//$VGA_CONTENT['edit_delete_button']
				?>
				<form autocomplete="off" method="post" action="editproposal.php">
				<input type="hidden" name="p" value="<?=$row['id']?>" />
				<input type="hidden" name="backurl" value="<?=$question_url?>" />
				<input type="hidden" class="qid" name="q" value="<?=$question?>" />
				<input type="hidden" class="qgen" name="g" value="<?=$generation?>" />
				<input type="submit" name="submit" value="Edit" title="<?=$VGA_CONTENT['click_ed_del_title']?>"/>
				<input type="button" class="deletebtn" id="<?='del_p'.$row['id']?>" value="Delete" title="<?=$VGA_CONTENT['click_ed_del_title']?>"/>
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
		// Fetch User Comments 
		$proposallist = getCurrentProposalIDs($question, $generation);
		//set_log('$proposallist:');
		//set_log($proposallist);
		
		$userendorsedlist = array();
		$useropposedlist = array();
		if ($userid)
		{
			$userendorsedlist = getUserEndorsedFromList($userid, $proposallist);
			$useropposedlistdata = getUserOpposedFromList($userid, $proposallist);
			$useropposedlist = $useropposedlistdata['type'];
			$user_commentid = $useropposedlistdata['commentid'];			
		}
	
		set_log('$useropposedlistdata');
		set_log($useropposedlistdata);

		set_log('$user_commentid'); 
		set_log($user_commentid);
		
		set_log('$userendorsedlist');
		set_log($userendorsedlist);
		
		set_log('$useropposedlist');
		set_log($useropposedlist);

		$commentslist = getCommentsList($proposallist);
		set_log('$commentslist');
		set_log($commentslist);
		
		?>
		<script>
		votingcommentslist = <?=json_encode($commentslist)?>; // DONOW
		var user_commentid = <?=json_encode($user_commentid)?>;
		</script>
		<?php
		
		// Set $userendorsedata array
		$userendorsedata = array();
		set_log("commentslist = ");
		set_log($commentslist);
		foreach($proposallist as $p)
		{
			set_log("commentslist key = $p");
			set_log("userid = $userid");
			
			if (!empty($userendorsedlist) && in_array($p, $userendorsedlist))
			{
				$userendorsedata[$p] = 1;
			}
			elseif ($userid && !empty($useropposedlist) && array_key_exists($p, $useropposedlist))
			{
				if ($useropposedlist[$p] == 'dislike')
				{
					$userendorsedata[$p] = 2;
				}
				elseif ($useropposedlist[$p] == 'confused')
				{
					$userendorsedata[$p] = 3;
				}
			}
			else
			{
				$userendorsedata[$p] = 0;
			}
		}
		
		/*
		set_log('userendorsedata:');
		set_log($userendorsedata);
		set_log($commentsbyusers);
		*/
				
		$sql = "SELECT * FROM `proposals` WHERE `experimentid` = $question AND `roundid` = $generation ORDER BY `id` DESC";
		
		//they should be randomly sorted!
		$response = mysql_query($sql);
		if ($response)
		{
			$userhasvoted = false;
#			$userhasvoted = true; #(let's try with a default of true)
			if ($userid)
			{
				//$userhasvoted = checkuservote($userid, $question, $generation);
				$userhasvoted = hasuservoted($userid, $question, $generation);
				//set_log('userhasvoted?');
				//set_log($userhasvoted);
			}
			
			echo "<h3>{$VGA_CONTENT['proposals_txt']}:</h3>";
			
			if ($userhasvoted)
			{
				echo "<div id=\"Voted\"  class=\"feedback\">Your votes have been registered for this round <img src=\"images/grn_tick_trans.gif\" width=\"20\" height=\"20\" alt=\"\" /><div>(<u>Hint</u>: You can change you votes by voting again below)</div>";
				echo " </div>";
				
				
				$proposalsEndorsers=ReturnProposalsEndorsersArray($question,$generation); 
				set_log('$proposalsEndorsers');
				set_log($proposalsEndorsers);
				
				$ParetoFront=CalculateParetoFrontFromProposals($proposalsEndorsers);
				$ParetoFrontEndorsers=	array_intersect_key($proposalsEndorsers, array_flip($ParetoFront));
				
				if($voting_settings['anonymize_graph']) 	{ $AnonymizeGraph=true; }
				else						{ $AnonymizeGraph=false;}
				
				$use_old_graph_layout = false;
				// --------------- begin old
				if ($voting_settings['display_interactive_graphs'] && $use_old_graph_layout)
				{
					echo "<table cellpadding=\"0\" cellspacing=\"0\" border=0>";
						echo "<tr><td width=\"70%\">";
						InsertMapFromArray($question, $generation, $proposalsEndorsers, $ParetoFront, $room, $userid, "M", 0, $question_url, "Layers", "Layers", $AnonymizeGraph);
						echo "</td><td>";
						InsertMapFromArray($question, $generation, $ParetoFrontEndorsers, $ParetoFront, $room, $userid, "S", 0, $question_url, "Layers", "Layers", $AnonymizeGraph);
						echo "</td></tr>";
					echo "</table>";
				}
				// ------------------ end old
				
				
				// ------------------ begin New
				
				//elseif ($display_interactive_graphs && USE_GRAPHVIZ_MAPS)
				elseif ($voting_settings['display_interactive_graphs'] && USE_GRAPHVIZ_MAPS)
				{
					//set_log("Prop node layering option = " . $voting_settings['proposal_node_layout']);
					//set_log("User node layering option = " . $voting_settings['user_node_layout']);
					
					$votesgraph = GenerateMapFromArray(
						$question,
						$generation,
						$proposalsEndorsers,
						$ParetoFront,
						$room,
						$userid,
						"M",
						0,
						$question_url, 
						$voting_settings['proposal_node_layout'],
						$voting_settings['user_node_layout'], 
						$AnonymizeGraph);									
					
					$pfvotesgraph =  GenerateMapFromArray(
						$question,
						$generation,
						$ParetoFrontEndorsers,
						$ParetoFront,
						$room,
						$userid,
						"S",
						0,
						$question_url,
						$voting_settings['proposal_node_layout'],
						$voting_settings['user_node_layout'],
						$AnonymizeGraph);
					?>
					
					<br /><br />
					<div class="graphpanel"><span id="vgonly" class="button">User Voting</span><span id="bothgraphs" class="button">Show Both</span><span id="pfonly" class="button">Winning Proposals Only</span></div>

					<div id="graphs" class="graphcontainer">
						<div id="votesgraph" class="inner"></div>
						<div id="pfgraph" class="inner right"></div>
					</div>
					
					<script type="text/javascript">
					
					var votesgraph = <?=stripslashes(json_encode($votesgraph))?>;
					var pfvotesgraph = 	<?=stripslashes(json_encode($pfvotesgraph))?>;			
					
					function loadGraph(svgfile, svgfile2)
					{
						console.log("loadGraph called: Loading ", svgfile, " and ", svgfile2);
						$('#votesgraph').svg({loadURL: svgfile, onLoad: initGraph});
						$('#pfgraph').svg({loadURL: svgfile2, onLoad: initGraph});
					}

					function initGraph(svg) {
						setGraphSize(svg);
						setFullSizeLink(svg);
						//setGraphData();
						//setCurrentUser(user);
					}
					function setFullSizeLink(svg)
					{
						var link = $('a', svg.root()).filter(function() {
						    return $(this).attr('xlink:title').indexOf("full size") > -1;
						});

						var url = link.attr('xlink:href');
						link.attr('xlink:href', 'map/'+url);
					}
					function setGraphSize(svg, width, height) 
					{
						gwidth = width || $(svg._container).innerWidth();
						gheight = height || $(svg._container).innerHeight();
						svg.configure({width: gwidth, height: gheight});
					}
					
					$(function() {
						$('.graphpanel').fadeIn(1000);
						//loadGraph('Q73_R2.svg', 'Q73_R2_PF.svg');
						loadGraph(votesgraph, pfvotesgraph);

						$('.button').mouseenter(function(event){
							$(this).addClass("over");
						});
						$(".button").mouseleave(function(event){
							$(this).removeClass("over");
						});

						$('#vgonly').click(function(event){	
							var votes = $('#votesgraph');
							var graphbox = $('#graphs');
							var pfgraph = $('#pfgraph');

							if (votes.is(":visible") && pfgraph.is(":visible"))
							{
								pfgraph.fadeOut(1000, function(){			
									votes.css("width", graphbox.innerWidth());
									setGraphSize(votes.svg("get")); 
								});
							}
							else if (!votes.is(":visible") && pfgraph.is(":visible"))
							{	
								pfgraph.fadeOut(1000, function(){			
									$(this).css("width", "49%");
									setGraphSize($(this).svg("get")); 
									votes.css("width", graphbox.innerWidth());
									setGraphSize(votes.svg("get")); 
									votes.fadeIn(1000);
								});	
							}
						});
						$('#pfonly').click(function(event){	
							var pfgraph = $('#pfgraph');
							var graphbox = $('#graphs');
							var votes = $('#votesgraph');

							if (votes.is(":visible") && pfgraph.is(":visible"))
							{
								votes.fadeOut(1000, function(){			
									pfgraph.css("width", graphbox.innerWidth());
									setGraphSize(pfgraph.svg("get")); 
								});
							}
							else if (votes.is(":visible") && !pfgraph.is(":visible"))
							{	
								votes.fadeOut(1000, function(){			
									$(this).css("width", "49%");
									setGraphSize($(this).svg("get")); 
									pfgraph.css("width", graphbox.innerWidth());
									setGraphSize(pfgraph.svg("get")); 
									pfgraph.fadeIn(1000);
								});	
							}
						});
						$('#bothgraphs').click(function(event){	

							var graphbox = $('#graphs');
							var votes = $('#votesgraph');
							var pfgraph = $('#pfgraph');

							if (votes.is(":visible") && !pfgraph.is(":visible"))
							{
								votes.css("width", "49%");
								setGraphSize(votes.svg("get"));
								pfgraph.fadeIn(1000);
							}
							else if (!votes.is(":visible") && pfgraph.is(":visible"))
							{
								pfgraph.css("width", "49%");
								setGraphSize(pfgraph.svg("get"));
								votes.fadeIn(1000);
							}
						});
					});
					
					</script>
					
					<?php
				}
				
				// ------------------ end New
				
				
				echo "<br>";
				echo "<br>";

				
				$PFE=CalculateFullParetoFrontExcludingFromArray($proposalsEndorsers,$userid);
				
				$ParetoFrontPlus=array_diff($PFE,$ParetoFront);
				$ParetoFrontMinus=array_diff($ParetoFront,$PFE);
				
				if (FALSE) #We take this off for now
				#if (sizeof($ParetoFrontPlus) OR sizeof($ParetoFrontMinus))
				{
					echo "<div class=\"feedback\">By voting You have changed the results.<br>Without you ";
					if (sizeof($ParetoFrontPlus))
					{
						foreach ($ParetoFrontPlus as $p)	
							#{echo WriteProposalNumber($p,$room);}						
							{echo WriteProposalNumberInternalLink($p,$room);}						
						echo "would have been in the Pareto Front.";						
					}
					if (sizeof($ParetoFrontPlus) AND sizeof($ParetoFrontMinus))
					{
						echo "<br>While without you ";						
					}					
					if (sizeof($ParetoFrontMinus))
					{
						foreach ($ParetoFrontMinus as $p)	
							{echo WriteProposalNumberInternalLink($p,$room);}						
							#{echo WriteProposalNumber($p,$room);}						
						echo "would NOT have been in the Pareto Front.";
					}
					echo "</div>";						
				}
				
				if (sizeof($ParetoFrontMinus))
				{
					$HomeWork=CalculateKeyPlayersKnowingPFfromArrayInteractiveExcludingKnowingDiff($proposalsEndorsers,$ParetoFront,$userid,$ParetoFrontMinus);
					#$HomeWork=CalculateKeyPlayersKnowingPFfromArrayInteractiveExcluding($proposalsEndorsers,$ParetoFront,$userid);
					if (count($HomeWork) > 0)
					{
						echo "<div class=\"feedback\">You are a Key Player. This means that with your vote you could simplify the Pareto Front. Please look at proposal(s) ";						
						foreach ($HomeWork as $PCD)
						{
							$proposalNumber = WriteProposalNumberInternalLink($PCD,$room);
							echo " ".$proposalNumber.", ";
						}
						echo "and consider if you could vote it.</div>";					
					}	
					else
					{
						echo "ATTENTION PARETO FRONT MINUS WITHOUT BEING A KEY PLAYER???";						
					}				
				}
				#$ParetoFront=CalculateParetoFront($question,$generation); #$ParetoFront=CalculateFullParetoFrontExcluding($proposals,0);
#				$proposals=GetProposalsInGeneration($question,$generation);				
#				$PFE=CalculateFullParetoFrontExcluding($proposals,$userid);
				
								
				$CouldDominate = CalculateKeyPlayersKnowingPFfromArrayInteractive($proposalsEndorsers,$ParetoFront);
				set_log('$CouldDominate');
				set_log($CouldDominate);
				$users = extractEndorsers($proposalsEndorsers);
				
				set_log("Voters");
				set_log($users);
				
				if ($voting_settings['display_key_players']) 
				{
					// Display Key Players -- Begin
					echo "<div class=\"feedback\">KEY PLAYERS:<br/><br/>";
					
					foreach ($users as $u)
					{
						if ($u==$userid)	{continue;}
						
						$HomeWork = $CouldDominate[$u];
						//('$HomeWork');
						//set_log($HomeWork);
						$ucomments = array();
						
						if (count($HomeWork) > 0)
						{
							$ucomments = getUserCommentsForProposals($u, $HomeWork);
							//set_log('$ucomments');
							//set_log($ucomments);
						}
						
						if (count($HomeWork) > 0)
						{
							$disiked=array();
							$confusing=array();
							
							//set_log("Processing homework...");
							
							foreach ($HomeWork as $PCD)
							{
								
								$vote = GetUserVoteForProposal($u, $question, $PCD, $generation);
								
								//set_log('$GetUserVoteForProposal for user $u on propsal $PCD');
								//set_log($vote);
								
								if ($vote=="dislike")
								{
#									echo "$u Dislikes $PCD <br/>";
									$disiked[]=$PCD;
								}
								elseif ($vote=="confused")
								{
#									echo "$u does not understand $PCD <br/>";
									$confusing[]=$PCD;
								}
							}
							
							$uString=WriteUserVsReader($u,$userid);
							foreach ($disiked as $dsl)
							{				
								$vote=GetUserVoteForProposal($userid, $question, $dsl, $generation);
								
								$proposalNumber = WriteProposalNumberInternalLink($dsl, $room);
								//set_log($uString);
								//set_log($uString);
								
								echo '<div class="kpinfo">';
								
								echo "$uString voted against proposal $proposalNumber ";
								
								// Display users comment DONOW
								if (isset($ucomments[$dsl]) && $vote=="like")
								{
									echo " <span class=\"kpreadcomment\" title=\"{$ucomments[$dsl]['comment']}\">(Show Comment)</span> ";
									echo "<div class=\"kpcomment dislike\">{$ucomments[$dsl]['comment']}</div>";
								}
								
								echo "</br>The result would be simpler if they were to vote for it. ";
								
																
								if ($vote=="like")
								{
									echo "</br><b>Can you try to convince him?</b>";
								}
								echo "</div>";
								//echo "</br>";
							}
							//echo "</br>";
							foreach ($confusing as $dsl)
							{
								$proposalNumber = WriteProposalNumberInternalLink($dsl,$room);
								
								echo '<div class="kpinfo">';
								echo "$uString did not understand proposal $proposalNumber ";
								
								// Display users comment
								if (isset($ucomments[$dsl]) && $vote=="like")
								{
									echo " <span class=\"kpreadcomment\" title=\"{$ucomments[$dsl]['comment']}\">(Show Comment)</span> ";
									echo "<div class=\"kpcomment confused\">{$ucomments[$dsl]['comment']}</div>";
								}
								echo "</br>The result would be simpler if they were to vote for it. ";
								$vote=GetUserVoteForProposal($userid, $question, $dsl, $generation);
								if ($vote=="like")
								{
									echo "</br><b>Could you explain it?</b>";
								}
								echo "</div>";
								//echo "</br>";
							}
				
							//echo "</br>";
						}	
					}
					echo "</div>";					
					// Display Key Players -- End
				}
				
				if ($voting_settings['display_interactive_graphs'])
				{
				echo "<div class=\"feedback\">";
				echo " Above are the results IF the voting would end right now. If you think by voting differently you can get a better result, please change your vote below</div>";
				}
				
			}
			?>
			
			<!--
			<div class="relation_panel">Identify proposal relations: <span id="select_same">Equivalent Proposals</span><span class="plist"></span></div>
			-->
			
			<form autocomplete="off" method="post" id="votingform" action="endorse_confused_or_not.php">
			<input type="hidden" name="question" value="<?=$question?>" />
			<input type="hidden" name="hasvoted" value="<?=$userhasvoted?>" />
			<input type="hidden" name="generation" value="<?=$generation?>" />
			<table border="1" class="your_endorsements userproposal">
			<tr class="top">
			<th class="history_cell"><h4><?=$VGA_CONTENT['voting_hist_txt']?></h4></td>
			<th class="voting_info">
				<h3><?=$VGA_CONTENT['prop_sol_txt']?></h3>
				<p><b><?=$VGA_CONTENT['check_all_endorse_txt']?></b></p>
				</td>
			<th class="endorse_cell">&nbsp;</td>
			</tr>

			<?php

			while ($row = mysql_fetch_array($response))
			{
				$current_prop = $row['id'];
				
				echo '<tr class="user_vote" id="user_vote_' . $row['id'] . '">';
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
				else{ 
					$proposal = $row['id'];
					echo '&nbsp;'; }
				echo '</td>';
				
				echo '<td class="proposalcontent">';
				echo '<div class="paretoproposal">';
				
				if (isset($commentslist[$row['id']]))
				{
					echo '<img class="usrmsgpic" src="images/hascomments.jpg" title="'.count($commentslist[$row['id']]).' comments">';
				}
				
				$originalname=GetOriginalProposal($proposal);
				#print_r($originalname[proposalid]);
				if (!empty($row['abstract'])) {
					echo '<div class="paretoabstract"><a name="proposal'.$originalname['proposalid'].'"></a>';
					echo display_fulltext_link();
					echo '<h3>'.$originalname['proposalid'] .': '. $VGA_CONTENT['prop_abstract_txt'] .'</h3>';
					echo $row['abstract'] ;
					echo '</div>';
					echo '<div class="paretotext">';
					echo '<h3>'. $VGA_CONTENT['proposal_txt'] .'</h3>';
					echo $row['blurb'];
					echo '</div>';
				}
				else {
					echo '<div class="paretofulltext"><a name="proposal'.$originalname['proposalid'].'"></a>';
					echo '<h3>'.$originalname['proposalid'] .': ' . $VGA_CONTENT['proposal_txt'] . '</h3>';
					echo $row['blurb'] ;
					echo '</div>';
				}
				echo '</div>';
				?>	
			
				
				<div class="comments">
				
					<div class="commentsleft disagree">
					<h3>Disagree</h3>
					<div class="dislikecommentslist commentslist"></div>
					</div>
				
					<div class="commentsright confused">
					<h3>Don't Understand</h3>
					<div class="confusedcommentslist commentslist"></div>
					</div>
				
					<div class="clear"></div>
				
				</div> <!-- comments -->
				
				<div class="clear"></div><br/>
				<div class="commentform">
					<span class="intro"><p>Please tell us why you don't understand this proposal.</p> <p>Select a comment you agree with (if there are any) or write your own below.</p></p></span>
					<textarea class="comment" rows="20" cols="100" name="user_comment[<?=$current_prop?>]"></textarea>
				</div>
				</td>
				<td class="votes">
				<div class="user_vote">
				<div class="voting_choices">
				<img class="2" src="images/thumbdown.png" width="30" height="30" alt="" title="I Disagree">
				<img class="1" src="images/thumbup.png" width="30" height="30" alt="" title="I Agree">
				<?php if ($voting_settings['display_confused_voting_option']):?>
				<img class="3" src="images/confused.png" width="30" height="30" alt="" title="I Don't Understand">
				<?php endif;?>
				</div>
				<div class="voting_choice"></div>
				<input type="hidden" class="voting_choice_val" name="proposal[<?=$current_prop?>]" 
				value="<?=($userendorsedata[$current_prop]) ? $userendorsedata[$current_prop] : 1?>">
				<input type="hidden" class="prev_voting_choice_val" name="prev_proposal[<?=$current_prop?>]" 
				value="<?=$userendorsedata[$current_prop]?>">
				<input type="hidden" name="prev_commentid[<?=$current_prop?>]" 
				value="<?= isset($user_commentid[$current_prop]) ? $user_commentid[$current_prop] : 0  ?>">
				
				</div>
				</td></tr>
				
				<?php
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
	else	
		{echo " ";}
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
		#echo "subscribed=".$subscribed;

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
	//set_log('Not logged in - storing request');
	SetRequest();
}

include('footer.php');

?>
