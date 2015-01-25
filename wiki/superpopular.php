<?php

require('functions.php');
$PeopleQuery = mysql_query("Select AccountID,count(*) as n
							from Wiki_Edits
							group by AccountID
							order by n desc
							limit 25");

while(list($AccountID, $Count) = mysql_fetch_array($PeopleQuery))
{
	$AccountCount++;
	
	$AccountQuery = mysql_query("Select `Name`, `EditTime` from `Wiki_Accounts` where `ID`='$AccountID'");
	list($AccountName, $EditTime) = mysql_fetch_array($AccountQuery);
	
	$AccountName = gethostbyaddr($AccountName);
	$AccountName = preg_replace('/\d+-\d+-\d+-\d+/', substr(md5(hash('whirlpool', $AccountName)), 0, 8), $AccountName);
	
	date_default_timezone_set('America/New_York');
	$EditTime = date("F j\, Y G:i:s", $EditTime)." EST";
	
	$AccountTable .= "<tr>
						<td>$AccountCount</td>
						<td><a href='names?id=$AccountID'>$AccountName</a></td>
						<td>$Count</td>
						<td>$EditTime</td>
					</tr>";
}

?>

<div>
	<span class='medium'>Coolest Hosts</span>

	<table>
		<tr style='font-weight:bold'>
			<td>&nbsp;</td>
			<td>Cool Hostname&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>Edits&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>Last Edit</td>
		</tr>
		<?php echo $AccountTable ?>
	</table>
</div>