<?php

$URL = parse_url($_GET['url']);
$allowed = getenv('ALLOWED_EMBEDS');

if(preg_match($allowed, $URL['host']))
{
    echo file_get_contents($_GET['url']);
}

?>
