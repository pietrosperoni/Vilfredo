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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
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
		<?php 	echo $headcommands; ?>
		<title>Vilfredo goes to Athens</title>
	</head>
	<?php
#	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

// ******************************************	
// 	Load System Serttings
require_once 'config.inc.php';
// ******************************************
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

		<h1><img src="images/titleimg.png" alt="Vilfredo goes to Athens" alt="Vilfredo goes to Athens"/></h1>

		<ul class="nav" id="top-nav">
			<li><a href="viewquestions.php?u= <?php echo $userid; ?>">View My Questions</a></li>
			<li><a href="viewquestions.php?todo=">ToDo List</a></li>
			<li><a href="http://metagovernment.org/wiki/Vilfredo">about</a></li>
			<li>Hello <?php echo get_session_username(); ?></li>
			<li><a href="editdetails.php">Update Email</a></li>
			<li><?php echo display_logout_link(); ?></li>							
		</ul>
	</div> <!-- header -->
<?php
}

// Not logged in
else 
{
?>
	<div id="header">

		<img src="images/pareto.png" id="paretoimg" alt="Illustration of Pereto"/>
		<img src="images/athens.png" id="athens" alt="Illustration of the Greek forum"/>

		<h1><img src="images/titleimg.png" alt="Vilfredo goes to Athens" /></h1>

		<ul class="nav">
			<li><a href="index.php">Home</a></li>
			<li><a href="viewquestions.php">View Questions</a></li>
			<li><a href="login.php">Login</a></li>
			<li><a href="register.php">Register</a></li>
		</ul>

	</div> <!-- header -->
 <?php
}
echo '</div>';
$current_room = GetParamFromQuery(QUERY_KEY_ROOM);
if  (!$current_room)
	$current_room = 'Common';
	
echo '<div id="room_title">Room: &nbsp;' . $current_room;
echo "</div>";

$rss_link = SITE_DOMAIN . '/rss.php';
if(strcasecmp($current_room, 'Common') != 0)
	$rss_link .= "?room=$current_room";
?>

<p><a class="rss-link" href="<?php echo $rss_link?>"> Subscribe to room feed (RSS)</a></p>

<form method="GET" action="viewquestions.php">
	<strong>Room:</strong>
	<input name="room" id="room" type="text" size="22" maxlength="20"/>
	<input type="submit" id="submit" value="Go!" />
</form>
<ul class="nav">
	<li><strong>Rooms:</strong></li>
	<li><a href="viewquestions.php">Common</a></li>
	<li><a href="viewquestions.php?room=Vilfredo" tooltip="this room is used to define the future of this website">Vilfredo</a></li>
	<li><a href="viewquestions.php?room=Politics" tooltip="Questions about politics">Politics</a></li>
	<li><a href="viewquestions.php?room=Metagovernment"  tooltip="Questions about the Metagovernment community">Metagovernment</a></li>
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
		<strong>Alert: This site will not display or work properly without Javascript enabled!</strong></p>
	</div>
</div>
<br />
</noscript>


<?php
include_once 'update_email_form.php';
?>

<div id="content_page">