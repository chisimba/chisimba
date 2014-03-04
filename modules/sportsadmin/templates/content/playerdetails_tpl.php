<?php
//-----sports class extends controller---------

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
*  Template for showing the details of the player
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary
*
* 
*/
$content ='';
$this->objUser = & $this->getObject('user','security');
$sportid = $this->getParam('sportid',NULL);

$imagepath = $this->objConfig->getcontentRoot()."players/";
$this->loadClass('link','htmlelements');
$this->objConfig = $this->getObject('config','config');

$this->objDbsport = & $this->getObject('dbsports','sportsadmin');

//make an instance of dbplayer class
$this->objDbplayer = $this->getObject('dbplayer');
$table = $this->getObject('htmltable','htmlelements'); 
$table->width= '40%';
$table->align = 'center';
$playerid = $this->getParam('playerid',NULL);
$playerdata = $this->objDbplayer->getPlayerDetails($playerid);
$this->loadClass('htmltable','htmlelements');
$heading =& $this->getObject('htmlheading','htmlelements');
 $this->objDbplayerdata = & $this->getObject('dbplayerdata');
 $iconEdit = $this->getObject('geticon','htmlelements');
 $objConfirm=&$this->newObject('confirm','utilities');
 $iconDelete = $this->getObject('geticon','htmlelements');
 
 $sportname =  $this->objDbsport->getSportsById($sportid);

//echo $sportid; exit;
$heading->str = $sportname."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_player','sportsadmin');

$heading->align = "center";


$content .=$heading->show();

//go through the list and display
foreach($playerdata  as $p){
$name = $p['name'];
$role = $p['position'];
$dob = $p['dateofbirth'];
$country = $p['country'];
$file = $p['playerimage'];
$teamid = $p['team'];
}

$uuuu = $imagepath.$file;    
$path = str_replace('\\', '/',$uuuu);

$photo = '<p><img src="'.$path.'" width="100" height="100" /></p>'; 

$this->objCountry =&$this->getObject('countries','utilities');
 $countryname = $this->objCountry->getCountryName($country );


$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('word_name')."</strong>");
$table->addCell($name);
$table->endRow();

//get the team name 
$team_name = $this->objDbteam->getTeamNameById($teamid);

$table->startRow();
$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin')."</strong>");
$table->addCell($team_name);
$table->endRow();

$backlink = & $this->getObject('link','htmlelements');
$backlink->href = "javascript:history.back()";
$backlink->link = $this->objLanguage->languageText('word_back');

if($this->objUser->isAdmin()){
		//Create a link for adding data for a player
		$add = new link($this->uri(array('action'=>'addplayerdata','playerid'=>$playerid,'sportid'=>$sportid)));
		 $add->link = $this->objLanguage->languageText('mod_sportadmin_addplayerdata','sportsadmin');
		$content .= $add->show();
		}
		
		
		$table->startRow();
		$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin')."</strong>");
		$table->addCell($role);
		$table->endRow();
		
		$table->startRow();
		$table->addCell("<strong>".$this->objLanguage->languageText('mod_sportsadmin_dateofbirth','sportsadmin')."</strong>");
		$table->addCell($dob);
		$table->endRow();
		
		$table->startRow();
		$table->addCell("<strong>".$this->objLanguage->languageText('word_country','system')."</strong>");
		$table->addCell($countryname);
		$table->endRow();	
		
		 
		//check if there is some data entered on the player and display 
		$playerinfo = $this->objDbplayerdata->getPlayerData($playerid);
		//$playerinfo->cellspacing =3;
		$infotable = new htmltable();
		$infotable->width = "80%"; 
		if(!empty($playerinfo)){
		
		//modify link
		$iconEdit->setIcon('edit');
		$iconEdit->alt = $objLanguage->languageText("mod_sports_edit",'sports');
		$iconEdit->align=false;
		$objLink =& $this->getObject("link","htmlelements");	
		
		
		//profile of the player
		$infotable->startRow();
		$infotable->addCell("<strong>".$this->objLanguage->languagetext('mod_sportsadmin_profile','sportsadmin')."</strong>",'','','left');
		$infotable->endRow();
		 
			foreach($playerinfo as $pi){
			//delete link
			$iconDelete->setIcon('delete');
			$iconDelete->alt = $objLanguage->languageText("mod_sportsadmin_deleteplayerinfo",'sportsadmin');
			$objConfirm->setConfirm(
			$iconDelete->show(),
		$this->uri(array('module'=>'sportsadmin','action'=>'deleteplayerinfo','playerid'=>$playerid,'sportid'=>$sportid,'confirm'=>'yes','infoid'=>$pi["id"])),
		$objLanguage->languageText('mod_sportsadmin_sureinfodelte','sportsadmin'));	
			
			
		 $iconEdit = $this->getObject('geticon','htmlelements');
		 $iconEdit->setIcon('edit');
		 $iconEdit->alt = $objLanguage->languageText('mod_sportsadmin_edit_player','sportsadmin');
		 $iconEdit->align=false;
		 $objLink->link($this->uri(array('module'=>'sportsadmin','action'=>'addplayerdata','useEdit'=>'1','infoid'=>$pi['id'],'playerid'=>$playerid)));
		 $objLink->link = $iconEdit->show();
		 $linkEdit = $objLink->show();
		 
		 
		$name = $this->objUser->fullname($pi['enteredBy']);		
			
		$infotable->startRow();
		$infotable->addCell($pi['event']);
		$infotable->addCell("<p class=\"minute\">".$this->objLanguage->languageText('word_by')."&nbsp;".$name."</p>&nbsp;". $linkEdit."&nbsp;".$objConfirm->show());		
		$infotable->endRow();
				
				
		$infotable->startRow();
		$infotable->addCell("");
		$infotable->endRow();
			
			}//closing foreach
		
}//closing if not empty
 

//create the outer table 
$outertable = & new htmltable();

$outertable->startRow();
$outertable->addCell($photo,'20%');
$outertable->addCell($table->show());
$outertable->endRow();

//backlink 
$backlink =& $this->getObject('link','htmlelements');
$backlink->href = "javascript:history.back()";
$backlink->link = $this->objLanguage->languageText('word_back');

$content .= $outertable->show();
$content .= $infotable->show();

echo $content;
echo $backlink->show();

?>