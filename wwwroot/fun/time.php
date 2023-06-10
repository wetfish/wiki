<?php

$Data = simplexml_load_file("http://ipinfodb.com/ip_query.php?ip={$_SERVER['REMOTE_ADDR']}&timezone=true");

date_default_timezone_set($Data->TimezoneName);

echo "It is ".date("F j\, Y G:i:s", time())." in ".$Data->TimezoneName;

?>