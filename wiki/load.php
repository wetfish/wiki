<?php

$URL = parse_url($_GET['url']);
require('simple_html_dom.php');
require('cssparser.php');

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

$site_url = getenv('SITE_URL');
$match = '/' . preg_quote($site_url) . '$/i';

if(preg_match($match, $URL['host']))
{
    echo file_get_contents($_GET['url']);
}

?>
