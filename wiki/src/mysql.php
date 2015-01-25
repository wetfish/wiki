<?php

require_once("config.php");

@mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die("HOLY SHIT THE DATABASE EXPLODED FUCK");
@mysql_select_db(MYSQL_DATABASE) or die("Unnable to connect to database.");

?>
