<?php

function replace($path, $action, $title, $content)
{
    // Make sure user is an admin
    if(!$_SESSION['admin'])
    {
        $title[] = "Nope";
        $content['Body'] = "get out.";
        
        return array($title, $content);
    }
    
    if(!empty($_POST))
    {
        $count = array
        (
            'pages' => 0,
            'edits' => 0
        );
        
        // Loop through all pages
        $pageQuery = mysql_query("Select `ID`, `Content` from `Wiki_Pages`");
        while(list($pageID, $pageContent) = mysql_fetch_array($pageQuery))
        {
            $pageContent = str_replace($_POST['find'], $_POST['replace'], $pageContent);
            $pageContent = mysql_real_escape_string($pageContent);
            
            mysql_query("Update `Wiki_Pages` set `Content` = '{$pageContent}' where `ID` = '{$pageID}'");
            unset($pageID, $pageContent);

            $count['pages']++;
        }
        
        // Loop through all edits
        $editQuery = mysql_query("Select `ID`, `Content` from `Wiki_Edits`");
        while(list($editID, $editContent) = mysql_fetch_array($editQuery))
        {
            $editContent = str_replace($_POST['find'], $_POST['replace'], $editContent);
            $editContent = mysql_real_escape_string($editContent);
            
            mysql_query("Update `Wiki_Edits` set `Content` = '{$editContent}' where `ID` = '{$editID}'");
            unset($editID, $editContent);

            $count['edits']++;
        }

        $content['Title'] = "Wiki updated!";
        $content['Body'] = "<p>{$count['pages']} pages modified.</p>";
        $content['Body'] .= "<p>{$count['edits']} edits modified.</p>";
    }
    else
    {
        $content['Title'] = "Find and replace things!";
        $content['Body'] = "<p><b>WARNING: Doing this will replace every occurence of a string in all pages and edits.</b></p>";

        $Form['_Options'] = "action:".str_replace("//", "/", "/?replace").";";
        $Form['Find']['Text'] = "Find";
        $Form['Find']['Form'] = "name:find; type:text;";
        $Form['Replace']['Text'] = "Replace";
        $Form['Replace']['Form'] = "name:replace; type:text;";
        $Form['Submit']['Form'] = "type:submit; value:Submit;";
        
        $content['Body'] .= Format($Form, Form);
    }
    
    return array($title, $content);
}
