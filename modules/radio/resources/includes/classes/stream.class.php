<?php
class stream
{

function get_start($station = "0", $playlist = "0", $more = "0", $laast_pl_file = "0", $debug)
{
if($debug){ echo "Starting getting start!<br>"; }
if($laast_pl_file == ""){$laast_pl_file = "0";}
	if ($station != "0" && $playlist != "0"){
		$file2 = "includes/playlist/$station/$playlist.data";

if (file_exists($file2)) {
if($debug){ echo "Getting start file ok!<br>"; }

$fp = fopen($file2, 'rb');
$filedata = fread($fp, 262144);
fclose($fp);
$filedata2 = explode(";", $filedata);
$laaste = count($filedata2) - 1;
$filedata_laast = explode("&", $laaste);
$laast_one = $filedata_laast[0];
$teller = 0;
$clock = time();
$stop = "";
$go_to_next = "0";
while ($stop == ""){

$out = explode('&', $filedata2[$teller]);

if($clock >= $out[2] or $go_to_next == "1")
{
 if($clock <= $out[3] or $go_to_next == "1")
{
if($debug){ echo "Starting getting audio found!<br>"; }
$kbs =  $out[1] / 8 * 1000;
$more = ($kbs / 100) * $more;
$kbs = $kbs + $more;
$on = $clock - $out[2];
$kbps = $out[1];
$file = $out[0];

if($debug){ echo "getting start [$laast_pl_file - $file]!<br>"; }
if($laast_pl_file == $file && $go_to_next != "1")
{
$go_to_next = "1";
}else{
$stop = 1;
$go_to_next = "0";
}
 }
}
If($out[0] == "" && $teller >= 1){$stop =1; $error=1;}
$teller++;
}
 if ($error == "")
 {

 	$out_a = explode('&', $filedata2[$teller]);
 	$next = $out_a[0];
 	if ($next == ""){$out_a = explode('&', $filedata2[0]);
 	$next = $out_a[0];}

$kbs = round($kbs);
if ($station != "0"){
if (file_exists("includes/live/$station/live.data")){
	$live_fp = fopen("includes/live/".$station."/live.data", "rb");
	$live_data = fread($live_fp, filesize("includes/live/".$station."/live.data"));
	fclose($live_fp);

	$live_out = explode("&", $live_data);
	$file = "includes/live/".$station."/".$live_out[2].".mp3";
	$file_fp = fopen($file, "rb");
		clearstatcache();
	$fstat = fstat($file_fp);
			$filesize = $fstat[size];
			fclose($file_fp);

	$kbs = ($live_out[1] / 8) * 1000;
	$humm_temp = round($filesize / $kbs);
	$on = $humm_temp - 5;
	if ($on <= 1)
	{
		$on = "0";
	}
}
}
$data = "$file&$on&$kbs&$next";
return $data;
}else {return "2";}
 
}else {return "0";}
}else {return "2";}
return "2";
}






function play_playlist($station = "0", $file = "0", $kbs = "0", $over = "0", $enable_metadata = "0", $time_playing = "0", $burst_rate = "1", $debug = "false", $next = "0")
{

	$over = round($over);
	$kbs = round($kbs);
if (file_exists("includes/live/$station/live.data")){
	if ($burst_rate >= 3)
	{
		$time_playing = $time_playing - 15;
	}
	}

clearstatcache();

	if ($file != "0" && $kbs != "0")
	{

		if (file_exists($file)) {
			$teller = NULL;
			$teller2 = NULL;
			$fp = NULL;
			$laast_played = $file;

			$out2 = explode("/", $file);
 			$laast = count($out2) - 1;
 			$songname = explode(".", $out2[$laast]);
 			$out23 = explode("/", $next);
 			$laast3 = count($out23) - 1;
 			$next = explode(".", $out23[$laast3]);
 			$title = "StreamTitle='".$songname[0].$timeleft."';\n\0";
 			$tailletitle = strlen($title);
			$headersize = ceil((float)$tailletitle / 16.0);
			$headerbyte = chr($headersize);
			$header = $headerbyte.$title;
			$fp = fopen($file, "r");
			$fstat = fstat($fp);
			$filesize = $fstat[size];
				if ($time_playing >= "1")
					{
						$resume = $kbs * $time_playing;
						fseek($fp, $resume);
						$to_go = $filesize - ftell($fp);
					}else {$to_go = $filesize;}
			$burst_bits = $kbs * $burst_rate;
			$once_br = "0";
			$once = "0";
			$go = 1;
			if ($debug){
				echo "[starting] [$to_go] [$kbs] [$resume] [".ftell($fp)."] [$filesize]<br>";
				}
				while (!feof($fp)) {
					if (file_exists("includes/live/$station/live.data") && $songname[0] == "live" or file_exists("includes/live/$station/live.data") && $songname[0] == "live_ghost"){
						clearstatcache();
						$fstat = fstat($fp);
						$filesize = $fstat[size];
						$to_go = $filesize - ftell($fp);
						}

				$teller_w++;
					if ($teller_w == "1")
					{
					$title_dat = " - ";
					}elseif($teller_w == "2"){
					$title_dat = " -- ";
					}elseif($teller_w == "3"){
					$title_dat = " --- ";
					}elseif($teller_w == "4"){
					$title_dat = " ---- ";
					}elseif($teller_w >= "5"){
					$title_dat = " ----- ";
					$teller_w = "0";
					}
			$tailletitle = strlen($title);
			$headersize = ceil((float)$tailletitle / 16.0);
			$headerbyte = chr($headersize);
			$header = $headerbyte.$title;


					set_time_limit(0);
				
						if ($teller == 1)
						{
							$title = "StreamTitle='Now".$title_dat."';\n\0";
							}
							if ($teller == 2)
						{
							$title = "StreamTitle='Playing".$title_dat."';\n\0";
							}
						if ($teller >= 3 && $teller <= 15)
						{
						$time_to_song_ends = round($to_go / $kbs);
						$timeleft = " [$time_to_song_ends] ";
						$title = "StreamTitle='".$songname[0].$timeleft."".$title_dat."';\n\0";

						}if ($teller == 16)
						{
							$title = "StreamTitle='Next".$title_dat."';\n\0";
						}
						if ($teller == 17)
						{
							$title = "StreamTitle='Song".$title_dat."';\n\0";
						}
						if ($teller >= 18)
						{
							$title = "StreamTitle='".$next[0]."".$title_dat."';\n\0";
							$teller2++;
						if ($teller2 >= 3){$teller = "0"; $teller2 = "0";}
						}

 			$tailletitle = strlen($title);
			$headersize = ceil((float)$tailletitle / 16.0);
			$headerbyte = chr($headersize);
			$header = $headerbyte.$title;




					if ($to_go >= $kbs)
					{
					if ($once == "0" && $to_go >= $over && $over >= "1"){$data .= fread($fp, $over); $to_go = $to_go - $over; if ($enable_metadata == "1"){$data .= $header; for($i=$tailletitle; $i<($headersize*16); $i++)$data .= chr(65);} $once = "1";}
					$data .= fread($fp, 8192);
					$to_go  = $to_go - 8192;
					$done = $done + 8192;
				if ($enable_metadata == "1"){ $data .= $header;
				for($i=$tailletitle; $i<($headersize*16); $i++)$data .= chr(65);
				}
				if (file_exists("includes/live/$station/live.data") && $songname[0] != "live" or file_exists("includes/live/$station/live.data") && $songname[0] == "live_ghost"){
					if ($teller_waitq >= 10)
					{
					$teller_wait = "0";
					fclose($fp);
					return "0&$file";
					}
					}
					if ($once_br == "0")
					{
						if ($burst_bits <= $to_go)
					{
						if ($done >= $burst_bits)
						{

							if ($debug){
								echo "[burst] [$done] [$to_go] [$enable_metadata]<br>";
								}else {print($data);}
							$teller++;
		$go = 1;
			$data = "";
			$done = "";
			flush();
			sleep(1);
			$once_br = "1";
			}
			}	
					}else {
						if ($done >= $kbs)
						{

								if ($debug){
								echo "[streaming normale] [$done] [$to_go] [$enable_metadata]<br>";
								}else {print($data);
							}
							$go = 1;
							$teller++;
			$data = "";
			$done = "";
			flush();
			sleep(1);
				if (file_exists("includes/live/$station/live.data") && $songname[0] != "live" or file_exists("includes/live/$station/live.data") && $songname[0] == "live_ghost"){
		$teller_waitq++;
		}

			}
			}
			}else {

			if ($to_go <= 8192)
			{

				if ($debug){

								echo "[finsching_1] [$done] [$to_go] [$enable_metadata] [$over]<br>";
								$stop = "1";
								if ($to_go >= "1")
								{
								$data .= fread($fp, $to_go);
								}
								}else {
									$go = 1;
									if ($to_go >= "1")
								{
							print(fread($fp, $to_go));
							}
							$stop = "1";
							}
							if ($to_go >= "1")
				{
			$over = 8192 - $to_go;
			}else {fclose($fp); return $over."&".$file;}
							$erro_teller++;
							if ($erro_teller >= 20){fclose($fp); return $over."&".$file;}

			}else {
			$stop = "";
			while ($stop == "")
			{
				if ($to_go >= 8192)
				{
				$to_go = $to_go - 8192;
				$title = "StreamTitle='Next';\n\0";
					$tailletitle = strlen($title);
			$headersize = ceil((float)$tailletitle / 16.0 );
			$headerbyte = chr($headersize);
			$header = $headerbyte.$title;
				$data .= fread($fp, 8192);
					if ($enable_metadata == "1"){$data .= $header;
				for($i=$tailletitle; $i<($headersize*16); $i++)$data .= chr(65);
				}

				}else {$data .= fread($fp, $to_go);

				$over = 8192 - $to_go;

				if ($debug){
								echo "[finsching_2] [$done] [$to_go] [$enable_metadata] [$over]<br>";
								$stop = "1";
								}else {print($data); fclose($fp); return $over."&".$file;}


				}
				
			}	
			}	
			}
	  					}
			}else {exit();}

	}else {return $over."&"."2";}
	$once_a = "1";
	$header = NULL;
	$title = NULL;
	$to_go = NULL;
	fclose($fp);
	return $over."&".$file;
}






function make_header($station = "N/A", $station_genre = "N/A", $station_bitrate = "N/A", $station_site = "N/A", $file = "0", $metadata = "0", $player = "0")
{

header("ICY 200 OK");
header('Content-Disposition: attachment; filename="'.$station.'"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header("icy-name: $station\r\n");
header("icy-genre: $station_genre\r\n");
header("icy-pub: 1\r\n");
header("icy-br: $station_bitrate\r\n");
if ($metadata == "1"){
header("icy-metaint: 8192");
}
if ($player == "qt" or $player == "rp")
{
header('Content-Length: 999000000');	
}
header("icy-url: $station_site\r\n");
header('Content-type: audio/mpeg');
$fp4 = fopen("includes/header.data", 'rb');
print(fread($fp4, 1024));
fclose($fp4);
return "7168";		
}



}
?>
