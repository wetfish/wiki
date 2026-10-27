<?php

// The visitor's address; nginx takes it from the X-Real-IP header sent by
// the wetfish/server proxy, which visitors can't fake
$userIP = $_SERVER['REMOTE_ADDR'];

// Make sure the user IP is sanitized, keeping IPv6 addresses whole
$userIP = preg_replace('/[^0-9a-fA-F.:]/', '', $userIP);

$hostname = gethostbyaddr($userIP);

// Replace the first half of a hostname with a hash
$hostname = explode('.', $hostname);
$hostname = array_slice($hostname, floor(count($hostname) / 2));
$hostname = substr(hash('whirlpool', $userIP), 0, 8) . "." . implode(".", $hostname);

echo "Your wetfish hostname is...<br />$hostname";

?>
