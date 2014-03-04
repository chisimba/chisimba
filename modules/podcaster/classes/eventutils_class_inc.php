<?php

/**
 * Class to provier reusable events management logic to the podcaster module
 *
 * This class takes functionality for viewing and creates reusable methods
 * based on it so that the code can be reused in different templates
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   podcaster
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright 2011 Wits and AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: viewer_class_inc.php 14266 2009-08-09 16:00:00Z davidwaf $
 * @link      http://chisimba.com
 */
// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global string $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Class for reusable events management logic
 * podcaster template
 *
 * @author Paul Mungai
 * @category Chisimba
 * @package podcaster
 * @copyright AVOIR
 * @licence GNU/GPL
 *
 */
class eventutils extends dbTable {

    /**
     *
     * @var $this->objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     *
     * @var $objUser String object property for holding the
     * user object
     * @access public
     *
     */
    public $objUser;
    /**
     *
     * @var $objConfig String object property for holding the
     * configuration object
     * @access public
     *
     */
    public $objConfig;
    /**
     * 
     * @var $_objGroupAdmin Object for GroupAdmin class
     * @access public
     */
    public $_objGroupAdmin;
    /**
     *
     * @var Object $_objManageGroups for the Class ManageGroups
     */
    public $_objManageGroups;
    /**
     *
     * @var object objGroupsOps for Group Ops class
     */
    public $objGroupsOps;
    /**
     *
     * @var Object for DB context
     */
    public $_objDBContext;
    /**
     *
     * @var Object for context users
     */
    public $objContextUsers;
    /**
     *
     * @var Object for DB dbpodcaster_events
     */
    public $objDBPodcasterEvents;
    /**
     *
     * @var Object for DB dbpodcaster_category
     */
    public $objDBPodcasterCategory;

    /**
     *
     * Standard init method
     *
     */
    public function init() {
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject("user", "security");
        // Instantiate the config object
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Instantiate the Context DB Object
        $this->_objDBContext = &$this->newObject('dbcontext', 'context');

        //$this->_objGAModel = $this->newObject('gamodel', 'groupadmin');
        $this->_objGroupAdmin = $this->newObject('groupadminmodel', 'groupadmin');
        $this->_objManageGroups = &$this->newObject('managegroups', 'contextgroups');
        $this->objContextUsers = $this->getObject('contextusers', 'contextgroups');
        $this->objMediaFileData = $this->getObject('dbmediafiledata');
        $this->objDBPodcasterEvents = $this->getObject('dbpodcaster_events');
        $this->objDBPodcasterCategory = $this->getObject('dbpodcaster_category');
        //TEMPORARY Check if class groupops exists
        if (file_exists($this->objConfig->getsiteRootPath() . "core_modules/groupadmin/classes/groupops_class_inc.php")) {
            $this->objGroupsOps = $this->getObject('groupops', 'groupadmin');
        }
        //$this->objGroupUsers = $this->getObject('groupusersdb', 'groupadmin');
    }

    /**
     * Method to remove a user from a event
     * @param string $userId User Id of the User
     * @param string $group Group to be deleted from - either lecturers, students or guest
     */
    public function removeUserFromEvent($userId = NULL, $groupId = NULL) {
        if ($userId == '') {
            return $this->nextAction(NULL, array(
                'message' => 'nouseridprovidedfordelete'
            ));
        }
        //$pkId = $this->objUser->PKId($userId);
        //Check if class groupops exists
        if (class_exists('groupops', false)) {
            $permid = $this->objGroupsOps->getUserByUserId($userId);
        }
        $pkId = $permid['perm_user_id'];
        $deleteMember = $this->_objGroupAdmin->deleteGroupUser($groupId, $pkId);
        return $deleteMember;
    }

    /**
     * Method to get the child id with a specified name
     */
    function getchildId($parentid, $groupName) {
        $thisgroupId = $this->_objGroupAdmin->getChildren($parentid);
        //Get the id for the child that corresponds to $groupName
        foreach ($thisgroupId as $item) {
            $mygroupName = $item['name'];
            if ($mygroupName == $groupName) {
                $groupId = $item['id'];
            }
        }
        return $groupId;
    }

