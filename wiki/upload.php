<?php

# The super great wetfish image uploader!
include("src/mysql.php");

function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
	return $prefix . $uuid;
}

if($_FILES)
{
	$Image = $_FILES['Image'];
			
	if($Image['error'])
	{
		echo "There was an error!";
	}
	else
	{
		$ImageInfo = pathinfo($Image['name']);
		$Filename = uuid();
		$Extension = $ImageInfo['extension'];

		if(preg_match('/^(jpe?g|gif|png|txt|mp3|mid)$/i', $Extension))
		{
			while(file_exists("upload/$Filename.$Extension"))
			{
				$Filename = uuid();
			}
						
			move_uploaded_file($Image['tmp_name'], "upload/$Filename.$Extension");
			chmod("upload/$Filename.$Extension", 0644);

			$Time = time();
			$Account = $_SERVER['REMOTE_ADDR'];
			mysql_query("Insert into `Images` values ('NULL', '$Time', '', '$Account', '{$Image['name']}', 'upload/$Filename.$Extension')");
				
			echo "<meta http-equiv='refresh' content='2;url=upload/$Filename.$Extension'>Image added!";
		}
		else
			echo "HACKER!!!!!!!!!!!!!!";
	}
}
else
{
	echo <<<Image
<form enctype="multipart/form-data" action="upload.php" method="POST">
	Send this file: <input name="Image" type="file" />
	<input type="submit" value="Send File" />
</form>
Image;

}

?>
