<?php
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

$this->objDbsports =& $this->getObject('dbsports','sportsadmin');
$this->objLanguage->languageText('language','language');
$this->loadClass('form','htmlelements');
$heading =& $this->getObject('htmlheading','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$objIcon =& $this->getObject('geticon','htmlelements');
$objDateLink =& $this->getObject('link','htmlelements');
$sportid = $this->getParam('sportid',NULL);
$wordto = $this->objLanguage->languageText('word_to');
$this->objDbtournament = $this->getObject('dbtournament');
$this->loadClass('hiddeninput','htmlelements');
$this->loadClass('htmltable','htmlelements');
$table = new htmltable();
$table->width = '80%';

//check whether we are editing or adding
	$useEdit=0;
	$useEdit= $this->getParam('useEdit', NULL);
	if($useEdit) {
		$id=0;
		$id= $this->getParam('tournamentid',NULL);	
		$heading->str = $this->objLanguage->languageText('word_edit','system').'&nbsp; '.$this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin');
	}
	
   else{
   $heading->str = $this->objLanguage->languageText('mod_sportsadmin_addtournament','sportsadmin').'&nbsp; '.$wordto.'&nbsp; '.$this->objDbsports->getSportsById($this->getParam('sportid',NULL));

  } 
$heading->align = 'center';

echo $heading->show();


$redirect = $this->getParam('redirect',NULL);
$action = $this->uri(array('module'=>'sportadmin','action'=>'savetournament','item'=>'tournament','sportid'=>$this->getParam('sportid',NULL),'redirect'=>$redirect));
$form = new form('addtournament',$action);
$form->displayType = 3;

// get all the values for the tournament
$tournamentdata = $this->objDbtournament->pickTournamentduration($sportid,$id);

//echo $tournamentid; exit;
if(!empty($tournamentdata)){
	 foreach($tournamentdata as $t){
	  $sponsor_value = $t['sponsorname'];
	  $name = $t['name'];
	  $startDate = $t['startdate'];
	  $endDate = $t['enddate'];
	 }
}

//label for the name of the tournament
$tournamentlabel = new label($this->objLanguage->languageText('word_name','system'),'');
$tournamentfield = new textinput('tournament','');
$tournamentfield->value = (useEdit?$name:''); 
$tournamentfield ->size = 50;

$sponsorlabel = new label($this->objLanguage->languageText('mod_sportsadmin_sponsorname','sportsadmin'),'');
$sponsorfield = new textinput('sponsor');
$sponsorfield->value = ($useEdit?$sponsor_value:'');
$sponsorfield->size = 50;


$defaultDuration=2;

	if($useEdit){
		$defaultEndDate = $endDate;
		$defaultstartDate = $startDate;			
	}
	
  else {	
	//default duration is after a week
	$defaultEndDate =strtotime(date('Y-m-d ')) + (60*60*24*7*$defaultDuration);
	$defaultstartDate = strtotime(date('Y-m-d'));
  }
  
$this->objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
$endDateLink = $this->objPopupcal->show('enddate', 'no', 'yes',$defaultEndDate);

$this->objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
$startDateLink = $this->objPopupcal->show('startdate', 'no', 'yes',$defaultstartDate);

$startdatelabel = new label($this->objLanguage->languageText('mod_sportsadmin_startdate','sportsadmin'),'');
if($useEdit){
   $startdatafield = new textinput('startdate',$defaultstartDate);}
   
   else 
     $startdatafield = new textinput('startdate',date('Y-m-d',$defaultstartDate));
	 

$enddatelabel = new label($this->objLanguage->languageText('mod_sportsadmin_enddate','sportsadmin'),'');
if($useEdit){$enddatefield = new textinput('enddate',$defaultEndDate);
}
else 
  {$enddatefield = new textinput('enddate',date('Y-m-d',$defaultEndDate));}

  $objHidden = new hiddeninput('action',($useEdit?'modifytournament':'savetournament'));
	if($useEdit) {
	  $objHiddenId = new hiddeninput('tournamentid',$id);
	}
	 $submit = new button("save",$objLanguage->languageText('word_save','system'));   
	 $submit->setToSubmit();
	
	$table->startRow('odd');
	$table->addCell($tournamentlabel->show(),'40%','','center');
	$table->addCell($tournamentfield->show(),'','','left');
	$table->endRow();

	$table->startRow('odd');
	$table->addCell($sponsorlabel->show(),'40%','','center');
	$table->addCell($sponsorfield->show(),'','','left');
	$table->endRow();

	$table->startRow('odd');
	$table->addCell($startdatelabel->show(),'40%','','center');
	$table->addCell($startDateLink ,'','','left');
	$table->endRow();

	$table->startRow('odd');
	$table->addCell($enddatelabel->show(),'40%','','center');
	$table->addCell($endDateLink,'','','left');
	$table->endRow();

$table->startRow('odd');
$table->addCell($submit->show().''.$objHidden->show().''.($useEdit?$objHiddenId->show():''),'','','center');
$table->addCell('');
$table->endRow();


$form->addToForm($table->show());

echo $form->show();

?>