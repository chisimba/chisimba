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
* Delete Branch list template for the LRS org
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
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$objDeleteBranchForm = new form('lrsorg');

// Create delete icon
$param = array('action' => 'confirmdeletebranch', 'unionId' => $unionId, 'branchId'=>$branchId, 'selected'=>'init_10');
$objDelIcon = &$this->newObject('geticon', 'htmlelements');
$deletephrase = $this->objLanguage->languageText('mod_lrsorg_delete_branch', 'award');
$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($branchId, $param, 'lrsorg', $deletephrase); 

$sql = "SELECT COUNT(id) AS count FROM tbl_award_unit_branch WHERE id = '$branchId'";
$unitCount = $this->objDbUnitBranches->getArray($sql);
$count = current($unitCount);

$valueRow = $this->dbBranch->getRow('id', $branchId);
$setName = $valueRow['name'];

$rep = array('NAME' => $setName, 'COUNT' => $count['count']);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 3;
$header->str = $setName."  ".$deleteIcon;
$objDeleteBranchForm->addToForm($header->show());

$objaddeditHeadTable = new htmlTable('lrsorg');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->code2Txt("mod_lrsorg_delete_counts_branch", 'award', $rep)."</i>");
$objaddeditHeadTable->endRow();

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrsorg_unitlist','award'), '80%', '', '', '');
$objSelectTable->endHeaderRow();

$bargainingUnits = $this->objDbUnitBranches->getAll("WHERE id = '$branchId'");

$class = '';

if(count($bargainingUnits) > '0'){
  foreach($bargainingUnits as $unit){ 
  	 $unitRow = $this->objDbUnit->getRow('id', $unit['id']);
     //Get group description
     $unitName = $unitRow['name'];
     
     //Create link to unit group template
     $objUnitLink =& $this->newObject('link', 'htmlelements');
     $objUnitLink->link($this->uri(array('action'=>'bargainingunitoverview', 'id'=>$unit['unitId'],'selected'=>'init_10'), 'award'));
     $objUnitLink->link = $unitName;

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objUnitLink->show(), '', '', '', $class, '');
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}


$linkDelete = $this->objLanguage->languageText("word_delete");
$location = $this->uri(array('action' => 'confirmdeletebranch', 'unionId' => $unionId, 'branchId'=>$branchId, 'selected'=>'init_10'), 'award');
$deletephrase = $this->objLanguage->languageText('mod_lrsorg_delete_branch','award');
$objConfirm = $this->newObject('confirm','utilities');
$objConfirm->setConfirm($linkDelete, $location, $deletephrase, NULL);

$linkBack = new link($this->uri(array('action'=>'viewbranch', 'unionId'=>$unionId, 'selected'=>'init_10' ), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

$objSelectTable->startRow();
$objSelectTable->addCell("<br />");
$objSelectTable->endRow();

$objSelectTable->startRow();
$objSelectTable->addCell($objConfirm->show().' / '.$linkBack->show());
$objSelectTable->endRow();

$objDeleteBranchForm->addToForm($objaddeditHeadTable->show());
$objDeleteBranchForm->addToForm($objSelectTable->show());

echo $objDeleteBranchForm->show();
?>