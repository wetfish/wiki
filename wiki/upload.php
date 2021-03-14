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
        $Mime = mime_content_type($Image['tmp_name']);
        switch($Mime){
            case "image/jpg":
                $Extension = "jpg";
                break;
            case "image/jpeg":
                $Extension = "jpeg";
                break;
            case "image/gif":
                $Extension = "gif";
                break;
            case "image/png":
                $Extension = "png";
                break;
            case "audio/mp3":
                $Extension = "mp3";
                break;
            case "audio/webm":
            case "video/webm":
                $Extension = "webm";
                break;
            case "video/mp4":
                $Extension = "mp4";
                break;
            case "audio/midi":
            case "audio/xmidi":
                $Extension = "mid";
                break;
            case "image/svg+xml":
                $Extension = "svg";
                break;
            default:
            // Catch all for the various text types that may end up being parsed, accept them as text files
                if(preg_match('/text\/.*/i', $Mime))
                    $Extension = "txt";
                else {
                    echo "HACKER!!!!!!!!!!!!!";
                    return;
		}
        }
    
        $Filename = uuid();
        while(file_exists("upload/$Filename.$Extension"))
            {
                $Filename = uuid();
            }

        move_uploaded_file($Image['tmp_name'], "upload/$Filename.$Extension");
        chmod("upload/$Filename.$Extension", 0644);

        $Time = time();

        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $userIP = $_SERVER['REMOTE_ADDR'];

        // Make sure the user IP is sanitized
        $userIP = preg_replace('/[^0-9.]/', '', $userIP);

        mysql_query("Insert into `Images` values ('NULL', '$Time', '', '$userIP', '{$Image['name']}', 'upload/$Filename.$Extension')");

        if(!empty($_GET['api']))
        {
            header("Location: /upload/{$Filename}.{$Extension}");
            exit;
        }

        $Mime = preg_replace('/[^a-zA-Z0-9].*/', '', $Mime);
        echo "<meta http-equiv='refresh' content='2;url=upload/$Filename.$Extension'>$Mime added!";
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
