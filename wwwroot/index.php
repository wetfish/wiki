<?php

error_reporting(E_ERROR | E_PARSE);
session_start();

require_once('src/config.php');
$mysql = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
require('functions.php');
include('src/libraries/simple_html_dom.php');

//require('recaptchalib.php');
require('src/markup/fishformat.php');
require('navigation.php');
include('fun/paginate.php');

if(!class_exists("Benchmark"))
{
    include_once("src/benchmark.php");
    $benchmark = new Benchmark;
}

function PageTitler($Page)
{
    $Titles[] = "Not Titled";
//	$Titles[] = "Useless Page";
//	$Titles[] = "Worthless Trash";
    $Titles[] = "Saucy Marquee";
    $Titles[] = "This isn't quite right.";
    $Titles[] = "Where'd you put it?";
    $Titles[] = "Wetfish... is sorry...";
    $Titles[] = "Find a Better Page!";
    $Titles[] = "Probably not what you wanted.";
    $Titles[] = "Sorry.";
    $Titles[] = "At least you tried?";

    if(empty($Page))
        return $Titles[array_rand($Titles)];

    return $Page;
}

if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
    $userIP = $_SERVER['REMOTE_ADDR'];

// Make sure the user IP is sanitized
$userIP = preg_replace('/[^0-9.:]/', '', $userIP);

// Original apache rewrite
if(isset($_GET['SUPERdickPAGE']))
{
    list($Path, $Action) = explode("@", $_GET['SUPERdickPAGE']);
}
// New nginx rewrite
else
{
    $uri = parse_url($_SERVER['REQUEST_URI']);
    $Path = $uri['path'];

    // Save get parameters
    if(!empty($uri['query']))
    {
        parse_str($uri['query'], $query);

        foreach($query as $key => $value)
        {
            $_GET[$key] = $value;
        }
    }
}

$actions = array('edit', 'preview', 'recent', 'history', 'login', 'register', 'diff', 'source', 'random', 'tag', 'archive', 'replace', 'rename', 'admin');
$get = array_change_key_case($_GET);

foreach($_GET as $action => $value)
{
    $actionText = strtolower($action);
    $action = explode('/', $actionText);

    if(in_array($action[0], $actions)) {
        $Action = $action;
    }
}

if(strpos($Path, ' ') !== FALSE || strpos($Path, '%20') !== FALSE)
    die(Redirect(str_replace(array(' ', '%20'), '-', $Path), 0));

$Path = trim($Path, "/");

if($Path == "home")
    $Path = "";

$Title[] = "wetfish.net";

$Content = array();
$Content['UserNav'] = new Navigation;
$Content['PageNav'] = new Navigation;

$Content['UserNav']->Add("Login", "/$Path/?login");
$Content['UserNav']->Add("Register", "/$Path/?register");
$Content['UserNav']->Add("Recent Activity", "/?recent");

$Content['PageNav']->Add("Home", "/");
$Content['PageNav']->Add("View Page", "/$Path");
$Content['PageNav']->Add("Edit Page", "/$Path/?edit");
$Content['PageNav']->Add("Page History", "/$Path/?history");

