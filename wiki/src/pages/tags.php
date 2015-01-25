<?php

include('../mysql.php');
include('../functions.php');


$viewedQuery = mysql_query("Select `tag`, `views`
								from `Wiki_Tag_Statistics`
								order by `views` desc
								limit 35");
								
while(list($tag, $views) = mysql_fetch_array($viewedQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	
	$viewedCount++;
	$viewedTable .= "<tr><td>$viewedCount</td><td class='littleborder' style='max-width:150px'><a href='/?tag/$tag'>$tagText</a></td><td class='littleborder' style='color:#FCA5BD'>$views</td></tr>";
}

$popularQuery = mysql_query("Select `tag`, `count`, `modified`
								from `Wiki_Tag_Statistics`
								order by `count` desc
								limit 35");
								
while(list($tag, $count, $modified) = mysql_fetch_array($popularQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	$popularDate = formatTime(strtotime($modified));
	
	$popularCount++;
	$popularTable .= "<tr><td>$popularCount</td><td class='littleborder' style='max-width:150px'><a href='/?tag/$tag'>$tagText</a></td><td class='littleborder' style='color:#FCA5BD'>$count</td><td class='littleborder'>$popularDate</td></tr>";
}

$recentQuery = mysql_query("Select `tag`, `count`, `modified`
								from `Wiki_Tag_Statistics`
								order by `modified` desc
								limit 35");
								
while(list($tag, $count, $modified) = mysql_fetch_array($recentQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	$recentDate = formatTime(strtotime($modified));
	
	$recentCount++;
	$recentTable .= "<tr><td>$recentCount</td><td class='littleborder' style='max-width:150px'><a href='/?tag/$tag'>$tagText</a></td><td class='littleborder' style='color:#FCA5BD'>$count</td><td class='littleborder'>$recentDate</td></tr>";
}

/*
$newestQuery = mysql_query("Select `tag`, `count`, `modified`
								from `Wiki_Tag_Statistics`
								order by `created` desc
								limit 35");
								
while(list($tag, $count, $modified) = mysql_fetch_array($newestQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	$modifiedLong = date("F j\, Y G:i:s", strtotime($modified))." PST";
	$modifiedShort = date("G:i:s", strtotime($modified))." PST";
	
	$newestCount++;
	$newestTable .= "<tr><td>$newestCount</td><td style='max-width:150px'><a href='/?tag/$tag'>$tagText</a></td><td>$count</td><td><div class='date' title='$modifiedLong'>$modifiedShort</div></td></tr>";
}
*/

?>

<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Most Viewed Tags</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Tag</b></td><td><b>Views</b></td></tr>
		<?php echo $viewedTable ?>
	</table>
</div>

<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Most Used Tags</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Tag</b></td><td><b>Count</b></td><td><b>Time Used</b></td></tr>
		<?php echo $popularTable ?>
	</table>
</div>

<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Recently Used Tags</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Tag</b></td><td><b>Count</b></td><td><b>Time Used</b></td></tr>
		<?php echo $recentTable ?>
	</table>
</div>

<?php

/*
<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Newest Tags</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Tag</b></td><td><b>Count</b></td><td><b>Time Used</b></td></tr>
		<?php echo $newestTable ?>
	</table>
</div>
*/

?>
