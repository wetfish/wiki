<?php

function Clean($Input, $Type="dicks")
{
        if($Type == "textarea")
                return str_replace(array("<", ">", "\"", "'", "\\", "`", "\r"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", ""), stripslashes($Input));
        else
                return trim(str_replace(array("<", ">", "\"", "'", "\\", "`", "\r", "\n"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", ""), stripslashes($Input)));
}

function CountUp($Count, $Files)
{
	$Count++;

	if($Count > count($Files) - 1)
	{
		$Count = 0;
	}

	return($Count);
}

function CountDown($Count, $Files)
{
	$Count--;

	if($Count < 0)
	{
		$Count = count($Files) - 1;
	}

	return($Count);
}

if($Handle = opendir("tiles/"))
{
	while(FALSE !== ($File = readdir($Handle)))
	{
		if(preg_match("/.*?\.png/", $File)) {
			$Files[] = Clean($File); }
	}

	closedir($Handle);
}

Shuffle($Files);
$Count = 0;

$MissingNo = ImageCreateTrueColor(512, 512);

for($X = 0; $X < 511; $X += 16)
{
	for($Y = 0; $Y < 511; $Y += 16)
	{
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		if(rand(0, 2) == 0)
		{
			$Count = CountUp($Count, $Files);
		}

		@ImageCopy($MissingNo, $Piece, $X, $Y, 0, 0, 32, 32);
		@ImageDestroy($Piece);
	}
}

for($Fun = 0; $Fun < 24; $Fun++)
{
	$Count = CountUp($Count, $Files);
	$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

	$RandX = rand(0, 511);
	$RandY = rand(0, 511);

	$RandX -= $RandX % 16;
	$RandY -= $RandY % 16;


	$BigJig = rand(1, 8);

	if($BigJig == 1)
	{
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY + 32, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY + 64, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY + 96, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX - 32, $RandY + 32, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX - 32, $RandY, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX - 64, $RandY, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX - 96, $RandY, 0, 0, 32, 32);

		$Count = CountDown($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);
	}
	elseif($BigJig == 2)
	{
		@ImageCopy($MissingNo, $Piece, $RandX + 32, $RandY, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX + 64, $RandY, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX + 96, $RandY, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX + 32, $RandY - 32, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY - 32, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY - 64, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY - 96, 0, 0, 32, 32);

		$Count = CountUp($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);
	}
	elseif($BigJig == 3)
	{
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY + 32, 0, 0, 32, 32);

		$Count = CountUp($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX, $RandY + 64, 0, 0, 32, 32);

		$Count = CountDown($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX, $RandY + 96, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX - 32, $RandY + 32, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX - 32, $RandY, 0, 0, 32, 32);

		$Count = CountUp($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX - 64, $RandY, 0, 0, 32, 32);

		$Count = CountDown($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX - 96, $RandY, 0, 0, 32, 32);

		$Count = CountDown($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);
	}
	elseif($BigJig == 4)
	{
		@ImageCopy($MissingNo, $Piece, $RandX + 32, $RandY, 0, 0, 32, 32);

		$Count = CountUp($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX + 64, $RandY, 0, 0, 32, 32);

		$Count = CountDown($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX + 96, $RandY, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX + 32, $RandY - 32, 0, 0, 32, 32);
		@ImageCopy($MissingNo, $Piece, $RandX, $RandY - 32, 0, 0, 32, 32);

		$Count = CountUp($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX, $RandY - 64, 0, 0, 32, 32);

		$Count = CountDown($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);

		@ImageCopy($MissingNo, $Piece, $RandX, $RandY - 96, 0, 0, 32, 32);

		$Count = CountUp($Count, $Files);
		$Piece = @ImageCreateFromPNG("tiles/".$Files[$Count]);
	}

	@ImageCopy($MissingNo, $Piece, $RandX, $RandY, 0, 0, 32, 32);
	@ImageDestroy($Piece);
}

Header("Content-type: image/png");
ImagePNG($MissingNo);
ImageDestroy($MissingNo);

?>
