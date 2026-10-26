<?php

require_once("config.php");

// Old MySQL API used by most of the project


// New MySQL API recommended for future development
$mysql = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
// The wiki's queries rely on MariaDB's old permissive sql mode, such as 'NULL'
// for auto increment IDs; the shared database runs in strict mode
$mysql->query("SET SESSION sql_mode = ''");

require_once "models/model.php";
$model = new Model($mysql);

?>
