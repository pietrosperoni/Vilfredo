<?php
include 'vga_functions.php';
session_start();
if (isset($_SESSION["locale"]) and ($_SESSION["locale"] == 'en' or $_SESSION["locale"] == 'it' ))
{
	$locale = $_SESSION["locale"];
}
else
{
	$locale = fetch_preferred_language_from_client();
}
@include getLanguage($locale);
?>
<div id="login" title="Vilfredo Login">
	<form action="#" method="post">
	<fieldset>
		<table><tr><td>
		<label for="username"><?=$VGA_CONTENT['username_label']?></label>
		</td><td>
		<input class="text ui-widget-content ui-corner-all" type="text" name="username" id="username" maxlength="60">
		</td></tr>
		<tr><td>
		<label for="pass"><?=$VGA_CONTENT['password_label']?></label>
		</td><td>
		<input class="text ui-widget-content ui-corner-all" type="password" name="pass" id="pass" maxlength="10">
		</td></tr></table>
		<br /><br />
		<label for="remember"><?=$VGA_CONTENT['keep_logged_label']?></label>
		<input class="text ui-widget-content ui-corner-all" name="remember" type="checkbox">
		</fieldset>
	</form>
</div>