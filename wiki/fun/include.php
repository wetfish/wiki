<?php

include(dirname(__FILE__) . "/../src/mysql.php");

function Redirect($URL, $Time=2)
{
	return "<meta http-equiv='refresh' content='$Time;url=$URL'>";
}


?>
