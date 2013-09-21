<?php
include('header.php');

?>
<script>
	var pids;
	var question;
	var room;
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
	log_error("Parameters not set: question = $question, room = $room");
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
	log_error("Failed to return question info for QID $question");
	header("Location: error_page.php");
	exit;
}

$title = $QuestionInfo['title'];
$content = $QuestionInfo['question'];
$generation = $QuestionInfo['roundid'];
$author = $QuestionInfo['usercreatorid'];

$question_url = SITE_DOMAIN."/viewquestion.php".CreateQuestionURL($question,$room);

if (getQuestionPhase($question) == 'evaluation')
{
	$url = "viewquestion.php?q=".$question;
	$url .= ($room != '') ? '&room='.$room : '';
	header("Location: ".$url);
	exit;
}

$room_title = ($room == '') ? 'Common' : $room;
$roomparam = ($room != '' ? '?room='.$room : '');
$questionsurl = "viewquestions.php".$roomparam;

$user = false;
$username = '';

if ($userid)
{
	$user = getuserdetails($userid);
	$username = $user['username'];
}

// Vote starts here ************************************

$pids = CalculatePareto($question, $generation);
set_log("List of PF proposals:");
set_log($pids);

?>
<script>
	pids = <?=json_encode($pids)?>;
	question = <?=json_encode($question)?>;
	room = <?=json_encode($room)?>;
</script>
<?php

set_log("Fetch pareto for Q $question for G $generation");
//$proposals = FetchParetoFront($question, $generation); // fixnow
$proposals = fetchProposalsFromIDs($pids);
set_log('$proposals');
set_log($proposals);
shuffle($proposals);

if ($userid)
{
	//CalculateParetoFrontFromProposals($proposals)
	$uservotes = getUserFinalVotes($userid, $pids);
	set_log('$uservotes');
	set_log($uservotes);
}
?>

<link rel="stylesheet" href="css/velocity.css" />

<script type="text/javascript" src="js/<?=SVG_DIR?>/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/json2.js"></script>
<script type="text/javascript" src="js/sprintf-0.6.js"></script> 
<script type="text/javascript" src="js/jquery-color.js"></script> 
<script type="text/javascript" src="js/jquery.raty.min.js"></script>


