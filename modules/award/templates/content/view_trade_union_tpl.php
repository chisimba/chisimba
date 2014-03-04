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
* Trade Union list template for the LRS org
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

$objTradeUnionForm = new form('lrstradeunion');

// Create add icon
$param = $this->uri(array('action' => 'addedittradeunion','selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_addmajordiv', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrsorg_tradeunion', 'award').' '.$addIcon;
$objTradeUnionForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '60%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_abbreviation'), '10%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_edit', 'award'), '10%', '', '', '');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_delete', 'award'), '10%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';

if(count($tradeUnions) > '0'){
  foreach($tradeUnions as $tradeUnion){ 
     //Get group description
     $unionName = $tradeUnion['name'];
     $unionAbb = $tradeUnion['abbreviation'];
     $unionId = $tradeUnion['id'];

     //Create link to unit group template
     $objTradeUnionLink =& $this->newObject('link', 'htmlelements');
     $objTradeUnionLink->link($this->uri(array('action'=>'viewbranch', 'unionId'=>$unionId, 'selected'=>'init_10')));
     $objTradeUnionLink->link = $unionName;
     
     // Create delete icon
//      $param = $this->uri(array('action' => 'deletetradeunion', 'unionId'=>$unionId, 'selected'=>'init_10'));
//      $objDelIcon = &$this->newObject('geticon', 'htmlelements');
//      $deleteIcon = $objDelIcon->getDeleteIcon($param); 
     // Create edit icon
     $param = $this->uri(array('action' => 'addedittradeunion', 'unionId'=>$unionId, 'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';

     //Add minor groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objTradeUnionLink->show(), '', '', '', $class, '');
     $objSelectTable->addCell($unionAbb, '', '', '', $class, '');
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

$objAddTradeUnionLink =& $this->newObject('link', 'htmlelements');
$objAddTradeUnionLink->link($this->uri(array('action' => 'addedittradeunion', 'selected'=>'init_10')));
$objAddTradeUnionLink->link = $this->objLanguage->languageText('mod_lrssic_add_tradeunion', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
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
$objAddTable->addCell($objAddTradeUnionLink->show());
$objAddTable->addCell($linkExit->show(),'','','right');
$objAddTable->endRow();

$objTradeUnionForm->addToForm($objSelectTable->show());
$objTradeUnionForm->addToForm($objAddTable->show());

echo $objTradeUnionForm->show();

?>