    /**
     * Method to create more groups for a user
     * @param string The user id.
     * @param string The Title of a new context.
     */
    function addGroups($title) {
        // user Pk id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $usergroupId = $this->_objGroupAdmin->getId($userPid);
        // Add subgroup
        $newGroupId = $this->_objGroupAdmin->addGroup($title, $userPid . ' ' . $title, $usergroupId);
        // then add them as subGroups of the parent Group.
        $data = array(
            'group_id' => $usergroupId,
            'subgroup_id' => $newGroupId
        );
        $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
        // Add groupMembers
        //$this->addGroupMembers();
        $groupId = $this->_objGroupAdmin->addGroupUser($newGroupId, $this->objUser->userId());
        // Now create the ACLS
        $this->_objManageGroups->createAcls($userPid, $title);
        return $newGroupId;
    }

    /**
     * Method to get the user groups. Renders output in a table with manage links(Add/edit)
     * @return string
     */
    public function getUserGroups() {
        //load classes
        $this->objLanguage = $this->getObject('language', 'language');
        $icon = &$this->newObject('geticon', 'htmlelements');
        $table = &$this->newObject('htmltable', 'htmlelements');
        $linkstable = &$this->newObject('htmltable', 'htmlelements');
        $objGroups = &$this->newObject('managegroups', 'contextgroups');

        $table->width = '40%';
        $linkstable->width = '40%';
        $str = '';
        //Add Group Link
        $iconAdd = $this->getObject('geticon', 'htmlelements');
        $iconAdd->setIcon('add');
        $iconAdd->title = $this->objLanguage->languageText("mod_podcaster_addevent", 'podcaster', 'Add event');
        $iconAdd->alt = $this->objLanguage->languageText("mod_podcaster_addevent", 'podcaster', 'Add event');
        $addlink = new link($this->uri(array(
                            'module' => 'podcaster',
                            'action' => 'add_event'
                        )));
        $addlink->link = $iconAdd->show();
        $objLink = &$this->getObject('link', 'htmlelements');
        $objLink->link($this->uri(array(
                    'module' => 'podcaster',
                    'action' => 'add_event'
                )));
        $objLink->link = $iconAdd->show();
        $mylinkAdd = $objLink->show();
        $addlink->link = $this->objLanguage->languageText("mod_podcaster_addevent", 'podcaster', 'Add event');
        $linkAdd = $addlink->show();
        $linkstableRow = array(
            $linkAdd . ' ' . $mylinkAdd
        );
        $linkstable->addRow($linkstableRow);
        $wordEvent = $this->objLanguage->languageText("mod_podcaster_event", 'podcaster', 'Event');
        $wordCategory = $this->objLanguage->languageText("mod_podcaster_category", 'podcaster', 'Category');
        $wordAccess = $this->objLanguage->languageText("mod_podcaster_access", 'podcaster', 'Access');
        $wordPublished = $this->objLanguage->languageText("mod_podcaster_published", 'podcaster', 'Published') . "?";
        $wordManage = $this->objLanguage->languageText("mod_podcaster_manage", 'podcaster', 'Manage');
        //Add title
        $tableRow = array(
            "<b>" . $wordEvent . "</b> ", "<b>" . $wordCategory . "</b> ", "<b>" . $wordAccess . "</b> ", "<b>" . $wordPublished . "</b> ", "<b>" . $wordManage . "</b> "
        );
        $table->addRow($tableRow);
        //Get group members
        //Get group id
        $userPid = $this->objUser->PKId($this->objUser->userId());
        //get the descendents.
        if (class_exists('groupops')) {
            $usergroupId = $this->_objGroupAdmin->getId($userPid);
            $usersubgroups = $this->_objGroupAdmin->getSubgroups($usergroupId);

            //Check if empty
            if (!empty($usersubgroups)) {
                $subgroup = $usersubgroups[0];
                // The member list of this group
                $myGroupsData = array();
                foreach ($subgroup as $key => $myGrpId) {
                    $groupName = $this->_objGroupAdmin->getName($key);
                    $findme = "^^";
                    $pos = strpos($groupName, $findme);
                    if ($pos) {
                        $groupName = explode("^^", $groupName);
                        $groupName = $groupName[1];
                        $myGroupsData[] = array('groupId' => $key, 'groupName' => $groupName);
                    }
                }
            }
            /* $fields = array(
              'firstName',
              'surname',
              'tbl_users.id'
              ); */
            //Check if empty
            if (!empty($myGroupsData)) {
                foreach ($myGroupsData as $groupData) {
                    $groupId = $groupData['groupId'];
                    $groupName = $groupData['groupName'];
                    //Add Users
                    $iconManage = $this->getObject('geticon', 'htmlelements');
                    $iconManage->setIcon('add_icon');
                    $manageeventmembers = $this->objLanguage->languageText("mod_podcaster_manageeventmembers", 'podcaster', 'Manage event members');
                    $iconManage->alt = $manageeventmembers;
                    $iconManage->title = $manageeventmembers;
                    $mnglink = new link($this->uri(array(
                                        'module' => 'podcaster',
                                        'action' => 'viewevents',
                                        'id' => $groupId
                                    )));
                    $mnglink->link = $iconManage->show();
                    $linkManage = $mnglink->show();

                    //Edit Group Link
                    $iconEdit = $this->getObject('geticon', 'htmlelements');
                    $iconEdit->setIcon('edit');
                    $editEventLang = $this->objLanguage->languageText("mod_podcaster_editevent", 'podcaster', 'Edit event');
                    $iconEdit->title = $editEventLang;
                    $iconEdit->alt = $editEventLang;

                    $objLink = &$this->getObject('link', 'htmlelements');
                    $objLink->link($this->uri(array(
                                'module' => 'podcaster',
                                'action' => 'edit_event',
                                'id' => $groupId
                            )));
                    $objLink->link = $iconEdit->show();
                    $mylinkEdit = $objLink->show();

                    //Manage Events
                    $iconShare = $this->getObject('geticon', 'htmlelements');
                    $iconShare->setIcon('fileshare');
                    $addEventPods = $this->objLanguage->languageText("mod_podcaster_manageeventpodcasts", 'podcaster', 'Manage event podcasts');
                    $iconShare->alt = $addEventPods;
                    $iconShare->title = $addEventPods;
                    $mnglink = new link($this->uri(array(
                                        'module' => 'podcaster',
                                        'action' => 'manage_event',
                                        'id' => $groupId
                                    )));
                    $mnglink->link = $iconShare->show();
                    $linkMng = $mnglink->show();
                    //Get other group data
                    $eventData = $this->objDBPodcasterEvents->listByEvent($groupId);
                    $eventData = $eventData[0];

                    //Check if already published
                    $wordYes = $this->objLanguage->languageText("mod_podcaster_yes", 'podcaster', 'Yes');
                    $wordNo = $this->objLanguage->languageText("mod_podcaster_no", 'podcaster', 'No');
                    $publishStatus = ($eventData["publish_status"] == 'published' ? $wordYes : $wordNo);

                    $eventAccess = ucwords($eventData["access"]);
                    $categoryData = $this->objDBPodcasterCategory->listSingle($eventData['categoryid']);
                    $categoryName = $categoryData[0]['category'];
                    $tableRow = array(
                        $groupName, $categoryName, $eventAccess, $publishStatus, $linkManage . '  ' . $linkMng . " " . $mylinkEdit
                    );
                    $table->addRow($tableRow);
                }
                $str.= $table->show();
            }

            if (empty($str)) {
                $tableRow = array(
                    $this->objLanguage->languageText("mod_podcaster_noeventsfound", 'podcaster', 'No events found')
                );
                $table->addRow($tableRow);
                $str.= $table->show();
            }
        }
        return $str . $linkstable->show();
        unset($users);
    }

