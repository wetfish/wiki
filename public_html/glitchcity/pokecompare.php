<?php

// Originally I wanted to split all of the images into unique chunks, instead of having duplicate blocks of trees for exmaple
// Unfortunately the source files were originally saved as .jpgs and all of the pixels are slightly different :(
// This file was used to figure that out, since it wasn't really visible with the naked eye

$Image1 = ImageCreateFromPNG("1.png");
$Image2 = ImageCreateFromPNG("2.png");

for($PieceX = 0; $PieceX < 32; $PieceX++)
{
	for($PieceY = 0; $PieceY < 32; $PieceY++)
	{
		$Data1 = ImageColorAt($Image1, $PieceX, $PieceY);
		$Data2 = ImageColorAt($Image2, $PieceX, $PieceY);

		$Color1['red'] = ($Data1 >> 16) & 0xFF;
		$Color1['green'] = ($Data1 >> 8) & 0xFF;
		$Color1['blue'] = $Data1 & 0xFF;

		$Color2['red'] = ($Data2 >> 16) & 0xFF;
		$Color2['green'] = ($Data2 >> 8) & 0xFF;
		$Color2['blue'] = $Data2 & 0xFF;

		if(($Color1['red'] != $Color2['red']) || ($Color1['green'] != $Color2['green']) || ($Color1['blue'] != $Color2['blue']))
		{
			echo "Differentness found at $PieceX, $PieceY<br />{$Color1['red']} vs {$Color2['red']}<br />{$Color1['green']} vs {$Color2['green']}<br />{$Color1['blue']} vs {$Color2['blue']}<hr />";
		}
	}
}

?>
