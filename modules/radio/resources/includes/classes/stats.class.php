<?php
class stats
{

function get_users_online($staion)
{
	$teller = "0";
	if ($handle = opendir('includes/users')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            if ($handle2 = opendir('includes/users/'.$file)) {
    while (false !== ($file2 = readdir($handle2))) {
        if ($file2 != "." && $file2 != "..") {
        	$temp = explode(".",$file2);
        	if($temp[0] == $staion){
        	if($temp[1] <= time()){ unlink("includes/users/".$file."/".$staion.".".$file2.".data"); }else{
            	$teller++;}
                    }
                    }
    }
    closedir($handle2);
}
            	
           
        }
    }
    closedir($handle);
}
return $teller;
}

function get_users_online_names($staion)
{
	$teller = "0";
	if ($handle = opendir('includes/users')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            if ($handle2 = opendir('includes/users/'.$file)) {
    while (false !== ($file2 = readdir($handle2))) {
        if ($file2 != "." && $file2 != "..") {
        	$temp = explode(".",$file2);
        	if($temp[0] == $staion){
        $data .= $file."&";
		}
                    }
                    }
    }
    closedir($handle2);

            	
           }
        }
    }
    closedir($handle);

return $data;
}

function station_status($station = "0", $playlist = "0")
{
	if($station  != "0" && $playlist != "0"){
if (file_exists("includes/playlist/".$station."/".$playlist.".data")) {
	$data = file_get_contents("includes/playlist/".$station."/".$playlist.".data");
	$stop = "0";
	$out = explode(";",$data);
	$teller = "0";
	while($stop == "0")
	{
	$out2= explode("&",$out[$teller]);	
	$file = $out2[0];
	if($file != "")
	{
	$start_time = $out2[2];
	$stop_time =$out2[3];
	if(time() >= $start_time)
	{
		if(time() <= $stop_time)
		{
			return true;
		}
	}	
		
	}
	else
	{
		$stop = "yes";
	}
	$teller++;
	}
	}	
	if(file_exists("includes/live/$station/live.data")){
		return true;
		}
	return false;
	}
}

function now_playing($station = "0", $playlist = "0")
{
$song = "0";
if($station  != "0" && $playlist != "0"){
if (file_exists("includes/playlist/".$station."/".$playlist.".data")) {
	$data = file_get_contents("includes/playlist/".$station."/".$playlist.".data");
	$stop = "0";
	$out = explode(";",$data);
	$total_files = count($out);
	$teller = "0";
	while($stop == "0")
	{
	$out2= explode("&",$out[$teller]);	
	$file = $out2[0];
	if($file != "")
	{
	$start_time = $out2[2];
	$stop_time =$out2[3];
	if(time() >= $start_time)
	{
		if(time() <= $stop_time)
		{
				if(file_exists("includes/live/$station/live.data")){
		return "LIVE";
		}
			$out3 = explode("/",$file);
			$laast = count($out3) -1;
			$song2 = explode(".",$out3[$laast]);
			$song = $song2[0];
			return $song;
		}
	}	
		
	}else{$stop = "yes";}
	$teller++;
	}
	}	
	if(file_exists("includes/live/$station/live.data")){
		return "LIVE";
		}
	return $song;
	}
}

function bitrate($station = "0", $playlist = "0")
{
$song = "0";
if($station  != "0" && $playlist != "0"){
if (file_exists("includes/playlist/".$station."/".$playlist.".data")) {
	$data = file_get_contents("includes/playlist/".$station."/".$playlist.".data");
	$stop = "0";
	$out = explode(";",$data);
	$teller = "0";
	while($stop == "0")
	{
	$out2= explode("&",$out[$teller]);	
	$file = $out2[0];
	if($file != "")
	{
	$start_time = $out2[2];
	$stop_time =$out2[3];
	if(time() >= $start_time)
	{
		if(time() <= $stop_time)
		{
		
			$bitrate = $out2[1];
			return $bitrate;
		}
	}	
		
	}
	else
	{
		$stop = "yes";
	}
	$teller++;
	}
	}	
	return 0;
	}
}

function laast_song($station = "0", $playlist = "0")
{
$song = "0";
if($station  != "0" && $playlist != "0"){
if (file_exists("includes/playlist/".$station."/".$playlist.".data")) {
	$data = file_get_contents("includes/playlist/".$station."/".$playlist.".data");
	$stop = "0";
	$out = explode(";",$data);
	$teller = "0";
	while($stop == "0")
	{
	$out2= explode("&",$out[$teller]);	
	$file = $out2[0];
	if($file != "")
	{
	$start_time = $out2[2];
	$stop_time =$out2[3];
	if(time() >= $start_time)
	{
		if(time() <= $stop_time)
		{
		
			$out2 = explode("&",$out[$teller - 1]);	
			$file = $out2[0];
			if($file == ""){ $total = count($out) -2;  $out2= explode("&",$out[$total]);	
			$file = $out2[0]; }
			$out3 = explode("/",$file);
			$laast = count($out3) -1;
			$song2 = explode(".",$out3[$laast]);
			$song = $song2[0];
			return $song;
		}
	}	
		
	}
	else
	{
		$stop = "yes";
	}
	$teller++;
	}
	}	
	return $song;	
	}
}

function next_song($station = "0", $playlist = "0")
{
$song = "0";
if($station  != "0" && $playlist != "0"){
if (file_exists("includes/playlist/".$station."/".$playlist.".data")) {
	$data = file_get_contents("includes/playlist/".$station."/".$playlist.".data");
	$stop = "0";
	$out = explode(";",$data);
	$teller = "0";
	while($stop == "0")
	{
	$out2= explode("&",$out[$teller]);	
	$file = $out2[0];
	if($file != "")
	{
	$start_time = $out2[2];
	$stop_time =$out2[3];
	if(time() >= $start_time)
	{
		if(time() <= $stop_time)
		{
			$out2= explode("&",$out[$teller + 1]);	
			$file = $out2[0];
			if($file == ""){$out2= explode("&",$out[0]);	
			$file = $out2[0]; }
			$out3 = explode("/",$file);
			$laast = count($out3) -1;
			$song2 = explode(".",$out3[$laast]);
			$song = $song2[0];
			return $song;
		}
	}	
		
	}
	else
	{
		$stop = "yes";
	}
	$teller++;
	}
	}	
	return $song;	
	}
}



}
?>