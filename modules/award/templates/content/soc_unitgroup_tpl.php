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

$objunitGroupForm = new form('lrssoc');

// Create add icon 
$param = $this->uri(array('action' => 'editunitgroup', 'majorGroupId'=>$majorGroupId, 'subMajorGroupId'=>$subMajorGroupId, 'minorGroupId'=>$minorGroup['id'], 'selected'=>'init_10'));
$objAddIcon =$this->newObject('geticon', 'htmlelements');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '1';
$header->str = $this->objLanguage->languageText('mod_lrssoc_unitGroup','award').' '.$addIcon;
$objunitGroupForm->addToForm($header->show());

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '3';
$header->str = $this->objLanguage->languageText('mod_lrssoc_minorgroup','award').': '. $minorGroup['description'];
$objunitGroupForm->addToForm($header->show());

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

if(count($unitGroups) > 0){
  foreach($unitGroups as $unitGroup){
     //Get group description
     $unitGroupDesc = $unitGroup['description'];
     //Get group id
     $unitGroupId = $unitGroup['id'];
     //Create link to unit group template

     $objunitGroupLink = $this->newObject('link', 'htmlelements');
     $objunitGroupLink->link($this->uri(array('action'=>'selectsocname', 'unit_groupid'=>$unitGroupId, 'selected'=>'init_10')));
     $objunitGroupLink->link = $unitGroupDesc;

     //$sql = "SELECT (major_groupid)  FROM tbl_award_socunitGroup";
     //$lowestDate = $this->objDbunitGroups->getArray($sql);
     //$lowest = current($lowestDate);

     //if($unitGroup['dateCreated'] > $lowest['low'])
     //{
	     // Create delete icon
	    // $param = array('action' => 'deleteunitGroup', 'unitGroupId' => $unitGroupId, 'majorGroupId'=>$majorGroupId,'selected'=>'init_10',);
	     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssoc_deleteunitGroup');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($unitGroupId, $param, 'lrssoc', $deletephrase); 
     //}
     // Create edit icon
     $param = $this->uri(array('action' => 'editunitgroup', 'unitGroupId' => $unitGroupId, 'minorGroupId'=>$minorGroup['id'], 'subMajorGroupId' => $subMajorGroupId, 'majorGroupId'=>$majorGroupId,'selected'=>'init_10'));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editunitGroup');
     $editIcon = $objEditIcon->getEditIcon($param); 
     
     $class = ($class=='odd')? 'even' : 'odd';

     //Add sub major groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objunitGroupLink->show(), '60%', '', '', $class, '');
     $objSelectTable->addCell($editIcon, '15%', '' ,'center', $class, '');
  }
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_norecordsavailable','award'), '', '', '', 'noRecordsMessage', 'colspan=2');
     $objSelectTable->endRow();
}


$linkBack = new link($this->uri(array('action'=>'selectminorgroup', 'submajor_groupid'=>$subMajorGroupId, 'selected'=>'init_10')));
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

$objunitGroupForm->addToForm($objSelectTable->show());
//Add content to the output layer
echo $objunitGroupForm->show();

?>