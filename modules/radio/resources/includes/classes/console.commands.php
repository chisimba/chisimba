<?php

if($command == "settings")
{
	$station = $data_temp[1];
	$out = settings::get($station);
	return $out;
}

?>