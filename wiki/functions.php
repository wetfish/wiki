<?php

error_reporting(E_ALL ^ E_NOTICE);
include("src/mysql.php");

function Redirect($URL, $Time=2)
{
    return "<meta http-equiv='refresh' content='$Time;url=$URL'>";
}

function Clean($Input, $Type="dicks")
{
    if($Type == "textarea")
        return str_replace(array("<", ">", "\"", "'", "\\", "`", "\r"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", ""), stripslashes($Input));
    else
        return trim(str_replace(array("<", ">", "\"", "'", "\\", "`", "\r", "\n"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", "", "$Break"), stripslashes($Input)));
}

function Format($Array)
{
    foreach($Array as $Key=>$Data)
    {
        switch($Key)
        {
            case "_Errors":
                if(!empty($Data['_Global']))
                    $Return = "<div class=\"gerror\"><span class=\"gerror\">{$Data['_Global']}</span></div>";
            break;

            case "_Options":
                $Options = Queries($Data);

                if(!empty($Options['enctype']))
                    $Extra .= "enctype=\"{$Options['enctype']}\" ";

                if(!empty($Options['onkeypress']))
                    $Extra .= "onkeypress=\"{$Options['onkeypress']}\" ";

                $Return .= "<form action=\"{$Options['action']}\" ".$Extra."name=\"{$Options['name']}\" id=\"{$Options['id']}\" method=\"post\"><table style='width:80%' cellpadding=\"0\">";
            break;

            default:
                $Data['Form'] = str_replace(array("\n", "\r"), array(";;n", ";;r"), $Data['Form']);
                $FormOptions = Queries($Data['Form']);

                if(!empty($Array['_Errors'][$Key]))
                    $Return .= "<tr><td colspan=\"2\"><span class=\"error\">{$Array['_Errors'][$Key]}</span></td></tr>";

                if(!empty($Data['Text']))
                    $Return .= "<tr><td class=\"text\">{$Data['Text']}</td><td>";
                else
                    $Return .= "<tr><td colspan=\"2\">";

                switch($FormOptions['type'])
                {
                    case "button":
                        $Return .= "<input type=\"button\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" value=\"{$FormOptions['value']}\" onClick=\"{$FormOptions['onclick']}\" />";
                    break;

                    case "checkbox":
                        if($FormOptions['checked'] != "")
                            $FormOptions['checked'] = "checked";

                        $Return .= "<input type=\"checkbox\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" {$FormOptions['checked']} />";
                    break;

                    case "file":
                        $Return .= "<input type=\"file\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" size=\"{$FormOptions['size']}\" />";
                    break;

                    case "hidden":
                        $Return .= "<input type=\"hidden\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" value=\"{$FormOptions['value']}\" />";
                    break;

                    case "password":
                        $Return .= "<input type=\"password\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" size=\"{$FormOptions['size']}\" maxlength=\"{$FormOptions['maxlength']}\" />";
                    break;

                    case "plaintext":
                        $Return .= $FormOptions['value'];
                    break;

                    case "submit":
                        $Return .= "<input type=\"submit\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" value=\"{$FormOptions['value']}\" size=\"{$FormOptions['size']}\" />";
                    break;

                    case "textarea":
                        $FormOptions['value'] = str_replace(array(";;n", ";;r"), array("\n", "\r"), $FormOptions['value']);
                        $Return .= "<textarea name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" style='{$Data['Style']}' rows=\"{$FormOptions['rows']}\" cols=\"{$FormOptions['cols']}\">{$FormOptions['value']}</textarea>";
                    break;

                    default:
                        if($FormOptions['readonly'] != "")
                            $FormOptions['readonly'] = "readonly";

                        $Return .= "<input type=\"text\" name=\"{$FormOptions['name']}\" id=\"{$FormOptions['id']}\" value=\"{$FormOptions['value']}\" size=\"{$FormOptions['size']}\" maxlength=\"{$FormOptions['maxlength']}\" {$FormOptions['readonly']} />";
                    break;
                }

                $Return .= "</td></tr>";

                if(!empty($Data['SubText']))
                    $Return .= "<tr><td>&nbsp;</td><td style=\"font-size: 8pt; font-style: italic;\">{$Data['SubText']}</td></tr>";
                break;
        }
    }

    $Return .= "</table></form>";

    return $Return;
}

function Queries($Text)
{
    preg_match_all("/\s*(.*?)\s*:\s*(?:(.)?{(.*)}\\2?|(.*?))(?:;|\s*$)/", $Text, $Matches);

    foreach($Matches[3] as $Key=>$Data)
    {
        if(trim($Data) == "")
            $Data = $Matches[4][$Key];

        if((trim($Matches[1][$Key]) != "") && (trim($Data) != ""))
            $Queries[strtolower(trim($Matches[1][$Key]))] = trim($Data);
    }

    return $Queries;
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

function RandomRow($table, $column)
{
    $max_sql = "SELECT max($column) AS max_id FROM $table";
    $max_row = mysql_fetch_array(mysql_query($max_sql));

    $random_number = mt_rand(1, $max_row['max_id']);

    $random_sql = "SELECT * FROM $table WHERE $column >= $random_number ORDER BY $column ASC LIMIT 1";
    $random_row = mysql_fetch_array(mysql_query($random_sql));

    while (!is_array($random_row))
    {
        $random_sql = "SELECT * FROM $table WHERE $column < $random_number ORDER BY $column DESC LIMIT 1";
        $random_row = mysql_fetch_array(mysql_query($random_sql));
    }
    
    return $random_row;
}

function FormatPath($Path)
{
    return str_replace("//", "/", $Path);
}

function str_entity_decode($input)
{
    return preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $input);
}

function str_split_unicode($str, $l = 0) {
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, "UTF-8");
        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, "UTF-8");
        }
        return $ret;
    }
    return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
}


function recentTime($timestamp)
{	
    $Now = time();
    $Passed = $Now - $timestamp;
    
    if($Passed < 4320000)
        return true;
    else
        return false;
}

function formatTime($timestamp)
{
    $Now = time();
    $Passed = $Now - $timestamp;
    $exactTime = date("F j\, Y G:i:s", $timestamp)." EST";
    
    if($Passed < 60)
    {
        if($Passed != 1)
            $Plural = 's';
            
        return "<span class='date' title='$exactTime'>$Passed second{$Plural} ago</span>";
    }
    elseif($Passed < 3600)
    {
        $Passed = round($Passed / 60);
        
        if($Passed != 1)
            $Plural = 's';
        
        return "<span class='date' title='$exactTime'>$Passed minute{$Plural} ago</span>";
    }
    elseif($Passed < 86400)
    {	
        $Passed = round($Passed / 60);
        $Passed = round($Passed / 60);

        if($Passed != 1)
            $Plural = 's';
        
        return "<span class='date' title='$exactTime'>$Passed hour{$Plural} ago</span>";
    }
    elseif($Passed < 4320000)
    {	
        $Passed = round($Passed / 24);
        $Passed = round($Passed / 60);
        $Passed = round($Passed / 60);	
        
        if($Passed != 1)
            $Plural = 's';
        
        return "<span class='date' title='$exactTime'>$Passed day{$Plural} ago</span>";
    }
    else
    {
        return $exactTime;
    }
}

function user_banned($bans, $user_ip)
{
    foreach($bans as $ban)
    {
        if(preg_match("/$ban/", $user_ip))
        {
            return true;
        }
    }

    return false;
}

?>
