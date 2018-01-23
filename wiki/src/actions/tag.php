<?php

function tag($path, $action, $title, $content)
{
    $action = implode('/', $action);
    $action = explode('/', $action, 3);
    
    $tag = Clean($action[1]);
    $cleanTag = ucwords(str_replace('-', ' ', $tag));

    if(isset($action[2]))
        $path = $action[2];

    $totalQuery = mysql_query("Select stats.`count`
                                from `Wiki_Tag_Statistics` as stats
                                where stats.`tag` = '$tag'");
                                                                
    
    $nextQuery = mysql_query("Select `Path`, `Title`
                                from `Wiki_Pages`,
                                    `Wiki_Tags` as tag
                                where tag.`tag` = '$tag' and tag.`pageID` = `ID`
                                    order by tag.`tagID` desc limit 1");

    $previousQuery = mysql_query("Select `Path`, `Title`
                                from `Wiki_Pages`,
                                    `Wiki_Tags` as tag
                                where tag.`tag` = '$tag' and tag.`pageID` = `ID`
                                    order by tag.`tagID` limit 1");
                                    
    list($tagTotal) = mysql_fetch_array($totalQuery);
    $next = mysql_fetch_array($nextQuery);
    $previous = mysql_fetch_array($previousQuery);	
                
    // Check if we're actually on the home page
    if($path or isset($action[2]) or preg_match("{^/home}", $_SERVER['REQUEST_URI']))
    {	
        $PageQuery = mysql_query("SELECT `ID`,`Title`,`Content`,`Edits`,`Views`,`EditTime`,tag.`tagID` FROM `Wiki_Pages`, `Wiki_Tags` as tag WHERE `Path` like '$path' and tag.`tag` = '$tag' and tag.`pageID` = `ID`");
        list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime, $tagID) = mysql_fetch_array($PageQuery);

        $previousQuery = mysql_query("Select `Path`, `Title`
                                        from `Wiki_Pages`,
                                            `Wiki_Tags` as tag
                                        where tag.`tag` = '$tag' and tag.`pageID` = `ID` and tag.`tagID` >'$tagID'
                                            order by tag.`tagID` limit 1");

        $nextQuery = mysql_query("Select `Path`, `Title`
                                    from `Wiki_Pages`,
                                        `Wiki_Tags` as tag
                                    where tag.`tag` = '$tag' and tag.`pageID` = `ID` and tag.`tagID` < '$tagID'
                                        order by tag.`tagID` desc limit 1");
                                            
        $pagePrevious = mysql_fetch_array($previousQuery);
        $pageNext = mysql_fetch_array($nextQuery);
        
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

        if($_SESSION['admin'])
        {
            $content['ExtraNav'] = new Navigation;
            $content['ExtraNav']->Add("Archive This Page", FormatPath("/$path/")."?archive");
            $content['ExtraNav']->Add("Rename This Page", FormatPath("/$path/")."?rename");
        }

        if($previous['Path'])
            $previous['Path'] = "/{$previous['Path']}/?tag/$tag";
        else
            $previous['Path'] = "/?tag/$tag/";

        if($next['Path'])
            $next['Path'] = "/{$next['Path']}/?tag/$tag";
        else
            $next['Path'] = "/?tag/$tag/";

        $title[] = FishFormat($PageTitle, "strip");
        $content['Title'] .= "<a href='{$previous['Path']}' title='Previous - {$previous['Title']}'>⟨</a> ".FishFormat($PageTitle)." <a href='{$next['Path']}' title='Next - {$next['Title']}'>⟩</a>";
        $content['Body'] .= FishFormat($PageContent);
        $content['Tags'] = $tagLinks;
    }
    else
    {
        mysql_query("Update `Wiki_Tag_Statistics` set `views` = `views` + 1
                        where `tag` = '$tag'");
    
        if($previous['Path'])
            $previous['Path'] = "/{$previous['Path']}/?tag/$tag";
        else
            $previous['Path'] = "/?tag/$tag/";

        if($next['Path'])
            $next['Path'] = "/{$next['Path']}/?tag/$tag";
        else
            $next['Path'] = "/?tag/$tag/";
    
    
        $content['Title'] = "Pages tagged: <a href='{$previous['Path']}' title='Previous - {$previous['Title']}'>⟨</a> $cleanTag <a href='{$next['Path']}' title='Next - {$next['Title']}'>⟩</a>";
        
        $pageQuery = "SELECT `ID`,`Path`,`Title`,`Content`,`Edits`, `EditTime`
                        FROM `Wiki_Pages`,
                             `Wiki_Tags` as tag
                        WHERE tag.`tag` = '$tag' and tag.`pageID` = `ID`
                        order by tag.`tagID` desc";
        
        list($Data, $Links) = Paginate($pageQuery, 50, $_GET['page'], $_SERVER['QUERY_STRING']);
        
        if($Data)
        {
            $content['Body'] .= "<center class='page-navigation'>$Links</center>";
            
            foreach($Data as $Result)
            {
                list($pageID, $pagePath, $pageTitle, $pageContent) = $Result;
                
                $tagQuery = mysql_query("Select tags.`tag`, stats.`count`
                                            from `Wiki_Tags` as tags,
                                                 `Wiki_Tag_Statistics` as stats
                                                 
                                            where tags.`pageID` = '$pageID'
                                                and stats.`tag` = tags.`tag`");
                
                $tagLinks = array();
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
                
                if($Count % 4 == 1 or $Count % 4 == 2)
                    $class = 'toggle';
                else
                    $class = '';
                
                if($Count % 2 == 0)
                    $content['Body'] .= "<div class='clear'></div>";

                if(!$pagePath)
                    $pagePath = "home";
                
                $content['Body'] .= "<div class='$class' style='float:left; width:50%'><div style='padding:16px'>";
                $content['Body'] .= "<a href='/$pagePath/?tag/$tag' style='font-weight:bold'>$pageTitle</a><br />";
                $content['Body'] .= "Tags: $tagLinks";
                $content['Body'] .= "</div></div>";
                $Count++;
            }
            
            $content['Body'] .= "<div class='clear'></div>";
            $content['Body'] .= "<center class='page-navigation bottom'>$Links</center>";
        }
        
        if(empty($Count))
            $content['Body'] .= "<br /><b>Sorry friend, it appears the tag you're looking for doesn't exist.</b>";			
    }
    
    if($tagTotal == 1)
        $footerPlural = '';
    else
        $footerPlural = 's';
    

/*
    if($previous['Path'])
        $previous['Path'] = "/{$previous['Path']}/?tag/$tag";
    else
        $previous['Path'] = "/?tag/$tag/";

    if($next['Path'])
        $next['Path'] = "/{$next['Path']}/?tag/$tag";
    else
        $next['Path'] = "/?tag/$tag/";
    */
    $content['Body'] .= <<<JavaScript
    
    <script>
        $(document).ready(function ()
        {
            $('body').on('keydown', function(event)
            {
                // what?

                event.stopImmediatePropagation()
                
                if(event.keyCode == 37) // Previous
                    location.href = '{$previous['Path']}';
                else if(event.keyCode == 39) // Next
                    location.href = '{$next['Path']}';

                    
    //			console.log(event);
            });
        });
    </script>
    
JavaScript;

    
    $content['Footer'] = " <a href='{$previous['Path']}' title='Previous - {$previous['Title']}'>Previous</a> &emsp; You are browsing <b><a href='/?tag/$tag'>$cleanTag</a></b>, this tag appears on <b>$tagTotal</b> page{$footerPlural}. &emsp; <a href='{$next['Path']}' title='Next - {$next['Title']}'>Next</a>";
    return array($title, $content);
}

?>
