<?php



include('header.php');
#$userid=isloggedin();
if (isAdmin($userid))
{
	$question = $_GET['q'];
	
	/*
	if ($userid!=2)
	{
		exit;
	}*/
	
	$sql = "SELECT * FROM proposals WHERE experimentid = ".$question."  ";
	$response = mysql_query($sql);
	while ($row = mysql_fetch_array($response))
	{
		echo $row[0]."<br>";
		echo $row[1]."<br>";
		echo $row[2]."<br>";
		echo $row[3]."<br>";
		echo $row[4]."<br>";
		echo $row[5]."<br>";
		echo $row[6]."<br>";
		echo $row[7]."<br>";
		$sql2 = "SELECT * FROM endorse WHERE proposalid = ".$row[0]."  ";
		$response2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_array($response2))
		{
			echo $row2[0]."<br>";
			echo $row2[1]."<br>";
			echo $row2[2]."<br>";
			echo $row2[3]."<br>";
		}
		$sql2 = "DELETE FROM endorse WHERE  endorse.proposalid = " .$row[0]. " ";
		$response2 = mysql_query($sql2);

	}
	$sql = "DELETE FROM proposals WHERE  proposals.experimentid = " .$question. " ";
	$response = mysql_query($sql);
	
	$sql3 = "SELECT * FROM updates WHERE question = ".$question."  ";
	$response3 = mysql_query($sql3);
	while ($row3 = mysql_fetch_array($response3))
	{
		echo $row3[0]."<br>";
		echo $row3[1]."<br>";
		echo $row3[2]."<br>";
		echo $row3[3]."<br>";
	}
	$sql3 = "DELETE FROM updates WHERE  updates.question = " . $question . " ";
	$response3 = mysql_query($sql3);


	$sql4 = "SELECT * FROM questions WHERE id = ".$question."  ";
	$response4 = mysql_query($sql4);
	while ($row4 = mysql_fetch_array($response4))
	{
		echo $row4[0]."<br>";
		echo $row4[1]."<br>";
		echo $row4[2]."<br>";
		echo $row4[3]."<br>";
		echo $row4[4]."<br>";
		echo $row4[5]."<br>";
		echo $row4[6]."<br>";
		echo $row4[7]."<br>";
	}
	$sql4 = "DELETE FROM questions WHERE  questions.id = " . $question . " ";
	$response4 = mysql_query($sql4);

}
else
{
		header("Location: login.php");
}

include('footer.php');

?> 







	