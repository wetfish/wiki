<?php

error_reporting(E_ALL ^ E_NOTICE);
session_start();
require_once('src/config.php');
require('functions.php');
include('src/libraries/simple_html_dom.php');

require('recaptchalib.php');
#require('diff/inline_function.php');
require('fishformat.php');
require('navigation.php');
include('fun/paginate.php');

function PageTitler($Page)
{
	$Titles[] = "Not Titled";
	$Titles[] = "Useless Page";
	$Titles[] = "Worthless Trash";
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
    if($uri['query'])
    {
        parse_str($uri['query'], $query);

        foreach($query as $key => $value)
        {
            $_GET[$key] = $value;
        }
    }
}

// Temporarilly disabling editing pages while backups are restored
//$actions = array('edit', 'preview', 'recent', 'history', 'login', 'register', 'diff', 'source', 'random', 'tag', 'freeze');
$actions = array('recent', 'history', 'login', 'register', 'diff', 'source', 'random', 'tag', 'freeze');
$get = array_change_key_case($_GET);

foreach($_GET as $action => $value)
{
	$actionText = strtolower($action);
	$action = explode('/', $actionText);
		
	if(in_array($action[0], $actions))
		$Action = $action;
}

if(strpos($Path, ' ') !== FALSE)
	die(Redirect(str_replace(' ', '-', $Path), 0));

$Path = trim($Path, "/");

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

if($_SESSION['Name'])
{
	$LoginQuery = mysql_query("SELECT `ID`,`Name`,`Password`,`Verified`,`EditTime` FROM `Wiki_Accounts` WHERE `ID`='{$_SESSION['ID']}'");
	list($ID, $Name, $Password, $Verified, $EditTime) = mysql_fetch_array($LoginQuery);

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
	$LoginQuery = mysql_query("SELECT `ID`,`Verified`,`EditTime` FROM `Wiki_Accounts` WHERE `Name`='{$_SERVER['REMOTE_ADDR']}'");
	list($ID, $Verified, $EditTime) = mysql_fetch_array($LoginQuery);

	if(empty($ID))
	{
		mysql_query("INSERT INTO `Wiki_Accounts` VALUES ('NULL', '{$_SERVER['REMOTE_ADDR']}', '', '', '', '0', '')");
		$ID = mysql_insert_id();
	}

	$_SESSION['ID'] = $ID;
	$_SESSION['Verified'] = $Verified;
	$_SESSION['EditTime'] = $EditTime;

	$Navigation['Login']['URL'] = "/$PathURL/?login";
	$Navigation['Register']['URL'] = "/$PathURL/?register";
}

