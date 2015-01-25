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

	if(preg_match('/^gif$/i', $Extension))
		$Image = @ImageCreateFromGif($Filename);
	elseif(preg_match('/^png$/i', $Extension))
		$Image = @ImageCreateFromPng($Filename);
	else
		$Image = @ImageCreateFromJpeg($Filename);
	
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

	if(preg_match('/^gif$/i', $Extension))
		@ImageGif($NewImage, $Thumbnail);
	elseif(preg_match('/^png$/i', $Extension))
		@ImagePng($NewImage, $Thumbnail);
	else
		@ImageJpeg($NewImage, $Thumbnail);

		
	@chmod($Thumbnail, 0644);
}

?>