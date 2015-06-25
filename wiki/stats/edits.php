<?php

require('../functions.php');
require('phpgraphlib.php'); 

date_default_timezone_set('America/New_York');

$display = strtotime("30 days ago");
$query = mysql_query("Select `EditTime` from `Wiki_Edits` where `EditTime` > $display order by `EditTime` asc");
while(list($time) = mysql_fetch_array($query))
{
    $when = date('d M', $time);
    $data[$when]++;
}

$graph = new PHPGraphLib(800,400);
$graph->addData($data);
$graph->setTitle("Edits This Month");
$graph->setTextColor("blue");
$graph->createGraph();

?>
