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

$objBranchForm = new form('lrsorg');

$unionRow = $this->objDbParty->getRow('id', $unionId);

// Create add icon
$param = $this->uri(array('action' => 'addeditbranch', 'unionId'=>$unionId, 'selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_addmajordiv', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrsorg_branch', 'award').' '.$addIcon;
$objBranchForm->addToForm($header->show());

//create heading
$headerTitle = $this->getObject('htmlheading','htmlelements');
$headerTitle->type = 3;
$headerTitle->str = $this->objLanguage->languageText('mod_lrsorg_tradeunion', 'award').': '.$unionRow['name'];
$objBranchForm->addToForm($headerTitle->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '60%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_edit', 'award'), '10%', '', '', '');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_delete', 'award'), '10%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';

if(count($branches) > '0'){
  foreach($branches as $branch){ 
     //Get group description
     $branchName = $branch['name'];
     $branchId = $branch['id'];
     
     //Create link to unit group template
     $objBranchLink =& $this->newObject('link', 'htmlelements');
     $objBranchLink->link($this->uri(array('action'=>'viewbargainingunit', 'branchId'=>$branchId, 'unionId'=>$unionId, 'selected'=>'init_10')));
     $objBranchLink->link = $branchName;
     
     // Create delete icon
     $param = $this->uri(array('action' => 'deletebranch', 'unionId'=>$unionId, 'branchId'=>$branchId, 'selected'=>'init_10'));
     $objDelIcon = &$this->newObject('geticon', 'htmlelements');
     $deleteIcon = $objDelIcon->getDeleteIcon($param); 
     // Create edit icon
     $param = $this->uri(array('action' => 'addeditbranch', 'unionId'=>$unionId, 'branchId'=>$branchId, 'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objBranchLink->show(), '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
	 //$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$objAddBranchLink =& $this->newObject('link', 'htmlelements');
$objAddBranchLink->link($this->uri(array('action' => 'addeditbranch', 'unionId'=>$unionId, 'selected'=>'init_10')));
$objAddBranchLink->link = $this->objLanguage->languageText('mod_lrsorg_add_branch', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'viewtradeunion', 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

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
$objAddTable->addCell($objAddBranchLink->show().' / '.$linkBack->show());
$objAddTable->addCell($linkExit->show(), '','','right');
$objAddTable->endRow();

$objBranchForm->addToForm($objSelectTable->show());
$objBranchForm->addToForm($objAddTable->show());

echo $objBranchForm->show();

?>