<?php

/**
 *
 * Provides functionality specifically aimed at the UWC Elearning Mobile website
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
 * @package   uwcelearning
 * @author    Qhamani Fenana qfenama@uwc.ac.za/qfenama@gmail.com
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php,v 1.4 2007-11-25 09:13:27 qfenama Exp $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Controller class for Chisimba for the module uwcelearningmobile
 *
 * @author Qhamani Fenama
 *
 */
class uwcelearningmobile extends controller {

    /**
     *
     * @var string $objSysConfig String object property for holding the
     * configuration object
     * @access public;
     *
     */
    public $objSysConfig;
    /**
     *
     * @var string $objConfig String object property for holding the
     * configuration object
     * @access public;
     *
     */
    public $objConfig;
    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     *
     * @var string $objLog String object property for holding the
     * logger object for logging user activity
     * @access public
     *
     */
    public $objLog;

    /**
     *
     * Intialiser for the Uwc Elearning Mobile controller
     * @access public
     *
     */
    public function init() {
        $this->objSysConfig = $this->getObject('altconfig', 'config');
        $this->dbSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objAltconfig = $this->getObject('altconfig', 'config');
        $this->objConfig = $this->getObject('config', 'config');
        $this->objContext = $this->getObject('usercontext', 'context');
        $this->dbContext = $this->getObject('dbcontext', 'context');
        $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->objMobileSecurity = $this->getObject('mobilesecurity', 'uwcelearningmobile');
        $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');
        $this->link = $this->getObject('link', 'htmlelements');
        $this->objGroups = $this->getObject('managegroups', 'contextgroups');
        $this->objGroupAdmin = $this->getObject('groupadminmodel', 'groupadmin');
        // Store Context Code
        $this->userId = $this->objUser->userId();
        $this->userContext = $this->objContext->getUserContext($this->userId);
        $this->contextCode = $this->dbContext->getContextCode();
        $this->contextTitle = $this->dbContext->getField('title', $this->contextCode);

        $homelink = new link($this->URI(null));
        $homelink->link = '&nbsp;' . $this->objLanguage->languageText('mod_uwcelearningmobile_wordhome', 'uwcelearningmobile');
        $this->homeAndBackLink = $homelink->show();

        if ($this->contextCode != null) {
            $backlink = new link($this->URI(array('action' => 'context',
                                'contextcode' => $this->contextCode)));
            $backlink->link = $this->objLanguage->languageText('mod_uwcelearningmobile_wordbacktocontext', 'uwcelearningmobile');
            '' . $this->homeAndBackLink .= ' - ' . $backlink->show();
        }
    }

