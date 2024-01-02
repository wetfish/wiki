<?php

require_once('src/config.php');
include('functions.php');

if($_POST)
{
    if(is_numeric($_POST['id']) and $_POST['password'] == BAN_PASSWORD)
    {
        $ID = $_POST['id'];
        mysqli_query($mysql,"Update `Wiki_Accounts` set `Verified`='-1' where `ID`='$ID'");
        echo "User banned.<br />";
        
        if($_POST['revert'] == "on")
        {
            $Reverted = array();
            $BadAccount = $ID;
            
            $PageQuery = mysqli_query($mysql,"SELECT `PageID` FROM `Wiki_Edits` WHERE `AccountID`='$BadAccount'");
            while(list($PageID) = mysqli_fetch_array($PageQuery))
            {
                if(empty($Reverted[$PageID]))
                {
                    $DataQuery = mysqli_query($mysql,"SELECT `Name`,`Description`,`Title`,`Content` FROM `Wiki_Edits` WHERE `PageID`='$PageID' AND `AccountID`!='$BadAccount' ORDER BY `ID` DESC LIMIT 1");
                    list($PageName, $PageDescription, $PageTitle, $PageContent) = mysqli_fetch_array($DataQuery);

                    $Time = Time();
                    $Size = strlen($PageContent);

                    mysqli_query($mysql,"UPDATE `Wiki_Pages` SET `EditTime`='$Time',`Title`='$PageTitle',`Content`='$PageContent' WHERE `ID`='$PageID'");
                    $SQLError .= mysqli_error($mysql);

                    mysqli_query($mysql,"INSERT INTO `Wiki_Edits` VALUES ('NULL', '$PageID', '{$_SESSION['ID']}', '$Time', '$Size', '$PageName', 'Rachel&#39;s Super Revert: $PageDescription', '$PageTitle', '$PageContent', '')");
                    $SQLError .= mysqli_error($mysql);

                    mysqli_query($mysql,"UPDATE `Wiki_Accounts` SET `EditTime`='$Time' WHERE `ID`='{$_SESSION['ID']}'");
                    $SQLError .= mysqli_error($mysql);

                    $Reverted[$PageID] = TRUE;
                }
            }

            $Count = $Reverted[$PageID];
            
            if($SQLError)
                echo "<b>Holy SHIT there was a MySQL error.</b>";
            else
                echo "$Count pages reverted.";
        }
        
        echo "<hr />";
    }
}

?>

<h1>Who deserves it this time?</h1>

<form method='post'>
    User ID <input name='id' type='text' /><br />
    Password <input name='password' type='password' /><br />
    Revert <input name='revert' type='checkbox' /><br />
    <input type='submit' value='Kablam!!' />
</form>
