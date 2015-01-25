<form>
	Enter Filename
	<input type='text' name='image' />
	<input type='submit' value='Submit' />
</form>

<?php

if($_GET['image'])
{
	echo "<hr />";

	foreach(exif_read_data($_GET['image']) as $Key => $Value)
	{
		if(is_array($Value))
			$Value = print_r($Value, true);
	
		if($Key != 'MakerNote')
			echo "<strong>$Key</strong> =&gt; $Value<br />";
	}
}

?>