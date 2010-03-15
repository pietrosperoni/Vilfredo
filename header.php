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
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<link rel="stylesheet" type="text/css" href="style.css" media="screen, print" />
		<link rel="stylesheet" type="text/css" href="widgets.css" />
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

	<a href="http://en.wikipedia.org/wiki/Vilfredo_Pareto"><img src="images/pareto.png" id="paretoimg" /></a>
	<img src="images/athens.png" id="athens" />

		<h1><img src="images/titleimg.png" alt="Vilfredo goes to Athens" /></h1>

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

		<img src="images/pareto.png" id="paretoimg" />
		<img src="images/athens.png" id="athens" />

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
?>

<form method="GET" action="viewquestions.php">
	<strong>Room:</strong>
	<input name="room" id="roomid" type="text" size="22" maxlength="20" value="Vilfredo"/>
	<input type="submit" id="submit" value="Go!" />
</form>
<ul class="nav">
	<li><strong>Rooms:</strong></li>
	<li><a href="viewquestions.php">Common</a></li>
	<li><a href="viewquestions.php?room=Vilfredo" tooltip="this room is used to define the future of this website">Vilfredo</a></li>
	<li><a href="viewquestions.php?room=Politics" tooltip="Questions about politics">Politics</a></li>
	<li><a href="viewquestions.php?room=Metagovernment"  tooltip="Questions about the Metagovernment community">Metagovernment</a></li>
	<?php 
	$room_param = GetParamFromQuery(QUERY_KEY_ROOM);
	if  ($room_param and $room_param!="Vilfredo" and $room_param!="Politics" and $room_param!="Metagovernment")
	{ 
	QUERY_KEY_ROOM
	?>
		<li><a href="viewquestions.php<?php echo CreateNewRoomURL(); ?>"><?php echo $room_param; ?></a></li>
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