    /**
     * Override the login object in the parent class
     *
     * @param void
     * @return bool
     * @access public
     */
    public function requiresLogin($action) {
        $actions = array('', 'home', 'login');
        //This Hack help to prevent going to the security module when :-
        //1 - action requires login and
        //2 - the user is no logged on
        if (!in_array($action, $actions) && !$this->objUser->isLoggedIn()) {
            return $this->goToLogin();
        }

        if (in_array($action, $actions)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function dispatch($action='home') {
        $actions = array('', 'home', 'login', 'context', 'readmail', 'internalmail', 'filemanager', 'forum', 'viewforum', 'topic', 'compose', 'sendmail', 'calladdrecipient', 'addrecipient', 'rmrecipient', 'showbooks', 'addressbook', 'multirecipient', 'upload');

        if ($this->contextCode == NULL && !in_array($action, $actions)) {
            $action = 'home';
        }
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->__getMethod($action);
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
     * @return stromg the name of the method
     *
     */
    function __getMethod(& $action) {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
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
    function __validAction(& $action) {
        if (method_exists($this, "__" . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Default Action for Uwc Elearning Mobile module
     * It shows the prelogin or postlogin depending if you logged in or not
     * @access private
     */
    private function __home() {
        $this->setSession('recipientList', NULL);
        if ($this->objUser->isLoggedIn()) {
            $this->dbContext->leaveContext();
	    $this->objContext = $this->getObject('usercontext', 'context');
            $usercontexts = $this->objContext->getUserContext($this->objUser->userId());
            $this->setVarByRef('usercontexts', $usercontexts);

            $modules = array('filemanager', 'internalmail', 'forum');
            $tools = array();
            foreach ($modules as $mod) {
                if ($this->objModuleCatalogue->checkifRegistered($mod)) {
                    $tools[] = $mod;
                }
            }
            $this->setVarByRef('tools', $tools);
            return 'postlogin_tpl.php';
        } else {
            $error = $this->getParam('error');
            if ($error) {
                $this->setVarByRef('error', $error);
            }
            return 'prelogin_tpl.php';
        }
    }

    /**
     * Mothod that takes care of the login in the mobile site
     * @access private
     *
     */
    private function __login() {
        $error = $this->objMobileSecurity->CheckErrors();
        if ($error != true) {
            return $this->nextAction('login', array(), 'security');
        } else {
            return $this->nextAction('home', array('error' => $error));
        }
    }

    /**
     * Method to view course tools
     * @access private
     */
    private function __context() {
        $contextcode = $this->getParam('contextcode');
        $status = $this->dbContext->joinContext($contextcode);
        $con = $this->dbContext->getContext($contextcode);
        $conexttitle = $con['title'];
        $this->setVarByRef('conextcode', $contextcode);
        $this->setVarByRef('conexttitle', $conexttitle);

        //Modules that should be able to be viewable
        /* Course Content - File Manager - Assignments - Discussion Forum - Internal Email - Calander - Notifications */
        $modules = array('contextcontent', 'filemanager', 'assignment', 'forum', 'internalmail', 'calendar', 'announcements', 'mcqtests');

        $tools = array();
        $objContextModules = $this->getObject('dbcontextmodules', 'context');
        $contextModules = $objContextModules->getContextModules($contextcode);
        //Get Context tools
        foreach ($contextModules as $mod) {
            if (in_array($mod, $modules)) {
                $tools[] = $mod;
            }
        }
        $this->setVarByRef('tools', $tools);
        return 'context_tpl.php';
    }

    /**
     * Method to view courses announcements
     * @access private
     */
    private function __announcements() {
        $this->objAnnouncements = $this->getObject('dbannouncements', 'announcements');

        //Get a current course announcement
        $coursesann = $this->objAnnouncements->getContextAnnouncements($this->contextCode);
        $coursesanncount = $this->objAnnouncements->getNumContextAnnouncements($this->contextCode);
        //or $coursesanncount = count($coursesann);
        //Get all my courses announcement
        $allann = $this->objAnnouncements->getAllAnnouncements($this->userContext);
        $allanncount = count($allann);

        $this->setVarByRef('coursesann', $coursesann);
        $this->setVarByRef('coursesanncount', $coursesanncount);
        $this->setVarByRef('allann', $allann);
        $this->setVarByRef('allanncount', $allanncount);
        return 'announcements_tpl.php';
    }

    /**
     * Method to view a single announcement
     * @access private
     */
    private function __viewannouncements() {
        $id = $this->getParam('id');
        $this->objAnnouncements = $this->getObject('dbannouncements', 'announcements');
        $ann = $this->objAnnouncements->getMessage($id);

        $this->setVarByRef('announcement', $ann);
        return 'viewannouncements_tpl.php';
    }

    /**
     * Method to view course MCQ Test
     * @access private
     */
    private function __mcqtests() {
        $this->dbTestadmin = $this->getObject('dbtestadmin', 'mcqtests');
        $tests = $this->dbTestadmin->getTests($this->contextCode);

        $this->setVarByRef('tests', $tests);
        return 'mcqtests_tpl.php';
    }

    /**
     * Method to view course forums
     * @access private
     */
    private function __forum() {
        if ($this->contextCode == null) {
            $this->contextCode = 'root';
            $this->contextTitle = 'Lobby';
        }
        $this->objForum = $this->getObject('dbforum', 'forum');

        $allForums = $this->objForum->showAllForums($this->contextCode);

        $this->setVarByRef('forums', $allForums);

        return 'forum_tpl.php';
    }

    /**
     * Method to view a selected forum's topics
     * @access private
     */
    private function __viewforum() {
        $this->objTopic = $this->getObject('dbtopic', 'forum');
        $this->objForum = $this->getObject('dbforum', 'forum');
        $id = $this->getParam('id');

        $forum = $this->objForum->getForum($id);

        // Check if user has access to workgroup forum
        $this->objWorkGroupUser = $this->getObject('dbworkgroupusers', 'workgroup');
        if ($forum['forum_workgroup'] != NULL && !$this->objWorkGroupUser->memberOfWorkGroup($this->userId, $forum['forum_workgroup'])) {
            return 'forum_workgroup_noaccess.php';
        }

        // Check if the forum exists, if not, go to the Forum Home Page
        if ($forum == '') {
            return $this->nextAction('forum');
        }


        $limit = $this->objTopic->getNumTopicsInForum($id, true);
        $forum = $this->objForum->getForum($id);
        $order = 'date';
        $direction = 'asc';
        $limit = ' LIMIT 0, ' . $limit;
        $allTopics = $this->objTopic->showTopicsInForum($id, $this->userId, $forum['archivedate'], $order, $direction, NULL, $limit);
        $this->setVarByRef('allTopics', $allTopics);
        $this->setVarByRef('forum', $forum);
        return 'singleforum_tpl.php';
    }

    /**
     * Method to view a selected topic
     * @access private
     */
    private function __topic() {
        $mode = $this->getParam('mode', NULL);
        $message = $this->getParam('message');

        $id = $this->getParam('id');

        $objTopic = $this->getObject('dbtopic', 'forum');
        $topic = $objTopic->getTopicDetails($id);

        $subject = $this->getParam('title');

        $objForum = $this->getObject('dbforum', 'forum');
        $forum = $objForum->getForum($topic['forum_id']);

        $objPost = $this->getObject('dbpost', 'forum');
        $posts = $objPost->getFlatThread($id);
        // Get details on the topic
        $rootpost = $objPost->getRootPost($id);

        $subject = $this->getParam('title', 'RE: ' . $topic['post_title']);

        if ($mode == 'add') {
            if ($subject != null && $message != null) {
                $this->saveReply();
            } else {
                $err = 'All fields are required';
                $this->setVarByRef('err', $err);
            }
        }
        $readmore = $this->getParam('readmore');

        $this->setVarByRef('readmore', $readmore);
        $this->setVarByRef('forum', $forum);
        $this->setVarByRef('topic', $topic);
        $this->setVarByRef('subject', $subject);
        $this->setVarByRef('message', $message);
        $this->setVarByRef('posts', $posts);
        $this->setVarByRef('rootpost', $rootpost);
        $this->setVarByRef('topicid', $id);
        return 'viewtopic_tpl.php';
    }

    /**
     * Method to view a assignments
     * @access private
     */
    private function __assignment() {
        $this->objUtil = $this->getObject('util');
        $assignments = $this->objUtil->getAssignments($this->contextCode);
        $this->setVarByRef('assignments', $assignments);
        return 'assignment_tpl.php';
    }

    /**
     * Method to view a assignment details
     * @access private
     */
    private function __viewassignment() {
        $id = $this->getParam('id');
        $this->objUtil = $this->getObject('util');
        $assignment = $this->objUtil->getAssignment($id);
        $this->setVarByRef('assignment', $assignment);
        return 'viewassignment_tpl.php';
    }

    /**
     * Method to show events for the current/selected month.
     * @access private
     */
    private function __calendar() {
        $month = $this->getParam('month', date('m'));
        $year = $this->getParam('year', date('Y'));
        $this->setVarByRef('month', $month);
        $this->setVarByRef('year', $year);

        $this->objCalendarInterface = $this->getObject('calendarinterface', 'calendar');
        $this->objCalendarInterface->setupCalendar($month, $year);

        $eventsCalendar = $this->objCalendarInterface->getCalendar();

        $this->setVarByRef('userEvents', $this->objCalendarInterface->numUserEvents);
        $this->setVarByRef('contextEvents', $this->objCalendarInterface->numContextEvents);
        $this->setVarByRef('otherContextEvents', $this->objCalendarInterface->numOtherEvents);
        $this->setVarByRef('siteEvents', $this->objCalendarInterface->numSiteEvents);
        $this->setVarByRef('eventsCalendar', $eventsCalendar);
        $this->setVarByRef('eventsList', $this->objCalendarInterface->getSmallEventsList());

        return 'calendar_tpl.php';
    }

    /**
     * Method to view a new course content
     * @access private
     */
    private function __contextcontent() {
        $this->objContextChapters = $this->getObject('db_contextcontent_contextchapter', 'contextcontent');
        $this->objContextActivityStreamer = $this->getObject('db_contextcontent_activitystreamer', 'contextcontent');
        $chapters = $this->objContextChapters->getContextChapters($this->contextCode);
        $arr = array();
        foreach ($chapters as $con) {
            $ischapterlogged = $this->objContextActivityStreamer->getRecord($this->objUser->userId(), $con['chapterid'], $this->contextCode);
            if ($ischapterlogged == FALSE) {
                $arr[] = $con;
            }
        }
        $this->setVarByRef('content', $arr);
        return 'contextcontent_tpl.php';
    }

    /**
     * Method to view a internal mails
     * @access private
     */
    private function __internalmail() {
        $this->setSession('recipientList', NULL);
        $folderId = $this->getParam('folderId', 'init_1');
        $this->dbFolders = $this->getObject('dbfolders', 'internalmail');
        $this->dbRouting = $this->getObject('dbrouting', 'internalmail');

        $arrFolderList = $this->dbFolders->listFolders();
        $arrFolderData = $this->dbFolders->getFolder($folderId);
        $arrEmailListData = $this->dbRouting->getAllMail($folderId, $sortOrder, $filter);

        $this->setVarByRef('arrFolderData', $arrFolderData);
        $this->setVarByRef('arrEmailListData', $arrEmailListData);
        $this->setVarByRef('folderId', $folderId);
        $this->setVarByRef('arrFolderList', $arrFolderList);

        return 'internalmail_tpl.php';
    }

    /**
     * Method to view/read the email
     * @access private
     */
    private function __readmail() {
        $routingId = $this->getParam('routingid');
        $this->dbRouting = $this->getObject('dbrouting', 'internalmail');
        $this->dbemail = $this->getObject('dbemail', 'internalmail');
        $route = $this->dbRouting->getMail($routingId);

        $msgid = $route['email_id'];
        $msg = $this->dbemail->getMail($msgid);
        $this->dbRouting->markAsRead($routingId);

        $this->setVarByRef('routing', $route);
        $this->setVarByRef('message', $msg);
        return 'readmail_tpl.php';
    }

    /**
     * Method to redirect the user to the login screen
     * @access private
     */
    private function goToLogin() {
        return $this->nextAction('home', array('error' => 'Login is required'));
    }

    /**
     * Method to view a course and personal files and folder
     * @access private
     */
    private function __filemanager() {
        //My files
        $this->objFiles = $this->getObject('dbfile', 'filemanager');
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objFileIcons = $this->getObject('fileicons', 'files');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        // Get Folder Details
        $folderpath = 'users/' . $this->objUser->userId();
        $folderId = $this->objFolders->getFolderId($folderpath);
        $folderId = $this->getParam('folderid', $folderId);
        $folders = $this->objFolders->getSubFolders($folderId);
        $singlefolder = $this->objFolders->getFolderPath($folderId);
        $files = $this->objFiles->getFolderFiles($singlefolder);
        $currid = $this->getSuperFolder($folderId);
        $foldername = basename($singlefolder);
        if ($foldername == basename($folderpath)) {
            $foldername = $this->objLanguage->languageText('mod_uwcelearningmobile_wordmyfiles', 'uwcelearningmobile');
        }
        $this->setVarByRef('folderid', $folderId);
        $this->setVarByRef('currname', $foldername);
        $this->setVarByRef('currfolder', $currid);
        $this->setVarByRef('files', $files);
        $this->setVarByRef('folders', $folders);

        //Get Context Files
        if ($this->contextCode != NULL) {
            // Get Folder Details
            $coursefolderpath = 'context/' . $this->contextCode;
            $coursefolderId = $this->objFolders->getFolderId($coursefolderpath);

            $coursefolderId = $this->getParam('coursefolderid', $coursefolderId);
            $coursefolders = $this->objFolders->getSubFolders($coursefolderId);
            $coursesinglefolder = $this->objFolders->getFolderPath($coursefolderId);
            $coursefiles = $this->objFiles->getFolderFiles($coursesinglefolder);
            $coursecurrid = $this->getSuperFolder($coursefolderId);

            $coursefoldername = basename($coursesinglefolder);
            if ($coursefoldername == basename($coursefolderpath)) {
                $coursefoldername = $this->contextTitle . ' - Files';
            }
            $folderParts = explode('/', $coursesinglefolder);
            $folderPermission = $this->objFolders->checkPermissionUploadFolder($folderParts[0], $folderParts[1]);
            $this->setVarByRef('folderPermission', $folderPermission);
            $this->setVarByRef('coursecurrname', $coursefoldername);
            $this->setVarByRef('coursefolderid', $coursefolderId);
            $this->setVarByRef('coursecurrfolder', $coursecurrid);
            $this->setVarByRef('coursefiles', $coursefiles);
            $this->setVarByRef('coursefolders', $coursefolders);
        }
        return 'filemanager_tpl.php';
    }

    /**
     * Method used to upload the files
     *
     */
    function __upload() {
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objUpload = $this->getObject('upload', 'filemanager');

        $folderid = $this->getParam('folderid');
        $folder = $this->objFolders->getFolder($folderid);
        //error_log(var_export($folder, true));
        if ($folder != FALSE) {
            $this->objUpload->setUploadFolder($folder['folderpath']);
        }
        // Upload File
        $results = $this->objUpload->uploadFiles();
        if ($this->contextCode != NULL) {
            return $this->nextAction('filemanager', array('folderid' => $folderid));
        } else {
            return $this->nextAction('filemanager', array('folderid' => $folderid));
        }
    }

    /**
     * Method to get the folder's super folder
     * @access private
     * @param string id
     * @return string folderid
     */
    private function getSuperFolder($folderid) {
        $folder = $this->objFolders->getFolder($folderid);
        $del = '/' . basename($folder['folderpath']);
        $path = explode($del, $folder['folderpath']);
        return $this->objFolders->getFolderId($path[0]);
    }

    /**
     * Method that view the context content
     *
     */
    private function __viewcontextcontent() {
        $id = $this->getParam('id');
        $this->objContentOrder = $this->getObject('db_contextcontent_order', 'contextcontent');
        $firstPage = $this->objContentOrder->getFirstChapterPage($this->contextCode, $id);
        $this->setVarByRef('firstPage', $firstPage);
        return 'viewcontextcontent_tpl.php';
    }

    /**
     * Method that called when the reply/reply all
     *
     * @return template @type string
     *
     */
    private function __compose() {

        $arrUserId = $this->getParam('userId');
        $subject = $this->getParam('subject');
        $message = $this->getParam('message');
        $emailId = $this->getParam('emailId');
        $this->dbRouting = $this->getObject('dbrouting', 'internalmail');

        $recipientList = $this->getParam('recipientList', NULL);
        if (!empty($arrUserId)) {
            if (!is_array($arrUserId)) {
                $arrUserId = explode('|', $arrUserId);
            }
            if ($recipientList != NULL) {
                $arrRecipients = explode('|', $recipientList);
                $arrUserId = array_merge($arrRecipients, $arrUserId);
            }
            $toList = '';
            foreach ($arrUserId as $key => $userId) {
                if ($userId != "") {
                    $username = $this->objUser->userName($userId);
                    $this->addRecipient($username);
                }
            }
        } else {
            $toList = '';
            $recipientList = '';
        }
        $recipientList = $this->getSession('recipientList');
        $this->setVarByRef('recipientList', $recipientList);
        $this->setVarByRef('userId', $userId);
        $this->setVarByRef('subject', $subject);
        $this->setVarByRef('message', $message);
        $this->setVarByRef('emailId', $emailId);
        return 'compose_tpl.php';
    }

    /**
     * The method that send the email
     *
     */
    private function __sendmail() {

        $this->dbEmail = $this->getObject('dbemail', 'internalmail');
        $subject = $this->getParam('subject');

        if ($subject == '') {
            $subject = $this->objLanguage->languageText('phrase_nosubject');
        }
        $message = $this->getParam('message');

        $recipientList = $this->getRecipientListForDB();

        if ($recipientList) {
            $emailId = $this->dbEmail->sendMail($recipientList, $subject, $message, 0);
        }
        return $this->nextAction('internalmail');
    }

    /**
     * The method that calls the addrecipients template
     *
     */
    private function __calladdrecipient() {
        $page = $this->getParam('page', 0);
        $search = $this->getParam('search');
        $this->objUtil = $this->getObject('util');
        $users = $this->objUtil->getUsers($page, $search);

        $this->setVarByRef('users', $users);
        return 'addrecipient_tpl.php';
    }

    /**
     * The method that remove the recipient from the recipiens list
     */
    private function __rmrecipient() {
        $username = $this->getParam('username');
        $this->removeRecipient($username);
        $this->nextAction('compose');
    }

    /**
     * Method to add person to the recipient list
     * @param string $username
     */
    private function addRecipient($username) {

        $reccipients = $this->getSession('recipientList');
        //create the sesstion list if it doesnt exist
        if ($reccipients == NULL) {
            $reccipients = array();
            $this->setSession('recipientList', $reccipients);
        }

        //Check whether the user is not null and the user already exist on th list
        if ($username != "" && !in_array($username, $reccipients)) {
            //add the recipient to the session list
            $reccipients[] = $username;
            $this->setSession('recipientList', $reccipients);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Method to add person to the recipient list
     * @param string $username
     */
    private function removeRecipient($username) {

        $reccipients = $this->getSession('recipientList');
        $k = array_keys($reccipients, $username);

        if (count($k) > 0) {
            unset($reccipients[$k[0]]);
            $this->setSession('recipientList', $reccipients);
        }
    }

    /**
     * Method to put the recipient list into a string
     *
     */
    private function getRecipientListForDB() {
        $reccipients = $this->getSession('recipientList');
        if (count($reccipients) > 0) {
            $str = '';
            foreach ($reccipients as $rec) {
                $str .= $this->objUser->getUserId($rec) . '|';
            }
            return $str;
        } else {
            return FALSE;
        }
    }

    /**
     * Method that get the number of users in the system
     * @return Int
     */
    private function getAllUsersCount() {
        $usercount = $this->objUser->getAll();
        $usercount = count($usercount);
        return $usercount;
    }

    /*
     * The method that show the address book
     */

    private function __showbooks() {
        $recipientList = $this->getParam('recipient');
        $subject = $this->getParam('subject');
        $message = $this->getParam('message');
        $arrContextList = $this->objGroups->usercontextcodes($this->userId);

        $this->dbBooks = $this->getObject('dbaddressbooks', 'internalmail');
        $arrBookList = $this->dbBooks->listBooks();
        $this->setVarByRef('arrContextList', $arrContextList);
        $this->setVarByRef('arrBookList', $arrBookList);
        return 'addressbook_tpl.php';
    }

    /**
     * Method that calls the address book
     * @access private
     * 
     */
    private function __addressbook() {
        $contextCode = $this->getParam('contextcode', NULL);
        $bookId = $this->getParam('bookId', NULL);
        if ($contextCode != NULL) {
            $groupId = $this->objGroupAdmin->getLeafId(array($contextCode
                    ));

            $users = $this->objGroupAdmin->getGroupUsers($groupId, array(
                        'userId',
                        'firstname',
                        'surname',
                        'username'
                    ));
        } else if ($bookId != NULL) {
            $this->dbBookEntries = $this->getObject('dbbookentries', 'internalmail');
            $users = $this->dbBookEntries->listBookEntries($bookId);
        }

        $this->setVarByRef('contextCode', $contextCode);
        $this->setVarByRef('users', $users);
        $this->setVarByRef('bookId', $bookId);
        $this->setVarByRef('currentFolderId', $currentFolderId);
        $this->setVar('mode', NULL);
        return 'entries_tpl.php';
    }

    /**
     * Method to add multiply recipients in the mail
     *
     */
    private function __multirecipient() {
        $users = $this->getParam('users');
        foreach ($users as $userId) {
            if ($userId != "") {
                $username = $this->objUser->userName($userId);
                $this->addRecipient($username);
            }
        }
        return $this->nextAction('compose');
    }

    /**
     * Method to save a reply to a topic
     */
    public function saveReply() {

        if ($this->getParam('replytype') == 'reply') {
            $post_parent = $this->getParam('parent');
            $post_tangent_parent = 0;
        } else {
            $post_parent = 0;
            $post_tangent_parent = $this->getParam('parent');
        }

        $this->objPost = $this->getObject('dbpost', 'forum');
        $this->objPostText = $this->getObject('dbposttext', 'forum');
        $this->objTopicRead = $this->getObject('dbtopicread', 'forum');

        $parentPostDetails = $this->objPost->getRow('id', $this->getParam('parent'));

        //gathering the params
        $forum_id = $this->getParam('forum');
        $topic_id = $this->getParam('topic');
        $type_id = $this->getParam('discussionType');
        $post_title = $this->getParam('title');
        $post_text = $this->getParam('message');
        $language = 'en';
        $original_post = 1;
        $level = $parentPostDetails['level'];

        $post_id = $this->objPost->insertSingle($post_parent, $post_tangent_parent, $forum_id, $topic_id, $this->userId, $level);
        $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $original_post, $this->userId);
        //Forum and Topic Classes
        $this->objTopic = $this->getObject('dbtopic', 'forum');
        $this->objForum = $this->getObject('dbforum', 'forum');
        $this->objTopic->updateLastPost($topic_id, $post_id);
        $this->objForum->updateLastPost($forum_id, $post_id);

        // Forum Email Class
        $this->objForumEmail = $this->getObject('forumemail', 'forum');

        // Load Forum Subscription classes
        $this->objForumSubscriptions = $this->getObject('dbforumsubscriptions', 'forum');
        $this->objTopicSubscriptions = $this->getObject('dbtopicsubscriptions', 'forum');

        $forumDetails = $this->objForum->getForum($forum_id);
        $topicSubscription = $this->objTopicSubscriptions->isSubscribedToTopic($topic_id, $this->objUser->userId());
        $forumSubscription = $this->objForumSubscriptions->isSubscribedToForum($forum_id, $this->objUser->userId());
        // Manage Subscriptions
        if (isset($_POST['subscriptions'])) {
            if ($_POST['subscriptions'] == 'forumsubscribe') { // First check subscriptions to forum
                if (!$forumSubscription) { // If not subscribed to forum, subscribe user now.
                    $this->objForumSubscriptions->subscribeUserToForum($forum_id, $this->objUser->userId());
                }
            } else if ($_POST['subscriptions'] == 'topicsubscribe') { // Now check if the user wants to subscribe to the topic
                if ($forumSubscription) { // If user was subscribed to forum, remove subscription
                    $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $this->objUser->userId());
                }
                // Now subscribe user to topic, if the user was not subscribed to the topic
                if (!$topicSubscription) {
                    $this->objTopicSubscriptions->subscribeUserToTopic($topic_id, $this->objUser->userId());
                }
            } else if ($_POST['subscriptions'] == 'nosubscriptions') { // Else remove subscription from topic
                $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $this->objUser->userId());
            }
        }
        if ($forumDetails['subscriptions'] == 'Y') {
            $replyUrl = $this->uri(array('action' => 'postreply', 'id' => $post_id));
            $emailSuccess = NULL;
        } else {
            $emailSuccess = NULL;
        }
        return $this->nextAction('topic', array('id' => $topic_id));
    }

    /**
     * Method or action
     *
     *
     *
     */
    function __viewsubmission() {

        $id = $this->getParam('id');
        $this->objUtil = $this->getObject('util');
        $submission = $this->objUtil->getSubmission($id);

        if ($submission == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownsubmission'));
        }
        $assignment = $this->objUtil->getAssignment($submission['assignmentid']);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownassignment'));
        }
        if ($assignment['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }
        $this->setVarByRef('assignment', $assignment);
        $this->setVarByRef('submission', $submission);
        return 'viewsubmission_tpl.php';
    }

    /**
     * Method used to download the student's assignment
     *
     */
    function __downloadfile() {
        $id = $this->getParam('id');
        $fileId = $this->getParam('fileid');
        $this->objUtil = $this->getObject('util');
        $submission = $this->objUtil->getSubmission($id);
        if ($submission == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownsubmission'));
        }
        $assignment = $this->objUtil->getAssignment($submission['assignmentid']);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownassignment'));
        }
        $filePath = $this->objUtil->getAssignmentFilename($submission['id'], $fileId);
        $objDateTime = $this->getObject('dateandtime', 'utilities');
        $objFile = $this->getObject('dbfile', 'filemanager');
        $file = $objFile->getFile($fileId);
        $extension = $file['datatype'];

        $filename = $this->objUser->fullName($submission['userid']) . ' ' . $objDateTime->formatDate($submission['datesubmitted']);
        $filename = str_replace(' ', '_', $filename);
        $filename = str_replace(':', '_', $filename);

        if (file_exists($filePath)) {
            // Set Mimetype
            header('Content-type: ' . $file['mimetype']);
            // Set filename and as download
            header('Content-Disposition: attachment; filename="' . $filename . '.' . $extension . '"');
            // Load file
            readfile($filePath);
            exit;
        }
    }

}

?>
