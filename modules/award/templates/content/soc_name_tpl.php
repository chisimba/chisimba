<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS SOC
*/

/**
* Sub major group list template for the LRS SOC
* Author Warren Windvogel, Brent van Rensburg
*/  
	
//Load the form class
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$objsocNameForm = new form('lrssoc');

// Create add icon 
$param = $this->uri(array('action' => 'editsocname', 'majorGroupId'=>$majorGroupId, 'subMajorGroupId'=>$subMajorGroupId, 'minorGroupId'=>$minorGroupId, 'unitGroupId'=>$unitGroup['id'], 'selected'=>'init_10'));
$objAddIcon =$this->newObject('geticon', 'htmlelements');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '1';
$header->str = $this->objLanguage->languageText('word_occupation').' '.$addIcon;
$objsocNameForm->addToForm($header->show());

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '3';
$header->str = $this->objLanguage->languageText('mod_lrssoc_unitgroup','award').': '. $unitGroup['description'];
$objsocNameForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable = $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '60%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_edit'),'10%', '' ,'' ,'');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_delete'),'10%', '' ,'' ,'');
$objSelectTable->endHeaderRow();


$class = '';

if(count($socNames) > 0){
  foreach($socNames as $socName){
     //Get group description
     $socNameDesc = $socName['name'];
     //Get group id
     $socNameId = $socName['id'];
     
     //$sql = "SELECT (major_groupid)  FROM tbl_award_socsocName";
     //$lowestDate = $this->objDbsocNames->getArray($sql);
     //$lowest = current($lowestDate);

     //if($socName['dateCreated'] > $lowest['low'])
     //{
	     // Create delete icon
	    // $param = array('action' => 'deletesocName', 'socNameId' => $socNameId, 'majorGroupId'=>$majorGroupId,'selected'=>'init_10',);
	     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssoc_deletesocName');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($socNameId, $param, 'lrssoc', $deletephrase); 
     //}
     // Create edit icon
     $param = $this->uri(array('action' => 'editsocname', 'socNameId' => $socNameId, 'minorGroupId'=>$minorGroupId, 'subMajorGroupId' => $subMajorGroupId, 'majorGroupId'=>$majorGroupId, 'unitGroupId'=>$unitGroup['id'], 'selected'=>'init_10'));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editsocName');
     $editIcon = $objEditIcon->getEditIcon($param); 
     
     $class = ($class=='odd')? 'even' : 'odd';

     //Add sub major groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($socNameDesc, '60%', '', '', $class, '');
     $objSelectTable->addCell($editIcon, '15%', '' ,'center', $class, '');
  }
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_norecordsavailable','award'), '', '', '', 'noRecordsMessage', 'colspan=2');
     $objSelectTable->endRow();
}


$linkBack = new link($this->uri(array('action'=>'selectunitgroup', 'minor_groupid'=>$minorGroupId, 'selected'=>'init_10')));
$linkBack->link = $this->objLanguage->languageText("word_back");

// set up exit link
$exitLink = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10')));
$exitLink->link = $this->objLanguage->languageText("word_exit");

$objSelectTable->startRow();
$objSelectTable->addCell("<br />");
$objSelectTable->addCell("<br />");
$objSelectTable->endRow();
$objSelectTable->startRow();
$objSelectTable->addCell($linkBack->show());
$objSelectTable->addCell($exitLink->show());
$objSelectTable->endRow();

$objsocNameForm->addToForm($objSelectTable->show());
//Add content to the output layer
echo $objsocNameForm->show();

?>