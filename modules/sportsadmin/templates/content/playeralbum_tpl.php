<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
/*
* Templet to display photos of the players in the different teams
* @Author : nsabagwa mary
*/
$content = "";
$sportid = $this->getParam('sportid',NULL);
$this->playerfolder = $this->objConfig->getModulePath().'sportsadmin/players/';
$heading = & $this->getObject('htmlheading','htmlelements');
$this->objDbsports = & $this->getObject('dbsports');
$this->loadClass('htmltable','htmlelements');
$imagepath = "chisimba_modules/sportsadmin/players/";//path for the images
$this->objDbplayer = & $this->getObject('dbplayer');
$this->objDbteam =& $this->getObject('dbteam');
$form =& $this->getObject('form','htmlelements');

$sportname = $this->objDbsports->getSportsById($sportid);
$heading->str = $sportname."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_player','sportsadmin')."'s&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_album','sportsadmin');

//get details of the player plus te image file name
$playerdata = $this->objDbplayer->geAll($sportid);

$heading->align = "center";
$content .= $heading->show();

$table = new htmltable();
$table->width = "40%";

if(!empty($playerdata)){
$rowcount = 0;

foreach($playerdata as $p){
$playerdetail = & $this->getObject('link','htmlelements');
$playerdetail->href = $this->uri(array('action'=>'playerdetails','playerid'=>$p['id']));
$playerdetail->link = $p['name'];
$position = $p['position'];
$country = $p['country'];

$this->objCountry =&$this->getObject('countries','utilities');
 $countryname = $this->objCountry->getCountryName($country);

$imagepath ="modules/sportsadmin/players/";

$team = $this->objDbteam->getTeamNameById($p['team']);
$image_path = $imagepath.$p['playerimage'];
$photo = '<p><img src="'.$image_path.'" width="100" height="100" /></p>'; 

$table->startRow();
$table->addCell($photo);	
$table->addCell($playerdetail->show()."<br/><strong>".$this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin')."</strong>&nbsp;&nbsp;&nbsp;".$team."<br/><strong>Position</strong>&nbsp;&nbsp;".$position."<br/><br/><strong>Country</strong>&nbsp;&nbsp;".$countryname);

$table->endRow();

	  }  
}

$content .= $table->show();
echo $content;


?>