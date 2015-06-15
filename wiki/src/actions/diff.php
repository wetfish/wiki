<?php

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
        
        $nl = '#**!)@<>#';

        $PreviousContent = str_replace("\n", "<br>", $PreviousContent);
        $PageContent = str_replace("\n", "<br>", $PageContent);

        
        ob_start();
        echo $PreviousContent;
        echo $PageContent;
//        inline_diff($PreviousContent, $PageContent, $nl);
        $content['Body'] .= ob_get_contents();
        ob_end_clean();
        
        date_default_timezone_set('America/New_York');
        $PageEditTime = formatTime($PageEditTime);
        $content['Footer'] = "This page is an old revision made by <b><a href='/names?id=$AccountID'>$PageName</a></b> on $PageEditTime.";

        if($PageDescription)
            $content['Footer'] .= "<br />'$PageDescription'";
    }

    return $content;
}

?>
