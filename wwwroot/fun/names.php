<?php

session_start();
require('include.php');
$ID = filter_var(stripslashes($_GET['id']), FILTER_SANITIZE_SPECIAL_CHARS);

if(empty($ID))
	$ID = $_SESSION['ID'];

$AccountQuery = mysql_query("Select `Name` from `Wiki_Accounts` where `ID`='$ID'");
list($AccountName) = mysql_fetch_array($AccountQuery);

$hostname = gethostbyaddr($AccountName);

// Replace the first half of a hostname with a hash
$hostname = explode('.', $hostname);
$hostname = array_slice($hostname, floor(count($hostname) / 2));
$hostname = substr(hash('whirlpool', $AccountName), 0, 8) . "." . implode(".", $hostname);

if($ID == $_SESSION['ID'])
{
	$Title = "Names you ($hostname) have edited with";
}
else
{
	$Title = "Names $hostname has edited with";
}

$NameQuery = mysql_query("Select `Name`, max(`EditTime`),count(*) as n
								from `Wiki_Edits`
								where `AccountID`='$ID'
								group by `Name`
								order by n desc");
								
while(list($Name, $Time, $Count) = mysql_fetch_array($NameQuery))
{
	$NameCount++;
	
	date_default_timezone_set('America/New_York');
	$Time = date("F j\, Y G:i:s", $Time)." EST";

	$Toggle++;
	
	if($Toggle % 2 == 1)
		$Class = "class='toggle'";
	else
		$Class = '';
	
	$AccountTable .= "<tr $Class>
						<td>$NameCount</td>
						<td><a href='/edits?name=$Name'>$Name</td>
						<td>$Count</td>
						<td>$Time</td>
					</tr>";
}

?>


<div>
	<span class='title'><?php echo $Title ?></span>

	<table width='100%'>
		<tr style='font-weight:bold'>
			<td>&nbsp;</td>
			<td>Name&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>Times Used&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>Last Edit</td>
		</tr>
		<?php echo $AccountTable ?>
	</table>
</div>
