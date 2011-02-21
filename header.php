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
	<meta http-equiv="expires" value="Thu, 16 Mar 2000 11:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />

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
		<title><?=$VGA_CONTENT['site_title_txt']?></title>
	</head>
	<?php
#	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
?>


<?php
echo '<body>';
echo LoadGoogleAnalytics();

// Get user ID if logged in
$userid=isloggedin();
//*******************************Header Links***************************
echo '<div id="top_links">';
// Logged in
if ($userid)
{
?>
	<div id="header">

	<a href="http://en.wikipedia.org/wiki/Vilfredo_Pareto"><img src="images/pareto.png" id="paretoimg" alt="Illustration of Pereto"/></a>
	<img src="images/athens.png" id="athens" alt="Illustration of the Greek forum"/>

		<h1><img src="images/titleimg.png" alt="<?=$VGA_CONTENT['site_title_txt']?>"/></h1>

		<ul class="nav" id="top-nav">
			<li><a href="viewquestions.php?u= <?php echo $userid; ?>"><?=$VGA_CONTENT['myquestions_link']?></a></li>
			<li><a href="viewquestions.php?todo="><?=$VGA_CONTENT['todo_link']?></a></li>
			<li><a href="http://metagovernment.org/wiki/Vilfredo"><?=$VGA_CONTENT['about_link']?></a></li>
			<li><?=$VGA_CONTENT['hello_txt']?> <?php echo get_session_username(); ?></li>
			<li><a href="editdetails.php"><?=$VGA_CONTENT['update_email_link']?></a></li>
			<!--  <li>Languages: <a href="<?=AppendToQuery('locale','en')?>">English</a> <a href="<?=AppendToQuery('locale','it')?>">Italian</a></li> -->
			<li><a href="logout.php"><?=$VGA_CONTENT['lougout_link']?></a></li>
		</ul>
	</div> <!-- header -->
<?php
}

// Not logged in
else 
{
?>
	<div id="header">

		<img src="images/pareto.png" id="paretoimg" alt="<?=$VGA_CONTENT['pareto_alt']?>"/>
		<img src="images/athens.png" id="athens" alt="<?=$VGA_CONTENT['forum_alt']?>"/>

		<h1><img src="images/titleimg.png" alt="<?=$VGA_CONTENT['site_title_txt']?>" /></h1>

		<ul class="nav">
			<li><a href="index.php"><?=$VGA_CONTENT['home_link']?></a></li>
			<li><a href="viewquestions.php"><?=$VGA_CONTENT['view_questions_link']?></a></li>
			<li><a href="login.php"><?=$VGA_CONTENT['login_link']?></a></li>
			<li><a href="register.php"><?=$VGA_CONTENT['register_link']?></a></li>
			<!-- <li>Languages: <a href="<?=$_SERVER['REQUEST_URI']?>&locale=en">English</a> <a href="<?=$_SERVER['REQUEST_URI']?>&locale=it">Italian</a></p></li> -->
		</ul>
	</div> <!-- header -->
 <?php
}
echo '</div>';
$query_room = GetParamFromQuery(QUERY_KEY_ROOM);
if  (!$query_room)
{
	$current_room = 'Common';
}
else
{
	$current_room = $query_room;
}
echo '<div id="room_title">' . $VGA_CONTENT['room_label'] . ': &nbsp;' . $current_room;
echo "</div>";
?>


<p><strong><?=$VGA_CONTENT['langs_label']?></strong> <a href="<?=AppendToQuery('locale','en')?>">English</a> <a href="<?=AppendToQuery('locale','it')?>">Italiano</a></p>
<p><a class="rss-link" href="<?php echo $rss_link?>"> <?=$VGA_CONTENT['room_feed_link']?></a></p>


<form method="GET" action="viewquestions.php">
	<strong><?=$VGA_CONTENT['room_label']?>:</strong>
	<input name="room" id="room" type="text" size="22" maxlength="20"/>
	<input type="submit" id="submit" value="<?=$VGA_CONTENT['go_button']?>
	" />
</form>
<ul class="nav">
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

<div id="content_page">