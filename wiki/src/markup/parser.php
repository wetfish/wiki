<?php

function replace_once($search, $replacement, $string)
{
    // We have to use preg_replace instead of str_replace to ensure this match is only replaced once
    return preg_replace("/" . preg_quote($search, "/") . "/", $replacement, $string, 1);
}

function find_markup($input)
{
    $tags = array
    (
        'Ad|Ads',
        'Pre',
        'Flash',
        'Color',
        'Bold|B',
        'Box|Title|Infobox|TitleBox',
        'Underline|U',
        'Italics?|I',
        'Strike|S',
        'Heading|SubHeading',
        'Big',
        'Medium|Med',
        'Small|Sml',
        'URL',
        'Image|IMG',
        'Redirect',
        'Video|Youtube|Vimeo|Vine|Ted',
        'SoundCloud',
        'Load',
        'Music',
        'Snow',
        '(?:Double|Dbl)?Rainbow2?',
        'Glitch',
        'Embed',
        'Center',
        'Right|Left',
        'Playlist',
        'Style',
        'Total',
        'Anchor',
        'Codepen',
        'FB|FishBux',
        'NSFW',
        'Snip|Hide',
        'Date',
    );
    
    $start = array
    (
        '{',
        '\['
    );

    $end = array
    (
        '}',
        '\]'
    );

    $braces = implode('', $start) . implode('', $end);
    $content = '[^'.implode('', $start).']*?';
    $tags = implode('|', $tags);
    $start = implode('|', $start);
    $end = implode('|', $end);

    // Don't treat newlines as whitespace
    $whitespace = "[^\S\n]*";

    // Match all tags, or tag groups
    $tags = "(?:(?:(?:$tags),$whitespace)+)?(?:$tags)";

    // Regex for matching tags with delimiters
    $delimited = "\b($tags)$whitespace([^ $braces])(?:$start)(.*)(?:$end)\\2";

    // Regex for matching regular tags
    $regular = "\b($tags)$whitespace(?:$start)($content)(?:$end)";
    $output = array();

    while(preg_match("/(?:$delimited|$regular)/is", $input, $match))
    {
        $input = replace_once($match[0], "", $input);
        $data = array
        (
            'source' => $match[0],
            'tag' => ($match[1]) ? $match[1] : $match[4],
            'content' => ($match[3]) ? $match[3] : $match[5] 
        );

        if(preg_match("/(?:$delimited|$regular)/is", $data['content']))
        {
            $data['content'] = find_markup($data['content']);
        }

        $output[] = $data;
    }

    return $output;
}

function filter_markup($input)
{
    $output = array();
    
    foreach($input as $object)
    {
        if(is_array($object['content']))
        {
            $filtered = filter_markup($object['content']);

            foreach($filtered as $markup)
            {
                $text .= $markup['text'];
            }
        }
        else
        {
            $text = explode('|', $object['content']);
            $text = array_pop($text);
        }

        $output[] = array
        (
            'source' => $object['source'],
            'text' => $text
        );
    }

    return $output;
}

?>
