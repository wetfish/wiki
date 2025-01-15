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
        # why not use mime_content_type($filename) ?
		if(preg_match('{jpe?g|gif|png}i', $Extension))
			return 'Image';
		elseif(preg_match('{mp4|webm}i', $Extension))
			return 'Video';
	}

}

# ref. https://www.php.net/manual/en/function.imagecreatefromwebp.php#126269
# PHP still cannot handle animated webp files; must exclude them when found
function webpinfo($file){if(!is_file($file)){return false;}else{$file=realpath($file);}$fp=fopen($file,'rb');if(!$fp){return false;}$data=fread($fp,90);fclose($fp);unset($fp);$header_format='A4Riff/'.'I1Filesize/'.'A4Webp/'.'A4Vp/'.'A74Chunk';$header=unpack($header_format,$data);unset($data,$header_format);if(!isset($header['Riff'])||strtoupper($header['Riff'])!=='RIFF'){return false;}if(!isset($header['Webp'])||strtoupper($header['Webp'])!=='WEBP'){return false;}if(!isset($header['Vp'])||strpos(strtoupper($header['Vp']),'VP8')===false){return false;}if(strpos(strtoupper($header['Chunk']),'ANIM')!==false||strpos(strtoupper($header['Chunk']),'ANMF')!==false){$header['Animation']=true;}else{$header['Animation']=false;}if(strpos(strtoupper($header['Chunk']),'ALPH')!==false){$header['Alpha']=true;}else{if(strpos(strtoupper($header['Vp']),'VP8L')!==false){$header['Alpha']=true;}else{$header['Alpha']=false;}}unset($header['Chunk']);return $header;}

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
		case "image/jpg":
			$Image = @ImageCreateFromJpeg($Filename);
			break;
		case "image/webp":
            $WebpData = webpinfo($Filename);
            if(isset($WebpData['Animation']) && $WebpData['Animation'] === true)
                $Image = "";
            else
                $Image = ImageCreateFromWebp($Filename);
			break;
	}
    if(!$Image)
        return false;

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
		case "image/webp":
			@ImageJpeg($NewImage, $Thumbnail);
			break;
	}

	@chmod($Thumbnail, 0644);
	return true;
}

function VideoThumbnail($Filename, $Thumbnail, $Size)
{
    if(!file_exists("/usr/bin/ffprobe"))
        return false;
	$Mime = mime_content_type($Filename);
    if(!preg_match("/^video/", $Mime))
        return false;
    $ffprobe_cmd = '/usr/bin/ffprobe -v quiet -select_streams "v:0" -show_entries "stream=duration" -of "default=nokey=1:noprint_wrappers=1" ' . $Filename;
    exec($ffprobe_cmd, $exec_stdout, $status);
    $duration = (int)$exec_stdout;
    if($duration==0)
        return false;
    if(!file_exists("/usr/bin/ffmpeg"))
        return false;
    $ffmpeg_cmd = '/usr/bin/ffmpeg -v quiet -ss "' . $duration * 0.25 .
        '" -i "' . $Filename .
        '" -vf "scale=' . $Size . ':' . $Size . ':force_original_aspect_ratio=decrease" ' .
        '-vframes 1 ' .
        '"' . $Thumbnail . '"';
    exec($ffmpeg_cmd, $exec_stdout, $status);
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
