<?php
clearstatcache();
#error_reporting(6143);
error_reporting(0);
$version = "1";
include('classes/playlist.class.php');
include('classes/stream.class.php');
include('classes/station.class.php');
include('classes/settings.class.php');
include('classes/console.class.php');
include('classes/stats.class.php');
$stream = new stream;
$playlist = new playlist;
$stations = new stations;
$settings = new settings;
$console = new console;
$stats = new stats;
$console->ban_check();
$station = $_GET['station'];
$key = $_GET['debug'];
$station = $stations->default_s($station);
$playlist_name = $playlist->get_playlist_list($station);
$settings_data = $settings->get($station);
$settings_data_temp = explode("&", $settings_data);
$header_title = $settings_data_temp[0];
$header_genre = $settings_data_temp[1];
$header_bitrate = $stats->bitrate($station, $playlist_name);
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
	function debug($key, $key2)
	{
	if($key != "" && $key2 != "" && $key == $key2)
	{
		return true;
	}else {return false;}
	}
	if (debug($key, $debugkey))
	{
	$debug = true;
	}else {$debug = false;}

?>
