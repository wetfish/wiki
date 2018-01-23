<?php

require('functions.php');


$viewsQuery = mysql_query("Select `Path`,`Title`,`Views`
                            from Wiki_Pages
                            order by `Views` desc
                            limit 40");

while(list($path, $title, $views) = mysql_fetch_array($viewsQuery))
{
    $viewsCount++;
    ob_start();

    ?>
        <tr>
            <td><?php echo $viewsCount; ?></td>
            <td class='littleborder' style='max-width:175px'>
                <a href='/<?php echo $path; ?>'><?php echo $title; ?></a>
            </td>
            <td class='littleborder' style='color:#FCA5BD'>
               <?php echo $views; ?>
            </td>
        </tr>
    <?php
    
    $viewsTable .= ob_get_contents();
    ob_end_clean();
}


$peopleQuery = mysql_query("Select Name,count(*) as n
                            from Wiki_Edits
                            where Archived != 1
                            group by Name
                            order by n desc
                            limit 40");

while(list($name, $count) = mysql_fetch_array($peopleQuery))
{
    $peopleCount++;
    ob_start();

    $path = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
    $path = str_replace(array("'", '"', " "), array("", "", "-"), $path);
    $path = urlencode($path);
    
    ?>
        <tr>
            <td><?php echo $peopleCount; ?></td>
            <td class='littleborder'>
                <a href='/<?php echo $path; ?>'><?php echo $name; ?></a>
            </td>
            <td class='littleborder'>
                <a style='color:#FCA5BD' href='/edits?name=<?php echo $name; ?>'><?php echo $count; ?></a>
            </td>
        </tr>
    <?php

    $peopleTable .= ob_get_contents();
    ob_end_clean(); 
}


$pageQuery = mysql_query("Select PageID,count(*) as n
                            from Wiki_Edits
                            where Archived != 1
                            group by PageID
                            order by n desc
                            limit 40");

while(list($pageID, $count) = mysql_fetch_array($pageQuery))
{
    $pageInfo = mysql_query("Select `Path`, `Title` from `Wiki_Pages` where `ID`='$pageID'");
    list($path, $title) = mysql_fetch_array($pageInfo);
    
    $pageCount++;
    ob_start();

    ?>
        <tr>
            <td><?php echo $pageCount; ?></td>
            <td class='littleborder' style='max-width:175px'>
                <a href='/<?php echo $path; ?>'><?php echo $title; ?></a>
            </td>
            <td class='littleborder' style='color:#FCA5BD'>
                <?php echo $count; ?>
            </td>
        </tr>
    <?php
    
    $pageTable .= ob_get_contents();
    ob_end_clean(); 
}

?>

<div style='float:left; padding:8px; margin:0px 16px;'>
    <span class='medium'>Most Viewed Pages</span>

    <table class='history'>
        <tr><td>&nbsp;</td><td><b>Cool Page</b>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Views</b></td></tr>
        <?php echo $viewsTable ?>
    </table>
</div>

<div style='float:left; padding:8px; margin:0px 16px;'>
    <span class='medium'>Coolest Editors</span>

    <table class='history'>
        <tr><td>&nbsp;</td><td><b>Cool Person</b>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Edits</b></td></tr>
        <?php echo $peopleTable ?>
    </table>
</div>


<div style='float:left; padding:8px; margin:0px 16px;'>
    <span class='medium'>Most Edited Pages</span>

    <table class='history'>
        <tr><td>&nbsp;</td><td><b>Cool Page</b>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b>Edits</b></td></tr>
        <?php echo $pageTable ?>
    </table>
</div>

<div style='clear:both;'></div>
