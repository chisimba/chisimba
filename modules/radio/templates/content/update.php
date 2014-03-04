<?php
if($_SESSION['id'] == "1"){
	exit();
	}
	$url = "";
	$fp = fopen($url, "r");
	while(!feof($fp))
	{
		$data = fread($fp ,500);
	}
	
?>