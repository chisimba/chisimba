<?php
//-----sports class extends controller---------

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Controller class for sportadmin module
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary, Kaddu Ismael
*
* 
*/

$sportid = $this->getParam('sportid',NULL);
$this->loadClass('hiddeninput','htmlelements');
$tournamentdetails = $this->getParam('tournamentdetails',NULL);
$this->objDbsports =& $this->getObject('dbsports','sportsadmin');
$this->objLanguage->languageText('language','language');
$this->objDbteam = & $this->getObject('dbteam');
$this->loadClass('form','htmlelements');
$heading =& $this->getObject('htmlheading','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$objIcon =& $this->getObject('geticon','htmlelements');
$objDateLink =& $this->getObject('link','htmlelements');



//check whether we are editing or adding
	$useEdit=0;
	$useEdit=$this->getParam('useEdit', NULL);
	if($useEdit) {
		$teamid=0;
		$teamid=$this->getParam('teamid', NULL);
	}
	
	//get the details of the team
	 
	$teamdetails = $this->objDbteam->getTeamDetails($teamid);
	
	if(!empty($teamdetails)){
	  foreach($teamdetails as $t){
	  $name = $t['name'];
	  $motto = $t['motto'];
	  $field = $t['homeground'];
	  }
	
	}
	

$wordto = $this->objLanguage->languageText('word_to');

$heading->str = $this->objLanguage->languageText('mod_sportsadmin_addteam','sportsadmin').'&nbsp;'.$wordto.'&nbsp;'.$this->objDbsports->getSportsById($this->getParam('sportid',NULL));
$heading->align = 'center';
$this->loadClass('htmltable','htmlelements');

//table
$table = new htmltable();
$table->width = '80%';
$table->align = 'center';
$table->cellspacing = '2';

echo $heading->show();

$form = new form('addteam');
$form->extra ="enctype='multipart/form-data'";

//label for the name of the team
$teamname = new label($this->objLanguage->languageText('mod_sportsadmin_teamname','sportsadmin'),'');
$teamfield = new textinput('teamfield','');
$teamfield->value = (useEdit?$name:'');
$tournamentfield = new textinput('tournamentdetails',$tournamentdetails,'hidden');
$teamfield ->size = 50;

$teammoto = $this->objLanguage->languageText('mod_sportsadmin_teammotto','sportsadmin');
$mottofield = new textinput('motto');
$mottofield->value = (useEdit?$motto:'');
$mottofield->size = 50;


$ground = new label($this->objLanguage->languageText('mod_sportsadmin_homeground','sportsadmin'),'');
$homeground = new textinput('ground');
$homeground->value = (useEdit?$field:'');
$homeground->size = 50;


///add logo of the team
$teamlogo = $this->objLanguage->languageText('mod_sportadmin_teamlogo','sportsadmin');
$logofile = new textinput('playerimage','','file');

if($useEdit) {
	  $objHiddenId = new hiddeninput('teamid',$this->getParam('teamid',NULL));  
	  $itemHiddenId = new hiddeninput('item','teams');  
	   $sportHiddenId = new hiddeninput('sportid',$this->getParam('sportid',NULL));     
	}

//Save button
$objButton = $this->newObject('button', 'htmlelements');
$objButton = new button("save",$objLanguage->languageText("word_save"));   
$objButton->setToSubmit();

$objHidden = new hiddeninput('action',($useEdit?'modifyteam':'saveteam'));

//back link 
$backlink=& $this->getObject('link','htmlelements');
$backlink->href = "javascript:history.back()";
$backlink->link = $this->objLanguage->languageText('word_back','system');

$table->startRow();
$table->addCell($teamname->show(),'','','');
$table->addCell($teamfield ->show(),'','','left');
$table->endRow();

$table->startRow();
$table->addCell($teammoto,'','','');
$table->addCell($mottofield->show(),'','','left');
$table->endRow();

$table->startRow();
$table->addCell($tournamentfield->show(),'','','left');
$table->endRow();

$table->startRow();
$table->addCell($ground->show(),'','','');
$table->addCell($homeground->show(),'','','left');
$table->endRow();


$table->startRow();
$table->addCell($teamlogo,'','','');
$table->addCell($logofile->show(),'','','left');
$table->endRow();


$table->startRow();
$table->addCell($objHidden->show(),'','','');
$table->addCell($objButton->show());
$table->endRow();

$form->addToForm($table->show());

echo $form->show();
echo $backlink->show();
?>
