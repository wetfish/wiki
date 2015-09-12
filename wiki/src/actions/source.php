<?php

function source($path, $action, $title, $content)
{
    $Head = '<meta name="robots" content="noindex, nofollow" />';
    $content['PageNav']->Active("Page History");

    $content['ExtraNav'] = new Navigation;
    $content['ExtraNav']->Add("View Revision", FormatPath("/$path/")."?history/$action[1]");
    $content['ExtraNav']->Add("View Diff", FormatPath("/$path/")."?diff/$action[1]");

    if(is_numeric($action[1]))
    {
        $PageQuery = mysql_query("SELECT `AccountID`,`EditTime`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$action[1]' and `Archived` = 0");
        list($AccountID, $PageEditTime, $PageName, $PageDescription, $PageTitle, $PageContent) = mysql_fetch_array($PageQuery);

        $Form['_Options'] = "action:;";

        $Form['Name']['Text'] = "Name:";
        $Form['Name']['Form'] = "id:Name; name:Name; value:{".$PageName."}; maxlength:32;";

        $Form['Title']['Text'] = "Title:";
        $Form['Title']['Form'] = "name:Title; value:x{".$PageTitle."}x; maxlength:255;";

        $Form['Content']['Text'] = "Content:";
        $Form['Content']['Form'] = "name:Content; value:x{".$PageContent."}x; type:textarea; cols:80; rows:12;";

        $Form['Description']['Text'] = "Description:";
        $Form['Description']['Form'] = "name:Description; value:x{".$PageDescription."}x; size: 80; maxlength:255;";

        $content['Title'] = "Viewing Source of: $PageTitle";
        $content['Body'] .= Format($Form, Form);

        date_default_timezone_set('America/New_York');
//			$PageEditTime = date("F j\, Y G:i:s", $PageEditTime)." EST";
        $PageEditTime = formatTime($PageEditTime);
        $content['Footer'] = "This page is an old revision made by <b><a href='/names?id=$AccountID'>$PageName</a></b> on $PageEditTime.";
    }

    return array($title, $content);
}

?>
