<?php
include 'vga_functions.php';
require_once('lib/recaptcha-php-1.11/recaptchalib.php');
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
<div id="register" title="Vilfredo Register">
		<form action="#" method="post">
		<fieldset>
			<label for="username"><?=$VGA_CONTENT['choose_username_label']?></label>
			<input class="text ui-widget-content ui-corner-all" type="text" name="username" id="username" maxlength="60">

		<div class="reg_form">
		    <table>
		    <tr>
		    <td>
			<label for="email"><?=$VGA_CONTENT['email_label']?></label>
			</td><td>
			<input class="text ui-widget-content ui-corner-all" type="text" name="email" id="email" maxlength="60">
			</td></tr>
			<tr><td>
			<label for="pass"><?=$VGA_CONTENT['password_label']?></label>
			</td><td>
			<input class="text ui-widget-content ui-corner-all" type="password" name="pass" id="pass" maxlength="10">
			</td></tr>
			<tr><td>
			<label for="pass2"><?=$VGA_CONTENT['pass_conf_label']?></label>
			</td><td>
			<input class="text ui-widget-content ui-corner-all" type="password" name="pass2" id="pass2" maxlength="10">
			</td></tr></table>
			<script type="text/javascript" src="js/recaptcha_ajax.js"></script>
			<script type="text/javascript">
			  Recaptcha.create(recaptcha_public_key, 'captchadiv', {
              theme: "clean"});
			 </script>
			<div id='captchadiv'></div>
			<input type="hidden" name="usernameok" id="usernameok" value="" />
			</div>
			</fieldset>
		</form>
</div>
