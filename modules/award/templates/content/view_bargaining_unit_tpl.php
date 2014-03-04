<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS org
*/

/**
* Branch list template for the LRS org
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');


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
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$objBargainingUnitForm = new form('lrsorg');

$unionRow = $this->objDbParty->getRow('id', $unionId);
$branchRow = $this->objDbBranch->getRow('id', $branchId);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrsorg_bargaining_unit', 'award');
$objBargainingUnitForm->addToForm($header->show());

//create heading
$headerBranchTitle = $this->getObject('htmlheading','htmlelements');
$headerBranchTitle->type = 3;
$headerBranchTitle->str = $this->objLanguage->languageText('mod_lrsorg_branch', 'award').': '.$branchRow['name'];
$objBargainingUnitForm->addToForm($headerBranchTitle->show());

$headerTradeTitle = $this->getObject('htmlheading','htmlelements');
$headerTradeTitle->type = 4;
$headerTradeTitle->str = $this->objLanguage->languageText('mod_lrsorg_tradeunion', 'award').': '.$unionRow['name'];
$objBargainingUnitForm->addToForm($headerTradeTitle->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable = $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '80%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_edit', 'award'), '20%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';

if(count($bargainingUnits) > 0){
  foreach($bargainingUnits as $unit){ 
  	 $unitRow = $this->objDbUnit->getRow('id', $unit['unitid']);
     //Get group description
     $unitName = $unitRow['name'];
     
     //Create link to unit group template
     $objUnitLink = $this->newObject('link', 'htmlelements');
     $objUnitLink->link($this->uri(array('action'=>'bargainingunitoverview', 'unitId'=>$unit['unitid'], 'selected'=>'init_10'), 'award'));
     $objUnitLink->link = $unitName;

     // Create edit icon
     $param = $this->uri(array('action' => 'editbargainingunit', 'unitId'=>$unit['unitid'], 'selected'=>'init_10'), 'award');
     $objEditIcon = $this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';

     //Add minor groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objUnitLink->show(), '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'viewbranch', 'unionId'=>$unionId, 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

//Create htmltable for selectmajorgroup form elements
$objAddTable = $this->newObject('htmltable', 'htmlelements');
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
$objAddTable->addCell($linkBack->show());
$objAddTable->addCell($linkExit->show(), '','','right');
$objAddTable->endRow();

$objBargainingUnitForm->addToForm($objSelectTable->show());
$objBargainingUnitForm->addToForm($objAddTable->show());

echo $objBargainingUnitForm->show();

?>