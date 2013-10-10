<?php
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$linkAdd = '';
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
// Show the heading
$objHeading->str = $objUser->getSurname() . $objLanguage->languageText("phrase_eportfolio_userinformation", 'eportfolio');
echo $objHeading->show();
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 2;
//view name
// Show the heading
// Show the edit link
$iconEdit = $this->getObject('geticon', 'htmlelements');
$iconEdit->setIcon('edit');
$iconEdit->alt = $objLanguage->languageText("word_edit");
$iconEdit->align = false;
$objLink = &$this->getObject("link", "htmlelements");
$objLink->link($this->uri(NULL, 'userdetails'));
$objLink->link = $iconEdit->show();
$linkEdit = $objLink->show();
$objHeading->str = $objLanguage->languageText("mod_eportfolio_title", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkEdit;
echo "<br/>";
echo $objHeading->show();
echo "<br/>";
// Create a table object
$userTable = &$this->newObject("htmltable", "htmlelements");
$userTable->border = 0;
$userTable->cellspacing = '3';
$userTable->width = "40%";
// Add the table heading.
$userTable->startRow();
$userTable->addHeaderCell("<b>" . $objLanguage->languageText('word_title', 'system') . "</b>");
$userTable->addHeaderCell("<b>" . $objLanguage->languageText('word_surname', 'system') . "</b>");
$userTable->addHeaderCell("<b>" . $objLanguage->languageText('phrase_othernames', 'eportfolio') . "</b>");
$userTable->endRow();
// Step through the list of addresses.
if (!empty($user)) {
    // Display each field for addresses
    $userTable->startRow();
    $userTable->addCell($user['title'], "", NULL, NULL, NULL, '');
    $userTable->addCell($user['surname'], "", NULL, NULL, NULL, '');
    $userTable->addCell($user['firstname'], "", NULL, NULL, NULL, '');
    $userTable->endRow();
} else {
    $userTable->startRow();
    $userTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
    $userTable->endRow();
}
echo $userTable->show();
//end view name
echo '<br></br><br></br>';
//Start Address View
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_address',
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
// Show the heading
$objHeading->str = $objLanguage->languageText("mod_eportfolio_heading", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
$addressList = $this->objDbAddressList->getByItem($userId);
echo "<br/>";
// Create a table object
$addressTable = &$this->newObject("htmltable", "htmlelements");
$addressTable->border = 0;
$addressTable->cellspacing = '3';
$addressTable->width = "100%";
// Add the table heading.
$addressTable->startRow();
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_streetno", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_streetname", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_locality", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_city", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_postcode", 'eportfolio') . "</b>");
$addressTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_postaddress", 'eportfolio') . "</b>");
$addressTable->endRow();
// Step through the list of addresses.
if (!empty($addressList)) {
    foreach($addressList as $addressItem) {
        // Display each field for addresses
        $addressTable->startRow();
        $addressTable->addCell($addressItem['type'], "", NULL, NULL, NULL, '');
        $addressTable->addCell($addressItem['street_no'], "", NULL, NULL, NULL, '');
        $addressTable->addCell($addressItem['street_name'], "", NULL, NULL, NULL, '');
        $addressTable->addCell($addressItem['locality'], "", NULL, NULL, NULL, '');
        $addressTable->addCell($addressItem['city'], "", NULL, NULL, NULL, '');
        $addressTable->addCell($addressItem['postcode'], "", NULL, NULL, NULL, '');
        $addressTable->addCell($addressItem['postal_address'], "", NULL, NULL, NULL, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_eportfolio_edit", 'eportfolio');
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editaddress',
            'id' => $addressItem["id"]
        )));
        $objLink->link = $iconEdit->show();
        $linkEdit = $objLink->show();
        // Show the delete link
        $iconDelete = $this->getObject('geticon', 'htmlelements');
        $iconDelete->setIcon('delete');
        $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
        $iconDelete->align = false;
        $objConfirm = &$this->getObject("link", "htmlelements");
        $objConfirm = &$this->newObject('confirm', 'utilities');
        $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
            'module' => 'eportfolio',
            'action' => 'deleteConfirm',
            'id' => $addressItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $addressTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, NULL, '');
        $addressTable->endRow();
    }
    unset($addressItem);
} else {
    $addressTable->startRow();
    $addressTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
    $addressTable->endRow();
}
echo $addressTable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_address'
)));
$mainlink->link = $objLanguage->languageText("mod_eportfolio_addAddress", 'eportfolio');
echo '<br clear="left" />' . $mainlink->show();
//End Address View
echo "<br></br><br></br>";
//Start Contacts View
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_contact'
)));
$objLink->link = $iconAdd->show();
$linkAdd = $objLink->show();
// Show the heading
$objHeading->str = $objLanguage->languageText("mod_eportfolio_contact", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
echo $objHeading->show();
$contactList = $this->objDbContactList->getByItem($userId);
$emailList = $this->objDbEmailList->getByItem($userId);
echo "<br/>";
// Create a table object
$contactTable = &$this->newObject("htmltable", "htmlelements");
$contactTable->border = 0;
$contactTable->cellspacing = '3';
$contactTable->width = "100%";
// Add the table heading.
$contactTable->startRow();
$contactTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$contactTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contacttypes", 'eportfolio') . "</b>");
$contactTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . "</b>");
$contactTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . "</b>");
$contactTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contactnumber", 'eportfolio') . "</b>");
$contactTable->endRow();
// Step through the list of addresses.
if (!empty($contactList)) {
    foreach($contactList as $contactItem) {
        // Display each field for addresses
        $contactTable->startRow();
        $contactTable->addCell($contactItem['type'], "", NULL, NULL, NULL, '');
        $contactTable->addCell($contactItem['contact_type'], "", NULL, NULL, NULL, '');
        $contactTable->addCell($contactItem['country_code'], "", NULL, NULL, NULL, '');
        $contactTable->addCell($contactItem['area_code'], "", NULL, NULL, NULL, '');
        $contactTable->addCell($contactItem['id_number'], "", NULL, NULL, NULL, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editcontact',
            'id' => $contactItem["id"]
        )));
        $objLink->link = $iconEdit->show();
        $linkEdit = $objLink->show();
        // Show the delete link
        $iconDelete = $this->getObject('geticon', 'htmlelements');
        $iconDelete->setIcon('delete');
        $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
        $iconDelete->align = false;
        $objConfirm = &$this->getObject("link", "htmlelements");
        $objConfirm = &$this->newObject('confirm', 'utilities');
        $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
            'module' => 'eportfolio',
            'action' => 'deletecontact',
            'id' => $contactItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $contactTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, NULL, '');
        $contactTable->endRow();
    }
    unset($contactItem);
} else {
    $contactTable->startRow();
    $contactTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $contactTable->endRow();
}
echo $contactTable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_contact'
)));
$mainlink->link = $objLanguage->languageText("mod_eportfolio_addcontact", 'eportfolio');
echo '<br clear="left" />' . $mainlink->show();
//End Contact View
echo "<br></br><br></br>";
//Start Email View
echo "<br/>";
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$myobjLink = &$this->getObject("link", "htmlelements");
$myobjLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_email'
)));
$myobjLink->link = $iconAdd->show();
$emailLinkAdd = $myobjLink->show();
// Create a heading for emails
$objHeading->str = $objLanguage->languageText("mod_eportfolio_emailList", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $emailLinkAdd;
echo $objHeading->show();
echo "<br/>";
// Create a table object for emails
$emailTable = &$this->newObject("htmltable", "htmlelements");
$emailTable->border = 0;
$emailTable->cellspacing = '3';
$emailTable->width = "50%";
// Add the table heading.
$emailTable->startRow();
$emailTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$emailTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_email", 'eportfolio') . "</b>");
$emailTable->endRow();
// Step through the list of addresses.
$class = 'even';
if (!empty($emailList)) {
    foreach($emailList as $emailItem) {
        // Display each field for addresses
        $emailTable->startRow();
        $emailTable->addCell($emailItem['type'], "", NULL, NULL, NULL, '');
        $emailTable->addCell($emailItem['email'], "", NULL, NULL, NULL, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editemail',
            'id' => $emailItem["id"]
        )));
        $objLink->link = $iconEdit->show();
        $linkEdit = $objLink->show();
        // Show the delete link
        $iconDelete = $this->getObject('geticon', 'htmlelements');
        $iconDelete->setIcon('delete');
        $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
        $iconDelete->align = false;
        $objConfirm = &$this->getObject("link", "htmlelements");
        $objConfirm = &$this->newObject('confirm', 'utilities');
        $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
            'module' => 'eportfolio',
            'action' => 'deleteemail',
            'myid' => $emailItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $emailTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, NULL, '');
        $emailTable->endRow();
    }
    unset($emailItem);
} else {
    $emailTable->startRow();
    $emailTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
    $emailTable->endRow();
}
echo $emailTable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_email'
)));
$mainlink->link = $objLanguage->languageText("mod_eportfolio_addemail", 'eportfolio');
echo '<br clear="left" />' . $mainlink->show();
//End Email View
echo "<br></br><br></br>";
//Demographics view
$demographicsList = $this->objDbDemographicsList->getByItem($userId);
if (empty($demographicsList)) {
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_demographics'
    )));
    $objLink->link = $iconAdd->show();
    $linkAdd = $objLink->show();
    // Show the heading
    $objHeading->str = $objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
    echo $objHeading->show();
} else {
    // Show the heading
    $objHeading->str = $objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio');
    echo $objHeading->show();
}
echo "<br/>";
// Create a table object
$demographicsTable = &$this->newObject("htmltable", "htmlelements");
$demographicsTable->border = 0;
$demographicsTable->cellspacing = '3';
$demographicsTable->width = "50%";
// Add the table heading.
$demographicsTable->startRow();
$demographicsTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$demographicsTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . "</b>");
$demographicsTable->addHeaderCell("<b>" . $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . "</b>");
$demographicsTable->endRow();
// Step through the list of addresses.
if (!empty($demographicsList)) {
    foreach($demographicsList as $demographicsItem) {
        // Display each field for Demographics
        $demographicsTable->startRow();
        $demographicsTable->addCell($demographicsItem['type'], "", NULL, NULL, NULL, '');
        $demographicsTable->addCell($this->objDate->formatDate($demographicsItem['birth']) , "", NULL, NULL, NULL, '');
        $demographicsTable->addCell($demographicsItem['nationality'], "", NULL, NULL, NULL, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editdemographics',
            'id' => $demographicsItem["id"]
        )));
        $objLink->link = $iconEdit->show();
        $linkEdit = $objLink->show();
        // Show the delete link
        $iconDelete = $this->getObject('geticon', 'htmlelements');
        $iconDelete->setIcon('delete');
        $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
        $iconDelete->align = false;
        $objConfirm = &$this->getObject("link", "htmlelements");
        $objConfirm = &$this->newObject('confirm', 'utilities');
        $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
            'module' => 'eportfolio',
            'action' => 'deletedemographics',
            'id' => $demographicsItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $demographicsTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, NULL, '');
        $demographicsTable->endRow();
    }
    unset($demographicsItem);
} else {
    $demographicsTable->startRow();
    $demographicsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $demographicsTable->endRow();
}
echo $demographicsTable->show();
//End Demographics view
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$mainlink->link = 'ePortfolio home';
echo '<br clear="left" />' . $mainlink->show();
?>