    /**
     * Method to get the user groups. Renders output in a table with manage links(Add/edit)
     * @return string
     */
    public function getUserEvents() {
        //load classes
        $this->objLanguage = $this->getObject('language', 'language');
        $icon = &$this->newObject('geticon', 'htmlelements');
        $table = &$this->newObject('htmltable', 'htmlelements');
        $linkstable = &$this->newObject('htmltable', 'htmlelements');
        $objGroups = &$this->newObject('managegroups', 'contextgroups');
        $table->width = '40%';
        $linkstable->width = '40%';
        $str = '';
        $wordEvent = $this->objLanguage->languageText("mod_podcaster_event", 'podcaster', 'Event');
        $wordCategory = $this->objLanguage->languageText("mod_podcaster_category", 'podcaster', 'Category');
        $wordAccess = $this->objLanguage->languageText("mod_podcaster_access", 'podcaster', 'Access');
        $wordOpen = $this->objLanguage->languageText("mod_podcaster_open", 'podcaster', 'Open');
        //Add title
        $tableRow = array(
            "<b>" . $wordEvent . "</b> ", "<b>" . $wordCategory . "</b> ", "<b>" . $wordAccess . "</b> ", "<b>" . $wordOpen . "</b> "
        );

        $table->addRow($tableRow);

        //Get group members
        $userId = $this->objUser->userId();
        //Get group id
        $userPid = $this->objUser->PKId($userId);
        //get the descendents.
        if (class_exists('groupops')) {
            //Get perm Id            
            $usrGroups = $this->getUserPermGroups($userId);

            if (!empty($usrGroups)) {
                $myGroupsData = array();
                //Retrieve the groupId's
                foreach ($usrGroups as $thisGroup) {
                    $groupId = $thisGroup['group_id'];
                    $groupName = $this->_objGroupAdmin->getName($groupId);
                    $findme = "^^";
                    $pos = strpos($groupName, $findme);
                    if ($pos) {
                        $groupName = explode("^^", $groupName);
                        $groupName = $groupName[1];
                        $myGroupsData[] = array('groupId' => $groupId, 'groupName' => $groupName);
                    }
                }
            }
            $fields = array(
                'firstName',
                'surname',
                'tbl_users.id'
            );
            //Check if empty
            if (!empty($myGroupsData)) {
                foreach ($myGroupsData as $groupData) {
                    $groupName = $groupData['groupName'];
                    $groupId = $groupData['groupId'];
                    //Add Users
                    $iconManage = $this->getObject('geticon', 'htmlelements');
                    $iconManage->setIcon('bookopen');
                    $iconManage->alt = $this->objLanguage->languageText("mod_podcaster_vieweventpodcasts", 'podcaster', 'View event podcasts');
                    $iconManage->title = $this->objLanguage->languageText("mod_podcaster_vieweventpodcasts", 'podcaster', 'View event podcasts');

                    $mnglink = new link($this->uri(array(
                                        'module' => 'podcaster',
                                        'action' => 'event_podcasts',
                                        'id' => $groupId
                                    )));

                    $mnglink->link = $iconManage->show();

                    $linkManageImg = $mnglink->show();

                    $mnglink = new link($this->uri(array(
                                        'module' => 'podcaster',
                                        'action' => 'event_podcasts',
                                        'id' => $groupId
                                    )));

                    $mnglink->link = $groupName;

                    $linkManageTxt = $mnglink->show();
                    //Get other group data
                    $eventData = $this->objDBPodcasterEvents->listByEvent($groupId);
                    $eventData = $eventData[0];

                    //Check if already published
                    $wordYes = $this->objLanguage->languageText("mod_podcaster_yes", 'podcaster', 'Yes');
                    $wordNo = $this->objLanguage->languageText("mod_podcaster_no", 'podcaster', 'No');
                    $publishStatus = ($eventData["publish_status"] == 'published' ? $wordYes : $wordNo);

                    $eventAccess = ucwords($eventData["access"]);
                    $categoryData = $this->objDBPodcasterCategory->listSingle($eventData['categoryid']);
                    $categoryName = $categoryData[0]['category'];
                    $tableRow = array(
                        $linkManageTxt, $categoryName, $eventAccess, $linkManageImg
                    );
                    $table->addRow($tableRow);
                } //end foreach
                $str.= $table->show();
            }
        }
        if (empty($str)) {
            $tableRow = array(
                $this->objLanguage->languageText("mod_podcaster_noeventsfound", 'podcaster', 'No events found')
            );
            $table->addRow($tableRow);
            $str.= $table->show();
        }
        return $str;
    }

