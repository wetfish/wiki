<?php

# Get input
# Explode into array
# Output it back out

function Queries($Text)
{
	preg_match_all("/\s*(.*?)\s*:\s*(?:(.)?{(.*)}\\2?|(.*?))(?:;|\s*$)/", $Text, $Matches);

	foreach($Matches[3] as $Key=>$Data)
	{
		if(trim($Data) == "")
			$Data = $Matches[4][$Key];

		if((trim($Matches[1][$Key]) != "") && (trim($Data) != ""))
			$Queries[strtolower(trim($Matches[1][$Key]))] = trim($Data);
	}

	return $Queries;
}

class CSS
{
	public $CSS = array();
	
	public function Import($Data)
	{	
		if(is_array($Data))
		{
			$this->CSS = $Data;
		}
		else
		{
			$URL = parse_url($Data);
		
			if($URL['scheme'] == 'http')
				$Data = file_get_contents($Data);
		
			preg_match_all('/(.+?)\s?{(.*?)}/s', $Data, $Matches);
		
			foreach($Matches[1] as $ID => $Tag)
			{
				$Tag = trim($Tag);
			
				if(preg_match('/,/', $Tag))
				{
					foreach(explode(',', $Tag) as $SuperTag)
					{
						if(empty($this->CSS[$SuperTag]))
							$this->CSS[$SuperTag] = array();
				
						$this->CSS[$SuperTag] = array_merge($this->CSS[$SuperTag], Queries($Matches[2][$ID]));
					}
				}
				elseif(preg_match('/ /', $Tag))
				{
					foreach(explode(' ', $Tag) as $SuperTag)
					{
						if(empty($this->CSS[$SuperTag]))
							$this->CSS[$SuperTag] = array();
				
						$this->CSS[$SuperTag] = array_merge($this->CSS[$SuperTag], Queries($Matches[2][$ID]));
					}
				}
				else
				{
					if(empty($this->CSS[$Tag]))
						$this->CSS[$Tag] = array();
				
					$this->CSS[$Tag] = array_merge($this->CSS[$Tag], Queries($Matches[2][$ID]));
				}
			}
		}
	}
	
	public function Export($Type='derp')
	{
		switch(strtolower($Type))
		{
			default:
				foreach($this->CSS as $Tag => $Styles)
				{
					$Style = '';
				
					foreach($Styles as $Option => $Value)
					{
						$Style .= "$Option:$Value;";
					}
					
					$Output .= "$Tag { $Style } \n";
				}
				
				return $Output;
			break;
			
			case 'raw':
				return $this->CSS;
			break;
		}
	}
}

?>