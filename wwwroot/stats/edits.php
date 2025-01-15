<?php

require('../functions.php');
require('phpgraphlib.php'); 

date_default_timezone_set('America/New_York');

$mode = $_GET['mode'];

if($mode == "year")
{
    $title = "Edits This Year";
    $display = "333 days ago";
    $width = 1825;
    $height = 768;
}
else
{
    $title = "Edits This Month"; 
    $display = "30 days ago";
    $width = 1024;
    $height = 600;
}

$display = strtotime($display);
$query = mysqli_query($mysql,"Select `EditTime` from `Wiki_Edits` where `EditTime` > {$display} order by `EditTime` asc");

while(list($time) = mysqli_fetch_array($query))
{
    if($mode == "year")
    {
        $when = date('M n/d', $time);
        $month = date('M', $time);
        $day = date('d', $time);

        // Save the first day for this month
        if(!isset($months[$month]))
        {
            $months[$month] = $day;
        }
        // Use a custom date format for all days that aren't the first of the month
        elseif($months[$month] != $day)
        {
            $when = date('n/d', $time);
        }
    }
    else
    {
        $when = date('M d', $time);
    }

    $data[$when]++;
}

$graph = new PHPGraphLib($width, $height);
$graph->addData($data);
$graph->setTitle($title);
$graph->setTextColor("blue");
$graph->createGraph();

?>