    /**
     * Method to get the public/open groups. Renders output in a table
     * @param string $natureofevent Either public or open events
     * @return string
     */
    public function getOtherEvents($natureofevent='public') {
        //load classes
        $this->objLanguage = $this->getObject('language', 'language');
        $icon = &$this->newObject('geticon', 'htmlelements');
        $table = &$this->newObject('htmltable', 'htmlelements');
        $objGroups = &$this->newObject('managegroups', 'contextgroups');
        $table->width = '40%';
        $str = '';
        $wordEvent = $this->objLanguage->languageText("mod_podcaster_event", 'podcaster', 'Event');
        $wordCategory = $this->objLanguage->languageText("mod_podcaster_category", 'podcaster', 'Category');
        $wordAccess = $this->objLanguage->languageText("mod_podcaster_access", 'podcaster', 'Access');
        $wordOpen = $this->objLanguage->languageText("mod_podcaster_open", 'podcaster', 'Open');
        //Add title
        $tableRow = array(
            "<b>" . $wordEvent . "</b> ", "<b>" . $wordCategory . "</b> ", "<b>" . $wordAccess . "</b> ", "<b>" . $wordOpen . "</b> "
        );
        $table->addRow($tableRow);

        //Get user Id
        $userId = $this->objUser->userId();
        //If not logged in, default to public events
        if ($userId == Null) {
            $prevaction = 'publicevents';
            $natureofevent = 'public';
        } else {
            $prevaction = 'openevents';
        }
        //get the descendents.
        if (class_exists('groupops')) {
            //Get perm Id
            $otherGroups = $this->objDBPodcasterEvents->listByAccessPublishStatus($natureofevent, 'published');

            if (!empty($otherGroups)) {
                $myGroupsData = array();
                //Retrieve the groupId's
                foreach ($otherGroups as $thisGroup) {
                    $groupId = $thisGroup['eventid'];
                    $groupName = $this->_objGroupAdmin->getName($groupId);
                    $findme = "^^";
                    $pos = strpos($groupName, $findme);
                    if ($pos) {
                        $groupName = explode("^^", $groupName);
                        $groupName = $groupName[1];
                        $myGroupsData[] = array('groupId' => $groupId, 'groupName' => $groupName);
                    }
                }
            }
            $fields = array(
                'firstName',
                'surname',
                'tbl_users.id'
            );
            //Check if empty
            if (!empty($myGroupsData)) {
                foreach ($myGroupsData as $groupData) {
                    $groupName = $groupData['groupName'];
                    $groupId = $groupData['groupId'];
                    //Add Users
                    $iconManage = $this->getObject('geticon', 'htmlelements');
                    $iconManage->setIcon('bookopen');
                    $iconManage->alt = $this->objLanguage->languageText("mod_podcaster_vieweventpodcasts", 'podcaster', 'View event podcasts');
                    $iconManage->title = $this->objLanguage->languageText("mod_podcaster_vieweventpodcasts", 'podcaster', 'View event podcasts');

                    $mnglink = new link($this->uri(array(
                                        'module' => 'podcaster',
                                        'action' => 'event_podcasts',
                                        'id' => $groupId,
                                        'prevaction' => $prevaction
                                    )));

                    $mnglink->link = $iconManage->show();

                    $linkManageImg = $mnglink->show();

                    $mnglink->link = $groupName;

                    $linkManageTxt = $mnglink->show();

                    $tableRow = array(
                        $linkManageTxt . '  ' . $linkManageImg
                    );
                    //Get other group data
                    $eventData = $this->objDBPodcasterEvents->listByEvent($groupId);
                    $eventData = $eventData[0];

                    //Check if already published
                    $wordYes = $this->objLanguage->languageText("mod_podcaster_yes", 'podcaster', 'Yes');
                    $wordNo = $this->objLanguage->languageText("mod_podcaster_no", 'podcaster', 'No');
                    $publishStatus = ($eventData["publish_status"] == 'published' ? $wordYes : $wordNo);

                    $eventAccess = ucwords($eventData["access"]);
                    $categoryData = $this->objDBPodcasterCategory->listSingle($eventData['categoryid']);
                    $categoryName = $categoryData[0]['category'];
                    $tableRow = array(
                        $linkManageTxt, $categoryName, $eventAccess, $linkManageImg
                    );
                    $table->addRow($tableRow);
                    $str.= $table->show();
                } //end foreach
            }
        }
        if (empty($str)) {
            $tableRow = array(
                $this->objLanguage->languageText("mod_podcaster_noeventsfound", 'podcaster', 'No events found')
            );
            $table->addRow($tableRow);
            $str.= $table->show();
        }
        return $str;
    }

