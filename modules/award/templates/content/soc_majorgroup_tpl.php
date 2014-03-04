<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check
/**
* @package LRS SOC
*/

/**
* Major group list template for the LRS SOC
* Author Brent van Rensburg
*/
//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');

$objMajorGroupForm = new form('lrssoc');


// Create add icon
$param = $this->uri(array('action' => 'editSocmajorgroup','selected'=>'init_10'),'award');
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssoc_majorgroup', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrssoc_majorgroup', 'award').' '.$addIcon;
$objMajorGroupForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable = $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_name'), '60%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_edit'),'10%', '' ,'' ,'');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('word_delete'),'10%', '' ,'' ,'');
//$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_delete','award'), '10%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';

if(count($majorGroups) > '0'){
  foreach($majorGroups as $majorGroup){
     $majorGroupDesc = $majorGroup['description'];
     //Get group id
     $majorGroupId = $majorGroup['id'];
  
     //Create link to unit group template
     $objMajorGroupLink = $this->newObject('link', 'htmlelements');
     $objMajorGroupLink->link($this->uri(array('action'=>'selectsubmajorgroup', 'majorGroupId'=>$majorGroupId,'selected'=>'init_10'),'award'));
     $objMajorGroupLink->link = $majorGroupDesc;

   // $sql = "SELECT MIN(dateCreated) AS low FROM tbl_award_socmajorgroup";
    //$lowestDate = $this->objDbSocMajorGroup->getArray($sql);
     //$lowest = current($lowestDate);

    //$majorGroupDate = date("Y-m-d", strtotime($majorGroup['dateCreated']));
    //$low = date("Y-m-d", strtotime($lowest['low']));

//     if($majorGroupDate > $low)
 
//	     $param = array('action' => 'deletemajorgroup', 'majorGroupId' => $majorGroupId,'selected'=>'init_10');
//	     $objDelIcon = &$this->newObject('geticon', 'htmlelements');
	     //$deletephrase = $this->objLanguage->languageText('mod_lrssoc_deletemajorgroup', 'award');
	     //$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($majorGroupId, $param, 'lrssoc', $deletephrase); 
    
    
 // Create edit icon
     $param = $this->uri(array('action' => 'editSocmajorgroup', 'majorGroupId' => $majorGroupId,'selected'=>'init_10','award'));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editmajorgroup','award');
     $editIcon = $objEditIcon->getEditIcon($param); 

	 $class = ($class=='odd')? 'even' : 'odd';
     //Add minor groups to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($objMajorGroupLink->show(), '60%', '', '', $class, '');
     $objSelectTable->addCell($editIcon, '15%', '' ,'center', $class, '');
    // if($majorGroupDate > $low)
     //{
     	//$objSelectTable->addCell($deleteIcon, '', '', '', $class, '');
    // }
  //  else
    // {
     	//$objSelectTable->addCell("<br />",'','','',$class);
    // }
     $objSelectTable->endRow();
 
  }
 } else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText('mod_lrssoc_norecordsavailable','award'), '', '', '', 'noRecordsMessage', 'colspan=2');
     $objSelectTable->endRow();
}

//Create search tab for searching for soc names 
//Set form action

$formAction = ($this->uri(array('action'=>'search', 'group'=>'major','selected'=>'init_10'),'award'));
//Create new form object
$objSearchForm = new form('search', $formAction);
$objSearchForm->displayType = '3';

//Create htmltable for addmajorgroup form elements  
$objSearchTable = $this->newObject('htmltable', 'htmlelements');
$objSearchTable->cellspacing = '2';
$objSearchTable->cellpadding = '2';
$objSearchTable->width = '90%';

//Create textinput for input of search term
$objSearchInput = new textinput('searchterm');
//Create label for search term input field
$searchLabel = new label($this->objLanguage->languageText('mod_lrssoc_occupationname', 'award'), 'input_searchterm');

// Create a submit button
$objSubmit = new button('submit'); 
// Set the button type to submit
$objSubmit->setToSubmit(); 
// Use the language object to add the word
$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_search") . ' ');

//create heading
$headerSelect = $this->getObject('htmlheading','htmlelements');
$headerSelect->type = 4;
$headerSelect->str = $this->objLanguage->languageText('mod_lrssoc_searchforoccupation', 'award');
$objSearchForm->addToForm($headerSelect->show());

$objSearchTable->startRow();
$objSearchTable->addCell($searchLabel->show(), '20%', '', '', 'odd', '');
$objSearchTable->addCell($objSearchInput->show(),'20%');
$objSearchTable->addCell($objSubmit->show());
$objSearchTable->endRow();

$objSearchTable->startRow();
$objSearchTable->addCell("<br />");
$objSearchTable->addCell("<br />");
$objSearchTable->addCell("<br />");
$objSearchTable->endRow();

//Add validation here
$objSearchForm->addRule('searchterm', $this->objLanguage->languageText('mod_lrssoc_valrequired', 'award'), 'required');

//Add table to form
$objSearchForm->addToForm($objSearchTable->show());

// set up exit link
$exitLink = new link($this->uri(array('action'=>'admin','selected'=>'init_10')));
$exitLink->link = $this->objLanguage->languageText("word_exit");

$objMajorGroupForm->addToForm($objSelectTable->show());
//Add content to the output layer
$middleColumnContent = '';
$middleColumnContent .= $objMajorGroupForm->show();
$middleColumnContent .= '</br>'.'&nbsp;'.'</br>';
$middleColumnContent .= $objSearchForm->show();
$middleColumnContent .= $exitLink->show();
echo $middleColumnContent;
?>