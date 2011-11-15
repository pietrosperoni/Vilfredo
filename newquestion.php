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
<!-- <link type="text/css" href="widgets.css" rel="stylesheet" /> -->

<script type="text/javascript" src="js/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
<!-- <script type="text/javascript" src="js/jquery/retweet.js"></script> -->
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
//if ($userid)
//{
	$randomID = getUniqueRoomCode();
	$room_param = GetParamFromQuery(QUERY_KEY_ROOM);
?>

<script type="text/javascript">

var roomId = <?php echo "'$randomID'" ?>;
</script>

<?php

#echo '<div id="leftfloatbox">';

    ?>
    <div id="actionbox">
	<p>
    <h2><?=$VGA_CONTENT['create_quest_txt']?></h2>
	<form method="POST" action="newquestiontake.php">
	<p><strong><?=$VGA_CONTENT['create_room_txt']?></strong>: <input name="room_id" id="room_id" type="text" size="22" maxlength="20" value="<?php echo $room_param?>"/> <input name="getRoomID" value="Generate Room ID" type="button" onclick="document.getElementById('room_id').value=roomId"/></p>
	<p><strong><?=$VGA_CONTENT['leave_blank_txt']?></strong></p>
	<p><strong><?=$VGA_CONTENT['invite_exp_txt']?></strong></p>
	<p><?=$VGA_CONTENT['new_quest_exp_txt']?></p><p><code>http://vilfredo.org/viewquestion.php?q=67&room=vilfredo</code></p>


	<div id="choosequestiontype">
	<h3>Select Type of Question</h3>
	<p><input id="vgaqtype" type = "radio" value="question" name ="questiontype" title="" /> Create a Vilfredo Open Question</p>
	<p><input id="vgabtype" type = "radio" value="bubble" name ="questiontype" title=""  /> Create a Question Bubble (New)</p>
	</div>


	<p id="bubblequestionintro" class="quest-type-intro" style="display:none;">Bubble questions should be Open Questions looking for a number of Proposed Solutions.</p>
	
	<p id="vgaquestionintro" class="quest-type-intro"><?=$VGA_CONTENT['open_exp_txt']?> <a href="FAQ.php"><?=$VGA_CONTENT['faq_link']?>.</a></p>
	

    <h3><?=$VGA_CONTENT['title_max_txt']?>
     <input type="text" size="100" maxlength="100" name="title" class="title"/></h3>
   
   
<div id="editor_panel">
    <h3><?=$VGA_CONTENT['content_txt']?>

<div id="proposal_RTE">
      <textarea id="content" name="question" class="jqrte_popup" rows="500" cols="70"></textarea>
      <?php
         include_once("js/jquery/RichTextEditor/content_editor.php");
         include_once("js/jquery/RichTextEditor/editor.php");


/*
$min = $VGA_CONTENT['min'];
$mins = $VGA_CONTENT['mins'];
$hour = $VGA_CONTENT['hour'];
$hours = $VGA_CONTENT['hours'];
$day = $VGA_CONTENT['day'];
$days = $VGA_CONTENT['days'];
$week = $VGA_CONTENT['week'];
$weeks = $VGA_CONTENT['weeks'];
$week_and_a_half = $VGA_CONTENT['week and a half'];
*/
      ?>      
      </h3>
      
      
  <script type="text/javascript" src="<?=BUBBLES_DIR?>/js/cookies/jquery.cookie.js"></script>
 
 <script type="text/javascript">
 var cookieexpires = 3; //days
  $(function() {
 	 $("input:radio[name=questiontype]").click(function() {
		 if ($(this).val() == 'bubble')
		 {
		 	$.cookie('btab', 'b', {expires: cookieexpires});
		 	$('#vgaquestionoptions').slideUp(1000);
		 	$('#vgaquestionintro').fadeOut(1000, function() {
		 		$('#bubblequestionintro').fadeIn(1000);
		 	});
		 	$('#choosequestiontype').css({'background-color' : '#3399FF', 'color' : 'white', 'background-image' : 'url(images/bubble-small.gif)'});
		 }
		 else
		 {
		 	$.cookie('btab', null);
		 	$('#vgaquestionoptions').slideDown(1000);
		 	$('#bubblequestionintro').fadeOut(1000, function() {
				$('#vgaquestionintro').fadeIn(1000);
		 	});
		 	$('#choosequestiontype').css({'background-color' : '#cdffcc', 'color' : 'black', 'background-image' : 'url(images/pareto_fb.png)'});
		 }
   	});
   	
   	if ($.cookie('btab'))
	{
		//$("input:radio[name=questiontype]").click();
		//$("input:radio[value=question]").attr('checked', '');
		//$("input:radio[value=bubble]").attr('checked', 'checked');
		//$('input[name="questiontype"]').attr('checked', false);
		//$('#vgaqtype').attr('checked', '');
		$('#vgabtype').attr('checked', 'checked').click();
	}
	else
	{
		$('#vgaqtype').attr('checked', 'checked');
		//$('#vgabtype').attr('checked', '');
	}
 });
