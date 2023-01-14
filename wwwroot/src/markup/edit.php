<?php

function edit_markup($input, $markup)
{
    $output = $input;
    $markup = array_reverse($markup);

    foreach($markup as $id => $object)
    {
        $tags = explode(",", $object['tag']);
        $replacement = false;

        foreach($tags as $tag)
        {
            // Check if any replacements need to be made
            $tag = trim($tag);
            $replacement = edit_replacements($tag, $object['content']);

            if($replacement)
            {
                if(is_array($replacement))
                {
                    $output = replace_once($id, $object['tag'] . '[' . $replacement['content'] . ']', $output);
                }
                else
                {
                    $output = replace_once($id, $replacement, $output);
                }
            }
        }

        // If there was no replacement and all tags have been looped through
        if(!$replacement)
        {
            // Restore original source
            $output = replace_once($id, $object['source'], $output);
        }
    }

    return $output;
}

function edit_replacements($tag, $content)
{
    switch(strtolower($tag))
    {
        case "img":
        case "image":
        case "video":
            list($Link, $Size, $Position, $Border, $Text) = explode("|", $content, 5);

            $Link = trim($Link);
            $Size = trim($Size);
            $Position = trim(strtolower($Position));
            $Border = trim($Border);
            $Text = trim($Text);
            $URL = parse_url($Link);
            $site_url = getenv('SITE_URL');
            $site_regex = '/^' . preg_quote($site_url) . '$/i';

            // Automatically rehost content that isn't on wetfish or certain embedded sites
            if(preg_match("{https?}", $URL['scheme']) and
                (
                    // Negative matches, if the content isn't hosted on the following sites:
                    (
                        !preg_match($site_regex, $URL['host']) and
                        !preg_match("/(^|\.)youtube\.com$/", $URL['host']) and
                        !preg_match("/(^|\.)youtu\.be$/", $URL['host']) and
                        !preg_match("/(^|\.)vimeo\.com$/", $URL['host']) and
                        !preg_match("/(^|\.)vine\.co$/", $URL['host']) and
                        !preg_match("/(^|\.)ted\.com$/", $URL['host'])
                    )

                    // Positive matches, if the content is on the following sites:
                    or
                    (
                        preg_match("/cdn\.vine\.co$/", $URL['host'])
                    )
                ))
            {
                $Path = pathinfo($URL['path']);
                $Filename = uuid();

                // PHP's mime_content_type won't work on partial files it seems
                $Data = tmpfile();
                fwrite($Data, file_get_contents($Link));
                fflush($Data);

                $Mime = mime_content_type($Data);

                // Try to recover if mime misses
                if($Mime == "application/octet-stream")
                {
                    $Mime = get_headers($Link, 1)["Content-Type"];
                }

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
                    case "video/webm":
                        $Extension = "webm";
                        break;
                    case "video/mp4":
                        $Extension = "mp4";
                        break;
                    case "video/ogg":
                        $Extension = "ogv";
                        break;
                    case "image/svg+xml":
                        $Extension = "svg";
                        break;
                    default:
                        return "$Mime: Unsupported format! Please use: jpg, gif, png, webm, gifv, mp4, or ogv";
                }

                while(file_exists("upload/$Filename.$Extension"))
                {
                    $Filename = uuid();
                }


                // We have to loop through with file ops
                // copy() doesn't seem to be able to handle tmpfile() data
                $Disk = fopen("upload/$Filename.$Extension", "wb");

                // Make sure we're back at the start
                fseek($Data, 0);

                while(! feof($Data))
                {
                        $block = fread($Data, 32768);
                        fwrite($Disk, $block);
                }

                fclose($Disk);
                chmod("upload/$Filename.$Extension", 0644);

                $Time = time();

                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                    $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
                else
                    $userIP = $_SERVER['REMOTE_ADDR'];

                // Make sure the user IP is sanitized
                $userIP = preg_replace('/[^0-9.]/', '', $userIP);

                mysql_query("Insert into `Images` values ('NULL', '$Time', '', '$userIP', '$Link', 'upload/$Filename.$Extension')");

                $Text = trim("upload/$Filename.$Extension|$Size|$Position|$Border|$Text", '|');
                return array('tag' => strtolower($tag), 'content' => $Text);
            }

            return array('tag' => strtolower($tag), 'content' => $content);
        break;

        case "music":
            list($link, $autoplay, $loop) = explode("|", $content, 3);

            $url = parse_url($link);
            $site_url = getenv('SITE_URL');
            $site_regex = '/^' . preg_quote($site_url) . '$/i';

            // Automatically rehost content that isn't on wetfish
            if(preg_match("{https?}", $url['scheme']) and
                !preg_match($site_regex, $url['host']))
            {
                $path = pathinfo($url['path']);
                $Filename = uuid();

                $Data = tmpfile();
                fwrite($Data, file_get_contents($link));
                fflush($Data);

                $Mime = mime_content_type($Data);
                if($Mime == "application/octet-stream")
                {
                    $Mime = get_headers($link, 1)["Content-Type"];
                }

                switch($Mime){
                    case "application/ogg":
                        $Extension = "ogg";
                        break;
                    case "audio/ogg":
                        $Extension = "oga";
                        break;
                    case "audio/webm":
                        $Extension = "weba";
                        break;
                    case "audio/mpeg":
                        $Extension = "mp3";
                        break;
                    case "audio/wav":
                        $Extension = "wav";
                        break;
                    default:
                        return "$Mime: Unsupported format! Please use: mp3, wav, weba, or ogg";
                }

                while(file_exists("upload/$Filename.$Extension"))
                {
                    $Filename = uuid();
                }


                $Disk = fopen("upload/$Filename.$Extension", "wb");
                fseek($Data, 0);

                while(! feof($Data))
                {
                        $block = fread($Data, 32768);
                        fwrite($Disk, $block);
                }

                fclose($Disk);
 
                chmod("upload/$Filename.$Extension", 0644);

                $time = time();

                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                    $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
                else
                    $userIP = $_SERVER['REMOTE_ADDR'];

                // Make sure the user IP is sanitized
                $userIP = preg_replace('/[^0-9.]/', '', $userIP);

                mysql_query("Insert into `Images` values ('NULL', '$time', '', '$userIP', '$link', 'upload/$Filename.$Extension')");
                $text = trim("upload/$Filename.$Extension|$autoplay|$loop", '|');

                return array('tag' => strtolower($tag), 'content' => $text);
            }

            return array('tag' => strtolower($tag), 'content' => $content);
        break;

        case "soundcloud":
            $URL = parse_url($content);
            parse_str($URL['query'], $Query);

            if($Query['id'])
                return array('tag' => 'soundcloud', 'content' => $content);
            else
            {
                $options = array
                (
                    'http'=>array
                    (
                        'method'=>"GET",
                        'header'=>"Accept-language: en\r\n" .
                                  "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0\r\n"
                    )
                );

                $context = stream_context_create($options);
                $html = file_get_html($content, false, $context);
                $androidHack = $html->find("meta[property='al:android:url']", 0)->getAttribute('content');
                $trackID = preg_replace("/[^0-9]/", "", $androidHack);
            }

            return array('tag' => 'soundcloud', 'content' => "$content?id=$trackID");
        break;

        case "date":
            list($time, $format) = explode('|', $content, 2);

            if(empty($time))
            {
                $time = time();
            }

            if(!is_numeric($time))
            {
                $time = strtotime($time);
            }

            if(empty($format))
            {
                $format = 'l, F j, Y';
            }

            return date($format, $time);
        break;
    }

    return false;
}

?>
