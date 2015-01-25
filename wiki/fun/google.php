<?php

$Query = filter_var(trim(stripslashes(stripslashes($_GET['q']))), FILTER_SANITIZE_SPECIAL_CHARS);
$Filter = filter_var(trim(stripslashes(stripslashes($_GET['filter']))), FILTER_SANITIZE_SPECIAL_CHARS);

?>

<br /><br /><br />

<center>
	<img src='http://wiki.wetfish.net/upload/300_8b057e45-97ce-7b72-e500-9724fb894b59.png' />
	<h1 style='margin:4px;'>Search Scraper</h1>
	
	<form>
		<table>
			<tr>
				<td><b>Search Query</b></td>
				<td><input type='text' name='q' value='<?php echo $Query ?>' /></td>
			</tr>
			
			<tr>
				<td>Filter</td>
				<td><input type='text' name='filter' value='<?php echo $Filter ?>' /></td>
			</tr>
		</table>

		<input type='submit' value='Search' />		
	</form>
</center>

<?php

if($_GET['q'])
{
	require('simple_html_dom.php');

	echo "<hr />";
	$URL =	"http://google.com/search?".
			"&q=". urlencode($_GET['q']).
			"&num=100";

	$Data = file_get_contents($URL);
	$HTML = str_get_html($Data);
	
	foreach($HTML->find('#ires li') as $Result)
	{
		$Count++;
		
		$Link = $Result->find('a', 0);
		if(preg_match("/{$_GET['filter']}/i", $Link))
			echo "$Count. $Link <br />";
	}
}

?>
