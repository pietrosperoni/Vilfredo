<?php
include('header.php');
$site_error = "There seems to have been a problem somewhere. It's been reported and we'll look into it as soon as possible.";
$message = (isError()) ? getError() : $site_error;
?>
<h1>Oops, something unexpected happened.</h1>
<p><?=$message?></p>
<p>Please click one of the navigation links to return to the site.</p>
<p>The Vilfredo Team.</p>
<?php
include('footer.php');
?>