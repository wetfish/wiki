<?php

if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
else
    $userIP = $_SERVER['REMOTE_ADDR'];
    
// Make sure the user IP is sanitized
$userIP = preg_replace('/[^0-9.]/', '', $userIP);

$hostname = gethostbyaddr($userIP);

// Replace the first half of a hostname with a hash
$hostname = explode('.', $hostname);
$hostname = array_slice($hostname, floor(count($hostname) / 2));
$hostname = substr(hash('whirlpool', $userIP), 0, 8) . "." . implode(".", $hostname);

echo "Your wetfish hostname is...<br />$hostname";

?>
