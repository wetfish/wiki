
<?php

// Lonely pages :(
//									   Count(tag.`tagID`) as tagCount,
//										 `Wiki_Tags` as tag,
//tag.`pageID` = page.`ID`
//		   
                        //			 `Wiki_Edits` as edit
//										edit.`PageID` = page.`ID`
                        

include('../../functions.php');
include('../mysql.php');
include('../markup/fishformat.php');
include('../../fun/paginate.php');


$lonelyQuery = "Select page.`ID`, page.`Views`, page.`Path`, page.`Title`, page.`EditTime`,
                       Count(distinct edit.`ID`) as editCount,
                       Count(distinct tag.`tagID`) as tagCount
                    from `Wiki_Pages` as page
                    left join `Wiki_Edits` as edit on page.`ID` = edit.`PageID`
                    left join `Wiki_Tags` as tag on page.`ID` = tag.`pageID`
                    group by page.`ID`
                    order by tagCount, editCount,  page.`EditTime`, page.`Views`";

list($lonelyPages, $navigation) = Paginate($lonelyQuery, 50, $_GET['page'], $_SERVER['QUERY_STRING']);

if($lonelyPages)
{
    echo "<center class='page-navigation'>$navigation</center>";

    ?>
    <table width='100%' class='history'>
        <tr>
            <td><b>Date Modified</b></td>
            <td><b>Edits</b></td>		
            <td><b>Views</b></td>
            <td><b>Tags</b></td>
            <td><b>Title</b></td>
        </tr>
        <?php
        
        foreach($lonelyPages as $result)
        {
            $toggle++;		
            $dateModified = date("F j\, Y G:i:s", $result['EditTime'])." EST";

            if($toggle % 2 == 1)
                $class = "class='toggle'";
            else
                $class = '';

            ?>
                <tr <?php echo $class ?>>
                    <td><?php echo $dateModified ?></td>
                    <td><?php echo $result['editCount'] ?></td>
                    <td><?php echo $result['Views'] ?></td>
                    <td><?php echo $result['tagCount'] ?></td>
                    <td style='max-width:400px;'><b><a href='/<?php echo $result['Path'] ?>' rel='nofollow'><?php echo FishFormat($result['Title'], "strip"); ?></a></b></td>
                </tr>
                
            <?php
        }
        ?>
        </table>
        <?php
    
    echo "<center class='page-navigation bottom'>$navigation</center>";
}
else
{
    echo "<b>Oops!!</b>";	
}

?>
