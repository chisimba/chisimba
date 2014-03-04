<?php

/**
 * @package radio
 * This is the main admin template for radio station
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

$this->setVar('pageTitle',$station);
$this->setVar('bodyParams','bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"');
$url = isset($url) ? trim($url) : '';
//Setup tables
//Main table
$table =$this->newObject('htmltable', 'htmlelements');
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
//layout image holder
$imagesrc = $extras.'admin_logo.gif';
$image ="<img src='{$imagesrc}' alt='Admin Panle' />" ;
$table->addCell($image,null,null,'center',null,'');
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
$link_logout = new link($this->uri(array('action' => 'logout','admin'=>true), 'radio'));
$link_logout->link = $this->objLanguage->languageText('mod_radio_logout','radio');
$link_playlist = new link($this->uri(array('action'=>'playlistadmin','admin'=>true),'radio'));
$link_playlist->link = $this->objLanguage->languageText('mod_radio_playlist','radio');
$link_users = new link($this->uri(array('action'=>'usersadmin','admin'=>true),'radio'));
$link_users->link = $this->objLanguage->languageText('mod_radio_users','radio');
$link_stations = new link($this->uri(array('action'=>'stationsadmin','admin'=>true),'radio'));
$link_stations->link = $this->objLanguage->languageText('mod_radio_stations','radio');
$link_admin = new link($this->uri(array('action'=>'admins','admin'=>true),'radio'));
$link_admin->link = $this->objLanguage->languageText('mod_radio_admins','radio');
$table->addCell('<center>'.$link_logout->show().'| '.$link_playlist->show().'| '.$link_users->show().'| '.$link_stations->show().'| '.$link_admin->show().'|'.'<a href="'.$this->uri(array('action'=>'addsongs','admin'=>true),'radio').'" target="_BLANK">Add songs</a></center>');
$table->endRow();

//layout image
$imagesrc8 = $extras.'layout_08.gif';
$image8 ="<img src='{$imagesrc8}'width='751' height='18' />" ;
$table->addCell($image8);
$table->endRow();
$table->startRow();
$line ='<iframe frameborder="0" height="405" name="frame1" border="0" scrolling="Yes" src="'.$url.'" width="751"></iframe>';
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