<?php

function LoadJpeg($imgname)
{
    /* Attempt to open */
    $im = @imagecreatefromjpeg($imgname);

    /* See if it failed */
    if(!$im)
    {
        /* Create a black image */
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);

        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

        /* Output an error message */
        imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
    }

    return $im;
}

function AllocateColorByHex($Image, $Hex)
{
    $Hex = str_replace('#', $Hex);
    $Hex = split($Hex, 2);
    return imagecolorallocate($Image, $Hex[0], $Hex[1], $Hex[2]);
}

// Create a 300x100 image
$img = imagecreatetruecolor(400, 25);
$white = imagecolorallocate($img, 0xFF, 0xFF, 0xFF);
$black = imagecolorallocate($img, 0x00, 0x00, 0x00);

// Make the background red
imagefilledrectangle($img, 0, 0, 399, 15, $white);

// Path to our ttf font file
$fontbold = './fontbold.ttf';

$background = LoadJpeg('./rainbowsmoke.jpg');
imagecopy($img, $background, 0, 0, rand(1, 600), rand(1, 300), 400, 25);

if($_GET['text'])
    $_GET['word'] = $_GET['text'];

$Word = stripslashes($_GET['word']);
if(empty($Word))
    $Word = 'Rachel should learn how to program.';

imagefttext($img, 13, 0, 17, 17, $white, $fontbold, $Word);
imagefttext($img, 13, 0, 16, 16, $black, $fontbold, $Word);


# Output image to the browser
header('Content-Type: image/jpg');
imagejpeg($img);
imagedestroy($img);
imagedestroy($background);
?>
