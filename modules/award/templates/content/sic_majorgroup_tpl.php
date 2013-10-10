<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS SIC
*/

/**
* sic major group list template for the LRS SIC
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');

//Get sub major group info
if(isset($sicMajorDiv)){
  $sicMajorDivId = $sicMajorDiv['id'];
  $sicMajorDivDesc = $sicMajorDiv['description'];
}
//Get div info
if(isset($sicDiv)){
  $sicDivId = $sicDiv['id'];
  $sicDivDesc = $sicDiv['description'];
}

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');

$objMajorGrpForm = new form('lrssic');

// Create add icon
$param = $this->uri(array('action' => 'addeditmajorgroup', 'sicDivId'=>$sicDiv['id'], 'selected'=>'init_10'));
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_majorgroup', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrssic_majorgroup', 'award').' '.$addIcon;
$objMajorGrpForm->addToForm($header->show());

//create heading
$headerTitle = $this->getObject('htmlheading','htmlelements');
$headerTitle->type = 3;
$headerTitle->str = $this->objLanguage->languageText('mod_lrssic_div', 'award').': '.$sicDiv['description'];
$objMajorGrpForm->addToForm($headerTitle->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable = $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_code'), '10%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_description'), '55%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_notes'), '25%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_edit', 'award'), '10%', '', '', '');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_delete', 'award'), '10%', '', '', '');
$objSelectTable->endHeaderRow();


$class = '';
if(count($sicMajorGroups) > '0'){
  foreach($sicMajorGroups as $sicMajorGroup){ 
     //Get group description
     $sicMajorGroupName = $sicMajorGroup['code'];
     $sicMajorGroupDesc = $sicMajorGroup['description'];
     $sicMajorGroupNotes = $sicMajorGroup['notes'];
     //Get group id
     $sicMajorGroupId = $sicMajorGroup['id'];
     //Create link to unit group template
     $objMajorGroupLink =& $this->newObject('link', 'htmlelements');
     $objMajorGroupLink->link($this->uri(array('action'=>'selectsicgroup', 'sicMajorGroupId'=>$sicMajorGroupId,'selected'=>'init_10')));
     $objMajorGroupLink->link = $sicMajorGroupDesc;
     
     //$sql = "SELECT MIN(dateCreated) AS low FROM tbl_award_sic_major_group";
     //$lowestDate = $this->objDbSicMajorGroups->getArray($sql);
     //$lowest = current($lowestDate);
     
     //$sicMajorGroupDate = date("Y-m-d", strtotime($sicMajorGroup['dateCreated']));
     //$low = date("Y-m-d", strtotime($lowest['low']));
     
     //if($sicMajorGroupDate > $low)
     //{
	 	 // Create delete icon
	     //$param = array('action' => 'deletemajorgroup', 'sicMajorGroupId' => $sicMajorGroupId, 'sicDivId'=>$sicDivId);
	     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssic_deletemajorgrp');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($sicMajorGroupId, $param, 'lrssic', $deletephrase);
     //}

     // Create edit icon
     $param = $this->uri(array('action' => 'addeditmajorgroup', 'sicMajorGroupId'=>$sicMajorGroupId, 'sicDivId' => $sicDivId, 'selected'=>'init_10'));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
    // $objSelectTable->addCell($sicMajorGroupName, '', '', '', $class, '');
     $objSelectTable->addCell($objMajorGroupLink->show(), '', '', '', $class, '');
     $objSelectTable->addCell($sicMajorGroupNotes, '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
	 //if($sicMajorGroupDate > $low)
     //{
     	//$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
     //}else{
     	//$objSelectTable->addCell("<br />", '', '', '', $class, '');
     //}
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$objAddMajGrpLink = $this->newObject('link', 'htmlelements');
$objAddMajGrpLink->link($this->uri(array('action' => 'addeditmajorgroup', 'sicDivId'=>$sicDivId, 'selected'=>'init_10')));
$objAddMajGrpLink->link = $this->objLanguage->languageText('mod_lrssic_addmajorgroup', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'selectsicdiv', 'majorDivId'=>$sicMajorDivId, 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

//Create htmltable for selectmajorgroup form elements
$objAddTable =& $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

$txtHidden = new textinput('sicDivId', $sicMajorDivId, 'hidden');

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell("<br />");
$objAddTable->addCell($txtHidden->show());
$objAddTable->endRow();

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell($objAddMajGrpLink->show().' / '.$linkBack->show());
$objAddTable->addCell($linkExit->show(), '', '', 'right');
$objAddTable->endRow();

$objMajorGrpForm->addToForm($objSelectTable->show());
$objMajorGrpForm->addToForm($objAddTable->show());

echo $objMajorGrpForm->show();

?>