<script type="text/javascript">
	var installdir = '';
	var question = <?=$question?>;
	var room = '<?=$room?>';
	var questiondetails = <?=json_encode($QuestionInfo)?>;	
	
	function nl2br(str, is_xhtml) {   
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
	}
	
	function log(msg)
	{
		//if (console && console.log) console.log(msg);
		if (typeof console != 'undefined') console.log(msg);
	}
	
	<?php if ($userid) { ?>
	var userid = <?=$userid?>;
	<?php } else { ?>
	var userid = null;
	<?php } ?>

	function setSelectedQuestion()
	{
		$('#question_heading').html(questiondetails['title']);
		$('#creatorname').html(questiondetails['username']);
		$('#questioncontent').html(nl2br(questiondetails['question']));
		// add read all link if text too long
		// var content_el = $('#questioncontent')[0];
	}
	// Handle BUBBLING question
	// ***********************
	
	function isInt(n) 
	{
    	return +n === n && !(n % 1);
	}
	
	function refreshVoterCount()
	{
		log("refreshVoterCount called....");
		log(pids);
		//return;
		$.ajax({
			type: "POST",
			url: "./countFinalVoters.php",
			async: true,
			error: function ajax_error(jqxhr, status, error)
					{
						$('#usermsg').html("<?=$VGA_CONTENT['oops_internet_txt']?>");
					},
			data: ({pids : pids}),
			dataType: 'json',
			success: function(data, status)
			{
				data = jQuery.trim(data);
				log(data + " returned");

				if (data == 'error')
				{
					log("Could not retrieve voter count due to some error");
				}
				else if (parseInt(data) >= 0)
				{
					$('.votercount').html(parseInt(data) + ' people have voted so far.');
				}
				else
				{
					log(data);
				}
			}
		});
	}
	
	function refreshVoting()
	{
		$.ajax({
			type: "POST",
			url: "fetchQuestionPhase.php",
			async: true,
			error: function ajax_error(jqxhr, status, error)
					{
						$('#usermsg').html("<?=$VGA_CONTENT['oops_internet_txt']?>");
					},
			data: ({question : question}),
			dataType: 'html',
			success: function(data, status)
			{
				data = jQuery.trim(data);
				log(data + " returned");

				if (data == '0')
				{
					log("Could not retrieve phase due to some error");
				}
				else if (data == '3')
				{
					log("Question has been deleted");
				}
				else if (data == 'evaluation')
				{
					log("Question is in evaluation phase");
				}
				else if (data == 'closed')
				{
					log("Question is in closed phase");
				}
				else
				{
					log("Unknown string returned");
				}
			}
		});
	}
	
	function updateSaveColumn()
	{
		$('.stars').each(function(){
			var score = $(this).siblings('input').val();
			$(this).parent().siblings('.saved_vote').addClass("votecounted").html(score+'/10');
		});
	}
	
	function updateTableEntry(pid, vote)
	{
		var $showsaved = $('td[name="savedvote[' + pid + ']"]');
		$showsaved.text(vote+"/10");
		if ($showsaved.hasClass("votecounted"))
		{
			$showsaved.animate({backgroundColor: "#00FF33"},"slow", function(){
		    	$(this).animate({backgroundColor: "#d0f5a9"},"slow");
			});
		}
		else
		{
			$showsaved.addClass("votecounted");
		}
	}

	function submitVotes()
	{
		$('#usermsg').removeClass('error').html("");
		
		var info = $("form").serialize();
		//alert(info);
		$.ajax({
			type: "POST",
			url: "submitVotes.php",
			cache: false,
			async: false,
			error: function ajax_error(jqxhr, status, error)
					{
						$('#usermsg').html("<?=$VGA_CONTENT['oops_internet_txt']?>");
					},
			data: info,
			dataType: 'html',
			success: function(data, status)
			{
				refreshVoterCount();
				data = jQuery.trim(data);
				if (data == '0')
				{
					$('#usermsg').addClass('error').html("<?=$VGA_CONTENT['some_error_txt']?>").fadeIn(1000);
					setTimeout(function() {
						$('#usermsg').fadeOut(1000, function(){
							$(this).removeClass('error').html('');
						});
					}, 4000);
				}
				else if (data == '1')
				{
					$('#usermsg').html("<?=$VGA_CONTENT['votes_recorded_msg']?> <img src=\"images/grn_tick_trans.gif\" width=\"20\" height=\"20\"/>").fadeIn(1000);
					$('.saved_vote').animate({backgroundColor: "#00FF33"},"slow", function(){
					    $(this).animate({backgroundColor: "#d0f5a9"},"slow");
					});
					updateSaveColumn();
					setTimeout(function() {
						$('#usermsg').fadeOut(1000, function(){
							$(this).html('');
						});
					}, 4000);
				}
				else
				{
					//log("submitVotes() returned an error: " + data);
					$('#usermsg').addClass('error').html("<?=$VGA_CONTENT['problem_txt']?>").fadeIn(1000);
					setTimeout(function() {
						$('#usermsg').fadeOut(1000, function(){
							$(this).removeClass('error').html('');
						});
					}, 4000);
				}
			}
		});
	}
	//
	function resetDisplayedVote(pid)
	{
		var $stars = $('.stars[pid="' + pid  + '"]');
		var $showsaved = $('td[name="savedvote[' + pid + ']"]');
		var storedvote = $stars.data("storedvote");
		$stars.raty('score', storedvote);
		$stars.parent().next().children('.showvote').text(storedvote+'/10');
	}
	function submitVote(userid, pid, vote)
	{
		//disableUI();
		$('#usermsg').removeClass('error').html("");

		//alert(info);
		$.ajax({
			type: "POST",
			url: "submitVote.php",
			cache: false,
			async: true,
			error: function ajax_error(jqxhr, status, error)
					{
						$('#usermsg').html("<?=$VGA_CONTENT['oops_internet_txt']?>");
					},
			data: ({tv_pid : pid, tv_vote : vote, tv_userid : userid, question: question}),
			dataType: 'html',
			success: function(data, status)
			{
				refreshVoterCount();
				data = jQuery.trim(data);
				if (data == 'closed')
				{
					var url = "viewvotingresults.php?q=" + question;
					if (room != '')
					{
						url += '&room=' + room;
					}
					window.location.replace(url);
				}
				else if (data == 'evaluating')
				{
					var url = "viewquestion.php?q=" + question;
					if (room != '')
					{
						url += '&room=' + room;
					}
					window.location.replace(url);
				}
				else if (data == '0')
				{
					$('#usermsg').addClass('error').html("<?=$VGA_CONTENT['some_error_txt']?>").fadeIn(1000);
					setTimeout(function() {
						$('#usermsg').fadeOut(1000, function(){
							$(this).removeClass('error').html('');
							resetDisplayedVote(pid);
						});
					}, 4000);
				}
				else if (data == '1')
				{
					//$('#usermsg').html("<?=$VGA_CONTENT['votes_recorded_msg']?> <img src=\"images/grn_tick_trans.gif\" width=\"20\" height=\"20\"/>").fadeIn(1000);
					//enableUI();
					updateTableEntry(pid, vote);
					setTimeout(function() {
						$('#usermsg').fadeOut(1000, function(){
							$(this).html('');
						});
					}, 4000);
				}
				else
				{
					//log("submitVotes() returned an error: " + data);
					$('#usermsg').addClass('error').html("<?=$VGA_CONTENT['problem_txt']?>").fadeIn(1000);
					//enableUI();
					setTimeout(function() {
						$('#usermsg').fadeOut(1000, function(){
							$(this).removeClass('error').html('');
							resetDisplayedVote(pid);
						});
					}, 4000);
				}
			}
		});
	}
