<?php

function view_markup($input, $markup)
{
    $output = $input;
    $markup = array_reverse($markup);
    
    foreach($markup as $id => $object)
    {
        $tags = explode(",", $object['tag']);
        $tags = array_reverse($tags);
        $replacement = "";

        foreach($tags as $tag)
        {
            if($replacement)
            {
                $replacement = view_replacements($tag, $replacement);

            }
            else
            {
                $replacement = view_replacements($tag, $object['content']);
            }
        }

        if($replacement)
        {
            $output = replace_once($id, $replacement, $output);
        }
        else
        {
            $output = replace_once($id, $object['source'], $output);
        }
    }

    return $output;
}
function view_replacements($tag, $content)
{
    if($content !== "")
    {
        $tag = trim($tag);
        
        switch(strtolower($tag))
        {
            case "total":
                switch(strtolower($content))
                {
                    case "pages":
                        $pageTotal = mysql_query("Select `ID` from `Wiki_Pages`");
                        $totalPages = mysql_num_rows($pageTotal);
                        
                        return number_format($totalPages);
                    break;

                    if($args[1])
                        $args[1] = "background-color:{$args[1]}";
                    elseif(count($args) == 2)
                    {
                        return "<span style='color:{$args[0]}'>{$args[1]}</span>";
                    }
                    else
                        return $content;
                    
                    return "<span style='color:{$args[0]}; {$args[1]}'>{$args[2]}</span>";
                }                
            break;
            
            case "ad":
                $position = strtolower($content);
                
                if($position == 'right')
                    $class = 'right';
                
                elseif($position == 'left')
                    $class = 'left';
            
                $ID = uuid();
                return "<div id='$ID' class='$class'><iframe id='scraper-friend' src='https://ads.wetfish.net/friendship/scraper.html' style='width:175px; height:640px; border:0; outline:0; overflow:hidden;' scrolling='no'></iframe></div>";
            break; 
            
            case "ads":
                $ID = uuid();
                $ID2 = uuid();
                return "<div id='$ID' class='left'><iframe id='scraper-friend' src='https://ads.wetfish.net/friendship/scraper.html' style='width:175px; height:640px; border:0; outline:0; overflow:hidden;' scrolling='no'></iframe></div>"
                        ."<div id='$ID2' class='right'><iframe id='scraper-friend' src='https://ads.wetfish.net/friendship/scraper.html' style='width:175px; height:640px; border:0; outline:0; overflow:hidden;' scrolling='no'></iframe></div>";
 
            break;
            
            case "pre":
                $content = trim(str_replace(array("{", "}", '[', ']'), array("&#123;", "&#125;", '&#91;', '&#93;'), $content));
                return "<pre>$content</pre>";
            break;

            case "center":
                return "<center>$content</center>";
            break;
            
            case "color":
                $args = explode("|", $content, 3);

                if(count($args) == 3)
                {
                    if($args[1])
                        $args[1] = "background-color:{$args[1]}";
                    
                    return "<span style='color:{$args[0]}; {$args[1]}'>{$args[2]}</span>";					
                }
                elseif(count($args) == 2)
                {
                    return "<span style='color:{$args[0]}'>{$args[1]}</span>";
                }
                else
                    return $content;
                
            break;
            
            case "infobox":
                $args = explode("|", $content, 3);

                if($args[2])
                    return "<div class='wiki-box' style='{$args[0]}'><div class='wiki-box-title'>{$args[1]}</div>{$args[2]}</div>";
                else
                    return "<div class='wiki-box'><div class='wiki-box-title'>{$args[0]}</div>{$args[1]}</div>";
            break;

            case "titlebox":
                $args = explode("|", $content, 2);
                return "<header class='title-box'><h1>{$args[0]}</h1><span>{$args[1]}</span></header>";
            break;

            case "box":
                $args = explode("|", $content, 2);

                if($args[1])
                    return "<div class='wiki-box' style='{$args[0]}'>{$args[1]}</div>";
                else
                    return "<div class='wiki-box'>{$args[0]}</div>";
            break;
            
            case "title":
                return "<div class='wiki-box-title'>$content</div>";
            break;

            case "style":
                $args = explode("|", $content, 2);
                $args[0] = preg_replace("/([\s\n]|&emsp;)+/", " ", $args[0]);
                return "<div style='{$args[0]}'>{$args[1]}</div>";
            break;

            case "right":
                return "<div class='right'>$content</div>";
            break;
            
            case "left":
                return "<div class='left'>$content</div>";
            break;

            case "bold":
            case "b":
                return "<b>$content</b>";
            break;

            case "underline":
            case "u":
                return "<u>$content</u>";
            break;

            case "italic":
            case "italics":
            case "i":
                return "<i>$content</i>";
            break;

            case "strike":
            case "s":
                return "<span class='strike'>$content</span>";
            break;

            case "big":
                return "<span class='big'>$content</span>";
            break;

            case "medium":
            case "med":
                return "<span class='medium'>$content</span>";
            break;

            case "small":
            case "sml":
                return "<span class='small'>$content</span>";
            break;

            case "redirect":
                if(isset($_GET['random']))
                    return "<meta http-equiv='refresh' content='1;url=/$content/?random'>You're being brought to '$content'...";
                else
                    return "<meta http-equiv='refresh' content='1;url=/$content'>You're being brought to '$content'...";
            break;
            
            case "heading":
                //return "<a name='".str_replace(array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", " "), "", $content)."'></a>$content<hr />";
                return "<div class='clear'>$content</div><hr />";
            break;
            
            case "subheading":
                //return "<a name='".str_replace(array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", " "), "", $content)."'></a>$content<hr />";
                return "$content<hr />";
            break;
            
            case "url":
                list($link, $text) = explode("|", $content, 2);

                $link = trim($link);
                $text = trim($text);
                $url = parse_url($link);

                switch($url['scheme'])
                {
                    case "http":
                    case "https":
                    case "ftp":
                    case "irc":
                        if(empty($text))
                            $text = $link;

                        return "<a href='{$link}' target='_blank'>{$text}</a>";
                    break;

                    case "fish":
                        $link = "https://wiki.wetfish.net/{$url['host']}{$url['path']}";

                        if(empty($text))
                            $text = $url['host'];

                        return "<a href='{$link}'>{$text}</a>";
                    break;

                    default:
                        if(empty($text))
                            $text = $link;

                        return "<a href='http://{$link}' target='_blank'>{$text}</a>";
                    break;
                }
            break;

            case "image":
            case "img":
                //list($Link, $Size, $Position, $Border, $Text) = explode("|", $content, 5);
        
                $args = explode('|', $content, 6);
                $Link = trim($args[0]);
                $Border = trim($args[3]);
                $Text = trim($args[4]);
                $rand = trim($args[5]);

                $path = pathinfo($Link);

                // If a video extension was used
                if(preg_match('/^(webm|mp4|ogv)$/i', $path['extension']))
                {
                    // Output a special html5 player that behaves like a gif
                    $options = array
                    (
                        'autoplay' => true,
                        'controls' => false,
                        'muted' => true,
                        'loop' => true
                    );

                    return embed_html5_video($Link, $options);
                }
                
//				$args[1] = trim(str_replace('px', '', strtolower($args[1])));
//				$args[2] = trim(str_replace('px', '', strtolower($args[2])));
                
                $args[1] = trim($args[1]);
                $args[2] = trim($args[2]);
                
                if(empty($args[1]))
                    $args[1] = 0;
                
                if(is_numeric($args[1]) and (is_string($args[2]) or empty($args[2])))
                {
                    $Size = $args[1];
                    $Position = $args[2];
                }
                elseif(is_string($args[1]) and (is_numeric($args[2]) or empty($args[2])))
                {
                    $Size = $args[2];
                    $Position = $args[1];
                }
                
                if(strtolower($Position) == "right" or strtolower($Position) == "left")
                    $Position = "float:$Position;";
                elseif(strtolower($Position) == "border")
                    $Border = true;
                elseif(empty($Text))
                    $Text = $Position;

                if($Border)
                    $Border = "border: 4px solid #C17EE8; padding:4px; margin-bottom:16px; border-radius:8px; -moz-border-radius:8px; -webkit-border-radius:8px;";
                else
                    unset($Border);
                
                if(is_numeric($Size) and $Size < 1600 and $Size > 0 and strpos($Link, 'http://') === false)
                {
                    $Info = pathinfo($Link);

                    // Make sure the file actually exists
                    if(!file_exists(__DIR__ . "/../../upload/{$Info['basename']}"))
                    {
                        // Display an icon if the image can't be loaded
                        $ImageText = "<a href='#error' class='exempt'><img src='/upload/apple.gif' title='There was an error loading this image' border='0' /></a>";
                    }
                    else
                    {
                        if(!file_exists(__DIR__ . "/../../upload/{$Size}_{$Info['basename']}"))
                            ResizeImage($Link, __DIR__ . "/../../upload/{$Size}_{$Info['basename']}", $Size);
                            
                        $ImageText = "<a href='/$Link' class='exempt'><img src='/upload/{$Size}_{$Info['basename']}' border='0' /></a>";
                    }
                }
                else
                {
                    if(strpos($Link, 'http://') === false && strpos($Link, 'https://') === false)
                        $Link = "/$Link";
                
                    unset($Size);
                
                    $URL = parse_url($Link);

                    // Make sure wiki images exist
                    if(preg_match("{^/upload}", $URL['path']) && !file_exists(__DIR__ . "/../..{$URL['path']}"))
                    {
                        // Display an icon if the image can't be loaded
                        $ImageText = "<a href='#error' class='exempt'><img src='/upload/apple.gif' title='There was an error loading this image' border='0' /></a>";
                    }
                    else
                    {
                        if($URL['host'] == "glitch.wetfish.net")
                            $class = "class='glitchme'";
                        
                        // This is a terrible hack and really should be expanded to parse and reconstruct the url
                        if($rand)
                            $rand = "&rand=".mt_rand();
                        
                        // Lol I should finish this some time
                        if(is_numeric($Size) and $Size < 1600 and $Size > 0)
                            $size = "style=''";
                        
                        $ImageText = "<img src='$Link$rand' $class />";
                    }
                }
                
                if($Text)
                    $Text = "<div class='small'>$Text</div>";
                
                return "<div style='margin:0px 8px; display:inline-block; max-width:100%; $Position $Border'>$ImageText $Text</div>";
            break;

            case "video":
                $url = parse_url($content);

                if(preg_match("/(^|\.)youtube\.com$/i", $url['host']))
                    return embed_youtube($content);

                if(preg_match("/(^|\.)youtu\.be$/i", $url['host']))
                    return embed_youtube($content);

                if(preg_match("/(^|\.)vimeo\.com$/i", $url['host']))
                    return embed_vimeo($content);

                if(preg_match("/(^|\.)vine\.co$/i", $url['host']))
                    return embed_vine($content);

               if(preg_match("/(^|\.)ted\.com$/i", $url['host']))
                    return embed_ted($content);

                // Otherwise, it must be a html5 video!
                return embed_html5_video($content);
            break;

            case "youtube":
            case "vimeo":
            case "vine":
            case "ted":
                return call_user_func('embed_' . $tag, $content);
            break;

            case "playlist":
                $url = parse_url($content);
                parse_str($url['query'], $query);

                return "<iframe width='640' height='360' src='https://www.youtube.com/embed/videoseries?list={$query['list']}&index={$query['index']}' frameborder='0' allowfullscreen></iframe>";			
            break;
            
            case "soundcloud":
                return embed_soundcloud($content);
            break;
            
            case "load":
            case "embed":
                $URL = parse_url($content);
                
                if(empty($URL['scheme']))
                    $URL['scheme'] = "http";

                if(empty($URL['host']))
                    $URL['host'] = "wiki.wetfish.net";

                if($URL['path'] == 'index.php' or empty($URL['path']) or $URL['path'] == $_GET['SUPERdickPAGE'] or !empty($_GET['load']))
                    $URL['path'] = 'yousosilly.php';
                    
                if(empty($URL['query']))
                {
                    $Query = array();
                
                    foreach($_GET as $Key => $Value)
                    {
                        if($Key != 'SUPERdickPAGE')
                            $Query[] = "$Key=".urlencode($Value);
                    }
                    
                    $URL['query'] = implode('&', $Query);
                }
                
                if(preg_match('/^.*\.?wetfish.net$/i', $URL['host']))
                {
                    // Prepend uuid with a string to ensure it's valid
                    $ID = "embed-" . uuid();
                    
                    if($URL['host'] == 'danger.wetfish.net')
                    {
                        $URL['path'] = substr($URL['path'], 1);
                    
                        if(preg_match("/^[a-f0-9]+$/", $URL['path']))
                        {
                            $URL['query'] = "hash={$URL['path']}";
                            $URL['path'] = "view.php";
                            
                        }
                    }
                    
                    //return "<iframe src='{$URL['scheme']}://{$URL['host']}/{$URL['path']}?{$URL['query']}' style='height:0px; width:0px; display:none;'></iframe>
                    
                    
                    /**/if($URL['host'] != "wiki.wetfish.net")
                        return "<div id='$ID'><script>$('#$ID').load('/load.php?id=$ID&url={$URL['scheme']}://{$URL['host']}/{$URL['path']}?".urlencode($URL['query'])."');</script></div>";
                    else
                        return "<div id='$ID'><script>$('#$ID').load('/{$URL['path']}?{$URL['query']}&load=true');</script></div>";
                    /**/
                    //return "LOL GOOGLE HACKED WETFISH";
                }
            break;
            
            case "music":
                list($link, $autoplay, $loop) = explode("|", $content, 3);

                // Output a special html5 player that behaves like a gif
                $options = array
                (
                    'autoplay' => ($autoplay) ? true : false,
                    'loop' => ($loop) ? true : false
                );

                return embed_html5_audio($link, $options);
            break;
            
            case "flash":
                list($url, $width, $height) = explode("|", $content);

                return "<object width='$width' height='$height'><param name='movie' value='$url'><embed src='$url' width='$width' height='$height'></embed></object>";
            break;

            case "drainbow":
            case "dblrainbow":
            case "doublerainbow":
            case "rainbow2":
                $Words = str_split_unicode(html_entity_decode($content, ENT_QUOTES, 'UTF-8'));
                $Splitter = '';
                            
                foreach($Words as $Word)
                {
                    /*
                    $RandomRed = rand(0, 255);
                    $RandomGreen = rand(0, 255);
                    $RandomBlue  = rand(0, 255);
                    
                    $InvertedRed = 255 - $RandomRed;
                    $InvertedGreen = 255 - $RandomGreen;
                    $InvertedBlue = 255 - $RandomBlue;
                    */

                    $rainbowCounter++;

                    $randomHue = $rainbowCounter * 24 % 360;
                    $randomSaturation = rand(0, 5) + 95;
                    $randomLuminosity = rand(0, 24) + 44;

                    $invertedHue = ($randomHue + 180) % 360;

                    //background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);
                    //$Stuff .= "<span style='color:rgb($RandomRed, $RandomGreen, $RandomBlue); background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);'>$Word</span>$Splitter";

                    $Stuff .= "<span style='font-size:110%; color:hsla($randomHue, $randomSaturation%, $randomLuminosity%, 0.8); text-shadow:1px 1px #000; background-color:hsla($invertedHue, $randomSaturation%, $randomLuminosity%, 0.6);'>".$Word."</span>$Splitter";
                }
                            
                return $Stuff;
            break;

            case "snow":
                return '<script type="text/javascript" src="/snowstorm.js"></script>';
            break;
                    
            case "rainbow":
                $Words = str_split_unicode(html_entity_decode($content, ENT_QUOTES, 'UTF-8'));
                $Splitter = '';
                            
                foreach($Words as $Word)
                {
                    /*
                    $RandomRed = rand(0, 255);
                    $RandomGreen = rand(0, 255);
                    $RandomBlue  = rand(0, 255);
                    
                    $InvertedRed = 255 - $RandomRed;
                    $InvertedGreen = 255 - $RandomGreen;
                    $InvertedBlue = 255 - $RandomBlue;
                    */

                    $rainbowCounter++;

                    $randomHue = -1 * $rainbowCounter * 24 % 360;
                    $randomSaturation = rand(0, 5) + 95;
                    $randomLuminosity = rand(0, 24) + 44;

                    $invertedHue = ($randomHue + 180) % 360;

                    //background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);
                    //$Stuff .= "<span style='color:rgb($RandomRed, $RandomGreen, $RandomBlue); background-color:rgb($InvertedRed, $InvertedGreen, $InvertedBlue);'>$Word</span>$Splitter";

                    $Stuff .= "<span style='font-size:110%; color:hsla($randomHue, $randomSaturation%, $randomLuminosity%, 0.8); text-shadow:1px 1px #000;'>".$Word."</span>$Splitter";
                }
                            
                return $Stuff;
            break;
            
            case "glitch":
                $Splitter = ' ';
                $Words = preg_split("/$Splitter/", $content);
                
                if(count($Words) == 1)
                {
                    $Words = str_split($content);
                    $Splitter = '';
                }
                
                foreach($Words as $Word)
                {
                    $rainbowCounter++;

                    $randomHue = rand(0, 360);
                    $randomSaturation = rand(0, 5) + 95;
                    $randomLuminosity = rand(0, 44) + 14;

                    $invertedHue = ($randomHue + 180) % 360;

                    $Stuff .= "<span style='font-size:110%; color:hsl($randomHue, $randomSaturation%, $randomLuminosity%); background-color:hsl($invertedHue, $randomSaturation%, $randomLuminosity%);'>$Word</span>$Splitter";
                }
                
                return $Stuff;
            break;

            case "anchor":
                return "<a name='$content'>&nbsp;</a>";
            break;

            case "codepen":
                return embed_codepen($content);
            break;

            case "fb":
            case "fishbux":
                list($amount, $image) = explode("|", $content, 2);

                // Make sure image is an integer
                $image = (int)$image;

                // If none is set, generate a random image
                if(!$image)
                    $image = mt_rand(2, 4);
                
                return "<div class='fishbux'> {$amount}  <div class='wrap'><img src='/upload/fishbux/bux{$image}.gif'></div></div>";
            break;

            case "nsfw":
                return "<div class='nsfw'><span class='message'>NSFW content hidden.<p>(Click to show)</p></span>{$content}</div>";
            break;

            case "snip":
            case "hide":
                return "<div class='snip'><span class='message'>[ <a>Read More</a> ]</span> <div class='stuff'>{$content}</div></div>";
            break;

            default:
                return "$tag, $content";
            break;
        }
    }
    else
    {
        // Self documenting!
        if($tag)
            return $tag."&#91;&#93;";
    }
}