switch($Action[0])
{
	case "fixtags":
		$tagQuery = mysql_query("Select `tag` from `Wiki_Tags`");
		while(list($tag) = mysql_fetch_array($tagQuery))
		{
			$fixTags[$tag]++;
		}
		
		foreach($fixTags as $tag => $count)
		{
			mysql_query("Insert into `Wiki_Tag_Statistics`
							values ('', '$tag', '1', '0', NOW(), NOW())
							
							on duplicate key update `count` = '$count'");
		}
	break;
	
	case "tag":
		include('src/actions/tagPage.php');
		$Content = tagPage($Path, $Action, $Content);
	break;
	
	case "edit":
	case "preview":
		$Head = '<meta name="robots" content="noindex, nofollow" />';
		$Content['PageNav']->Active("Edit Page");

		if($_SESSION['Verified'] == -1)
		{
			$Content['Body'] = "<b>LOL UR BANNED</b>";
			break;
		}

		$PageQuery = mysql_query("SELECT `ID`,`Title`,`Content`,`Edits`,`Views`, `EditTime` FROM `Wiki_Pages` WHERE `Path`='$Path'");
		list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysql_fetch_array($PageQuery);

		$originalTags = array();

		$tagQuery = mysql_query("Select `tag` from `Wiki_Tags` where `pageID` = '$PageID'");
		while(list($tagName) = mysql_fetch_array($tagQuery))
		{
			$originalTags[] = $tagName;
		}

		$tagText = implode(', ', $originalTags);
		$Name = ($_POST['Name']) ? Clean($_POST['Name']) : $_SESSION['username'];

		if(!empty($_POST))
		{
			$Account = $_POST['Account'];
			$Anonymous = $_POST['Anonymous'];
            $oldPageTitle = $PageTitle;
			$PageTitle = Clean($_POST['Title']);
            $oldPageContent = $PageContent;
            $PageContent = FishFormat(Clean($_POST['Content'], 'textarea'), 'edit');
			$Description = Clean($_POST['Description']);
            $oldTagText = $tagText;
            $tagText = Clean($_POST['tags']);
			$Message = $_POST['Message'];
			$Time = Time();
			$Size = strlen($PageContent);

			if(preg_match("/(ross|fox)/i", $Name) and $Path == 'rossthefox')
				$PageContent = FishFormat($PageContent, 'ross');

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

			if(preg_match("/^(fuck? you captcha?|fuck? captchas?|i hate captchas?|captchas?.*sucks?)$/i", $_POST["recaptcha_response_field"]))
				$_SESSION['bypass'] = true;

			$Resp = recaptcha_check_answer (RECAPTCHA_PRIVATE,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

			if (!$Resp->is_valid and !$_SESSION['bypass'] and $Action[0] != "preview")
				$Form['_Errors']['Captcha'] = "You did it wrong! Please try again.";

			if($Time < $_SESSION['EditTime'] + 15)
				$Form['_Erros']['_Global'] = "Please wait 15 seconds between edits.";

			if(empty($Form['_Errors']) and $Action[0] != "preview")
			{
				$_SESSION['username'] = $Name;
				$newTags = array_filter(array_unique(explode(",", $tagText)));
				$tagCount = count($newTags);				
				
				if($PageID)
				{
					mysql_query("UPDATE `Wiki_Pages` SET `Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
					$SQLError .= mysql_error();

					mysql_query("INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$tagCount', '$Name', '$Description', '$PageTitle', '$PageContent')");
					$SQLError .= mysql_error();

					$EditID = mysql_insert_id();
				}
				else
				{
					mysql_query("INSERT INTO `Wiki_Pages` VALUES ('NULL', '1', '$Path', '$PageTitle', '$PageContent', '', '')");
					$SQLError .= mysql_error();

					$PageID = mysql_insert_id();

					mysql_query("INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$tagCount', '$Name', '$Description', '$PageTitle', '$PageContent')");
					$SQLError .= mysql_error();

					$EditID = mysql_insert_id();
				}
				
				mysql_query("Delete from `Wiki_Tags` where `pageID`='$PageID'");
				
				foreach($newTags as $tag)
				{
					$tag = trim($tag);
					
					if($tag)
					{
						$tag = str_replace(" ", "-", $tag);
						mysql_query("Insert into `Wiki_Tags` values('', '$PageID', '$tag')");
						
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
							mysql_query("Insert into `Wiki_Tag_Statistics`
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
					mysql_query("Update `Wiki_Tag_Statistics`
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

				mysql_query("UPDATE `Wiki_Pages` SET `Edits`='$PageEdits',`EditTime`='$Time' WHERE `ID`='$PageID'");
				$SQLError .= mysql_error();

				mysql_query("UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
				$SQLError .= mysql_error();

				if($SQLError)
					$Content['Body'] .= "Holy SHIT there was a MySQL error.";
				else
					$Content['Body'] .= "Page updated... <meta http-equiv='refresh' content='2;url=/$Path'>";
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
		
		Window(	"<span class='medium'>Super Edit 3.1 !!!</span><hr />"+
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

			$Form['Name']['Text'] = "Name:";
			$Form['Name']['Form'] = "id:Name; name:Name; value:{".$Name."}; maxlength:32;";

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
				$Form['Captcha']['Form'] = "type:plaintext; value:".recaptcha_get_html(RECAPTCHA_PUBLIC);

			$Form['Submit']['Form'] = "type:plaintext; value:{<input type='submit' value='Submit' /> <input type='button' value='Preview' onClick='SelectAction(\"preview\")' />};";
			
			if($Action[0] == "preview")
			{
				$Content['Title'] = 'Preview: '.FishFormat($PageTitle);
				$Content['Body'] .= FishFormat($PageContent)."<hr />";
			}
			$Content['Body'] .= Format($Form, Form);
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
	
	case "random":
		if($Path)
		{
			$PageQuery = mysql_query("SELECT `ID`,`Title`,`Content`,`Edits`,`Views`,`EditTime` FROM `Wiki_Pages` WHERE `Path` = '$Path'");
			list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysql_fetch_array($PageQuery);

		
			$pagePrevious = RandomRow('Wiki_Pages', 'ID');
			$pageNext = RandomRow('Wiki_Pages', 'ID');
			
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

			if($previous['Path'])
				$previous['Path'] = "/{$previous['Path']}/?random";
			else
				$previous['Path'] = "/?random";

			if($next['Path'])
				$next['Path'] = "/{$next['Path']}/?random";
			else
				$next['Path'] = "/?random";

			$Title[] = FishFormat($PageTitle, "strip");
			$Content['Title'] .= "<a href='{$previous['Path']}' title='Previous - {$previous['Title']}'>&#8668;</a> ".FishFormat($PageTitle)." <a href='{$next['Path']}' title='Next - {$next['Title']}'>&#8669;</a>";
			$Content['Body'] .= FishFormat($PageContent);
			$Content['Tags'] = $tagLinks;
			
			$Content['Body'] .= <<<JavaScript
	
	<script>
		$(document).ready(function ()
		{
			$('body').on('keydown', function(event)
			{
				event.stopImmediatePropagation()
				
				if(event.keyCode == 37) // Previous
					history.back();
				else if(event.keyCode == 39) // Next
					location.href = '{$next['Path']}';

					
	//			console.log(event);
			});
		});
	</script>
	
JavaScript;

			if($PageEdits)
			{
				$EditCount = count(explode(",", $PageEdits));
				
				date_default_timezone_set('America/New_York');
				$PageEditTime = formatTime($PageEditTime);
                
				if($pageViews != 1)
					$viewPlural = 's';

				if($EditCount != 1)
					$Plural = "s";

				$Content['Tags'] = $tagLinks;
				$Content['Footer'] = "<b>".number_format($pageViews)."</b> page view{$viewPlural}. <b>$EditCount</b> edit{$Plural} &ensp;&mdash;&ensp; Last modified <b>$PageEditTime</b>.";
	//			$Content['Footer'] = "This page has been edited <b>$EditCount</b> time{$Plural}, and was last edited on $PageEditTime.";
			}
		
		}
		else
		{
			$Head = '<meta name="robots" content="noindex, nofollow" />';
			$Random = RandomRow('Wiki_Pages', 'ID');			
			$ID = uuid();

			$randomTitles = array('Wormhole open: ', 'An adventure!', 'Welcome to', 'Internet Space Award', 'Friendship served', 'WOW!');
			$randomPhrases = array('Hold on to your hat!', 'Hold on to your butt!!', 'I love butts', 'Wet, fish', 'I LOVE ANIME!!!!!!!!', 'COOL!');
			
			shuffle($randomTitles);
			shuffle($randomPhrases);
			
			$Content['Title'] = FishFormat("rainbow[{$randomTitles[0]}] {$Random['Title']}");		
			$Content['Body'] = FishFormat("redirect[ {$Random['Path']}/?random ] ad[right] big,big,big,big[{$randomPhrases[0]}] ");
		}
	break;
	
	case "history":
		include('src/actions/history.php');
		$Content = history($Path, $Action, $Content);
	break;

	case "source":
		$Head = '<meta name="robots" content="noindex, nofollow" />';
		$Content['PageNav']->Active("Page History");
	
		if(is_numeric($Action[1]))
		{
			$PageQuery = mysql_query("SELECT `AccountID`,`EditTime`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$Action[1]'");
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

			$Content['Title'] = "Viewing Source of: $PageTitle";
			$Content['Body'] .= Format($Form, Form);

			date_default_timezone_set('America/New_York');
//			$PageEditTime = date("F j\, Y G:i:s", $PageEditTime)." EST";
			$PageEditTime = formatTime($PageEditTime);
			$Content['Footer'] = "This page is an old revision made by <b><a href='/names?id=$AccountID'>$PageName</a></b> on $PageEditTime.";
		}
	break;
	
	case "diff":
		$Head = '<meta name="robots" content="noindex, nofollow" />';
		$Content['PageNav']->Active("Page History");
		
		if(is_numeric($Action[1]))
		{
			$PageQuery = mysql_query("SELECT `PageID`,`AccountID`,`EditTime`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$Action[1]'");
			list($PageID, $AccountID, $PageEditTime, $PageName, $PageDescription, $PageTitle, $PageContent) = mysql_fetch_array($PageQuery);
		
			$PreviousQuery = mysql_query("Select `Content` from `Wiki_Edits` where `ID` < '$Action[1]' and `PageID`='$PageID' order by `ID` desc limit 1");
			list($PreviousContent) = mysql_fetch_array($PreviousQuery);
			
			$Title[] = FishFormat($PageTitle, "strip");			
			$Content['Title'] .= FishFormat($PageTitle);
			
			$nl = '#**!)@<>#';

			$PreviousContent = str_replace("\n", "<br>", $PreviousContent);
			$PageContent = str_replace("\n", "<br>", $PageContent);
			
			ob_start();
			inline_diff($PreviousContent, $PageContent, $nl);
			$Content['Body'] .= ob_get_contents();
			ob_end_clean();
			
			date_default_timezone_set('America/New_York');
			$PageEditTime = formatTime($PageEditTime);
			$Content['Footer'] = "This page is an old revision made by <b><a href='/names?id=$AccountID'>$PageName</a></b> on $PageEditTime.";

			if($PageDescription)
				$Content['Footer'] .= "<br />'$PageDescription'";
		}
	break;
	
	case "Massrevert":
		$Reverted = array();

		$BadAccount = 27713;
		
		$PageQuery = mysql_query("SELECT `PageID` FROM `Wiki_Edits` WHERE `AccountID`='$BadAccount'");
		while(list($PageID) = mysql_fetch_array($PageQuery))
		{
			if(empty($Reverted[$PageID]))
			{
				$DataQuery = mysql_query("SELECT `Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `PageID`='$PageID' AND `AccountID`!='$BadAccount' ORDER BY `ID` DESC LIMIT 1");
				list($PageName, $PageDescription, $PageTitle, $PageContent) = mysql_fetch_array($DataQuery);

				$Time = Time();
				$Size = strlen($PageContent);

				mysql_query("UPDATE `Wiki_Pages` SET `EditTime`='$Time',`Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
				$SQLError .= mysql_error();

				mysql_query("INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$PageName', 'Rachel&#39;s Super Revert: $PageDescription', '$PageTitle', '$PageContent')");
				$SQLError .= mysql_error();

				mysql_query("UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
				$SQLError .= mysql_error();

				$Reverted[$PageID] = TRUE;
			}
		}

		if($SQLError)
			$Content['Body'] = "<b>Holy SHIT there was a MySQL error.</b>";
		else
			$Content['Body'] = "<b>Reverting...</b> <meta http-equiv='refresh' content='2;url=/'>";

	break;

	case "revert":
		$Head = '<meta name="robots" content="noindex, nofollow" />';

		$PageQuery = mysql_query("SELECT `PageID`,`Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `ID`='$Action[1]'");
		list($PageID, $PageName, $PageDescription, $PageTitle, $PageContent) = mysql_fetch_array($PageQuery);

		if($PageID and $_SESSION['Verified'] == 1)
		{
			$Time = Time();
			$Size = strlen($PageContent);

			if($Time < $_SESSION['EditTime'] + 15)
			{
				$Content['Body'] = "Please wait 15 seconds between edits.";
				break;
			}

			mysql_query("UPDATE `Wiki_Pages` SET `EditTime`='$Time',`Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
			$SQLError .= mysql_error();

			mysql_query("INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$PageName', 'Reverted to: $PageDescription', '$PageTitle', '$PageContent')");
			$SQLError .= mysql_error();

			mysql_query("UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
			$SQLError .= mysql_error();

			if($SQLError)
				$Content['Body'] = "<b>Holy SHIT there was a MySQL error.</b>";
			else
				$Content['Body'] = "<b>Reverting...</b> <meta http-equiv='refresh' content='2;url=/$Path'>";
		}
	break;

	case "recent":
		$Head = '<meta name="robots" content="noindex, nofollow" />';
		$Content['UserNav']->Active("Recent Activity");

		if(empty($_SESSION['Recent']))
		{
			$_SESSION['Recent']['Active'][0] = "All Edits";
			$_SESSION['Recent']['Active'][3] = "Descending";
			$_SESSION['Recent']['Order'] = "DESC";
		}

		$Content['ExtraNav'] = new Navigation;
		$Content['ExtraNav']->Add("All Edits", "?recent/all");
		$Content['ExtraNav']->Add("Most Recent Edit", "?recent/unique");
		$Content['ExtraNav']->Add("Ascending", "?recent/asc");
		$Content['ExtraNav']->Add("Descending", "?recent/desc");
		$Content['ExtraNav']->Active($_SESSION['Recent']['Active']);

		$Template['Title'] = "View:";
		$Content['ExtraNav']->Template($Template);

		if(empty($Action[1]))
		{
			$Content['Title'] = "Recent Activity";

			$ActivityQuery = "SELECT $Unique `ID`,`PageID`,`AccountID`,`EditTime`,`Size`,`Tags`,`Name`,`Description`,`Title` FROM `Wiki_Edits` ORDER BY `ID` {$_SESSION['Recent']['Order']}";
			list($QueryData, $Links) = Paginate($ActivityQuery, 50, $_GET['page'], $_SERVER['QUERY_STRING']);
			
			$Content['Body'] .= "<center class='page-navigation'>$Links</center>";
			$Content['Body'] .= "<table width='100%' class='history'>";
			$Content['Body'] .= "<tr><td><b>Revision</b></td><td><b>Size</b></td><td><b>Tags</b></td><td><b>Editor</b></td><td style='min-width:200px;'><b>Title</b></td><td><b>Description</b></td></tr>";
			
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
					
					$Content['Body'] .= "<tr $Class><td style='min-width:{$minWidth}px;'>$PageTime</td><td>$PageSize</td><td>$pageTags</td><td><b><a href='/names?id=$AccountID'>$PageName</a></b></td><td style='max-width:400px'><span style='float:right;'><a href='$DiffURL' rel='nofollow'>d</a></span><b><a href='/$PagePath'>$PageTitle</a></b></td><td class='multi-line'>$PageDescription</td></tr>";
				}
			}

			$Content['Body'] .= "</table>";
			$Content['Body'] .= "<center class='page-navigation bottom'>$Links</center>";			
		}
		elseif($Action[1] == "page")
		{

		}
		else
		{
			switch($Action[1])
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

			$Content['Body'] = "<b>Settings changed...</b> <meta http-equiv='refresh' content='2;url=".FormatPath("/$Path/?recent")."'>";
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
			
			$Resp = recaptcha_check_answer (RECAPTCHA_PRIVATE,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

			if (!$Resp->is_valid)
				$Form['_Errors']['Captcha'] = "You did it wrong! Please try again.";

			if(empty($Form['_Errors']))
			{
				$Penis = mysql_query("Select `Name` from `Wiki_Accounts` where `Name`='$Name'");
				list($OldName) = mysql_fetch_array($Penis);
				
				if($Name == $OldName)
					$Form['_Errors']['Name'] = "Someone already has this name! :(";
				
				list($EmailName, $EmailURL) = explode('@', $Email);
				
				if($SQLError)
					$Content['Body'] .= "Holy SHIT there was a MySQL error.";
				else
					$Content['Body'] .= "You did it! You deserve a friendship bracelet. Now go click on that verification link in your email.<br /><br />Are you super lazy? Opening a new tab too much of a hassle? Here's a link to your email provider! (Hopefully) &mdash; <a href='http://$EmailURL'>$EmailURL</a>";
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
			
			$Form['Captcha']['Form'] = "type:plaintext; value:".recaptcha_get_html(RECAPTCHA_PUBLIC);

			$Form['Submit']['Form'] = "type:submit; value:Submit;";

			$Content['Body'] .= "<br />";
			$Content['Body'] .= "<div class='big'><div class='big'><div class='big'>FUN FACT THIS PAGE DOES NOT WORK, IT HAS NEVER WORKED, STOP TRYING TO USE IT LOL</div></div>Seriously, just start editing pages. You don't need an account. :)</div>";
			$Content['Body'] .= Format($Form, Form);
		}
	break;

	case "login":
		$Content['UserNav']->Active("Login");
		$Content['Title'] = "Super Secret Login Form";

		if($_POST)
		{
			if($_POST['Password'] == LOGIN_PASSWORD)
			{
				$_SESSION['bypass'] = true;
				$Content['Body'] = "YOU DID IT FRIEND!!<br /><br />You will now be brought back to your previous page.";
				$Content['Body'] .= Redirect(str_replace("//", "/", "/$Path"));
			}
		}
		
		if(empty($_SESSION['bypass']))
		{
			$Content['Body'] .= "Protip: The super secret password is the same as the PIBDGAF ichc password.";
			
			$Form['_Options'] = "action:".str_replace("//", "/", "/$Path/?login").";";
			$Form['Password']['Text'] = "Password:";
			$Form['Password']['Form'] = "name:Password; type:password;";
			$Form['Submit']['Form'] = "type:submit; value:Submit;";
			
			$Content['Body'] .= Format($Form, Form);
		}

#			$_SESSION['ID'] = $ID;
	break;

	case "logout":
		$Content['UserNav']->Active("Logout");

		if($_SESSION['Name'])
		{
			session_unset();
			setcookie("sid", "", time() - 2629743, "/", ".wetfish.net");
			$Content['Body'] = "<b>Logging out...</b> <meta http-equiv='refresh' content='2;url=/$Path'>";
		}
		else
		{
			$Content['Body'] = "<b>Ohh you so silly!</b>";
		}
	break;

	case "freeze":
		if($_SESSION['Background'])
			unset($_SESSION['Background']);
		else
			$_SESSION['Background'] = "Frozen";

		$Content['Body'] = "<b>Background updated...</b> <meta http-equiv='refresh' content='2;url=/$Path'>";
	break;

	default:
		$Content['PageNav']->Active("View Page");

		$PageQuery = mysql_query("SELECT `ID`,`Title`,`Content`,`Edits`,`Views`,`EditTime` FROM `Wiki_Pages` WHERE `Path`='$Path'");
		list($PageID, $PageTitle, $PageContent, $PageEdits, $pageViews, $PageEditTime) = mysql_fetch_array($PageQuery);
		
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
								 "Would you like to be the first? {{{$Path}/?edit|All it takes is a click.}}",
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

		$Title[] = FishFormat($PageTitle, "strip");
		$Content['Title'] .= FishFormat($PageTitle);
		$Content['Body'] .= FishFormat($PageContent);

		if($PageEdits)
		{
			$EditCount = count(explode(",", $PageEdits));
			
			date_default_timezone_set('America/New_York');
			$PageEditTime = formatTime($PageEditTime);

			if($pageViews != 1)
				$viewPlural = 's';

			if($EditCount != 1)
				$Plural = "s";

			$Content['Tags'] = $tagLinks;
			$Content['Footer'] = "<b>".number_format($pageViews)."</b> page view{$viewPlural}. <b>$EditCount</b> edit{$Plural} &ensp;&mdash;&ensp; Last modified <b>$PageEditTime</b>.";
//			$Content['Footer'] = "This page has been edited <b>$EditCount</b> time{$Plural}, and was last edited on $PageEditTime.";
		}
	break;
}


$Titles = array('THE BEST INTERENT ON THE INTERNET',
				'Bringing People Together~~',
//				'FREE COOKIES!!!',
//				'FREE CANDY!!!',
				'Rainbow vagina cupcakes?',
				'Wild MISSINGNO. appeared!',
//				"It's like the future",
				"It IS the future!",
//				"It's a blast from the past!",
				"It's a blast from the future!",
				"We've fucked you by proxy.",
				"Wetfish touches you.");

shuffle($Titles);
				
$Title[] = $Titles[0];
$Title = implode(" &mdash; ", $Title);
$Title = str_replace(array('{', '}'), '', $Title);

if(empty($Content['Footer']))
	$Content['Footer'] = '(´ﾟ∀ﾟ)つ Oops! Feet not found. ';

$Content['UserNav'] = $Content['UserNav']->Export();
$Content['PageNav'] = $Content['PageNav']->Export();

if($_SESSION['Background'] == "Frozen")
	$Freeze = "<span class='small'><a href='".FormatPath("/$Path/?freeze")."'>Refresh Background</a></span>";
else
	$Freeze = "<span class='small'><a href='".FormatPath("/$Path/?freeze")."'>Freeze Background</a></span>";

if($Content['ExtraNav'])
	$Content['ExtraNav'] = '<div class="extranav">'.$Content['ExtraNav']->Export().'</div>';
/*
*/

if($_SESSION['Background'] == "Frozen")
{
    $noSwimStart = "/*";
    $noSwimEnd = "*/";
}

$HTML = <<<HTML
<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>$Title</title>

		$Head
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="google-site-verification" content="lj_UCeIzlK8MDZyzJ-73XUUZHgroWS_1kQ6kkNar0Vg" />
		<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
		<link href="/style.php" rel="stylesheet" type="text/css" />
		<link href="/colorbox.css" rel="stylesheet" type="text/css" />
		<!--[if IE]>
				<link href="/styleie.css" rel="stylesheet" type="text/css" media="screen" />
		<![endif]-->

		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/jquery.colorbox.js"></script>
		<script type="text/javascript" src="/jquery.json-2.2.min.js"></script>
		<!-- <script type="text/javascript" src="http://www.cornify.com/js/cornify.js"></script>  -->
		
		<script type="text/javascript" src="/jquery-fieldselection.js"></script>
		<script type="text/javascript" src="/window.js"></script>
		<script src='/src/js/wetki.js'></script>
		<!-- <script src='/src/js/jquery.transit.js'></script> -->
		<link href="/window.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript">
			function Wiki(Type)
			{
				var Output = new Object;
			
				switch(Type)
				{
					case "Bold":
						Output.start = " b {";
						Output.end = "} ";
					break;
					
					case "Italics":
						Output.start = " i {";
						Output.end = "} ";
					break;
					
					case "Underline":
						Output.start = " u {";
						Output.end = "} ";
					break;
					
					case "Strike":
						Output.start = " s {";
						Output.end = "} ";
					break;
					
					case "Big":
						Output.start = " big {";
						Output.end = "} ";
					break;
					
					case "Medium":
						Output.start = " med {";
						Output.end = "} ";
					break;
					
					case "Small":
						Output.start = " small {";
						Output.end = "} ";
					break;
					
					case "Rainbow":
						Output.start = " rainbow {";
						Output.end = "} ";
					break;
					
					case "Internal":
						Output.start = "{{";
						Output.end = "}}";
					break;
					
					case "External":
						Output.start = " url {";
						Output.end = "} ";
					break;
					
					case "Image":
						Output.start = " image {";
						Output.end = "} ";
					break;
					
					case "Video":
						Output.start = " video {";
						Output.end = "} ";
					break;
					
					case "Music":
						Output.start = " music {";
						Output.end = "} ";
					break;
				}
				
				var Text = $('#Editbox').getSelection().text;
				$('#Editbox').replaceSelection(Output.start + Text + Output.end);
			}
		
			function Jump(URL)
			{
				if(URL == undefined)
					URL = 'http://wiki.wetfish.net/';
				
				window.location.href = URL; 
			}

			function SuperJump(URL)
			{
				if(URL == undefined)
					URL = 'http://wiki.wetfish.net/';
				
				window.open(URL, '_blank'); 
			}
			
			function SelectAction(Type)
			{
				var Form = document.getElementById('TheInternet');
				Form.action = Form.action + Type;
				Form.submit();
			}
			
			window.onload = function(){
				$('#TheInternet').submit(function() {
					var Form = document.getElementById('TheInternet');
					Form.action = Form.action + 'edit';
				});
			}			
			
			var RecaptchaOptions = {
				theme : 'blackglass'
			};
		</script>
		
		<script>
			var swimUp = 30;
			var swimDown = 50;
						
			$(document).ready(function()
			{
//				$('body').append("<img src='/src/img/coolfish.png' id='kristyfish'>");
//				$('body').append("<img src='http://glowbug.me/unicornkingdom/coolfish_fuckyah.png' id='kristyfish'>");
				$('body').append("<img src='http://wiki.wetfish.net/upload/52a357b9-3680-9030-34ed-fc68895773c1.png' id='kristyfish'>");


                $noSwimStart
				$('#kristyfish').load(function()
				{
					$('#kristyfish').animate({'left': $(window).width(), 'top': Math.random() * $('body').height()}, function() { swim(); });

				});
				
				function swim()
				{
					if(parseInt($('#kristyfish').css('left')) > $(window).width() || parseInt($('#kristyfish').css('left')) < - $('#kristyfish').width())
					{
						//$('#kristyfish').transition({rotateY: '+=180deg', 'top': Math.random() * $('body').height()});
						$('#kristyfish').animate({rotateY: '+=180deg', 'top': Math.random() * $('body').height()});
						
						swimUp = swimUp * -1;
						swimDown = swimDown * -1;
					}
					
					$('#kristyfish').animate({'rotate': '+=10deg', 'left': parseInt($('#kristyfish').css('left')) + swimUp}, function()
					{
						$('#kristyfish').animate({'rotate': '-=10deg', 'left': parseInt($('#kristyfish').css('left')) + swimDown}, function()
						{
							swim();
						});
					});
			
					
				}
                $noSwimEnd
				

/*				$('#kristyfish').transition({'rotate': '-=5deg', 'top': Math.random() * $('body').height()}, function()
				{
					swim();
				});
*/				
//					$('#kristyfish').animate({'left': $(window).width()}, 8877, 'linear');					
			});
		</script>

		<link rel="icon" type="image/png" href="/favzz.png"/>
	</head>
	<body>
		<div class="bodyborder">
			<div class="body">
				<div class="header">
					$Freeze
		
					<a class='header' href='http://wiki.wetfish.net/' onclick='cornify_add(); setTimeout("Jump()", 5000); return false;'><img src='/thisiswetfish.png' border='0'></a>
				</div>

				<div class="navigation">
					<div class="navbox" onClick="Jump('http://wiki.wetfish.net/search')">
						<a class="nav exempt" href="http://wiki.wetfish.net/search">Search</a>
					</div>
					
					<div class="navbox" onClick="Jump('http://wiki.wetfish.net/browse')">
						<a class="nav exempt" href="http://wiki.wetfish.net/browse">Browse</a>
					</div>
					
					<div class="navbox" onClick="Jump('http://wiki.wetfish.net/?random')">
						<a class="nav exempt" href="http://wiki.wetfish.net/?random">Random</a>
					</div>

					<div class="navbox" onClick="SuperJump('http://wiki.wetfish.net/chat/')">
						<a class="nav exempt" href="http://wiki.wetfish.net/chat/" onClick="return false" target="_blank">Chat</a>
					</div>

<!--					<div class="navbox" onClick="Jump('http://music.wetfish.net/')">
						<a class="nav exempt" href="http://music.wetfish.net/">Playlists</a>
					</div>
-->
					<div class='navbox' onClick="Jump('/popular')">
						<a class='nav exempt' href='/popular'>Popular</a>
					</div>

					<div class='navbox' onClick="Jump('/tags')">
						<a class='nav exempt' href='/tags'>Tags</a>
					</div>
					
					<div class="navbox" onClick="Jump('http://wiki.wetfish.net/qpalz/')">
						<a class="nav exempt" href="http://wiki.wetfish.net/qpalz/">Media</a>
					</div>
				</div>

				<div class="subnav">
					<hr />
					<div class="usernav">
						{$Content['UserNav']}
					</div>

					<div class="pagenav">
						{$Content['PageNav']}
					</div>
					<hr />
				</div>

				{$Content['ExtraNav']}

				<div class="title">{$Content['Title']}</div>
				
				<br />
				
				<div class="content">{$Content['Body']}</div>

				<div style='clear:both;'></div>
				
				{$Content['Tags']}
				
				<hr />
				<center><iframe id='leader-friend' src='http://ads.wetfish.net/friendship/leader.html' style='width:750px; height:115px; border:0; outline:0; overflow:hidden;' scrolling="no"></iframe></center>
			</div>
		</div>

		<div class="footer">
			<div class="footerborder">
				<div class="footerbox">
					{$Content['Footer']}
				</div>
			</div>
		</div>		
	</body>
</html>
HTML;

echo $HTML;

?>
