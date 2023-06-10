<?php

function the($Thing)
{
	if(empty($Thing))
		return 'Empty';

	elseif(is_dir($Thing))
		return 'Directory';

	elseif(is_link($Thing))
		return 'Symlink';

	else
	{
		$Path = pathinfo($Thing);
		$Extension = $Path['extension'];

		if(preg_match('{jpe?g|gif|png}i', $Extension))
			return 'Image';
	}

}

function ResizeImage($Filename, $Thumbnail, $Size)
{
	$Path = pathinfo($Filename);
	$Extension = $Path['extension'];

	$ImageData = @GetImageSize($Filename);
	if ($ImageData == false)
		return false;
	$Width = $ImageData[0];
	$Height = $ImageData[1];

	if($Width >= $Height and $Width > $Size)
	{
		$NewWidth = $Size;
		$NewHeight = ($Size / $Width) * $Height;
	}
	elseif($Height >= $Width and $Height > $Size)
	{
		$NewWidth = ($Size / $Height) * $Width;
		$NewHeight = $Size;
	}
	else
	{
		$NewWidth = $Width;
		$NewHeight = $Height;
	}

	$NewImage = @ImageCreateTrueColor($NewWidth, $NewHeight);

	$Mime = mime_content_type($Filename);
	switch ($Mime) {
		case "image/gif":
			$Image = @ImageCreateFromGif($Filename);
			break;
		case "image/png":
			$Image = @ImageCreateFromPng($Filename);
			break;
		case "image/jpeg":
			$Image = @ImageCreateFromJpeg($Filename);
			break;
	}

	if($ImageData[2] == IMAGETYPE_GIF or $ImageData[2]  == IMAGETYPE_PNG)
	{
		$TransIndex = imagecolortransparent($Image);

		// If we have a specific transparent color
		if ($TransIndex >= 0)
		{
			// Get the original image's transparent color's RGB values
			$TransColor = imagecolorsforindex($Image, $TransIndex);

			// Allocate the same color in the new image resource
			$TransIndex = imagecolorallocate($NewImage, $TransColor['red'], $TransColor['green'], $TransColor['blue']);

			// Completely fill the background of the new image with allocated color.
			imagefill($NewImage, 0, 0, $TransIndex);

			// Set the background color for new image to transparent
			imagecolortransparent($NewImage, $TransIndex);

		}
		// Always make a transparent background color for PNGs that don't have one allocated already
		elseif ($ImageData[2] == IMAGETYPE_PNG)
		{

			// Turn off transparency blending (temporarily)
			imagealphablending($NewImage, false);

			// Create a new transparent color for image
			$color = imagecolorallocatealpha($NewImage, 0, 0, 0, 127);

			// Completely fill the background of the new image with allocated color.
			imagefill($NewImage, 0, 0, $color);

			// Restore transparency blending
			imagesavealpha($NewImage, true);
		}
	}

	@ImageCopyResampled($NewImage, $Image, 0, 0, 0, 0, $NewWidth, $NewHeight, $Width, $Height);

	switch ($Mime) {
		case "image/gif":
			@ImageGif($NewImage, $Thumbnail);
			break;
		case "image/png":
			@ImagePng($NewImage, $Thumbnail);
			break;
		case "image/jpeg":
			@ImageJpeg($NewImage, $Thumbnail);
			break;
	}

	@chmod($Thumbnail, 0644);
	return true;
}

function scandirByDate($dir)
{
    $ignore = array('.', '..', '.svn', '.htaccess', '.git');
    $files = array();

    foreach (scandir($dir) as $file)
	{
        if(in_array($file, $ignore)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

?>
