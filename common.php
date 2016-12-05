<?php


// Connect to DB

include('dbinfo.php');

$conn = mysql_connect("$db_host:$db_port", $db_username, $db_password);
echo mysql_error();

mysql_select_db($db_name);
echo mysql_error();

?>