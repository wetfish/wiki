<?php

require('../functions.php');
require('phpgraphlib.php'); 

date_default_timezone_set('America/New_York');

// Use date to generate the current day.
// Omitting the specific time passed makes strtotime() return today's first second.

$Today = strtotime(date("d F Y"));
$Day = 86400; // Seconds

$LastWeek = $Today - ($Day * 30);

$Query = mysql_query("Select `Time` from `Wiki Searches` where `Time` > $LastWeek");
while(list($Time) = mysql_fetch_array($Query))
{
	$When = date('d M', $Time);
	$Data[$When]++;
}

$Graph = new PHPGraphLib(800,400);
$Graph->addData($Data);
$Graph->setTitle("Searches This Month");
$Graph->setTextColor("blue");
$Graph->createGraph();

?>