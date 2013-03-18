<?php 
include('config.inc.php'); 
header('Content-Type: text/html; charset=utf-8'); 

// Get user ID if logged in
$userid=isloggedin();

if (!$userid)
{
	setError("You must be logged in to access this page.");
	header("Location: error_page.php");
	exit;
}
elseif (!isAdmin($userid))
{
	setError("Only Administrators may access this page.");
	header("Location: error_page.php");
	exit;
}

?>
<style type="text/css">
 td 
 {
	padding: 2px;
 }

 td.lang 
 {
	width: 200px;
 }

.links {
	height: 50px;
	font-size: 1.2em;
	margin-top: 25px;
}

.intro {
	font-size: 1.1em;
	width: 600px;
	background-color: #FFEFD5;
	padding: 10px;
}

</style>


<?php

if (isAdmin($userid))
{
?>
<div class="intro">
	<h3>Admin</h3>
	<p>Edit the entries below then when you are finished click on the "Language Admin Page" link to go to the admin page where you can generate the language files from the database in order to make the changes live.</p>
	<p>Then click "Back to listing" link at the top to return to this listing page.</p>
	
	<div class="links">
	<a href="language_admin.php">Language Admin Page</a>
	</div>
</div>
<br /><br />
<?php	
}


echo "<table border=1 >"; 
echo "<tr>"; 
echo "<th><b>Id</b></th>"; 
echo "<th><b>Page</b></th>"; 
echo "<th><b>Last Modified Time</b></th>"; 
echo "<th><b>Var Key</b></th>"; 
echo "<th><b>Text</b></th>"; 
echo "<th><b>Italian</b></th>"; 
echo "</tr>"; 
$result = mysql_query("SELECT * FROM `vga_content`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td>" . nl2br( $row['id']) . "</td>";   
echo "<td>" . nl2br( $row['page']) . "</td>";   
echo "<td>" . nl2br( $row['last_modified_time']) . "</td>";  
echo "<td>" . nl2br( $row['var_key']) . "</td>";  
echo "<td class=\"lang\">" . nl2br( $row['text']) . "</td>";  
echo "<td class=\"lang\">" . nl2br( $row['it']) . "</td>";  
echo "<td class=\"lang\"><a href=edit_trans.php?id={$row['id']}>Edit</a></td>"; 
echo "</tr>"; 
} 
echo "</table>"; 
?>