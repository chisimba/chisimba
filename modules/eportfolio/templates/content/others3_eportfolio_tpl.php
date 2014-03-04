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
$this->loadClass('checkbox', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objinfoTitles = &$this->getObject('htmlheading', 'htmlelements');
$objactivityTitles = &$this->getObject('htmlheading', 'htmlelements');
$objaddressTitles = &$this->getObject('htmlheading', 'htmlelements');
$objcontactTitles = &$this->getObject('htmlheading', 'htmlelements');
$emailobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$demographicsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$objactivityTitles = &$this->getObject('htmlheading', 'htmlelements');
$objaddressTitles = &$this->getObject('htmlheading', 'htmlelements');
$objaffiliationTitles = &$this->getObject('htmlheading', 'htmlelements');
$objtranscriptTitles = &$this->getObject('htmlheading', 'htmlelements');
$objqclTitles = &$this->getObject('htmlheading', 'htmlelements');
$objgoalsTitles = &$this->getObject('htmlheading', 'htmlelements');
$objcompetencyTitles = &$this->getObject('htmlheading', 'htmlelements');
$objinterestTitles = &$this->getObject('htmlheading', 'htmlelements');
$objreflectionTitles = &$this->getObject('htmlheading', 'htmlelements');
$objassertionsTitles = &$this->getObject('htmlheading', 'htmlelements');
$objcategoryTitles = &$this->getObject('htmlheading', 'htmlelements');
$tabBox = $this->newObject('tabpane', 'htmlelements');
$childtabBox = $this->newObject('tabpane', 'htmlelements');
$contacttabBox = $this->newObject('tabpane', 'htmlelements');
$emailtabBox = $this->newObject('tabpane', 'htmlelements');
$demographicstabBox = $this->newObject('tabpane', 'htmlelements');
$featureBox = $this->newObject('featurebox', 'navigation');
$addressfeatureBox = $this->newObject('featurebox', 'navigation');
$contactfeatureBox = $this->newObject('featurebox', 'navigation');
$demographicsfeatureBox = $this->newObject('featurebox', 'navigation');
$page = '';
$demographicspage = '';
$emailpage = '';
$contactpage = '';
$addresspage = '';
$activitypage = '';
$affiliationpage = '';
$transcriptpage = '';
$qclpage = '';
$goalspage = '';
$competencypage = '';
$interestpage = '';
$reflectionpage = '';
$assertionspage = '';
$categorypage = '';
$categorytypepage = '';
//Get Group Name
$groupname = $this->_objGroupAdmin->getName($groupId);
//$groupId = $this->getParam('groupId', null);
$filter = " WHERE id = '$groupId'";
$parentId = $this->_objGroupAdmin->getGroups($fields = array(
    "id",
    "name",
    "parent_id"
) , $filter);
$myparentId = $parentId[0];
$userId = $this->_objGroupAdmin->getname($myparentId[parent_id]);
$fullname = $this->objUserAdmin->getUserDetails($userId);
$groupId = $groupId;
$objHeading->align = center;
$objHeading->type = 2;
$objHeading->str = '<font color="#F38C0B">' . $objUser->fullName($fullname[userid]) . ' ' . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio') . '</font>';
echo $objHeading->show();
echo "</br>";
//Link to epms home
$iconSelect = $this->getObject('geticon', 'htmlelements');
$iconSelect->setIcon('home');
$iconSelect->alt = $objLanguage->languageText("mod_eportfolio_eportfoliohome", 'eportfolio');
$mnglink = new link($this->uri(array(
    'module' => 'eportfolio'
)));
$mnglink->link = $iconSelect->show();
$linkManage = $mnglink->show();
echo '<div align="center">' . $linkManage . '</div>';
echo "</br>";
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'addparts'
)));
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
$objHeading->align = left;
$objinfoTitles->type = 1;
$objaddressTitles->type = 1;
$objcontactTitles->type = 1;
$hasAccess = $this->objEngine->_objUser->isContextLecturer();
$hasAccess|= $this->objEngine->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
$link = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_contact'
)));
$link->link = 'View Identification Details';
//echo '<br clear="left" />'.$link->show();
//Start Address View
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$addressList = $this->objDbAddressList->getByItem($fullname[userid]);
// Create a table object
$addressTable = &$this->newObject("htmltable", "htmlelements");
$addressTable->border = 0;
$addressTable->cellspacing = '12';
$addressTable->width = "100%";
// Add the table heading.
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
    $addcnt = 0;
    foreach($addressList as $addressItem) {
        //Check if contact exists in group
        $isMember = $this->checkIfExists($addressItem['id'], $groupId);
        if ($isMember) {
            $addcnt = 1;
            // Display each field for addresses
            $addressTable->startRow();
            $cattype = $this->objDbCategorytypeList->listSingle($addressItem['type']);
            $addressTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['street_no'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['street_name'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['locality'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['city'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['postcode'], "", NULL, NULL, NULL, '');
            $addressTable->addCell($addressItem['postal_address'], "", NULL, NULL, NULL, '');
            $addressTable->endRow();
        }
    }
    unset($addressItem);
}
if ($addcnt == 0) {
    $addressTable->startRow();
    $addressTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
    $addressTable->endRow();
}
//echo '<br clear="left" />'.$mainlink->show();
//End Address View
//Start Contacts View
$contactList = $this->objDbContactList->getByItem($fullname[userid]);
$emailList = $this->objDbEmailList->getByItem($fullname[userid]);
// Create a table object
$contactTable = &$this->newObject("htmltable", "htmlelements");
$contactTable->border = 0;
$contactTable->cellspacing = '3';
$contactTable->width = "100%";
// Add the table heading.
$contactTable->startRow();
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contacttype", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . "</b>");
$contactTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contactnumber", 'eportfolio') . "</b>");
$contactTable->endRow();
// Step through the list of addresses.
if (!empty($contactList)) {
    $concnt = 0;
    foreach($contactList as $contactItem) {
        // Display each field
        $cattype = $this->objDbCategorytypeList->listSingle($contactItem['type']);
        $modetype = $this->objDbCategorytypeList->listSingle($contactItem['contact_type']);
        //Check if contact exists in group
        $isGroupMember = $this->checkIfExists($contactItem['id'], $groupId);
        if ($isGroupMember) {
            $concnt = 1;
            $contactTable->startRow();
            $contactTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
            $contactTable->addCell($modetype[0]['type'], "", NULL, NULL, NULL, '');
            $contactTable->addCell($contactItem['country_code'], "", NULL, NULL, NULL, '');
            $contactTable->addCell($contactItem['area_code'], "", NULL, NULL, NULL, '');
            $contactTable->addCell($contactItem['id_number'], "", NULL, NULL, NULL, '');
            $contactTable->endRow();
        }
    }
    unset($contactItem);
}
if ($concnt == 0) {
    $contactTable->startRow();
    $contactTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $contactTable->endRow();
}
//End Contact View
//Start Email View
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->alt = $objLanguage->languageText("mod_eportfolio_add", 'eportfolio');
//echo $emailobjHeading->show();
// Create a table object for emails
$emailTable = &$this->newObject("htmltable", "htmlelements");
$emailTable->border = 0;
$emailTable->cellspacing = '3';
$emailTable->width = "50%";
// Add the table heading.
$emailTable->startRow();
$emailTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$emailTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_email", 'eportfolio') . "</b>");
$emailTable->endRow();
// Step through the list of addresses.
$class = 'even';
if (!empty($emailList)) {
    $emcnt = 0;
    foreach($emailList as $emailItem) {
        //Check if contact exists in group
        $isMember = $this->checkIfExists($emailItem['id'], $groupId);
        if ($isMember) {
            $emcnt = 1;
            // Display each field for addresses
            $cattype = $this->objDbCategorytypeList->listSingle($emailItem['type']);
            $emailTable->startRow();
            $emailTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
            $emailTable->addCell($emailItem['email'], "", NULL, NULL, NULL, '');
            $emailTable->endRow();
        }
    }
    unset($emailItem);
}
if ($emcnt == 0) {
    $emailTable->startRow();
    $emailTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
    $emailTable->endRow();
}
//End Email View
//Demographics view
$demographicsList = $this->objDbDemographicsList->getByItem($fullname[userid]);
// Create a table object
$demographicsTable = &$this->newObject("htmltable", "htmlelements");
$demographicsTable->border = 0;
$demographicsTable->cellspacing = '3';
$demographicsTable->width = "50%";
// Add the table heading.
$demographicsTable->startRow();
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . "</b>");
$demographicsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . "</b>");
$demographicsTable->endRow();
// Step through the list of addresses.
if (!empty($demographicsList)) {
    $demcnt = 0;
    foreach($demographicsList as $demographicsItem) {
        // Display each field for Demographics
        $cattype = $this->objDbCategorytypeList->listSingle($demographicsItem['type']);
        //Check if contact exists in group
        $dgisGrpMember = $this->checkIfExists($demographicsItem['id'], $groupId);
        if ($dgisGrpMember) {
            $demcnt = 1;
            $demographicsTable->startRow();
            $demographicsTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
            $demographicsTable->addCell($this->objDate->formatDate($demographicsItem['birth']) , "", NULL, NULL, NULL, '');
            $demographicsTable->addCell($demographicsItem['nationality'], "", NULL, NULL, NULL, '');
            $demographicsTable->endRow();
        }
    }
    unset($demographicsItem);
}
if ($demcnt == 0) {
    $demographicsTable->startRow();
    $demographicsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $demographicsTable->endRow();
}
//echo $demographicsTable->show();
//End Demographics view
//view name
// Show the heading
$objHeading->type = 3;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_title", 'eportfolio');
//echo $objHeading->show();
// Create a table object
$userTable = &$this->newObject("htmltable", "htmlelements");
$userTable->border = 0;
$userTable->cellspacing = '12';
$userTable->width = "40%";
// Add the table heading.
$userTable->startRow();
$userTable->addCell("<b>" . $objLanguage->languageText('word_title', 'system') . "</b>");
$userTable->addCell("<b>" . $objLanguage->languageText('word_surname', 'system') . "</b>");
$userTable->addCell("<b>" . $objLanguage->languageText('phrase_othernames', 'eportfolio') . "</b>");
$userTable->endRow();
// Step through the list.
$owner = $this->objUserAdmin->getUserDetails($fullname[id]);
if (!empty($user)) {
    // Display each field for addresses
    $userTable->startRow();
    $userTable->addCell($owner['title'], "", NULL, NULL, NULL, '');
    $userTable->addCell($owner['surname'], "", NULL, NULL, NULL, '');
    $userTable->addCell($owner['firstname'], "", NULL, NULL, NULL, '');
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
//View Activity
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$activitylist = $this->objDbActivityList->getByItem($fullname[userid]);
// Create a table object
$activityTable = &$this->newObject("htmltable", "htmlelements");
$activityTable->border = 0;
$activityTable->cellspacing = '3';
$activityTable->width = "100%";
// Add the table heading.
$activityTable->startRow();
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$activityTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($activitylist)) {
    $i = 0;
    $actcnt = 0;
    foreach($activitylist as $item) {
        //Get context title
        $objDbContext = &$this->getObject('dbcontext', 'context');
        $mycontextRecord = $objDbContext->getContextDetails($item['contextid']);
        if (!empty($mycontextRecord)) {
            $mycontextTitle = $mycontextRecord['title'];
        } else {
            $mycontextTitle = $item['contextid'];
        }
        $acisMember = $this->checkIfExists($item['id'], $groupId);
        if ($acisMember) {
            $actcnt = 1;
            // Display each field for activities
            $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
            $activityTable->startRow();
            $activityTable->addCell($mycontextTitle, "", NULL, NULL, $class, '');
            $activityTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
            $activityTable->addCell($this->objDate->formatDate($item['start']) , "", NULL, NULL, $class, '');
            $activityTable->addCell($this->objDate->formatDate($item['finish']) , "", NULL, NULL, $class, '');
            $activityTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $activityOwner = $item["id"] . ',' . $fullname[userid] . ',' . $groupId;
            //Display Icon
            $atyiconView = $this->getObject('geticon', 'htmlelements');
            $atyiconView->setIcon('bookopen');
            $atyiconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
            $atymnglink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'displayothers_activity',
                'thisid' => $activityOwner
            )));
            $atymnglink->link = $atyiconView->show();
            $atylinkManage = $atymnglink->show();
            $activityTable->addCell($atylinkManage, "", NULL, NULL, $class, '');
            $activityTable->endRow();
        }
    }
    unset($item);
}
if ($actcnt == 0) {
    $activityTable->startRow();
    $activityTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $activityTable->endRow();
}
//End View Activity
//View Affiliation
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$affiliationList = $this->objDbAffiliationList->getByItem($fullname[userid]);
// Create a table object
$affiliationTable = &$this->newObject("htmltable", "htmlelements");
$affiliationTable->border = 0;
$affiliationTable->cellspacing = '12';
$affiliationTable->width = "100%";
// Add the table heading.
$affiliationTable->startRow();
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_classificationView", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_roleView", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
$affiliationTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
$affiliationTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($affiliationList)) {
    $i = 0;
    $affcnt = 0;
    foreach($affiliationList as $affiliationItem) {
        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($affiliationItem['type']);
        //Check if exists in group
        $affisMember = $this->checkIfExists($affiliationItem['id'], $groupId);
        if ($affisMember) {
            $affcnt = 1;
            $affiliationTable->startRow();
            $affiliationTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
            $affiliationTable->addCell($affiliationItem['classification'], "", NULL, NULL, $class, '');
            $affiliationTable->addCell($affiliationItem['role'], "", NULL, NULL, $class, '');
            $affiliationTable->addCell($affiliationItem['organisation'], "", NULL, NULL, $class, '');
            $affiliationTable->addCell($this->objDate->formatDate($affiliationItem['start']) , "", NULL, NULL, $class, '');
            $affiliationTable->addCell($this->objDate->formatDate($affiliationItem['finish']) , "", NULL, NULL, $class, '');
            $affiliationTable->endRow();
        }
    }
    unset($affiliationItem);
}
if ($affcnt == 0) {
    $affiliationTable->startRow();
    $affiliationTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
    $affiliationTable->endRow();
}
//View Affiliation
//View Transcript
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$transcriptlist = $this->objDbTranscriptList->getByItem($fullname[userid]);
// Create a table object
$transcriptTable = &$this->newObject("htmltable", "htmlelements");
$transcriptTable->border = 0;
$transcriptTable->cellspacing = '12';
$transcriptTable->width = "50%";
// Add the table heading.
$transcriptTable->startRow();
$transcriptTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$transcriptTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$transcriptTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($transcriptlist)) {
    $tracnt = 0;
    foreach($transcriptlist as $item) {
        //Check if exists in group
        $transisMember = $this->checkIfExists($item['id'], $groupId);
        if ($transisMember) {
            $tracnt = 1;
            // Display each field for activities
            $transcriptTable->startRow();
            $transcriptTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $transcriptOwner = $item["id"] . ',' . $fullname[userid] . ',' . $groupId;
            //Display Icon
            $triconView = $this->getObject('geticon', 'htmlelements');
            $triconView->setIcon('bookopen');
            $triconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
            $trmnglink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'displayothers_transcript',
                'thisid' => $transcriptOwner
            )));
            $trmnglink->link = $triconView->show();
            $trlinkManage = $trmnglink->show();
            $transcriptTable->addCell($trlinkManage, "", NULL, NULL, $class, '');
            $transcriptTable->endRow();
        }
    }
    unset($item);
}
if ($tracnt == 0) {
    $transcriptTable->startRow();
    $transcriptTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="3"');
    $transcriptTable->endRow();
}
//View Transcript
//View Qcl
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$qclList = $this->objDbQclList->getByItem($fullname[userid]);
// Create a table object
$qclTable = &$this->newObject("htmltable", "htmlelements");
$qclTable->border = 0;
$qclTable->cellspacing = '3';
$qclTable->width = "100%";
// Add the table heading.
$qclTable->startRow();
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordtitle", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_level", 'eportfolio') . "</b>");
$qclTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
//$qclTable->addHeaderCell("<b>".$objLanguage->languageText("mod_eportfolio_shortdescription",'eportfolio')."</b>");
$qclTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($qclList)) {
    foreach($qclList as $qclItem) {
        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($qclItem['qcl_type']);
        $qclcnt = 0;
        //Check if exists in group
        $qclisMember = $this->checkIfExists($qclItem['id'], $groupId);
        if ($qclisMember) {
            $qclcnt = 1;
            $qclTable->startRow();
            $qclTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
            $qclTable->addCell($qclItem['qcl_title'], "", NULL, NULL, $class, '');
            $qclTable->addCell($qclItem['organisation'], "", NULL, NULL, $class, '');
            $qclTable->addCell($qclItem['qcl_level'], "", NULL, NULL, $class, '');
            $qclTable->addCell($this->objDate->formatDate($qclItem['award_date']) , "", NULL, NULL, $class, '');
            $qclTable->endRow();
        }
    }
    unset($qclItem);
}
if ($qclcnt == 0) {
    $qclTable->startRow();
    $qclTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="6"');
    $qclTable->endRow();
}
//End View Qcl
//View Goals
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
$goalsList = $this->objDbGoalsList->getByItem($fullname[userid]);
// Create a table object
$goalsTable = &$this->newObject("htmltable", "htmlelements");
$goalsTable->border = 0;
$goalsTable->cellspacing = '12';
$goalsTable->width = "60%";
// Add the table heading.
$goalsTable->startRow();
$goalsTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_goals", 'eportfolio') . "</b>");
$goalsTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($goalsList)) {
    $i = 0;
    $goalcnt = 0;
    foreach($goalsList as $item) {
        //Check if exists in group
        $glisMember = $this->checkIfExists($item['id'], $groupId);
        if ($glisMember) {
            $goalcnt = 1;
            // Display each field for activities
            $goalsTable->startRow();
            $goalsTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $goalsTable->endRow();
        }
    }
    unset($item);
}
if ($goalcnt == 0) {
    $goalsTable->startRow();
    $goalsTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="2"');
    $goalsTable->endRow();
}
//End View Goals
//View Competency
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$competencyList = $this->objDbCompetencyList->getByItem($fullname[userid]);
// Create a table object
$competencyTable = &$this->newObject("htmltable", "htmlelements");
$competencyTable->border = 0;
$competencyTable->cellspacing = '12';
$competencyTable->width = "100%";
// Add the table heading.
$competencyTable->startRow();
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$competencyTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($competencyList)) {
    foreach($competencyList as $item) {
        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $comcnt = 0;
        //Check if exists in group
        $ctyisMember = $this->checkIfExists($item['id'], $groupId);
        if ($ctyisMember) {
            $comcnt = 1;
            $competencyTable->startRow();
            $competencyTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
            $competencyTable->addCell($this->objDate->formatDate($item['award_date']) , "", NULL, NULL, $class, '');
            $competencyTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $competencyOwner = $item["id"] . ',' . $fullname[userid] . ',' . $groupId;
            //Display Icon
            $ctyiconView = $this->getObject('geticon', 'htmlelements');
            $ctyiconView->setIcon('bookopen');
            $ctyiconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
            $ctymnglink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'displayothers_competency',
                'thisid' => $competencyOwner
            )));
            $ctymnglink->link = $ctyiconView->show();
            $ctylinkManage = $ctymnglink->show();
            $competencyTable->addCell($ctylinkManage, "", NULL, NULL, $class, '');
            $competencyTable->endRow();
        }
    }
    unset($item);
}
if ($comcnt == 0) {
    $competencyTable->startRow();
    $competencyTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $competencyTable->endRow();
}
//End View Competency
//View Interest
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$interestList = $this->objDbInterestList->getByItem($fullname[userid]);
// Create a table object
$interestTable = &$this->newObject("htmltable", "htmlelements");
$interestTable->border = 0;
$interestTable->cellspacing = '12';
$interestTable->width = "100%";
// Add the table heading.
$interestTable->startRow();
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$interestTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($interestList)) {
    foreach($interestList as $item) {
        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        //Check if exists in group
        $incnt = 0;
        $intrstisMember = $this->checkIfExists($item['id'], $groupId);
        if ($intrstisMember) {
            $incnt = 1;
            $interestTable->startRow();
            $interestTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
            $interestTable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
            $interestTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $interestOwner = $item["id"] . ',' . $fullname[userid] . ',' . $groupId;
            //Display Icon
            $inticonView = $this->getObject('geticon', 'htmlelements');
            $inticonView->setIcon('bookopen');
            $inticonView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
            $intmnglink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'displayothers_interest',
                'thisid' => $interestOwner
            )));
            $intmnglink->link = $inticonView->show();
            $intlinkManage = $intmnglink->show();
            $interestTable->addCell($intlinkManage, "", NULL, NULL, $class, '');
            $interestTable->endRow();
        }
    }
    unset($item);
}
if ($incnt == 0) {
    $interestTable->startRow();
    $interestTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $interestTable->endRow();
}
//End View Interest
//View reflection
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$reflectionList = $this->objDbReflectionList->getByItem($fullname[userid]);
// Create a table object
$reflectionTable = &$this->newObject("htmltable", "htmlelements");
$reflectionTable->border = 0;
$reflectionTable->cellspacing = '3';
$reflectionTable->width = "100%";
// Add the table heading.
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
        //Check if exists in group
        $refcount = 0;
        $rfctnisMember = $this->checkIfExists($item['id'], $groupId);
        if ($rfctnisMember) {
            $refcount = 1;
            // Display each field for activities
            $reflectionTable->startRow();
            $reflectionTable->addCell($item['rationale'], "", NULL, NULL, $class, '');
            $reflectionTable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
            $reflectionTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $reflectionOwner = $item["id"] . ',' . $fullname[userid] . ',' . $groupId;
            //Display Icon
            $riconView = $this->getObject('geticon', 'htmlelements');
            $riconView->setIcon('bookopen');
            $riconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
            $rmnglink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'displayothers_reflection',
                'thisid' => $reflectionOwner
            )));
            $rmnglink->link = $riconView->show();
            $rlinkManage = $rmnglink->show();
            $reflectionTable->addCell($rlinkManage, "", NULL, NULL, $class, '');
            $reflectionTable->endRow();
        }
    }
    unset($item);
}
if ($refcount == 0) {
    $reflectionTable->startRow();
    $reflectionTable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $reflectionTable->endRow();
}
//End View Reflection
//View assertions
//Language Items
$notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
// Show the heading
$assertionslist = $this->objDbAssertionList->getByItem($fullname[userid]);
// Create a table object
$assertionstable = &$this->newObject("htmltable", "htmlelements");
$assertionstable->border = 0;
$assertionstable->cellspacing = '3';
$assertionstable->width = "100%";
// Add the table heading.
$assertionstable->startRow();
$assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
$assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_display", 'eportfolio') . "</b>");
$assertionstable->endRow();
// Step through the list of addresses.
$class = NULL;
$arrayLists = array();
if (!empty($assertionslist)) {
    $asscount = 0;
    foreach($assertionslist as $item) {
        $asnisMember = $this->checkIfExists($item['id'], $groupId);
        if ($asnisMember) {
            $asscount = 1;
            // Display each field for activities
            $assertionstable->startRow();
            $assertionstable->addCell($item['rationale'], "", NULL, NULL, $class, '');
            $assertionstable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
            $assertionstable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
            $assertionOwner = $assertionslist[0]["id"] . ',' . $fullname[userid] . ',' . $groupId;
            //Display Icon
            $iconView = $this->getObject('geticon', 'htmlelements');
            $iconView->setIcon('bookopen');
            $iconView->alt = $objLanguage->languageText("mod_eportfolio_display", 'eportfolio');
            $mnglink = new link($this->uri(array(
                'module' => 'eportfolio',
                'action' => 'displayothers_assertion',
                'thisid' => $assertionOwner
            )));
            $mnglink->link = $iconView->show();
            $linkManage = $mnglink->show();
            $assertionstable->addCell($linkManage, "", NULL, NULL, $class, '');
            $assertionstable->endRow();
        }
    }
    unset($item);
}
if ($asscount == 0) {
    $assertionstable->startRow();
    $assertionstable->addCell($notestsLabel, '', '', '', 'noRecordsMessage', 'colspan="4"');
    $assertionstable->endRow();
}
//Store the GroupId
$groupId = new hiddeninput("groupId", $groupId);
$row = array(
    $groupId->show()
);
$assertionstable->addRow($row, NULL);
//}//end else hasAccess
//End View Assertions
//Information Title
$objinfoTitles->str = $objUser->getSurname($userId) . $objLanguage->languageText("phrase_eportfolio_userinformation", 'eportfolio');
$this->objmainTab = $this->newObject('tabber', 'htmlelements');
$this->objTab = $this->newObject('tabber', 'htmlelements');
$namesLabel = $userTable->show();
$addressLabel = $addressTable->show();
$contactLabel = $contactTable->show();
$demographicsLabel = $addressTab . $demographicsTable->show();
$emailLabel = $emailTable->show();
$activityLabel = $activityTable->show();
$this->objTab->init();
$this->objTab->tabId = TRUE;
$this->objTab->addTab(array(
    'name' => $this->objLanguage->languageText("word_name") ,
    'content' => $namesLabel
));
$this->objTab->tabId = FALSE;
$this->objTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordAddress", 'eportfolio') ,
    'content' => $addressLabel
));
$this->objTab->tabId = FALSE;
$this->objTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordContact", 'eportfolio') ,
    'content' => $contactLabel
));
$this->objTab->tabId = FALSE;
$this->objTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordEmail", 'eportfolio') ,
    'content' => $emailLabel
));
$this->objTab->tabId = FALSE;
$this->objTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordDemographics", 'eportfolio') ,
    'content' => $demographicsLabel
));
//Information tab
$this->objmainTab->init();
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordInformation", 'eportfolio') ,
    'content' => $this->objTab->show()
));
//Activity Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio') ,
    'content' => $activityLabel
));
//Affiliation Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') ,
    'content' => $affiliationTable->show()
));
//Transcript Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio') ,
    'content' => $transcriptTable->show()
));
//Qcl Title
$objqclTitles->str = $objUser->getSurname($userId) . $objLanguage->languageText("mod_eportfolio_qclheading", 'eportfolio');
$qclpage.= $featureBox->show($objqclTitles->show() , $qclTable->show() , 'yourbox5', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') ,
    'content' => $qclTable->show()
));
//Goals Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio') ,
    'content' => $goalsTable->show()
));
//Competency Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio') ,
    'content' => $competencyTable->show()
));
//interest Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio') ,
    'content' => $interestTable->show()
));
//reflection Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio') ,
    'content' => $reflectionTable->show()
));
//assertions Title
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio') ,
    'content' => $assertionstable->show()
));
$myeportfolioTab = $this->objmainTab->show();
$tabBox->addTab(array(
    'name' => $this->objLanguage->languageText("phrase_myePortfolio", 'eportfolio') ,
    'content' => $myeportfolioTab
) , 'winclassic-tab-style-sheet');
//echo $tabBox->show();
$form->addToForm($myeportfolioTab);
echo $form->show();
?>
