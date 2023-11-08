<?php

if($Handle = opendir("source/"))
{
	while(FALSE !== ($File = readdir($Handle)))
	{
		if(preg_match("/.*?\.png/", $File)) {
			$Files[] = Clean($File); }
	}

	closedir($Handle);
}

foreach($Files as $File)
{
	echo "Processing $File...<br />";

	list($Width, $Height) = GetImageSize("source/$File");
	$Image = ImageCreateFromPNG("source/$File");

	for($X = 0; $X < $Width; $X += 32)
	{
		for($Y = 0; $Y < $Height; $Y += 32)
		{
			$Count++;

			$Piece = ImageCreateTrueColor(32, 32);
			ImageCopy($Piece, $Image, 0, 0, $X, $Y, 32, 32);

			unset($Unique);

			for($PieceX = 0; $PieceX < 32; $PieceX++)
			{
				for($PieceY = 0; $PieceY < 32; $PieceY++)
				{
					$Data = ImageColorAt($Piece, $PieceX, $PieceY);

					$Unique .= ($Data >> 16) & 0xFF;
					$Unique .= ($Data >> 8) & 0xFF;
					$Unique .= $Data & 0xFF;
				}
			}

			$wat[$Count] = $Unique;

			ImagePNG($Piece, "tiles/temp.png");
			chmod("tiles/temp.png", 0644);

			$Tile = "tiles/".md5($Unique).".png";
			$Tiles[] = $Tile;

			rename("tiles/temp.png", $Tile);
			ImageDestroy($Piece);
		}
	}

}

echo "<hr />";
echo "Images created:<br /><br />";

$Tiles = array_unique($Tiles);

foreach($Tiles as $Tile)
{
	echo "<img src='$Tile'>";
}

?>
