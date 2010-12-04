<?php
require_once "config.inc.php";

$room = GetParamFromQuery(QUERY_KEY_ROOM);

//$room = ucfirst($room);

UpdateFeed($room);

?>