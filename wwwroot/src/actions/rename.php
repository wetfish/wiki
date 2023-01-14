<?php

function action_rename($path, $action, $title, $content)
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
        $path = mysql_real_escape_string($path);
        $page_query = mysql_query("Select ID from `Wiki_Pages` where Path = '{$path}' limit 1");
        list($page_id) = mysql_fetch_array($page_query);

        // We can only change the path of an existing page...
        if($page_id)
        {
            $new = str_replace(" ", "-", $_POST['new']);
            mysql_query("Update `Wiki_Pages` set `Path` = '".mysql_real_escape_string($new)."' where ID = '{$page_id}'");
            
            $content['Title'] = "Page renamed!";
            $content['Body'] = "<p>The old page <b>{$path}</b> has been renamed to <a href='/{$new}'>{$_POST['new']}</a></p>";
        }
        else
        {
            $content['Title'] = "There was an error!";
            $content['Body'] = "<p>You tried to rename a page that doesn't exist!</p>";
        }
    }
    else
    {
        $content['Title'] = "Rename this page";

        $Form['_Options'] = "action:".str_replace("//", "/", "/{$path}?rename").";";
        $Form['Old']['Text'] = "Old Path";
        $Form['Old']['Form'] = "name:old; type:text; readonly:true; value:{$path};";
        $Form['New']['Text'] = "New Path";
        $Form['New']['Form'] = "name:new; type:text;";
        $Form['Submit']['Form'] = "type:submit; value:Submit;";
        
        $content['Body'] .= Format($Form, Form);
    }
    
    return array($title, $content);
}
