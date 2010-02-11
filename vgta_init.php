<?php
require_once "loc/config.domain.php";
require_once "priv/dbdata.php";
// Connects to the Database
mysql_connect($dbaddress, $dbusername, $dbpassword) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

?>