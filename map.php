<?php
include('header.php');

if (isloggedin())
{
?>

<h2>Online democracy</h2>


<p>This website is part of of a study on online democracy. You can use this system to find common positions among people.</p>

<p align=center><img src="images/Vilfredomap.png">
</p>

		<div class="endorsingbox2">
			<h2><a href="newquestion.php">Ask Your Question</a></h2>
		</div>




<?php

#echo WriteQuestion(52);

}
else
{
		DoLogin();
}

include('footer.php');

?> 