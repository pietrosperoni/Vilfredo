<?php
include('header.php');

if (isloggedin())
{
	echo "Admin Area<p>";
	echo "Your Content<p>";
	echo "<a href=logout.php>Logout</a>";
}
else
{
		header("Location: login.php");
}
?> 