<?php
/**
 * @Model extension of controller that displays entries
 * @authors:Godwin Du Plessis, Ewan Burns, Helio Rangeiro, Jacques Cilliers, Luyanda Mgwexa, George Amabeoku, Charl Daniels, and Qoane Seitlheko.
 * @copyright 2007 University of the Western Cape
 */
// Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);
// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
// Initialize left column
$leftSideColumn = $this->leftMenu->show();
$rightSideColumn = NULL;
$middleColumn = NULL;
// Create add icon and link to add template
$objAddIcon = $this->newObject('geticon', 'htmlelements');
$objLink = $this->uri(array(
    'action' => 'link'
));
$objAddIcon->setIcon("add", "gif");
$objAddIcon->alt = $objLanguage->languageText('mod_phonebook_addicon', 'phonebook');
$add = $objAddIcon->getAddIcon($objLink);
// Create header with add icon
$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
$pgTitle->str = $objLanguage->languageText('mod_phonebook_head', 'phonebook') . "&nbsp;" . $add;
//create array to hold data and set the language items
$tableRow = array();
$tableHd[] = $objLanguage->languageText('mod_phonebook_contact', 'phonebook');
$tableHd[] = $objLanguage->languageText('mod_phonebook_email', 'phonebook');
$tableHd[] = $objLanguage->languageText('mod_phonebook_landline', 'phonebook');
$tableHd[] = $objLanguage->languageText('mod_phonebook_cellnumber', 'phonebook');
$tableHd[] = $objLanguage->languageText('mod_phonebook_address', 'phonebook');
$tableHd[] = $objLanguage->languageText('mod_phonebook_delete', 'phonebook');
$tableHd[] = $objLanguage->languageText('mod_phonebook_update', 'phonebook');
// Create the table header for display
$objTableClass = $this->newObject('htmltable', 'htmlelements');
$objTableClass->addHeader($tableHd, "heading");
$index = 0;
$rowcount = 0;
//language item for no records
$norecords = $objLanguage->languageText('mod_phonebook_nodata', 'phonebook');
//A statement not to display the records if it is empty.
if (empty($records)) {
    $objTableClass->addCell($norecords, NULL, NULL, 'center', 'noRecordsMessage', 'colspan="7"');

}
 else {
    //Create an array for each value in the table.
    foreach($records as $record) {
        $rowcount++;
        // Set odd even colour scheme
        $class = ($rowcount%2 == 0) ? 'odd' : 'even';
        $objTableClass->startRow();
        //add id
        $id = $record['id'];
        $records == $objUser->userId();
        //add first name
        $username = $record['firstname'] . '&nbsp;' . '&nbsp;' . '&nbsp;' . '&nbsp;' . $record['lastname'];
        $records == $objUser->userId();
        $objTableClass->addCell($username, '', 'center', 'center', $class);
        //add e-mail
        $email = $record['emailaddress'];
        $records == $objUser->userId();
        $objTableClass->addCell($email, '', 'center', 'center', $class);
        //add landline
        $landline = $record['landlinenumber'];
        $records == $objUser->userId();
        $objTableClass->addCell($landline, '', '', 'center', $class);
        //add cell number
        $cell = $record['cellnumber'];
        $records == $objUser->userId();
        $objTableClass->addCell($cell, '', 'center', 'center', $class);
        //add address
        $address = $record['address'];
        $records == $objUser->userId();
        $objTableClass->addCell($address, '', 'center', 'center', $class);
        // Create delete icon and delete action
        $objDelIcon = $this->newObject('geticon', 'htmlelements');
        $delLink = array(
            'action' => 'deleteentry',
            'id' => $id,
            'module' => 'phonebook',
            'confirm' => 'yes',
        );
        $deletephrase = $objLanguage->languageText('mod_phonebook_deleteicon', 'phonebook');
        $conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'phonebook', $deletephrase);
        $update = $conf;
        $records == $objUser->userId();
        $objTableClass->addCell($update, '', 'center', 'center', $class);
        // Create edit icon and action
        $this->loadClass('link', 'htmlelements');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $link = new link($this->uri(array(
            'action' => 'editentry',
            'id' => $id
        ) , 'phonebook'));
        $objIcon->setIcon('edit');
        $link->link = $objIcon->show();
        $update = $link->show();
        $objTableClass->addCell($update, '', 'center', 'center', $class);
        $objTableClass->endRow();
    } //end of loop   

}
//shows the array in a table
$ret = $objTableClass->show();
$middleColumn = $pgTitle->show() . $ret;
//add left column
$cssLayout->setLeftColumnContent($leftSideColumn);
$cssLayout->setRightColumnContent($rightSideColumn);
//add middle column
$cssLayout->setMiddleColumnContent($middleColumn);
echo $cssLayout->show();
?>
