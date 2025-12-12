<?php

include('paginate.php');
include('../functions.php');

parse_str($_SERVER['QUERY_STRING'], $Query);
unset($Query['load']);

$URL['Created'] = http_build_query(array_merge($Query, array('order' => 'created')));
$URL['Modified'] = http_build_query(array_merge($Query, array('order' => 'modified')));
$URL['Ascending'] = http_build_query(array_merge($Query, array('sort' => 'asc')));
$URL['Descending'] = http_build_query(array_merge($Query, array('sort' => 'desc')));

$Created = "<a href='?{$URL['Created']}'>Date Created</a>";
$Modified = "<a href='?{$URL['Modified']}'>Date Modified</a>";
$Ascending = "<a href='?{$URL['Ascending']}'>Ascending</a>";
$Descending = "<a href='?{$URL['Descending']}'>Descending</a>";

if($_GET['order'] == 'modified')
{
	$Modified = "<a href='?{$URL['Modified']}' class='title'>Date Modified</a>";
	$Order = "order by `EditTime`";
}
else
{
	$Created = "<a href='?{$URL['Created']}' class='title'>Date Created</a>";
	$Order = "order by `ID`";
}
	
if($_GET['sort'] == 'desc')
{
	$Descending = "<a href='?{$URL['Descending']}' class='title'>Descending</a>";
	$Sort = "desc";
}
else
{
	$Ascending = "<a href='?{$URL['Ascending']}' class='title'>Ascending</a>";
	$Sort = "asc";
}

echo 	"<div style='font-size:8pt; float:right;'>
			Order by: $Created, $Modified &mdash; $Ascending, $Descending
		</div>";

list($Data, $Links) = Paginate("Select `Path`, `Title`, `EditTime` from `Wiki_Pages` $Order $Sort", 50, $_GET['page'], $_SERVER['QUERY_STRING']);

echo "<div style='clear:right'></div>";

#$Time = simplexml_load_file("http://ipinfodb.com/ip_query.php?ip={$_SERVER['REMOTE_ADDR']}&timezone=true");
#date_default_timezone_set($Time->TimezoneName);

if($Data)
{
	echo "<center class='page-navigation'>$Links</center>";

	foreach($Data as $Page)
	{
		echo "<a href='/{$Page['Path']}'>{$Page['Title']}</a> &mdash; Last edited at ".formatTime($Page['EditTime'])."<br />";
	}

	echo "<center class='page-navigation bottom'>$Links</center>";

}
else
{
	echo "<b>Oops!!</b>";
}


?>
