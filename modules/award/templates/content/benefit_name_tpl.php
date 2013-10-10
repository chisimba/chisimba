<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS benefit types
*/

/**
* Benefit name list template for the LRS benefit types
* Author Brent van Rensburg
*/

//Get div info
if(isset($benefitType)){
  $typeid = $benefitType['id'];
  $name = $benefitType['name'];
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

$objBenefitNameForm = new form('benefitNameForm');

// Create add icon
$param = $this->uri(array('action' => 'addeditbenefitname','typeid'=>$typeid,'selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('word_add');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('phrase_benefit_name').' '.$addIcon;
$objBenefitNameForm->addToForm($header->show());

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 3;
$header->str = $this->objLanguage->languageText('phrase_benefit_type').': '.$name;
$objBenefitNameForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '40%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrsbenefittypes_unit_measurement','award'), '20%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_edit'), '10%', '', '', '');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_delete'), '10%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';

if(count($benefitName) > '0'){
  foreach($benefitName as $name){ 
     //Get benefit type
     $benefitNameId = $name['id'];
     $benefitNameName = $name['name'];
     $benefitUnit = $name['measure'];

     // Create delete icon
     // $param = $this->uri(array('action' => 'deletebenefitname', 'typeid'=>$typeid, 'benefitNameId'=>$benefitNameId,'selected'=>'init_10'));
     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
     //$deleteIcon = $objDelIcon->getDeleteIcon($param); 
     
     // Create edit icon
     $param = $this->uri(array('action' => 'addeditbenefitname', 'typeid'=>$typeid, 'benefitNameId'=>$benefitNameId,'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('word_edit');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add benefit type to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($benefitNameName, '', '', '', $class, '');
	 $objSelectTable->addCell($benefitUnit, '', '', 'center', $class, '');
     $objSelectTable->addCell($editIcon, '', '', 'center', $class, '');
	 // $objSelectTable->addCell($deleteIcon, '', '', 'center', $class, '');
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_norecordsavailable','award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$objAddLink =& $this->newObject('link', 'htmlelements');
$objAddLink->link($this->uri(array('action' => 'addeditbenefitname', 'typeid'=>$typeid,'selected'=>'init_10'),'award'));
$objAddLink->link = $this->objLanguage->languageText('mod_lrsbenefittypes_addbenefitname','award');

$linkExit = new link($this->uri(array('action'=>'admin')));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'viewbenefittype', 'selected'=>'init_10')));
$linkBack->link = $this->objLanguage->languageText("word_back");

//Create htmltable for selectmajorgroup form elements
$objAddTable = $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

$txthidden = new textinput('typeid', $typeid, 'hidden');

$objAddTable->startRow();
$objAddTable->addCell($txthidden->show());
$objAddTable->addCell("<br />");
$objAddTable->endRow();

$objAddTable->startRow();
$objAddTable->addCell($objAddLink->show().' / '.$linkBack->show());
$objAddTable->addCell($linkExit->show(), null, null, 'right');
$objAddTable->endRow();

$objBenefitNameForm->addToForm($objSelectTable->show());
$objBenefitNameForm->addToForm($objAddTable->show());

echo $objBenefitNameForm->show();
?>