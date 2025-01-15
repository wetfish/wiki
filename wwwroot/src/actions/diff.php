<?php

// Include the diff class
require_once dirname(__FILE__).'/../libraries/Diff.php';

function diff($path, $action, $title, $content)
{
    include dirname(__FILE__).'/../connection.php';
    $Head = '<meta name="robots" content="noindex, nofollow" />';
    $content['PageNav']->Active("Page History");
    
    if(is_numeric($action[1]))
    {
        $pageQuery = mysqli_query($mysql,"SELECT `PageID`,`AccountID`,`EditTime`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$action[1]' and `Archived` = 0");
        list($PageID, $AccountID, $PageEditTime, $PageName, $PageDescription, $PageTitle, $pageContent) = mysqli_fetch_array($pageQuery);

        $previousQuery = mysqli_query($mysql,"Select `ID`, `Content` from `Wiki_Edits` where `ID` < '$action[1]' and `PageID`='$PageID' and `Archived` = 0 order by `ID` desc limit 1");
        list($previousID, $previousContent) = mysqli_fetch_array($previousQuery);

        $nextQuery = mysqli_query($mysql,"Select `ID` from `Wiki_Edits` where `ID` > '$action[1]' and `PageID`='$PageID' and `Archived` = 0 order by `ID` limit 1");
        list($nextID) = mysqli_fetch_array($nextQuery);

        if(!empty($previousID))
        {
            $previousPath = FormatPath("/$path/?diff/{$previousID}");
            $content['Title'] = "<a href='$previousPath' title='Previous Revision'>⟨</a> ";
        }

        $content['Title'] .= FishFormat($PageTitle);
        
        if(!empty($nextID))
        {
            $nextPath = FormatPath("/$path/?diff/{$nextID}");
            $content['Title'] .= " <a href='$nextPath' title='Next Revision'>⟩</a>";
        }

        $content['ExtraNav'] = new Navigation;
        $content['ExtraNav']->Add("View Revision", FormatPath("/$path/")."?history/$action[1]");
        $content['ExtraNav']->Add("View Source", FormatPath("/$path/")."?source/$action[1]");

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
                });
            });
        </script>
JavaScript;
        
        $old = explode("\n", html_entity_decode($previousContent, ENT_QUOTES));
        $new = explode("\n", html_entity_decode($pageContent, ENT_QUOTES));

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

    return array($title, $content);
}

?>
