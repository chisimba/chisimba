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

//Get major group info
if($majorGroup){
  $majorGroupId = $majorGroup['id'];
  $majorGroupDesc = $majorGroup['description'];
}  
	
//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');


$objSubMajorGroupForm = new form('lrssoc');

// Create add icon 
$param = $this->uri(array('action' => 'editsubmajorgroup', 'majorGroupId'=>$majorGroupId,'selected'=>'init_10'));
$objAddIcon =$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssoc_submajorgroup','award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '1';
$header->str = $this->objLanguage->languageText('mod_lrssoc_submajorgroup','award').' '.$addIcon;
$objSubMajorGroupForm->addToForm($header->show());

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '3';
$header->str = $this->objLanguage->languageText('mod_lrssoc_majorgroup','award').':'. $majorGroup['description'];
$objSubMajorGroupForm->addToForm($header->show());

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

if(count($subMajorGroups) > 0){
  foreach($subMajorGroups as $subMajorGroup){
     //Get group description
     $subMajorGroupDesc = $subMajorGroup['description'];
     //Get group id
     $subMajorGroupId = $subMajorGroup['id'];
     //Create link to unit group template

     $objSubMajorGroupLink = $this->newObject('link', 'htmlelements');
     $objSubMajorGroupLink->link($this->uri(array('action'=>'selectminorgroup', 'submajor_groupid'=>$subMajorGroupId,'selected'=>'init_10')));
     $objSubMajorGroupLink->link = $subMajorGroupDesc;

     //$sql = "SELECT (major_groupid)  FROM tbl_award_socsubmajorgroup";
     //$lowestDate = $this->objDbSubMajorGroups->getArray($sql);
     //$lowest = current($lowestDate);

     //if($subMajorGroup['dateCreated'] > $lowest['low'])
     //{
	     // Create delete icon
	    // $param = array('action' => 'deletesubmajorgroup', 'subMajorGroupId' => $subMajorGroupId, 'majorGroupId'=>$majorGroupId,'selected'=>'init_10',);
	     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssoc_deletesubmajorgroup');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($subMajorGroupId, $param, 'lrssoc', $deletephrase); 
     //}
     // Create edit icon
     $param = $this->uri(array('action' => 'editsubmajorgroup', 'subMajorGroupId' => $subMajorGroupId, 'majorGroupId'=>$majorGroupId,'selected'=>'init_10'));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editsubmajorgroup');
     $editIcon = $objEditIcon->getEditIcon($param); 
     
     $class = ($class=='odd')? 'even' : 'odd';

     //Add sub major groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objSubMajorGroupLink->show(), '60%', '', '', $class, '');
     $objSelectTable->addCell($editIcon, '15%', '' ,'center', $class, '');
  }
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_norecordsavailable','award'), '', '', '', 'noRecordsMessage', 'colspan=2');
     $objSelectTable->endRow();
}


$linkBack = new link($this->uri(array('action'=>'selectmajorgroup', 'selected'=>'init_10')));
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

$objSubMajorGroupForm->addToForm($objSelectTable->show());
//Add content to the output layer
echo $objSubMajorGroupForm->show();

?>