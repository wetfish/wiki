<?php

function archive($path, $action, $title, $content)
{
    include dirname(__FILE__).'/../connection.php';
    // Make sure user is an admin
    if(!$_SESSION['admin'])
    {
        $title[] = "Nope";
        $content['Body'] = "get out.";
        
        return array($title, $content);
    }
    
    // If we're archiving a specific edit
    if(is_numeric($action[1]))
    {
        if($_POST['confirmed'])
        {
            mysqli_query($mysql, "Update `Wiki_Edits` set `Archived` = '1' where `ID` = '{$action[1]}'");
            $content['Body'] = "<p><b>Edit archived!</b></p>";
        }
        else
        {
            $content['Title'] = "Are you sure you want to archive this edit?";
            $content['Body'] = "<p><b>Doing so will remove this edit from the page history.</b></p>";

            $Form['_Options'] = "action:".str_replace("//", "/", "/$path/?archive/{$action[1]}").";";
            $Form['Confirmed']['Text'] = "Are you sure?";
            $Form['Confirmed']['Form'] = "name:confirmed; type:hidden; value:true";
            $Form['Submit']['Form'] = "type:submit; value:Yes;";
            
            $content['Body'] .= Format($Form, 'Form');
        }
    }
    
    // If we're archiving an entire page
    else
    {
        if($_POST['confirmed'])
        {
            // Get the page ID
            $PageQuery = mysqli_query($mysql,"SELECT `ID` FROM `Wiki_Pages` WHERE `Path`='$path'");
            list($pageID) = mysqli_fetch_array($PageQuery);

            if($pageID)
            {
                mysqli_query($mysql,"Update `Wiki_Edits` set `Archived` = '1' where `PageID` = '{$pageID}'");
                mysqli_query($mysql,"Delete from `Wiki_Tags` where `pageID` = '{$pageID}'");
                mysqli_query($mysql,"Delete from `Wiki_Pages` where `ID` = '{$pageID}'");
                $content['Body'] = "<p><b>Page archived!</b></p>";
            }
            else
            {
                $content['Body'] = "<p><b>This isn't a page you goose.</b></p>";
            }

        }
        else
        {
            $content['Title'] = "Are you sure you want to archive this page?";
            $content['Body'] = "<p><b>Doing so will remove this page from the wiki and all edits will be archived.</b></p>";

            $Form['_Options'] = "action:".str_replace("//", "/", "/$path/?archive").";";
            $Form['Confirmed']['Text'] = "Are you sure?";
            $Form['Confirmed']['Form'] = "name:confirmed; type:hidden; value:true";
            $Form['Submit']['Form'] = "type:submit; value:Yes;";
            
            $content['Body'] .= Format($Form, 'Form');
        }
    }
    
    return array($title, $content);
}
