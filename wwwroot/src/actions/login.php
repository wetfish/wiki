<?php

function login($path, $action, $title, $content)
{
    include dirname(__FILE__).'/../connection.php';

    $content['UserNav']->Active("Login");
    $content['Title'] = "Super Secret Login Form";
    if (empty($content['Body'])) {
        $content['Body'] = '';
    }
    if($_POST)
    {
        if($_POST['Password'] == LOGIN_PASSWORD)
        {
            $_SESSION['bypass'] = true;
            $content['Body'] = "YOU DID IT FRIEND!!<br /><br />You will now be brought back to your previous page.";
            $content['Body'] .= Redirect(str_replace("//", "/", "/$path"));
        }

        elseif($_POST['Password'] == ADMIN_PASSWORD)
        {
            $_SESSION['bypass'] = true;
            $_SESSION['admin'] = true;
            $content['Body'] = "Wow, you're an admin!!<br /><br />You will now be brought to the admin page.";
            $content['Body'] .= Redirect(str_replace("//", "/", "/$path?admin"));
        }

        else
        {
            $content['Body'] = "nope";
        }
    }

    else
    {
        if(!empty($_SESSION['bypass']))
        {
            $content['Body'] .= "<b>Hey, you're already logged in!</b><br>";
        }

        $content['Body'] .= "Protip: The super secret password is the same as the PIBDGAF ichc password.";

        $Form['_Options'] = "action:".str_replace("//", "/", "/$path/?login").";";
        $Form['Password']['Text'] = "Password:";
        $Form['Password']['Form'] = "name:Password; type:password;";
        $Form['Submit']['Form'] = "type:submit; value:Submit;";
        
        $content['Body'] .= Format($Form);
    }

    return array($title, $content);
}
