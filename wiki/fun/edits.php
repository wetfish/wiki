<?php

session_start();
include('paginate.php');
include('include.php');
include('../fishformat.php');

$Name = stripslashes(filter_input(INPUT_GET, 'name', FILTER_SANITIZE_SPECIAL_CHARS));

?>

<form>
	&emsp;&emsp;&emsp;&emsp;Editor: <input type='text' name='name' value='<?php echo $Name ?>' /> <input type='submit' value='Submit' />
</form>

<?php

if($Name)
{
	$Query = "Select `ID`,`PageID`,`AccountID`,`EditTime`,`Size`,`Name`,`Description`,`Title` from `Wiki_Edits` where `Name`='$Name' order by `ID` desc";
	list($QueryData, $Links) = Paginate($Query, 50, $_GET['page'], $_SERVER['QUERY_STRING']);

	if($QueryData)
	{
		echo "<hr /><center>$Links</center><hr />";
		echo "<table width='100%'>";
		echo "<tr><td style='min-width:175px;'><b>Revision</b></td><td><b>Size</b></td><td><b>Editor</b></td><td style='min-width:200px;'><b>Title</b></td><td><b>Description</b></td></tr>";
	
		foreach($QueryData as $Result)
		{
			list($EditID, $PageID, $AccountID, $PageTime, $PageSize, $PageName, $PageDescription, $PageTitle) = $Result;

			if(empty($Data[$PageID]))
			{
				$PageQuery = mysql_query("SELECT `Path` FROM `Wiki_Pages` WHERE `ID`='$PageID'");
				list($PagePath) = mysql_fetch_array($PageQuery);

				$Data[$PageID] = $PagePath;
			}
			else
				$PagePath = $Data[$PageID];

			$Toggle++;
			
			date_default_timezone_set('America/New_York');
			$PageTime = date("F j\, Y G:i:s", $PageTime)." EST";

			if($Toggle % 2 == 1)
				$Class = "class='toggle'";
			else
				$Class = '';

			$PageName = FishFormat($PageName, "format");
			$PageDescription = FishFormat($PageDescription, "format");
			$PageTitle = FishFormat($PageTitle, "format");
			$DiffURL = str_replace("//", "/", "/$PagePath/?diff/$EditID");
			
			echo "<tr $Class><td>$PageTime</td><td>$PageSize</td><td><b><a href='/names?id=$AccountID'>$PageName</a></b></td><td><b><a href='/$PagePath'>$PageTitle</a></b><span style='float:right;'><a href='$DiffURL'>d</a></span></td><td>$PageDescription</td></tr>";
		}
		
		echo "</table>";				
		echo "<hr /><center>$Links</center>";
	}
	else
	{
		echo "<hr /><b>Nothing found, sorry.</b>";
	}
}

?>