if(!empty($_SESSION['Name']))
{
    $LoginQuery = mysqli_query($mysql,"SELECT `ID`,`Name`,`Password`,`Verified`,`EditTime` FROM `Wiki_Accounts` WHERE `ID`='{$_SESSION['ID']}'");
    list($ID, $Name, $Password, $Verified, $EditTime) = mysqli_fetch_array($LoginQuery,MYSQLI_NUM);

    if($Password and $_SESSION['Password'] == $Password)
    {
        $Content['UserNav']->Remove("Login");
        $Content['UserNav']->Remove("Register");
        $Content['UserNav']->Add("Logout", "/$Path/?logout");

        # In case someone ever changes their username? And edits have a tendancy of changing.
        $_SESSION['Name'] = $Name;
        $_SESSION['Verified'] = $Verified;
        $_SESSION['EditTime'] - $EditTime;
    }
    else
    {
        session_unset();
        unset($ID, $Name, $Password, $Verified, $EditTime); # Private user variables! Eek.
    }
}
else
{
    $LoginQuery = mysqli_query($mysql,"SELECT `ID`,`Verified`,`EditTime` FROM `Wiki_Accounts` WHERE `Name`='$userIP'");
    list($ID, $Verified, $EditTime) = mysqli_fetch_array($LoginQuery, MYSQLI_NUM);

    if(empty($ID))
    {
        mysqli_query($mysql,"INSERT INTO `Wiki_Accounts` VALUES ('NULL', '$userIP', '', '', '', '0', '')");
        $ID = mysqli_insert_id($mysql);
    }

    $_SESSION['ID'] = $ID;
    $_SESSION['Verified'] = $Verified;
    $_SESSION['EditTime'] = $EditTime;

    $Navigation['Login']['URL'] = "/$Path/?login";
    $Navigation['Register']['URL'] = "/$Path/?register";
}

