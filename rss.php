<?php
require_once "config.inc.php";

$room = GetParamFromQuery(QUERY_KEY_ROOM);

UpdateFeed($room);

?>