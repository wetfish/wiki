<?php

include('../mysql.php');

$viewedTags = array();
$popularTags = array();
$recentTags = array();

$viewedQuery = mysql_query("Select `tag`, `views`
								from `Wiki_Tag_Statistics`
								order by `views` desc
								limit 50");
								
while(list($tag, $views) = mysql_fetch_array($viewedQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	$viewedTags[$tag] = array('text' => $tagText, 'views' => $views);
}

uksort($viewedTags, 'strnatcasecmp');

echo "<div style='width:300px; float:left; margin:0px 16px'>";
echo "<span class='medium'>Most Viewed Tags</span>";
	foreach($viewedTags as $tag => $data)
	{
		echo "<div style='float:left'><a href='/?tag/$tag' title='Viewed {$data['views']} times' style=' margin:8px; font-size: {$data['views']}%'>{$data['text']}</a></div>";
	}
echo "</div>";



$popularQuery = mysql_query("Select `tag`, `count`, `modified`
								from `Wiki_Tag_Statistics`
								order by `count` desc
								limit 50");

while(list($tag, $count, $modified) = mysql_fetch_array($popularQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	$popularTags[$tag] = array('text' => $tagText, 'count' => $count);
}

uksort($popularTags, 'strnatcasecmp');

echo "<div style='width:300px; float:left; margin:0px 16px'>";
echo "<span class='medium'>Most Used Tags</span>";
	foreach($popularTags as $tag => $data)
	{
		$percentLol = $data['count'];
		echo "<div style='float:left'><a href='/?tag/$tag' title='Used {$data['count']} times' style=' margin:8px; font-size: $percentLol%'>{$data['text']}</a></div>";
	}
echo "</div>";


$recentQuery = mysql_query("Select `tag`, `count`, `modified`
								from `Wiki_Tag_Statistics`
								order by `modified` desc
								limit 50");
								
while(list($tag, $count, $modified) = mysql_fetch_array($recentQuery))
{
	$tagText = str_replace('-', ' ', $tag);
	$recentTags[$tag] = array('text' => $tagText, 'count' => $count);
}

uksort($recentTags, 'strnatcasecmp');

echo "<div style='width:300px; float:left; margin:0px 16px'>";
echo "<span class='medium'>Recently Used Tags</span>";
	foreach($recentTags as $tag => $data)
	{
		$percentLol = $data['count'] + 50;
		
		echo "<div style='float:left'><a href='/?tag/$tag' title='Used {$data['count']} times' style=' margin:8px; font-size: $percentLol%'>{$data['text']}</a></div>";
	}
echo "</div>";


?>
