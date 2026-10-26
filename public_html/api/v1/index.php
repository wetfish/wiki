<?php

require '../../src/mysql.php';
require 'api.php';

$api = new API($model);
echo $api->request($_SERVER['REQUEST_URI']);

?>
