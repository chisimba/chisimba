<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('mouseoverpopup', 'htmlelements');
$this->loadClass('mysqlxml_eportfolio', 'eportfolio');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objPopup = &$this->loadClass('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$tabBox = $this->newObject('tabpane', 'htmlelements');
$this->objEssayView = $this->newObject('manageviews_essay', 'essay');
//Titles
$objinfoTitles = &$this->getObject('htmlheading', 'htmlelements');
$objactivityTitles = &$this->getObject('htmlheading', 'htmlelements');
$objaddressTitles = &$this->getObject('htmlheading', 'htmlelements');
$objcontactTitles = &$this->getObject('htmlheading', 'htmlelements');
$emailobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$demographicsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$objactivityTitles = &$this->getObject('htmlheading', 'htmlelements');
//$objaddressTitles =& $this->getObject('htmlheading','htmlelements');
$objaffiliationTitles = &$this->getObject('htmlheading', 'htmlelements');
$objtranscriptTitles = &$this->getObject('htmlheading', 'htmlelements');
$objqclTitles = &$this->getObject('htmlheading', 'htmlelements');
$objgoalsTitles = &$this->getObject('htmlheading', 'htmlelements');
$objcompetencyTitles = &$this->getObject('htmlheading', 'htmlelements');
$objinterestTitles = &$this->getObject('htmlheading', 'htmlelements');
$objreflectionTitles = &$this->getObject('htmlheading', 'htmlelements');
$objassertionsTitles = &$this->getObject('htmlheading', 'htmlelements');
$objcategoryTitles = &$this->getObject('htmlheading', 'htmlelements');
//Check if user is admin or lecturer
$hasAccess = $this->objUser->isAdmin();
$hasAccess|= $this->objUser->isContextLecturer();
//Get user contexts
$myContexts = $this->objContextUser->getUserContext($this->userId);
$this->viewAssessments = $this->newObject('viewassessments_Eportfolio', 'eportfolio');
$featureBox = &$this->newObject('featurebox', 'navigation');
//Link to print pdf
$iconPdf = $this->getObject('geticon', 'htmlelements');
$iconPdf->setIcon('pdf');
$iconPdf->alt = $objLanguage->languageText("mod_eportfolio_saveaspdf", 'eportfolio');
if (class_exists('groupops', false)) {
    $mngpdflink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'makepdf'
    )));
} else {
    $mngpdflink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'makepdf'
    )));
}
$mngpdflink->link = $iconPdf->show();
$linkpdfManage = $mngpdflink->show();
//Link to view eportfolio
$iconView = $this->getObject('geticon', 'htmlelements');
$iconView->setIcon('bookopen');
$iconView->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
$mngviewlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'myview'
)));
$mngviewlink->link = $iconView->show();
$linkviewManage = $mngviewlink->show();
//Link to export eportfolio
$iconExport = $this->getObject('geticon', 'htmlelements');
$iconExport->setIcon('exportcvs');
$iconExport->alt = $objLanguage->languageText("mod_eportfolio_export", 'eportfolio');
$mngExportlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'export'
)));
$mngExportlink->link = $iconExport->show();
$linkExportManage = $mngExportlink->show();
//Link to import eportfolio
$iconImport = $this->getObject('geticon', 'htmlelements');
$iconImport->setIcon('importcvs');
$iconImport->alt = $objLanguage->languageText("mod_eportfolio_import", 'eportfolio');
$mngImportlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'import'
)));
$mngImportlink->link = $iconImport->show();
$linkImportManage = $mngImportlink->show();
//echo '<div align="center">'.$linkpdfManage.'</div>';
//echo "</br>";
$objHeading->type = 2;
$objHeading->align = 'center';
$objHeading->str = $objUser->fullName() . ' ' . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
echo $objHeading->show();
//Create Group 1 and Group 2 for user eportfolio
if (class_exists('groupops', false)) {
    $eportfoliogrpList = $this->_objGroupAdmin->getId($name = $this->objUser->PKId($this->objUser->userId()));
    if (empty($eportfoliogrpList)) {
        //Add User to context groups
        $title = $this->objUser->PKId($this->objUser->userId()) . ' ' . $objUser->getSurname();
        $this->createGroups($this->objUser->PKId($this->objUser->userId()) , $title);
    }
} else {
    $eportfoliogrpList = $this->_objGroupAdmin->getId($this->objUser->PKId($this->objUser->userId()) , $pkField = 'name');
    if (empty($eportfoliogrpList)) {
        //Add User to context groups
        $title = $this->objUser->PKId($this->objUser->userId()) . ' ' . $objUser->getSurname();
        $this->createGroupsOld($this->objUser->PKId($this->objUser->userId()) , $title);
    }
}
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
// Create a table object
$userTable = &$this->newObject("htmltable", "htmlelements");
$userTable->border = 0;
$userTable->cellspacing = '12';
$userTable->width = "40%";
// Add the table heading.
$userTable->addCell("<b>" . $objLanguage->languageText('word_title', 'system') . "</b>");
$userTable->addCell("<b>" . $objLanguage->languageText('word_surname', 'system') . "</b>");
$userTable->addCell("<b>" . $objLanguage->languageText('phrase_othernames', 'eportfolio') . "</b>");
$userTable->addCell("", "", NULL, NULL, NULL, '');
$userTable->endRow();
// Step through the list of addresses.
if (!empty($user)) {
    // Display each field for addresses
    $userTable->startRow();
    $userTable->addCell($user['title'], "", NULL, NULL, NULL, '');
    $userTable->addCell($user['surname'], "", NULL, NULL, NULL, '');
    $userTable->addCell($user['firstname'], "", NULL, NULL, NULL, '');
    $userTable->addCell($objLink->show() , "", NULL, NULL, NULL, '');
    $userTable->endRow();
    $userTable->startRow();
    $userTable->addCell('', '', '', '', '', 'colspan="3"');
    $userTable->endRow();
} else {
    $userTable->startRow();
    $userTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
    $userTable->endRow();
}
//	echo $userTable->show();
//end view name
//Start Address View
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
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
$linkaddressAdd = $objLink->show();
// Show the heading
$objaddressTitles->type = 3;
$objaddressTitles->str = $objLanguage->languageText("mod_eportfolio_heading", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkaddressAdd;
//echo $objHeading->show();
$addressList = $this->objDbAddressList->getByItem($userId);
// Create a table object
$addressTable = &$this->newObject("htmltable", "htmlelements");
$addressTable->border = 0;
$addressTable->cellspacing = '12';
$addressTable->width = "100%";
// Add the table heading.
$addressTable->startRow();
$addressTable->addCell($objLink->show() , '', '', 'left', '', 'colspan="8"');
$addressTable->endRow();
$addressTable->startRow();
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_streetno", 'eportfolio') . "</b>");
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_streetname", 'eportfolio') . "</b>");
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_locality", 'eportfolio') . "</b>");
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_city", 'eportfolio') . "</b>");
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_postcode", 'eportfolio') . "</b>");
$addressTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_postaddress", 'eportfolio') . "</b>");
$addressTable->endRow();
// Step through the list of addresses.
if (!empty($addressList)) {
    foreach($addressList as $addressItem) {
        // Display each field for addresses
        $addressTable->startRow();
        $cattype = $this->objDbCategorytypeList->listSingle($addressItem['type']);
        //$addressTable->startRow();
        $addressTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
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
//echo $addressTable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_address'
)));
$mainlink->link = $objLanguage->languageText("mod_eportfolio_addAddress", 'eportfolio');
$addressTable->startRow();
$addressTable->addCell($mainlink->show() , '', '', '', '', '', 'colspan="8"');
$addressTable->endRow();
$addressTable->startRow();
$addressTable->addCell('', '', '', '', '', 'noRecordsMessage', 'colspan="8"');
$addressTable->endRow();
//echo '<br clear="left" />'.$mainlink->show();
//End Address View
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
//echo $objHeading->show();
$contactList = $this->objDbContactList->getByItem($userId);
// Create a table object
$contactTable = &$this->newObject("htmltable", "htmlelements");
$contactTable->border = 0;
$contactTable->cellspacing = '3';
$contactTable->width = "100%";
// Add the table heading.
$contactTable->startRow();
$contactTable->addCell($objLink->show() , '', '', '', '', 'colspan="6"');
$contactTable->endRow();
$contactTable->startRow();
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contacttype", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contactnumber", 'eportfolio') . "</b>");
$contactTable->endRow();
// Step through the list of contacts
if (!empty($contactList)) {
    foreach($contactList as $contactItem) {
        // Display each field for contacts
        $cattype = $this->objDbCategorytypeList->listSingle($contactItem['type']);
        $modetype = $this->objDbCategorytypeList->listSingle($contactItem['contact_type']);
        $contactTable->startRow();
        $contactTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
        $contactTable->addCell($modetype[0]['type'], "", NULL, NULL, NULL, '');
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
//echo $contactTable->show();
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_contact'
)));
$mainlink->link = $objLanguage->languageText("mod_eportfolio_addcontact", 'eportfolio');
$contactTable->startRow();
$contactTable->addCell($mainlink->show() , '', '', '', '', 'colspan="6"');
$contactTable->endRow();
$contactTable->startRow();
$contactTable->addCell('', '', '', '', 'noRecordsMessage', 'colspan="6"');
$contactTable->endRow();
//End Contact View
//Start Email View
$emailList = $this->objDbEmailList->getByItem($userId);
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$myobjLink = &$this->getObject("link", "htmlelements");
$myobjLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_email'
)));
$myobjLink->link = $iconAdd->show();
// Create a table object for emails
$emailTable = &$this->newObject("htmltable", "htmlelements");
$emailTable->border = 0;
$emailTable->cellspacing = '3';
$emailTable->width = "50%";
// Add the table heading.
$emailTable->startRow();
$emailTable->addCell($myobjLink->show() , '', '', '', '', 'colspan="3"');
$emailTable->endRow();
$emailTable->startRow();
$emailTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$emailTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_email", 'eportfolio') . "</b>");
$emailTable->endRow();
// Step through the list of addresses.
$class = 'even';
if (!empty($emailList)) {
    foreach($emailList as $emailItem) {
        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($emailItem['type']);
        $emailTable->startRow();
        $emailTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
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
$mainlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_email'
)));
$mainlink->link = $objLanguage->languageText("mod_eportfolio_addemail", 'eportfolio');
$emailTable->startRow();
$emailTable->addCell($mainlink->show() , '', '', '', '', 'colspan="3"');
$emailTable->endRow();
$emailTable->startRow();
$emailTable->addCell('', '', '', '', 'noRecordsMessage', 'colspan="3"');
$emailTable->endRow();
//End Email View
//Demographics view
$demographicsList = $this->objDbDemographicsList->getByItem($userId);
// Create a table object
$demographicsTable = &$this->newObject("htmltable", "htmlelements");
$demographicsTable->border = 0;
$demographicsTable->cellspacing = '3';
$demographicsTable->width = "50%";
// Add the table heading.
//if (empty($demographicsList)) {
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
$demographicsTable->startRow();
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_add", 'eportfolio') . '&nbsp;' . $objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio') . '&nbsp;&nbsp;&nbsp;</b>' . $objLink->show() , '', 'left', '', '', 'colspan="4"');
$demographicsTable->endRow();
//echo $objHeading->show();
//}
$demographicsTable->startRow();
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . "</b>");
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . "</b>");
$demographicsTable->endRow();
// Step through the list of addresses.
if (!empty($demographicsList)) {
    foreach($demographicsList as $demographicsItem) {        
        // Display each field for Demographics
        $cattype = $this->objDbCategorytypeList->listSingle($demographicsItem['type']);
        if(!empty($cattype)) {
            $cat_type = $cattype[0]['type'];
        } else {
            $cat_type = "";
        }
        $datetime = explode("-", $this->objDate->formatDateOnly($demographicsItem['birth']));
        $demographicsTable->startRow();
        $demographicsTable->addCell($cat_type, "", NULL, NULL, NULL, '');
        $demographicsTable->addCell($datetime[0], "", NULL, NULL, NULL, '');
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
//echo $demographicsTable->show();
//End Demographics view
//View Activity
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_activity'
)));
$objLink->link = $iconAdd->show();
$activitylist = $this->objDbActivityList->getByItem($userId);
// Create a table object
$activityTable = &$this->newObject("htmltable", "htmlelements");
$activityTable->border = 0;
$activityTable->cellspacing = '3';
$activityTable->width = "100%";
// Add the table heading.
$activityTable->startRow();
$activityTable->addCell($objLink->show() , '', '', '', '', 'colspan="7"');
$activityTable->endRow();
$activityTable->startRow();
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$activityTable->addCell("&nbsp;");
$activityTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($activitylist)) {
    $i = 0;
    foreach($activitylist as $item) {
        //Get context title
        $objDbContext = &$this->getObject('dbcontext', 'context');
        $mycontextRecord = $objDbContext->getContextDetails($item['contextid']);
        if (!empty($mycontextRecord)) {
            $mycontextTitle = $mycontextRecord['title'];
        } else {
            $mycontextTitle = $item['contextid'];
        }
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singleactivity',
          'atyId' => $item['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page

        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $activityTable->startRow();
        $activityTable->addCell($mycontextTitle, "", NULL, NULL, $class, '');
        $activityTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $activityTable->addCell($this->objDate->formatDateOnly($item['start']) , "", NULL, NULL, $class, '');
        $activityTable->addCell($this->objDate->formatDateOnly($item['finish']) , "", NULL, NULL, $class, '');
        $activityTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
        $activityTable->addCell($objPopup->show(), "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editactivity',
            'id' => $item["id"]
        )));
        //if( $this->isValid( 'edit' ))
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
            'action' => 'deleteactivity',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $activityTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $activityTable->endRow();
    }
    unset($item);
} else {
    $activityTable->startRow();
    $activityTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $activityTable->endRow();
}
//    	echo $activityTable->show();
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_activity'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addactivity", 'eportfolio');
$activityTable->startRow();
$activityTable->addCell($addlink->show() , '', '', '', '', 'colspan="6"');
$activityTable->endRow();
//End View Activity
//View Affiliation
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_affiliation',
)));
$objLink->link = $iconAdd->show();
$affiliationList = $this->objDbAffiliationList->getByItem($userId);
// Create a table object
$affiliationTable = &$this->newObject("htmltable", "htmlelements");
$affiliationTable->border = 0;
$affiliationTable->cellspacing = '12';
$affiliationTable->width = "100%";
// Add the table heading.
$affiliationTable->startRow();
$affiliationTable->addCell($objLink->show() , '', '', '', '', 'colspan="8"');
$affiliationTable->endRow();
$affiliationTable->startRow();
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$affiliationTable->addCell("&nbsp;");
$affiliationTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($affiliationList)) {
    $i = 0;
    foreach($affiliationList as $affiliationItem) {
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singleaffiliation',
          'affiId' => $affiliationItem['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page

        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($affiliationItem['type']);
        $affiliationTable->startRow();
        $affiliationTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($affiliationItem['classification'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($affiliationItem['role'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($affiliationItem['organisation'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($this->objDate->formatDateOnly($affiliationItem['start']) , "", NULL, NULL, $class, '');
        $affiliationTable->addCell($this->objDate->formatDateOnly($affiliationItem['finish']) , "", NULL, NULL, $class, '');
        $affiliationTable->addCell($objPopup->show() , "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editaffiliation',
            'id' => $affiliationItem["id"]
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
            'action' => 'deleteaffiliation',
            'id' => $affiliationItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $affiliationTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $affiliationTable->endRow();
    }
    unset($affiliationItem);
} else {
    $affiliationTable->startRow();
    $affiliationTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
    $affiliationTable->endRow();
}
//echo $affiliationTable->show();
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_affiliation'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addAffiliation", 'eportfolio');
$affiliationTable->startRow();
$affiliationTable->addCell($addlink->show() , '', '', '', '', 'colspan="8"');
$affiliationTable->endRow();
//View Affiliation
//View Transcript
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_transcript'
)));
$objLink->link = $iconAdd->show();
$transcriptlist = $this->objDbTranscriptList->getByItem($userId);
// Create a table object
$transcriptTable = &$this->newObject("htmltable", "htmlelements");
$transcriptTable->border = 0;
$transcriptTable->cellspacing = '12';
$transcriptTable->width = "100%";
$class = NULL;
$hasEssays = 0;
$hasAssignments = 0;
foreach($myContexts as $contextCode) {
    $contextLecturers = $this->objContextUser->getContextLecturers($contextCode);
    $isaLecturer = False;
    foreach($contextLecturers as $isLecturer) {
        if ($this->userPid == $isLecturer['id']) $isaLecturer = True;
    }
    if (!$isaLecturer) {
        //Get student essays for this course
        $contextEssay = $this->objEssayView->getStudentEssays($contextCode);
        if (!empty($contextEssay)) {
            $hasEssays = 1;
            $viewEssays = $this->viewAssessments->viewEssays($contextEssay);
            $list = $this->objLanguage->languageText('word_list');
            $head = $this->_objDBContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('mod_essay_essay', 'essay');
            //echo "<b>".$head."</b>".$viewEssays;
            $transcriptTable->startRow();
            $transcriptTable->addCell("<h3>" . $head . "</h3>" . $viewEssays, '', '', '', '', 'colspan="6"');
            $transcriptTable->endRow();
        }
        //Get student essays for this course
        $contextAssignments = $this->objAssignmentFunctions->displayAssignment($contextCode);
        if (!empty($contextAssignments)) {
            $hasAssignments = 1;
            //$viewEssays = $this->viewAssessments->viewEssays($contextEssay);
            $list = $this->objLanguage->languageText('word_list');
            $head = $this->_objDBContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('mod_assignment_assignments', 'assignment');
            //echo "<b>".$head."</b>".$viewEssays;
            $transcriptTable->startRow();
            $transcriptTable->addCell("<h3>" . $head . "</h3>" . $contextAssignments, '', '', '', '', 'colspan="6"');
            $transcriptTable->endRow();
        }
        $contextWorksheets = $this->objWorksheetFunctions->displayWorksheets($contextCode, $userId);
        if (!empty($contextWorksheets)) {
            $transcriptTable->startRow();
            $transcriptTable->addCell($contextWorksheets, '', '', '', '', 'colspan="6"');
            $transcriptTable->endRow();
        }
        //Get mcqtests
        $objmcq = $this->objMcqtestsFunctions->displaymcq($contextCode, $userId, $uriAction = 'showtest', $uriModule = 'eportfolio');
        if (!empty($objmcq)) {
            $mcqHead = $this->_objDBContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('mod_mcqtests_mcq', 'mcqtests');
            $transcriptTable->startRow();
            $transcriptTable->addCell("<h3>" . $mcqHead . "</h3>" . $objmcq, '', '', '', '', 'colspan="6"');
            $transcriptTable->endRow();
        }
        //Get Rubrics
        $studRubrics = $this->objRubricFunctions->displayrubric($contextCode, $userId, $uriModule = 'eportfolio', $assessmentAction = 'rubricsassessments', $viewTableAction = 'rubricviewtable');
        if (!empty($studRubrics)) {
            $rubricHead = $this->_objDBContext->getTitle($contextCode) . " : " . $this->objLanguage->languageText('rubric_rubrics', 'rubric');
            $transcriptTable->startRow();
            $transcriptTable->addCell("<h3>" . $rubricHead . "</h3>" . $studRubrics, '', '', '', '', 'colspan="6"');
            $transcriptTable->endRow();
        }
    }
}
// Add the table heading.
$transcriptTable->startRow();
$transcriptTable->addCell($objLink->show() , '', '', '', '', 'colspan="2"');
$transcriptTable->endRow();
$transcriptTable->startRow();
$transcriptTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$transcriptTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$transcriptTable->endRow();
// Step through the list of addresses.
if (!empty($transcriptlist)) {
    foreach($transcriptlist as $item) {
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singletranscript',
          'transId' => $item['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page

        // Display each field for activities
        $transcriptTable->startRow();
        $transcriptTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
        $transcriptTable->addCell($objPopup->show(), "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'edittranscript',
            'id' => $item["id"]
        )));
        //if( $this->isValid( 'edit' ))
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
            'action' => 'deletetranscript',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $transcriptTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $transcriptTable->endRow();
    }
    unset($item);
} else {
    $transcriptTable->startRow();
    $transcriptTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
    $transcriptTable->endRow();
}
//	echo $transcriptTable->show();
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_transcript'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addtranscript", 'eportfolio');
$transcriptTable->startRow();
$transcriptTable->addCell($addlink->show() , '', '', '', '', 'colspan="2"');
$transcriptTable->endRow();
//View Transcript
//View Qcl
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$linkAdd = '';
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_qcl',
)));
$objLink->link = $iconAdd->show();
$qclList = $this->objDbQclList->getByItem($userId);
// Create a table object
$qclTable = &$this->newObject("htmltable", "htmlelements");
$qclTable->border = 0;
$qclTable->cellspacing = '3';
$qclTable->width = "100%";
// Add the table heading.
$qclTable->startRow();
$qclTable->addCell($objLink->show() , '', '', '', '', 'colspan="6"');
$qclTable->endRow();
$qclTable->startRow();
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordtitle", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$qclTable->addCell("&nbsp;");
$qclTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($qclList)) {
    foreach($qclList as $qclItem) {
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singlequali',
          'qualiId' => $qclItem['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page

        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($qclItem['qcl_type']);
        $qclTable->startRow();
        $qclTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['qcl_title'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['organisation'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['qcl_level'], "", NULL, NULL, $class, '');
        $qclTable->addCell($this->objDate->formatDateOnly($qclItem['award_date']) , "", NULL, NULL, $class, '');
        //$qclTable->addCell($qclItem['shortdescription'], "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editqcl',
            'id' => $qclItem["id"]
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
            'action' => 'deleteqcl',
            'id' => $qclItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $qclTable->addCell($objPopup->show() , "", NULL, NULL, $class, '');
        $qclTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $qclTable->endRow();
    }
    unset($qclItem);
} else {
    $qclTable->startRow();
    $qclTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $qclTable->endRow();
}
$addlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_qcl'
)));
$addlink->link = $objLanguage->languageText("mod_eportfolio_addQualification", 'eportfolio');
$qclTable->startRow();
$qclTable->addCell($addlink->show() , '', '', '', '', 'colspan="6"');
$qclTable->endRow();
//	echo $qclTable->show();
//End View Qcl
//View Goals
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_goals'
)));
$objLink->link = $iconAdd->show();
$goalsList = $this->objDbGoalsList->getByItem($userId);
// Create a table object
$goalsTable = &$this->newObject("htmltable", "htmlelements");
$goalsTable->border = 0;
$goalsTable->cellspacing = '12';
$goalsTable->width = "60%";
// Add the table heading.
$goalsTable->startRow();
$goalsTable->addCell($objLink->show());
$goalsTable->addCell($objLanguage->languageText("mod_eportfolio_display", 'eportfolio'));
$goalsTable->addCell("&nbsp;");
$goalsTable->endRow();
// Step through the list of addresses.
$class = NULL;
//List the course outcomes for each course a user is affiliated to
if (!empty($myContexts)) {
    foreach($myContexts as $contextCode) {
        $contextDetails = $this->_objDBContext->getContextDetails($contextCode);
        if (!empty($contextDetails["goals"])) {
            $contextTitle = "<b>" . $contextDetails["contextcode"] . " - " . ucwords(strtolower($contextDetails["title"])) . " " . $this->objLanguage->languageText('mod_contextadmin_courseoutcomes', 'contextadmin', NULL, 'Outcomes') . "</b><br>";
            $contextOutcomes = $contextDetails["goals"];
            $goalsTable->startRow();
            $goalsTable->addCell($contextTitle, '', '', '', $class, 'colspan="2"');
            $goalsTable->endRow();
            $goalsTable->startRow();
            $goalsTable->addCell($contextOutcomes, '', '', '', $class, 'colspan="2"');
            $goalsTable->endRow();
        }
    }
}
if (!empty($goalsList)) {
    $i = 0;
    echo "<ul>";
    foreach($goalsList as $item) {
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singlegoal',
          'goalId' => $item['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page

        // Display each field for activities
        $goalsTable->startRow();
        $goalsTable->addCell("<li>" . $item['shortdescription'] . "</li>", "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_eportfolio_edit", 'eportfolio');
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editgoals',
            'id' => $item["id"]
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
            'action' => 'deletegoals',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        $goalsTable->addCell($objPopup->show() , "", NULL, NULL, $class, '');
        $goalsTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $goalsTable->endRow();
    }
    unset($item);
    echo "</ul>";
}
if (empty($myContexts) && empty($goalsList)) {
    $goalsTable->startRow();
    $goalsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
    $goalsTable->endRow();
}
$goaladdlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_goals'
)));
$goaladdlink->link = $objLanguage->languageText("mod_eportfolio_addGoal", 'eportfolio');
$goalsTable->startRow();
$goalsTable->addCell($goaladdlink->show() , '', '', '', '', 'colspan="2"');
$goalsTable->endRow();
//	echo $goalsTable->show();
//End View Goals
//View Competency
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_competency'
)));
$objLink->link = $iconAdd->show();
$competencyList = $this->objDbCompetencyList->getByItem($userId);
// Create a table object
$competencyTable = &$this->newObject("htmltable", "htmlelements");
$competencyTable->border = 0;
$competencyTable->cellspacing = '12';
$competencyTable->width = "100%";
// Add the table heading.
$competencyTable->startRow();
$competencyTable->addCell($objLink->show() , '', '', '', '', 'colspan="4"');
$competencyTable->endRow();
$competencyTable->startRow();
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$competencyTable->addCell("&nbsp;");
$competencyTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($competencyList)) {
    foreach($competencyList as $item) {
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singlecompetency',
          'competencyId' => $item['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page
        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $competencyTable->startRow();
        $competencyTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $competencyTable->addCell($this->objDate->formatDateOnly($item['award_date']) , "", NULL, NULL, $class, '');
        $competencyTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
        $competencyTable->addCell($objPopup->show(), "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editcompetency',
            'id' => $item["id"]
        )));
        //if( $this->isValid( 'edit' ))
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
            'action' => 'deletecompetency',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $competencyTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $competencyTable->endRow();
    }
    unset($item);
} else {
    $competencyTable->startRow();
    $competencyTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $competencyTable->endRow();
}
$competencyaddLink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_competency'
)));
$competencyaddLink->link = $objLanguage->languageText("mod_eportfolio_addCompetency", 'eportfolio');
$competencyTable->startRow();
$competencyTable->addCell($competencyaddLink->show() , '', '', '', '', 'colspan="4"');
$competencyTable->endRow();
//End View Competency
//View Interest
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_interest'
)));
$objLink->link = $iconAdd->show();
$interestList = $this->objDbInterestList->getByItem($userId);
// Create a table object
$interestTable = &$this->newObject("htmltable", "htmlelements");
$interestTable->border = 0;
$interestTable->cellspacing = '12';
$interestTable->width = "100%";
// Add the table heading.
$interestTable->startRow();
$interestTable->addCell($objLink->show() , '', '', '', '', 'colspan="4"');
$interestTable->endRow();
$interestTable->startRow();
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$interestTable->addCell("&nbsp;");
$interestTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($interestList)) {
    foreach($interestList as $item) {
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
          'action' => 'singleinterest',
          'interestId' => $item['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page

        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $interestTable->startRow();
        $interestTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $interestTable->addCell($this->objDate->formatDateOnly($item['creation_date']) , "", NULL, NULL, $class, '');
        $interestTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
        $interestTable->addCell($objPopup->show(), "", NULL, NULL, $class, '');
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editinterest',
            'id' => $item["id"]
        )));
        //if( $this->isValid( 'edit' ))
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
            'action' => 'deleteinterest',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $interestTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $interestTable->endRow();
    }
    unset($item);
} else {
    $interestTable->startRow();
    $interestTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $interestTable->endRow();
}
$interestaddlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_interest'
)));
$interestaddlink->link = $objLanguage->languageText("mod_eportfolio_addInterest", 'eportfolio');
$interestTable->startRow();
$interestTable->addCell($interestaddlink->show() , '', '', '', '', 'colspan="4"');
$interestTable->endRow();
//End View Interest
//View reflection
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the add link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
$iconAdd->align = false;
$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_reflection'
)));
$objLink->link = $iconAdd->show();
// Show the heading
$reflectionList = $this->objDbReflectionList->getByItem($userId);
// Create a table object
$reflectionTable = &$this->newObject("htmltable", "htmlelements");
$reflectionTable->border = 0;
$reflectionTable->cellspacing = '3';
$reflectionTable->width = "100%";
// Add the table heading.
$reflectionTable->startRow();
$reflectionTable->addCell($objLink->show() , '', '', '', '', 'colspan="4"');
$reflectionTable->endRow();
$reflectionTable->startRow();
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$reflectionTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($reflectionList)) {
    foreach($reflectionList as $item) {
        // Display each field for activities
        $reflectionTable->startRow();
        $reflectionTable->addCell($item['rationale'], "", NULL, NULL, $class, '');
        $reflectionTable->addCell($this->objDate->formatDateOnly($item['creation_date']) , "", NULL, NULL, $class, '');
        $reflectionTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
        //Show the view Icon
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
        $this->objIcon->setIcon('comment_view');
        $commentIcon = $this->objIcon->show();
        $objPopup = new windowpop();
        $objPopup->set('location', $this->uri(array(
            'action' => 'singlereflection',
            'reflectId' => $item['id']
        ) , 'eportfolio'));
        $objPopup->set('linktext', $commentIcon);
        $objPopup->set('width', '600');
        $objPopup->set('height', '350');
        $objPopup->set('left', '200');
        $objPopup->set('top', '200');
        $objPopup->set('scrollbars', 'yes');
        $objPopup->set('resizable', 'yes');
        $objPopup->putJs(); // you only need to do this once per page
        //echo $objPopup->show();
        // Show the edit link
        $iconEdit = $this->getObject('geticon', 'htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("word_edit");
        $iconEdit->align = false;
        $objLink = &$this->getObject("link", "htmlelements");
        $objLink->link($this->uri(array(
            'module' => 'eportfolio',
            'action' => 'editreflection',
            'id' => $item["id"]
        )));
        //if( $this->isValid( 'edit' ))
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
            'action' => 'deletereflection',
            'id' => $item["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
        $reflectionTable->addCell($objPopup->show() , "", NULL, NULL, $class, '');
        $reflectionTable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
        $reflectionTable->endRow();
    }
    unset($item);
} else {
    $reflectionTable->startRow();
    $reflectionTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $reflectionTable->endRow();
}
$reflectionaddlink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'add_reflection'
)));
$reflectionaddlink->link = $objLanguage->languageText("mod_eportfolio_addReflection", 'eportfolio');
$reflectionTable->startRow();
$reflectionTable->addCell($reflectionaddlink->show() , '', '', '', '', 'colspan="4"');
$reflectionTable->endRow();
// echo $reflectionTable->show();
//End View Reflection
//View assertions
//$this->setVar('pageSuppressXML',true);
if (!$hasAccess) {
    //Language Items
    $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
    $linkAdd = '';
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_assertion'
    )));
    $objLink->link = $iconAdd->show();
    // Create a table object
    $assertionstable = &$this->newObject("htmltable", "htmlelements");
    $assertionstable->border = 0;
    $assertionstable->cellspacing = '3';
    $assertionstable->width = "100%";
    // Add the table heading.
    $assertionstable->startRow();
    $assertionstable->addCell($objLink->show() , '', '', '', '', 'colspan="5"');
    $assertionstable->endRow();
    $assertionstable->startRow();
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_lecturer", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
    $assertionstable->endRow();
    if (class_exists('groupops', false)) {
        // Step through the list of addresses.
        $class = NULL;
        //    if (!empty($Id))
        if (!empty($myGroups)) {
            foreach($myGroups as $groupId) {
                //Get the group parent_id
                foreach(array_keys($groupId) as $myGrpId) $groupId = $myGrpId;
                //            $parentId = $this->_objGroupAdmin->getParent($groupId);
                $myownerId = $this->_objGroupAdmin->getGroupUsers($groupId, $fields = null, $filter = null);
                //var_dump($myownerId[0]['perm_user_id']);
                //            foreach($parentId as $myparentId) {
                //Get the name from group table
                $assertionId = $this->_objGroupAdmin->getName($myparentId['parent_id']);
                $assertionslist = $this->objDbAssertionList->listSingle($assertionId);
                if (!empty($assertionslist)) {
                    // Display each field for activities
                    $assertionstable->startRow();
                    $assertionstable->addCell($objUser->fullName($assertionslist[0]['userid']) , "", NULL, NULL, $class, '');
                    $assertionstable->addCell($assertionslist[0]['rationale'], "", NULL, NULL, $class, '');
                    $assertionstable->addCell($this->objDate->formatDateOnly($assertionslist[0]['creation_date']) , "", NULL, NULL, $class, '');
                    $assertionstable->addCell($assertionslist[0]['shortdescription'], "", NULL, NULL, $class, '');
                    // Show the view link
                    //Display Icon
                    $atyiconView = $this->getObject('geticon', 'htmlelements');
                    $atyiconView->setIcon('bookopen');
                    $atyiconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
                    $atymnglink = new link($this->uri(array(
                        'module' => 'eportfolio',
                        'action' => 'displayassertion',
                        'thisid' => $assertionslist[0]["id"]
                    )));
                    $atymnglink->link = $atyiconView->show();
                    $atylinkManage = $atymnglink->show();
                    $assertionstable->addCell($atylinkManage, "", NULL, NULL, $class, '');
                    $assertionstable->endRow();
                } else {
                    $assertionstable->startRow();
                    $assertionstable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
                    $assertionstable->endRow();
                }
                unset($myparentId);
                //}
                unset($groupId);
            }
        } else {
            $assertionstable->startRow();
            $assertionstable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $assertionstable->endRow();
        }
        //Else if groupops does not exist
        
    } else {
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $Id = $this->_objGroupAdmin->getUserGroups($userPid);
        // Step through the list of assertions.
        $class = NULL;
        if (!empty($Id)) {
            foreach($Id as $groupId) {
                //Get the group parent_id
                $parentId = $this->_objGroupAdmin->getParent($groupId);
                foreach($parentId as $myparentId) {
                    //Get the name from group table
                    $assertionId = $this->_objGroupAdmin->getName($myparentId['parent_id']);
                    $assertionslist = $this->objDbAssertionList->listSingle($assertionId);
                    if (!empty($assertionslist)) {
                        // Display each field
                        $assertionstable->startRow();
                        $assertionstable->addCell($objUser->fullName($assertionslist[0]['userid']) , "", NULL, NULL, $class, '');
                        $assertionstable->addCell($assertionslist[0]['rationale'], "", NULL, NULL, $class, '');
                        $assertionstable->addCell($this->objDate->formatDateOnly($assertionslist[0]['creation_date']) , "", NULL, NULL, $class, '');
                        $assertionstable->addCell($assertionslist[0]['shortdescription'], "", NULL, NULL, $class, '');
                        // Show the view link
                        //Display Icon
                        $atyiconView = $this->getObject('geticon', 'htmlelements');
                        $atyiconView->setIcon('bookopen');
                        $atyiconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
                        $atymnglink = new link($this->uri(array(
                            'module' => 'eportfolio',
                            'action' => 'displayassertion',
                            'thisid' => $assertionslist[0]["id"]
                        )));
                        $atymnglink->link = $atyiconView->show();
                        $atylinkManage = $atymnglink->show();
                        $assertionstable->addCell($atylinkManage, "", NULL, NULL, $class, '');
                        $assertionstable->endRow();
                    }
                    unset($myparentId);
                }
                unset($groupId);
            }
        } else {
            $assertionstable->startRow();
            $assertionstable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $assertionstable->endRow();
        }
    }
} else {
    //Language Items
    $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_assertion'
    )));
    $objLink->link = $iconAdd->show();
    $assertionslist = $this->objDbAssertionList->getByItem($userId);
    // Create a table object
    $assertionstable = &$this->newObject("htmltable", "htmlelements");
    $assertionstable->border = 0;
    $assertionstable->cellspacing = '3';
    $assertionstable->width = "100%";
    // Add the table heading.
    $assertionstable->startRow();
    $assertionstable->addCell($objLink->show() , '', '', '', '', 'colspan="5"');
    $assertionstable->endRow();
    $assertionstable->startRow();
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordManage", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
    $assertionstable->addCell("&nbsp;");
    $assertionstable->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($assertionslist)) {
        foreach($assertionslist as $item) {
            // Display each field for activities
            $assertionstable->startRow();
            $assertionstable->addCell($item['rationale'], "", NULL, NULL, $class, '');
            $assertionstable->addCell($this->objDate->formatDateOnly($item['creation_date']) , "", NULL, NULL, $class, '');
            $assertionstable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            // Show the edit link
            $iconEdit = $this->getObject('geticon', 'htmlelements');
            $iconEdit->setIcon('edit');
            $iconEdit->alt = $objLanguage->languageText("word_edit");
            $iconEdit->align = false;
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'editassertion',
                'id' => $item["id"]
            )));
            //if( $this->isValid( 'edit' ))
            $objLink->link = $iconEdit->show();
            $linkEdit = $objLink->show();
            //Manage Students
            $managestudlink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'manage_stud',
                'id' => $item["id"]
            )));
            $managestudlink->link = 'Students';
            $linkstudManage = $managestudlink->show();
            //Manage Lecturers
            $this->group = 'lect';
            $this->setVarByRef('group', $this->group);
            $lect = 'lect';
            $manageleclink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'manage_' . $lect,
                'id' => $item["id"]
            )));
            $manageleclink->link = 'Lecturers';
            $linklecManage = $manageleclink->show();
            // Show the delete link
            $iconDelete = $this->getObject('geticon', 'htmlelements');
            $iconDelete->setIcon('delete');
            $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
            $iconDelete->align = false;
            $objConfirm = &$this->getObject("link", "htmlelements");
            $objConfirm = &$this->newObject('confirm', 'utilities');
            $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
                'module' => 'eportfolio',
                'action' => 'deleteassertion',
                'id' => $item["id"]
            )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
            //Show the view Icon
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objIcon->title = $this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
            $this->objIcon->setIcon('comment_view');
            $commentIcon = $this->objIcon->show();
            $objPopup = new windowpop();
            $objPopup->set('location', $this->uri(array(
             'action' => 'singleassertion',
             'assertionId' => $item['id']
            ) , 'eportfolio'));
            $objPopup->set('linktext', $commentIcon);
            $objPopup->set('width', '600');
            $objPopup->set('height', '350');
            $objPopup->set('left', '200');
            $objPopup->set('top', '200');
            $objPopup->set('scrollbars', 'yes');
            $objPopup->set('resizable', 'yes');
            $objPopup->putJs(); // you only need to do this once per page

            //echo $objConfirm->show();
            $assertionstable->addCell($linkstudManage . "<br> " . $linklecManage, "", NULL, NULL, $class, '');
            $assertionstable->addCell($objPopup->show() , "", NULL, NULL, $class, '');
            $assertionstable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
            $assertionstable->endRow();

            //Check if assertion group exists and add in contextgroups
            $contextCode = $item["id"];
            $groupName = $item['rationale'];
            if (empty($groupName)) {
                $groupName = Null;
                $contextgrpList = Null;
            } else {
                $contextgrpList = $this->_objGroupAdmin->getLeafId(array(
                    $contextCode,
                    $groupName
                ));
            }
            if (empty($contextgrpList)) {
                //Add Assertion to context groups
                $title = $item['rationale'];
                $contextGroups = $this->getObject('manageGroups', 'contextgroups');
                $contextGroups->createGroups($contextCode, $title);
            }
        }
        unset($item);
    } else {
        $assertionstable->startRow();
        $assertionstable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
        $assertionstable->endRow();
    }
    //echo $assertionstable->show();
    $assertionsaddlink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_assertion'
    )));
    $assertionsaddlink->link = $objLanguage->languageText("mod_eportfolio_addAssertion", 'eportfolio');
    $assertionstable->startRow();
    $assertionstable->addCell($assertionsaddlink->show() , '', '', '', '', 'colspan="5"');
    $assertionstable->endRow();
    //echo $assertionstable->show();
    
} //end else hasAccess
//End View Assertions
//View category
$this->setVar('pageSuppressXML', true);
if ($hasAccess) {
    //Language Items
    $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_category'
    )));
    $objLink->link = $iconAdd->show();
    $categoryList = $this->objDbCategoryList->getByItem();
    // Create a table object
    $categorytable = &$this->newObject("htmltable", "htmlelements");
    $categorytable->border = 0;
    $categorytable->cellspacing = '12';
    $categorytable->width = "50%";
    // Add the table heading.
    $categorytable->startRow();
    $categorytable->addCell($objLink->show() , '', '', '', '', 'colspan="2"');
    $categorytable->endRow();
    $categorytable->startRow();
    $categorytable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_category", 'eportfolio') . "</b>");
    $categorytable->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($categoryList)) {
        foreach($categoryList as $item) {
            // Display each field for activities
            $categorytable->startRow();
            $categorytable->addCell($item['category'], "", NULL, NULL, $class, '');
            // Show the edit link
            $iconEdit = $this->getObject('geticon', 'htmlelements');
            $iconEdit->setIcon('edit');
            $iconEdit->alt = $objLanguage->languageText("word_edit");
            $iconEdit->align = false;
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'editcategory',
                'id' => $item["id"]
            )));
            //if( $this->isValid( 'edit' ))
            $objLink->link = $iconEdit->show();
            $linkEdit = $objLink->show();
            // Show the delete link
            $iconDelete = $this->getObject('geticon', 'htmlelements');
            $iconDelete->setIcon('delete');
            $iconDelete->alt = $objLanguage->languageText("mod_eportfolio_delete", 'eportfolio');
            $iconDelete->align = false;
            $objConfirm = &$this->getObject("link", "htmlelements");
            $objConfirm = &$this->newObject('confirm', 'utilities');
            $checkAssociation = $this->objDbCategorytypeList->listCategory($item["id"]);
            if (!empty($checkAssociation)) {
                $deleteLink = new link("javascript:alert('" . $this->objLanguage->languageText('mod_eportfolio_failDelete', 'eportfolio') . ".');");
                $deleteLink->link = $iconDelete->show();
                // $categorytable->addCell($linkEdit. $deleteLink->show(), "", NULL, NULL, $class, '');
                
            } else {
                $objConfirm->setConfirm($iconDelete->show() , $this->uri(array(
                    'module' => 'eportfolio',
                    'action' => 'deletecategory',
                    'id' => $item["id"]
                )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
                //	$categorytable->addCell($linkEdit. $objConfirm->show(), "", NULL, NULL, $class, '');
                
            }
            $categorytable->endRow();
        }
        unset($item);
    } else {
        $categorytable->startRow();
        $categorytable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
        $categorytable->endRow();
    }
    $categoryaddlink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_category'
    )));
    $categoryaddlink->link = $objLanguage->languageText("mod_eportfolio_addCategory", 'eportfolio');
    $categorytable->startRow();
    $categorytable->addCell($categoryaddlink->show() , '', '', '', '', 'colspan="2"');
    $categorytable->endRow();
    //echo $categorytable->show();
    
}
//End View category
//View categorytype
if ($hasAccess) {
    //Language Items
    $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
    $categoryList = $this->objDbCategoryList->getByItem();
    $mycategoryList = $this->objDbCategoryList->getByItem();
    $linkAdd = '';
    // Show the add link
    $iconAdd = $this->getObject('geticon', 'htmlelements');
    $iconAdd->setIcon('add');
    $iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
    $iconAdd->align = false;
    $objLink = &$this->getObject('link', 'htmlelements');
    $objLink->link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_categorytype'
    )));
    $objLink->link = $iconAdd->show();
    // echo $categorytypeobjHeading->show();
    $categoryList = $this->objDbCategorytypeList->getByItem();
    // Create a table object
    $categorytypetable = &$this->newObject("htmltable", "htmlelements");
    $categorytypetable->border = 0;
    $categorytypetable->cellspacing = '12';
    $categorytypetable->width = "50%";
    //Check for categories
    if (!empty($categoryList)) {
        $categorytypetable->startRow();
        $categorytypetable->addCell($objLink->show() , '', '', '', '', 'colspan="3"');
        $categorytypetable->endRow();
    }
    $categorytypetable->startRow();
    $categorytypetable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_category", 'eportfolio') . "</b>");
    $categorytypetable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_categoryType", 'eportfolio') . "</b>");
    $categorytypetable->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($categoryList)) {
        foreach($categoryList as $item) {
            // Display each field for activities
            $categorytypetable->startRow();
            $category = $this->objDbCategoryList->listSingle($item['categoryid']);
            if (!empty($category)) {
                $categorytypetable->addCell($category[0]['category'], "", NULL, NULL, $class, '');
            }
            $categorytypetable->addCell($item['type'], "", NULL, NULL, $class, '');
            // Show the edit link
            $iconEdit = $this->getObject('geticon', 'htmlelements');
            $iconEdit->setIcon('edit');
            $iconEdit->alt = $objLanguage->languageText("word_edit");
            $iconEdit->align = false;
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'editcategorytype',
                'id' => $item["id"]
            )));
            //if( $this->isValid( 'edit' ))
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
                'action' => 'deletecategorytype',
                'id' => $item["id"]
            )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
            //echo $objConfirm->show();
            $categorytypetable->addCell($linkEdit, "", NULL, NULL, $class, '');
            //$categorytypetable->addCell($linkEdit. $objConfirm->show(), "", NULL, NULL, $class, '');
            $categorytypetable->endRow();
        }
        unset($item);
    } else {
        $categorytypetable->startRow();
        $categorytypetable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
        $categorytypetable->endRow();
    }
    $categorytypeaddlink = new link($this->uri(array(
        'module' => 'eportfolio',
        'action' => 'add_categorytype'
    )));
    $categorytypeaddlink->link = $objLanguage->languageText("mod_eportfolio_addCategorytype", 'eportfolio');
    //Check for categories
    if (!empty($mycategoryList)) {
        $categorytypetable->startRow();
        $categorytypetable->addCell($categorytypeaddlink->show() , '', '', '', '', 'colspan="3"');
        $categorytypetable->endRow();
    }
    //echo $categorytypetable->show();
    
}
//End View categorytype

