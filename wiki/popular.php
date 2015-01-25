<?php

require('functions.php');


$viewsQuery = mysql_query("Select `Path`,`Title`,`Views`
							from Wiki_Pages
							order by `Views` desc
							limit 35");

while(list($path, $title, $views) = mysql_fetch_array($viewsQuery))
{	
	$viewsCount++;
	$viewsTable .= "<tr><td>$viewsCount</td><td class='littleborder' style='max-width:175px'><a href='/$path'>$title</a></td><td class='littleborder' style='color:#FCA5BD'>$views</td></tr>";
}


$PeopleQuery = mysql_query("Select Name,count(*) as n
							from Wiki_Edits
							group by Name
							order by n desc
							limit 35");

while(list($Name, $Count) = mysql_fetch_array($PeopleQuery))
{
	$PeopleCount++;
	$PeopleTable .= "<tr><td>$PeopleCount</td><td class='littleborder'><a href='/".urlencode(str_replace(array("'", '"'), '', html_entity_decode($Name, false, 'UTF-8')))."'>$Name</a></td><td class='littleborder'><a style='color:#FCA5BD' href='/edits?name=$Name'>$Count</a></td></tr>";
}


$PageQuery = mysql_query("Select PageID,count(*) as n
							from Wiki_Edits
							group by PageID
							order by n desc
							limit 35");

while(list($PageID, $Count) = mysql_fetch_array($PageQuery))
{
	$PageInfo = mysql_query("Select `Path`, `Title` from `Wiki_Pages` where `ID`='$PageID'");
	list($Path, $Title) = mysql_fetch_array($PageInfo);
	
	$PageCount++;
	$PageTable .= "<tr><td>$PageCount</td><td class='littleborder' style='max-width:175px'><a href='/$Path'>$Title</a></td><td class='littleborder' style='color:#FCA5BD'>$Count</td></tr>";
}

?>

<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Most Viewed Pages</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Cool Page</b>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Views</b></td></tr>
		<?php echo $viewsTable ?>
	</table>
</div>

<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Coolest Editors</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Cool Person</b>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Edits</b></td></tr>
		<?php echo $PeopleTable ?>
	</table>
</div>


<div style='float:left; padding:8px; margin:0px 16px;'>
	<span class='medium'>Most Edited Pages</span>

	<table class='history'>
		<tr><td>&nbsp;</td><td><b>Cool Page</b>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Edits</b></td></tr>
		<?php echo $PageTable ?>
	</table>
</div>

<div style='clear:both;'></div>
