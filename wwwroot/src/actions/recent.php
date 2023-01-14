<?php

function recent($path, $action, $title, $content)
{
//		$Head = '<meta name="robots" content="noindex, nofollow" />';
    $content['UserNav']->Active("Recent Activity");

    if(empty($_SESSION['Recent']))
    {
        $_SESSION['Recent']['Active'][0] = "All Edits";
        $_SESSION['Recent']['Active'][3] = "Descending";
        $_SESSION['Recent']['Order'] = "DESC";
    }

    $content['ExtraNav'] = new Navigation;
    $content['ExtraNav']->Add("All Edits", "?recent/all");
    $content['ExtraNav']->Add("Most Recent Edit", "?recent/unique");
    $content['ExtraNav']->Add("Ascending", "?recent/asc");
    $content['ExtraNav']->Add("Descending", "?recent/desc");
    $content['ExtraNav']->Active($_SESSION['Recent']['Active']);

    $Template['Title'] = "View:";
    $content['ExtraNav']->Template($Template);

    if(empty($action[1]))
    {
        $content['Title'] = "Recent Activity";

        $ActivityQuery = "SELECT $Unique `ID`,`PageID`,`AccountID`,`EditTime`,`Size`,`Tags`,`Name`,`Description`,`Title` FROM `Wiki_Edits` where `Archived` = 0 ORDER BY `ID` {$_SESSION['Recent']['Order']}";
        list($QueryData, $Links) = Paginate($ActivityQuery, 50, $_GET['page'], $_SERVER['QUERY_STRING']);
        
        $content['Body'] .= "<center class='page-navigation'>$Links</center>";
        $content['Body'] .= "<table width='100%' class='history'>";
        $content['Body'] .= "<tr><td><b>Revision</b></td><td><b>Size</b></td><td><b>Tags</b></td><td><b>Editor</b></td><td style='min-width:200px;'><b>Title</b></td><td><b>Description</b></td></tr>";
        
        if($QueryData)
        {
            foreach($QueryData as $Result)
            {
                list($EditID, $PageID, $AccountID, $PageTime, $PageSize, $pageTags, $PageName, $PageDescription, $PageTitle) = $Result;

                if(empty($Data[$PageID]))
                {
                    $PageQuery = mysql_query("SELECT `Path` FROM `Wiki_Pages` WHERE `ID`='$PageID'");
                    list($PagePath) = mysql_fetch_array($PageQuery);

                    $Data[$PageID] = $PagePath;
                }
                else
                    $PagePath = $Data[$PageID];

                $Toggle++;
                
                date_default_timezone_set('America/New_York');
                $minWidth = (recentTime($PageTime)) ? 85 : 175;
                $PageTime = formatTime($PageTime);


                if($Toggle % 2 == 1)
                    $Class = "class='toggle'";
                else
                    $Class = '';

                $PageName = FishFormat($PageName, "format");
                $PageDescription = FishFormat($PageDescription, "format");
                $PageTitle = FishFormat($PageTitle, "format");
                $DiffURL = str_replace("//", "/", "/$PagePath/?diff/$EditID");
                
                $content['Body'] .= "<tr $Class><td style='min-width:{$minWidth}px;'>$PageTime</td><td>$PageSize</td><td>$pageTags</td><td><b><a href='/edits?name=$PageName'>$PageName</a></b></td><td style='max-width:400px'><span style='float:right;'><a href='$DiffURL' rel='nofollow'>d</a></span><b><a href='/$PagePath'>$PageTitle</a></b></td><td class='multi-line'>$PageDescription</td></tr>";
            }
        }

        $content['Body'] .= "</table>";
        $content['Body'] .= "<center class='page-navigation bottom'>$Links</center>";			
    }
    elseif($action[1] == "page")
    {

    }
    else
    {
        switch($action[1])
        {
            case "all":
                $_SESSION['Recent']['Active'][0] = "All Edits";
                $_SESSION['Recent']['Active'][1] = "";
                $_SESSION['Recent']['View'] = "";
            break;

            case "unique":
                $_SESSION['Recent']['Active'][0] = "";
                $_SESSION['Recent']['Active'][1] = "Most Recent Edit";
                $_SESSION['Recent']['View'] = "DISTINCT";
            break;

            case "asc":
                $_SESSION['Recent']['Active'][2] = "Ascending";
                $_SESSION['Recent']['Active'][3] = "";
                $_SESSION['Recent']['Order'] = "";
            break;

            case "desc":
                $_SESSION['Recent']['Active'][2] = "";
                $_SESSION['Recent']['Active'][3] = "Descending";
                $_SESSION['Recent']['Order'] = "DESC";
            break;
        }

        $content['Body'] = "<b>Settings changed...</b> <meta http-equiv='refresh' content='2;url=".FormatPath("/$path/?recent")."'>";
    }

    return array($title, $content);
}

?>