function basic_link($matches)
{
    return replace_links($matches, 'basic');
}

function custom_link($matches)
{
    return replace_links($matches, 'custom');
}

function replace_links($matches, $mode)
{
    // Replace spaces with hyphens
    $page = str_replace(" ", "-", $matches[1]);
    
    // Remove invalid characters from URLs
    $invalid = array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;");
    $page = str_replace($invalid, "", $page);

    // Don't check if the page exists when linking to special pages
    if(!(strpos($page, "?") !== false))
    {
        // Check if the page exists
        $escaped_page = mysql_real_escape_string($page);
        $page_query = mysql_query("Select ID from `Wiki_Pages` where `Path`='{$escaped_page}'");
        list($page_exists) = mysql_fetch_array($page_query);

        if(empty($page_exists))
        {
            $class = "broken";
        }
    }
    
    if(isset($_GET['random']))
        $random = "/?random";

    if($mode == "custom")
    {
        return "<a href='https://wiki.wetfish.net/{$page}{$random}' class='$class'>{$matches[2]}</a>";
    }
    else
    {
        return "<a href='https://wiki.wetfish.net/{$page}{$random}' class='$class'>{$matches[1]}{$matches[2]}</a>";
    }
}

function replace_lists($matches)
{
    $type = $matches[1];
    $list = explode("\n", trim($matches[0]));

    foreach($list as $index => $row)
    {
        $row = preg_replace("/^\s*[-*#]\s*/", "", $row);

        if($row)
        {
            $list[$index] = "<li>{$row}</li>";
        }
    }

    $list = implode("", $list);

    if($type == '#')
    {
        return "<ol>{$list}</ol>";
    }
    else
    {
        return "<ul>{$list}</ul>";
    }
}

?>
