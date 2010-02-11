<?php
$userid=isloggedin();
// Logged in
if ($userid)
{
	$room_param = GetParamFromQuery(QUERY_KEY_ROOM);
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
				if  ($room_param and $room_param!="Vilfredo" and $room_param!="Politics" and $room_param!="Metagovernment")
				{ 
				QUERY_KEY_ROOM
				?>
					<li><a href="viewquestions.php<?php echo CreateNewRoomURL(); ?>"><?php echo $room_param; ?></a></li>
				<?php
				} 
				?>
				</ul>
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
?>