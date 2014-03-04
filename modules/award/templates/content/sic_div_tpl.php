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
* sic division  list template for the LRS SIC
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');

//Get sub major group info
if(isset($sicMajorDiv)){
  $sicMajorDivId = $sicMajorDiv['id'];
  $sicMajorDivDesc = $sicMajorDiv['description'];
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

$objDivForm = new form('lrssic');

// Create add icon
$param = $this->uri(array('action' => 'addeditdiv', 'majorDiv'=>$MajorDivId, 'selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_addmajordiv');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrssic_div', 'award').' '.$addIcon;
$objDivForm->addToForm($header->show());

//create heading
$headerTitle = $this->getObject('htmlheading','htmlelements');
$headerTitle->type = 3;
$headerTitle->str = $this->objLanguage->languageText('mod_lrssic_majordiv', 'award').': '.$sicMajorDiv['description'];
$objDivForm->addToForm($headerTitle->show());

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

if(count($sicDivs) > '0'){
  foreach($sicDivs as $sicDiv){ 
     //Get group description
     $sicDivName = $sicDiv['code'];
     $sicDivDesc = $sicDiv['description'];
     $sicDivNotes = $sicDiv['notes'];
     //Get group id
     $sicDivId = $sicDiv['id'];
     //Create link to unit group template
     $objDivLink =& $this->newObject('link', 'htmlelements');
     $objDivLink->link($this->uri(array('action'=>'selectsicmajorgroup', 'sicDivId'=>$sicDivId, 'selected'=>'init_10')));
     $objDivLink->link = $sicDivDesc;
     
     //$sql = "SELECT MIN(dateCreated) AS low FROM tbl_lrs_sic_div";
     //$lowestDate = $this->objDbSicDivs->getArray($sql);
     //$lowest = current($lowestDate);
     
     //$sicDivDate = date("Y-m-d", strtotime($sicDiv['dateCreated']));
     //$low = date("Y-m-d", strtotime($lowest['low']));
     
     //if($sicDivDate > $low)
     //{
	 	 // Create delete icon
	     //$param = array('action' => 'deletediv', 'sicDivId' => $sicDivId, 'majorDiv'=>$MajorDivId, 'selected'=>'init_10');
	     //$objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssic_deletediv');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($sicDivId, $param, 'lrssic', $deletephrase); 
     //}
     
     // Create edit icon
     $param = $this->uri(array('action' => 'addeditdiv', 'sicDivId' => $sicDivId, 'majorDiv'=>$MajorDivId, 'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
  //   $objSelectTable->addCell($sicDivName, '', '', '', $class, '');
     $objSelectTable->addCell($objDivLink->show(), '', '', '', $class, '');
     $objSelectTable->addCell($sicDivNotes, '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
	 //if($sicDivDate > $low)
     //{
     	//$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
     //}else{
     	//$objSelectTable->addCell("<br />", '', '', '', $class, '');
     //}
     $objSelectTable->endRow();
     }    
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$objAddDivLink =& $this->newObject('link', 'htmlelements');
$objAddDivLink->link($this->uri(array('action' => 'addeditdiv', 'majorDiv'=>$MajorDivId, 'selected'=>'init_10')));
$objAddDivLink->link = $this->objLanguage->languageText('mod_lrssic_adddiv', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'selectmajordiv', 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

//Create htmltable for selectmajorgroup form elements
$objAddTable =& $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

$txtHidden = new textinput('majorDiv', $MajorDivId, 'hidden');

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell("<br />");
$objAddTable->addCell($txtHidden->show());
$objAddTable->endRow();

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell($objAddDivLink->show().' / '.$linkBack->show());
$objAddTable->addCell($linkExit->show(), '', '', 'right');
$objAddTable->endRow();

$objDivForm->addToForm($objSelectTable->show());
$objDivForm->addToForm($objAddTable->show());

echo $objDivForm->show();

?>
