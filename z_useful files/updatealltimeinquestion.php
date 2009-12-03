<?php
include('header.php');


#$userid=isloggedin();
if ($userid)
{	

#	$sql = "UPDATE questions SET lastmoveon = NOW() ";
#	mysql_query($sql);

}
else
{
		header("Location: login.php");
}
?> 