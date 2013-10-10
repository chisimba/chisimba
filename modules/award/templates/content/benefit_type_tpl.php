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
* Benefit Type list template for the LRS SIC
* Author Brent van Rensburg
*/


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

$objBenefitTypeForm = new form('benefitTypeForm');

// Create add icon
$param = $this->uri(array('action' => 'addeditbenefittype','selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('word_add');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('phrase_benefit_type').' '.$addIcon;
$objBenefitTypeForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '50%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_edit'), '10%', '', '', '');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_delete'), '10%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';

if(count($benefitType) > '0'){
  foreach($benefitType as $type){ 
     //Get benefit type
     $benefitTypeId = $type['id'];
     $benefitTypeName = $type['name'];

     //Create link to unit group template
     $objLink =& $this->newObject('link', 'htmlelements');
     $objLink->link($this->uri(array('action'=>'viewbenefitname', 'benefitTypeId'=>$benefitTypeId,'selected'=>'init_10')));
     $objLink->link = $benefitTypeName;
     
  	 // Create delete icon
   	//$param = $this->uri(array('action' => 'deletebenefittype', 'benefitTypeId'=>$benefitTypeId));
     	//$objDelIcon = &$this->newObject('geticon', 'htmlelements');
    	//$deleteIcon = $objDelIcon->getDeleteIcon($param); 
     
     // Create edit icon
     $param = $this->uri(array('action' => 'addeditbenefittype', 'benefitTypeId'=>$benefitTypeId, 'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('word_edit');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add benefit type to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objLink->show(), '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', 'center', $class, '');
	// $objSelectTable->addCell($deleteIcon, '', '', 'center', $class, '');
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$objAddLink =& $this->newObject('link', 'htmlelements');
$objAddLink->link($this->uri(array('action' => 'addeditbenefittype','selected'=>'init_10')));
$objAddLink->link = $this->objLanguage->languageText('mod_lrsbenefittypes_addbenefit', 'award');

$linkExit = new link($this->uri(array('action'=>'admin'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

//Create htmltable for selectmajorgroup form elements
$objAddTable =& $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell("<br />");
$objAddTable->addCell("<br />");
$objAddTable->endRow();

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell($objAddLink->show());
$objAddTable->addCell($linkExit->show(), '', '', 'right');
$objAddTable->endRow();

$objBenefitTypeForm->addToForm($objSelectTable->show());
$objBenefitTypeForm->addToForm($objAddTable->show());

echo $objBenefitTypeForm->show();
?>