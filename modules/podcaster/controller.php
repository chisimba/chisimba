<?php

/**
 * podcaster is used for sharing podcasts online.
 * Registered users can upload and manage thier podcasts
 * Supported file formats: mp3.
 * JODConverter is used primarly as the document converter engine, although
 * in same cases we are using swftools.
 *
 *  PHP version 5
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
 *
 * @copyright 2011 Free Software Innnovation Unit
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 * 
 */
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end of security

class podcaster extends controller {

    public $objConfig;
    public $realtimeManager;
    public $presentManager;
    public $objAnalyzeMediaFile;
    public $objMediaFileData;
    /**
     *
     * @var Object for DB context
     */
    public $_objDBContext;
    /**
     *
     * @var $_objGroupAdmin Object for GroupAdmin class
     * @access public
     */
    public $_objGroupAdmin;

    /**
     * Constructor
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objAnalyzeMediaFile = $this->getObject('analyzemediafile', 'filemanager');
        $this->objMediaFileData = $this->getObject('dbmediafiledata');
        $this->objFiles = $this->getObject('dbpodcasterfiles');
        $this->objTags = $this->getObject('dbpodcastertags');
        $this->objUtils = $this->getObject('userutils');
        $this->objUploads = $this->getObject('dbfileuploads');
        $this->documents = $this->getObject('dbdocuments');
        $this->objViewerUtils = $this->getObject('viewerutils');
        $this->objViewer = $this->getObject('viewer');
        $this->objSchedules = $this->getObject('dbpodcasterschedules');
        $this->realtimeManager = $this->getObject('realtimemanager');
        $this->folderPermissions = $this->getObject('dbfolderpermissions');
        $this->parse4RSS = $this->getObject('parse4rss', 'filters');
        $this->objEventUtils = $this->newObject('eventutils', 'podcaster');
        $this->objDbCategoryList = &$this->getObject('dbpodcaster_category', 'podcaster');
        $this->objDbEvents = &$this->getObject('dbpodcaster_events', 'podcaster');

        $this->objSearch = $this->getObject('indexdata', 'search');
        // user object
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->userPKId = $this->objUser->PKId($this->userId);
        //Instantiate the Context DB Object
        $this->_objDBContext = &$this->newObject('dbcontext', 'context');

        $this->_objGroupAdmin = $this->newObject('groupadminmodel', 'groupadmin');
        //Get system paths
        $this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');
        $this->siteBase = $this->objConfig->getitem('KEWL_SITEROOT_PATH');
        $this->siteUrl = $this->objConfig->getitem('KEWL_SITE_ROOT');
    }

    /**
     * Method to override login for certain actions
     * @param <type> $action
     * @return <type>
     */
    public function requiresLogin($action) {
        $required = array('openevents', 'myevents', 'addusers', 'configure_events', 'viewevents', 'manage_event', 'add_event', 'viewsearchresults', 'myuploads', 'doajaxsendmail', 'sendmail', 'describepodcast', 'login', 'steponeupload', 'upload', 'edit', 'updatedetails', 'tempiframe', 'erroriframe', 'uploadiframe', 'doajaxupload', 'ajaxuploadresults', 'delete', 'admindelete', 'deleteslide', 'deleteconfirm', 'regenerate', 'schedule', 'addfolder', 'removefolder', 'createfolder', 'folderexistscheck', 'renamefolder', 'deletetopic', 'deletefile', 'viewfolder', 'unpublishedpods');

        if (in_array($action, $required)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Standard Dispatch Function for Controller
     * @param <type> $action
     * @return <type>
     */
    public function dispatch($action) {

        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    function __home() {
        $tagCloud = $this->objTags->getTagCloud();
        $this->setVarByRef('tagCloud', $tagCloud);

        //$latestFiles = $this->objMediaFileData->getLatestAccessiblePodcasts();
        //$this->setVarByRef('latestFiles', $latestFiles);

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $hometpl = $objSysConfig->getValue('HOMETPL', 'podcaster');

        $permittedTypes = array('newhome', 'home');

        // Check that period is valid, if not, the default home install
        if (!in_array($hometpl, $permittedTypes)) {
            $period = 'home';
        }
        return $hometpl . '_tpl.php';
    }

    /**
     * Function that returns template for adding an event
     * @return template
     */
    function __add_event() {
        $mode = 'add';
        $this->setVarByRef('mode', $mode);
        $categoriesList = $this->objDbCategoryList->getAllCategories();
        $this->setVarByRef('categoriesList', $categoriesList);
        return "add_event_tpl.php";
    }

    /**
     * Function that returns edit event template
     * @return template
     */
    function __edit_event() {
        $mode = 'edit';
        $this->setVarByRef('mode', $mode);
        $eventId = $this->getParam('id', null);

        if (empty($eventId)) {
            return $this->nextAction('configure_events');
        }
        $categoriesList = $this->objDbCategoryList->getAllCategories();
        $this->setVarByRef('categoriesList', $categoriesList);
        $groupName = $this->_objGroupAdmin->getName($eventId);

        $groupName = explode("^^", $groupName);
        $groupName = $groupName['1'];
        $eventsData = $this->objDbEvents->listByEvent($eventId);
        $this->setVarByRef('eventId', $eventId);
        $this->setVarByRef('eventsData', $eventsData['0']);
        $this->setVarByRef('eventName', $groupName);
        return "add_event_tpl.php";
    }

    /**
     * Function that adds users to an event
     * @return template
     */
    function __addusers() {
        $resultsArr = $this->objEventUtils->updateUserRoles();
        return $this->nextAction('viewevents', array(
            'message' => 'usersupdated'
        ));
    }

    /**
     * Function that returns add category template
     * @return template
     */
    function __addcategory() {
        return "add_category_tpl.php";
    }

    /**
     * Function that returns edit category template
     * @return template
     */
    function __editcategory() {
        $id = $this->getParam('id', null);
        $this->setVarByRef('id', $id);
        $list = $this->objDbCategoryList->listSingle($id);
        $category = $list[0]['category'];
        $description = $list[0]['description'];
        $this->setVarByRef('category', $category);
        $this->setVarByRef('description', $description);
        return "edit_category_tpl.php";
    }

    /**
     * Function that returns edit category template
     * @return template
     */
    function __viewcategories() {
        return "view_category_tpl.php";
    }

    /**
     * Function that adds a category
     * @return template
     */
    function __deletecategory() {
        $this->nextAction($myid = $this->getParam('id', null), $this->objDbCategoryList->deleteSingle($myid));
        return $this->nextAction('viewcategories');
    }

    /**
     * Function that adds a category
     * @return template
     */
    function __addcategoryconfirm() {
        $id = $this->objDbCategoryList->insertSingle($this->getParam('category', NULL), $this->getParam('description', NULL));
        return $this->nextAction('viewcategories');
    }

    /**
     * Function that updates a category
     * @return template
     */
    function __editcategoryconfirm() {
        $myid = $this->getParam('id', null);
        $this->setVarByRef('id', $myid);
        $this->nextAction($this->objDbCategoryList->updateSingle($myid, $this->getParam('category', NULL), $this->getParam('description', NULL)));
        return $this->nextAction('viewcategories');
    }

    /**
     * Function that returns template for configuring an event
     * @return template
     */
    function __configure_events() {
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $this->setVarByRef('userPid', $this->userPid);
        return "manage_events_tpl.php";
    }

    /**
     * Function that returns template for configuring an event
     * @return template
     */
    function __myevents() {
        $userPid = $this->objUser->PKId($this->objUser->userId());
        $this->setVarByRef('userPid', $this->userPid);
        return "view_events_tpl.php";
    }
    /**
     * Function that returns template with public events
     * @return template
     */
    function __publicevents() {
        $this->setVarByRef('userId', $this->userId);
        $status = 'public';
        $this->setVarByRef('natureofevent', $status);
        return "other_events_tpl.php";
    }
    /**
     * Function that returns template with open events
     * @return template
     */
    function __openevents() {
        if($this->userId==Null){
            return $this->nextAction('home');
        }
        $this->setVarByRef('userId', $this->userId);
        $status = 'open';
        $this->setVarByRef('natureofevent', $status);
        return "other_events_tpl.php";
    }

    /**
     * Function that returns confirmation on add event
     * @return object
     */
    function __addeventconfirm() {
        if (class_exists('groupops', false)) {
            $event = $this->getParam('event', NULL);
            $categoryId = $this->getParam('category', NULL);
            $access = $this->getParam('access', NULL);
            $publish = $this->getParam('publish', NULL);
            $eventId = $this->objEventUtils->addGroups($this->userId . "^^" . $event);
            $this->objDbEvents->insertSingle($eventId, $categoryId, $access, $publish);
        }
        return $this->nextAction('configure_events');
    }

    /**
     * Function that returns confirmation on edit event
     * @return object
     */
    function __editeventconfirm() {
        if (class_exists('groupops', false)) {
            $eventId = $this->getParam('eventId', NULL);
            $categoryId = $this->getParam('category', NULL);
            $access = $this->getParam('access', NULL);
            $event = $this->getParam('event', NULL);
            $publish = $this->getParam('publish', NULL);
            $this->objDbEvents->updateSingle($eventId, $categoryId, $access, $publish);
            $this->objEventUtils->changeEventName($eventId, $event);
        }
        return $this->nextAction('configure_events');
    }

    /**
     * Function that returns template for viewing events
     * @return object
     */
    function __viewevents() {
        $this->setSession('showconfirmation', TRUE);
        $myId = $this->getParam('id', null);
        if (empty($myId))
            $myId = $this->getSession('groupId');
        $this->setSession('groupId', $myId);
        if (empty($myId)) {
            return $this->nextAction('configure_events');
        }
        return $this->eventsHome($myId);
    }

    /**
     * Method to show the list of users in an event
     * @param string $group
     * @return object
     */
    function eventsHome($group) {
        // Generate an array of users in the event, and send it to page template
        $guestArr = $this->objEventUtils->prepareEventUsersArray();
        // Send to Template
        $this->setVarByRef('guests', $guestsArr['guests']);
        $this->setVarByRef('guestDetails', $guestsArr['guestDetails']);
        // Default Values for Search
        $searchFor = $this->getSession('searchfor', '');
        $this->setVar('searchfor', $searchFor);
        $field = $this->getSession('field', 'firstName');
        $course = $this->getSession('course', 'course');
        //$group=$this->getSession('group','group');
        $this->setVar('field', $field);
        $this->setVar('course', $course);
        $this->setVar('group', $group);
        $confirmation = $this->getSession('showconfirmation', FALSE);
        $this->setVar('showconfirmation', $confirmation);
        //$this->setSession('showconfirmation', FALSE);
        //Ehb-added-begin
        $currentContextCode = $this->_objDBContext->getContextCode();
        $where = "where contextCode<>" . "'" . $currentContextCode . "'";
        $data = $this->_objDBContext->getAll($where);
        $this->setVarByRef('data', $data);
        //Ehb-added-End
        return 'eventhome_tpl.php';
    }

    /**
     * Function that returns template for adding podcasts to an event
     * @return object
     */
    function __manage_event() {
        $groupId = $this->getParam('id', null);
        $this->setVarByRef('groupId', $groupId);
        if (class_exists('groupops', true)) {
            $userId = $this->userId;
            $sort = $this->getParam('sort', 'dateuploaded_desc');

            $sort = $this->getParam('sort', 'datecreated_desc');

            // Check that sort options provided is valid
            if (!preg_match('/(datecreated|title|artist)_(asc|desc)/', strtolower($sort))) {
                $sort = 'datecreated_desc';
            }

            $files = $this->objMediaFileData->getAllAuthorPodcasts($userId, $sort);

            $this->setVarByRef('files', $files);
            $this->setVarByRef('sort', $sort);
            $this->setVarByRef('userId', $userId);

            return 'addmypodcaststoevent_tpl.php';
        } else {
            return $this->nextAction('configure_events');
        }
    }

    /**
     * Function that saves the new podcasts affiliated to a certain event
     * 
     */
    function __eventpodcastsmgr() {
        $groupId = $this->getParam('groupId', NULL);
        if (class_exists('groupops', true)) {
            $selectedParts = $this->getArrayParam('fileid');
            $this->setVarByRef('groupId', $groupId);
            //Get user Groups
            $subGroups = $this->_objGroupAdmin->getSubgroups($groupId);
            $subGroups = $subGroups[0];

            if (empty($selectedParts)) {
                $this->objEventUtils->deleteGroupPodcasts($userGroups, $groupId);
            } else {
                if (!empty($subGroups)) {
                    $newsubGroup = array();
                    foreach ($subGroups as $thisSub) {
                        $newsubGroup[] = $thisSub["group_define_name"];
                    }
                    // Get the added member ids
                    $addList = array_diff($selectedParts, $newsubGroup);
                    // Get the deleted member ids
                    $delList = array_diff($newsubGroup, $selectedParts);

                    // Delete these members
                    if (!empty($delList)) {
                        foreach ($delList as $partPid) {
                            $gpId = $this->_objGroupAdmin->getId($partPid);
                            $delChild = $this->objEventUtils->deleteChildGroup($gpId, $groupId);
                        }
                    }
                    // Add these members
                    if (count($addList) > 0) {
                        $this->objEventUtils->manageEventPodcasts($addList, $groupId);
                    }
                } else {
                    $addList = $selectedParts;
                    // Add these members
                    if (count($addList) > 0) {
                        $this->objEventUtils->manageEventPodcasts($addList, $groupId);
                    }
                }
                //Empty array
                $selectedParts = array();
            }
            return $this->nextAction('manage_event', array('id' => $groupId));
        }
        return $this->nextAction('manage_event', array('id' => $groupId));
    }

    /**
     * Function that returns template to search users
     * @return object
     */
    function __searchforusers() {
        return $this->searchForUsers();
    }

    /**
     * Method to search for Users
     * This function sets them as a session and then redirects to the results
     */
    public function searchForUsers() {
        $searchFor = $this->getParam('search');
        $this->setSession('searchfor', $searchFor);
        $field = $this->getParam('field');
        $this->setSession('field', $field);
        //Ehb-added-begin
        $course = $this->getParam('course');
        $this->setSession('course', $course);
        $group = $this->getParam('group');
        $this->setSession('group', $group);
        //Ehb-added-End
        $order = $this->getParam('order');
        $this->setSession('order', $order);
        $numResults = $this->getParam('results');
        $this->setSession('numresults', $numResults);
        return $this->nextAction('viewsearchresults');
    }

    /**
     * Function that returns the search results
     * @return object
     */
    function __viewsearchresults() {
        $groupId = $this->getSession('groupId', "");
        $resultsArr = $this->objEventUtils->getResults($this->getParam('page', 1));
        $this->setVarByRef('groupId', $groupId);
        if (empty($groupId)) {
            return $this->nextAction('configure_events');
        }
        $this->setVarByRef('resultsArr', $resultsArr);
        return 'searchresults_tpl.php';
    }

    /**
     * Function that removes a user from an event
     * @return object
     */
    function __removeuser() {
        $group = $this->getParam('group', '');
        $userId = $this->getParam('userid', '');
        $actDelete = $this->objEventUtils->removeUserFromEvent($userId, $group);
        return $this->nextAction('viewevents', array(
            'message' => 'userdeletedfromgroup'
        ));
    }

    /**
     * Function that removes a batch of users from an event
     * @return object
     */
    function __batchremoveusers() {
        $users = $userId = $this->getParam('user', '');
        $usersCount = count($users);
        if (is_array($users)) {
            foreach ($users as $userId) {
                if (!empty($userId)) {
                    $group = $this->getParam('group', '');
                    $actDelete = $this->objEventUtils->removeUserFromEvent($userId, $group);
                }
                return $this->nextAction('viewevents', array(
                    'message' => 'userdeletedfromgroup'
                ));
            }
        } else {
            return $this->nextAction('viewevents', array(
                'message' => 'nouseridprovidedfordelete'
            ));
        }
    }

    /**
     * function that loads create folder form
     *
     * @return form
     */
    public function __steponeupload() {
        $defaultexists = $this->objUtils->defaultFolderExistsCheck();
        //Create only if new
        if (!$defaultexists) {
            $this->objUtils->createDefaultFolder();
        }

        $createcheck = $this->getParam('createcheck', 'new');
        $dir = $this->getParam("folder", "");
        if (empty($dir)) {
            $successmsg = Null;
            $dir = $this->objUser->userId();
            $this->setVarByRef('successmsg', $successmsg);
        } else {
            if ($createcheck == "add") {
                $successmsg = $dir . " " . $this->objLanguage->languageText('mod_podcaster_createsuccess', 'podcaster', "was created successfully");
                $this->setVarByRef('successmsg', $successmsg);
            } else if ($createcheck == "fail") {
                if ($dir == "/") {
                    $successmsg = $this->objLanguage->languageText('mod_podcaster_enterfoldername', 'podcaster', "You need to type in a meaningful folder name before submitting");
                    $this->setVarByRef('successmsg', $successmsg);
                } else {
                    $successmsg = $dir . " " . $this->objLanguage->languageText('mod_podcaster_createfail', 'podcaster', "was not created successfully. A corresponding folder already exists");
                    $this->setVarByRef('successmsg', $successmsg);
                }
            }
        }

        $this->setVarByRef("dir", $dir);
        $this->setVarByRef("mode", $this->mode);
        $selected = $this->baseDir . $dir;
        $selected = str_replace("//", "/", $selected);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("successmsg", $successmsg);
        return "steponeupload_tpl.php";
    }

    /**
     * function that loads create folder form
     *
     * @return form
     */
    public function __addfolder() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");

        $defaultexists = $this->objUtils->defaultFolderExistsCheck();
        //Create only if new
        if (!$defaultexists) {
            $this->objUtils->createDefaultFolder();
        }

        $createcheck = $this->getParam('createcheck', 'new');
        $dir = $this->getParam("folder", "");
        if (empty($dir)) {
            $successmsg = Null;
            $dir = $this->objUser->userId();
            $this->setVarByRef('successmsg', $successmsg);
        } else {
            if ($createcheck == "add") {
                $successmsg = $dir . " " . $this->objLanguage->languageText('mod_podcaster_createsuccess', 'podcaster', "was created successfully");
                $this->setVarByRef('successmsg', $successmsg);
            } else if ($createcheck == "fail") {
                if ($dir == "/") {
                    $successmsg = $this->objLanguage->languageText('mod_podcaster_enterfoldername', 'podcaster', "You need to type in a meaningful folder name before submitting");
                    $this->setVarByRef('successmsg', $successmsg);
                } else {
                    $successmsg = $dir . " " . $this->objLanguage->languageText('mod_podcaster_createfail', 'podcaster', "was not created successfully. A corresponding folder already exists");
                    $this->setVarByRef('successmsg', $successmsg);
                }
            }
        }

        $this->setVarByRef("dir", $dir);
        $this->setVarByRef("mode", $this->mode);
        $selected = $this->baseDir . $dir;
        $selected = str_replace("//", "/", $selected);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("successmsg", $successmsg);
        return "createfolder_tpl.php";
    }

    /**
     * Function that gets the default folder for the user
     * @param <type> $dir
     * @return <type>
     */
    function __getdefaultfolder($dir) {
        $handle = opendir($dir);
        $files = array();
        while (($file = readdir($handle)) !== false) {

            if ($file == '.' || $file == '..') {
                continue;
            }
            $filepath = $dir == '.' ? $file : $dir . '/' . $file;
            if (is_link($filepath))
                continue;
            if (is_dir($filepath)) {
                $cfile = substr($filepath, strlen($dir));
                if ($this->folderPermissions->isValidFolder($cfile)) {
                    $files[] = $filepath;
                }
            }
        }
        closedir($handle);
        sort($files, SORT_LOCALE_STRING);

        return $files;
    }

    /**
     * function that loads delete folder form
     *
     * @return form
     */
    public function __removefolder() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $dir = $this->getParam("folder", "");
        $this->setVarByRef("mode", $this->mode);
        $selected = $this->baseDir . $dir;
        $message = $this->getParam('message', '');
        $this->setVarByRef("mode", $this->mode);
        $this->setVarByRef("message", $message);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("successmsg", $message);
        return "deletefolder_tpl.php";
    }

    /**
     * used to create a new folder in a selected dir. If none is provided, the folder is
     * created in the root dir
     * @return array
     */
    public function __createfolder2() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $path = $this->getParam('parentfolder');
        if ($path == '0')
            $path = '/';
        $name = $this->getParam('foldername');

        $userId = $this->objUser->userId();
        $pathf = "/";
        //We need to remove the userId from the path
        if ($path != '/') {
            $remUId = split("/", $path);
            $count = count($remUId);
            $start = 0;
            do {
                if ($userId != $remUId[$start] && $remUId[$start] != "") {
                    $pathf .= $remUId[$start];
                    if (($start + 1) != $count)
                        $pathf .= "/";
                }
                $start++;
            }while ($start < $count);
            $path = $pathf;
        }
        if (empty($name) || $name == "/") {
            $flag = "selected";
            $folderdata = $this->folderPermissions->getPermmissions($pathf);
            $folderid = $folderdata[0]['id'];
            //return $this->nextAction('upload', array('createcheck' => $flag, 'folderid' => $folderid, 'path' => $path));
            return $this->nextAction('upload', array('createcheck' => $flag, 'folderid' => $folderid, 'path' => $path));
        }
        if (!$path) {
            $path = "";
        }

        $flag = "";

        $defaultexists = $this->objUtils->defaultFolderExistsCheck();
        //Create only if new
        if (!$defaultexists) {
            $this->objUtils->createDefaultFolder();
        }
        //Confirm that folder does not exist
        $exists = $this->objUtils->folderExistsCheck($path, $name);
        //Create only if new
        if (!$exists) {
            $path = $pathf;
            $this->objUtils->createFolder($path, $name);
            $path = $path . "/" . $name;
            $flag = 'add';
        } else {
            $path = $path . "/" . $name;
            $flag = 'fail';
        }
        $path = str_replace("//", "/", $path);
        $folderdata = $this->folderPermissions->getPermmissions($path);

        $folderid = $folderdata[0]['id'];
        $this->setVarByRef('folder', $name);
        $this->setVarByRef('path', $path);
        $this->nextAction('sendmail', array('createcheck' => $flag, 'folderid' => $folderid, 'path' => $path));
    }