    /**
     * Method to Show the Results for a Search
     * @param int $page - Page of Results to show
     */
    public function getResults($page = 1) {
        $searchFor = $this->getSession('searchfor', '');
        $field = $this->getSession('field', 'firstName');
        //Ehb-added-begin
        $course = $this->getSession('course', 'course');
        $group = $this->getSession('group', 'group');
        //Ehb-added-End
        $order = $this->getSession('order', 'firstName');
        $numResults = $this->getSession('numresults', 20);
        $groupId = $this->getSession('groupId', "");
        $resultsArr = array();
        $resultsArr['searchfor'] = $searchFor;
        $resultsArr['field'] = $field;
        $resultsArr['order'] = $order;
        $resultsArr['numresults'] = $numResults;
        //Ehb-added-begin
        $resultsArr['course'] = $course;
        $resultsArr['group'] = $group;
        //Ehb-added-End
        // Prevent Corruption of Page Value - Negative Values
        if ($page < 1) {
            $page = 1;
        }
        $currentContextCode = $this->_objDBContext->getContextCode();
        $results = $this->objContextUsers->searchUsers($searchFor, $field, $order, $numResults, ($page - 1), $course, $group);
        $resultsArr['results'] = $results;
        $countResults = $this->objContextUsers->countResults();
        $resultsArr['countResults'] = $countResults;
        $resultsArr['page'] = $page;
        $paging = $this->objContextUsers->generatePaging($searchFor, $field, $order, $numResults, ($page - 1));
        $resultsArr['paging'] = $paging;
        $contextCode = $this->_objDBContext->getContextCode();
        $resultsArr['contextCode'] = $contextCode;
        //Ehb-added-begin
        $currentContextCode = $this->_objDBContext->getContextCode();
        $where = "where contextCode<>" . "'" . $currentContextCode . "'";
        $data = $this->_objDBContext->getAll($where);
        $resultsArr['data'] = $data;
        //Ehb-added-End
        // Get Users into Arrays
        $eventsArr = $this->prepareEventUsersArray();
        $resultsArr['guests'] = $eventsArr['guests'];
        $resultsArr['guestDetails'] = $eventsArr['guestDetails'];

        return $resultsArr;
    }

