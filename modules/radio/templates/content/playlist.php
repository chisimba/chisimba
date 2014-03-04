<?php
//block all skin banners
$this->setVar('pageSuppressToolbar',true);
$this->setVar('pageSuppressSkin',true);
$this->setVar('pageSuppressBanner',true);
$this->setVar('suppressFooter',true);

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

clearstatcache();
$playlist_type = $this->getParam('type');
$station_temp = explode(".",$playlist_type);
if($station_temp[0] != "" && $station_temp != $playlist_type){$station = $station_temp[0]; $playlist_type = $station_temp[1];}
if($playlist_type == ""){
	$playlist_type = "m3u";
}


If($playlist_type == m3u)
{
header("content-type:audio/x-mpegurl;charset=utf-8");
header("Content-Disposition: attachment; filename=$station.m3u");
?>
#EXTM3U
#EXTINF:-1,<?php echo $station; ?>
<?php echo  $this->uri(array('action'=>'stream','station'=>$station,'mediaplayer'=>''),'radio');
}
If($playlist_type == xml)
{
header("content-type:application/xml;charset=utf-8");
header("Content-Disposition: attachment; filename=$station.xml");
?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
	<trackList>

		<track>
			<title><?php echo $station; ?></title>
			<location><?php echo html_entity_decode($this->uri(array('action'=>'stream','station'=>$station,'mediaplayer'=>'flash.mp3'),'radio'));?></location>
		</track>

	</trackList>
</playlist>
<?php
}
if($playlist_type == asx)
{
header("content-type:video/x-ms-asf;charset=utf-8");
header("Content-Disposition: attachment; filename=$station.asx");
?>
<asx version="3.0">

  <entry>
    <title><?php echo $station; ?></title>
    <ref href="<?php echo html_entity_decode($this->uri(array('action'=>'stream','station'=>$station,'mediaplayer'=>'wmp'),'radio')); ?>">
    <copyright>�2007 <?php echo $station; ?></copyright>
  </entry>
</asx>
<?php
}


if($playlist_type == pls)
{
header("content-type:audio/x-scpls;charset=utf-8");
header("Content-Disposition: attachment; filename=$station.pls");
?>
[Playlist]\n
NumberOfEntries=1\n
File1=<?php echo html_entity_decode($this->uri(array('action'=>'stream','station'=>$station,'mediaplayer'=>''),'radio')); ?>\n
Title1=<?php echo $station; ?>\n
Length1=-1\n
Version=2
<?php
}
if($playlist_type == qtl)
{
header("content-type:application/x-quicktime-media-link;charset=utf-8");
header("Content-Disposition: attachment; filename=$station.QTL");
echo "<?xml version=\"1.0\"?>
<?quicktime type=\"application/x-quicktime-media-link\"?>";
?>
<embed
 autoplay="true"
 moviename="<?php echo $station; ?>"
 src="<?php echo html_entity_decode($this->uri(array('action'=>'stream','station'=>$station,'mediaplayer'=>'qt&p=.mp3'),'radio')); ?>"
 />
<?php
}
if($playlist_type == ram)
{
header("content-type:audio/x-pn-realaudio;charset=utf-8");
header("Content-Disposition: attachment; filename=$station.ram");
?>
<?php echo html_entity_decode($this->uri(array('action'=>'stream','station'=>$station,'mediaplayer'=>'rp'),'radio')); ?>
<?php
}
?>
