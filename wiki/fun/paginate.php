<?php if(count(get_included_files()) == 1): ?>
Hello and welcome to the super cool Wetfish Pagination Function!!

<h3>How do I do it?</h3>
Paginate(<b>'SQL Query'</b>, <b>'Limit per Page'</b> [, <i>'Page Number'</i>, <i>'Query String'</i>]);

<br />
<br />

The page number is optional, with a default value of 1.<br />
The query string is also optional, in case your URLs need to use get variables.

<br />
<br />

Paginate() returns an array of two values:<br />
&emsp;1. An array containing the data from SQL<br />
&emsp;2. An HTML formatted string linking to the nearest 10 pages<br />

<h3>Examples</h3>

<pre>
list($Data, $Links) = Paginate('Select `PageID` from `Wiki Pages` order by `Time` desc', 50);


$Query = "Select `Path`, `Title`, `Content`
			from `Wiki_Pages`
			where match(`Path`, `Title`, `Content`)
			against('$Search')");

list($Data, $Links) = Paginate($Query, 25, $_GET['page'], $_SERVER['QUERY_STRING']);
</pre>

<?php endif; ?>

<?php

####################
##
## The real code
##

function Paginate($Query, $Limit, $Page = 1, $QueryString = '')
{	
	$Page--;
	$Start = $Page * $Limit;
	$paginationOffset = $Limit * 3;

	$CountQuery = mysql_query("$Query limit $Start, $paginationOffset");
	$Rows = mysql_num_rows($CountQuery);
	$Pages = $Page + ceil($Rows / $Limit);
	
	$PageQuery = mysql_query("$Query limit $Start, $Limit");
	while($Result = mysql_fetch_array($PageQuery))
	{
		$Data[] = $Result;
	}
	
	if($Page < 5)
	{
		for($Count = 1; $Count <= 10 and $Count <= $Pages; $Count++)
		{
			parse_str($QueryString, $URL);
			unset($URL['load']);
				
			$URL = http_build_query(array_merge($URL, array('page' => $Count)));
			
			if($Page + 1 == $Count)
				$Links .= "<div class='paginate'><a href='?$URL' class='title'>$Count</a></div> ";
			elseif($Page == $Count)
				$Links .= "<div class='paginate'><a href='?$URL' class='previous'>$Count</a></div> ";			
			elseif($Page + 2 == $Count)
				$Links .= "<div class='paginate'><a href='?$URL' class='next'>$Count</a></div> ";
			else
				$Links .= "<div class='paginate'><a href='?$URL'>$Count</a></div> ";
		}
	}
	else
	{
		for($Count = $Page - 4; $Count <= $Page + 6 and $Count <= $Pages; $Count++)
		{			
			parse_str($QueryString, $URL);
			unset($URL['load']);
			
			$URL = http_build_query(array_merge($URL, array('page' => $Count)));
			
			if($Page + 1 == $Count)
				$Links .= "<div class='paginate'><a href='?$URL' class='title'>$Count</a></div> ";
			elseif($Page == $Count)
				$Links .= "<div class='paginate'><a href='?$URL' class='previous'>$Count</a></div> ";			
			elseif($Page + 2 == $Count)
				$Links .= "<div class='paginate'><a href='?$URL' class='next'>$Count</a></div> ";
			else
				$Links .= "<div class='paginate'><a href='?$URL'>$Count</a></div> ";
		}
	}
	
	$Links .= <<<JavaScript
	
	<script>
		$('body').on('keydown', function(event)
		{
			if(event.keyCode == 37 && $('.previous').attr('href')) // Previous
				location.href = $('.previous').attr('href');
			else if(event.keyCode == 39 && $('.next').attr('href')) // Next
				location.href = $('.next').attr('href');

					
	//		console.log(event);
		});
	</script>
	
JavaScript;
	
	
	return array($Data, $Links);
}

?>
