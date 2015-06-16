<?php

// Include the diff class
require_once dirname(__FILE__).'/../libraries/Diff.php';

// Include two sample files for comparison
$a = explode("\n", file_get_contents(dirname(__FILE__).'/a.txt'));
$b = explode("\n", file_get_contents(dirname(__FILE__).'/b.txt'));


function diff($path, $action, $content)
{
    $Head = '<meta name="robots" content="noindex, nofollow" />';
    $content['PageNav']->Active("Page History");
    
    if(is_numeric($action[1]))
    {
        $PageQuery = mysql_query("SELECT `PageID`,`AccountID`,`EditTime`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$action[1]'");
        list($PageID, $AccountID, $PageEditTime, $PageName, $PageDescription, $PageTitle, $PageContent) = mysql_fetch_array($PageQuery);
    
        $PreviousQuery = mysql_query("Select `Content` from `Wiki_Edits` where `ID` < '$action[1]' and `PageID`='$PageID' order by `ID` desc limit 1");
        list($PreviousContent) = mysql_fetch_array($PreviousQuery);
        
        $Title[] = FishFormat($PageTitle, "strip");			
        $content['Title'] .= FishFormat($PageTitle);
        
        $old = explode("\n", html_entity_decode($PreviousContent, ENT_QUOTES));
        $new = explode("\n", html_entity_decode($PageContent, ENT_QUOTES));

        // Initialize the diff class
        $diff = new Diff($old, $new);

        require_once dirname(__FILE__).'/../libraries/Diff/Renderer/Html/SideBySide.php';
        $renderer = new Diff_Renderer_Html_SideBySide;
        $content['Body'] .= $diff->Render($renderer);
        
        date_default_timezone_set('America/New_York');
        $PageEditTime = formatTime($PageEditTime);
        $content['Footer'] = "This page is an old revision made by <b><a href='/names?id=$AccountID'>$PageName</a></b> on $PageEditTime.";

        if($PageDescription)
            $content['Footer'] .= "<br />'$PageDescription'";
    }

    return $content;
}

?>
