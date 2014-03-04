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
* Delete Trade Union list template for the LRS org
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

$objDeleteTradeUnionForm = new form('lrsorg');

// Create delete icon
$param = array('action' => 'confirmdeletetradeunion', 'unionId' => $unionId, 'selected'=>'init_10');
$objDelIcon = &$this->newObject('geticon', 'htmlelements');
$deletephrase = $this->objLanguage->languageText('mod_lrssic_delete_trade_union', 'award');
$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($unionId, $param, 'lrsorg', $deletephrase); 

$sql = "SELECT COUNT(id) AS count FROM tbl_award_branch WHERE partyid = '$unionId'";
$branchCount = $this->objDbBranch->getArray($sql);
$count = current($branchCount);

$allBranches = $this->objDbBranch->getAll("WHERE partyid = '$unionId'");
$allUnits = '';

foreach ($allBranches as $b)
{
	$branch = $b['id'];
	$sql = "SELECT COUNT(branchid) AS count FROM tbl_award_unit_branch WHERE branchid = '$branch'";
	$unitCount = $this->objDbUnitBranches->getArray($sql);
	$countUnits = current($unitCount);
	$allUnits += $countUnits['count'];
}


$valueRow = $this->objDbParty->getRow('id', $unionId);
$setAbbreviation = $valueRow['abbreviation'];
$setName = $valueRow['name'];

$rep = array('ABBR' => $setAbbreviation, 'NAME' => $setName, 'COUNT' => $count['count'], 'UNITCOUNT' => $allUnits);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 3;
$header->str = $setName."  ".$deleteIcon;
$objDeleteTradeUnionForm->addToForm($header->show());

$objaddeditHeadTable = new htmlTable('lrsorg');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->code2Txt("mod_lrsorg_delete_counts",'award', $rep)."</i>");
$objaddeditHeadTable->endRow();

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<br />");
$objaddeditHeadTable->endRow();

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrsorg_branchlist', 'award'), '80%', '', '', '');
$objSelectTable->endHeaderRow();

$branches = $this->objDbBranch->getAll("WHERE partyid = '$unionId' ORDER BY name");

$class = '';

if(count($branches) > '0'){
  foreach($branches as $branch){ 
     //Get group description
     $branchName = $branch['name'];
     $branchId = $branch['id'];
     
     //Create link to unit group template
     $objBranchLink =& $this->newObject('link', 'htmlelements');
     $objBranchLink->link($this->uri(array('action'=>'deletebranch', 'branchId'=>$branchId, 'unionId'=>$unionId, 'selected'=>'init_10')));
     $objBranchLink->link = $branchName;

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objBranchLink->show(), '', '', '', $class, '');
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$linkDelete = $this->objLanguage->languageText("word_delete");
$location = $this->uri(array('action' => 'confirmdeletetradeunion', 'unionId' => $unionId, 'selected'=>'init_10'), 'award');
$deletephrase = $this->objLanguage->languageText('mod_lrssic_delete_trade_union', 'award');
$objConfirm = $this->newObject('confirm','utilities');
$objConfirm->setConfirm($linkDelete, $location, $deletephrase, NULL);

$linkBack = new link($this->uri(array('action'=>'viewtradeunion', 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

$objSelectTable->startRow();
$objSelectTable->addCell("<br />");
$objSelectTable->endRow();

$objSelectTable->startRow();
$objSelectTable->addCell($objConfirm->show().' / '.$linkBack->show());
$objSelectTable->endRow();

$objDeleteTradeUnionForm->addToForm($objaddeditHeadTable->show());
$objDeleteTradeUnionForm->addToForm($objSelectTable->show());

echo $objDeleteTradeUnionForm->show();
?>