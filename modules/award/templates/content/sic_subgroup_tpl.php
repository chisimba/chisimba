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
* sic sub group list template for the LRS SIC
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');

//Get sub major group info
if(isset($sicMajorDiv)){
  $sicMajorDivId = $sicMajorDiv['id'];
  $sicMajorDivDesc = $sicMajorDiv['description'];
}
//Get div info
if(isset($sicDiv)){
  $sicDivId = $sicDiv['id'];
  $sicDivDesc = $sicDiv['description'];
}
//Get major group info
if(isset($sicMajorGroup)){
  $sicMajorGroupId = $sicMajorGroup['id'];
  $sicMajorGroupDesc = $sicMajorGroup['description'];
}
//Get group info
if(isset($sicGroup)){
  $sicGroupId = $sicGroup['id'];
  $sicGroupDesc = $sicGroup['description'];
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

$objSubGroupForm = new form('lrssic');
// Create add icon
$param = $this->uri(array('action' => 'addeditsubgroup', 'sicGroupId'=>$sicGroupId, 'selected'=>'init_10'));
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssic_group', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrssic_subgroup', 'award').' '.$addIcon;
$objSubGroupForm->addToForm($header->show());

//create heading
$headerTitle = $this->getObject('htmlheading','htmlelements');
$headerTitle->type = 3;
$headerTitle->str = $this->objLanguage->languageText('mod_lrssic_group', 'award').': '.$sicGroup['description'];
$objSubGroupForm->addToForm($headerTitle->show());

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
if(count($sicSubGroups) > '0'){
  foreach($sicSubGroups as $sicSubGroup){ 
     //Get group description
     $sicSubGroupName = $sicSubGroup['code'];
     $sicSubGroupDesc = $sicSubGroup['description'];
     $sicSubGroupNotes = $sicSubGroup['notes'];
     //Get group id
     $sicSubGroupId = $sicSubGroup['id'];
     
    // $sql = "SELECT MIN(dateCreated) AS low FROM tbl_award_sicsubgroup";
     //$lowestDate = $this->objDbSicSubGroups->getArray($sql);
     //$lowest = current($lowestDate);
     
     //$sicSubGroupDate = date("Y-m-d", strtotime($sicSubGroup['dateCreated']));
     //$low = date("Y-m-d", strtotime($lowest['low']));
     
     /*if($sicSubGroupDate > $low)
     {
	 	 // Create delete icon
	     $param = array('action' => 'deletesubgroup', 'sicSubGroupId' => $sicSubGroupId, 'sicGroupId'=>$sicGroupId);
	     $objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     $deletephrase = $this->objLanguage->languageText('mod_lrssic_deletesubgroup');
	     $deleteIcon = $objDelIcon->getDeleteIconWithConfirm($sicSubGroupId, $param, 'lrssic', $deletephrase); 
     }*/
     
     // Create edit icon
     $param = $this->uri(array('action' => 'addeditsubgroup', 'sicSubGroupId' => $sicSubGroupId, 'sicGroupId'=>$sicGroupId, 'selected'=>'init_10'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     //Add minor groups to table
     $objSelectTable->startRow();
 //    $objSelectTable->addCell($sicSubGroupName, '', '', '', $class, '');
     $objSelectTable->addCell($sicSubGroupDesc, '', '', '', $class, '');
     $objSelectTable->addCell($sicSubGroupNotes, '', '', '', $class, '');
	 $objSelectTable->addCell($editIcon, '', '', '', $class, '');
}
	/* if($sicSubGroupDate > $low)
     {
     	$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
     }else{
     	$objSelectTable->addCell("<br />", '', '', '', $class, '');
     }
     $objSelectTable->endRow();
     } */   
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}


$objAddSubGroupLink =& $this->newObject('link', 'htmlelements');
$objAddSubGroupLink->link($this->uri(array('action' => 'addeditsubgroup', 'sicGroupId'=>$sicGroupId, 'selected'=>'init_10')));
$objAddSubGroupLink->link = $this->objLanguage->languageText('mod_lrssic_addsubgroup', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$linkBack = new link($this->uri(array('action'=>'selectsicgroup', 'sicMajorGroupId'=>$sicMajorGroupId, 'selected'=>'init_10', 'award'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

//Create htmltable for selectmajorgroup form elements
$objAddTable =& $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

$txtHidden = new textinput('sicGroupId', $sicGroupId, 'hidden');

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell("<br />");
$objAddTable->addCell($txtHidden->show());
$objAddTable->endRow();

//Add minor groups to table
$objAddTable->startRow();
$objAddTable->addCell($objAddSubGroupLink->show().' / '.$linkBack->show());
$objAddTable->addCell($linkExit->show(), '', '', 'right');
$objAddTable->endRow();

$objSubGroupForm->addToForm($objSelectTable->show());
$objSubGroupForm->addToForm($objAddTable->show());

echo $objSubGroupForm->show();
?>
