<?php

$URL = parse_url($_GET['url']);
$allowed = getenv('ALLOWED_EMBEDS');
$internal = getenv('INTERNAL_HOSTNAME');

// Check if the requested URL matches the internal hostname or the list of allowed URLs
if($URL['host'] == $internal || preg_match($allowed, $URL['host']))
{
    echo file_get_contents($_GET['url']);
}

?>