    /**
     * Method to Update User Roles
     */
    public function updateUserRoles() {

        $groupId = $this->getSession('groupId');

        //$changedItems = $this->getparam('changedItems');
        $existingMembers = $this->getparam('existingMembers');
        $existingMembers = explode(",", $existingMembers);
        $changedItems = $this->getparam('user');
        //$changedItems = array_unique($changedItems);

        $groups = $this->_objGroupAdmin->getTopLevelGroups();
        //Remove terminated members
        foreach ($existingMembers as $item) {
            //Check if class groupops exists
            if (class_exists('groupops', false)) {

                $permid = $this->objGroupsOps->getUserByUserId($item);
                $pkId = $permid['perm_user_id'];

                //Add if not-exists
                if (empty($changedItems)) {
                    $this->objGroupsOps->removeUser($groupId, $pkId);
                } else {
                    if (!in_array($item, $changedItems)) {
                        $this->objGroupsOps->removeUser($groupId, $pkId);
                    }
                }
            }
        }
        //Add new members
        foreach ($changedItems as $item) {
            //Check if class groupops exists
            if (class_exists('groupops', false)) {
                $permid = $this->objGroupsOps->getUserByUserId($item);
                $pkId = $permid['perm_user_id'];
                $this->_objGroupAdmin->addGroupUser($groupId, $pkId);
            }
        }
        return true;
    }

    //Function for managing the podcasts within an event

    public function manageEventPodcasts($selectedParts, $groupId) {
        if (empty($groupId))
            $groupId = $this->getSession('groupId', $groupId);

        foreach ($selectedParts as $partId) {
            //$thisId = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
            $partList = $this->_objGroupAdmin->getId($partId, $pkField = 'name');
            if (empty($partList)) {
                $partGroupsId = $this->_objGroupAdmin->addGroup($partId, $partId, $groupId);
                // then add them as subGroups of the parent Group.
                $data = array(
                    'group_id' => $groupId,
                    'subgroup_id' => $partGroupsId
                );
                $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
                //$newGroupId = $this->_objGroupAdmin->addGroupUser( $partGroupsId, $groupId );
                // Now create the ACLS
                //$this->_objManageGroups->createAcls($partGroupsId, $groupId);
            } else {
                $isSubGroup = $this->_objGroupAdmin->getSubgroups($groupId);
                $check = 0;
                if (!empty($isSubGroup)) {
                    foreach ($isSubGroup[0] as $subgrp) {
                        if ($partId == $subgrp['group_define_name']) {
                            $check = 1;
                        }
                    }
                }
                //If subgroup does not exist, create
                if ($check == 0) {
                    $data = array(
                        'group_id' => $groupId,
                        'subgroup_id' => $partList
                    );
                    $newSubGroupId = $this->objLuAdmin->perm->assignSubGroup($data);
                }
                /*
                  if (!$isGroupMember) {
                  $addGrpUser = $this->_objGroupAdmin->addGroupUser($groupId,$partList);
                  }
                 */
            }
        }
        return true;
    }

