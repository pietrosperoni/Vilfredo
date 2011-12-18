<?php
/**
*  Software License Agreement (Affero GPL)
*  
*  Vilfredo
*  You can use this system to make decisions amongst groups of people.
*  Vilfredo is used to really explore the possible alternative answers to an open question, 
*  and find an answer endorsed by as many people as possible (theorethically everybody). 
*  As such the question should be an open question. 
*  The Vilfredo project is at http://www.vilfredo.org
*  Copyright (C) 2009  Vilfredo.org
*  
*  This program is free software: you can redistribute it and/or modify
*  it under the terms of the GNU Affero General Public License as
*  published by the Free Software Foundation, either version 3 of the
*  License, or (at your option) any later version.
*  
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU Affero General Public License for more details.
*  
*  You should have received a copy of the GNU Affero General Public License
*  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*  
*/
// Start output buffer
ob_start();
// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';

// load phrases
//$common_lang_page = 'common';
//$VGA_CONTENT[$common_lang_page] = loadLangXML($common_lang_page, $lang);
//eg $VGA_CONTENT[$common_lang_page]['site_title_txt']

if (isset($_GET["locale"]) and ($_GET["locale"] == 'en' or $_GET["locale"] == 'it' ))
{
	$locale = $_GET["locale"];
	$_SESSION['locale'] = $locale;
}
elseif (isset($_SESSION["locale"]) and ($_SESSION["locale"] == 'en' or $_SESSION["locale"] == 'it' ))
{
	$locale = $_SESSION["locale"];
}
else
{
	$locale = fetch_preferred_language_from_client();
	$_SESSION['locale'] = $locale;
}

//set_log((int)isset($_GET["locale"]));
//set_log(locale);

//$locale = "it"; // For debugging
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
//printbrx(getenv('LC_ALL'));
@include getLanguage($locale);
//print_array($VGA_CONTENT);
//print_array($_SERVER);
//printbrx($_SERVER['REQUEST_URI']);
//exit;
// ******************************************
// Create RSS link
$rss_link = CreateRSSLink();
#<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
#   "http://www.w3.org/TR/html4/loose.dtd">

// ******************************************
?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<title><?=$VGA_CONTENT['site_title_txt']?></title>
	<meta http-equiv="expires" content="Thu, 16 Mar 2000 11:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />
	<!--[if IE8]>
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<![endif]-->

		<link rel="stylesheet" type="text/css" href="style.css" media="screen, print" >
		<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="css/ie-sucks.css" media="screen, print" />
		<![endif]-->
		<!--[if IE6]>
		<link rel="stylesheet" type="text/css" href="css/ie6-sucks.css" media="screen, print" />
		<![endif]-->
		<link rel="stylesheet" type="text/css" href="widgets.css">
		<link rel="alternate" type="application/rss+xml" href="<?php echo $rss_link; ?>">
		<?php echo $headcommands; ?>
	</head>
	<?php
#	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
?>


<?php
echo '<body>';
echo LoadGoogleAnalytics();

