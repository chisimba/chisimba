<?php

$teller_stream = "0";
$mp = $this->getParam('mediaplayer');
$station = $this->getParam('station');

$playlist_name = $this->playlist->get_playlist_list($station);
$settings_data = $this->settings->get($station);
$settings_data_temp = explode("&", $settings_data);
$header_title = $settings_data_temp[0];
$header_genre = $settings_data_temp[1];
$header_bitrate = $this->stats->bitrate($station, $playlist_name);
if ($header_bitrate == "0" or $header_bitrate == "")
{
	$header_bitrate = $settings_data_temp[2];
}
$header_site = $settings_data_temp[3];
$debugkey = $settings_data_temp[4];
$site_temp = explode("/", $_SERVER["PHP_SELF"]);
$laast_one = count($site_temp) -1;
$between = str_replace($site_temp[$laast_one], "", $_SERVER["PHP_SELF"]);
$station_site = "http://".$_SERVER["HTTP_HOST"].$between;

if($mp == ""){$metadata = "1"; $br = "3";}
elseif($mp == "wmp"){$metadata = "0"; $br = "24";}
elseif($mp == "flash.mp3"){$metadata = "0"; $br = "8";}
elseif($mp == "qt"){$metadata = "0"; $br = "24";}
elseif($mp == "rp"){$metadata = "0"; $br = "24";}
else{$metadata = "1"; $br = "3";}
if($debug == false){$over = $this->stream->make_header($header_title, $header_genre, $header_bitrate, $header_site, "0", $metadata, $mp);
}
while ($stop == "")
{
	$playlist_name = $this->playlist->get_playlist_list($station);
	$out = $this->stream->get_start($station, $playlist_name, 5, $laast_song_played, $debug);
	$re2 = $out;
	if ($re2 != "2")
	{
	$out = explode("&", $out);
	$file = $out[0];
	$time_played = $out[1];
	$bits_pers = $out[2];
	$next = $out[3];
	}else {$file = 0;}
	$this->console->add_online_user($station);
	if ($debug){echo "In over; $over file: $file time played: $time_played bitpersec: $bits_pers playlist: $playlist_name station: $station {".$re2."}<br>";}
$re = $this->stream->play_playlist($station, $file, $bits_pers, $over, $metadata, $time_played, $br, $debug, $next, $re[2]);
$re = explode("&", $re);
if ($re[0] >= "0")
{
	$over = $re[0];
	$re[0] = "0";
}
$laast_song_played = $file;
if ($re[1] == "2") { $return = $this->playlist->reload($station,$playlist_name,$debug); if ($debug){echo "Reloading station [$return - $station - $playlist_name]<br>"; } }else {$teller_stream = "0";}
if ($teller_stream >= 5) {$stop = "a"; }
$teller_stream++;
if ($debug) {echo $teller_stream." laast played song $laast_song_played<br>";}
flush();
$this->console->update_online_users();
}
?>
