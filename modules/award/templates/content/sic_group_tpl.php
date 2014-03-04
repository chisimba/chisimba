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
* sic group list template for the LRS SIC
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
//Get major group info
if(isset($sicMajorGroup)){
  $sicMajorGroupId = $sicMajorGroup['id'];
  $sicMajorGroupDesc = $sicMajorGroup['description'];
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

$objGroupForm = new form('lrssic');
// Create add icon
$param = $this->uri(array('action' => 'addeditgroup', 'sicMajorGroupId'=>$sicMajorGroupId, 'selected'=>'init_10'));
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_group', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrssic_group', 'award').' '.$addIcon;
$objGroupForm->addToForm($header->show());

//create heading
$headerTitle = $this->getObject('htmlheading','htmlelements');
$headerTitle->type = 3;
$headerTitle->str = $this->objLanguage->languageText('mod_lrssic_majorgroup', 'award').': '.$sicMajorGroup['description'];
$objGroupForm->addToForm($headerTitle->show());

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
if(count($sicGroups) > '0'){
  foreach($sicGroups as $sicGroup){ 
     //Get group description
     $sicGroupName = $sicGroup['code'];
     $sicGroupDesc = $sicGroup['description'];
     $sicGroupNotes = $sicGroup['notes'];
     //Get group id
     $sicGroupId = $sicGroup['id'];
     //Create link to unit group template
     $objGroupLink = $this->newObject('link', 'htmlelements');
     $objGroupLink->link($this->uri(array('action'=>'selectsicsubgroup', 'sicGroupId'=>$sicGroupId, 'selected'=>'init_10')));
     $objGroupLink->link = $sicGroupDesc;
     
     //$sql = "SELECT MIN(dateCreated) AS low FROM tbl_lrs_sic_group";
     //$lowestDate = $this->objDbSicGroups->getArray($sql);
     //$lowest = current($lowestDate);
     
     //$sicGroupDate = date("Y-m-d", strtotime($sicGroup['dateCreated']));
     //$low = date("Y-m-d", strtotime($lowest['low']));
     
     /*if($sicGroupDate > $low)
     {
	 	 // Create delete icon
	     $param = array('action' => 'deletegroup', 'sicGroupId'=>$sicGroupId, 'sicMajorGroupId' => $sicMajorGroupId);
	     $objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     $deletephrase = $this->objLanguage->languageText('mod_lrssic_deletegroup');
	     $deleteIcon = $objDelIcon->getDeleteIconWithConfirm($sicGroupId, $param, 'lrssic', $deletephrase); 
     }*/
     
     // Create edit icon
     $param = $this->uri(array('action' => 'addeditgroup', 'sicGroupId' => $sicGroupId, 'sicMajorGroupId'=>$sicMajorGroupId, 'selected'=>'init_10'));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
  //   $objSelectTable->addCell($sicGroupName, '', '', '', $class, '');
     $objSelectTable->addCell($objGroupLink->show(), '', '', '', $class, '');
     $objSelectTable->addCell($sicGroupNotes, '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
	 /*if($sicGroupDate > $low)
     {
     	$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
     }else{
     	$objSelectTable->addCell("<br />", '', '', '', $class, '');
     }
     $objSelectTable->endRow();*/
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
    }


$objAddGroupLink = $this->newObject('link', 'htmlelements');
$objAddGroupLink->link($this->uri(array('action' => 'addeditgroup', 'sicMajorGroupId'=>$sicMajorGroupId, 'selected'=>'init_10')));
$objAddGroupLink->link = $this->objLanguage->languageText('mod_lrssic_addgroup', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'selectsicmajorgroup', 'sicDivId'=>$sicDiv['id'], 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

//Create htmltable for selectmajorgroup form elements
$objAddTable = $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

$txtHidden = new textinput('sicMajorGroupId', $sicMajorGroupId, 'hidden');

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell("<br />");
$objAddTable->addCell($txtHidden->show());
$objAddTable->endRow();

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell($objAddGroupLink->show().' / '.$linkBack->show());
$objAddTable->addCell($linkExit->show(), '', '', 'right');
$objAddTable->endRow();

$objGroupForm->addToForm($objSelectTable->show());
$objGroupForm->addToForm($objAddTable->show());

echo $objGroupForm->show();

?>