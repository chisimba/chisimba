<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package Award
*/

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the tabbedbox class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');


//Set form action
$formAction = $this->uri(array('action'=>'searchuserlist','selected'=>'init_10'));
//Create new form object
$objSearchForm = new form('search', $formAction);
$objSearchForm->displayType = '3';

//Create htmltable for addmajorgroup form elements  
$objSearchTable = $this->newObject('htmltable', 'htmlelements');
$objSearchTable->cellspacing = '2';
$objSearchTable->cellpadding = '2';
$objSearchTable->width = '90%';

if (!isset($searchTerm)) {
	$searchTerm = '';
}
//Create textinput for input of search term
$objSearchInput = new textinput('searchterm',$searchTerm);
//Create label for search term input field
$searchLabel = new label($this->objLanguage->languageText('mod_lrs_user_search', 'award'), 'input_searchterm');

// Create a submit button
$objSubmit = new button('submit'); 
// Set the button type to submit
$objSubmit->setToSubmit(); 
// Use the language object to add the word
$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_search") . ' ');



$objSearchTable->startRow();
$objSearchTable->addCell($searchLabel->show(), '20%', '', '', 'odd', '');
$objSearchTable->addCell($objSearchInput->show(),'20%');
$objSearchTable->addCell($objSubmit->show());
$objSearchTable->endRow();

//Add table to form
$objSearchForm->addToForm($objSearchTable->show());

$objUserListForm = new form('lrslist');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrs_useradmin', 'award');
//$objJobCodeForm->addToForm($header->show());

//Create table for displaying data
$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellspacing = '2';
$objTable->cellpadding = '2';
//$objTable->width = '90%';

//Set table header row
$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('word_username'), '15%');
$objTable->addHeaderCell($this->objLanguage->languageText('word_position'), '10%');
$objTable->addHeaderCell($this->objLanguage->languageText('phrase_tradeunion'), '30%');
$objTable->addHeaderCell($this->objLanguage->languageText('word_edit'),'5%');
$objTable->addHeaderCell($this->objLanguage->languageText('word_delete'),'5%');
$objTable->endHeaderRow();

$class = '';

if (isset($userList)) {
  if(!empty($userList)){ 
    $objDbUserReg = $this->getObject('dbuserreg');
	foreach($userList as $list) {
    	$lrsList = $objDbUserReg->getRow('userid', $list['userid']);
    	$tuList = $this->objDbParty->getRow('id', $lrsList['tuid']);

        // Set variables
        $userName = $list['username'];
        $position = $lrsList['position'];
    	$tradeUnion = $tuList['name'];
    	
        // Create delete icon
        $param = array('action' => 'deleteuser', 'userid' => $list['userid']);
        $objDelIcon = $this->newObject('geticon', 'htmlelements');
        $deletephrase = $this->objLanguage->languageText('phrase_deleterecord');
        $deleteIcon = $objDelIcon->getDeleteIconWithConfirm($list['userid'], $param, 'award', $deletephrase); 
        // Create edit icon
        $param = $this->uri(array('action' => 'edituser', 'userId' => $list['userid'], 'selected'=>'init_10'));
        $objEditIcon = $this->newObject('geticon', 'htmlelements');
        $objEditIcon->alt = $this->objLanguage->languageText('word_edit');
        $editIcon = $objEditIcon->getEditIcon($param); 
       
        $class = ($class=='odd')? 'even' : 'odd';
        
        //Add content to table
        $objTable->startRow();
        $objTable->addCell($userName, '', '', '', $class);
        $objTable->addCell($position, '', '', '', $class);
        $objTable->addCell($tradeUnion, '', '', '', $class);
        $objTable->addCell($editIcon, '', '' ,'', $class, '');
     	$objTable->addCell($deleteIcon, '', '' ,'', $class, '');
        $objTable->endRow();
       } 
     } else { 
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText("phrase_norecords"), '', '', '', 'noRecordsMessage', 'colspan=2');
        $objTable->endRow();
    }
}

$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->endRow();

//Create table for displaying data
$objlinkTable = $this->newObject('htmltable', 'htmlelements');
$objlinkTable->cellspacing = '2';
$objlinkTable->cellpadding = '2';
//$objlinkTable->width = '90%';

// set up add grade link
$addLink = new link($this->uri(array('action' => 'edituser', 'selected'=>'init_10')));
$addLink->link = $this->objLanguage->languageText("mod_lrs_add_user", 'award');

//link back to previous step in path
$linkback = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'),'award'));
$linkback->link = $this->objLanguage->languageText('mod_lrs_link_back', 'award');

$objlinkTable->startRow();
$objlinkTable->addCell($addLink->show().' / '.$linkback->show());
$objlinkTable->endRow();

$objUserListForm->addToForm($objTable->show());

echo $header->show();
echo $objSearchForm->show();
echo $objUserListForm->show();
echo $objlinkTable->show();
?>