</script>

      
    <div id="vgaquestionoptions">  
    
  <!--   <h3>Complete the Following Options for a VGA Open Question Only</h3> -->
    
      <?=$VGA_CONTENT['min_tme_txt']?>
      <select name="minimumtime" title="<?=$VGA_CONTENT['time_exp_title']?>">
  <option value="60">1  <?= $VGA_CONTENT['time_minute_txt'] ?></option>
  <option value="120">2  <?= $VGA_CONTENT['time_minutes_txt'] ?></option>
  <option value="300">5  <?= $VGA_CONTENT['time_minutes_txt'] ?></option>
  <option value="3600">1 <?= $VGA_CONTENT['time_hour_txt'] ?></option>
  <option value="7200">2 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
  <option value="10800">3 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
  <option value="21600">6 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
  <option value="43200">12 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
  <option value="64800">18 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
  <option value="86400" selected="yes" >1 <?= $VGA_CONTENT['time_day_txt'] ?></option>
  <option value="172800">2 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="259200">3 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="302400"><?= $VGA_CONTENT['time_halfweek_txt'] ?></option>
  <option value="345600">4 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="432000">5 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="518400">6 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="604800">1 <?= $VGA_CONTENT['time_week_txt'] ?></option>
  <option value="864000">10 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="907200"><?= $VGA_CONTENT['time_weekandahalf_txt'] ?> </option>
  <option value="1209600">2 <?= $VGA_CONTENT['time_weeks_txt'] ?></option>
  <option value="1296000">15 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="1728000">20 <?= $VGA_CONTENT['time_days_txt'] ?></option>
  <option value="1814400">3 <?= $VGA_CONTENT['time_weeks_txt'] ?></option>
  <option value="2419200">4 <?= $VGA_CONTENT['time_weeks_txt'] ?></option>
  <option value="2592000">30 <?= $VGA_CONTENT['time_days_txt'] ?> / 1 month</option>
  <option value="2678400">31 <?= $VGA_CONTENT['time_days_txt'] ?></option>
</select> <br/>
<?=$VGA_CONTENT['max_time_txt']?>
<select name="maximumtime" title="<?=$VGA_CONTENT['auto_exp_title']?>">
  <option value="60">1  <?= $VGA_CONTENT['time_minute_txt'] ?></option>
    <option value="120">2  <?= $VGA_CONTENT['time_minutes_txt'] ?></option>
    <option value="300">5  <?= $VGA_CONTENT['time_minutes_txt'] ?></option>
    <option value="3600">1 <?= $VGA_CONTENT['time_hour_txt'] ?></option>
    <option value="7200">2 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
    <option value="10800">3 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
    <option value="21600">6 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
    <option value="43200">12 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
    <option value="64800">18 <?= $VGA_CONTENT['time_hours_txt'] ?></option>
    <option value="86400">1 <?= $VGA_CONTENT['time_day_txt'] ?></option>
    <option value="172800">2 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="259200">3 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="302400"><?= $VGA_CONTENT['time_halfweek_txt'] ?></option>
    <option value="345600">4 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="432000">5 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="518400">6 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="604800" selected="yes" >1 <?= $VGA_CONTENT['time_week_txt'] ?></option>
    <option value="864000">10 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="907200"><?= $VGA_CONTENT['time_weekandahalf_txt'] ?> </option>
    <option value="1209600">2 <?= $VGA_CONTENT['time_weeks_txt'] ?></option>
    <option value="1296000">15 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="1728000">20 <?= $VGA_CONTENT['time_days_txt'] ?></option>
    <option value="1814400">3 <?= $VGA_CONTENT['time_weeks_txt'] ?></option>
    <option value="2419200">4 <?= $VGA_CONTENT['time_weeks_txt'] ?></option>
    <option value="2592000">30 <?= $VGA_CONTENT['time_days_txt'] ?> / 1 month</option>
  <option value="2678400">31 <?= $VGA_CONTENT['time_days_txt'] ?></option>
</select> <br/>
<?=$VGA_CONTENT['max_min_exp_txt']?><br/><br/>

<h4><?=$VGA_CONTENT['perm_anon_txt']?></h4>
<p><?=$VGA_CONTENT['permit_anon_txt']?></p>
<p><Input type = "Checkbox" Name ="permit_anon_votes" id="permit_anon_votes" title="<?=$VGA_CONTENT['anon_votes_title']?>" value="" /> <?=$VGA_CONTENT['anon_vote_txt']?></p>
<p><Input type = "Checkbox" Name ="permit_anon_proposals" id="permit_anon_proposals" title="<?=$VGA_CONTENT['anon_props_title']?>" value="" /> <?=$VGA_CONTENT['anon_props_txt']?></p>

</div>

	<?php 
	if ($userid) {
		$regclass = "submit_ok";
	} else {
		$regclass = "reg_submit";
	}
	?>
	
	<input class="rte_submit <?= $regclass; ?>" type="button" name="submit_nq" id="submit_nq" value="<?=$VGA_CONTENT['create_question_button']?>
	"/>

<!--<input type="submit" name="submit_nq" id="submit_nq" value="Create question" />--?

<br />
</div> <!-- proposal_RTE -->
</div> <!-- editor_panel -->
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
	<?php

	echo '</div>';
#	echo '</div>';
	echo '<br/>';

/*
}
else
{
		DoLogin();
}*/

include('footer.php');


?>