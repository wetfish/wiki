<?php

function view($path, $action, $title, $content)
{
    include dirname(__FILE__).'/../connection.php';

    $content['PageNav']->Active("View Page");
    $tagLinks = null;
    $PageQuery = mysqli_query($mysql,"SELECT `ID`,`Title`,`Content`,`Edits`,`Views`,`EditTime` FROM `Wiki_Pages` WHERE `Path`='$path'");
    list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysqli_fetch_array($PageQuery);
    
    $tagQuery = mysqli_query($mysql,"Select tags.`tag`, stats.`count`
                                from `Wiki_Tags` as tags,
                                     `Wiki_Tag_Statistics` as stats
                                     
                                where tags.`pageID` = '$PageID'
                                    and stats.`tag` = tags.`tag`");

    while(list($tagName, $tagCount) = mysqli_fetch_array($tagQuery))
    {
        $plural = 's';
        
        if($tagCount == 1)
            $plural = '';
        
        $tagLink = urlencode($tagName);
        $tagTitle = str_replace('-', ' ', $tagName);
        $tagLinks[] = "<a href='/?tag/$tagLink' title='$tagCount tagged page{$plural}'>$tagTitle</a>";
    }

    if($tagLinks)
    {
        $tagLinks = implode(" | ", $tagLinks);    
        $tagLinks = "<hr />Tags: $tagLinks";
    }

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
        mysqli_query($mysql,"Update `Wiki_Pages` set `Views` = `Views` + 1 where `ID`='$PageID'");
    }

    if(!empty($_SESSION['admin']))
    {
        $content['ExtraNav'] = new Navigation;
        $content['ExtraNav']->Add("Archive This Page", FormatPath("/$path/")."?archive");
        $content['ExtraNav']->Add("Rename This Page", FormatPath("/$path/")."?rename");
    }

    $title[0] = FishFormat($PageTitle, "strip");
    if(!empty($content['Title'])) {
        $content['Title'] .= FishFormat($PageTitle);
    }
    
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
    
    return array($title, $content);
}

?>