// Get user ID if logged in
$userid=isloggedin();
$userquestioncount = 0;
if ($userid)
{
	$userquestioncount = getUserQuestionCount($userid);
}
//*******************************Header Links***************************
echo '<div id="content_page">';
?>
	<div id="header">
		<div id="top-pics">
			<a href="http://en.wikipedia.org/wiki/Vilfredo_Pareto">
			<img src="images/pareto.png" width="95" height="126" id="paretoimg" alt="Illustration of Pereto"/></a>
			<img src="images/athens.png" width="194" height="120" id="athens" alt="Illustration of the Greek forum"/><!-- <img src="images/titleimg.png" id="titleimg" width="462" height="97" alt="<?=$VGA_CONTENT['site_title_txt']?>"/> -->
			<div id="vilfredo_title">
				<img src="images/vilfredo_title_s.png" width="206" height="40" alt="<?=$VGA_CONTENT['site_title_txt']?>"/>
				<img src="images/goes_title_s.png" width="104" height="40" alt="<?=$VGA_CONTENT['site_title_txt']?>"/>
				<img src="images/to_title_s.png" width="77" height="40" alt="<?=$VGA_CONTENT['site_title_txt']?>"/>
				<img src="images/athens_title_s.png" width="161" height="40" alt="<?=$VGA_CONTENT['site_title_txt']?>"/>
			</div>
		</div> <!-- top-pics -->
	
		<ul class="nav" id="top-nav">
		<li><a href="viewquestions.php"><?=$VGA_CONTENT['home_link']?></a></li>
		<?php
		if ($userid)
		{
			if ($userquestioncount)
			{ ?>
			<li><a href="viewquestions.php?u=<?=$userid?>"><?=$VGA_CONTENT['myquestions_link']?></a></li>
			<?php
			}
			else
			{ ?>
			<li class="disabled-link" title="You currently have no questions to view"><?=$VGA_CONTENT['myquestions_link']?></li>
			<?php
			} ?>
			
			<li><a href="viewquestions.php?todo="><?=$VGA_CONTENT['todo_link']?></a></li>
			<li><a href="http://metagovernment.org/wiki/Vilfredo"><?=$VGA_CONTENT['about_link']?></a></li>
			<li><?=$VGA_CONTENT['hello_txt']?> <?=get_session_username()?></li>
			<li><a href="editdetails.php"><?=$VGA_CONTENT['update_email_link']?></a></li>
			<li><a href="logout.php"><?=$VGA_CONTENT['lougout_link']?></a></li>
			
		<?php
		}		
		// Not logged in
		else 
		{
		?>
			<li><a href="login.php"><?=$VGA_CONTENT['login_link']?></a></li>
			<li><a href="register.php"><?=$VGA_CONTENT['register_link']?></a></li>
			<li><?php echo facebook_login_header_button_refresh(DISPLAY_FACEBOOK_LOGIN); ?></li>
		<?php 
		}		
		?>
			<li><a href="feedback.php">Feedback</a></li>
		</ul>
	</div> <!-- header -->

<?php
echo '<div id="innerheader">';
$query_room = GetParamFromQuery(QUERY_KEY_ROOM);
if  (!$query_room)
{
	$current_room = 'Common';
}
else
{
	$current_room = $query_room;
}
?>

<div id="room_title"><?=$VGA_CONTENT['room_label']?>:&nbsp; <?=$current_room?><a href="<?=$rss_link?>"><img src="images/rss.jpg" width="19" height="19" alt="RSS Feed" /></a></div>

<span id="langs"><strong><?=$VGA_CONTENT['langs_label']?></strong> <a href="<?=AppendToQuery('locale','en')?>"><img src="images/en.png" width="16" height="16" alt="Select English" /></a> <a href="<?=AppendToQuery('locale','it')?>"><img src="images/it.png" width="16" height="16" alt="Select Italian" /></a></span>

<!-- <div id="roomfeed"><a class="rss-link" href="<?php echo $rss_link?>"> <?=$VGA_CONTENT['room_feed_link']?></a></div> -->

<div class="navbar">
<form method="get" action="viewquestions.php">
	<strong><?=$VGA_CONTENT['room_label']?>:</strong>
	<input name="room" id="room" type="text" size="22" maxlength="20"/>
	<input type="submit" id="submit" value="<?=$VGA_CONTENT['go_button']?>"/>
</form>

<ul class="innernav">
	<li><strong><?=$VGA_CONTENT['rooms_txt']?>:</strong></li>
	<li><a href="viewquestions.php">Common</a></li>
	<li><a href="viewquestions.php?room=Vilfredo" tooltip="<?=$VGA_CONTENT['vilfredo_tooltip']?>">Vilfredo</a></li>
	<li><a href="viewquestions.php?room=Politics" tooltip="<?=$VGA_CONTENT['politics_room_tooltip']?>">Politics</a></li>
	<li><a href="viewquestions.php?room=Metagovernment"  tooltip="<?=$VGA_CONTENT['metagov_room_tooltip']?>">Metagovernment</a></li>
	<?php 
	if  ($current_room!="Common" and $current_room!="Vilfredo" and $current_room!="Politics" and $current_room!="Metagovernment")
	{ 
	QUERY_KEY_ROOM
	?>
		<li><a href="viewquestions.php<?php echo CreateNewRoomURL(); ?>"><?php echo $current_room; ?></a></li>
	<?php
	} 
	?>
	</ul>
	
	</div> <!-- navbar -->
	
	</div> <!-- innerheader -->
	
<noscript>
<br />
<div class="ui-widget">
	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em; text-size: 1.2em;"></span> 
		<strong><?=$VGA_CONTENT['js_alert_txt']?></strong></p>
	</div>
</div>
<br />
</noscript>


<?php
include_once 'update_email_form.php';

//printbr(htmlentities($VGA_CONTENT['succ_prop_create_id_txt'], ENT_COMPAT));
//printbr($VGA_CONTENT['succ_prop_create_id_txt']);
?>

