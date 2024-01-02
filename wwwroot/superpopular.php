<?php

require('functions.php');
include('src/connection.php');
$PeopleQuery = mysqli_query($mysql,"Select AccountID,count(*) as n
                            from Wiki_Edits
                            group by AccountID
                            order by n desc
                            limit 25");

while(list($AccountID, $Count) = mysqli_fetch_array($PeopleQuery))
{
    $AccountCount++;
    
    $AccountQuery = mysqli_query($mysql,"Select `Name`, `EditTime` from `Wiki_Accounts` where `ID`='$AccountID'");
    list($AccountName, $EditTime) = mysqli_fetch_array($AccountQuery);
    
    $AccountName = gethostbyaddr($AccountName);
    $AccountName = preg_replace('/\d+-\d+-\d+-\d+/', substr(md5(hash('whirlpool', $AccountName)), 0, 8), $AccountName);
    
    date_default_timezone_set('America/New_York');
    $EditTime = date("F j\, Y G:i:s", $EditTime)." EST";
    
    $AccountTable .= "<tr>
                        <td>$AccountCount</td>
                        <td><a href='names?id=$AccountID'>$AccountName</a></td>
                        <td>$Count</td>
                        <td>$EditTime</td>
                    </tr>";
}

?>

<div>
    <span class='medium'>Coolest Hosts</span>

    <table>
        <tr style='font-weight:bold'>
            <td>&nbsp;</td>
            <td>Cool Hostname&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>Edits&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>Last Edit</td>
        </tr>
        <?php echo $AccountTable ?>
    </table>
</div>
