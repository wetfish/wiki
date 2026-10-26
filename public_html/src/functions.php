<?php

function formatTime($timestamp)
{
    $Now = time();
    $Passed = $Now - $timestamp;
    $exactTime = date("F j\, Y G:i:s", $timestamp)." EST";
    
    if($Passed < 60)
    {
        if($Passed != 1)
            $Plural = 's';
            
        return "<span class='date' title='$exactTime'>$Passed second{$Plural} ago</span>";
    }
    elseif($Passed < 3600)
    {
        $Passed = round($Passed / 60);
        
        if($Passed != 1)
            $Plural = 's';
        
        return "<span class='date' title='$exactTime'>$Passed minute{$Plural} ago</span>";
    }
    elseif($Passed < 86400)
    {	
        $Passed = round($Passed / 60);
        $Passed = round($Passed / 60);

        if($Passed != 1)
            $Plural = 's';
        
        return "<span class='date' title='$exactTime'>$Passed hour{$Plural} ago</span>";
    }
    elseif($Passed < 4320000)
    {	
        $Passed = round($Passed / 24);
        $Passed = round($Passed / 60);
        $Passed = round($Passed / 60);	
        
        if($Passed != 1)
            $Plural = 's';
        
        return "<span class='date' title='$exactTime'>$Passed day{$Plural} ago</span>";
    }
    else
    {
        return $exactTime;
    }
}

?>
