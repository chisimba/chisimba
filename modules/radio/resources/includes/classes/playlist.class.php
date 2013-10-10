<?php
class playlist
{

function creat_playlist($station_name = "0", $playlist_name = "0")
{	

if ($station_name != "0" && $playlist_name != "0")
{
	if (!is_dir("includes/playlist/".$station_name))
	{
		mkdir("includes/playlist/".$station_name, 0777);

	}
if ($fp = fopen("includes/playlist/".$station_name."/".$playlist_name.".data", "w+"))
{
fclose($fp);
return "0";
}else {return "0";}	
}else {return "0";}

}

function get($station = "0")
{
if ($station != "0")
	{
	if (!is_dir("includes/playlist/".$station))
	{
		mkdir("includes/playlist/".$station, 0777);
		}
if ($handle=opendir("includes/playlist/".$station)){
while (false !== ($file = readdir($handle))) {
    if ($file != "." && $file != "..") {
    	$file = str_replace(".data", "", $file);
        $data .= "$file&";

    }
}
closedir($handle);
}



		}
		return $data;
}


function get_playlist_list($station = "0")
{
	if ($station != "0")
	{
		if (file_exists("includes/playlist/".$station."/".date('l').".data")) {
			return date('l');
			}
			elseif (file_exists("includes/playlist/".$station."/default.data")) {

			return "default";
			}else {
					if (!is_dir("includes/playlist/".$station))
	{
		mkdir("includes/playlist/".$station, 0777);

	}
		if ($handle=opendir("includes/playlist/$station")){
while (false !== ($file = readdir($handle))) {
    if ($file != "." && $file != "..") {
    	$file = str_replace(".data", "", $file);
        $data .= "$file";

    }
}
closedir($handle); 
}else {return 0;}
if ($data == ""){$fp = fopen("includes/playlist/".$station."/"."default.data", "w+"); fclose($fp); $data = "default&";}
return $data;
}
	}else {return 0;}
}


function sec2hms ($sec, $padHours = false)
  {

    // holds formatted string
    $hms = "";
    
    // there are 3600 seconds in an hour, so if we
    // divide total seconds by 3600 and throw away
    // the remainder, we've got the number of hours
    $hours = intval(intval($sec) / 3600); 

    // add to $hms, with a leading 0 if asked for
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';
     
    // dividing the total seconds by 60 will give us
    // the number of minutes, but we're interested in 
    // minutes past the hour: to get that, we need to 
    // divide by 60 again and keep the remainder
    $minutes = intval(($sec / 60) % 60); 

    // then add to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

    // seconds are simple - just divide the total
    // seconds by 60 and keep the remainder
    $seconds = intval($sec % 60); 

    // add to $hms, again with a leading 0 if needed
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;

  }

function build_list($replace_what = "0", $replace_with= "0", $max = "0")
{
	if ($max == ""){$max = "0";}
$stop = "0";
$teller = "0";
$data = "";
while ($stop == "0")
{
$data .= $teller. "&";
if ($teller >= $max){$stop = "yes";}
$teller++;	
}	
$data = str_replace($replace_with."&", $replace_with."Q"."&", $data);
$data = str_replace($replace_what."&", $replace_with."&", $data);
$data = str_replace($replace_with."Q"."&", $replace_what."&", $data);
return $data;
}

function add_songs($file = "0", $time = "0", $bitrate = "0", $playlist_name = "0", $station_name = "0")
{
	if ($file != "0" && $time != "0" && $bitrate != "0" && $playlist_name != "0" && $station_name != "0")
	{
		if ($playlist_name != "" && $station_name != ""){
			if (!is_dir("includes/playlist/".$station_name))
	{
		mkdir("includes/playlist/".$station_name, 0777);

	}
	if (file_exists("includes/playlist/".$station_name."/".$playlist_name.".data")) {

		$file_data = file_get_contents("includes/playlist/".$station_name."/".$playlist_name.".data");

		}else
		{
			$fp = fopen("includes/playlist/".$station_name."/".$playlist_name.".data", "w+");
			fclose($fp);
		}



		$file_data1 = explode(";", $file_data);
		$laast = count($file_data1) - 2;
		$file_data2 = explode("&", $file_data1[$laast]);
		$file_data3 = $file_data2[3];
		if ($file_data3 == "" or $file_data3 == "0"){$file_data3 = time();}
	$time = explode(":", $time);
	$min_sec = $time[0] * 60;
	$max_time = $min_sec + $time[1];
	$start_time =$file_data3 + 1;
	 $end_time = $start_time + $max_time;



	if ($fp = fopen("includes/playlist/".$station_name."/".$playlist_name.".data", "a"))
{	if (fwrite($fp, $file. "&". $bitrate. "&" .$start_time. "&" .$end_time. ";"))
{return "1";}else {return "0";}
	fclose($fp);

	}else {return "0";}
	}else {return "0";}
	}

}

function del_songs($number = "nothing", $playlist_name = "0", $station_name = "0")
{
if ($number != "nothing" && $playlist_name != "0" && $station_name != "0")
{
if ($fp = fopen("includes/playlist/".$station_name."/".$playlist_name.".data", "r"))
{
$data = fread($fp, 990000);
fclose($fp);
if ($fp = fopen("includes/playlist/".$station_name."/".$playlist_name.".data", "w+"))
{
$data_arry = explode (";", $data);
$stop = "0";
$teller = "0";
$once = "0";
while ($stop == "0")
{
$out = explode("&", $data_arry[$teller]);
$file = $out[0];
$bitrate = $out[1];
$start_time = $out[2];
$end_time = $out[3];
$total_time = $end_time - $start_time;
if ($once == "0"){$end = $start_time; $once = "1";}
if ($file != "")
{
if ($teller != $number)
{
$end_t = $end +$total_time;
$data_out .= $file. "&" .$bitrate. "&" .$end. "&" .$end_t .";";
$end = $end_t;
}
}else
{
	$stop = "yes";
}
$teller++;	
}

if (fwrite($fp, $data_out))
{fclose($fp); return "1";
}else {fclose($fp); return "0";}

}else {return "0";}
}else {return "0";}
}else {return "0";}
}

function move_songs($station = "0", $playlist = "0", $volgorde = "0")
{

if ($station != "0" && $playlist != "0" && $volgorde != "0")
{
	if (file_exists('includes/playlist/'.$station.'/'.$playlist.'.data')){
		clearstatcache();
$filename = "includes/playlist/".$station."/".$playlist.".data";
$handle = fopen($filename, "r");
$fstat = fstat($handle);
$data_out = fread($handle, $fstat[size]);
fclose($handle);
	$stop = "0";
	$out = explode(";", $data_out);
	$teller = "0";
	$once = "0";
	$teller_o = "0";
	while ($stop == "0")
	{
	$out2 = explode("&", $out[$teller]);
	$file = $out2[0];
	$bitrate = $out2[1];
	$start = $out2[2];
	if ($once == "0"){$time_start = $start; $once = "1";}

	$end = $out2[3];
	if ($file != "" && $file != "Array")
	{
		$teller_o++;
	$total_time = $end - $start;

	$file2[$teller] = $file. "&" .$bitrate. "&" .$total_time;

	$teller++;
	}else {$stop = "1";}
	}
	$stop = "0";
	$out3 = explode("&", $volgorde);
	$teller = "0";
	$data = "";

		while ($stop == "0" && $teller_o >= "1")
		{
		$out4 = explode("&", $file2[$out3[$teller]]);
		$file = $out4[0];
		$biterate = $out4[1];
		$total_time = $out4[2];
		$start = $time_start;
		$end = $start + $total_time;
		$time_start = $end;
		if ($file != "" && $file != "Array")
		{
		$data .= $file. "&". $biterate. "&" .$start. "&" .$end. ";";
		}else {$stop = "1";}
		$teller++;
		}
		$fp = fopen("includes/playlist/".$station."/".$playlist.".data", "w+");
		fwrite($fp, $data);
		fclose($fp);
}
}	
}

function del_playlist($station_name = "0", $playlist_name = "0")
{
if ($station_name != "0" && $playlist_name != "0")
{
	if (unlink("includes/playlist/".$station_name."/".$playlist_name.".data"))
	{
		return 1;
	}else {return 0;}

	}
}






function reload($station = "0", $playlist = "0",$debug)
{
	if($debug){echo "Start reloading!<br>";}
if ($station != "0" && $playlist != "0"){
 $file2 = "includes/playlist/$station/$playlist.data";
 if($debug){echo "reloading! [$station - $playlist]<br>";}
 if (file_exists($file2)) {
 	if($debug){echo "reloading file ok!<br>";}
$fp = fopen($file2, 'rb');
$filedata = fread($fp, 262144);
fclose($fp);
 $clock = time();

  unlink($file2);
 $fp4 = fopen($file2, 'w+b');
 $filedata2 = explode(";", $filedata);

$stop = "";
$teller = 0;
while ($stop == ""){
$newtime_end = "";
$newtime_start = "";
$out = explode("&", $filedata2[$teller]);
$songname = $out[0];
$kbps = $out[1];
$time = $out[3] - $out[2];
if ($laast_end == ""){$laast_end = $clock;}
$newtime_end = $laast_end + $time;
$newtime_start = $laast_end;
$laast_end = $newtime_end;
if ($songname != ""){
	if($debug){echo "reloading adding song!<br>";}
$content .= "$songname&$kbps&$newtime_start&$newtime_end;";
}
$teller++;
if ($songname == ""){$stop = 1; $yes = 1;}
	}

if($debug){
	echo "reloading writeing!<br>";
	}
fwrite($fp4, $content);
	fclose($fp4);
	}else {return "0";}
	}else {return "0";}
}

function get_playlist_info($station = "0", $playlist = "0")
{
	if ($station != "0" && $playlist != "0")
	{
		$file2 = "includes/playlist/$station/$playlist.data";
		if (file_exists($file2)) {
		$playlist_open = fopen($file2, "rb");
		$filesize = filesize($file2);
		if ($filesize == "0" or $filesize == ""){$filesize = "1";}
		$playlist_data = fread($playlist_open, $filesize);
		fclose($playlist_open);
		$playlist_data_open_1 = explode(";", $playlist_data);
		$teller = "0";
		$stop = "";
		while ($stop == "")
		{
		$playlist_data_open_2 = explode("&", $playlist_data_open_1[$teller]);
		$filename = $playlist_data_open_2[0];
		if ($filename != ""){
		$bitrate = $playlist_data_open_2[1];
		$start_time = $playlist_data_open_2[2];
		$end_time = $playlist_data_open_2[3];
		$out2 = explode("/", $filename);
 		$laast = count($out2) - 1;
 		$songname = explode(".", $out2[$laast]);
 		$tottaltime = $end_time - $start_time;
 		$tottaltime  = playlist::sec2hms($tottaltime);
 		$endtime = $end_time - time();
		$endtime = playlist::sec2hms($endtime);
		if (time() >= $start_time)
		{
		if (time() <= $end_time)
		{
		$data .= $songname[0]. "&" .$bitrate. "&$tottaltime&$endtime Playing;";
		}else {$ago = time() - $end_time; $ago = playlist::sec2hms($ago); $data .= $songname[0]. "&" .$bitrate. "&$ago;";}
		}else {$togo = $start_time - time(); $togo = playlist::sec2hms($togo); $data .= $songname[0]. "&" .$bitrate. "&$togo;";}
		$teller++;
		}else {$stop = "1";}
		}

		}else {return "0";}
	}else {return "0";}
return $data;	
}
}
?>
