<?php

function random($path, $action, $title, $content)
{
    if($path)
    {
        $PageQuery = mysql_query("SELECT `ID`,`Title`,`Content`,`Edits`,`Views`,`EditTime` FROM `Wiki_Pages` WHERE `Path` = '$path'");
        list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysql_fetch_array($PageQuery);

    
        $pagePrevious = RandomRow('Wiki_Pages', 'ID');
        $pageNext = RandomRow('Wiki_Pages', 'ID');
        
        if($pagePrevious)
            $previous = $pagePrevious;
        
        if($pageNext)
            $next = $pageNext;
        
        
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

        if($previous['Path'])
            $previous['Path'] = "/{$previous['Path']}/?random";
        else
            $previous['Path'] = "/?random";

        if($next['Path'])
            $next['Path'] = "/{$next['Path']}/?random";
        else
            $next['Path'] = "/?random";

        if($_SESSION['admin'])
        {
            $content['ExtraNav'] = new Navigation;
            $content['ExtraNav']->Add("Archive This Page", FormatPath("/$path/")."?archive");
        }

        $title[] = FishFormat($PageTitle, "strip");
        $content['Title'] .= "<a href='{$previous['Path']}' title='Previous - {$previous['Title']}'>&#8668;</a> ".FishFormat($PageTitle)." <a href='{$next['Path']}' title='Next - {$next['Title']}'>&#8669;</a>";
        $content['Body'] .= FishFormat($PageContent);
        $content['Tags'] = $tagLinks;
        
        $content['Body'] .= <<<JavaScript

<script>
    $(document).ready(function ()
    {
        $('body').on('keydown', function(event)
        {
            event.stopImmediatePropagation()
            
            if(event.keyCode == 37) // Previous
                history.back();
            else if(event.keyCode == 39) // Next
                location.href = '{$next['Path']}';

                
//			console.log(event);
        });
    });
</script>

JavaScript;

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
//			$content['Footer'] = "This page has been edited <b>$EditCount</b> time{$Plural}, and was last edited on $PageEditTime.";
        }
    
    }
    else
    {
        $Head = '<meta name="robots" content="noindex, nofollow" />';
        $Random = RandomRow('Wiki_Pages', 'ID');			
        $ID = uuid();

        $randomTitles = array('Wormhole open: ', 'An adventure!', 'Welcome to', 'Internet Space Award', 'Friendship served', 'WOW!');
        $randomPhrases = array('Hold on to your hat!', 'Hold on to your butt!!', 'I love butts', 'Wet, fish', 'I LOVE ANIME!!!!!!!!', 'COOL!');
        
        shuffle($randomTitles);
        shuffle($randomPhrases);

        header("Location: /{$Random['Path']}/?random");
        exit;
    }

    return array($title, $content);
}

?>
