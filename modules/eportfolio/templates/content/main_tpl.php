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
$tabBox = $this->newObject('tabpane', 'htmlelements');
$this->objEssayView = $this->newObject('manageviews_essay', 'essay');
$this->viewAssessments = $this->newObject('viewassessments_Eportfolio', 'eportfolio');
$featureBox = &$this->newObject('featurebox', 'navigation');
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
$objHeading->type = 1;
$objHeading->align = 'center';
$objHeading->str = $objLanguage->languageText("mod_eportfolio_maintitle", 'eportfolio');
echo $objHeading->show();
echo "</br>";
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
$objHeading->str = $objUser->fullName() . ' ' . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio') . '    ' . $linkpdfManage . '    ' . $linkviewManage . '    ' . $linkExportManage . '    ' . $linkImportManage;
echo $objHeading->show();
$objHeading->align = 'left';
$objinfoTitles->type = 1;
$objaddressTitles->type = 1;
$objcontactTitles->type = 1;
$this->_objUser = $this->getObject('user', 'security');
//Get user contexts
$myContexts = $this->objContextUser->getUserContext($this->userId);
/*
$isContextLecturer = $this->objContextUser->getContextWhereLecturer($this->userId);
$isContextStudent = $this->objContextUser->getContextWhereStudent($this->userId);
$isContextGuest = $this->objContextUser->getContextWhereGuest($this->userId);
*/
//$hasAccess = $this->objEngine->_objUser->isContextLecturer();
//$hasAccess|= $this->objEngine->_objUser->isAdmin();
$hasAccess = $this->_objUser->isContextLecturer();
$hasAccess = $this->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
$link = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_contact'
)));
$link->link = 'View Identification Details';
//echo '<br clear="left" />'.$link->show();
//Create Owner and Guest group for user
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
//End Create
//View Other's eportfolio
// Create a table object for eportfolio
$epTable = &$this->newObject("htmltable", "htmlelements");
$epTable->border = 0;
$epTable->cellspacing = '3';
$epTable->width = "30%";
$epTable->startRow();
$epTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . "</b>", '', '', 'left', '', '');
$epTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_owner", 'eportfolio') . "</b>", '', '', 'left', '', '');
$epTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordGroup", 'eportfolio') . "</b>", '', '', 'left', '', '');
$epTable->endRow();
//Loading View others portfolio. Checks if the system contains groupops, otherwise uses old framework
if (class_exists('groupops', false)) {
    //getUserDirectGroups
    $groupexists = 0;
    $myPid = $this->objUser->PKId($this->objUser->userId());
    //$myGroups = $this->_objGroupAdmin->getUserGroups($this->objUser->userId());
    $myGroupsId = $this->_objGroupAdmin->getId($myPid);
    $myGroups = $this->_objGroupAdmin->getSubgroups($myGroupsId);
    //Get all groups and determine which ones the user belongs to
    $userGroupsArray = array();
    //Get the perm_user_d
    if (class_exists('groupops', false)) {
        $usrGrpId = $this->objGroupsOps->getUserByUserId($this->objUser->userId());
    }
    $permUserId = $usrGrpId['perm_user_id'];
    if (class_exists('groupops', false)) {
        $allGrps = $this->objGroupsOps->getAllGroups();
        foreach($allGrps as $thisGrp) {
            $isGpMbr = $this->objGroupsOps->isGroupMember($thisGrp['group_id'], $this->objUser->userId());
            if ($isGpMbr) {
                $userGroupsArray[] = $thisGrp['group_id'];
            }
        }
    }
    //Array to store grpUsers inorder to get the owner of the group
    $usersListArr = array();
    $usersPidListArr = array();
    $buddiesListArr = array();
    $buddiesPidListArr = array();
    if (!empty($userGroupsArray)) {
        foreach($userGroupsArray as $userGroup) {
            $grpUsers = $this->objGroupsOps->getUsersInGroup($userGroup);
            foreach($grpUsers as $grpUser) {
                //Get the users in these groups
                if (!in_array($grpUser['auth_user_id'], $usersListArr)) {
                    //Store user Pid
                    $userPid = $this->objUser->PKId($grpUser['auth_user_id']);
                    $usersPidListArr[] = $userPid;
                    //Store User Id
                    $usersListArr[] = $grpUser['auth_user_id'];
                    //Get the user's groupId (the root)
                    $userGrpId = $this->_objGroupAdmin->getId($userPid);
                    if (!empty($userGrpId)) {
                        //Get the user's sub groups
                        $userSubGrps = $this->_objGroupAdmin->getSubgroups($userGrpId);
                        //Check if empty
                        if (!empty($userSubGrps)) {
                            foreach($userSubGrps[0] as $key => $userSubGrp) {
                                //					echo "<br>userSubGrp<br>";
                                //The fields to use in the select for getting group users
                                $fields = array(
                                    'firstName',
                                    'surname',
                                    'tbl_users.id'
                                );
                                //Get the group users
                                $membersList = $this->_objGroupAdmin->getGroupUsers($key, $fields);
                                foreach($membersList as $users) {
                                    //Check if the logged in user is a user here, if true store userid and groupid
                                    if ($users['id'] == $this->userPid) {
                                        $buddiesPidListArr[$userGrpId] = $userPid;
                                        $buddiesListArr[$userGrpId] = $grpUser['auth_user_id'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $usrGrpOwner = array();
            foreach($userGroupsArray as $usrSubGrp) {
                $parentGrp = $this->_objGroupAdmin->getParent($usrSubGrp);
                if ($myPid !== $parentGrp) $usrGrpOwner[$usrSubGrp] = $parentGrp;
            }
        }
        foreach($buddiesPidListArr as $grpIdKey => $buddy) {
            //get the array key value
            $groupId = $grpIdKey;
            $filter = " WHERE id = '" . $groupId . "'";
            if ($buddy !== $myPid) {
                //    $fullname = $this->objUserAdmin->getUserDetails($ownerId);
                $fullname = $this->objUserAdmin->getUserDetails($buddy);
                if (!empty($fullname)) {
                    //Get the buddys' sub groups
                    $userSubGrps = $this->_objGroupAdmin->getSubgroups($groupId);
                    foreach($userSubGrps[0] as $key => $userSubGrp) {
                        //The fields to use in the select for getting group users
                        $fields = array(
                            'firstName',
                            'surname',
                            'tbl_users.id'
                        );
                        //Get the group users
                        $membersList = $this->_objGroupAdmin->getGroupUsers($key, $fields);
                        foreach($membersList as $users) {
                            //Check if the logged in user is a user here, if true store userid and groupid
                            if ($users['id'] == $this->userPid) {
                                //Select View
                                $iconSelect = $this->getObject('geticon', 'htmlelements');
                                $iconSelect->setIcon('view');
                                $iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $fullname[firstname] . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
                                $mnglink = new link($this->uri(array(
                                    'module' => 'eportfolio',
                                    'action' => 'view_others_eportfolio',
                                    'id' => $key,
                                    'ownerId' => $buddy
                                )));
                                $mnglink->link = $iconSelect->show();
                                $linkManage = $mnglink->show();
                                //Store Group id
                                $textinput = new textinput("groupId", $key);
                                $epTable->startRow();
                                $epTable->addCell($linkManage, '', '', 'left', '', '');
                                $epTable->addCell($fullname['title'] . ' ' . $fullname['firstname'] . ' ' . $fullname[surname], '', '', 'left', '', '');
                                $epTable->addCell($userSubGrp['group_define_name'], '', '', 'left', '', '');
                                $epTable->endRow();
                                $groupexists = $groupexists+1;
                            }
                        }
                    }
                }
            }
        }
    } else {
        $allGrps = $this->_objGroupAdmin->getUserGroups($this->objUser->PKId($this->objUser->userId()));
        $myPid = $this->objUser->PKId($this->objUser->userId());
        foreach($myGroups as $groupId) {
            $filter = " WHERE id = '$groupId'";
            $parentId = $this->_objGroupAdmin->getGroups($fields = array(
                "id",
                "name",
                "parent_id"
            ) , $filter);
            $myparentId = $parentId[0];
            $ownerId = $this->_objGroupAdmin->getname($myparentId[parent_id]);
            if ($ownerId !== $myPid) {
                $fullname = $this->objUserAdmin->getUserDetails($ownerId);
                if (!empty($fullname)) {
                    // Add row with user details.
                    $groupname = $this->_objGroupAdmin->getName($groupId);
                    //Select View
                    $iconSelect = $this->getObject('geticon', 'htmlelements');
                    $iconSelect->setIcon('view');
                    $iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $fullname[firstname] . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
                    $mnglink = new link($this->uri(array(
                        'module' => 'eportfolio',
                        'action' => 'view_others_eportfolio',
                        'id' => $groupId
                    )));
                    $mnglink->link = $iconSelect->show();
                    $linkManage = $mnglink->show();
                    //Store Group id
                    $textinput = new textinput("groupId", $groupId);
                    $epTable->startRow();
                    $epTable->addCell($linkManage, '', '', 'left', '', '');
                    $epTable->addCell($fullname[title] . ' ' . $fullname[firstname] . ' ' . $fullname[surname], '', '', 'left', '', '');
                    $epTable->addCell($groupname, '', '', 'left', '', '');
                    $epTable->endRow();
                    $groupexists = $groupexists+1;
                }
            }
        }
    }
    if ($groupexists == 0) {
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        $epTable->startRow();
        $epTable->addCell($notestsLabel, '', '', 'left', '', 'colspan="3"');
        $epTable->endRow();
    }
} else {
    //Else if groupops not found, use old groupadmin
    //getUserDirectGroups
    $groupexists = 0;
    $myGroups = $this->_objGroupAdmin->getUserGroups($this->objUser->PKId($this->objUser->userId()));
    $myPid = $this->objUser->PKId($this->objUser->userId());
    foreach($myGroups as $groupId) {
        $filter = " WHERE id = '$groupId'";
        $parentId = $this->_objGroupAdmin->getGroups($fields = array(
            "id",
            "name",
            "parent_id"
        ) , $filter);
        $myparentId = $parentId[0];
        $ownerId = $this->_objGroupAdmin->getname($myparentId['parent_id']);
        if ($ownerId !== $myPid) {
            $fullname = $this->objUserAdmin->getUserDetails($ownerId);
            if (!empty($fullname)) {
                // Add row with user details.
                $groupname = $this->_objGroupAdmin->getName($groupId);
                //Select View
                $iconSelect = $this->getObject('geticon', 'htmlelements');
                $iconSelect->setIcon('view');
                $iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $fullname[firstname] . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
                $mnglink = new link($this->uri(array(
                    'module' => 'eportfolio',
                    'action' => 'view_others3_eportfolio',
                    'id' => $groupId
                )));
                $mnglink->link = $iconSelect->show();
                $linkManage = $mnglink->show();
                //Store Group id
                $textinput = new textinput("groupId", $groupId);
                $epTable->startRow();
                $epTable->addCell($linkManage, '', '', 'left', '', '');
                $epTable->addCell($fullname['title'] . ' ' . $fullname['firstname'] . ' ' . $fullname[surname], '', '', 'left', '', '');
                $epTable->addCell($groupname, '', '', 'left', '', '');
                $epTable->endRow();
                $groupexists = $groupexists+1;
            }
        }
    }
    if ($groupexists == 0) {
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        $epTable->startRow();
        $epTable->addCell($notestsLabel, '', '', 'left', '', 'colspan="3"');
        $epTable->endRow();
    }
}
//View Other's eportfolio
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
$addressTable->addCell($objaddressTitles->show() , '', '', '', '', 'colspan="8"');
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
$linkcontactAdd = $objLink->show();
// Show the heading
$objcontactTitles->type = 3;
$objcontactTitles->str = $objLanguage->languageText("mod_eportfolio_contact", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkcontactAdd;
//echo $objHeading->show();
$contactList = $this->objDbContactList->getByItem($userId);
$emailList = $this->objDbEmailList->getByItem($userId);
// Create a table object
$contactTable = &$this->newObject("htmltable", "htmlelements");
$contactTable->border = 0;
$contactTable->cellspacing = '3';
$contactTable->width = "100%";
// Add the table heading.
$contactTable->startRow();
$contactTable->addCell($objcontactTitles->show() , '', '', '', '', 'colspan="6"');
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
$emailobjHeading->str = $objLanguage->languageText("mod_eportfolio_emailList", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $emailLinkAdd;
//echo $emailobjHeading->show();
// Create a table object for emails
$emailTable = &$this->newObject("htmltable", "htmlelements");
$emailTable->border = 0;
$emailTable->cellspacing = '3';
$emailTable->width = "50%";
// Add the table heading.
$emailTable->startRow();
$emailTable->addCell($emailobjHeading->show() , '', '', '', '', 'colspan="3"');
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
    $linkdemographicsAdd = $objLink->show();
    // Show the heading
    $demographicsobjHeading->str = $objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkdemographicsAdd;
    $demographicsTable->startRow();
    $demographicsTable->addCell($demographicsobjHeading->show() , '', '', '', '', 'colspan="4"');
    $demographicsTable->endRow();
    //echo $objHeading->show();
    
} else {
    $demographicsobjHeading->str = $objLanguage->languageText("mod_eportfolio_demographics", 'eportfolio');
    $demographicsTable->startRow();
    $demographicsTable->addCell($demographicsobjHeading->show() , '', '', '', '', 'colspan="4"');
    $demographicsTable->endRow();
}
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
        $datetime = explode("-", $this->objDate->formatDate($demographicsItem['birth']));
        $demographicsTable->startRow();
        $demographicsTable->addCell($cattype[0]['type'], "", NULL, NULL, NULL, '');
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
$objHeading->type = 3;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_title", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkEdit;
//echo $objHeading->show();
// Create a table object
$userTable = &$this->newObject("htmltable", "htmlelements");
$userTable->border = 0;
$userTable->cellspacing = '12';
$userTable->width = "40%";
// Add the table heading.
$userTable->startRow();
$userTable->addCell($objHeading->show() , '', '', '', '', 'colspan="3"');
$userTable->endRow();
$userTable->startRow();
$userTable->addCell("<b>" . $objLanguage->languageText('word_title', 'system') . "</b>");
$userTable->addCell("<b>" . $objLanguage->languageText('word_surname', 'system') . "</b>");
$userTable->addCell("<b>" . $objLanguage->languageText('phrase_othernames', 'eportfolio') . "</b>");
$userTable->endRow();
// Step through the list of addresses.
if (!empty($user)) {
    // Display each field for addresses
    $userTable->startRow();
    $userTable->addCell($user['title'], "", NULL, NULL, NULL, '');
    $userTable->addCell($user['surname'], "", NULL, NULL, NULL, '');
    $userTable->addCell($user['firstname'], "", NULL, NULL, NULL, '');
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
$linkAdd = '';
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
$linkAdd = $objLink->show();
// Show the heading
$activityobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$activityobjHeading->type = 3;
$activityobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $linkAdd;
// echo $activityobjHeading->show();
$activitylist = $this->objDbActivityList->getByItem($userId);
// Create a table object
$activityTable = &$this->newObject("htmltable", "htmlelements");
$activityTable->border = 0;
$activityTable->cellspacing = '3';
$activityTable->width = "100%";
// Add the table heading.
$activityTable->startRow();
$activityTable->addCell($activityobjHeading->show() , '', '', '', '', 'colspan="6"');
$activityTable->endRow();
$activityTable->startRow();
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . "</b>");
$activityTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
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
        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $activityTable->startRow();
        $activityTable->addCell($mycontextTitle, "", NULL, NULL, $class, '');
        $activityTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $activityTable->addCell($this->objDate->formatDate($item['start']) , "", NULL, NULL, $class, '');
        $activityTable->addCell($this->objDate->formatDate($item['finish']) , "", NULL, NULL, $class, '');
        $activityTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
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
/*
$hasEssays = 0;
foreach ($myContexts as $contextCode){
$contextEssay = $this->objEssayView->getStudentEssays($contextCode);
if(!empty($contextEssay)){
$hasEssays = 1;
$viewEssays = $this->viewAssessments->viewEssays($contextEssay);
$list=$this->objLanguage->languageText('word_list');
$head=$list.' '.$this->objLanguage->languageText('mod_essay_of','essay').' '.$this->objLanguage->languageText('mod_essay_essay','essay').' '.$this->objLanguage->languageText('word_for').' '.$contextCode." - ".$this->_objDBContext->getTitle($contextCode);

//echo "<b>".$head."</b>".$viewEssays;
$activityTable->startRow();
$activityTable->addCell("<b>".$head."</b>".$viewEssays , '', '', '', '', 'colspan="6"');
$activityTable->endRow();

}
}
*/
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
$linkAdd = '';
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
$affiliationlinkAdd = $objLink->show();
// Show the heading
$affiliationobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$affiliationobjHeading->type = 3;
$affiliationobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $affiliationlinkAdd;
//    echo $affiliationobjHeading->show();
$affiliationList = $this->objDbAffiliationList->getByItem($userId);
// Create a table object
$affiliationTable = &$this->newObject("htmltable", "htmlelements");
$affiliationTable->border = 0;
$affiliationTable->cellspacing = '12';
$affiliationTable->width = "100%";
// Add the table heading.
$affiliationTable->startRow();
$affiliationTable->addCell($affiliationobjHeading->show() , '', '', '', '', 'colspan="8"');
$affiliationTable->endRow();
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
    foreach($affiliationList as $affiliationItem) {
        // Display each field for addresses
        $cattype = $this->objDbCategorytypeList->listSingle($affiliationItem['type']);
        $affiliationTable->startRow();
        $affiliationTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($affiliationItem['classification'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($affiliationItem['role'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($affiliationItem['organisation'], "", NULL, NULL, $class, '');
        $affiliationTable->addCell($this->objDate->formatDate($affiliationItem['start']) , "", NULL, NULL, $class, '');
        $affiliationTable->addCell($this->objDate->formatDate($affiliationItem['finish']) , "", NULL, NULL, $class, '');
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
$addlink->link = $objLanguage->languageText("mod_eportfolio_addaffiliation", 'eportfolio');
$affiliationTable->startRow();
$affiliationTable->addCell($addlink->show() , '', '', '', '', 'colspan="8"');
$affiliationTable->endRow();
//View Affiliation
//View Transcript
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
    'action' => 'add_transcript'
)));
$objLink->link = $iconAdd->show();
$transcriptlinkAdd = $objLink->show();
// Show the heading
$transcriptobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$transcriptobjHeading->type = 3;
$transcriptobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $transcriptlinkAdd;
// echo $transcriptobjHeading->show();
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
$transcriptTable->addCell($transcriptobjHeading->show() , '', '', '', '', 'colspan="2"');
$transcriptTable->endRow();
$transcriptTable->startRow();
$transcriptTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$transcriptTable->endRow();
// Step through the list of addresses.
if (!empty($transcriptlist)) {
    foreach($transcriptlist as $item) {
        // Display each field for activities
        $transcriptTable->startRow();
        $transcriptTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
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
$qcllinkAdd = $objLink->show();
// Show the heading
$qclobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$qclobjHeading->type = 3;
$qclobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $qcllinkAdd;
//echo $qclobjHeading->show();
$qclList = $this->objDbQclList->getByItem($userId);
// Create a table object
$qclTable = &$this->newObject("htmltable", "htmlelements");
$qclTable->border = 0;
$qclTable->cellspacing = '3';
$qclTable->width = "100%";
// Add the table heading.
$qclTable->startRow();
$qclTable->addCell($qclobjHeading->show() , '', '', '', '', 'colspan="6"');
$qclTable->endRow();
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
        $qclTable->startRow();
        $qclTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['qcl_title'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['organisation'], "", NULL, NULL, $class, '');
        $qclTable->addCell($qclItem['qcl_level'], "", NULL, NULL, $class, '');
        $qclTable->addCell($this->objDate->formatDate($qclItem['award_date']) , "", NULL, NULL, $class, '');
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
            'action' => 'deleteqcl',
            'id' => $qclItem["id"]
        )) , $objLanguage->languageText('mod_eportfolio_suredelete', 'eportfolio'));
        //echo $objConfirm->show();
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
$linkAdd = '';
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
$goalslinkAdd = $objLink->show();
// Show the heading
$goalsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$goalsobjHeading->type = 3;
$goalsobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordGoals", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $goalslinkAdd;
//  echo $goalsobjHeading->show();
$goalsList = $this->objDbGoalsList->getByItem($userId);
// Create a table object
$goalsTable = &$this->newObject("htmltable", "htmlelements");
$goalsTable->border = 0;
$goalsTable->cellspacing = '12';
$goalsTable->width = "60%";
// Add the table heading.
$goalsTable->startRow();
$goalsTable->addCell($goalsobjHeading->show() , '', '', '', '', 'colspan="2"');
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
$linkAdd = '';
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
$competencylinkAdd = $objLink->show();
// Show the heading
$competencyobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$competencyobjHeading->type = 3;
$competencyobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $competencylinkAdd;
//echo $competencyobjHeading->show();
$competencyList = $this->objDbCompetencyList->getByItem($userId);
// Create a table object
$competencyTable = &$this->newObject("htmltable", "htmlelements");
$competencyTable->border = 0;
$competencyTable->cellspacing = '12';
$competencyTable->width = "100%";
// Add the table heading.
$competencyTable->startRow();
$competencyTable->addCell($competencyobjHeading->show() , '', '', '', '', 'colspan="4"');
$competencyTable->endRow();
$competencyTable->startRow();
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_qclawarddate", 'eportfolio') . "</b>");
$competencyTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$competencyTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($competencyList)) {
    foreach($competencyList as $item) {
        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $competencyTable->startRow();
        $competencyTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $competencyTable->addCell($this->objDate->formatDate($item['award_date']) , "", NULL, NULL, $class, '');
        $competencyTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
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
//    	echo $competencyTable->show();
//View Competency
//View Interest
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
    'action' => 'add_interest'
)));
$objLink->link = $iconAdd->show();
$interestlinkAdd = $objLink->show();
// Show the heading
$interestobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$interestobjHeading->type = 3;
$interestobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $interestlinkAdd;
//echo $interestobjHeading->show();
$interestList = $this->objDbInterestList->getByItem($userId);
// Create a table object
$interestTable = &$this->newObject("htmltable", "htmlelements");
$interestTable->border = 0;
$interestTable->cellspacing = '12';
$interestTable->width = "100%";
// Add the table heading.
$interestTable->startRow();
$interestTable->addCell($interestobjHeading->show() , '', '', '', '', 'colspan="4"');
$interestTable->endRow();
$interestTable->startRow();
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$interestTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$interestTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($interestList)) {
    foreach($interestList as $item) {
        // Display each field for activities
        $cattype = $this->objDbCategorytypeList->listSingle($item['type']);
        $interestTable->startRow();
        $interestTable->addCell($cattype[0]['type'], "", NULL, NULL, $class, '');
        $interestTable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
        $interestTable->addCell($item['shortdescription'], "", NULL, NULL, $class, '');
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
//echo $competencyTable->show();
//End View Interest
//View reflection
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
    'action' => 'add_reflection'
)));
$objLink->link = $iconAdd->show();
$reflectionlinkAdd = $objLink->show();
// Show the heading
$reflectionobjHeading = &$this->getObject('htmlheading', 'htmlelements');
$reflectionobjHeading->type = 3;
$reflectionobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $reflectionlinkAdd;
//echo $reflectionobjHeading->show();
$reflectionList = $this->objDbReflectionList->getByItem($userId);
// Create a table object
$reflectionTable = &$this->newObject("htmltable", "htmlelements");
$reflectionTable->border = 0;
$reflectionTable->cellspacing = '3';
$reflectionTable->width = "100%";
// Add the table heading.
$reflectionTable->startRow();
$reflectionTable->addCell($reflectionobjHeading->show() , '', '', '', '', 'colspan="4"');
$reflectionTable->endRow();
$reflectionTable->startRow();
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
$reflectionTable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
$reflectionTable->endRow();
// Step through the list of addresses.
$class = NULL;
if (!empty($reflectionList)) {
    foreach($reflectionList as $item) {
        // Display each field for activities
        $reflectionTable->startRow();
        $reflectionTable->addCell($item['rationale'], "", NULL, NULL, $class, '');
        $reflectionTable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
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
        $reflectionTable->addCell($objPopup->show() . $linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
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
$hasAccess = $this->_objUser->isContextLecturer();
$hasAccess = $this->_objUser->isAdmin();
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
    $assertionsobjLink->link = $iconAdd->show();
    //    $linkAdd = $objLink->show();
    // Show the heading
    $assertionsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
    $assertionsobjHeading->type = 3;
    $assertionsobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $assertionslinkAdd;
    //  echo $assertionsobjHeading->show();
    //    $Id = $this->_objGroupAdmin->getUserGroups($userPid);
    // Create a table object
    $assertionstable = &$this->newObject("htmltable", "htmlelements");
    $assertionstable->border = 0;
    $assertionstable->cellspacing = '3';
    $assertionstable->width = "100%";
    // Add the table heading.
    $assertionstable->startRow();
    $assertionstable->addCell($assertionsobjHeading->show() , '', '', '', '', 'colspan="5"');
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
                    $assertionstable->addCell($this->objDate->formatDate($assertionslist[0]['creation_date']) , "", NULL, NULL, $class, '');
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
        // Step through the list of addresses.
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
                        // Display each field for activities
                        $assertionstable->startRow();
                        $assertionstable->addCell($objUser->fullName($assertionslist[0]['userid']) , "", NULL, NULL, $class, '');
                        $assertionstable->addCell($assertionslist[0]['rationale'], "", NULL, NULL, $class, '');
                        $assertionstable->addCell($this->objDate->formatDate($assertionslist[0]['creation_date']) , "", NULL, NULL, $class, '');
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
    $assertionslinkAdd = $objLink->show();
    // Show the heading
    $assertionsobjHeading = &$this->getObject('htmlheading', 'htmlelements');
    $assertionsobjHeading->type = 3;
    $assertionsobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $assertionslinkAdd;
    // echo $assertionsobjHeading->show();
    $assertionslist = $this->objDbAssertionList->getByItem($userId);
    // Create a table object
    $assertionstable = &$this->newObject("htmltable", "htmlelements");
    $assertionstable->border = 0;
    $assertionstable->cellspacing = '3';
    $assertionstable->width = "100%";
    // Add the table heading.
    $assertionstable->startRow();
    $assertionstable->addCell($assertionsobjHeading->show() , '', '', '', '', 'colspan="5"');
    $assertionstable->endRow();
    $assertionstable->startRow();
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . "</b>");
    $assertionstable->addCell("<b>" . $objLanguage->languageText("mod_eportfolio_wordManage", 'eportfolio') . "</b>");
    $assertionstable->endRow();
    // Step through the list of addresses.
    $class = NULL;
    if (!empty($assertionslist)) {
        foreach($assertionslist as $item) {
            // Display each field for activities
            $assertionstable->startRow();
            $assertionstable->addCell($item['rationale'], "", NULL, NULL, $class, '');
            $assertionstable->addCell($this->objDate->formatDate($item['creation_date']) , "", NULL, NULL, $class, '');
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
            //echo $objConfirm->show();
            $assertionstable->addCell($linkstudManage . "<br> " . $linklecManage, "", NULL, NULL, $class, '');
            $assertionstable->addCell($linkEdit . $objConfirm->show() , "", NULL, NULL, $class, '');
            $assertionstable->endRow();
            //Check if assertion group exists and add in contextgroups
            $contextCode = $item["id"];
            $contextgrpList = $this->_objGroupAdmin->getLeafId(array(
                $contextCode,
                $groupName
            ));
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
$hasAccess = $this->_objUser->isContextLecturer();
$hasAccess = $this->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
if ($hasAccess) {
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
        'action' => 'add_category'
    )));
    $objLink->link = $iconAdd->show();
    $categorylinkAdd = $objLink->show();
    // Show the heading
    $categoryobjHeading = &$this->getObject('htmlheading', 'htmlelements');
    $categoryobjHeading->type = 3;
    $categoryobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordCategory", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $categorylinkAdd;
    //echo $categoryobjHeading->show();
    $categoryList = $this->objDbCategoryList->getByItem();
    // Create a table object
    $categorytable = &$this->newObject("htmltable", "htmlelements");
    $categorytable->border = 0;
    $categorytable->cellspacing = '12';
    $categorytable->width = "50%";
    // Add the table heading.
    $categorytable->startRow();
    $categorytable->addCell($categoryobjHeading->show() , '', '', '', '', 'colspan="2"');
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
$hasAccess = $this->_objUser->isContextLecturer();
$hasAccess = $this->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
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
    $categorytypelinkAdd = $objLink->show();
    // Show the heading
    $categorytypeobjHeading = &$this->getObject('htmlheading', 'htmlelements');
    $categorytypeobjHeading->type = 3;
    //Check for categories
    if (!empty($categoryList)) {
        $categorytypeobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordCategorytype", 'eportfolio') . '&nbsp;&nbsp;&nbsp;' . $categorytypelinkAdd;
    } else {
        $categorytypeobjHeading->str = $objLanguage->languageText("mod_eportfolio_wordCategorytype", 'eportfolio');
    }
    // echo $categorytypeobjHeading->show();
    $categoryList = $this->objDbCategorytypeList->getByItem();
    // Create a table object
    $categorytypetable = &$this->newObject("htmltable", "htmlelements");
    $categorytypetable->border = 0;
    $categorytypetable->cellspacing = '12';
    $categorytypetable->width = "50%";
    // Add the table heading.
    $categorytypetable->startRow();
    $categorytypetable->addCell($categorytypeobjHeading->show() , '', '', '', '', 'colspan="3"');
    $categorytypetable->endRow();
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
//Manage Users
//End Manage Users
//Information Title
$objinfoTitles->str = $objUser->getSurname() . $objLanguage->languageText("phrase_eportfolio_userinformation", 'eportfolio');
$this->objmainTab = $this->newObject('tabber', 'htmlelements');
$this->objTab = $this->newObject('tabber', 'htmlelements');
$namesLabel = $userTable->show();
$addressLabel = $addressTable->show();
$contactLabel = $contactTable->show();
$demographicsLabel = $demographicsTable->show();
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
$addressTab = $this->objTab->show();
//Information tab
//public function show($title = null, $content = null, $id = null, $hidden = 'default', $showToggle = TRUE)
$page.= $featureBox->show($objinfoTitles->show() , $addressTab, '1', 'default', TRUE);
$this->objmainTab->init();
$this->objmainTab->tabId = TRUE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordInformation", 'eportfolio') ,
    'content' => $page
));
//Activity Title
$objactivityTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_activitylist", 'eportfolio');
$activitypage.= $featureBox->show($objactivityTitles->show() , $activityLabel, '2', 'default', TRUE);
$this->objmainTab->tabId = TRUE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio') ,
    'content' => $activitypage
));
//Affiliation Title
$objaffiliationTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_affiliationheading", 'eportfolio');
$affiliationpage.= $featureBox->show($objaffiliationTitles->show() , $affiliationTable->show() , '3', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordAffiliation", 'eportfolio') ,
    'content' => $affiliationpage
));
//Transcript Title
$objtranscriptTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_transcriptlist", 'eportfolio');
$transcriptpage.= $featureBox->show($objtranscriptTitles->show() , $transcriptTable->show() , '4', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordTranscripts", 'eportfolio') ,
    'content' => $transcriptpage
));
//Qcl Title
$objqclTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_qclheading", 'eportfolio');
$qclpage.= $featureBox->show($objqclTitles->show() , $qclTable->show() , '5', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordQualification", 'eportfolio') ,
    'content' => $qclpage
));
//Goals Title
$objgoalsTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_goalList", 'eportfolio');
$goalspage.= $featureBox->show($objgoalsTitles->show() , $goalsTable->show() , '6', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $objLanguage->languageText("mod_eportfolio_goals", 'eportfolio') ,
    'content' => $goalspage
));
//Competency Title
$objcompetencyTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_competencylist", 'eportfolio');
$competencypage.= $featureBox->show($objcompetencyTitles->show() , $competencyTable->show() , '7', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordCompetency", 'eportfolio') ,
    'content' => $competencypage
));
//interest Title
$objinterestTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_interestList", 'eportfolio');
$interestpage.= $featureBox->show($objinterestTitles->show() , $interestTable->show() , '8', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordInterests", 'eportfolio') ,
    'content' => $interestpage
));
//reflection Title
$objreflectionTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_reflectionList", 'eportfolio');
$reflectionpage.= $featureBox->show($objreflectionTitles->show() , $reflectionTable->show() , '9', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordReflections", 'eportfolio') ,
    'content' => $reflectionpage
));
//assertions Title
$objassertionsTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_assertionList", 'eportfolio');
$assertionspage.= $featureBox->show($objassertionsTitles->show() , $assertionstable->show() , '10', 'default', TRUE);
$this->objmainTab->tabId = FALSE;
$this->objmainTab->addTab(array(
    'name' => $this->objLanguage->languageText("mod_eportfolio_wordAssertion", 'eportfolio') ,
    'content' => $assertionspage
));
//Category
if ($hasAccess) {
    //category Title
    $objcategoryTitles->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_categoryList", 'eportfolio');
    $categoryLabel = $categorytable->show();
    $categorytypeLabel = $categorytypetable->show();
    $this->objTab->init();
    $this->objTab->tabId = TRUE;
    $this->objTab->addTab(array(
        'name' => $this->objLanguage->languageText("mod_eportfolio_category", "eportfolio") ,
        'content' => $categoryLabel
    ));
    $this->objTab->tabId = TRUE;
    $this->objTab->addTab(array(
        'name' => $this->objLanguage->languageText("mod_eportfolio_categoryType", "eportfolio") ,
        'content' => $categorytypeLabel
    ));
    $categoryTab = $this->objTab->show();
    //category tab
    $categorypage.= $featureBox->show($objcategoryTitles->show() , $categoryTab, '11', 'default', TRUE);
    $this->objmainTab->tabId = FALSE;
    $this->objmainTab->addTab(array(
        'name' => $this->objLanguage->languageText("mod_eportfolio_wordCategory", 'eportfolio') ,
        'content' => $categorypage
    ));
}
$myeportfolioTab = $this->objmainTab->show();
$tabBox->addTab(array(
    'name' => $this->objLanguage->languageText("phrase_myePortfolio", 'eportfolio') ,
    'content' => $myeportfolioTab
) , 'winclassic-tab-style-sheet');
$tabBox->addTab(array(
    'name' => $this->objLanguage->languageText("phrase_othersePortfolio", 'eportfolio') ,
    'content' => $epTable->show()
) , 'winclassic-tab-style-sheet');
if (class_exists('groupops', false)) {
    $tabBox->addTab(array(
        'name' => $this->objLanguage->languageText("phrase_manage", 'eportfolio') ,
        'content' => $this->getEportfolioUsers()
    ) , 'winclassic-tab-style-sheet');
} else {
    $tabBox->addTab(array(
        'name' => $this->objLanguage->languageText("phrase_manage", 'eportfolio') ,
        'content' => $this->getEportfolioUsersOld()
    ) , 'winclassic-tab-style-sheet');
}
echo $tabBox->show();
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