</script>



<h1>Final Voting Stage</h1>

<div>
	<div class="votingquestion">
		<h3 id="question_heading"></h3>
		<span class="question_asker"><?=$VGA_CONTENT['cite_txt']?> <span id="creatorname"></span></span><a id="viewquestioncontentlink" href=""><?=$VGA_CONTENT['view_full_txt_link']?></a>
		<div id="questioncontent"></div>
	</div><!-- votingquestion -->
</div>

<br />

<?php
if (!$userid)
{
	$request = $bubblesdir."/voting.php?q=$question";
	$request .= ($room != '') ? "&room=$room" : '';
	$_SESSION['request'] = $request;
?>

<p><?=$VGA_CONTENT['login_finalvote_txt']?></p>

<a class="user_login_link" href="../login.php"><?=$VGA_CONTENT['login_txt']?></a>

<?php
}
else
{
?>

<div class="bubblescontent">
<h2><?=$VGA_CONTENT['welcome_usr_txt']?> <?=$username?></h2>

<p>The proposing and endorsing phase for this question has ended. It\'s time for you to evaluate the winning proposals from the existing winners.</p><p>Rate each proposal below from 0 to 10, than click the button to register your votes.</p><p>To maximise your influence you should try to have you lowest vote at 0/10 and your highest vote at 10/10.</p>

<div class="votercount"></div>

<?php if ($userid and $userid == $author) {?>
<form autocomplete="off" method="post" action="movebacktoevaluation.php">
Return this question to evaluation:
	<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
	<input type="submit" name="submit" id="submit" value="Move Back to Evaluation" />
</form>
<form autocomplete="off" method="post" action="closequestion.php">
Or close this question and display the final results page:
	<input type="hidden" name="question" id="question" value="<?php echo $question; ?>" />
	<input type="submit" name="submit" id="submit" value="Close Question" />
</form>
<?php } ?>
</div> <!-- bubblescontent -->

<div id="feedbacksection">
	<span id="finalvotebtn"><?=$VGA_CONTENT['final_vote_btn']?></span>
	<span id="usermsg"></span>
</div>


<?php
if (!$proposals) :
?>
<p><?=$VGA_CONTENT['some_error_txt']?></p>
<?php
endif;
?>

<form method="post" action="">
<table class="final_voting_table" border="1">
<th><?=$VGA_CONTENT['proposals_txt']?></th><th colspan="2"><?=$VGA_CONTENT['set_votes_txt']?></th><th id="savedvoteshdr"><?=$VGA_CONTENT['saved_votes_txt']?></th>
<?php
foreach ($proposals as $proposal) :
	$pid = $proposal['id'];
	$savedvotestr = (isset($uservotes[$pid])) ? (string)$uservotes[$pid] : '';
	$currentvote = (isset($uservotes[$pid])) ? $uservotes[$pid] : 5;
	$savedvote = (isset($uservotes[$pid])) ? $uservotes[$pid]."/10" : "--";
	$savedvoteclass = (isset($uservotes[$pid])) ? "saved_vote votecounted" : "saved_vote";
?>
<tr>
	<td class="prop_blurb"><?=nl2br($proposal['blurb'])?></td>
	<td class="">
		<div class="stars" data-rating="<?=$currentvote?>" pid="<?=$pid?>" stored-vote="<?=$currentvote?>"></div>
		<input name="proposal[<?=$pid?>]" type="hidden" value="<?=$currentvote?>">
	</td>
	<td class="user_score"><div class="showvote"></div></td>
	<td name="savedvote[<?=$pid?>]" class="<?=$savedvoteclass?>"><?=$savedvote?></td>
</tr>
<?php
endforeach;
?>

</table>

<br />


<input name="userid" type="hidden" value="<?=$userid?>">
<input name="question" type="hidden" value="<?=$question?>">
</form>

<?php
} // User logged in
?>

