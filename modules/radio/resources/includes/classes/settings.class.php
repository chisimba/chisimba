<?php
class settings
{
	
	function get($station = "0")
	{
$header_title = "PHP-Radio";
$header_genre = "N/A";
$header_bitrate = "N/A";
$header_site  = "N/A";
$debugkey  = "test";
	if (is_dir("includes/station/".$station)) {
		if (file_exists("includes/station/".$station."/settings.data")) {
			$fp = fopen("includes/station/".$station."/settings.data", "rb");
		$data = fread($fp, filesize("includes/station/".$station."/settings.data"));
		fclose($fp);
		return $data;
			}
			else
			{
			$data = $header_title."&".$header_genre."&".$header_bitrate."&".$header_site."&".$debugkey;	
			}
		}	else
			{
			$data = $header_title."&".$header_genre."&".$header_bitrate."&".$header_site."&".$debugkey;	
			}
			return $data;	
	}
	
	function update($station = "0",$where = "0",$what = "0")
	{
	if (is_dir("includes/station/".$station)) {
		if (file_exists("includes/station/".$station."/settings.data")) {
			$fp = fopen("includes/station/".$station."/settings.data", "rb");
		$data = fread($fp, filesize("includes/station/".$station."/settings.data"));
		fclose($fp);
		$data_out = explode("&",$data);
		$change = $data_out[$where];
		$data  = str_replace($change,$what,$data );
		$fp = fopen("includes/station/".$station."/settings.data", "w+b");
		fwrite($fp, $data);
		fclose($fp);
			}
			else
			{
				return "0";	
			}
		}	else
			{
				return "0";	
			}
			
	}	
	
	
	function add($station = "0", $header_title = "N/A",$header_genre = "N/A",$header_bitrate = "N/A",$header_site = "N/A",$debugkey = "0")
	{
		$station = str_replace(" ","_",$station);
	if($station != "0" && $debugkey != "0")
	{
		
		$data = $header_title."&".$header_genre."&".$header_bitrate."&".$header_site."&".$debugkey;
	if (is_dir("includes/station/".$station)) {
		$fp = fopen("includes/station/".$station."/settings.data", "w+b");
		fwrite($fp, $data);
		fclose($fp);
		
							}
							else{
								mkdir("includes/station/".$station, 0777);
									$fp = fopen("includes/station/".$station."/settings.data", "w+b");
									fwrite($fp, $data);
									fclose($fp);
							}	
	}else{return "0";}
	}
}



?>