switch($Action[0] ?? false)
{
    case "admin":
        if($_SESSION['admin'])
        {
            ob_start();
            phpinfo();
            echo "<style>.v,.e { background-color: rgba(0, 0, 0, 0.5); } a:link { background-color: initial }</style>";
            $phpinfo = ob_get_contents();
            ob_end_clean();

            $Content['Body'] = $phpinfo;
        }
        else
        {
            $Content['Body'] = Redirect("/$PathURL?login", 0);
        }
    break;

    case "fixtags":
        $tagQuery = mysqli_query($mysql,"Select `tag` from `Wiki_Tags`");
        while(list($tag) = mysqli_fetch_array($tagQuery))
        {
            $fixTags[$tag]++;
        }

        foreach($fixTags as $tag => $count)
        {
            mysqli_query($mysql,"Insert into `Wiki_Tag_Statistics`
                            values ('', '$tag', '1', '0', NOW(), NOW())

                            on duplicate key update `count` = '$count'");
        }
    break;

    case "edit":
    case "preview":
        $Head = '<meta name="robots" content="noindex, nofollow" />';


        $Content['PageNav']->Active("Edit Page");
        $Content['Body'] = null;
        if($_SESSION['Verified'] == -1)
        {
            $Content['Body'] = "<b>LOL UR BANNED</b>";
            break;
        }

        $PageQuery = mysqli_query($mysql,"SELECT `ID`,`Title`,`Content`,`Edits`,`Views`, `EditTime` FROM `Wiki_Pages` WHERE `Path`='$Path'");
        list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysqli_fetch_array($PageQuery);

        $originalTags = array();

        $tagQuery = mysqli_query($mysql,"Select `tag` from `Wiki_Tags` where `pageID` = '$PageID'");
        while(list($tagName) = mysqli_fetch_array($tagQuery))
        {
            $originalTags[] = $tagName;
        }

        $tagText = implode(', ', $originalTags);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // code...
            $Name = ($_POST['Name']) ? Clean($_POST['Name']) : $_SESSION['username'];
        }


        if(!empty($_POST))
        {
            $Account = $_POST['Account'];
            $Anonymous = $_POST['Anonymous'];
            $oldPageTitle = $PageTitle;
            $PageTitle = FishFormat(Clean($_POST['Title']), 'edit');
            $oldPageContent = $PageContent;
            $PageContent = FishFormat(Clean($_POST['Content'], 'textarea'), 'edit');
            $Description = Clean($_POST['Description']);
            $oldTagText = $tagText;
            $tagText = Clean($_POST['tags']);
            $Message = $_POST['Message'];
            $Time = Time();
            $Size = strlen($PageContent);

            include('src/ban.php');

            if(isset($banlist))
            {
                if(user_banned($banlist, $userIP))
                {
                    $Form['_Errors']['Content'] = "Error: Your subnet is currently blocked from editing. Please <a href='https://chat.wetfish.net/'>get on IRC</a> for more information!";
                }
            }

            if($Account == "on")
                $Name = "\nAccount";
            elseif($Anonymous == "on")
                $Name = "\nAnonymous";
            else
            {
                if(empty($Name))
                    $Form['_Errors']['Name'] = "Error: You must enter a name!";
                elseif(strlen($Name) > 32)
                    $Form['_Errors']['Name'] = "Error: Your name is too long.";
            }

            if(empty($PageTitle))
                $Form['_Errors']['Title'] = "Error: Pages need titles!";
            elseif(strlen($PageTitle) > 255)
                $Form['_Errors']['Title'] = "Error: Woah now, that's a bit much.";

            if(empty($PageContent))
                $Form['_Errors']['Content'] = "Error: So... you were planning on writing something, right?";
            elseif(strlen($PageContent) > 65000)
                $Form['_Errors']['Content'] = "Error: Maybe you should think about using more than one page.";
            elseif($oldPageTitle == $PageTitle and $oldPageContent == $PageContent and $oldTagText == $tagText)
                $Form['_Errors']['Content'] = "Error: You were going to change something, right?";

            if(strlen($Description) > 255)
                $Form['_Errors']['Description'] = "Error: Hey, c'mon now.";

            if(strlen($tags) > 255)
                $Form['_Errors']['tags'] = "Error: Hey, c'mon now.";

            if(isset($_SESSION['captchaSuccess']))
            {
                $_SESSION['bypass'] = true;
            }

            if(!isset($_SESSION['bypass']))
            {
                if (!$_SESSION['bypass'] and $Action[0] != "preview")
                    $Form['_Errors']['Captcha'] = "You did it wrong! Please try again.";
            }

            if($Time < $_SESSION['EditTime'] + 15)
                $Form['_Erros']['_Global'] = "Please wait 15 seconds between edits.";

            if($_GET['api'] && !empty($Form['_Errors']))
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'session' => $_SESSION['status'], 'message' => 'There was a problem saving your post.', 'data' => $Form['_Errors']));
                exit;
            }

            if(empty($Form['_Errors']) and $Action[0] != "preview")
            {
                $_SESSION['username'] = $Name;
                $newTags = array_filter(array_unique(explode(",", $tagText)));
                $tagCount = count($newTags);

                if($PageID)
                {
                    mysqli_query($mysql,"UPDATE `Wiki_Pages` SET `Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
                    $SQLError .= mysqli_error($mysql);

                    mysqli_query($mysql,"INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$tagCount', '$tagText', '$Name', '$Description', '$PageTitle', '$PageContent', '')");
                    $SQLError .= mysqli_error($mysql);

                    $EditID = mysqli_insert_id($mysql);
                }
                else
                {
                    mysqli_query($mysql,"INSERT INTO `Wiki_Pages` VALUES ('NULL', '1', '$Path', '$PageTitle', '$PageContent', '', '')");
                    $SQLError .= mysqli_error($mysql);

                    $PageID = mysqli_insert_id($mysql);

                    mysqli_query($mysql,"INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$tagCount', '$tagText', '$Name', '$Description', '$PageTitle', '$PageContent', '')");
                    $SQLError .= mysqli_error($mysql);

                    $EditID = mysqli_insert_id($mysql);
                }

                mysqli_query($mysql,"Delete from `Wiki_Tags` where `pageID`='$PageID'");

                foreach($newTags as $tag)
                {
                    $tag = trim($tag);

                    if($tag)
                    {
                        $tag = str_replace(" ", "-", $tag);
                        mysqli_query($mysql,"Insert into `Wiki_Tags` values('', '$PageID', '$tag')");

                        $tagKey = array_search($tag, $originalTags);

                    /*	echo "New tag: ";
                        var_dump($tag);

                        echo "<br />Tag key: ";
                        var_dump($tagKey);
                        */
                        if($tagKey === false)
                        {
                            //echo "<br />Tag update called<hr />";

                            // If the current tag doesn't exist in the original tag array, insert/update it
                            mysqli_query($mysql,"Insert into `Wiki_Tag_Statistics`
                                            values ('', '$tag', '1', '0', NOW(), NOW())

                                            on duplicate key update `count` = `count` + 1, `modified` = NOW()");
                        }
                        else
                        {
                            // Otherwise, remove it from the list
                            unset($originalTags[$tagKey]);
                        }
                    }
                }

                // Take all the remaining original tags and subtract one from the count
                foreach($originalTags as $tag)
                {
                    mysqli_query($mysql,"Update `Wiki_Tag_Statistics`
                                    set `count` = `count` - 1
                                    where `tag`='$tag'");
                }

                $PageEdits = explode(",", $PageEdits);

                if(empty($PageEdits[0]))
                    $PageEdits[0] = $EditID;
                else
                    $PageEdits[] = $EditID;

                $PageEdits = implode(",", $PageEdits);

                $UserEdits = $_SESSION['Edits'];

                if(empty($UserEdits[0]))
                    $UserEdits[0] = $EditID;
                else
                    $UserEdits[] = $EditID;

                $UserEdits = implode(",", $UserEdits);

                mysqli_query($mysql,"UPDATE `Wiki_Pages` SET `Edits`='$PageEdits',`EditTime`='$Time' WHERE `ID`='$PageID'");
                $SQLError .= mysqli_error($mysql);

                mysqli_query($mysql,"UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
                $SQLError .= mysqli_error($mysql);

                if($SQLError)
                    $Content['Body'] .= "Holy SHIT there was a MySQL error";
		else
                {
                    // If the captcha bypass was set by the API
                    if($_SESSION['api'])
                    {
                        unset($_SESSION['api']);
                        unset($_SESSION['bypass']);

                        $_SESSION['status']['authed'] = false;
                        $_SESSION['status']['credits'] = 0;
                    }

                    if($_GET['api'])
                    {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'success', 'session' => $_SESSION['status'], 'message' => 'Page updated!'));
                        exit;
                    }
                    else
                    {
                        $Content['Body'] .= "Page updated... <meta http-equiv='refresh' content='2;url=/$Path'>";
                    }
                }
            }
        }

        $PageTitle = PageTitler($PageTitle);
        $Content['Title'] .= 'Editing: '.FishFormat($PageTitle);


        if((!empty($Form['_Errors'])) || (empty($_POST)) || $Action[0] == "preview")
        {

$Content['Body'] .= <<<SuperNav
<script>
    $(document).ready(function ()
    {

        Window(	"<span class='medium'>Super Edit 3.3 !!!</span><hr />"+
                "<table><tr><td>"+
                "<a href='javascript:Wiki(\"Bold\")'>Bold</a> &emsp; <a href='javascript:Wiki(\"Italics\")'>Italics</a> &emsp; <a href='javascript:Wiki(\"Underline\")'>Underline</a> &emsp; <a href='javascript:Wiki(\"Strike\")'>Strike</a><br />"+
                "<a href='javascript:Wiki(\"Big\")'>Big</a> &emsp; <a href='javascript:Wiki(\"Medium\")'>Medium</a> &emsp; <a href='javascript:Wiki(\"Small\")'>Small</a> &emsp; <a href='javascript:Wiki(\"Rainbow\")'>Rainbow</a>"+
                "</td><td>&emsp;</td><td>"+
                "<a href='javascript:Wiki(\"Internal\")'>Internal Link</a> &emsp; <a href='javascript:Wiki(\"External\")'>External Link</a><br />"+
                "<a href='javascript:Wiki(\"Image\")'>Image</a> &emsp; <a href='javascript:Wiki(\"Video\")'>Video</a> &emsp; <a href='javascript:Wiki(\"Music\")'>Music</a>"+
                "</td></tr></table>");
    });
</script>
SuperNav;

            $Form['_Options'] = "action:".str_replace("//", "/", "/$Path/?")."; id:TheInternet;";

            $Form['Name']['Text'] = "Editor:";
            $Form['Name']['Form'] = "id:Name; name:Name; value:{".$Name."}; maxlength:32;";
            $Form['Name']['SubText'] = "Your name which will appear in the page history.";

            if($LoggedIn)
            {
                if($Account == "on")
                    $Account = "checked";
                elseif($Anonymous == "on")
                    $Anonymous = "checked";

                $Form['Name']['SubText'] = "<input type='checkbox' id='Account' name='Account' $Account onClick='DisableForm(\"Account\")'> Post as Your Account<br /><input type='checkbox' id='Anonymous' name='Anonymous' $Anonymous onClick='DisableForm(\"Anonymous\")'> Post Anonymously";
            }

            $Form['Title']['Text'] = "Title:";
            $Form['Title']['Form'] = "name:Title; value:x{".$PageTitle."}x; maxlength:255;";

            $Form['Content']['Text'] = "Content:";
            $Form['Content']['Form'] = "id:Editbox; name:Content; value:x{".$PageContent."}x; type:textarea;";
            $Form['Content']['Style'] = "width:100%; height:400px";

            $Form['Description']['Text'] = "Description:";
            $Form['Description']['Form'] = "name:Description; value:x{".$Description."}x; size: 80; maxlength:255;";
            $Form['Description']['SubText'] = "Optional; to let other editors know why you made this edit.";

            $Form['tags']['Text'] = "Tags:";
            $Form['tags']['Form'] = "name:tags; value:x{".$tagText."}x; size: 80; maxlength:255;";
            $Form['tags']['SubText'] = "Optional; used for grouping similar pages. Separate tags by commas.";

            if(!$_SESSION['bypass'])
            {
                  $Form['Captcha']['Text'] = "Captcha:";
                  $Form['Captcha']['Form'] = "type:plaintext; name:captcha; value: {<div id='captcha'></div><script type='text/javascript'>captcha();</script>};";
            }

            $Form['Submit']['Form'] = "type:plaintext; value:{<input type='submit' value='Submit' /> <input type='button' value='Preview' onClick='SelectAction(\"preview\")' />};";

            if($Action[0] == "preview")
            {
                $Content['Title'] = 'Preview: '.FishFormat($PageTitle);
                $Content['Body'] .= FishFormat($PageContent)."<div style='clear:both'></div><hr />";
            }
            $Content['Body'] .= Format($Form);
        }

        if($PageEdits)
        {
            $EditCount = count(explode(",", $PageEdits));

            date_default_timezone_set('America/New_York');
            $PageEditTime = formatTime($PageEditTime);

            if($pageViews != 1)
                $viewPlural = 's';

            if($EditCount != 1)
                $Plural = "s";

            $Content['Footer'] = "<b>".number_format($pageViews)."</b> page view{$viewPlural}. <b>$EditCount</b> edit{$Plural}. &mdash; Last modified <b>$PageEditTime</b>.";
        }
    break;

    case "massrevert":
        $Reverted = array();

        //$BadAccount = 250437;
        //$BadAccount = 250481;
        $BadAccount = 250534;

        $PageQuery = mysqli_query($mysql,"SELECT `ID`, `PageID` FROM `Wiki_Edits` WHERE `AccountID`='$BadAccount'");
        while(list($BadEditID, $PageID) = mysqli_fetch_array($PageQuery))
        {

            if(empty($Reverted[$PageID]))
            {
                $DataQuery = mysqli_query($mysql,"SELECT `Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `PageID`='$PageID' AND `AccountID`!='$BadAccount' AND `Archived` = '0' ORDER BY `ID` DESC LIMIT 1");
                list($PageName, $PageDescription, $PageTitle, $PageContent) = mysqli_fetch_array($DataQuery);

                $Time = Time();
                $Size = strlen($PageContent);

                mysqli_query($mysql,"UPDATE `Wiki_Pages` SET `EditTime`='$Time',`Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
                $SQLError .= mysqli_error($mysql);

                //mysqli_query($mysql,"INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$PageName', 'Rachel&#39;s Super Revert: $PageDescription', '$PageTitle', '$PageContent', '')");
                //$SQLError .= mysqli_error($mysql);

                //mysqli_query($mysql,"UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
                //$SQLError .= mysqli_error($mysql);

                mysqli_query($mysql,"UPDATE `Wiki_Edits` SET `Archived` = 1 where `ID` = '$BadEditID'");
                $SQLError .= mysqli_error($mysql);

                $Reverted[$PageID] = TRUE;
            }
            else
            {
                mysqli_query($mysql,"UPDATE `Wiki_Edits` SET `Archived` = 1 where `ID` = '$BadEditID'");
                $SQLError .= mysqli_error($mysql);
            }
        }

        if($SQLError)
            $Content['Body'] = "<b>Holy SHIT there was a MySQL error.</b>";
        else
            $Content['Body'] = "<b>Reverting...</b> <meta http-equiv='refresh' content='2;url=/'>";

    break;

    case "revert":
        $Head = '<meta name="robots" content="noindex, nofollow" />';

        $PageQuery = mysqli_query($mysql,"SELECT `PageID`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$Action[1]'");
        list($PageID, $PageName, $PageDescription, $PageTitle, $PageContent) = mysqli_fetch_array($PageQuery);

        if($PageID and $_SESSION['Verified'] == 1)
        {
            $Time = Time();
            $Size = strlen($PageContent);

            if($Time < $_SESSION['EditTime'] + 15)
            {
                $Content['Body'] = "Please wait 15 seconds between edits.";
                break;
            }

            mysqli_query($mysql,"UPDATE `Wiki_Pages` SET `EditTime`='$Time',`Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
            $SQLError .= mysqli_error($mysql);

            mysqli_query($mysql,"INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$PageName', 'Reverted to: $PageDescription', '$PageTitle', '$PageContent', '')");
            $SQLError .= mysqli_error($mysql);

            mysqli_query($mysql,"UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
            $SQLError .= mysqli_error($mysql);

            if($SQLError)
                $Content['Body'] = "<b>Holy SHIT there was a MySQL error.</b>";
            else
                $Content['Body'] = "<b>Reverting...</b> <meta http-equiv='refresh' content='2;url=/$Path'>";
        }
    break;

    case "register":
        $Head = '<meta name="robots" content="noindex, nofollow" />';
        $Content['UserNav']->Active("Register");
        $Content['Title'] = 'Friendship and happiness?<br />&nbsp;&nbsp;&nbsp;&nbsp;Register today!';

        if(!empty($_POST))
        {
            $Name = Clean($_POST['Name']);
            $Email = Clean($_POST['Email']);
            $Password = Clean($_POST['Password']);
            $Confirm = Clean($_POST['Confirm']);

            if(empty($Name))
                $Form['_Errors']['Name'] = "Error: You must enter a name!";
            elseif(strlen($Name) > 32)
                $Form['_Errors']['Name'] = "Error: Your name is too long.";

            if(empty($Email))
                $Form['_Errors']['Email'] = "Error: You must enter an email!";
            elseif(strlen($Email) > 255)
                $Form['_Errors']['Email'] = "Error: Your email is too long.";

            if(empty($Password))
                $Form['_Errors']['Password'] = "Error: You must enter a password!";
            elseif(strlen($Password) > 32)
                $Form['_Errors']['Password'] = "Error: Your password is too long.";

            if($Confirm != $Password)
                $Form['_Errors']['Password'] = "Error: Your passwords do not match.";

            if(empty($Form['_Errors']))
            {
                $Penis = mysqli_query($mysql,"Select `Name` from `Wiki_Accounts` where `Name`='$Name'");
                list($OldName) = mysqli_fetch_array($Penis);

                if($Name == $OldName)
                    $Form['_Errors']['Name'] = "Someone already has this name! :(";

                list($EmailName, $EmailURL) = explode('@', $Email);

                if($SQLError)
                    $Content['Body'] .= "Holy SHIT there was a MySQL error.";
                else
                    $Content['Body'] .= "You did it! You deserve a friendship bracelet. Now go click on that verification link in your email.<br /><br />Are you super lazy? Opening a new tab too much of a hassle? Here's a link to your email provider! (Hopefully) &mdash; <a href='https://$EmailURL'>$EmailURL</a>";
            }
        }

        if((!empty($Form['_Errors'])) || (empty($_POST)))
        {
            $Form['_Options'] = "action:".str_replace("//", "/", "/$Path/?Register").";";

            $Form['Name']['Text'] = "Name:";
            $Form['Name']['Form'] = "name:Name; value:$Name;";

            $Form['Email']['Text'] = "Email:";
            $Form['Email']['Form'] = "name:Email; value:$Email;";
            $Form['Email']['SubText'] = "&nbsp;";

            $Form['Password']['Text'] = "Password:";
            $Form['Password']['Form'] = "name:Password; type:password;";

            $Form['Confirm']['Text'] = "&nbsp;";
            $Form['Confirm']['Form'] = "name:Confirm; type:password;";
            $Form['Confirm']['SubText'] = "Retype password to make sure you did it right!";

            $Form['Submit']['Form'] = "type:submit; value:Submit;";

            $Content['Body'] .= "<br />";
            $Content['Body'] .= "<div class='big'><div class='big'><div class='big'>FUN FACT THIS PAGE DOES NOT WORK, IT HAS NEVER WORKED, STOP TRYING TO USE IT LOL</div></div>Seriously, just start editing pages. You don't need an account. :)</div>";
            $Content['Body'] .= Format($Form);
        }
    break;

    case "logout":
        $Content['UserNav']->Active("Logout");

        if($_SESSION['Name'])
        {
            session_unset();
            setcookie("sid", "", time() - 2629743, "/", $Site);
            $Content['Body'] = "<b>Logging out...</b> <meta http-equiv='refresh' content='2;url=/$Path'>";
        }
        else
        {
            $Content['Body'] = "<b>Ohh you so silly!</b>";
        }
    break;

    default:
        // If you're on a page and there is no action, view it!
        if(empty($Action) || !is_string($Action[0]))
        {
            $Action[0] = "view";
        }

        $source = $Action[0];
        $function = $Action[0];

        // Check if this action function is already defined (predefined PHP function)
        if(function_exists($Action[0]))
        {
            // Prepend "action_" to the function name instead
            $function = "action_" . $function;
        }

        // Include the code for this action
        include "src/actions/{$source}.php";

        // Call it
        list($Title, $Content) = call_user_func($function, $Path, $Action, $Title, $Content);
    break;
}

$Titles = array('THE BEST INTERENT ON THE INTERNET',
                'Super internet website',
                'Bringing People Together~~',
//				'FREE COOKIES!!!',
//				'FREE CANDY!!!',
//				'Rainbow vagina cupcakes?',
                'Wild MISSINGNO. appeared!',
//				"It's like the future",
                "Wow, so future!",
//				"It's a blast from the past!",
//				"It's a blast from the future!",
//				"We've fucked you by proxy.",
                "Wetfish touches you.");

shuffle($Titles);

$Title[] = $Titles[0];
$Title = implode(" &mdash; ", $Title);
$Site = getenv('SITE_URL');

if(empty($Content['Footer']))
    $Content['Footer'] = '(´ﾟ∀ﾟ)つ Oops! Feet not found. ';

$Content['UserNav'] = $Content['UserNav']->Export();
$Content['PageNav'] = $Content['PageNav']->Export();

if($Content['ExtraNav'])
    $Content['ExtraNav'] = '<div class="extranav">'.$Content['ExtraNav']->Export().'</div>';

include "src/template.php";

?>