<div id="backgroundPopup"></div>
<div id="loader"></div>
<div id="refreshing">
	<p><?=$VGA_CONTENT['refreshing_txt']?></p>
</div>
<div id="feedback">
	<span class="popupcontent"></span>
</div>

<br /><br /><br />

<script type="text/javascript">
$(function() {	
	refreshVoterCount();
	
	$("#finalvotebtn").mouseenter(function(event){
		$(this).addClass("over");
	});
	$("#finalvotebtn").mouseleave(function(event){
		$(this).removeClass("over");
	});
	$("#viewquestioncontentlink").click(function (e) 
	{
		var $content = $('#questioncontent');
		if ($content.is(":hidden"))
		{
			$content.slideDown();
			$(this).html("<?=$VGA_CONTENT['hide_full_txt_link']?>");
		}
		else
		{
			$content.slideUp();
			$(this).html("<?=$VGA_CONTENT['view_full_txt_link']?>");
		}
		e.preventDefault();
	});
	setSelectedQuestion();		
	$('.stars').each(function(){
		//Add pid
		$(this).data("pid", $(this).attr('pid'));
		$(this).data("storedvote", $(this).attr('stored-vote'));
		var id = parseInt($(this).attr('id'));
		var currentvote = parseInt($(this).attr('data-rating'));
		//$(this).data('vote', currentvote);
		//$(this).data('id', id);
		$(this).parent().next().children('.showvote').text(currentvote+'/10');
		$(this).raty({
			path: 'images/',
			number: 10,
			cancel: true,
			cancelHint: '0',
			width: 230,
			hints: [null, null, null, null, null, null, null, null, null, null],
			score: function() {
				return $(this).attr('data-rating'); 
			},
			click: function(score, evt) {
				//alert($(this).data("pid"));
				if (score == null)
				{
					//$(this).data('vote', 0);
					$(this).siblings('input').val(0);
					$(this).parent().next().children('.showvote').text('0/10');
				}
				else
				{
					//$(this).data('vote', score);
					$(this).siblings('input').val(score);
					$(this).parent().next().children('.showvote').text(score+'/10');
				}	
				//alert($(this).next('input').attr('name'));
				//alert($(this).siblings('input').val());
				// Added to record individual votes
				//
				var pid = $(this).data("pid");
				var vote = (score == null) ? 0 : score;
				submitVote(userid, pid, vote);
				//
			},
			mouseover : function(score, evt) {
			    var target = $(this).parent().next().children('.showvote');

			    if (score === null) {
			      target.html('0/10');
			    } else if (score === undefined) {
			      target.html('0/10');
			    } else {
			      target.html(score + '/10');
			    }
			  }
		});
	});
	
	$('#finalvotebtn').click(function(event) {
		submitVotes();
	});
});
</script>

<?php
require_once 'footer.php';
?>