<?php

require_once("config.php");

// Old MySQL API used by most of the project


// New MySQL API recommended for future development
$mysql = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

require_once "models/model.php";
$model = new Model($mysql);

?>
