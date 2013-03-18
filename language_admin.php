<?php
require "config.inc.php";
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

require "lang_functions.php";

$lang = "en";
$table = 'vga_content';
$msg = '';
$filename = '';
$newfilename = '';

function showMessages($msg)
{
	if (is_array($msg))
	{
		foreach ($msg as $m)
		{
			echo "<div class=\"message\">$m</div>";
		}
	}
	else
	{
		echo "<div class=\"message\">$msg</div>";
	}
}

if (isset($_POST['generate_dictionary_file']))
{
	if($_POST['dictfilelang'] == 'it' or $_POST['dictfilelang'] == 'en')
	{
		$language = ($_POST['dictfilelang'] == 'en') ? "English" : "Italian";
		
		if (generate_translation_file($_POST['dictfilelang'], $_POST['langfilename'] ))
		{
			$msg[] = "$language dictionary {$_POST['langfilename']} successfully created in ". LANG_FILES_DIRECTORY ." subdirectory!";
		}
		else
		{
			$msg[] = "ERROR! $language dictionary {$_POST['langfilename']} COULD NOT be created...";
		}
	}
}


?>
<style type="text/css">
table.admin {
	width: 100%;
}
table.admin td {
	padding: 10px;
	text-align: center;
}
table.panel {
	margin-top: 20px;
}
table.panel td, table.panel th {
	padding: 5px;
}
.admin input:text {
 width: 10px;
}

#generate_dictionary_file {
	height: 30px;
	width: 200px;
	font-size: 0.9em;
	margin-top: 5px;
	background-color: #528b8b;
	color: white;
}
#dictfilelang {
	height: 30px;
	font-size: 0.9em;
}

table.panel {
	font-size: 1.1em;
}

.message {
	font-size: 2em;
	color: green;
}
.error {
	font-size: 2em;
	color: red;
	font-weight: bold;
}

.intro {
	font-size: 1.3em;
	width: 600px;
	background-color: #FFEFD5;
	padding: 10px;
}
</style>

<p><a href='list_trans.php'>Back To Listing</a></p>

<h1>Manage Dictionary</h1>

<h2>Using table <?=$table?></h2>

<div class="intro">
<p>Select language 'en' or 'it' from from down then click button to create the language file in the /<?=LANG_FILES_DIRECTORY?> subdirectory from the database. The current file will be renamed with the current time and date as a backup.</p>
<p>Click "Back to listing" link at the top to return to the listing page.</p>
</div>

<p><?=showMessages($msg)?></p>

<form action="" method="POST" onsubmit=false>
<table class="panel">
<tr>
<td>Lang:
<select name="dictfilelang" id="dictfilelang">
<option value="en" 'selected'>en</option>
<option value="it">it</option>
</select>
</td>
<td>
<input type='submit' id="generate_dictionary_file" name='generate_dictionary_file' value='Generate Dictionary File' />
</td>
</tr>
</table>
</form>