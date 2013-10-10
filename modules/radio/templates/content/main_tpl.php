<?php

/**
 * @package radio
 * This is the main template for radio station
 */
//initiate objects

$this->loadClass('label', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlarea', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$h3 =$this->newObject('htmlheading', 'htmlelements');

$player_src = $this->getResourcePath('musicplayer.swf','radio');
$live_src = $this->getResourcePath('includes/live/','radio');
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
$station_site = ($station_site.$this->getResourceUri());

//Check to see if the station is online

if(!$this->stats->station_status($station,$playlist_name))
{
	$this->playlist->reload($station,$playlist_name);
}
//If there are any changes refresh
function Redirect($timee, $topage) {
echo "<meta http-equiv=\"refresh\" content=\"{$timee}; url={$topage}\" /> ";
}
$this->setVar('pageTitle',$station);

$script = "<script language=\"JavaScript\">
//<![CDATA[
function MM_jumpMenu(targ,selObj,restore){ //v3.0
eval(targ+\".location='\"+selObj.options[selObj.selectedIndex].value+\"'\");
if (restore) selObj.selectedIndex=0;
}
//]]>
</script>";

$this->appendArrayVar('headerParams',$script);
$this->setVar('bodyParams','bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"');

//Setup tables
//Main table
$table =$this->newObject('htmltable', 'htmlelements');
//Middle table with On /Offline image
$table_mid = $this->newObject('htmltable', 'htmlelements');
$table_mid->width ='0';
//Form table for st6ation selection
$table_form = $this->newObject('htmltable', 'htmlelements');
$table_form->width ='0';
//Setup form
$frm_station = new form('station',$this->uri(array('action' => 'station'), 'radio'));
//Set id for main table
$table->id = "Table_01";
$table->width = "800";
$table->attributes = "align= 'center'";
$table->cellpadding = '0';
$table->cellspacing = '0';
$table->startRow();
//layout image holder
$imagesrc1 = $extras.'layout_01.gif';
$image1 ="<img src='{$imagesrc1}'width='800' height='4' alt='' />" ;
$table->addCell($image1,null,null,null,null,'');
$table->endRow();
$table->startRow();
//This is the middle table with the station dropdown and the on Air or Offline images
$table_mid->startRow();
if($this->stats->station_status($station,$playlist_name)){
	//layout image for Online /Offline
	$imagesrc3 =$extras.'online.gif';
	$image3 ="<img src='{$imagesrc3}'alt='online' />" ;

} else {
	//layout image for Online /Offline
	$imagesrc3 = $extras.'offline.gif';
	$image3 ="<img src='{$imagesrc3}'alt='offline' />" ;

}
$table_mid->addCell($image3,null,null,'center',null,'');
//Setup the dropdown
$dropdown = "<select class=\"searchselect\" name=\"station\" onChange=\"MM_jumpMenu('parent',this,0)\">";
$data = explode("&",$this->stations->get());

$stop = "0";
$teller = "0";
while($stop == "0")
{
$station_n = $data[$teller];
if($station_n != "")
{

	$dropdown .= "<option  value='?module=radio&station=$station_n'";
	if($station_n == $station)
	{
		$dropdown .= "SELECTED";
	}
	$dropdown .= ">$station_n</option>";
}else{
	$stop ="yes";
}
$teller++;
}
$dropdown .= "</select>";
$form_uri = $this->uri(array('action'=>'home'),'radio');
//add this to the form
$table_mid->addCell("<center><table><form action='$form_uri' method='POST'><tr><td>".$dropdown."</td></tr></form></table></center>",null,null);
$table_mid->endRow();
$table->addCell($table_mid->show(),null,null,'center',null,'');
$table->endRow();
$table->startRow();
//layout image for layout
$imagesrc4 = $extras.'layout_04.gif';
$image4 ="<img src='{$imagesrc4}'width='28' height='80' />" ;
$table->addCell($image4,null,'left',null,null,'');
$table->endRow();
$table->startRow();
//layout image
$imagesrc5 = $extras.'layout_05.gif';
$image5 ="<img src='{$imagesrc5}'width='746' height='33' />" ;
$table->addCell($image5);
$table->startRow();

//Navigation links
$link_home = new link($this->uri(array('action' => 'home','staion'=>$station), 'radio'));
$link_home->link = $this->objLanguage->languageText('mod_radio_home','radio');
$link_playlist = new link($this->uri(array('action'=>'songlist','station'=>$station),'radio'));
$link_playlist->link = $this->objLanguage->languageText('mod_radio_playlist','radio');
$link_admin = new link($this->uri(array('action'=>'controlpanel'),'radio'));
$link_admin->link = $this->objLanguage->languageText('mod_radio_admin','radio');
$table->addCell('<center>'.$link_home->show().'| '.$link_playlist->show().'| '.$link_admin->show().'</center>');
$table->endRow();

//layout image
$imagesrc8 = $extras.'layout_08.gif';
$image8 ="<img src='{$imagesrc8}'width='751' height='18' />" ;
$table->addCell($image8);
$table->endRow();
$table->startRow();
$url_mediaplayer = $station_site."playlist.php?type=$station.asx";//$this->uri(array('action'=>'play','type'=>$station.'.asx'),'radio');
$url_winamp = $station_site."playlist.php?type=$station.pls";//$this->uri(array('action'=>'play','type'=>$station.'.asx'),'radio');
$url_quicktime = $station_site."playlist.php?type=$station.qtl";//$this->uri(array('action'=>'play','type'=>$station.'.qtl'),'radio');
$url_realplayer = $station_site."playlist.php?type=$station.ram";//$this->uri(array('action'=>'play','type'=>$station.'.ram'),'radio');
$url_itunes = $station_site."playlist.php?type=$station.pls";//$this->uri(array('action'=>'play','type'=>$station.'.pls'),'radio');
$url_obj1 = $station_site."playlist.php?type=$station.xml&repeat_playlist=true";//$this->uri(array('action'=>'play','type'=>$station.'.xml','repeat_playlist'=>'true'),'radio');
$url_obj2 = $station_site."playlist.php?type=$station.xml&repeat_playlist=true";//$this->uri(array('action'=>'play','type'=>$station.'.xml','repeat_playlist'=>'true'),'radio');

if ($page == "home" or $page == null) {

			$line = "<center><table width=\"%75\"><tr><td>Station Name:</td><td>$station</td></tr>";
			$line .= "<tr><td>Genre:</td><td>$header_genre</td></tr>";
			$line .=  "<tr><td>Biterate:</td><td>$header_bitrate</td></tr>";
			$line .=  "<tr><td>Users Online:</td><td>".$this->stats->get_users_online($station)."</td></tr>";
			$live_stream = $live_src."live.mp3";
			$line .=  "<tr><td>Laast Song:</td><td>".substr(str_replace("_", " ",$this->stats->laast_song($station,$playlist_name)),0,35)."</td></tr>";
			$line .=  "<tr><td>Now Playing:</td><td>".substr(str_replace("_", " ",$this->stats->now_playing($station,$playlist_name)),0,35)."</td></tr>";
			$line .=  "<tr><td>Next Song</td><td> ".substr(str_replace("_", " ",$this->stats->next_song($station,$playlist_name)),0,35)."</td></tr></table>";
			$line .=  '<a href="'.$url_mediaplayer.'"><img src="'.$extras.'mediaplayer_icon.gif" border="0" alt="Windoes Media Player"></a>
			<a href="'.$url_winamp.'"><img src="'.$extras.'winamp_icon.gif"  border="0" alt="Winamp"></a>
			<a href="'.$url_quicktime.'"><img src="'.$extras.'quicktime_icon.gif"  border="0" alt="quicktime Player"></a>
			<a href="'.$url_realplayer.'"><img src="'.$extras.'realplayer_icon.gif"  border="0" alt="RealPlayer"></a>
			<a href="'.$url_itunes.'"><img src="'.$extras.'itunes_icon.gif"  border="0" alt="Itunes"></a>
			<object type="application/x-shockwave-flash" data="'.$station_site.'musicplayer.swf?playlist_url='.$url_obj1.'" width="32" height="20">
			<param name="movie"value="'.$station_site.'musicplayer.swf?playlist_url="'.$url_obj2.'" />
			</object>';
			$line .= "</center>";


}else{
			$line ='<iframe frameborder="0" height="405" name="frame1" border="0" scrolling="Yes" src="'.$songlist.'" width="751"></iframe>';
}
$table->addCell($line);
$table->endRow();
$table->startRow();
//layout image
$imagesrc10 = $extras.'layout_10.gif';
$image10 ="<img src='{$imagesrc10}' width='751' height='28'  />" ;
$table->addCell($image10);
$table->endRow();
$table->startRow();
$table->addCell(null,null,null,null,null,'width="751" height="31" colspan="2"');
$table->endRow();
$table->startRow();
//layout image
$imagesrc = $extras.'spacer.gif';
$image ="<img src='{$imagesrc}' width='26' height='80'  />" ;
$table->addCell($image,null,null,null,null);
$table->endRow();
$table->startRow();
//layout image
$imagesrc = $extras.'spacer.gif';
$image ="<img src='{$imagesrc}' width='746' height='1'  />" ;
$table->addCell($image,null,null,null,null);
$table->endRow();
$table->startRow();
//layout image
$imagesrc = $extras.'spacer.gif';
$image ="<img src='{$imagesrc}' width='5' height='1'  />" ;
$table->addCell($image,null,null,null,null);
$table->endRow();
$table->startRow();
//layout image
$imagesrc = $extras.'spacer.gif';
$image ="<img src='{$imagesrc}' width='23' height='1'  />" ;
$table->addCell($image,null,null,null,null);
$table->endRow();
echo '<center>';
echo $table->show();
echo "</center>";

?>