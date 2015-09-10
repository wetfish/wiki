<?php

// Shouldn't these do some sort of filtering / sanitization?
function embed_youtube($input)
{
    $url = parse_url($input);
    $query = array();

    if($url['host'] == 'youtu.be')
    {
        $query['v'] = trim($url['path'], '/');
    }
    else
    {
        parse_str($url['query'], $query);
    }
    
    return "<iframe width='640' height='360' src='https://www.youtube.com/embed/{$query['v']}' frameborder='0' allowfullscreen></iframe>";
}

function embed_vimeo($input)
{
    $url = parse_url($input);
    $videoID = preg_replace("/[^0-9]/", "", $url['path']);

    return "<iframe src='https://player.vimeo.com/video/{$videoID}?byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff' width='640' height='360' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
}

function embed_vine($input)
{
    $url = parse_url($input);
    return "<iframe src='https://vine.co/{$url['path']}/embed/simple?audio=1' width='600' height='600' frameborder='0'></iframe>";
}

function embed_html5($input, $options = array())
{
    $default = array('controls' => true);
    $options = array_merge($default, $options);
    $attributes = array();

    foreach($options as $option => $value)
    {
        if($value)
        {
            if($value === true)
                array_push($attributes, $option);
            else
                array_push($attributes, "$option='$value'");
        }
    }

    $attributes = implode(' ', $attributes);
    return "<video $attributes><source src='/{$input}'></video>";
}

function embed_ted($ted)
{
    $url = parse_url($ted);

    if(preg_match("/(^|\.)ted\.com$/i", $url['host']))
    {
        if(preg_match("/\.html$/", $url['path']))
        {
            $url['path'] = preg_replace("/\.html$/", "", $url['path']);
        }

        return "<iframe src='https://embed-ssl.ted.com{$url['path']}' width='854' height='480' frameborder='0' scrolling='no' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
    }
 }

function embed_codepen($input)
{
    $url = parse_url($input);
    $path = str_replace('/pen/', '/embed/', $url['path']);

    return "<iframe height='420' scrolling='no' src='https://codepen.io/{$path}?height=420&theme-id=0&default-tab=result' frameborder='no' allowtransparency='true' allowfullscreen='true' style='width: 100%;'></iframe>";
}

function embed_soundcloud($input)
{
    $url = parse_url($input);
    parse_str($url['query'], $get);

    if(preg_match('{/sets/}', $url['path']))
    {
        $endpoint = 'playlists';
    }
    else
    {
        $endpoint = 'tracks';
    }

    $options = "&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true";
    $src = "https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/{$endpoint}/{$get['id']}{$options}";
    return "<iframe width='100%' height='166' scrolling='no' frameborder='no' src='{$src}'></iframe>";
}

?>
