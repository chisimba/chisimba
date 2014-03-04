<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
//instance of the language class
$this->objLanguage =& $this->getObject('language','language');
/*
* Templet to display photos of the players in the different teams
* @Author : nsabagwa mary
*/
$content = "";
$sportid = $this->getParam('sportid',NULL);

$heading = & $this->getObject('htmlheading','htmlelements');
$this->objDbsports = & $this->getObject('dbsports','sportsadmin');
$this->loadClass('htmltable','htmlelements');
$imagepath = "chisimba_modules/sportsadmin/players/";//path for the images
$this->objDbplayer = & $this->getObject('dbplayer','sportsadmin');
$this->objDbteam =& $this->getObject('dbteam','sportsadmin');
$form =& $this->getObject('form','htmlelements');

$sportname = $this->objDbsports->getSportsById($sportid);
$heading->str = $sportname."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_player','sportsadmin')."s'&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_album','sportsadmin');

//get details of the player plus te image file name
$playerdata = $this->objDbplayer-> geAll($sportid);

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

$team = $this->objDbteam->getTeamNameById($p['team']);
$image_path = $imagepath.$p['playerimage'];
$photo = '<p><img src="'.$image_path.'" width=100 height=100></p>'; 

if(($rowcount%4)==0 || $rowcount==0){
$rowcount = 0;
	$table->startRow();
		
	}
	$table->addCell($photo."<br><br>".$playerdetail->show()."<br><br>".$team);	

if($rowcount==4 ){
	 $table->endRow();
	 }
	  $rowcount ++;
	
	  }
  

 
}


$content .= $table->show();
echo $content;
?>