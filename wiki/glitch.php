<?php

function SuperPrint($Array)
{
	echo "<pre>";
	print_r($Array);
	echo "</pre>";
}

if($_GET['url'])
{
	$URL = parse_url($_GET['url']);
	
	if($URL['host'])
	{
		$File = pathinfo($URL['path']);
		
		if(in_array($File['extension'], array('jpg', 'jpeg', 'png', 'bmp', 'gif')))
		{
			$Location = tempnam('/tmp', 'Glitch_');
			$Data = @file_get_contents($_GET['url']);
			file_put_contents($Location, $Data);
	
			$Type = mime_content_type($Location);
			Header("Content-type: $Type");
			
			$Length = strlen($Data);
			$Corruption = rand(3, $Length / rand(3, $Length / 3));
	
			while($Corruption > 0)
			{
				$Where = rand(0, $Length);
				$Data[$Where] = rand(0, getrandmax());

				$Corruption--;
			}
			
			echo $Data;
		}
	}
}

?>