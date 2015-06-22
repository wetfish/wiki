<?php

function view($path, $action, $content)
{
    $content['PageNav']->Active("View Page");

    $PageQuery = mysql_query("SELECT `ID`,`Title`,`Content`,`Edits`,`Views`,`EditTime` FROM `Wiki_Pages` WHERE `Path`='$path'");
    list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysql_fetch_array($PageQuery);
    
    $tagQuery = mysql_query("Select tags.`tag`, stats.`count`
                                from `Wiki_Tags` as tags,
                                     `Wiki_Tag_Statistics` as stats
                                     
                                where tags.`pageID` = '$PageID'
                                    and stats.`tag` = tags.`tag`");
                                    
    while(list($tagName, $tagCount) = mysql_fetch_array($tagQuery))
    {
        $plural = 's';
        
        if($tagCount == 1)
            $plural = '';
        
        $tagLink = urlencode($tagName);
        $tagTitle = str_replace('-', ' ', $tagName);
        $tagLinks[] = "<a href='/?tag/$tagLink' title='$tagCount tagged page{$plural}'>$tagTitle</a>";
    }

    $tagLinks = implode(" | ", $tagLinks);
    
    if($tagLinks)
        $tagLinks = "<hr />Tags: $tagLinks";
    
    $PageTitle = PageTitler($PageTitle);

    if(empty($PageContent))
    {
        $PageContent = array("Hello friend. b{Wetfish regrets to inform you this page does not exist.}",
                             "",
                             "Confused? This is the {{wiki|Wetfish Wiki}}, a place anyone can edit!",
                             "It appears you've stumbled upon a place none have yet traveled.",
                             "Would you like to be the first? {{{$path}/?edit|All it takes is a click.}}",
                             "",
                             "i{But please, don't wallow.}",
                             "i{A new page surely follows.}",
                             "i{You have the power.}");

        $PageContent = implode("<br />", $PageContent);
    }
    
    else
    {
        mysql_query("Update `Wiki_Pages` set `Views` = `Views` + 1 where `ID`='$PageID'");
    }

    $Title[] = FishFormat($PageTitle, "strip");
    $content['Title'] .= FishFormat($PageTitle);
    $content['Body'] .= FishFormat($PageContent);

    if($PageEdits)
    {
        $EditCount = count(explode(",", $PageEdits));
        
        date_default_timezone_set('America/New_York');
        $PageEditTime = formatTime($PageEditTime);

        if($pageViews != 1)
            $viewPlural = 's';

        if($EditCount != 1)
            $Plural = "s";

        $content['Tags'] = $tagLinks;
        $content['Footer'] = "<b>".number_format($pageViews)."</b> page view{$viewPlural}. <b>$EditCount</b> edit{$Plural} &ensp;&mdash;&ensp; Last modified <b>$PageEditTime</b>.";
    }

    return $content;
}

?>
