<?php
//-----sports class extends controller---------

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
*
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary
*
*
*/

$tournamentid = $this->getParam('tournamentid',NULL);
$this->objDbplayer =& $this->getObject('dbplayer');
//check whether we are editing or adding
	$useEdit=0;
	$useEdit=$this->getParam('useEdit', NULL);
	if($useEdit) {
		$id=0;
		$playerid=$this->getParam('playerid', NULL);
	}


$fixtureid = $this->getParam('fixtureid',NULL);
//instance of dbteam class
$sportid = $this->getParam('sportid',NULL);
$display ='';

$teamid = $this->getParam('teamid',NULL);

$this->objDbteam =& $this->getObject('dbteam');

$this->objDbsports =& $this->getObject('dbsports','sportsadmin');
$this->objLanguage->languageText('language','language');
//$this->loadClass('form','htmlelements');
$heading =& $this->getObject('htmlheading','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$objIcon =& $this->getObject('geticon','htmlelements');
$objDateLink =& $this->getObject('link','htmlelements');
$this->loadClass('dropdown','htmlelements');
$wordto = $this->objLanguage->languageText('word_to');



if(!$useEdit) {
	$heading->str = $this->objLanguage->languageText('mod_sportsadmin_player','sportsadmin').'&nbsp; '.$wordto.'&nbsp; '.$this->objDbsports->getSportsById($this->getParam('sportid',NULL));
	}
	else {
	$heading->str = $this->objLanguage->languageText('mod_sportsadmin_modify','sportsadmin').'&nbsp; '.$this->objDbsports->getSportsById($this->getParam('sportid',NULL)).'&nbsp; '.$this->objLanguage->languageText('mod_sportsadmin_player','sportsadmin');

$playerdata = $this->objDbplayer->getPlayerDetails($this->getParam('playerid',NULL));

if(!empty($playerdata)){
		foreach($playerdata as $p){
		$pname = $p['name'];
		$pposition = $p['position'];
		$pteam = $p['team'];
		$pfilename = $p['playerimage'];
		$pcountry = $p['country'];
		$pdob = $p['dateofbirth'];
		}
	}
}
$heading->align = 'center';
$this->loadClass('htmltable','htmlelements');

//table
$table = new htmltable();
$table->width = '80%';

echo $heading->show();

$form = new form('addplayer');

$form->extra ="enctype='multipart/form-data'";

//label for the name of the team
$name = new label($this->objLanguage->languageText('word_name','system'),'');
$playerfield = new textinput('playerfield');
$playerfield->size = 47;
$playerfield->value=($useEdit?$pname:'');

$table->startRow('odd');
$table->addCell($name->show(),'40%','','right');
$table->addCell("&nbsp;&nbsp;".$playerfield->show(),'','','left');
$table->endRow();

//team to which he player belongs
$team= new label($this->objLanguage->languageText('mod_sportsadmin_team','sportsadmin'),'');
//pick the availabe teams for the sport from the database only if the team id is not set or
//if they are not adding the player to the team
$teams = $this->objDbteam->getAll($sportid);
$teamdropbox = new dropdown('team');

$value = ($useEdit?$pteam:'');

//$teamdropbox->addOption('','---teams---');


if(!empty($teams)){

  foreach($teams as $teamnames){

 $teamdropbox->addOption($teamnames['id'],$teamnames['name']);
  }
  $teamdropbox->setSelected($value);

}

if(isset($teamid)){
$teamname = new textinput('team',$teamid,'hidden');
}

$country = new label($this->objLanguage->languageText('word_country','system'),'');

 $objCountries=&$this->getObject('languagecode','language');
    $table->startRow();
    $table->addCell($this->objLanguage->languageText('word_country', 'system'),'40%','','right');
    $table->addCell("&nbsp;&nbsp;".$objCountries->countryAlpha($pcountry));
    $table->endRow();

if($useEdit){
$defaultstartDate = $pdob;
}

else { $defaultstartDate = date('Y-m-d');}
$this->objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
$dateField = $this->objPopupcal->show('dob', 'no', 'yes',$defaultstartDate);

$table->startRow('odd');
$table->addCell($this->objLanguage->languageText('mod_sportsadmin_dateofbirth','sportsadmin'),'40%','','right');
$table->addCell("&nbsp;&nbsp;".$dateField);

$table->endRow();

$table->startRow('odd');
$table->addCell($team->show(),'40%','','right');
if(isset($teamid)){
$table->addCell($this->objDbteam->getTeamNameById($teamid),'','','left');
$table->addCell("&nbsp;&nbsp;".$teamname->show());
}
else{
$table->addCell("&nbsp;&nbsp;".$teamdropbox->show(),'','','left');}
$table->endRow();

//position
$position = $this->objLanguage->languageText('mod_sportsadmin_position','sportsadmin');
$positionfield = new textinput('position');
$positionfield->value=($useEdit?$pposition:'');
$positionfield->size = 48;

$table->startRow('odd');
$table->addCell($position,'40%','','right');
$table->addCell("&nbsp;&nbsp;".$positionfield->show(),'','','');
$table->endRow();

//the path in which the folder is stored
$this->playerfolder = $this->objConfig->getModulePath().'sportsadmin/players/';
$logofilepath =  $this->playerfolder.$pfilename;

//upload an image for the player
$playerimage = new textinput('playerimage',$useEdit?$logofilepath:'','file');
$playerimage->size = 48;

$imageLabel = $this->objLanguage->languageText('mod_sportsadmin_playerimage','sportsadmin');

$table->startRow('odd');
$table->addCell($imageLabel,40,'','right');
$table->addCell("&nbsp;&nbsp;".$playerimage->show(),'','','');
$table->endRow();

$this->loadClass('hiddeninput','htmlelements');

//Save button
	$objButton = $this->newObject('button', 'htmlelements');
	$objHidden = new hiddeninput('action',($useEdit?'modifyplayer':'saveplayer'));
	$itemHidden = new hiddeninput('item','players');
		$sportHidden = new hiddeninput('sportid',$sportid);
	$teamHidden = new hiddeninput('teamid',$teamid);
	$fixtureHidden = new hiddeninput('fixtureid',$fixtureid);
	$fixtureHidden = new hiddeninput('tournamentid',$tournamentid);

	$table->startRow();
	$table->addCell($fixtureHidden->show());
	$table->addCell($fixtureHidden->show(),'','','');
	$table->endRow();

	$table->startRow();
	$table->addCell($teamHidden->show());
	$table->addCell($sportHidden->show(),'','','');
	$table->endRow();

	$table->startRow();
	$table->addCell('');
	$table->addCell($itemHidden->show(),'','','');
	$table->endRow();

	if($useEdit) {
	  $objHiddenId = new hiddeninput('playerid',$this->getParam('playerid',NULL));

	}

	$objButton = new button("save",$objLanguage->languageText("word_save"));
	$objButton->setToSubmit();
	$table->addCell('');
	$table->addCell($objButton->show().''.$objHidden->show().''.($useEdit?$objHiddenId->show():''));


//back link
$back = & $this->getObject('link','htmlelements');
$back->href = 'javascript:history.back()';
$back->link = $this->objLanguage->languageText('word_back','system');


$table->startRow('odd');
$table->addCell('');
$table->addCell($back->show(),'','','');
$table->endRow();


$form->addToForm($table->show());

echo $form->show();

?>
