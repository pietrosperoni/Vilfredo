<?php 
include('config.inc.php'); 
header('Content-Type: text/html; charset=utf-8'); 
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
}
</style>
<?php
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
echo "<td class=\"lang\"><a href=edit_italian.php?id={$row['id']}>Edit</a></td>"; 
echo "</tr>"; 
} 
echo "</table>"; 
?>