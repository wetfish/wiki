<?php

$Host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$Host = preg_replace('/\d+-\d+-\d+-\d+/', substr(md5(hash('whirlpool', $AccountName)), 0, 8), $Host);

echo "Your wetfish hostname is...<br />$Host";

?>