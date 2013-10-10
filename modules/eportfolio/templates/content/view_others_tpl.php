<?php

$this->loadClass('htmlheading', 'htmlelements');
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
    $myPid = $this->objUser->PKId($this->userId);
    $userId = $this->userId;
    //$myGroups = $this->_objGroupAdmin->getUserGroups($this->objUser->userId());
    //$myGroupsId = $this->_objGroupAdmin->getId($userId);
    $myGroupsId = $this->objEportfolioActivityStreamer->getAuthUserId($userId);
    $userGrps = $this->objEportfolioActivityStreamer->getUserGroups($myGroupsId);
    foreach ($userGrps as $thisGrp) {
        $grpName = $this->_objGroupAdmin->getName($thisGrp["group_id"]);

        $getOwner = explode("^", $grpName);
        //Check if grpName was concatenated by ^
        if (count($getOwner) == 2) {
            //Check if $getOwner[0] is a userId
            $nameOfOwner = $this->objUser->userName($getOwner[0]);
            if ($nameOfOwner != "Error: Data Not Found") {
                $ownerPkId = $this->objUser->PKId($getOwner[0]);
                $fullname = $this->objUserAdmin->getUserDetails($ownerPkId);
                //Select View
                $iconSelect = $this->getObject('geticon', 'htmlelements');
                $iconSelect->setIcon('view');
                $iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $fullname['firstname'] . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
                $mnglink = new link($this->uri(array(
                                    'module' => 'eportfolio',
                                    'action' => 'view_others_eportfolio',
                                    'id' => $thisGrp["group_id"],
                                    'ownerId' => $ownerPkId
                                )));
                $mnglink->link = $iconSelect->show();
                $linkManage = $mnglink->show();
                //Store Group id
                $textinput = new textinput("groupId", $ownerPkId);
                $epTable->startRow();
                $epTable->addCell($linkManage, '', '', 'left', '', '');
                $epTable->addCell($fullname['title'] . ' ' . $fullname['firstname'] . ' ' . $fullname['surname'], '', '', 'left', '', '');
                $epTable->addCell($getOwner[1], '', '', 'left', '', '');
                $epTable->endRow();
                $groupexists = 1;
            }
        }
    }
    if ($groupexists == 0) {
        $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
        $epTable->startRow();
        $epTable->addCell($notestsLabel, '', '', 'left', '', 'colspan="3"');
        $epTable->endRow();
    }
    /* //getUserDirectGroups
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
      foreach ($userGroupsArray as $userGroup) {
      $grpUsers = $this->objGroupsOps->getUsersInGroup($userGroup);
      foreach ($grpUsers as $grpUser) {
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
      foreach ($userSubGrps[0] as $key => $userSubGrp) {
      //echo "<br>userSubGrp<br>";
      //The fields to use in the select for getting group users
      $fields = array(
      'firstName',
      'surname',
      'tbl_users.id'
      );
      //Get the group users
      $membersList = $this->_objGroupAdmin->getGroupUsers($key, $fields);
      foreach ($membersList as $users) {
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
      foreach ($userGroupsArray as $usrSubGrp) {
      $parentGrp = $this->_objGroupAdmin->getParent($usrSubGrp);
      if ($myPid !== $parentGrp)
      $usrGrpOwner[$usrSubGrp] = $parentGrp;
      }
      }
      foreach ($buddiesPidListArr as $grpIdKey => $buddy) {
      //get the array key value
      $groupId = $grpIdKey;
      $filter = " WHERE id = '" . $groupId . "'";
      if ($buddy !== $myPid) {
      //    $fullname = $this->objUserAdmin->getUserDetails($ownerId);
      $fullname = $this->objUserAdmin->getUserDetails($buddy);
      if (!empty($fullname)) {
      //Get the buddys' sub groups
      $userSubGrps = $this->_objGroupAdmin->getSubgroups($groupId);
      foreach ($userSubGrps[0] as $key => $userSubGrp) {
      //The fields to use in the select for getting group users
      $fields = array(
      'firstName',
      'surname',
      'tbl_users.id'
      );
      //Get the group users
      $membersList = $this->_objGroupAdmin->getGroupUsers($key, $fields);
      foreach ($membersList as $users) {
      //Check if the logged in user is a user here, if true store userid and groupid
      if ($users['id'] == $this->userPid) {
      //Select View
      $iconSelect = $this->getObject('geticon', 'htmlelements');
      $iconSelect->setIcon('view');
      $iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $fullname['firstname'] . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
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
      $epTable->addCell($fullname['title'] . ' ' . $fullname['firstname'] . ' ' . $fullname['surname'], '', '', 'left', '', '');
      $epTable->addCell($userSubGrp['group_define_name'], '', '', 'left', '', '');
      $epTable->endRow();
      $groupexists = $groupexists + 1;
      }
      }
      }
      }
      }
      }
      } else {
      $allGrps = $this->_objGroupAdmin->getUserGroups($this->objUser->PKId($this->objUser->userId()));
      $myPid = $this->objUser->PKId($this->objUser->userId());
      foreach ($myGroups as $groupId) {
      $filter = " WHERE id = '$groupId'";
      $parentId = $this->_objGroupAdmin->getGroups($fields = array(
      "id",
      "name",
      "parent_id"
      ), $filter);
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
      $groupexists = $groupexists + 1;
      }
      }
      }
      }
      if ($groupexists == 0) {
      $notestsLabel = $this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio');
      $epTable->startRow();
      $epTable->addCell($notestsLabel, '', '', 'left', '', 'colspan="3"');
      $epTable->endRow();
      } */
} else {
    //Else if groupops not found, use old groupadmin
    //getUserDirectGroups
    $groupexists = 0;
    $myGroups = $this->_objGAModel->getUserGroups($this->objUser->PKId($this->objUser->userId()));
    $myPid = $this->objUser->PKId($this->objUser->userId());
    foreach ($myGroups as $groupId) {
        $filter = " WHERE id = '$groupId'";
        $parentId = $this->_objGroupAdmin->getGroups($fields = array(
                    "id",
                    "name",
                    "parent_id"
                        ), $filter);
        $myparentId = $parentId[0];
        $ownerId = $this->_objGAModel->getname($myparentId['parent_id']);
        if ($ownerId !== $myPid) {
            $fullname = $this->objUserAdmin->getUserDetails($ownerId);
            if (!empty($fullname)) {
                // Add row with user details.
                $groupname = $this->_objGAModel->getName($groupId);
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
                $groupexists = $groupexists + 1;
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
echo $epTable->show();
?>
