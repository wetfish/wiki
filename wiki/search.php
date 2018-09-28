<form>
    <input type='text' name='q' value='<?php echo $_GET['q'] ?>' /> <input type='submit' value='Go!' />
</form>

<?php

require('functions.php');
include('fun/paginate.php');
$Search = Clean($_GET['q']);

if($Search)
{
    $Query = "Select `Path`, `Title`, `Content`
                from `Wiki_Pages`
                where match(`Path`, `Title`, `Content`)
                against('$Search')";
    
    $Results = mysql_num_rows(mysql_query($Query));
    $Time = time();

    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        $userIP = $_SERVER['REMOTE_ADDR'];

    // Make sure the user IP is sanitized
    $userIP = preg_replace('/[^0-9.]/', '', $userIP);
    
    list($Data, $Links) = Paginate($Query, 25, $_GET['page'], $_SERVER['QUERY_STRING']);

    echo "<hr /><center>$Links</center><hr />";
    
    if($Data)
    {
        foreach($Data as $Result)
        {
            list($Path, $Title, $Content) = $Result;
            
            echo "<a href='/$Path' rel='nofollow' style='font-weight:bold'>$Title</a><br />";
            echo substr($Content, 0, 255);
            echo "<hr />";
            $Count++;
        }
    }
    
    if(empty($Count))
        echo "<br /><b>Looks like your search didn't turn up anything.<br />Your query might be too short, too common, or maybe it's really not here.</b>";
    else
    {
        $TimeQuery = mysql_query("Select `Time`
                                    from `Wiki Searches`
                                    where `IP`='$userIP' and `Search`='$Search'
                                    order by `ID` desc");

        list($OldTime) = mysql_fetch_array($TimeQuery);

        if($OldTime + 86400 < $Time)
            mysql_query("Insert into `Wiki Searches` values ('', '$Time', '$Results', '$Search', '$userIP')");
    }
    
    echo "<center>$Links</center>";	
}
else
{
    $Query = "Select `Time`, `Results`, `Search`
                from `Wiki Searches`
                order by `ID` desc";
    
//	echo "<hr />";
    
    list($Data, $Links) = Paginate($Query, 25, $_GET['page']);

    echo "<center class='page-navigation'>$Links</center>";
    
    foreach($Data as $Result)
    {
        list($Time, $Results, $Search) = $Result;
        
        date_default_timezone_set('America/New_York');
        $Time = date("F j\, Y G:i:s", $Time)." EST";

        $TableContents .= "<tr><td><a href='search?q=$Search'>$Search</a></td><td>$Results</td><td>$Time</td></tr>\n";
    }
        
    echo "	<table>
                <tr style='font-weight:bold'><td>Search</td><td>Results</td><td>Time</td></tr>
                $TableContents
            </table>";
            
    echo "<center class='page-navigation bottom'>$Links</center>";
}

?>
