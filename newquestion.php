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
<script type="text/javascript" src="js/vilfredo.js"></script>';

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
    <h2>Create a New Question</h2>
	<form method="POST" action="newquestiontake.php">
	<p><strong>Create a new room for your question (Optional)</strong>: <input name="room_id" id="room_id" type="text" size="22" maxlength="20" value="<?php echo $room_param?>"/> <input name="getRoomID" value="Generate Room ID" type="button" onclick="document.getElementById('room_id').value=roomId"/></p>
	<p><strong>Important: Leave this blank if you want <i>everyone</i> to see your question!</strong></p>
	<p><strong>The people you invite to submit proposals, however, <i>will</i> see your new question listed on their ToDo List page.</strong></p>
	<p>When you ask a new question you are given the option to assign it to a room. You can name the room (alpha-numeric characters and underscores only) or generate a random name - good if you want privacy. Only people who know the room will see your question. Furthermore, room names beginning with an underscore '_' will not appear in future room searches. This will also be true if you name your room something like '_Politics', however such a name will be easily guessed and may still be found. For the moment, in order to view a question in a room you need to enter the question number and the room parameter in the URL, eg</p><p><code>http://vilfredo.org/viewquestion.php?q=67&room=vilfredo</code></p>

	<p><strong>The question must be an open question</strong>: A question that has multiple possible answers.<br/>
    So many, in fact, that you cannot think yourself of ALL the alternatives! <br/>
    Open questions generally start with 'what' or 'how', although other formulations are also possible. <br/>
    Yes/no questions are not the right type of question to ask over here. <br/>
    Please see the <a href="FAQ.php">frequently asked questions.</a></p>

    <h3>Title (max 100 ch):
     <input type="text" size="100" maxlength="100" name="title" class="title"/></h3>
   
   
<div id="editor_panel">
    <h3>Content:

<div id="proposal_RTE">
      <textarea id="content" name="question" class="jqrte_popup" rows="500" cols="70"></textarea>
      <?php
         include_once("js/jquery/RichTextEditor/content_editor.php");
         include_once("js/jquery/RichTextEditor/editor.php");

      ?></h3>
      Minimum Time:
      <select name="minimumtime" title="after this time, from when the first person has voted or proposed you are allowed to move the question on">
  <option value="60">1 min</option>
  <option value="120">2 min</option>
  <option value="300">5 min</option>
  <option value="3600">1 hour</option>
  <option value="7200">2 hours</option>
  <option value="10800">3 hours</option>
  <option value="21600">6 hours</option>
  <option value="43200">12 hours / half a day</option>
  <option value="64800">18 hours</option>
  <option value="86400" selected="yes" >1 day</option>
  <option value="172800">2 days</option>
  <option value="259200">3 days</option>
  <option value="302400">half a week</option>
  <option value="345600">4 days</option>
  <option value="432000">5 days</option>
  <option value="518400">6 days</option>
  <option value="604800">1 week</option>
  <option value="864000">10 days</option>
  <option value="907200">1 week and a half</option>
  <option value="1209600">2 weeks</option>
  <option value="1296000">15 days</option>
  <option value="1728000">20 days</option>
  <option value="1814400">3 weeks</option>
  <option value="2419200">4 weeks</option>
  <option value="2592000">30 days / 1 month</option>
  <option value="2678400">31 days</option>
</select> <br/>
Maximum Time:
<select name="maximumtime" title="after this time, from when the first person has voted or proposed the system will automatically move on">
  <option value="60">1 min</option>
  <option value="120">2 min</option>
  <option value="300">5 min</option>
  <option value="3600">1 hour</option>
  <option value="7200">2 hours</option>
  <option value="10800">3 hours</option>
  <option value="21600">6 hours</option>
  <option value="43200">12 hours / half a day</option>
  <option value="64800">18 hours</option>
  <option value="86400">1 day</option>
  <option value="172800">2 days</option>
  <option value="259200">3 days</option>
  <option value="302400">half a week</option>
  <option value="345600">4 days</option>
  <option value="432000">5 days</option>
  <option value="518400">6 days</option>
  <option value="604800" selected="yes" >1 week</option>
  <option value="864000">10 days</option>
  <option value="907200">1 week and a half</option>
  <option value="1209600">2 weeks</option>
  <option value="1296000">15 days</option>
  <option value="1728000">20 days</option>
  <option value="1814400">3 weeks</option>
  <option value="2419200">4 weeks</option>
  <option value="2592000">30 days / 1 month</option>
  <option value="2678400">31 days</option>
</select> <br/>
If you chose a maximum time smaller than the minimum time, then the system will never ask you to move on, but will just do it automatically.
Yes, this is a feature!)<br/><br/>

<h4>Anonymous Users</h4>
<p><Input type = "Checkbox" Name ="permit_anon" id="permit_anon" title="Check this box if you wish to permit anonymous users to vote and create proposals for this question" value="" /> Check this box if you wish to allow anonymous users to vote and create proposals for this question.</p>

	<?php 
	if ($userid) {
		$regclass = "submit_ok";
	} else {
		$regclass = "reg_submit";
	}
	?>
	
	<input class="rte_submit <?= $regclass; ?>" type="button" name="submit_nq" id="submit_nq" value="Create question"/>

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