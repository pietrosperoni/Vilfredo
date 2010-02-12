<?php
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
?>