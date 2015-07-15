<?php

require_once("config.php");

// Old MySQL API used by most of the project
@mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or die("HOLY SHIT THE DATABASE EXPLODED FUCK");
@mysql_select_db(MYSQL_DATABASE) or die("Unnable to connect to database.");

// New MySQL API recommended for future development
$mysql = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

require "models/model.php";
$model = new Model($mysql);

?>