    /**
     * function that loads "send email after new folder creation" form
     *
     * @return form
     */
    public function __sendmail() {
        $folderid = $this->getParam("folderid", "");
        $createcheck = $this->getParam('createcheck', '');
        $path = $this->getParam('path', '');
        $useremail = $this->objUser->email($this->userId);
        $this->setVarByRef("mode", $this->mode);
        $this->setVarByRef("createcheck", $createcheck);
        $this->setVarByRef("path", $path);
        $this->setVarByRef("useremail", $useremail);
        $this->setVarByRef("folderid", $folderid);
        return "addemails_tpl.php";
    }

    /**
     * function that loads "send email after new folder creation" form
     *
     * @return form
     */
    public function __doajaxsendmail() {
        //$this->setLayoutTemplate("podcaster_layout_tpl.php");
        $generatedid = $this->getParam('id', '');
        $useremail = $this->getParam('useremail', '');
        $folderid = $this->getParam("folderid", "");
        $createcheck = $this->getParam('createcheck', '');
        $path = $this->getParam('path', '');
        //Validate useremail
        $useremail = str_replace(" ", "", $useremail);
        //Confirm that each is an email before sending
        $useremails = explode(",", $useremail);
        $useremail = $this->objViewerUtils->validateEmails($useremails);
        $user = $this->objUser->getUserDetails($this->userId);
        if (empty($useremail))
            $useremail = $user['emailaddress'];

        //Add RSS Link
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('rss');

        $rssLink = new link($this->uri(array('action' => 'viewfolderfeed', 'id' => $folderid)));
        $rssLink->link = $objIcon->show();
        $rssLink = $rssLink->show();

        //End debug
        // Then bang off a mail to the user.
        $siteName = $this->objConfig->getSiteName();
        $siteEmail = $this->objConfig->getsiteEmail();
        $message = $this->objLanguage->languageText("mod_podcaster_dearsirmadam", "podcaster", "Dear Sir/Madam") . '<br />
<br />
' . $this->objLanguage->languageText("mod_podcaster_emailtext", "podcaster", "On [[DATE]], a podcast folder was created on the [[SITENAME]] website. Your can get updates of new podcasts in this folder via the following RSS feed") . ': ' . $rssLink . '<br />
<br />
' . $this->objLanguage->languageText("mod_podcaster_rss", "podcaster", "RSS") . ': [[RSS]]<br /><br />
The folder was created by [[FIRSTNAME]] [[SURNAME]]. You can contact them through this email address [[EMAIL]].
<br /><br />
Sincerely,<br />
[[SITENAME]]<br />
[[SITEADDRESS]]
<br />';

        $message = str_replace('[[FIRSTNAME]]', $user['firstname'], $message);
        $message = str_replace('[[SURNAME]]', $user['surname'], $message);
        $message = str_replace('[[USERNAME]]', str_replace(" ", '', $user['username']), $message);
        $message = str_replace('[[EMAIL]]', $user['emailaddress'], $message);
        $message = str_replace('[[SITENAME]]', $siteName, $message);
        $message = str_replace('[[RSS]]', $path, $message);
        $message = str_replace('[[SITEADDRESS]]', $this->objConfig->getsiteRoot(), $message);
        $message = str_replace('[[DATE]]', date('l dS \of F Y h:i:s A'), $message);


        $myFile = "testFile.txt";
        $fh = fopen($myFile, 'w') or die("can't open file");
        $stringData = $useremail . "\n";
        fwrite($fh, $stringData);
        $stringData = $message . "\n";
        fwrite($fh, $stringData);
        fclose($fh);

        $objMailer = $this->getObject('mailer', 'mail');
        $objMailer->setValue('to', array($useremail));
        $objMailer->setValue('from', $siteEmail);
        $objMailer->setValue('fromName', $siteName . ' Registration System');
        $objMailer->setValue('subject', 'Podcast folder: ' . $siteName);
        $objMailer->setValue('body', strip_tags($message));
        $objMailer->setValue('AltBody', strip_tags($message));

        if ($objMailer->send()) {
            $sendstatus = "send_success";
        } else {
            $sendstatus = "send_fail";
        }
        $this->setVarByRef("mode", $this->mode);
        $this->setVarByRef("createcheck", $createcheck);
        $this->setVarByRef("path", $path);
        $flag = 'add';
        $this->nextAction('ajaxsendmailresults', array('id' => $generatedid, 'emails' => $useremail, 'createcheck' => $flag, 'folderid' => $folderid, 'path' => $path, 'sendstatus' => $sendstatus));
    }

