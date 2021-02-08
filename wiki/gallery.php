<?php

    #
    # People options
    ##################

$Colorbox = 'Yes!';

    # Change it to anything but 'Yes!'
    # if you don't want this script to initiate
    # the jQuery Colorbox plugin.

$Directory = 'upload';

    # Defaults to the current directory.

    # But if your images are in a subfolder...
    # 	like	http://wetfish.net/upload/
    # 	use		$Directory = 'upload';

$Generate = 'Thumbnails';

    # Generate thumbnails by default.
    # Change to anything but 'Thumbnails' to turn it off.

$Thumbnail['Directory'] = 'upload/thumb';

    # A special place for your thumbnails...
    # 	like	http://wetfish.net/upload/thumb/
    # 	use		$Thumbnail['Directory'] = 'upload/thumb';

$Thumbnail['Size'] = '150';

$Pagination['is'] = 'On';
$Pagination['Count'] = '30';

    # How many images per page?
    #
    # Friendship files
    ####################

require('notgallery.php');

    #
    # Computer options
    ####################

$Path = $_SERVER['DOCUMENT_ROOT'];

    # Using the entire path is more user friendly.
    # Imagine this file gets include()-ed
    # the current working directory would be wrong
    # and likely to mess up relatave paths!

if(the($Directory) == 'Empty')
    $Directory = str_replace($Path, '', getcwd());

$Files = scandirByDate("$Path/$Directory");

echo "<center>";

if($Pagination['is'] == 'On')
{
    if(is_numeric($_GET['page']))
        $Page = $_GET['page'];

    if($Page < 2)
        $Page = 1;

    $Start = ($Page - 1) * $Pagination['Count'];

    $Pages = ceil(count($Files) / $Pagination['Count']);
    $Files = array_slice($Files, $Start, $Pagination['Count']);

    if($Pages > 1)
    {
        if($Page == 1) # The beginning
        {
            $Pagination['Output'] = "Page <b>$Page</b> of <b>$Pages</b>. &mdash; <a href='?page=2'>Next</a>";
        }
        elseif($Page == $Pages) # The end
        {
            $PreviousPage = $Page - 1;

            $Pagination['Output'] = "<a href='?page=$PreviousPage'>Previous</a> &mdash; Page <b>$Page</b> of <b>$Pages</b>.";
        }
        else # Everything else!
        {
            $NextPage = $Page + 1;
            $PreviousPage = $Page - 1;

            $Pagination['Output'] = "<a href='?page=$PreviousPage'>Previous</a> &mdash; Page <b>$Page</b> of <b>$Pages</b>. &mdash; <a href='?page=$NextPage'>Next</a>";
        }

        $Pagination['Output'] = "<div style='clear:both;'>{$Pagination['Output']}</div>";
        echo $Pagination['Output'];
    }
}

foreach($Files as $File)
{
    if(the($File) == 'Image')
    {
        if($Generate == 'Thumbnails')
        {
            if(!file_exists("$Path/{$Thumbnail['Directory']}/{$Thumbnail['Size']}_$File"))
		    if(!ResizeImage("$Path/$Directory/$File", "$Path/{$Thumbnail['Directory']}/{$Thumbnail['Size']}_$File", $Thumbnail['Size']))
			    continue;
        }

        echo	"<div class='GalleryContainer'>
                    <a href='/$Directory/$File' rel='Gallery'>
                        <img src='/{$Thumbnail['Directory']}/{$Thumbnail['Size']}_$File' class='GalleryImage' border='0' />
                    </a>
                </div>";
    }
}

if($Pagination['is'] == 'On')
{
    if($Pages > 1)
    {
        echo $Pagination['Output'];
    }
}

echo "</center>";

?>
