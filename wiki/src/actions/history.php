<?php

function history($path, $action, $title, $content)
{
    global $benchmark;
    $benchmark->start("Viewing History: $path");
    $Head = '<meta name="robots" content="noindex, nofollow" />';
    $content['PageNav']->Active("Page History");

    $pageQuery = mysql_query("Select `ID` from `Wiki_Pages` where `Path`='$path'");
    list($pageID) = mysql_fetch_array($pageQuery);

    $totalQuery = mysql_query("Select `ID`
                                from `Wiki_Edits`
                                where `PageID` = '$pageID' and `Archived` = 0");
    
    $nextQuery = mysql_query("Select `ID`, `Title`
                                from `Wiki_Edits`
                                where `PageID` = '$pageID' and `Archived` = 0
                                order by `ID` desc limit 1");

    $previousQuery = mysql_query("Select `ID`, `Title`
                                from `Wiki_Edits`
                                where `PageID` = '$pageID' and `Archived` = 0
                                order by `ID` limit 1");


    $totalEdits = mysql_num_rows($totalQuery);
    $next = mysql_fetch_array($nextQuery);
    $previous = mysql_fetch_array($previousQuery);	

    $benchmark->log('Page Queries');
        
    if(is_numeric($action[1]))
    {
        $PreviousQuery = mysql_query("Select `Content` from `Wiki_Edits` where `ID` < '$action[1]' and `Archived` = 0 order by `ID` desc limit 1");
        list($PreviousContent) = mysql_fetch_array($PreviousQuery);

        $PageQuery = mysql_query("SELECT `AccountID`,`EditTime`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$action[1]' and `Archived` = 0");
        list($AccountID, $PageEditTime, $PageName, $PageDescription, $PageTitle, $PageContent) = mysql_fetch_array($PageQuery);
        
        
        $previousQuery = mysql_query("Select `ID`, `Title`
                                        from `Wiki_Edits`
                                        where `PageID` = '$pageID' and `ID` > '$action[1]' and `Archived` = 0
                                            order by `ID` limit 1");

        $nextQuery = mysql_query("Select `ID`, `Title`
                                        from `Wiki_Edits`
                                        where `PageID` = '$pageID' and `ID` < '$action[1]' and `Archived` = 0
                                            order by `ID` desc limit 1");
                                            
        $pagePrevious = mysql_fetch_array($previousQuery);
        $pageNext = mysql_fetch_array($nextQuery);
        
        if($pagePrevious)
            $previous = $pagePrevious;
        
        if($pageNext)
            $next = $pageNext;
        
        
        $content['ExtraNav'] = new Navigation;
        $content['ExtraNav']->Add("View Source", FormatPath("/$path/")."?source/$action[1]");
        $content['ExtraNav']->Add("View Difference", FormatPath("/$path/")."?diff/$action[1]");			
        
        if($_SESSION['Verified'] == 1)
        {
            $content['ExtraNav']->Add("Revert Page", FormatPath("/$path/")."?revert/$action[1]");
        }

        if($_SESSION['admin'])
        {
            $content['ExtraNav']->Add("Archive This Edit", FormatPath("/$path/")."?archive/$action[1]");
        }

        $benchmark->log('Before Formatting');
        $title[] = FishFormat($PageTitle, "strip");
        $benchmark->log('Title Stripped');
        $PageContent = OldFishFormat($PageContent);
        $benchmark->log('Body Formatted');

        $previousPath = FormatPath("/$path/?history/{$previous['ID']}");
        $nextPath = FormatPath("/$path/?history/{$next['ID']}");
        
        $content['Title'] .= "<a href='$previousPath' title='Previous - {$previous['Title']}'>⟨</a> ". FishFormat($PageTitle) ." <a href='$nextPath' title='Next - {$next['Title']}'>⟩</a>";
        $content['Body'] .= $PageContent;
        
        date_default_timezone_set('America/New_York');
//			$PageEditTime = date("F j\, Y G:i:s", $PageEditTime)." EST";
        $PageEditTime = formatTime($PageEditTime);
        $content['Footer'] = "This page is an old revision made by <b><a href='/names?id=$AccountID'>$PageName</a></b> on $PageEditTime.";

        if($PageDescription)
            $content['Footer'] .= "<br />'$PageDescription'";
    }
    else
    {
        $previousPath = FormatPath("/$path/?history/{$previous['ID']}");
        $nextPath = FormatPath("/$path/?history/{$next['ID']}");
        
        $content['Title'] = "<a href='$previousPath' title='Previous - {$previous['Title']}'>⟨</a> Page History <a href='$nextPath' title='Next - {$next['Title']}'>⟩</a>";
        
        $PageQuery = mysql_query("SELECT `ID` FROM `Wiki_Pages` WHERE `Path`='$path'");
        list($PageID) = mysql_fetch_array($PageQuery);

        $HistoryQuery = "SELECT `ID`,`AccountID`,`EditTime`,`Size`,`Tags`,`Name`,`Description`,`Title` FROM `Wiki_Edits` WHERE `PageID`='$PageID' and `Archived` = 0 ORDER BY `ID` DESC";

        $request = parse_url($_SERVER['REQUEST_URI']);
        list($Data, $Links) = Paginate($HistoryQuery, 50, $_GET['page'], $request['query']);

        if($_SESSION['admin'])
        {
            $content['ExtraNav'] = new Navigation;
            $content['ExtraNav']->Add("Archive All Edits", FormatPath("/$path/")."?archive");
        }

        
        $content['Body'] .= "<hr /><center>$Links</center><hr />";
        $content['Body'] .= "<table width='100%' class='history'>";
        $content['Body'] .= "<tr><td><b>Revision</b></td><td><b>Size</b></td><td><b>Tags</b></td><td><b>Editor</b></td><td><b>Title</b></td><td><b>Description</b></td></tr>";
        
        foreach($Data as $Result)
        {
            list($HistoryID, $AccountID, $HistoryTime, $HistorySize, $historyTags, $HistoryName, $HistoryDescription, $HistoryTitle) = $Result;
    
            $Toggle++;
            
            date_default_timezone_set('America/New_York');
            $minWidth = (recentTime($HistoryTime)) ? 85 : 175;
            $HistoryTime = formatTime($HistoryTime);
        
            if($Toggle % 2 == 1)
                $Class = "class='toggle'";
            else
                $Class = '';

            $HistoryName = FishFormat($HistoryName, "format");
            $HistoryDescription = FishFormat($HistoryDescription, "format");
            $HistoryTitle = FishFormat($HistoryTitle, "format");

            $HistoryURL = str_replace("//", "/", "/$path/?history/$HistoryID");
            $DiffURL = str_replace("//", "/", "/$path/?diff/$HistoryID");
            
            $content['Body'] .= "<tr $Class><td style='min-width:$minWidth;'>$HistoryTime</td><td>$HistorySize</td><td>$historyTags</td><td><b><a href='/edits?name=$HistoryName'>$HistoryName</a></b></td><td style='max-width:400px;'><span style='float:right;'><a href='$DiffURL' rel='nofollow'>d</a></span><b><a href='$HistoryURL' rel='nofollow'>$HistoryTitle</a></b></td><td>$HistoryDescription</td></tr>";
        }

        $content['Body'] .= "</table>";
        $content['Body'] .= "<hr /><center>$Links</center>";
    }
    
    $content['Body'] .= <<<JavaScript
    
    <script>
        $(document).ready(function ()
        {
            $('body').on('keydown', function(event)
            {
                event.stopImmediatePropagation()
                
                if(event.keyCode == 37) // Previous
                    location.href = '$previousPath';
                else if(event.keyCode == 39) // Next
                    location.href = '$nextPath';

                    
    //			console.log(event);
            });
        });
    </script>
    
JavaScript;


    $benchmark->save();

    if($_SESSION['admin'])
    {
        $benchmark->display();
    }
    
    return array($title, $content);
}

?>