    /**
     * Used to push through send mail results for AJAX
     */
    function __ajaxsendmailresults() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $emails = $this->getParam('emails');
        $this->setVarByRef('emails', $emails);

        $path = $this->getParam('path');
        $this->setVarByRef('path', $path);

        $folderid = $this->getParam('folderid');
        $this->setVarByRef('folderid', $folderid);

        $sendstatus = $this->getParam('sendstatus');
        $this->setVarByRef('sendstatus', $sendstatus);

        $createcheck = $this->getParam('createcheck');
        $this->setVarByRef('createcheck', $createcheck);

        $generatedid = $this->getParam('id');
        $this->setVarByRef('id', $generatedid);
        return 'ajaxsendmailresults_tpl.php';
    }

    /**
     * used to create a new folder in a selected dir. If none is provided, the folder is
     * created in the root dir
     * @return <type>
     */
    public function __createfolder() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $path = $this->getParam('parentfolder');
        $name = $this->getParam('foldername');

        if (!$path) {
            $path = "";
        }
        $flag = "";
        $defaultexists = $this->objUtils->defaultFolderExistsCheck();
        //Create only if new
        if (!$defaultexists) {
            $this->objUtils->createDefaultFolder();
            $flag = 'add';
        }
        //Confirm that folder does not exist
        $exists = $this->objUtils->folderExistsCheck($path, $name);
        //Create only if new
        if (!$exists) {
            $userId = $this->objUser->userId();
            //We need to remove the userId from the path
            $remUId = split("/", $path);
            $pathf = "/";
            $count = count($remUId);
            $start = 0;
            do {
                if ($userId != $remUId[$start] && $remUId[$start] != "") {
                    $pathf .= $remUId[$start];
                    if (($start + 1) != $count)
                        $pathf .= "/";
                }
                $start++;
            }while ($start < $count);
            $path = $pathf;
            $this->objUtils->createFolder($path, $name);
            $flag = 'add';
        } else {
            $flag = 'fail';
        }
        $this->setVarByRef('folder', $name);
        $this->nextAction('addfolder', array('createcheck' => $flag, 'folder' => $name));
    }

    /**
     * used to check if a folder exists in the selected dir.
     *
     * @return boolean
     */
    public function __folderExistsCheck() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $path = $this->getParam('parentfolder');
        $name = $this->getParam('foldername');

        if (!$path) {
            $path = "";
        }

        $exists = $this->objUtils->folderExistsCheck($path, $name);
        if ($exists) {
            echo 'exists';
        } else {
            echo 'create';
        }
    }

    /**
     * renames the supplied folder
     * @return <type>
     */
    public function __renamefolder() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $res = $this->objUtils->renameFolder($this->getParam('folderpath'), $this->getParam('foldername'));
        return $this->nextAction('home', array("result" => $res));
    }

    /*
     * Method to delete folder/topic
     *
     */

    public function __deletetopic() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        //Get the folder
        $folder = $this->getParam("parentfolder", "");
        $userId = $this->objUser->userId();
        if ($folder == '0')
            $folder = '/';
        $path = $folder;
        $userId = $this->objUser->userId();
        //We need to remove the userId from the path
        $remUId = split("/", $path);
        $pathf = "/";
        $count = count($remUId);
        $start = 0;
        do {
            if ($userId != $remUId[$start] && $remUId[$start] != "") {
                $pathf .= $remUId[$start];
                if (($start + 1) != $count)
                    $pathf .= "/";
            }
            $start++;
        }while ($start < $count);
        $path = $pathf;
        $folder = $path;
        $folderpermserror = '<strong class="confirm">' . $this->objLanguage->languageText('mod_podcaster_deletefolderpermserror', 'podcaster', "You do not have permissions to delete this folder") . '</strong>';
        if (!empty($folder) || $folder == "/") {
            //Check if user is authorised to delete
            $isowner = $this->folderPermissions->permissionExists($userId, $folder);
        } else {
            return $this->nextAction('removefolder', array('message' => $folderpermserror, 'folder' => $folder));
        }
        $deletesuccess = '<strong class="confirm">' . $this->objLanguage->languageText('mod_podcaster_deletesuccess', 'podcaster', "was deleted successfully") . '</strong>';
        if (!$isowner) {
            return $this->nextAction('removefolder', array('message' => $folderpermserror, 'folder' => $folder));
        }

        //Check if folder has podcasts
        $checkfolderdocs = $this->objUploads->getAllNodeFiles($folder);

        $foldernotempty = '<strong class="confirm">' . $this->objLanguage->languageText('mod_podcaster_shortdeleteallinfoldermessage', 'podcaster', "Kindly delete both approved and un-approved podcasts in this folder before deleting it") . '</strong>';
        //Ask user to delete the contents of the folder first, else delete the topic if empty
        if (count($checkfolderdocs) >= 1) {
            return $this->nextAction('removefolder', array('message' => $foldernotempty, 'folder' => $folder));
        } else {
            //Delete the topic
            $this->folderPermissions->removePermission($userId, $folder);

            return $this->nextAction('removefolder', array('message' => '<strong id="confirm">' . $folder . "</strong> " . $deletesuccess, 'folder' => '/'));
        }

        if (strstr($result, "success")) {
            $this->nextAction('removefolder');
        } else {
            return $this->nextAction('removefolder', array('message' => $result));
        }
    }

    /**
     * deletes the selected file
     * @return array
     */
    public function __deletefile() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $userid = $this->objUser->userId();
        $id = $this->getParam('id');
        $fileRes = $this->objUtils->deleteFile($userid, $id);
        $result = "";

        if ($fileRes == 1) {
            $this->objUploads->deleteFileRecord($id);
        } else {
            $result = $this->objLanguage->languageText("mod_podcaster_deleteerror", 'podcaster', 'Folder could not be deleted. Note: You need to be the owner of this folder and also, the folder needs to be empty to delete');
        }
        return $this->nextAction('home', array("result" => "$result"));
    }

    /**
     * function that renders a folder and its associated documents
     *
     * @return form
     */
    public function __viewfolder() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        //Set show rows
        $rows = $this->pageSize;
        $start = $this->getParam("start", 0);
        //Select records Limit array
        $limit = array();
        $limit['start'] = $start;
        $limit['rows'] = $rows;
        //Get the rowcount
        $rowcount = $this->getParam("rowcount", Null);

        $rejecteddocuments = $this->documents->getdocuments($this->mode, 'N', "Y", $limit, $rowcount);

        $dir = $this->getParam("folder", "");
        $mode = $this->getParam("mode", "");
        $message = $this->getParam("message", "");


        $objPreviewFolder = $this->getObject('previewfolder');

        $selected = "";
        $selected = $dir;

        $basedir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
        if ($dir == $basedir) {
            $selected = "";
        }
        $rowcount = $this->getParam("rowcount", Null);
        $this->setVarByRef("start", $start);
        $this->setVarByRef("rows", $rows);
        $files = $this->objUtils->getFiles($dir, $limit, $rowcount);
        $this->setVarByRef("files", $files);
        $this->setVarByRef("dir", $dir);
        $this->setVarByRef("documents", $documents);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("message", $message);
        $this->setVarByRef("rejecteddocuments", $rejecteddocuments);
        $selected = $this->baseDir . $selected;
        $this->setVarByRef("selected", $selected);
        return "viewfolder_tpl.php";
    }

    /*
     * Function that returns unpublished podcasts
     */

    public function __unpublishedpods() {
        $this->setLayoutTemplate("podcaster_layout_tpl.php");
        $selected = "unapproved";

        //Set show rows
        $rows = $this->pageSize;
        $start = $this->getParam("start", 0);

        //Select records Limit array
        $limit = array();
        $limit['start'] = $start;
        $limit['rows'] = $rows;

        //Get the rowcount
        $rowcount = $this->getParam("rowcount", Null);

        $tobeeditedfoldername = $this->getParam("tobeeditedfoldername", Null);
        $attachmentStatus = $this->getParam("attachmentStatus", Null);
        $documents = $this->documents->getdocuments($this->mode, 'N', "N", $limit, $rowcount);
        $this->setVarByRef("start", $start);
        $this->setVarByRef("rows", $rows);
        $this->setVarByRef("tobeeditedfoldername", $tobeeditedfoldername);
        $this->setVarByRef("documents", $documents);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("mode", $this->mode);
        $this->setVarByRef("attachmentStatus", $attachmentStatus);
        return "unpublishedpodcasts_tpl.php";
    }

    /**
     * function to test whether target machine has java well installed
     */
    public function __willappletrun() {

        $actiontype = $this->getParam('actiontype');
        $id = $this->getParam('id');
        $agenda = $this->getParam('agenda');

        $this->setVarByRef('appletaction', $actiontype);
        $this->setVarByRef('id', $id);

        $this->setVarByRef('agenda', $agenda);

        return "willappletrun_tpl.php";
    }

    /**
     * This calls function that displays actual applet after veryifying that java exists
     * The applet is invoked in presenter mode
     *  @return <type>
     */
    public function __showpresenterapplet() {
        return $this->showapplet('true');
    }

    /**
     * Calls function to display applet, but in participant mode
     * @return <type>
     */
    public function __showaudienceapplet() {
        return $this->showapplet('false');
    }

    /**
     * Displays actual applet by returning the template responsible for this
     * @param <type> $isPresenter
     * @return <type>
     */
    private function showapplet($isPresenter) {

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $supernodeHost = $objSysConfig->getValue('SUPERNODE_HOST', 'realtime');
        $supernodePort = $objSysConfig->getValue('SUPERNODE_PORT', 'realtime');

        $this->setVarByRef('supernodeHost', $supernodeHost);
        $this->setVarByRef('supernodePort', $supernodePort);

        $slideServerId = $this->realtimeManager->randomString(32); //'gen19Srv8Nme50';
        $this->realtimeManager->startSlidesServer($slideServerId);

        $id = $this->getParam('id');
        $title = $this->getParam('agenda');

        $filePath = $this->objConfig->getContentBasePath() . '/podcaster/' . $id;
        $this->setVarByRef('filePath', $filePath);
        $this->setVarByRef('sessionTitle', $title);

        $this->setVarByRef('sessionid', $id);
        $this->setVarByRef('slideServerId', $slideServerId);
        $this->setVarByRef('isPresenter', $isPresenter);

        // $this->setVar('pageSuppressBanner', TRUE);
        // $this->setVar('suppressFooter', TRUE);

        return "showapplet_tpl.php";
    }

    /**
     * displayes error
     */
    public function __showerror() {
        $title = $this->getParam('title');
        $content = $this->getParam('content');
        $content.='<br><a href="http://java.com">Download here</a>';

        $desc = $this->getParam('desc');

        $this->setVarByRef('title', $title);
        $this->setVarByRef('content', $content);
        $this->setVarByRef('desc', $desc);

        return "dump_tpl.php";
    }

    /**
     * Method to display the search results
     */
    public function __search() {
        $query = $this->getParam('q');

        $this->setVarByRef('query', $query);

        return 'search_tpl.php';
    }

    /**
     * Method to edit the details of a presentation
     *
     */
    function __edit() {
        $id = $this->getParam('id');

        $file = $this->objFiles->getFile($id);

        if ($file == FALSE) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }

        $tags = $this->objTags->getTags($id);

        $this->setVarByRef('file', $file);
        $this->setVarByRef('tags', $tags);

        $mode = $this->getParam('mode', 'window');
        $this->setVarByRef('mode', $mode);

        if ($mode == 'submodal') {
            $this->setVar('pageSuppressBanner', TRUE);
            $this->setVar('suppressFooter', TRUE);
        }

        return 'process_tpl.php';
    }

    /**
     * Method to update the details of a presentation
     *
     */
    function __updatedetails() {
        $id = $this->getParam('id');
        $title = $this->getParam('title');
        $description = $this->getParam('description');
        $tags = explode(',', $this->getParam('tags'));
        $newTags = array();

        // Create an Array to store problems
        $problems = array();
        // Check that username is available
        if ($title == '') {
            $problems[] = 'emptytitle';
            $title = $id;
        }
        //
        // Clean up Spaces
        foreach ($tags as $tag) {
            $newTags[] = trim($tag);
        }

        $tags = array_unique($newTags);
        $license = $this->getParam('creativecommons');

        $this->objFiles->updateFileDetails($id, $title, $description, $license);
        $this->objTags->addTags($id, $tags);

        $file = $this->objFiles->getFile($id);
        $tags = $this->objTags->getTagsAsArray($id);
        $slides = $this->objSlides->getSlides($id);

        $file['tags'] = $tags;
        $file['slides'] = $slides;

        $this->_prepareDataForSearch($file);

        if (count($problems) > 0) {
            $this->setVar('mode', 'addfixup');
            $this->setVarByRef('problems', $problems);
            return 'process_tpl.php';
        } else {
            return $this->nextAction('view', array('id' => $id, 'message' => 'infoupdated'));
        }
    }

    /**
     * Method to view the details of a presentation
     *
     */
    function __view() {
        $id = $this->getParam('id');

        $filedata = $this->objMediaFileData->getFile($id);

        if (empty($filedata)) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }

        $tags = $this->objTags->getTags($filedata['id']);

        $getPodcast = $this->objViewerUtils->getPodcastView($id);

        $this->setVarByRef('file', $getPodcast);
        $this->setVarByRef('id', $id);
        $this->setVarByRef('tags', $tags);

        $this->setVar('pageTitle', $this->objConfig->getSiteName() . ' - ' . $filedata['title']);

        $objViewCounter = $this->getObject('dbpodcasterviewcounter');

        $objViewCounter->addView($id);

        return 'view_tpl.php';
    }

    /**
     * Method to generate the podcast feed
     *
     */
    function __viewpodfeed() {
        $id = $this->getParam('id');

        $filedata = $this->objMediaFileData->getFile($id);

        if (empty($filedata)) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }
        //Generate RSS Feed
        $title = $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        $description = $this->objConfig->getSiteName() . ' ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        ;
        $url = $this->uri(array('action' => 'latestrssfeed'));
        return $this->objViewer->generatePodcastFeed($title, $description, $url, $filedata);
    }

    /**
     * Method to generate the podcast author feed
     *
     */
    function __addauthorfeed() {
        $author = $this->getParam('author');
        return $this->objViewer->getUserFeed($author);
    }

    /**
     * Method to generate the podcast RSS by userId
     *
     */
    function __addownfeed() {
        $userId = $this->getParam('userId');
        return $this->objViewer->getOwnFeed($userId);
    }

    /**
     * Method to generate the folder feeds -- shows the pods within it
     *
     */
    function __viewfolderfeed() {
        $folderId = $this->getParam('id');
        return $this->objViewer->getFolderFeed($folderId);
    }

    /**
     * Method to generate the podcast feed
     *
     */
    function __getlatestfeeds() {
        //Generate RSS Feed
        return $this->objViewer->getLatestFeed();
    }

    /**
     * Method to get the flash file
     */
    function __getflash() {
        $id = $this->getParam('id');

        $fileExists = $this->objFiles->onlyCheckpodcasterVersion2($id);

        if ($fileExists) {
            // Return New version
            $redirect = $this->objConfig->getcontentPath() . 'podcaster/' . $id . '/' . $id . '_v2.swf';
        } else {
            // Return Old version
            $redirect = $this->objConfig->getcontentPath() . 'podcaster/' . $id . '/' . $id . '.swf';
        }

        header('Location:' . $redirect);
    }

    /**
     * Method to download a presentation
     */
    function __download() {
        $id = $this->getParam('id');
        $result = $this->objMediaFileData->getFile($id);
        if (!empty($result)) {
            //Get the path
            $pathdata = $this->folderPermissions->getById($result['uploadpathid']);
            $pathdata = $pathdata[0];
            $newpodpath = str_replace($this->siteBase, "/", $this->baseDir);
            $newpodpath = $newpodpath . "/" . $result['creatorid'] . "/" . $pathdata['folderpath'] . '/' . $result['filename'];
            $newpodpath = str_replace("//", "/", $newpodpath);
            $fileurl = $newpodpath;
            //Remove / at the start of path
            $fileurl = ltrim($fileurl, '/');
            $fileurl = str_replace("//", "/", $fileurl);

            $objDownloadCounter = $this->getObject('dbpodcasterdownloadcounter');
            $objDownloadCounter->addDownload($id, $result['format']);
            header('Location:' . $fileurl);
        } else {
            return $this->nextAction(NULL, array('error' => 'cannotfindfile'));
        }
    }

    /**
     * Method to download a presentation
     */
    function __viewembed() {
        $id = $this->getParam('id');
        $result = $this->objMediaFileData->getFile($id);
        if (!empty($result)) {
            //Get the path
            $pathdata = $this->folderPermissions->getById($result['uploadpathid']);
            $pathdata = $pathdata[0];
            $newpodpath = str_replace($this->siteBase, "/", $this->baseDir);
            $newpodpath = $newpodpath . "/" . $result['creatorid'] . "/" . $pathdata['folderpath'] . '/' . $result['filename'];
            $newpodpath = str_replace("//", "/", $newpodpath);
            $fileurl = $newpodpath;
            //Remove / at the start of path
            $fileurl = ltrim($fileurl, '/');
            $fileurl = str_replace("//", "/", $fileurl);
            $fileurl = $this->siteUrl . $fileurl;
            return $fileurl;
        } else {
            return $this->nextAction(NULL, array('error' => 'cannotfindfile'));
        }
    }
    /**
     * Method to view a list of podcasts that match a particular tag
     *
     */
    function __tag() {
        $tag = $this->getParam('tag');
        $sort = $this->getParam('sort', 'datecreated_desc');

        // Check that sort options provided is valid
        if (!preg_match('/(datecreated|title|creatorname)_(asc|desc)/', strtolower($sort))) {
            $sort = 'datecreated_desc';
        }

        if (trim($tag) != '') {
            $tagCounter = $this->getObject('dbpodcastertagviewcounter');
            $tagCounter->addView($tag);
        }

        $files = $this->objTags->getFilesWithTag($tag, $sort);

        $this->setVarByRef('tag', $tag);
        $this->setVarByRef('files', $files);
        $this->setVarByRef('sort', $sort);

        return 'tag_tpl.php';
    }

    /**
     * Used to view a list of podcasts uploaded by a particular user
     *
     */
    function __event_podcasts() {
        $userId = $this->userId;

        $groupId = $this->getParam('id', '');
        $prevaction = $this->getParam('prevaction', '');
        //Get group data
        $groupData = $this->objDbEvents->listByEvent($groupId);
        $access = $groupData[0]["access"];

        if (empty($groupData)) {
            return $this->nextAction('home');
        } else {
            //Check permissions
            if (empty($userId) && $access != "public") {
                //If not logged in, is event public?
                return $this->nextAction('home');
            } elseif ($access == "private") {
                $isGrpMember = $this->_objGroupAdmin->isGroupMember($userId, $groupId);
                if (!$isGrpMember) {
                    return $this->nextAction('home');
                }
            }
            //If access is open proceed as anyone logged in can view it
        }

        if (empty($groupId)) {
            return $this->nextAction('myevents');
        }

        $sort = $this->getParam('sort', 'dateuploaded_desc');

        $sort = $this->getParam('sort', 'datecreated_desc');

        // Check that sort options provided is valid
        if (!preg_match('/(datecreated|title|artist)_(asc|desc)/', strtolower($sort))) {
            $sort = 'datecreated_desc';
        }

        $files = $this->objEventUtils->getEventPodcasts($groupId, $sort);

        $this->setVarByRef('files', $files);
        $this->setVarByRef('sort', $sort);
        $this->setVarByRef('prevaction', $prevaction);
        $this->setVarByRef('userId', $userId);
        $this->setVarByRef('groupId', $groupId);

        return 'event_podcasts_tpl.php';
    }

    /**
     * Used to view a list of podcasts uploaded by a particular user
     *
     */
    function __myuploads() {
        $userId = $this->userId;
        $sort = $this->getParam('sort', 'dateuploaded_desc');

        $sort = $this->getParam('sort', 'datecreated_desc');

        // Check that sort options provided is valid
        if (!preg_match('/(datecreated|title|artist)_(asc|desc)/', strtolower($sort))) {
            $sort = 'datecreated_desc';
        }

        $files = $this->objMediaFileData->getAllAuthorPodcasts($userId, $sort);

        $this->setVarByRef('files', $files);
        $this->setVarByRef('sort', $sort);
        $this->setVarByRef('userId', $userId);

        return 'byuser_tpl.php';
    }

    /**
     * Used to view a list of podcasts uploaded by a particular user
     *
     */
    function __byuser() {
        $userid = $this->getParam('userid');
        $sort = $this->getParam('sort', 'dateuploaded_desc');

        // Check that sort options provided is valid
        if (!preg_match('/(dateuploaded|title)_(asc|desc)/', strtolower($sort))) {
            $sort = 'dateuploaded_desc';
        }

        $files = $this->objFiles->getByUser($userid, str_replace('_', ' ', $sort));

        $this->setVarByRef('userid', $userid);
        $this->setVarByRef('files', $files);
        $this->setVarByRef('sort', $sort);

        return 'byuser_tpl.php';
    }

    /**
     * Used to show a tag cloud for all tags
     */
    function __tagcloud() {
        $tagCloud = $this->objTags->getCompleteTagCloud();
        $this->setVarByRef('tagCloud', $tagCloud);

        return 'tagcloud_tpl.php';
    }

    /**
     * Ajax method to return statistics from another period/source
     */
    function __ajaxgetstats() {
        $period = $this->getParam('period');
        $type = $this->getParam('type');

        switch ($type) {
            case 'downloads':
                $objSource = $this->getObject('dbpodcasterdownloadcounter');
                break;
            case 'tags':
                $objSource = $this->getObject('dbpodcastertagviewcounter');
                break;
            case 'uploads':
                $objSource = $this->getObject('dbpodcasteruploadscounter');
                break;
            default:
                $objSource = $this->getObject('dbpodcasterviewcounter');
                break;
        }

        echo $objSource->getAjaxData($period);
    }

    /**
     * Used to show interface to upload a presentation
     *
     */
    function __upload() {
        $createcheck = $this->getParam('createcheck');
        $folder = $this->getParam('folder');
        $folderid = $this->getParam('folderid');
        $folderdata = $this->folderPermissions->getById($folderid);
        $folderdata = $folderdata[0];
        $this->setVarByRef('createcheck', $createcheck);
        $this->setVarByRef('folderdata', $folderdata);
        return 'testupload_tpl.php';
    }

    /**
     * Used to show a temporary iframe
     * (it is hidden, and thus does nothing)
     *
     */
    function __tempiframe() {
        echo '<pre>';
        print_r($_GET);
    }

    /**
     * Used to show upload errors
     *
     */
    function __erroriframe() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $message = $this->getParam('message');
        $this->setVarByRef('message', $message);

        return 'erroriframe_tpl.php';
    }

    /**
     * Used to show upload results if the upload was successful
     *
     */
    function __uploadiframe() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        return 'uploadiframe.php';
    }

    /**
     * Ajax Process to display form for user to add presentation info
     *
     */
    function __ajaxprocess() {
        $this->setPageTemplate(NULL);

        $id = $this->getParam('id');

        $file = $this->objFiles->getFile($id);

        if ($file == FALSE) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }

        // Set Filename as title in this process
        // Based on the filename, it might make it easier for users to complete the name
        $file['title'] = $file['filename'];

        $tags = $this->objTags->getTags($id);

        $this->setVarByRef('file', $file);
        $this->setVarByRef('tags', $tags);

        $this->setVar('mode', 'add');

        return 'process_tpl.php';
    }

    /**
     * Method to display the error messages/problems in the user registration
     * @param string $problem Problem Code
     * @return string Explanation of Problem
     */
    protected function explainProblemsInfo($problem) {
        switch ($problem) {
            case 'emptytitle':
                return 'Title of Presentation Required';
        }
    }

    /**
     * function that loads the edit podcast details form
     *
     * @return form
     */
    public function __describepodcast() {
        $fileid = $this->getParam("fileid", "");
        $filedata = $this->objMediaFileData->getFileByFileId($fileid);
        $this->setVarByRef("filedata", $filedata);
        return "tpl_addeditpodcast.php";
    }

    /**
     * function that saves the podcast details form
     *
     * @return form
     */
    public function __savedescribepodcast() {
        $id = $this->getParam("id", "");
        $fileid = $this->getParam("fileid", "");
        $podtitle = $this->getParam("podtitle", "");
        $cclicense = $this->getParam("creativecommons", "");
        $artist = $this->getParam("artist", "");
        $description = $this->getParam("description", "");
        $accesslevel = $this->getParam("access", "");
        $publishstatus = $this->getParam("publishstatus", "");
        $tags = $this->getParam("tags", "");
        $tags = explode(",", $tags);
        $this->objTags->addTags($id, $tags);

        $filedata = $this->objMediaFileData->updateFileDetails($id, $podtitle, $description, $cclicense, $artist, $accesslevel, $publishstatus);
        $this->setVarByRef("filedata", $filedata);
        return $this->nextAction('view', array('id' => $id, 'fileid' => $fileid));
    }

    /**
     * Used to do the actual upload
     *
     */
    function __doajaxupload() {
        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');
        $pathid = $this->getParam('pathid');
        $folderdata = $this->folderPermissions->getById($pathid);
        $folderdata = $folderdata[0];
        $id = $this->objFiles->autoCreateTitle();
        $path = $folderdata['folderpath'];
        $destinationDir = $this->baseDir . "/" . $this->userId . "/" . $path . "/";
        $destinationDir = str_replace("//", "/", $destinationDir);

        $fullPath = "/" . $this->userId . "/" . $path . "/";
        $fullPath = str_replace("//", "/", $fullPath);

        @chmod($destinationDir, 0777);

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array('mp3');
        $objUpload->overWrite = TRUE;
        $objUpload->setUploadFolder($destinationDir . '/');

        $result = $objUpload->doUpload(TRUE);

        if ($result['success'] == FALSE) {

            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';

            return $this->nextAction('erroriframe', array('message' => $result['message'], 'file' => $filename, 'pathid' => $pathid));
        } else {

            //var_dump($result);

            $filename = $result['filename'];
            $mimetype = $result['mimetype'];

            $path_parts = $result['storedname'];

            $ext = $path_parts['extension'];


            $file = $destinationDir . $filename;

            if ($ext == '1') {
                $rename = $destinationDir . $filename;

                rename($file, $rename);

                //$filename = $path_parts['filename'] . '.mp3';
            }

            if (is_file($file)) {
                @chmod($file, 0777);
            }
            // Check if File Exists
            if (file_exists($file)) {
                // Get Media Info
                $fileInfo = $this->objAnalyzeMediaFile->analyzeFile($file);

                // Add Information to Databse
                $this->objMediaFileData->addMediaFileInfo($id, $fileInfo[0], $filename, $folderdata['id']);
            }

            //$this->objFiles->updateReadyForConversion($id, $filename, $mimetype);

            $uploadedFiles = $this->getSession('uploadedfiles', array());
            $uploadedFiles[] = $id;
            $this->setSession('uploadedfiles', $uploadedFiles);

            return $this->nextAction('ajaxuploadresults', array('id' => $generatedid, 'fileid' => $id, 'filename' => $filename, 'pathid' => $pathid));
        }
    }

    /**
     * Used to push through upload results for AJAX
     */
    function __ajaxuploadresults() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $fileid = $this->getParam('fileid');
        $this->setVarByRef('fileid', $fileid);

        $filename = $this->getParam('filename');
        $this->setVarByRef('filename', $filename);

        $pathid = $this->getParam('pathid');
        $this->setVarByRef('pathid', $pathid);

        return 'ajaxuploadresults_tpl.php';
    }

    /**
     * Used to Start the Conversions of Files
     *
     * This method is called using an Ajax process and is then
     * run as a background process, so that it continues, even
     * if the user closes the browser, or moves away.
     */
    function __ajaxprocessconversions() {
        $objBackground = $this->newObject('background', 'utilities');

        //check the users connection status,
        //only needs to be done once, then it becomes internal
        $status = $objBackground->isUserConn();

        //keep the user connection alive, even if browser is closed!
        $callback = $objBackground->keepAlive();

        $result = $this->objFiles->convertFiles();

        $call2 = $objBackground->setCallback("john.doe@tohir.co.za", "Your Script", "The really long running process that you requested is complete!");

        echo $result;
    }

    /**
     * Used to delete a presentation
     * Check: Users can only upload their own podcasts
     */
    function __delete() {
        $id = $this->getParam('id');

        $file = $this->objFiles->getFile($id);

        if ($file == FALSE) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }

        if ($file['creatorid'] != $this->objUser->userId()) {
            return $this->nextAction('view', array('id' => $id, 'error' => 'cannotdeleteslidesofothers'));
        }

        return $this->_deleteslide($file);
    }

    /**
     * Used when an administrator deletes the file of another person
     */
    function __admindelete() {
        $id = $this->getParam('id');

        $file = $this->objFiles->getFile($id);

        if ($file == FALSE) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }

        return $this->_deleteslide($file);
    }

    /**
     * Used to display the delete form interface
     * This method is called once it is verified the user can delete the presentation
     *
     * @access private
     */
    private function _deleteslide($file) {
        $this->setVarByRef('file', $file);

        $randNum = rand(0, 500000);
        $this->setSession('delete_' . $file['id'], $randNum);

        $this->setVar('randNum', $randNum);

        $mode = $this->getParam('mode', 'window');
        $this->setVarByRef('mode', $mode);

        if ($mode == 'submodal') {
            $this->setVar('pageSuppressBanner', TRUE);
            $this->setVar('suppressFooter', TRUE);
        }


        return 'delete_tpl.php';
    }

    /**
     * Used to delete a presentation if user confirms delete
     *
     */
    private function __deleteconfirm() {
        // Get Id
        $id = $this->getParam('id');

        // Get Value
        $deletevalue = $this->getParam('deletevalue');

        // Get File
        $file = $this->objFiles->getFile($id);

        // Check that File Exists
        if ($file == FALSE) {
            return $this->nextAction('home', array('error' => 'norecord'));
        }

        // Check that user is owner of file, or is admin -> then delete
        if ($file['creatorid'] == $this->objUser->userId() || $this->isValid('admindelete')) {
            if ($deletevalue == $this->getSession('delete_' . $id) && $this->getParam('confirm') == 'yes') {
                $this->objFiles->deleteFile($id);
                $this->objSearch->removeIndex('podcaster_' . $id);
                return $this->nextAction(NULL);
            } else {
                return $this->nextAction('view', array('id' => $id, 'message' => 'deletecancelled'));
            }

            // Else User cannot delete files of others
        } else {
            return $this->nextAction('view', array('id' => $id, 'error' => 'cannotdeleteslidesofothers'));
        }
    }

    /**
     * Used to display the latest podcasts RSS Feed
     *
     */
    function __latestrssfeed() {
        $objViewer = $this->getObject('viewer');
        echo $objViewer->getLatestFeed();
    }

    /**
     * Used to show a RSS Feed of podcasts matching a tag
     *
     */
    function __tagrss() {
        $tag = $this->getParam('tag');
        $objViewer = $this->getObject('viewer');
        echo $objViewer->getTagFeed($tag);
    }

    /**
     * Used to display the latest podcasts of a user RSS Feed
     *
     */
    public function __userrss() {
        $userid = $this->getParam('userid');
        $objViewer = $this->getObject('viewer');
        echo $objViewer->getUserFeed($userid);
    }

    /**
     * Used to display the latest podcasts of an event as an RSS Feed
     *
     */
    public function __eventrss() {
        $groupId = $this->getParam('groupId');
        $objViewer = $this->getObject('viewer');
        echo $objViewer->getEventFeed($groupId);
    }

    /**
     * Get own RSS Feed
     *
     */
    public function __ownrss() {
        $userid = $this->getParam('userid');
        $objViewer = $this->getObject('viewer');
        echo $objViewer->getUserFeed($userid);
    }

    /**
     * Used to rebuild the search index
     */
    public function __rebuildsearch() {
        $files = $this->objFiles->getAll();

        //$objBackground = $this->newObject('background', 'utilities');
        //check the users connection status,
        //only needs to be done once, then it becomes internal
        //$status = $objBackground->isUserConn();
        //keep the user connection alive, even if browser is closed!
        //$callback = $objBackground->keepAlive();

        if (count($files) > 0) {
            $file = $files[0];
            foreach ($files as $file) {
                $tags = $this->objTags->getTagsAsArray($file['id']);
                $slides = $this->objSlides->getSlides($file['id']);

                $file['tags'] = $tags;
                $file['slides'] = $slides;

                $this->_prepareDataForSearch($file);
            }
        }

        //$call2 = $objBackground->setCallback("tohir@tohir.co.za","Search rebuild", "The really long running process that you requested is complete!");
    }

    /**
     * Used to take file information and make as much of that information available
     * for search purposes
     *
     * @param array $file File Information
     */
    private function _prepareDataForSearch($file) {
        $content = $file['filename'];

        $content .= ( $file['description'] == '') ? '' : ', ' . $file['description'];
        $content .= ( $file['title'] == '') ? '' : ', ' . $file['title'];

        $tagcontent = ' ';

        if (count($file['tags']) > 0) {
            $divider = '';
            foreach ($file['tags'] as $tag) {
                $tagcontent .= $divider . $tag;
                $divider = ', ';
            }

            $content .= $tagcontent;
        }

        $file['tags'] = $tagcontent;


        $content .= ', ';

        $divider = '';
        foreach ($file['slides'] as $slide) {
            if (preg_match('/slide \d+/', $slide['slidetitle'])) {
                $content .= $divider . $slide['slidetitle'];
                $divider = ', ';
            }

            if ($slide['slidecontent'] != '<h1></h1>') {
                $content .= $divider . strip_tags($slide['slidecontent']);
                $divider = ',';
            }
        }

        $file['numslides'] = count($file['slides']);

        $file['content'] = $content;

        $this->_luceneIndex($file);
    }

    /**
     * Used to add a file to the search index
     *
     * @param array $file File Information
     */
    private function _luceneIndex($file) {


        $docId = 'podcaster_' . $file['id'];
        $docDate = $file['dateuploaded'];
        $url = $this->uri(array('action' => 'view', 'id' => $file['id']));
        $title = $file['title'];
        $contents = $file['content'];
        $teaser = $file['description'];
        $module = 'podcaster';
        $userId = $file['creatorid'];
        $tags = $file['tags'];
        $license = $file['cclicense'];
        $context = 'nocontext';
        $workgroup = 'noworkgroup';
        $permissions = NULL;
        $dateAvailable = NULL;
        $dateUnavailable = NULL;
        $extra = array('numslides' => $file['numslides'], 'filename' => $file['filename'], 'filetype' => $file['filetype'], 'mimetype' => $file['mimetype']);

        $this->objSearch->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, $tags, $license, $context, $workgroup, $permissions, $dateAvailable, $dateUnavailable, $extra);
    }

    /**
     * Method to regenerate the Flash or PDF version of a file
     */
    public function __regenerate() {
        $id = $this->getParam('id');
        $type = $this->getParam('type');

        $result = $this->objFiles->regenerateFile($id, $type);

        return $this->nextAction('view', array('id' => $id, 'message' => 'regeneration', 'type' => $type, 'result' => $result));
    }

    /**
     * Method to listall podcasts
     * Used for testing purposes
     * @access private
     */
    private function __listall() {
        $results = $this->objFiles->getAll(' ORDER BY dateuploaded DESC');

        if (count($results) > 0) {
            $this->loadClass('link', 'htmlelements');

            echo '<ol>';

            foreach ($results as $file) {
                echo '<li>' . $file['title'];

                $link = new link($this->uri(array('action' => 'regenerate', 'type' => 'flash', 'id' => $file['id'])));
                $link->link = 'Flash';

                echo ' - ' . $link->show();

                $link = new link($this->uri(array('action' => 'regenerate', 'type' => 'flash', 'id' => $file['id'])));
                $link->link = 'Slides';

                echo ' / ' . $link->show() . '<br />&nbsp;</li>';
            }

            echo '</ol>';
        }
    }

    /**
     * Batch script to convert podcasts to version 2
     */
    private function __converttov2() {
        $results = $this->objFiles->getAll(' ORDER BY dateuploaded DESC');

        if (count($results) > 0) {


            foreach ($results as $file) {
                log_debug($file['id'] . ' - ' . $file['title']);

                echo '<hr />' . $file['title'];

                $ok = $this->objFiles->checkpodcasterVersion2($file['id']);


                var_dump($ok);
            }
        }
    }

}

?>