    /**
     * Function that deletes group podcasts
     * @param string $users
     * @param string $groupId
     */
    public function deleteGroupPodcasts($users, $groupId) {
        // Delete these members
        foreach ($users as $partId) {
            $this->_objGroupAdmin->deleteGroupUser($partId['group_id'], $groupId);
        }
        //Empty array
        $selectedParts = array();
        return true;
    }

    /**
     * Check if podcast is part of an event/group
     * @param string $partId
     * @param string $groupId
     * @return boolean
     */
    public function checkIfExists($partId, $parentId) {

        //Check if group exists
        $childId = $this->_objGroupAdmin->getId($partId);
        parent::init('tbl_perms_group_subgroups');
        $getPuid = $this->getAll("WHERE subgroup_id='" . $childId . "' AND group_id = '" . $parentId . "'");
        if (!empty($getPuid)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all the podcasts associated with this event
     * @param string $groupId
     * @param string $sort
     * @return array
     */
    public function getEventPodcasts($groupId, $sort) {
        //Get subgroups if any - subgroups are synonymous to the podcasts
        $subgroups = $this->_objGroupAdmin->getSubgroups($groupId);
        $eventPods = array();
        $idHolder = '';
        $podData = '';
        if (!empty($subgroups)) {
            foreach ($subgroups as $subgroup) {
                foreach ($subgroup as $grpData) {
                    $podId = $grpData['group_define_name'];
                    $idHolder .= "id='" . $podId . "' or ";
                }
            }
            $idHolder = rtrim($idHolder, 'or ');
            $podData = $this->objMediaFileData->getAllListedPodcasts($idHolder, $sort);
        }
        return $podData;
    }

    /**
     * Method to Prepare a List of Users in a Context sorted by lecturer, student, guest
     * The results are sent to the template
     */
    public function prepareEventUsersArray() {
        // Get Context Code
        $contextCode = $this->_objDBContext->getContextCode();
        $filter = " ORDER BY surname ";
        // Guests
        //$gid=$this->_objGroupAdmin->getLeafId(array($contextCode,'Guest'));
        $groupId = $this->getSession('groupId');
        $guests = $this->_objGroupAdmin->getGroupUsers($groupId, array(
                    'userid',
                    'firstName',
                    'surname',
                    'title',
                    'emailAddress',
                    'country',
                    'sex',
                    'staffnumber'
                        ), $filter);
        $guestsArray = array();
        if (count($guests) > 0) {
            foreach ($guests as $guest) {
                $guestsArray[] = $guest['userid'];
            }
        }
        $guestArr = array('guests' => $guestsArray, 'guestDetails' => $guests);
        return $guestArr;
    }

    /**
     * Function that deletes a child record in tbl_perms_group_subgroups
     * @param string $childId
     * @param string $parentId
     * @return true|false TRUE on success, FALSE on failure
     */
    public function deleteChildGroup($childId, $parentId) {
        parent::init('tbl_perms_group_subgroups');
        $getPuid = $this->getAll("WHERE subgroup_id='" . $childId . "' AND group_id = '" . $parentId . "'");
        //Deletes its affiliation with this parent
        if (!empty($getPuid)) {
            foreach ($getPuid as $thisPuid) {
                $this->delete("puid", $thisPuid["puid"]);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update group name
     * @param string $group_id The group Id
     * @param string $newName The New name
     */
    public function changeEventName($group_id, $newName) {
        parent::init('tbl_perms_groups');
        $userid = $this->objUser->userId();
        $newName = $userid . "^^" . $newName;
        $this->update("group_id", $group_id, array(
            'group_define_name' => $newName
        ));
    }

    /**
     * Update group name
     * @param string $userId The User Id
     */
    public function getUserPermGroups($userId) {
        parent::init('tbl_perms_groupusers');
        $usrPermId = $this->_objGroupAdmin->getPermUserId($userId);
        $usrGrps = $this->getAll('where perm_user_id="' . $usrPermId . '"');
        return $usrGrps;
    }
}

?>