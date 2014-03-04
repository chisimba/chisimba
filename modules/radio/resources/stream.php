<?php
include_once('includes/system.inc.php');
$teller_stream = "0";
$mp = $_REQUEST['mediaplayer'];
if($mp == ""){$metadata = "1"; $br = "3";}
elseif($mp == "wmp"){$metadata = "0"; $br = "24";}
elseif($mp == "flash.mp3"){$metadata = "0"; $br = "8";}
elseif($mp == "qt"){$metadata = "0"; $br = "24";}
elseif($mp == "rp"){$metadata = "0"; $br = "24";}
else{$metadata = "1"; $br = "3";}
if($debug == false){$over = $stream->make_header($header_title, $header_genre, $header_bitrate, $header_site, "0", $metadata, $mp);
}
while ($stop == "")
{
	$playlist_name = $playlist->get_playlist_list($station);
	$out = $stream->get_start($station, $playlist_name, 5, $laast_song_played, $debug);
	$re2 = $out;
	if ($re2 != "2")
	{
	$out = explode("&", $out);
	$file = $out[0];
	$time_played = $out[1];
	$bits_pers = $out[2];
	$next = $out[3];
	}else {$file = 0;}
	$console->add_online_user($station);
	if ($debug){echo "In over; $over file: $file time played: $time_played bitpersec: $bits_pers playlist: $playlist_name station: $station {".$re2."}<br>";}
$re = $stream->play_playlist($station, $file, $bits_pers, $over, $metadata, $time_played, $br, $debug, $next, $re[2]);
$re = explode("&", $re);
if ($re[0] >= "0")
{
	$over = $re[0];
	$re[0] = "0";	
}
$laast_song_played = $file;
if ($re[1] == "2") { $return = $playlist->reload($station,$playlist_name,$debug); if ($debug){echo "Reloading station [$return - $station - $playlist_name]<br>"; } }else {$teller_stream = "0";}
if ($teller_stream >= 5) {$stop = "a"; }
$teller_stream++;
if ($debug) {echo $teller_stream." laast played song $laast_song_played<br>";}
flush();
$console->update_online_users();
}
?>
