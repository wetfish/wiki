<?php

function rewrite_markup($input, $markup)
{
    $output = $input;
    
    foreach($markup as $object)
    {
        if(is_array($object['content']))
        {
            $output = replace_once($object['source'], rewrite_markup($object['source'], $object['content']), $output);
        }
        else
        {
            $tags = explode(",", $object['tag']);

            foreach($tags as $tag)
            {
                $replacement = replace_markup($tag, $object['content']);

                if($replacement)
                {
                    if(is_array($replacement))
                    {
                        $output = replace_once($object['source'], $object['tag'] . '[' . $replacement['content'] . ']', $output);
                    }
                    else
                    {
                        $output = replace_once($object['source'], $replacement, $output);
                    }
                }
            }
        }
    }

    return $output;
}

function replace_markup($tag, $content)
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
            
            // Automatically rehost content that isn't on wetfish or certain embedded sites
            if(preg_match("{https?}", $URL['scheme']) and
                (!preg_match("/^wiki\.wetfish\.net$/", $URL['host']) and
                 !preg_match("/(^|\.)youtube\.com$/", $URL['host']) and
                 !preg_match("/(^|\.)youtu\.be$/", $URL['host']) and
                 !preg_match("/(^|\.)vimeo\.com$/", $URL['host']) and
                 !preg_match("/(^|\.)vine\.co$/", $URL['host']) and
                 !preg_match("/(^|\.)ted\.com$/", $URL['host'])
                ))
            {
                $Path = pathinfo($URL['path']);
                $Filename = uuid();
                $Extension = $Path['extension'];
                
                if(strpos($Extension, '?') !== FALSE)
                    $Extension = substr($Extension, 0, strpos($Extension, '?'));
                
                if(preg_match('/^(jpe?g|gif|png|webm|gifv|mp4|ogv)$/i', $Extension))
                {
                    // Automatically convert gifv urls to webm
                    if($Extension == "gifv")
                    {
                        $Extension = "webm";
                        $Link = str_replace(".gifv", ".webm", $Link);
                    }
                    
                    while(file_exists("upload/$Filename.$Extension"))
                    {
                        $Filename = uuid();
                    }
                        
                    file_put_contents("upload/$Filename.$Extension", file_get_contents($Link));
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
                else
                {
                    return "HACKER!!!!!!!!!!!!!!";
                }
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
