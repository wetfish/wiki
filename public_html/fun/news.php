<?php

# Anus.

$Directory = "/home/rachel/noxbot/data/plugLogger/1/#wetfish";
$News = array();

# Loop through the directory to get the last (most recent) file.
foreach(scandir($Directory) as $File)
{
	if(!is_dir($File))
	{
		$New = file_get_contents("$Directory/$File");
		$News = array_merge($News, explode("\n", $New));
	}
}

$News = array_reverse($News);

foreach($News as $Line)
{
	list($Time, $Name, $Action, $Text) = explode(" ", $Line, 4);
	$Text = filter_var($Text, FILTER_SANITIZE_SPECIAL_CHARS);
	
	list($Command, $Text) = explode(" ", $Text, 2);
	
	if($Command == '!news')
		echo "<b>$Name</b> - $Text<br />";
}

?>