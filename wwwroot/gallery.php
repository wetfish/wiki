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

    # Icons, placeholders for missing thumbnails
$SVG_Icon_directory="<svg xmlns=http://www.w3.org/2000/svg viewBox='0 0 20 20'><path fill=#fff d='M2 3v14h16V5h-8L8 3z'/></svg>";
$SVG_Icon_image="<svg xmlns=http://www.w3.org/2000/svg viewBox='0 0 20 20'><path fill=#fff d='M8 8l3 4.712L13 11l4 5H3zm8-2a2 2 0 1 1-4 0 2 2 0 1 1 4 0z'/></svg>";
$SVG_Icon_text="<svg xmlns=http://www.w3.org/2000/svg viewBox='0 0 20 20'><path fill=#fff d='M4 3v2h12V3zm0 4v2h8V7zm0 4v2h12v-2zm0 4v2h6v-2z'/></svg>";
$SVG_Icon_video="<svg xmlns=http://www.w3.org/2000/svg viewBox='0 0 20 20'><path fill=#fff d='M6 4l9 6-9 6z'/></svg>";


if(the($Directory) == 'Empty')
    $Directory = str_replace($Path, '', getcwd());

$Files = scandirByDate("$Path/$Directory");

echo "<center>";

if($Pagination['is'] == 'On')
{

    $Page = $_GET['page'] ?? 1;
    if(!is_numeric($Page))
        $Page = 1;
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
    $mimetype = mime_content_type("$Path/$Directory/$File");
    $thumbnail_loc = "{$Thumbnail['Directory']}/{$Thumbnail['Size']}_{$File}";
    if(preg_match("/^video/", $mimetype))
        $thumbnail_loc = substr($thumbnail_loc, 0 , (strrpos($thumbnail_loc, "."))) . ".jpg";
    if(preg_match("/^(image|video)/", $mimetype))
    {
        if($Generate == 'Thumbnails')
        {
            if(!file_exists("$Path/$thumbnail_loc"))
            {
                if (preg_match("/^image/", $mimetype))
                    ResizeImage("$Path/$Directory/$File", "$Path/$thumbnail_loc", $Thumbnail['Size']);
                elseif (preg_match("/^video/", $mimetype))
                    VideoThumbnail("$Path/$Directory/$File", "$Path/$thumbnail_loc", $Thumbnail['Size']); 
            }
        }
        echo "<div class='GalleryContainer'><a href='/$Directory/$File' rel='Gallery'>";
        if(file_exists("$Path/$thumbnail_loc"))
            echo "  <img src='/$thumbnail_loc' class='GalleryImage' border='0' />";
        else
        {
            if(preg_match("/^image/", $mimetype))
                echo "  $SVG_Icon_image";
            elseif(preg_match("/^video/", $mimetype))
                echo "  $SVG_Icon_video";
            elseif(preg_match("/^text/", $mimetype))
                echo "  $SVG_Icon_text";
            else
                echo "  $SVG_Icon_directory";
        }
        echo "</a></div>";
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