//Information Title
$objinfoTitles->str = $objUser->getSurname() . $objLanguage->languageText("phrase_eportfolio_userinformation", 'eportfolio');
$this->objmainTab = $this->newObject('tabber', 'htmlelements');
$this->objTab = $this->newObject('tabber', 'htmlelements');
$this->objTab->init();
$this->objmainTab->init();

//Get Visible MAIN blocks if mainBlocks is empty
$mainBlocks = $this->objEPBlocks->getVisibleBlocks('main', $this->userId);
//For each tab, check if made visible and order as per the eportfolio blocks

//Step through each Block
foreach ($mainBlocks as $mainBlock) {
    if ($mainBlock["title"]=='Identification') {
        //Names tab (Visible By Default)
        $this->objTab->tabId = "minitab001";
        $this->objTab->addTab(array(
            'name' => $this->objLanguage->languageText("word_name") ,
            'content' => $userTable->show()
        ));
        //Get Visible IDENTIFICATION blocks
        $identityBlocks = $this->objEPBlocks->getVisibleBlocks('identity');
        //Step through each Block
        foreach ($identityBlocks as $identityBlock) {
            if  ($identityBlock["title"]=='Address') {
                //Address Tab
                $this->objTab->tabId = "identitytab001";
                $this->objTab->addTab(array(
                    'name' => $this->objLanguage->languageText("mod_eportfolio_wordAddress", 'eportfolio') ,
                    'content' => $addressTable->show()
                ));
            } elseif  ($identityBlock["title"]=='Contact') {
                //Contact Tab
                $this->objTab->tabId = "identitytab002";
                $this->objTab->addTab(array(
                    'name' => $this->objLanguage->languageText("mod_eportfolio_wordContact", 'eportfolio') ,
                    'content' => $contactTable->show()
                ));
            } elseif  ($identityBlock["title"]=='Email') {
                //Email Tab
                $this->objTab->tabId = "identitytab003";
                $this->objTab->addTab(array(
                    'name' => $this->objLanguage->languageText("mod_eportfolio_wordEmail", 'eportfolio') ,
                    'content' => $emailTable->show()
                ));
            } elseif  ($identityBlock["title"]=='Demographics') {
                //Demographics Tab
                $this->objTab->tabId = "identitytab004";
                $this->objTab->addTab(array(
                    'name' => $this->objLanguage->languageText("mod_eportfolio_wordDemographics", 'eportfolio') ,
                    'content' => $demographicsTable->show()
                ));
            }
        }
        $infotabs = $this->objTab->show();
        //Identification Tab
        $this->objmainTab->tabId = "maintab001";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordInformation", 'eportfolio') ,
            'content' => $infotabs
        ));
    } elseif  ($mainBlock["title"]=='Activities') {
        //Activity Title
        $this->objmainTab->tabId = "maintab002";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio') ,
            'content' => $activityTable->show()
        ));        
    } elseif  ($mainBlock["title"]=='Affiliation') {
        //Affiliation Title
        $this->objmainTab->tabId = "maintab003";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') ,
            'content' => $affiliationTable->show()
        ));        
    } elseif  ($mainBlock["title"]=='Transcripts') {
        //Transcripts Title
        $this->objmainTab->tabId = "maintab004";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio') ,
            'content' => $transcriptTable->show()
        ));
    } elseif  ($mainBlock["title"]=='Qualifications') {
        //Qualifications Title
        $this->objmainTab->tabId = "maintab005";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') ,
            'content' => $qclTable->show()
        ));
    } elseif  ($mainBlock["title"]=='Goals') {
        //Goals Title
        $this->objmainTab->tabId = "maintab006";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio') ,
            'content' => $goalsTable->show()
        ));
    } elseif  ($mainBlock["title"]=='Competencies') {
        //Competencies Title
        $this->objmainTab->tabId = "maintab007";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio') ,
            'content' => $competencyTable->show()
        ));
    } elseif  ($mainBlock["title"]=='Interests') {
        //Interests Title
        $this->objmainTab->tabId = "maintab008";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio') ,
            'content' => $interestTable->show()
        ));
    } elseif  ($mainBlock["title"]=='Reflections') {
        //Reflections Title
        $this->objmainTab->tabId = "maintab009";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio') ,
            'content' => $reflectionTable->show()
        ));
    } elseif  ($mainBlock["title"]=='Assertions') {
        //Assertions Title
        $this->objmainTab->tabId = "maintab010";
        $this->objmainTab->addTab(array(
            'name' => $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio') ,
            'content' => $assertionstable->show()
        ));
    }
}
//Category
if ($hasAccess) {
    //Category
    $this->objTab->init();
    $this->objTab->tabId = "cat001";
    $this->objTab->addTab(array(
        'name' => $this->objLanguage->languageText("mod_eportfolio_category", "eportfolio") ,
        'content' => $categorytable->show()
    ));
    //Category type
    $this->objTab->tabId = "cat002";
    $this->objTab->addTab(array(
        'name' => $this->objLanguage->languageText("mod_eportfolio_categoryType", "eportfolio") ,
        'content' => $categorytypetable->show()
    ));
    $categoryTab = $this->objTab->show();
    //category tab
    $this->objmainTab->tabId = "maintab010";
    $this->objmainTab->addTab(array(
        'name' => $this->objLanguage->languageText("mod_eportfolio_wordCategory", 'eportfolio') ,
        'content' => $categoryTab
    ));
}
$myeportfolioTab = $this->objmainTab->show();
echo $myeportfolioTab;
if ($this->getParam('message') == 'uploadsuccessful') {
    //$uploadstatus = $this->getParam('status');
    $alertBox = $this->getObject('alertbox', 'htmlelements');
    $alertBox->putJs();
    echo "<script type='text/javascript'>
 var browser=navigator.appName;
 var b_version=parseFloat(b_version);
 if(browser=='Microsoft Internet Explorer'){
	alert('" . $this->objLanguage->languageText('mod_eportfolio_congratulations', 'eportfolio') . '! ' . $this->objLanguage->languageText('mod_eportfolio_successMessage', 'eportfolio') . "');
 }else{
	 jQuery.facebox(function() {
	  jQuery.get('" . str_replace('&amp;', '&', $this->uri(array(
        'action' => 'uploaddonemessage'
    ))) . "', function(data) {
	    jQuery.facebox(data);
	  })
	 })
 }
</script>";
}
if ($this->getParam('message') == 'sorryemptypdf') {
    //$uploadstatus = $this->getParam('status');
    $alertBox = $this->getObject('alertbox', 'htmlelements');
    $alertBox->putJs();
    echo "<script type='text/javascript'>
 var browser=navigator.appName;
 var b_version=parseFloat(b_version);
 if(browser=='Microsoft Internet Explorer'){
	alert('" . $this->objLanguage->languageText('mod_eportfolio_wordNotice', 'eportfolio') . '! ' . $this->objLanguage->languageText('phrase_eportfolio_emptypdfmessage', 'eportfolio') . "');
 }else{
	 jQuery.facebox(function() {
	  jQuery.get('" . str_replace('&amp;', '&', $this->uri(array(
        'action' => 'emptypdfmessage'
    ))) . "', function(data) {
	    jQuery.facebox(data);
	  })
	 })
 }
</script>";
}
?>
