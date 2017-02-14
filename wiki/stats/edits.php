<?php

require('../functions.php');
require('phpgraphlib.php'); 

date_default_timezone_set('America/New_York');

$mode = $_GET['mode'];

if($mode == "year")
{
    $title = "Edits This Year";
    $display = "365 days ago";
}
else
{
    $title = "Edits This Month"; 
    $display = "30 days ago";
}

$display = strtotime($display);
$query = mysql_query("Select `EditTime` from `Wiki_Edits` where `EditTime` > {$display} order by `EditTime` asc");
while(list($time) = mysql_fetch_array($query))
{
    $when = date('d M', $time);
    $data[$when]++;
}

$graph = new PHPGraphLib(1800,768);
$graph->addData($data);
$graph->setTitle($title);
$graph->setTextColor("blue");
$graph->createGraph();

?>
