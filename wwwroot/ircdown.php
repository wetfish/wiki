<?php

exec('ps ax | grep Unreal', $IsItRunning);

foreach($IsItRunning as $Line)
{
	if(strpos($Line, '/home/rachel/Unreal3.2/src/ircd') !== FALSE)
		$Running = 'Yeah!';
}

if($Running)
	echo "IRC is having a great time :)";
else
{
	chdir('/home/rachel/Unreal3.2');
	exec('./unreal start /dev/null &');

	echo "IRC was down, but not anymore!";
}
?>