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

$objminorGroupForm = new form('lrssoc');

// Create add icon 
$param = $this->uri(array('action' => 'editminorgroup', 'majorGroupId'=>$majorGroupId, 'subMajorGroupId'=>$subMajorGroup['id'], 'selected'=>'init_10'));
$objAddIcon =$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssoc_majorgroup','award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '1';
$header->str = $this->objLanguage->languageText('mod_lrssoc_minorgroup','award').' '.$addIcon;
$objminorGroupForm->addToForm($header->show());

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = '3';
$header->str = $this->objLanguage->languageText('mod_lrssoc_submajorgroup','award').': '. $subMajorGroup['description'];
$objminorGroupForm->addToForm($header->show());

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

if(count($minorGroups) > 0){
  foreach($minorGroups as $minorGroup){
     //Get group description
     $minorGroupDesc = $minorGroup['description'];
     //Get group id
     $minorGroupId = $minorGroup['id'];
     //Create link to unit group template

     $objminorGroupLink = $this->newObject('link', 'htmlelements');
     $objminorGroupLink->link($this->uri(array('action'=>'selectunitgroup', 'minor_groupid'=>$minorGroupId,'selected'=>'init_10')));
     $objminorGroupLink->link = $minorGroupDesc;

     //$sql = "SELECT (major_groupid)  FROM tbl_award_socminorGroup";
     //$lowestDate = $this->objDbminorGroups->getArray($sql);
     //$lowest = current($lowestDate);

     //if($minorGroup['dateCreated'] > $lowest['low'])
     //{
	     // Create delete icon
	    // $param = array('action' => 'deleteminorGroup', 'minorGroupId' => $minorGroupId, 'majorGroupId'=>$majorGroupId,'selected'=>'init_10',);
	     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssoc_deleteminorGroup');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($minorGroupId, $param, 'lrssoc', $deletephrase); 
     //}
     // Create edit icon
     $param = $this->uri(array('action' => 'editminorgroup', 'minorGroupId' => $minorGroupId, 'subMajorGroupId' => $subMajorGroup['id'], 'majorGroupId'=>$majorGroupId,'selected'=>'init_10'));
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editminorGroup');
     $editIcon = $objEditIcon->getEditIcon($param); 
     
     $class = ($class=='odd')? 'even' : 'odd';

     //Add sub major groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objminorGroupLink->show(), '60%', '', '', $class, '');
     $objSelectTable->addCell($editIcon, '15%', '' ,'center', $class, '');
  }
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_norecordsavailable','award'), '', '', '', 'noRecordsMessage', 'colspan=2');
     $objSelectTable->endRow();
}


$linkBack = new link($this->uri(array('action'=>'selectsubmajorgroup', 'majorGroupId'=>$majorGroupId, 'selected'=>'init_10')));
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

$objminorGroupForm->addToForm($objSelectTable->show());
//Add content to the output layer
echo $objminorGroupForm->show();

?>