<?php 
include('config.inc.php'); 
header('Content-Type: text/html; charset=utf-8'); 
if (isset($_GET['id']) ) { 
$id = (int) $_GET['id']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 

$sql = "UPDATE `vga_content_new` SET  `text` = '{$_POST['text']}', `it` =  '{$_POST['it']}'   WHERE `id` = '$id' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Translation saved.<br />" : "Nothing changed. <br />"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `vga_content_new` WHERE `id` = '$id' ")); 
?>
<p><a href='list_trans.php'>Back To Listing</a></p>
<form action='' method='POST'> 
<p><b>Page:</b><br /><input type='text' name='page' value='<?= stripslashes($row['page']) ?>' disabled="disabled"/>
<p><b>Last Modified Time:</b><br /><input type='text' name='last_modified_time' value='<?= stripslashes($row['last_modified_time']) ?>' disabled="disabled"/> 
<p><b>Var Key:</b><br /><input type='text' name='var_key' value='<?= stripslashes($row['var_key']) ?>' disabled="disabled"/> 
<p><b>Text:</b><br /><textarea name='text' rows="10" cols="35"><?= stripslashes($row['text']) ?></textarea> 
<p><b>It:</b><br />
<textarea name='it' rows="10" cols="35"><?= stripslashes($row['it']) ?></textarea> 
<p><input type='submit' value='Save Translation' /><input type='hidden' value='1' name='submitted' /> 
</form> 
<p><a href='list_trans.php'>Back To Listing</a></p>
<?php 
} 
?> 
