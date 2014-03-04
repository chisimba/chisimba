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
* Major division list template for the LRS SIC
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');


//Get major div info
if(isset($sicMajorDiv)){
  $sicMajorDivId = $sicMajorDiv['id'];
  $sicMajorDivDesc = $sicMajorDiv['description'];
}
//Get div info
if(isset($sicDiv)){
  $sicDivId = $sicDiv['id'];
  $sicDivDesc = $sicDiv['description'];
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

$objMajorDivForm = new form('lrssic');



// Create add icon
$param = $this->uri(array('action' => 'addeditmajordiv', 'selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_addmajordiv', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrssic_majordiv', 'award').' '.$addIcon;
$objMajorDivForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
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

if(count($sicMajorDivs) > '0'){
  foreach($sicMajorDivs as $sicMajorDiv){ 
     //Get group description
     $sicMajorDivName = $sicMajorDiv['code'];
     $sicMajorDivDesc = $sicMajorDiv['description'];
     $sicMajorDivNotes = $sicMajorDiv['notes'];
     //Get group id
     $sicMajorDivId = $sicMajorDiv['id'];
     //Create link to unit group template
     $objMajorDivLink =& $this->newObject('link', 'htmlelements');
     $objMajorDivLink->link($this->uri(array('action'=>'selectsicdiv', 'majorDivId'=>$sicMajorDivId, 'selected'=>'init_10')));
     $objMajorDivLink->link = $sicMajorDivDesc;
     
     //$sql = "SELECT MIN(dateCreated) AS low FROM tbl_award_sicmajordiv"; 
     //$lowestDate = $this->objDbSicMajorDivs->getArray($sql);
     //$lowest = current($lowestDate);
     
     //$sicMajorDivDate = date("Y-m-d", strtotime($sicMajorDiv['dateCreated']));
     //$low = date("Y-m-d", strtotime($lowest['low']));
     
     /*if($sicMajorDivDate > $low)
     {
	 	 // Create delete icon
	     $param = array('action' => 'deletemajordiv', 'sicMajorDivId' => $sicMajorDivId);
	     $objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     $deletephrase = $this->objLanguage->languageText('mod_lrssic_deletemajordiv', 'award');
	     $deleteIcon = $objDelIcon->getDeleteIconWithConfirm($sicMajorDivId, $param, 'award', $deletephrase); 
     }*/

     // Create edit icon
     $param = $this->uri(array('action' => 'addeditmajordiv', 'sicMajorDivId' => $sicMajorDivId, 'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';

     //Add minor groups to table
     $objSelectTable->startRow();
     //$objSelectTable->addCell($sicMajorDivName, '', '', '', $class, '');
     $objSelectTable->addCell($objMajorDivLink->show(), '', '', '', $class, '');
     $objSelectTable->addCell($sicMajorDivNotes, '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
	 /*if($sicMajorDivDate > $low)
     {
     	$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
     }else{
     	$objSelectTable->addCell("<br />", '', '', '', $class, '');
     }*/
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$objAddMajorDivLink =& $this->newObject('link', 'htmlelements');
$objAddMajorDivLink->link($this->uri(array('action' => 'addeditmajordiv', 'selected'=>'init_10')));
$objAddMajorDivLink->link = $this->objLanguage->languageText('mod_lrssic_add_majordiv', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_back");

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
$objAddTable->addCell($objAddMajorDivLink->show());
$objAddTable->addCell($linkExit->show(), NULL, 'top', 'right');
$objAddTable->endRow();

$objMajorDivForm->addToForm($objSelectTable->show());
$objMajorDivForm->addToForm($objAddTable->show());

echo $objMajorDivForm->show();

?>
