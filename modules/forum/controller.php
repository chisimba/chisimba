<?php

/* -------------------- forum class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}
// end security check

/**
 * Forum Controller
 * This class controls all functionality to run the Discussion Forum module
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package forum
 * @version 1
 */
class forum extends controller {

        /**
         *
         * @var string Used to determine whether the forum is for context
         * or workgroup
         *
         * @access protected
         *
         */
        protected $forumtype;
        var $ckElement;

        /**
         *
         * @var string the alue of the context (e.g. the coursecode)
         *
         * @access protected
         *
         */
        protected $contextCode;

        /**
         *
         * @var string the value of the workgroup if in a workgroup
         * or workgroup
         *
         * @access protected
         *
         */
        protected $workgroupId;

        /**
         *
         * @var string The workgroup description if in a workgroup
         * or workgroup
         *
         * @access protected
         *
         */
        protected $workgroupDescription;

        /**
         * Constructor method to instantiate objects and get variables
         *
         * @access public
         *
         */
        public function init() {
                //trigger_error('*');
                //Get the activity logger class
                $this->objLog = $this->newObject('logactivity', 'logger');
                //Log this module call
                $this->objLog->log();

                $this->objAltConfig = $this->getObject("altconfig", "config");
                // General Classes
                $this->loadClass('attachmentreader', 'mail');
                $this->objUser = & $this->getObject('user', 'security');
                $this->userId = $this->objUser->userId();
                $this->isLoggedIn = $this->objUser->isLoggedIn();
                $this->objLanguage = & $this->getObject('language', 'language');
                $this->objTopic = $this->getObject('dbtopic', 'forum');

                //User context object
                $this->objUserContext = $this->getObject('usercontext', 'context');

                // Forum Classes
                $this->objForum = & $this->getObject('dbforum');
                $this->objDiscussionType = & $this->getObject('dbdiscussiontypes');
                $this->objTopic = & $this->getObject('dbtopic');
                $this->objPost = & $this->getObject('dbpost');
                $this->objPostText = & $this->getObject('dbposttext');
                $this->objTopicRead = & $this->getObject('dbtopicread');

                // Forum Attachments
                $this->objTempAttachments = & $this->getObject('dbtempattachments');
                $this->objPostAttachments = & $this->getObject('dbpostattachments');

                // Forum Ratings
                $this->objForumRatings = & $this->getObject('dbforum_ratings');
                $this->objPostRatings = & $this->getObject('dbpost_ratings');

                // Forum Email Class
                $this->objForumEmail = & $this->getObject('forumemail');

                // Load Forum Subscription classes
                $this->objForumSubscriptions = & $this->getObject('dbforumsubscriptions');
                $this->objTopicSubscriptions = & $this->getObject('dbtopicsubscriptions');

                // Forum Statistics
                $this->objForumStats = & $this->getObject('forumstats');

                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();

                // If not in context, set code to be 'root' called 'Lobby'
                $this->contextTitle = $this->contextObject->getTitle();
                if ($this->contextCode == '') {
                        $this->contextCode = 'root';
                        $this->contextTitle = 'Lobby';
                }

                // set Context to Template
                $this->setVarByRef('contextCode', $this->contextCode);
                $this->setVarByRef('contextTitle', $this->contextTitle);

                // Set Current Context
                $this->objForumEmail->setContextCode($this->contextCode);

                // Trim String Functions
                $this->trimstrObj = & $this->getObject('trimstr', 'strings');

                // Workgroup Classes
                $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');
                $this->usingWorkGroupsFlag = $this->objModuleCatalogue->checkIfRegistered('workgroup');
                if ($this->usingWorkGroupsFlag) {
                        $this->objWorkGroup = & $this->getObject('dbworkgroup', 'workgroup');
                        $this->objWorkGroupUser = & $this->getObject('dbworkgroupusers', 'workgroup');
                }

                // Check for workgroup
                if ($this->usingWorkGroupsFlag && ($this->getParam('action') == 'workgroup' OR $this->getParam('type') == 'workgroup')) {
                        $this->getWorkGroupDetails();
                } else {
                        $this->forumtype = 'context';
                }
                $this->setVarByRef('forumtype', $this->forumtype);

                // Set to post since some links are generated within the post
                $this->objPost->forumtype = $this->forumtype;

                // Load Menu Tools Class
                $this->objMenuTools = & $this->getObject('tools', 'toolbar');
                $this->objDateTime = & $this->getObject('dateandtime', 'utilities');
                $this->objFiles = & $this->getObject('dbfile', 'filemanager');
                $this->loadClass('link', 'htmlelements');

                if (strtolower($this->getParam('passthroughlogin')) == 'true') {
                        $this->updatePassThroughLogin();
                }
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->showFullName = $this->objSysConfig->getValue('SHOWFULLNAME', 'forum');
                if ($this->showFullName == '') {
                        $this->showFullName = TRUE;
                }
                if ($this->showFullName != FALSE) {
                        $this->showFullName = TRUE;
                }
                //Load Module Catalogue Class
                $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');
                $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
                if ($this->objModuleCatalogue->checkIfRegistered('activitystreamer')) {
                        $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
                        $this->eventDispatcher->addObserver(array($this->objActivityStreamer, 'postmade'));
                        $this->eventsEnabled = TRUE;
                } else {
                        $this->eventsEnabled = FALSE;
                }

                $this->ckElement = $this->newObject('htmlarea', 'htmlelements');
                $this->ckElement->name = 'blocktext';
                $this->ckElement->width = '100%';
                $this->ckElement->height = '250px';
//                $this->ckElement->toolbarSet = 'simple';

                $this->objManageGroups = $this->getObject('managegroups', 'contextgroups');
                $this->ignoreGroupMembership = $this->objSysConfig->getValue('IGNORE_GROUP_MEMBERSHIP', 'forum');
        }

        /**
         *
         * Determine if a given action requires login
         *
         * @return boolean TRUE|FALSE
         * @access public
         *
         */
        public function requiresLogin() {
                $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

                // Check if User is allowed to view forum without being logged in
                $allowPrelogin = $objSysConfig->getValue('ALLOW_PRELOGIN', 'forum');
                if ($this->getParam('action', NULL) == 'pullmail' || $this->getParam('action', NULL) == 'runmailjobs') {
                        return FALSE;
                }
                // If turned off, user requires login for ALL actions
                if ($allowPrelogin != '1') {
                        return TRUE;
                }

                // Else user requires login for some actions
                switch ($this->getParam('action', NULL)) {
                        case NULL:
                        case 'forum':
                        case 'viewtopic':
                        case 'thread':
                        case 'singlethreadview':
                        case 'flatview':
                        case 'viewtranslation':
                        /*
                          case 'downloadattachment':
                         */
                        case 'searchforum':
                        case 'viewtopicmindmap':
                        case 'generatetopicmindmap':
                        case 'viewmindmap':
                        case 'generatemindmap':
                        case 'loadtranslation':
                        case 'pullmail':
                        case 'runmailjobs':
                                return FALSE;
                                break;
                        default:
                                return TRUE;
                                break;
                }
        }

        /**
         *
         * Method to process actions to be taken from the action querystring parameter
         *
         * @access public
         * @return mixed Various templates depending on the action
         *
         */
        public function dispatch($action = Null) {
                $this->setLayoutTemplate('forum_layout.php');
                //$this->setVar('pageSuppressXML', TRUE);

                switch ($action) {
                        case 'savepostratingup' :
                                return $this->savePostRatingUp();

                        case 'savepostratingdown':
                                return $this->savePostRatingDown();

                        case 'updateforumsetting':
                                return $this->updateforumsetting();

                        case 'test':
                                return 'test.php';
                        case 'forum':
                                return $this->showForum($this->getParam('id'));

                        case 'newtopic':
                                return $this->newTopicForm($this->getParam('id'));

                        case 'savenewtopic':
                                return $this->saveNewTopic();

                        case 'viewtopic':
                                return $this->viewTopic($this->getParam('id'), $this->getParam('message'), $this->getParam('post'));

                        case 'thread':
                                return $this->showThread($this->getParam('id'));

                        case 'singlethreadview';
                                return $this->showSingleThread($this->getParam('id'));

                        case 'flatview';
                                return $this->showFlatView($this->getParam('id'));

                        case 'postreply':
                                return $this->showTopicReplyForm($this->getParam('id'));

                        case 'savepostreply':
//                                if ($_POST['replytype'] == 'tangent') {
//                                        return $this->saveNewTopic();
//                                } else {
                                return $this->saveReply();
//                                }

                        case 'savepostedit':
                                return $this->savepostedit();

                        case 'showeditpostpopup':
                                return $this->showeditpostpopup();

                        case 'removepost':
                                return $this->removepost();

                        case 'administration':
                                return $this->forumAdministration();

                        case 'createforum':
                                return $this->createForum();

                        case 'saveforum':
                                return $this->saveForum();

                        case 'editforum':
                                return $this->editForum($this->getParam('id'));

                        case 'editforumsave':
                                return $this->editForumSave();

                        case 'deleteforum':
                                return $this->deleteForum($this->getParam('id'));

                        case 'deleteforumconfirm':
                                return $this->deleteForumConfirm();

                        case 'changevisibilityconfirm':
                                return $this->updateForumVisibility();

                        case 'setdefaultforum':
                                return $this->setDefaultForum();

                        case 'attachments':
                                return $this->showAttachments($this->getParam('id'), $this->getParam('forum'));

                        case 'saveattachment':
                                return $this->saveAttachment();

                        case 'deleteattachment':
                                return $this->deleteAttachment($this->getParam('attachmentwindow'), $this->getParam('id'));

                        case 'editpost':
                                return $this->editPost($this->getParam('id'));

                        case 'updatepost':
                                return $this->updatePost();

                        case 'translate':
                                return $this->translatePost($this->getParam('id'));

                        case 'savetranslation':
                                return $this->saveTranslation();

                        case 'viewtranslation':
                                return $this->viewTranslation($this->getParam('id'));

                        case 'changetopicstatus':
                                return $this->changeTopicStatus();

                        case 'runmailjobs':
                                return $this->runMailJobs();

                        case 'pullmail':
                                return $this->pullMail();

//                        case 'savepostratings':
//                                return $this->savePostRatings();

                        case 'workgroup':
                                return $this->checkWorkgroupForum();

                        case 'noaccess':
                                return $this->showNoAccess($this->getParam('id'));

                        // case 'sendemail':
                        // return $this->sendEmail();

                        case 'statistics':
                                return $this->showForumStatistics($this->getParam('id'));

                        case 'moderatetopic':
                                return $this->moderateTopic($this->getParam('id'));

                        case 'moderate_deletetopic':
                                return $this->moderateDeleteTopic();

                        case 'moderate_movetotangent':
                                return $this->moderateMoveTangent();

                        case 'moderate_movetoforum':
                                return $this->moderateMoveNewForum();

                        case 'moderate_movetonewtopic':
                                return $this->moderateMoveNewTopic();

                        case 'moderate_topicsticky':
                                return $this->moderateStickyTopic();

                        case 'viewmindmap':
                                return $this->viewMindMap($this->getParam('id'));

                        case 'generatemindmap':
                                return $this->generateMindMap($this->getParam('id'));

                        case 'viewtopicmindmap':
                                return $this->viewTopicMindMap($this->getParam('id'));

                        case 'generatetopicmindmap':
                                return $this->generateTopicMindMap($this->getParam('id'));

                        case 'searchforum':
                                return $this->searchForum($this->getParam('term', NULL), $this->getParam('forum', 'all'));

                        case 'rebuildtopic':
                                return $this->rebuildTopic($this->getParam('id'));

                        case 'moderatepost':
                                return $this->moderatePost($this->getParam('id'));

                        case 'moderatepostdeleteconfirm':
                                return $this->deletePostConfirm();

                        case 'translationajax':
                                return $this->translationAjax();

                        case 'loadtranslation':
                                return $this->loadTranslation($this->getParam('id'), $this->getParam('lang'));

                        case 'usersubscription':
                                return $this->usersubscription();

                        default:
                                return $this->forumHome();
                }
        }

        /**
         * Method to show the 'Home Page' of the Discussion Forum - listing all
         * forums in a context and the last post
         *
         * @access public
         * @return string Template - forum_list.php
         *
         */
        public function forumHome() {

                // Check if link comes internal to forum or not.
                if (isset($_SERVER['HTTP_REFERER']) && substr_count($_SERVER['HTTP_REFERER'], 'type=workgroup') > 0 && $this->getParam('type') != 'context') {
                        return $this->nextAction('workgroup');
                }
                $forumNum = $this->objForum->getNumForums($this->contextCode);
                if ($forumNum == 0) {
                        $newforum = $this->objForum->autoCreateForum($this->contextCode, $this->contextTitle);
                        return $this->nextAction('forum', array('id' => $newforum));
                } else {
                        $allForums = $this->objForum->showAllForums($this->contextCode);
                        $this->setVarByRef('forums', $allForums);
                        return 'forum_list.php';
                }
        }

        /**
         * Get email messages from mail server and create posts from the retrieved messages.
         * 
         * @access public
         */
        function pullMail() {
                $this->objDbConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $objTopicSubScription = $this->getObject('dbtopicsubscriptions', 'forum');
                //get the config parameters
                $emailHost = $this->objDbConfig->getValue('forum_mail_host', 'forum');
                $emailPort = $this->objDbConfig->getValue('forum_email_port', 'forum');
                $emailOptions = $this->objDbConfig->getValue('forum_email_options', 'forum');
                $emailUserName = $this->objDbConfig->getValue('forum_inbox_username', 'forum');
                $emailPassword = $this->objDbConfig->getValue('forum_inbox_password', 'forum');
                $emailCatchAll = $this->objConfig->getValue('forum_inbox_catchall', 'forum');
                /**
                 * Create the email getter object
                 */
                $this->emailBox = new AttachmentReader($emailHost, $emailPort, $emailOptions, $emailUserName, $emailPassword, '');
                //get the number of messages
                $numMessages = $this->emailBox->getNumMessages();
                if ($numMessages > 0) {
                        for ($index = 1; $index <= $numMessages; $index++) {
                                $emailDetails = $this->emailBox->getEmailDetails($index);
                                //get the eMail address of the user
                                $userEmail = $emailDetails['sender'];
                                //get user information using the eMail address
                                if ($this->objUser->valueExists('emailaddress', $userEmail)) {
                                        //get the users information
                                        $userDetails = $this->objUser->getRow('emailaddress', $userEmail);
                                        //get the user ID
                                        $userId = $userDetails['userid'];
                                        //get the message subject to be used for retrieving the topic
                                        $eMailSubject = $emailDetails['subject'];
                                        $end = strpos($eMailSubject, '-');
                                        //extract the topic ID from the message subject
                                        $topic_id = 'gen' . substr($eMailSubject, $end + 1, strlen($eMailSubject));
                                        $topicDetails = $this->objTopic->getTopicDetails($topic_id);
                                        if (count($topicDetails) > 0) {
                                                //get the topic's forum ID
                                                $forum_id = $topicDetails['forum_id'];
                                                if ($objTopicSubScription->isSubscribedToTopic($topic_id, $userId) || $this->objForumSubscriptions->isSubscribedToForum($forum_id, $userId)) {
                                                        //get the forum details
                                                        $forumDetails = $this->objForum->getForum($forum_id);
                                                        // Get the message body to be used as post text.
                                                        $post_text = $emailDetails['messageBody'];
                                                        //Cleaning up the messge by removing all characters from the eMail reply
                                                        $lastOcc = strpos($post_text, strtolower('@end'), 0);
                                                        $cleanMessage = substr($emailDetails['messageBody'], 0, $lastOcc);
                                                        //get the first post of the topic to be used as the post parent
                                                        $postParent = $topicDetails['first_post'];
                                                        $firstPostDetails = $this->objPost->getPostWithText($postParent);
                                                        //get the first topic's title and assing it to the reply
                                                        $post_title = $firstPostDetails['post_title'];
                                                        //language
                                                        $language = $firstPostDetails['language'];
                                                        //check if message is not empty after cleaning it up
                                                        if (!empty($cleanMessage)) {
                                                                if ($objTopicSubScription->isSubscribedToTopic($topic_id, $userDetails['userid']) || $this->objUserContext->isContextMember($userId, $this->contextCode && $forumDetails['forum_visible'] == 'Y')) {
                                                                        //save thte message as a post
                                                                        $this->saveReply(NULL, $postParent, $forum_id, $topic_id, $post_title, $cleanMessage, $language, $userId);
                                                                        $this->emailBox->deleteEmail($index);
                                                                }
                                                        }
                                                } else {
                                                        //if the user is not subscribed, delete the message
                                                        $this->emailBox->deleteEmail($index);
                                                }
                                        } else {
                                                //Delete the message if it is from a non existant topic
                                                $this->emailBox->deleteEmail($index);
                                        }
                                } else {
                                        $this->emailBox->deleteEmail($index);
                                        //Generate an error indicating that the user does not exist;
                                }
                        }
                }
        }

        /**
         * View a single forum - with a list of all topics in that forum
         *
         * @param string $id - Record ID of the Forum
         * @return string Template - forum_view.php
         */
        public function showForum($id) {
                $forum = $this->objForum->getForum($id);

                $this->objMenuTools->addToBreadCrumbs(array($forum['forum_name']));
                $this->setVar('breadcrumbs', array($forum['forum_name']));

                if ($this->ignoreGroupMembership == 'false') {
                        // Check if type is being passed for workgroup, else redirect to get the type
                        if ($forum['forum_workgroup'] != NULL && $this->getParam('type') != 'workgroup') {
                                return $this->nextAction('workgroup');
                        }

                        // Check if user has access to workgroup forum
                        if ($this->usingWorkGroupsFlag && ($forum['forum_workgroup'] != NULL) && !($this->objWorkGroupUser->memberOfWorkGroup($this->userId, $forum['forum_workgroup']) || $this->objUser->isContextLecturer($this->userId, $this->contextCode))) {
                                return $this->nextAction('noaccess', array('id' => $forum['forum_workgroup']));
                        }
                }
                // Check if the forum exists, if not, go to the Forum Home Page
                if ($forum == '') {
                        return $this->forumHome();
                }

                $this->setVarByRef('forumid', $id);
                $this->setVarByRef('forum', $forum);

                // Get Order and Sorting Values
                $order = $this->getParam('order', $this->getSession('sortorder', 'date'));

                $direction = $this->getParam('direction', $this->getSession('sortdirection', 'asc'));


                // Set as Session
                $this->setSession('sortorder', $order);
                $this->setSession('sortdirection', $direction);

                // Flag to Forum Class
                $this->objForum->order = $order;
                $this->objForum->direction = $direction;

                $page = $this->getParam('page', 1);

                $limitPerPage = 30;

                // Prevent Users from adding alphabetical items to page
                if (!is_numeric($page)) {
                        $page = 1;
                }

                // Prevent URL by hacking
                // If page limit is too high, set to 1
                if ($page > $this->objTopic->getNumForumPages($id, $limitPerPage, FALSE)) {
                        $page = 1;
                }

                $limit = ' LIMIT ' . ($page - 1) * $limitPerPage . ', ' . $limitPerPage;



                $paging = $this->objTopic->prepareTopicPagingLinks($id, $page, $limitPerPage);
                $this->setVarByRef('paging', $paging);

                $allTopics = $this->objTopic->showTopicsInForum($id, $this->userId, $forum['archivedate'], $order, $direction, NULL, $limit);
                $topicsNum = count($allTopics);

                //add to activity log
                if ($this->eventsEnabled) {
                        $message = $this->objUser->getsurname() . " " . $this->objLanguage->languageText('mod_forum_hasentered', 'forum') . " " . $forum['forum_name'];
                        $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                            'link' => $this->uri(array()),
                            'contextcode' => $this->contextCode,
                            'author' => $this->objUser->fullname(),
                            'description' => $message));
                }
                $this->setVarByRef('topicsNum', $topicsNum);
                $this->setVarByRef('topics', $allTopics);
                return 'forum_view.php';
        }

        /**
         * Post a new message. This shows the form to do that.
         *
         * @param string $id - Record ID of the Forum message will be posted in
         * @return string Template - forum_newtopic.php
         */
        public function newTopicForm($id) {
                $forum = $this->objForum->getForum($id);
                // Start checking whether to show the link
                // Check if the forum is locked
                if ($forum['forumlocked'] != 'Y') {
                        // Check if students can start topic
                        if ($forum['studentstarttopic'] == 'Y') {
                                $returnTemplate = 'forum_newtopic.php';

                                // Else check if user is lecturer or admin
                        } else if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                $returnTemplate = 'forum_newtopic.php';
                        } else {
                                $returnTemplate = 'forum_studentaccess.php';
                        }
                } else {
                        $returnTemplate = 'forum_studentaccess.php';
                }
                $discussionTypes = $this->objDiscussionType->getDiscussionTypes();
                $this->setVarByRef('forumid', $id);
                $this->setVarByRef('forum', $forum);
                $this->setVarByRef('discussionTypes', $discussionTypes);

                // Check if form is a result of server-side validation or not'
                if ($this->getParam('message') == 'missing') {
                        $details = $this->getSession($this->getParam('tempid'));
                        $this->setVarByRef('details', $details);
                        $temporaryId = $details['temporaryId'];
                        $this->setVar('mode', 'fix');
                } else {
                        $temporaryId = $this->objUser->userId() . '_' . mktime();
                        $this->setVar('mode', 'new');
                }
                $this->setVarByRef('temporaryId', $temporaryId);
                $numTopicSubscriptions = $this->objTopicSubscriptions->getNumTopicsSubscribed($id, $this->objUser->userId());
                $this->setVarByRef('numTopicSubscriptions', $numTopicSubscriptions);
                $forumSubscription = $this->objForumSubscriptions->isSubscribedToForum($id, $this->objUser->userId());
                $this->setVarByRef('forumSubscription', $forumSubscription);
                return $returnTemplate;
        }

        /**
         * Save the new posted message in the forum
         *
         * @return redirects to the topic with a message to indicate the topic has been saved
         */
        public function saveNewTopic() {

                $attachment_id = $_POST['attachment'];
                $tempPostId = $_POST['temporaryId'];
//                $this->saveTempAttachmentIfAny($tempPostId);
                $forum_id = $_POST['forum'];
                $type_id = $_POST['discussionType'];
                $post_parent = 0;
                $post_title = $_POST['title'];
                $post_text = $_POST['message'];
                $language = 'en';
                $original_post = 1; // YES
                // Cater for Tangents
                if (isset($_POST['replytype'])) {
                        $replyType = $_POST['replytype'];
                } else {
                        $replyType = NULL;
                }
                // Some Server side validation - Redirect Form - Check details of the post
                // Validation
                //     - Remove Tags
                //     - &nbsp; becomes [nothing]
                //     - trim whitespace
                if (trim(strip_tags(str_replace('&nbsp;', '', $post_text))) == '') {
                        // Capture details to put in array - Preserve User's work
                        $details = array('replytype' => $replyType, 'type' => $type_id, 'title' => $post_title, 'language' => $language, 'temporaryId' => $_POST['temporaryId']);
                        // set array as a session
                        $this->setSession($_POST['temporaryId'], $details);
                        // If tangent, redirect to post reply form
                        if ($replyType == 'tangent') {
                                return $this->nextAction('postreply', array('id' => $_POST['parent'], 'type' => $this->forumtype, 'message' => 'missing', 'tempid' => $_POST['temporaryId']));
                        } else { // Redirect to Start New Topic Form
                                return $this->nextAction('newtopic', array('id' => $forum_id, 'type' => $this->forumtype, 'message' => 'missing', 'tempid' => $_POST['temporaryId']));
                        }
                } else {
                        $this->unsetSession($_POST['temporaryId']);
                }

                if (isset($_POST['replytype']) && $_POST['replytype'] == 'tangent') {
                        $topicCheck = $this->objTopic->getRow('id', $_POST['topic']);
                        if ($topicCheck['topic_tangent_parent'] == '0') {
                                $tangentParent = $_POST['topic'];
                        } else {
                                $tangentParent = $topicCheck['topic_tangent_parent'];
                        }
                        $post_tangent_parent = $_POST['parent'];
                } else {
                        $tangentParent = '0';
                        $post_tangent_parent = 0;
                }
                $topic_id = $this->objTopic->insertSingle(
                        $forum_id, $type_id, $tangentParent, // tangent parent
                        $this->userId, $post_title
                );

                $this->objForum->updateLastTopic($forum_id, $topic_id);
                $post_id = $this->objPost->insertSingle($post_parent, $post_tangent_parent, $forum_id, $topic_id, $this->userId);
                $this->saveTempAttachmentIfAny($post_id, $attachment_id);
                $this->handleAttachments($post_id, $attachment_id);
                $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $original_post, $this->userId);
                $this->objTopic->updateFirstPost($topic_id, $post_id);
                $this->objForum->updateLastPost($forum_id, $post_id);
                // Insert topic details into lucene search
                $this->objTopic->insertTopicSearch($topic_id, $post_title, $post_text, $this->userId, $forum_id);
                // Handle Sticky Topics
                if (isset($_POST['stickytopic']) && $_POST['stickytopic'] == '1') {
                        $this->objTopic->makeTopicSticky($topic_id);
                }
                // Attachment Handling
//                $this->handleAttachments($post_id, $_POST['temporaryId']);
                // Email Post
                $forumDetails = $this->objForum->getForum($forum_id);
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
                                // Now subscribe user to topic
                                $this->objTopicSubscriptions->subscribeUserToTopic($topic_id, $this->objUser->userId());
                        } else if ($_POST['subscriptions'] == 'nosubscriptions') { // Else remove subscription from topic
                                $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $this->objUser->userId());
                        }
                }
                if ($forumDetails['subscriptions'] == 'Y') {
                        //http://localhost/nextgen/index.php?module=forum&action=postreply&id=gen8Srv57Nme40_1&type=context
                        $replyUrl = $this->uri(array('action' => 'flatview', 'id' => $topic_id));
                        $emailSuccess = $this->objForumEmail->sendEmail($topic_id, $post_title, $post_text, $forumDetails['forum_name'], $this->userId, $replyUrl);
                        $emailSuccess = NULL;
                } else {
                        $emailSuccess = NULL;
                }
                // End Email Post
                return $this->nextAction('viewtopic', array('message' => 'save', 'id' => $topic_id, 'post' => $post_id, 'type' => $this->forumtype, 'email' => $emailSuccess));
        }

        /**
         * Method to redirect user to a type of topic view
         * There are three types of topic views, and this is stored as a preference (session).
         * This method picks up which session exists, and then redirects to the appropriate view
         * If no session is stored, it selects the flat view format.
         * @param string $topic_id Record Id of the topic
         * @param string $message Message to give to user
         * @param string $post Record Id of the post to highlight
         */
        public function viewTopic($topic_id, $message = NULL, $post = NULL) {
                $action = $this->getSession('forumdisplay', 'flatview');
                return $this->nextAction($action, array('id' => $topic_id, 'message' => $message, 'post' => $post, 'type' => $this->forumtype, 'type' => $this->forumtype));
        }

        /**
         * Method to show a thread in a tree format with indentations
         *
         * @param $topic_id Record Id of the Topic
         */
        public function showThread($topic_id) {
                // Store View as a Preference
                $this->setSession('forumdisplay', 'thread');
                // Get details on the topic
                $post = $this->objPost->getRootPost($topic_id);
                $this->objTopic->updateTopicViews($topic_id);
                // Check if the topic exists, else call an error message
                if ($post == NULL) {
                        // error message, post doesn't exist
                        return $this->nextAction(NULL, array('error' => 'topicdoesntexist', 'type' => $this->forumtype));
                } else {
                        // Send the topic to the template
                        $this->setVarByRef('post', $post);
                        $forum = $this->objForum->getForum($post['forum_id']);
                        // Check if user has access to workgroup forum else redirect
                        $this->checkWorkgroupAccessOrRedirect($forum);
                        // Check if forum is locked - if true - disable / editing replies
                        if ($this->objForum->checkIfForumLocked($post['forum_id'])) {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                                $this->objPost->forumLocked = TRUE;
                                $this->setVar('forumlocked', TRUE);
                        } else {
                                $this->setVar('forumlocked', FALSE);
                                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                        $this->objPost->showModeration = TRUE;
                                }
                        }
                        if ($post['topic_tangent_parent'] == '0') {
                                $tangentsTable = $this->objTopic->showTangentsTable($post['topic_id']);
                                $this->setVarByRef('tangentsTable', $tangentsTable);
                        }
                        // Check if Topic is locked
                        if ($post['status'] == 'CLOSE') {

                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                        }
                        // Check if ratings allowed in Forum
                        if ($forum['ratingsenabled'] == 'Y') {
                                $this->objPost->forumRatingsArray = $this->objForumRatings->getForumRatings($post['forum_id']);
                                $this->objPost->showRatings = TRUE;
                                $this->setVar('showRatingsForm', TRUE);
                        } else {
                                $this->setVar('showRatingsForm', FALSE);
                                $this->objPost->showRatings = FALSE;
                        }
                        // Create the indented thread
                        $thread = $this->objPost->displayThread($topic_id);
                        // Send the indented thread to the template
                        $this->setVarByRef('thread', $thread);
                        // Send the Record Id to the template
                        $this->setVarByRef('topic_id', $topic_id);
                        // Form with Drop down to switch between flat, threaded and single view
                        $this->setVarByRef('changeDisplayForm', $this->objTopic->showChangeDisplayTypeForm($topic_id, 'thread'));
                        // Mark the Topic as read for the current user if user is logged in
                        if ($this->isLoggedIn) {
                                $this->objTopicRead->markTopicRead($topic_id, $this->userId);
                        }
                        // Bread Crumbs
                        $forumLink = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'])));
                        $forumLink->link = $forum['forum_name'];
                        $this->objMenuTools->addToBreadCrumbs(array($forumLink->show(), $post['post_title']));
                        // return the template
                        return 'forum_topic_threadedview.php';
                }
        }

        /**
         * This shows a topic post by post, one at a time
         * @param string $topic_id Record Id of the topic
         */
        public function showSingleThread($topic_id) {
                // Store View as a Preference
                $this->setSession('forumdisplay', 'singlethreadview');
                $this->objTopic->updateTopicViews($topic_id);
                $post = $this->objPost->getRootPost($topic_id);
                if ($post == NULL) {
                        // error message, post doesn't exist
                        return $this->nextAction(NULL, array('error' => 'topicdoesntexist', 'type' => $this->forumtype));
                } else {
                        $this->setVarByRef('post', $post);
                        $forum = $this->objForum->getForum($post['forum_id']);
                        // Check if user has access to workgroup forum else redirect
                        $this->checkWorkgroupAccessOrRedirect($forum);
                        // Check if forum is locked - if true - disable / editing replies
                        if ($this->objForum->checkIfForumLocked($post['forum_id'])) {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                                $this->objPost->forumLocked = TRUE;
                                $this->setVar('forumlocked', TRUE);
                        } else {
                                $this->setVar('forumlocked', FALSE);
                                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                        $this->objPost->showModeration = TRUE;
                                }
                        }
                        // Check if ratings allowed in Forum
                        if ($forum['ratingsenabled'] == 'Y') {
                                $this->objPost->forumRatingsArray = $this->objForumRatings->getForumRatings($post['forum_id']);
                                $this->objPost->showRatings = TRUE;
                                $this->setVar('showRatingsForm', TRUE);
                        } else {
                                $this->setVar('showRatingsForm', FALSE);
                                $this->objPost->showRatings = FALSE;
                        }
                        // Check if Topic is locked
                        if ($post['status'] == 'CLOSE') {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                        }
                        // Check if Topic is a tangent
                        if ($post['topic_tangent_parent'] == '0') {
                                $tangentsTable = $this->objTopic->showTangentsTable($post['topic_id']);
                                $this->setVarByRef('tangentsTable', $tangentsTable);
                        }
                        if ($this->getParam('post') == '') {
                                $postDisplay = $this->objPost->displayPost($post);
                                $highlightPost = $post['post_id'];
                        } else {
                                $requestedPost = $this->objPost->getPostWithText($this->getParam('post'));
                                $postDisplay = $this->objPost->displayPost($requestedPost);
                                $highlightPost = $this->getParam('post');
                        }
                        $this->setVarByRef('postDisplay', $postDisplay);
                        $thread = $this->objPost->generateTopicPostsTree($topic_id, $highlightPost);
                        $this->setVarByRef('thread', $thread);
                        $this->setVarByRef('highlightPost', $highlightPost);
                        // Send the Record I
                        $this->setVarByRef('topic_id', $topic_id);
                        $this->setVarByRef('objTopic', $this->objTopic);
                        $this->setVarByRef('changeDisplayForm', $this->objTopic->showChangeDisplayTypeForm($topic_id, 'singlethreadview'));
                        // Mark the Topic as read for the current user if user is logged in
                        if ($this->isLoggedIn) {
                                $this->objTopicRead->markTopicRead($topic_id, $this->userId);
                        }
                        // Bread Crumbs
                        $forumLink = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'])));
                        $forumLink->link = $forum['forum_name'];
                        $this->objMenuTools->addToBreadCrumbs(array($forumLink->show(), $post['post_title']));
                        return 'forum_topic_singleview.php';
                }
        }

        /**
         * Ajax Method to Load the Translation
         *
         */
        function loadTranslation($postId, $language) {
                $post = $this->objPostText->getTranslatedPost($postId, $language);
                $objLanguageCode = & $this->getObject('languagecode', 'language');
                if ($post != FALSE) {
                        if ($post['original_post'] == '1') {
                                $text = '<strong class="warning">Viewing Original Post Made in ' . $objLanguageCode->getLanguage($language) . '</strong><br />';
                        } else {
                                $text = '<strong class="warning">Viewing Translation of Post in <em>' . $objLanguageCode->getLanguage($language) . '</em></strong><br />';
                        }
                        $text .= $post['post_text'];
                        echo $text;
                } else {
                        echo 'Could not find translation or post';
                }
        }

        /**
         * Dynamically remove a post
         */
        function removePost() {
                //get the post id
                $post_id = $this->getParam('postid');
                //get the topic id
                $topic_id = $this->getParam('topic_id');
                if (!empty($post_id)) {
                        //if poost id is not empty, gt the post details
                        $postDetails = $this->objPost->getPostWithText($post_id);
                        //if user is logged in, get the topic details
                        if ($this->objUser->isLoggedIn()) {
                                $topicDetails = $this->objTopic->getTopicDetails($topic_id);
                                //get the topic replies
                                $replies = $topicDetails['replies'];
                                //if the topic exits, get the number of replies and subtract one
                                if (isset($topicDetails)) {
                                        $values = array(
                                            'replies' => $replies - 1
                                        );
                                        //if the post is successfuly removed, update the forum
                                        if ($this->objPost->delete('id', $post_id)) {
                                                $this->objForum->updateForumAfterDelete($postDetails['id']);
                                                //if the number of replies is successfuly updated, end function
                                                if ($this->objTopic->update('id', $topic_id, $values, 'tbl_forum_topic')) {
                                                        die();
                                                }
                                        }
                                }
//                    $values = array(
//                        
//                    );
//                        $this->objTopic->update('topic_id',$topic_id);
//                var_dump($this->objPost->getPostForumDetails($post_id));
//                die();
                        }
                }
//        die();
        }

        /**
         * This shows a topic with all posts in a flat view based on date posted, without indentations
         * @param $topic_id Record Id of the Topic
         */
        public function showFlatView($topic_id) {
                // Store View as a Preference
                $this->setSession('forumdisplay', 'flatview');
                // Update Views
                $this->objTopic->updateTopicViews($topic_id);
                // Get details on the topic
                $post = $this->objPost->getRootPost($topic_id);
//DEREK EDITED HERE                echo '<pre>'; var_dump($post); echo '</pre>';
                // Check if the topic exists, else call an error message
                if ($post == NULL) {
                        // error message, post doesn't exist
                        return $this->nextAction(NULL, array('error' => 'topicdoesntexist', 'type' => $this->forumtype));
                } else {
                        // Send the topic to the template
                        $this->setVarByRef('post', $post);
                        $forum = $this->objForum->getForum($post['forum_id']);
                        // Check if user has access to workgroup forum else redirect
                        $this->checkWorkgroupAccessOrRedirect($forum);
                        // Check if forum is locked - if true - disable / editing replies
                        if ($this->objForum->checkIfForumLocked($post['forum_id'])) {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                                $this->objPost->forumLocked = TRUE;
                                $this->setVar('forumlocked', TRUE);
                        } else {
                                $this->setVar('forumlocked', FALSE);
                                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                        $this->objPost->showModeration = TRUE;
                                }
                        }
                        // Check if ratings allowed in Forum
                        if ($forum['ratingsenabled'] == 'Y') {
                                $this->objPost->forumRatingsArray = $this->objForumRatings->getForumRatings($post['forum_id']);
                                $this->objPost->showRatings = TRUE;
                                $this->setVar('showRatingsForm', TRUE);
                        } else {
                                $this->setVar('showRatingsForm', FALSE);
                                $this->objPost->showRatings = FALSE;
                        }
                        if ($post['topic_tangent_parent'] == '0') {
                                $tangentsTable = $this->objTopic->showTangentsTable($post['topic_id']);
                                $this->setVarByRef('tangentsTable', $tangentsTable);
                        }
                        // Check if Topic is locked
                        if ($post['status'] == 'CLOSE') {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                        }
                        // Create the indented thread
                        $thread = $this->objPost->displayFlatThread($topic_id);
                        // Send the indented thread to the template
                        $this->setVarByRef('thread', $thread);
                        // Send the Record Id to the template
                        $this->setVarByRef('topic_id', $topic_id);
                        $this->setVarByRef('changeDisplayForm', $this->objTopic->showChangeDisplayTypeForm($topic_id, 'flatview'));
                        // Mark the Topic as read for the current user if user is logged in
                        if ($this->isLoggedIn) {
                                $this->objTopicRead->markTopicRead($topic_id, $this->userId);
                        }
                        // Bread Crumbs
                        $forumLink = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'])));
                        $forumLink->link = $forum['forum_name'];
                        $this->objMenuTools->addToBreadCrumbs(array($forumLink->show(), $post['post_title']));
                        $this->setVar('breadcrumbs', array($forumLink->show(), $post['post_title']));
                        // return the template
                        return 'forum_topic_flatview.php';
                }
        }

        /**
         * Method to show the Form to reply to a topic
         *
         * $post - Record Id of the Post you are replying to
         */
        public function showTopicReplyForm($post) {
                // Set Variables to FALSE
                // A copy of the post you are replying to appears on top
                // Without these lines, users will be abled to click on links to edit and reply
                $this->objPost->repliesAllowed = FALSE;
                $this->objPost->editingPostsAllowed = FALSE;
                // Get the Post
                $post = $this->objPost->getPostWithText($post);
                // Get details of the Forum
                $forum = $this->objForum->getForum($post['forum_id']);
                // Check if user has access to workgroup forum else redirect
                $this->checkWorkgroupAccessOrRedirect($forum);
                if ($forum['forumlocked'] == 'Y') {
                        return $this->nextAction('viewtopic', array('message' => 'cantreplyforumlocked', 'id' => $post['topic_id'], 'post' => $post, 'type' => $this->forumtype));
                } else if ($post['status'] == 'CLOSE') {
                        return $this->nextAction('viewtopic', array('message' => 'cantreplytopiclocked', 'id' => $post['topic_id'], 'post' => $post, 'type' => $this->forumtype));
                } else {
                        $postDisplay = $this->objPost->displayPost($post);
                        $this->setVarByRef('post', $post);
                        $this->setVarByRef('postDisplay', $postDisplay);
                        $forum = $this->objForum->getForum($post['forum_id']);
                        $this->setVarByRef('forum', $forum);
                        // Check if form is a result of server-side validation or not'
                        if ($this->getParam('message') == 'missing') {
                                $details = $this->getSession($this->getParam('tempid'));
                                $this->setVarByRef('details', $details);
                                $temporaryId = $details['temporaryId'];
                                $this->setVar('mode', 'fix');
                        } else {
                                $temporaryId = $this->objUser->userId() . '_' . mktime();
                                $this->setVar('mode', 'new');
                        }
                        $this->setVarByRef('temporaryId', $temporaryId);
                        // Get the number of topics a user is subscribed to
                        $numTopicSubscriptions = $this->objTopicSubscriptions->getNumTopicsSubscribed($post['forum_id'], $this->objUser->userId());
                        $this->setVarByRef('numTopicSubscriptions', $numTopicSubscriptions);
                        // Check whether the user is subscribed to the current topic
                        $topicSubscription = $this->objTopicSubscriptions->isSubscribedToTopic($post['topic_id'], $this->objUser->userId());
                        $this->setVarByRef('topicSubscription', $topicSubscription);
                        // Check whether the user is subscribed to the current forum
                        $forumSubscription = $this->objForumSubscriptions->isSubscribedToForum($post['forum_id'], $this->objUser->userId());
                        $this->setVarByRef('forumSubscription', $forumSubscription);
                        return 'forum_postreply.php';
                }
        }

        /**
         * Method to save a reply to a topic
         */
        public function saveReply($attachment_id = NULL, $postParent = NULL, $forum_id = NULL, $topic_id = NULL, $post_title = NULL, $post_text = NULL, $language = NULL, $user_id = NULL) {

                $tempPostId = $this->objUser->userId() . '_' . mktime()/* $_POST['temporaryId'] */;
                //set the temporary ID so it can be used by other functions
                $this->setVarByref('attachment_tempid', $tempPostId);
                //get the attachment id
                if ($attachment_id == NULL) {
                        $attachment_id = $this->getParam('attachment');
                }
                //reply type
                $replyType = $this->objLanguage->languageText('word_reply', 'system');
                //parentID
                if ($postParent == NULL) {
                        $postParent = $this->getParam('parent');
                }
                $post_tangent_parent = 0;
                //

                $parentPostDetails = $this->objPost->getRow('id', $postParent);
                //get the forum ID
                if ($forum_id == NULL) {
                        $forum_id = $this->getParam('forum_id');
                }
                //get topic ID
                if ($topic_id == NULL) {
                        $topic_id = $this->getParam('topicid');
                }
                //we need this because IE is failing to pass it over
                $this->setSession('current_topic_id', $topic_id);
                $type_id = $this->getSession('discussionType'); //$_POST['discussionType'];
                //get the post title
                if ($post_title == NULL) {
                        $post_title = $this->getParam('posttitle');
                }
                //get the message if message is not supplied in as parameter
                if ($post_text == NULL) {
                        $post_text = $this->getParam('message');
                }
                //get the lanugage.
                if ($language == NULL) {
                        $language = $this->getParam('lang');
                }
//                $language = 'en';
                if (strlen($postParent) == 0) {
                        $original_post = 1;
                } else {
                        $original_post = 0;
                }
                //user ID
                if ($user_id == NULL) {
                        $user_id = $this->userId;
                }
                $level = $parentPostDetails['level'];
                $post_id = $this->objPost->insertSingle($postParent, $post_tangent_parent, $forum_id, $topic_id, $user_id, $level);
                $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $original_post, $user_id);
                $this->objTopic->updateLastPost($topic_id, $post_id);
                $this->objForum->updateLastPost($forum_id, $post_id);
//                // Email Post
                $forumDetails = $this->objForum->getForum($forum_id);
                $topicSubscription = $this->objTopicSubscriptions->isSubscribedToTopic($topic_id, $user_id);
                $forumSubscription = $this->objForumSubscriptions->isSubscribedToForum($forum_id, $user_id);
//                //get the forum
                $forum = $this->objForum->getForum($forum_id);
//                
                if (!empty($attachment_id) || $attachment_id != NULL) {
                        //set the attachment ID to be used by other functions
                        $this->setVarByRef('attachment_id', $attachment_id);
                        $this->saveTempAttachmentIfAny($post_id, $attachment_id);
                        $this->handleAttachments($post_id, $attachment_id);
                }
                // Manage Subscriptions
                if (isset($forum['subscriptions'])) {
                        if ($forum['subscriptions'] == 'forumsubscribe') { // First check subscriptions to forum
                                if (!$forumSubscription) { // If not subscribed to forum, subscribe user now.
                                        $this->objForumSubscriptions->subscribeUserToForum($forum_id, $user_id);
                                }
                        } else if ($forum['subscriptions'] == 'topicsubscribe') { // Now check if the user wants to subscribe to the topic
                                if ($forumSubscription) { // If user was subscribed to forum, remove subscription
                                        $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $user_id);
                                }
                                // Now subscribe user to topic, if the user was not subscribed to the topic
                                if (!$topicSubscription) {
                                        $this->objTopicSubscriptions->subscribeUserToTopic($topic_id, $user_id);
                                }
                        } else if ($forum['subscriptions'] == 'nosubscriptions') { // Else remove subscription from topic
                                $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $user_id);
                        }
                }
                $forumDetails['subscriptions'] = "Y";
                if ($forumDetails['subscriptions'] == 'Y') {
                        $replyUrl = $this->uri(array('action' => 'viewtopic', 'id' => $topic_id, 'post' => $post_id, 'context' => $this->contextCode), 'forum');
                        $emailSuccess = NULL;
                } else {
                        $emailSuccess = NULL;
                }
                echo $this->objPost->saveMailJob($postParent, $post_title, $post_text, $forumDetails['forum_name'], $user_id, $replyUrl);
        }

        /**
         * Send eMails that have not been sent yet
         */
        function runMailJobs() {
                $objPuller = $this->getObject('dbforum_emailpuller', 'forum');
                $eMails = $objPuller->getAll();
                foreach ($eMails as $eMail) {
                        if (!$eMail['sent']) {
                                $fields = array(
                                    'sent' => TRUE
                                );
                                $this->objForumEmail->sendEmail($eMail['post_parent'], $eMail['post_title'], $eMail['post_text'], $eMail['forum_name'], $eMail['user_id'], $eMail['reply_url']);
                                $objPuller->update('id', $eMail['id'], $fields);
                        }
                }
        }

        /**
         * Method to get a post and prepare it for editing form
         * @param string $id Record Id of the Post
         */
        public function editPost($id) {
                $id = $this->getParam('id');
                $post = $this->objPost->getPostWithText($id);
                $this->setVarByRef('post', $post);
                $forum = $this->objForum->getForum($post['forum_id']);
                // Check if user has access to workgroup forum else redirect
                $this->checkWorkgroupAccessOrRedirect($forum);
                $this->setVarByRef('forum', $forum);
        }

        /**
         * Method to update a post after editing
         */
        public function updatePost() {
                $tempPostId = $_POST['temporaryId'];
                $this->saveTempAttachmentIfAny($tempPostId);
                $post_id = $_POST['post_id'];
                $title = $_POST['title'];
                $text = $_POST['message'];
                $topic_id = $_POST['topic'];
                $this->objPostText->updatePostText($post_id, $title, $text);
                $this->objPostAttachments->deleteAttachments($post_id);
                $this->handleAttachments($post_id, $_POST['temporaryId']);
                return $this->nextAction('viewtopic', array('message' => 'postupdated', 'id' => $topic_id, 'post' => $post_id, 'type' => $this->forumtype));
        }

        // --------------------------------------------------------------------------------

        /**
         * Forum Administration Page
         *
         * Shows the list of forums in a context, allows you to create/edit forum settings
         */
        public function forumAdministration() {
                $contextForums = $this->objForum->getAllContextForums($this->contextCode);
                $this->setVarByRef('forumsList', $contextForums);
                $visibleForums = $this->objForum->getContextForums($this->contextCode);
                $this->setVarByRef('visibleForums', $visibleForums);
                $defaultForum = $this->objForum->getDefaultForum($this->contextCode);
                $this->setVarByRef('defaultForum', $defaultForum);
                return 'forum_administration.php';
        }

        /**
         * Method to show the form to create a new forum
         */
        public function createForum() {
                $action = 'create';
                $this->setVarByRef('action', $action);
                return 'forum_createedit.php';
        }

        /**
         * Method to Save a Newly Created Forum
         */
        public function saveForum() {
                $forum_context = $this->contextCode;
                $forum_workgroup = '';
                $forum_name = $this->getParam('name');
                $forum_description = $this->getParam('description');
                $defaultForum = 'N';
                $forum_visible = $this->getParam('visible');
                $forumLocked = 'N';
                $ratingsenabled = $this->getParam('ratings');
                $studentstarttopic = $this->getParam('student');
                $attachments = $this->getParam('attachments');
                $subscriptions = $this->getParam('subscriptions');
                // Needs to be worked on
                $moderation = 'N';
                $forum = $this->objForum->insertSingle($forum_context, $forum_workgroup, $forum_name, $forum_description, $defaultForum, $forum_visible, $forumLocked, $ratingsenabled, $studentstarttopic, $attachments, $subscriptions, $moderation);
                return $this->nextAction('administration', array('message' => 'forumcreated', 'id' => $forum));
        }

        /**
         * Method to get a forum's details in order to edit them
         *
         * @param $id Record Id of the Forum
         */
        public function editForum($id) {
                $forum = $this->objForum->getForum($id);
                // Check if Forum exists
                if ($forum == false) {
                        return $this->nextAction('administration');
                } else {
                        $action = 'edit';
                        $this->setVarByRef('action', $action);
                        $this->setVarByRef('forum', $forum);
                        return 'forum_createedit.php';
                }
        }

        /**
         * Update a forum's settings
         *
         * @return string Template - redirects back to forum admin
         */
        public function editForumSave() {
                $forum_id = $this->getParam('id');
                $forum_name = stripslashes($this->getParam('name'));
                $forum_description = stripslashes($this->getParam('description'));
                $forum_visible = $this->getParam('visible');
                $forumLocked = $this->getParam('lockforum');
                if ($forum_visible == 'default') {
                        $forum_visible = 'Y';
                }
                $ratingsenabled = $this->getParam('ratings');
                $studentstarttopic = $this->getParam('student');
                $attachments = $this->getParam('attachments');
                $subscriptions = $this->getParam('subscriptions');
                // Needs to be worked on
                $moderation = 'N';
                // Archiving
                $doArchive = $this->getParam('archivingRadio');
                if ($doArchive == 'Y') {
                        $archiveDate = $this->getParam('archivedate');
                } else {
                        $archiveDate = NULL;
                }
                $this->objForum->updateSingle($forum_id, $forum_name, $forum_description, $forum_visible, $forumLocked, $ratingsenabled, $studentstarttopic, $attachments, $subscriptions, $moderation, $archiveDate);
                return $this->nextAction('administration', array('message' => 'forumupdated', 'id' => $forum_id));
        }

        /**
         * Dynamically change the forum visibility
         */
        function updateForumSetting() {
                $forum_id = $this->getParam('forum_id');
                $forum_status = $this->getParam('forum_status');
                $forum_setting = $this->getParam('forum_setting');
                if ($this->objUser->isLoggedIn()) {
                        echo $this->objForum->updateForum($forum_id, $forum_setting, $forum_status);
                } else {
                        die();
                }
                die();
        }

        /**
         * Method to show a form for deleting a forum
         * @param string $id Record Id of the Forum
         */
        public function deleteForum($id) {
                $forum = $this->objForum->getForum($id);
                $this->setVarByRef('forum', $forum);
                return 'forum_deleteforum.php';
        }

        /**
         * Method to Delete a Forum following confirmation
         */
        public function deleteForumConfirm() {
                // Get the Id
                $id = $this->getParam('id');
                // Get Forum Details
                $forum = $this->objForum->getForum($id);
                if ($forum == NULL) { // Check if Forum Exists
                        return $this->nextAction('administration', array('message' => 'forumdoesnotexist'));
                } else if ($forum['defaultforum'] == 'Y') { // Check if Default Forum
                        return $this->nextAction('administration', array('message' => 'cannotdeletedefaultforum'));
                } else {
                        $result = $this->objForum->deleteForum($id); // Delete Forum

                        if ($result) { // Check whether deletion was successful
                                return $this->nextAction('administration', array('message' => 'forumdeleted', 'forum' => $forum['forum_name']));
                        } else {
                                return $this->nextAction('administration', array('message' => 'couldnotdeleteforum'));
                        }
                }
        }

        /**
         * Method to update the visibility status of a forum
         */
        public function updateForumVisibility() {
                $id = $this->getParam('id');
                $visibility = $this->getParam('visible');
                // Get Forum Details
                $forum = $this->objForum->getForum($id);
                // Check if changes made
                if ($forum['forum_visible'] == strtoupper($visibility)) {
                        return $this->nextAction('administration', array('forum' => $id, 'message' => 'visibilityunchanged'));
                } else {
                        $result = $this->objForum->updateForumVisibility($id, $visibility);

                        if ($result) {
                                return $this->nextAction('administration', array('forum' => $id, 'message' => 'visibilityupdated'));
                        } else {
                                return $this->nextAction('administration', array('forum' => $id, 'message' => 'errorvisibilityupdate'));
                        }
                }
        }

        /**
         * Set a Forum as the default Forum
         *
         * @return string Template - redirects back to forum administration
         */
        public function setDefaultForum() {
                $this->objForum->setDefaultForum($_POST['forum'], $this->contextCode);
                return $this->nextAction('administration', array('message' => 'defaultforumchanged'));
        }

        // --------------------------------------------------------------------------------------------------------------

        /**
         * Show the list attachments that have already include for a post
         *
         * Template that shows in the iframe when a user is busy with a topic/reply
         *
         * @param $id Record Id of the Temporary ID
         */
        public function showAttachments($id) {
                $this->setVarByRef('id', $id);
                $files = $this->objTempAttachments->getList($id);
                $this->setVarByRef('files', $files);
                $this->setVar('pageSuppressIM', TRUE);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressContainer', TRUE);
                $this->setVar('suppressFooter', TRUE);
                return 'forum_attachments.php';
        }

        /**
         * Method to save temp attahc
         */
        public function saveTempAttachmentIfAny($temp_id, $attachment_id) {
                $userId = $this->objUser->userId();
                $dateLastUpdated = mktime();
//                $attachment_id = $this->getVar('attachment_id');
                if ($attachment_id != '') {
                        $this->objTempAttachments->insertSingle($temp_id, $attachment_id, $userId, $dateLastUpdated);
                } else {
                        echo "<h4>failed to insert values into database<h4>";
                }
                $this->unsetSession('temporaryId');
//                return $this->nextAction('attachments', array('id' => $temp_id, 'attachment' => $attachment_id));
        }

        /**
         * Method to add an attachment to a file
         */
        public function saveAttachment($temp_id, $attachment_id) {

//                $temp_id = $this->getVar('attachment_tempid');
                $userId = $this->objUser->userId();
                $dateLastUpdated = mktime();
//                $attachment_id = $this->getVar('attachment_id');
//                if ($attachment_id != '') {
                echo $this->objTempAttachments->insertSingle($temp_id, $attachment_id, $userId, $dateLastUpdated);
//                }  else {
//                        echo "<h1>Failed to insert values into database</h1>";
//                }
//                return $this->nextAction('attachments', array('id' => $temp_id, 'attachment' => $attachment_id));
        }

        /**
         *
         *
         */
        public function deleteAttachment($temp_id, $attachment_id) {
                $this->objTempAttachments->deleteAttachment($temp_id, $attachment_id);
                return $this->nextAction('attachments', array('id' => $temp_id));
        }

        /**
         * Make Temporary Attachments Permanent
         *
         * When a user starts a topic, a temporary id is given for the post to record attachments
         * This function is run once the topic/reply is posted and moves the record to the post attachments
         * table and deletes the temporary record
         *
         * @param $postId The Records Permanent Post Id
         * @param $temporaryId The Record's Temporary Post Id
         */
        private function handleAttachments($postId, $temporaryId) {
                $files = $this->objTempAttachments->getQuickList($postId);
//                print_r($this->objTempAttachments->getQuickList($temporaryId));
                foreach ($files AS $file) {
                        $this->objPostAttachments->insertSingle($postId, $file['attachment_id'], $this->userId, mktime());
                }
                $this->objTempAttachments->deleteTemps($temporaryId);
//                return;
        }

        /**
         * Download attachments
         *
         * @param string $id - Record ID of the Attachment
         * @param string $topic - Record ID of the Topic
         * @return string Template - forum_view.php
         */
        public function downloadAttachment($id, $topic) {
                $files = $this->objPostAttachments->downloadAttachment($id);
                if (count($files) > 0) {
                        $location = $this->objFiles->getFullFilePath($files[0]['id']);
                        header('Content-Disposition: attachment; filename="' . $files[0]['filename'] . '"');
                        readfile($location);
                        //header('Location:'.$location); // Todo - Force Download
                } else {
                        return $this->nextAction('thread', array('id' => $topic, 'type' => $this->forumtype, 'message' => 'invalidattachment'));
                }
        }

        /**
         * Shows the form to translate a post
         * @param string $post_id Record Id of the Post
         */
        public function translatePost($post_id) {
                // These are dummy permission to prevent the display of reply, edit and translate links
                $this->objPost->repliesAllowed = FALSE;
                $this->objPost->editingPostsAllowed = FALSE;
                $this->objPost->forumLocked = TRUE;
                $post = $this->objPost->getPostWithText($post_id);
                $postDisplay = $this->objPost->displayPost($post);
                // Get a list of languages for the current post
                // Will be deleted from the list of languages to translate in
                $postLanguages = $this->objPostText->getPostLanguages($post_id);
                $forum = $this->objForum->getForum($post['forum_id']);
                $this->checkWorkgroupAccessOrRedirect($forum);
                // Send the topic to the template
                $this->setVarByRef('post', $post);
                $this->setVarByRef('postDisplay', $postDisplay);
                $this->setVarByRef('postLanguages', $postLanguages);
                return 'forum_translatepost_form.php';
        }

        /**
         * Method to process a form to save translation
         */
        public function saveTranslation() {
                $post_id = $_POST['post'];
                $post_title = $_POST['title'];
                $post_text = $_POST['message'];
                $language = $_POST['language'];
                $original_post = 0; // Set to Zero. this is a translation, not an original post
                $newId = $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $original_post, $this->userId);
                return $this->nextAction('viewtranslation', array('id' => $newId, 'message' => 'translationsaved', 'type' => $this->forumtype));
        }

        /**
         * Method to view the translation of a post in another language
         */
        public function viewtranslation($postTextId) {
                $post = $this->objPost->getPostInLanguage($postTextId);
                $postDisplay = $this->objPost->displayPost($post);
                // Send the topic to the template
                $this->setVarByRef('post', $post);
                $this->setVarByRef('postDisplay', $postDisplay);
                $forum = $this->objForum->getForum($post['forum_id']);
                $this->checkWorkgroupAccessOrRedirect($forum);
                $this->setVarByRef('forum', $forum);
                return 'forum_viewtranslation.php';
        }

        /**
         * Method to save a change in the topic status
         */
        public function changeTopicStatus() {
                $topic_id = $_POST['topic'];
                $status = $_POST['topic_status'];
                $reason = $_POST['reason'];
                $this->objTopic->changeTopicStatus($topic_id, $status, $reason, $this->userId);
                return $this->nextAction('viewtopic', array('message' => 'statuschanged', 'id' => $topic_id, 'type' => $this->forumtype));
        }

        /**
         * Method to show the settings for a forum (usually at the bottom of posts)
         * @param string $forum_id Record Id of the Forum
         * @param boolean $showForumJump Flag to indicate whether the switching between forum has to be shown
         * @return string Forum Settings
         */
        public function showForumFooter($forum_id, $showForumJump = TRUE) {
                $returnString = '';
                $fieldset = $this->newObject('fieldset', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('textinput', 'htmlelements');
                // By pass switching between forums if we are dealing with workgroups
                if ($this->forumtype != 'workgroup') {
                        $forumForm = new form('forumform', 'index.php');
                        $forumForm->method = 'GET';
                        $forums = $this->objForum->getContextForums($this->contextCode);
                        if (count($forums) > 1 && $showForumJump) {
                                $dd = new dropdown('id');
                                foreach ($forums AS $forum) {
                                        if ($forum['id'] != $forum_id) {
                                                $dd->addOption($forum['id'], $forum['forum_name']);
                                        }
                                }
                                $dd->setSelected($forum_id);
                                $button = new button('');
                                $button->setValue('Go');
                                $button->setToSubmit();
                                $forumForm->addToForm('<p align="center">' . $this->objLanguage->languageText('mod_forum_jumptoanotherforum', 'forum') . ': ' . $dd->show() . ' ' . $button->show() . '</p>');
                                $module = new textinput('module', 'forum');
                                $module->fldType = 'hidden';
                                $action = new textinput('action', 'forum');
                                $action->fldType = 'hidden';
                                $forumForm->addToForm($module->show() . $action->show());
                                $fieldset->addContent($forumForm->show());
                                $returnString .= $fieldset->show();
                        } else {
                                if ($showForumJump) {
                                        $returnString .= '<hr />';
                                }
                        }
                }
                $forumTable = $this->newObject('htmltable', 'htmlelements');
                $iconsFieldset = $this->newObject('fieldset', 'htmlelements');
                $iconsFieldset->setExtra(' class="forumBlock"');
                $iconsFieldset->setLegend($this->objLanguage->languageText('mod_forum_meaningoficons', 'forum'));
                $icon = $this->getObject('geticon', 'htmlelements');
                $icon->setIcon('lock', NULL, 'icons/forum/');
                $iconsFieldset->addContent($icon->show() . ' - ' . $this->objLanguage->languageText('mod_forum_topicislocked', 'forum'));
                $icon->setIcon('unlock', NULL, 'icons/forum/');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' - ' . $this->objLanguage->languageText('mod_forum_unlockedtopicexplained', 'forum'));
                $icon->setIcon('unreadletter');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' -  ' . $this->objLanguage->languageText('mod_forum_newunreadtopic', 'forum'));
                $icon->setIcon('readletter');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' - ' . $this->objLanguage->languageText('mod_forum_readtopic', 'forum'));
                $icon->setIcon('readnewposts');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' - ' . $this->objLanguage->languageText('mod_forum_readtopicnewreplies', 'forum'));
                $forumTable->addCell($iconsFieldset->show(), '50%');
                // -----------------------------------------------
                $forumFieldset = $this->newObject('fieldset', 'htmlelements');
                $forumFieldset->setExtra(' class="forumBlock"');
                $forumFieldset->setLegend($this->objLanguage->languageText('mod_forum_forumSettings', 'forum'));
                $forumDetails = $this->objForum->getRow('id', $forum_id);
                if ($forumDetails['forumlocked'] == 'Y') {
                        $forumFieldset->addContent('<div class="noRecordsMessage"><strong>' . $this->objLanguage->languageText('mod_forum_forumislocked', 'forum') . '<br /><br />' . $this->objLanguage->languageText('mod_forum_allfunctionalitydisabled', 'forum') . '</strong></div>');
                } else {
                        $forumFieldset->addContent('<ul>');
                        if ($forumDetails['studentstarttopic'] == 'Y') {
                                $forumFieldset->addContent('<li>' . ucfirst($this->objLanguage->code2Txt('mod_forum_studentsstartopics', 'forum')) . '</li>');
                        } else {
                                $forumFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_forum_onlylecturersstarttopics', 'forum') . '</li>');
                        }
                        if ($forumDetails['attachments'] == 'Y') {
                                $forumFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_forum_usersuploadattachments', 'forum') . '</li>');
                        } else {
                                $forumFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_forum_userscannotupload', 'forum') . '</li>');
                        }
                        if ($forumDetails['ratingsenabled'] == 'Y') {
                                $forumFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_forum_usersrateposts', 'forum') . '</li>');
                        } else {
                                $forumFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_forum_ratingpostsdisabled', 'forum') . '</li>');
                        }
                        $forumFieldset->addContent('</ul>');
                }
                // Bottom Fieldset
                $forumLinksFieldset = $this->getObject('fieldset', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $link = new link($this->uri(array('action' => 'administration')));
                $link->link = $this->objLanguage->languageText('mod_forum_forumadministration', 'forum');
                $forumLinksFieldset->addContent($link->show());
                $forumLinksFieldset->addContent(' / ');
                $link = new link($this->uri(array('action' => 'editforum', 'id' => $forum_id)));
                $link->link = $this->objLanguage->languageText('mod_forum_editforumsettings', 'forum');
                $forumLinksFieldset->addContent($link->show());
                $forumLinksFieldset->addContent(' / ');
                $link = new link($this->uri(array('action' => 'statistics', 'id' => $forum_id)));
                $link->link = $this->objLanguage->languageText('mod_forum_forumstatistics', 'forum');
                $forumLinksFieldset->addContent($link->show());
                if ($this->objUser->isCourseAdmin($this->contextCode) && $this->forumtype != 'workgroup' && $this->isLoggedIn) {
                        $showSettings = $forumLinksFieldset->show();
                } else if ($this->forumtype != 'workgroup') {
                        $link2 = new link($this->uri(array('type' => 'context')));
                        $link2->link = ucwords($this->objLanguage->code2Txt('mod_forum_returntocontextforums', 'forum'));
                        $showSettings = '<fieldset><p>' . $link2->show() . '</p></fieldset>';
                } else {
                        $link = new link($this->uri(NULL, 'workgroup'));
                        $link->link = ucwords($this->objLanguage->code2Txt('mod_forum_returntoworkgroup', 'forum'));
                        $link2 = new link($this->uri(array('type' => 'context')));
                        $link2->link = ucwords($this->objLanguage->code2Txt('mod_forum_returntocontextforums', 'forum'));
                        $showSettings = '<p>' . $link->show() . ' / ' . $link2->show() . '</p>';
                }
                $forumTable->addCell($forumFieldset->show() . $showSettings, '50%');
                $returnString .= $forumTable->show();
                return $returnString;
        }

        /**
         * Method to save the ratings of a post
         */
        public function savePostRatingDown() {
                $post_id = $this->getParam('post_id');
                $currentRating = $this->objPostRatings->getPostRatings($post_id);
                $smt = "WHERE post_id='{$post_id}' AND userid='{$this->objUser->userId()}'";
                $values = $this->objPostRatings->fetchAll($smt);
                if ($currentRating >= 1) {
                        $currentRating -= 1;
                        if (count($values) == 0) {
                                return $this->objPostRatings->insertRecord($post_id, $currentRating, $this->userId);
                        }
                } else {
                        return FALSE;
                }
        }

        /**
         * increase the post rating by one
         */
        function savePostRatingUp() {
                $post_id = $this->getParam('post_id');
                if (isset($post_id)) {
                        $currentRating = $this->objPostRatings->getPostRatings($post_id);
                        $smt = "WHERE post_id='{$post_id}' AND userid='{$this->objUser->userId()}'";
//                        echo $smt;
                        $values = $this->objPostRatings->fetchAll($smt);
//                        echo count($values);
                        if (empty($currentRating)) {
                                $currentRating = 1;
                                if (count($values) == 0) {
                                        return $this->objPostRatings->insertRecord($post_id, $currentRating, $this->userId);
                                }
                        } else {
                                $currentRating += 1;
                                if (count($values) == 0) {
                                        return $this->objPostRatings->insertRecord($post_id, $currentRating, $this->userId);
                                }
                        }
                }
        }

        /**
         * Method to show statistics for a discussion forum
         *
         * @param string $forum_id Record Id of the forum
         */
        public function showForumStatistics($forum_id) {
                $this->setVarByRef('id', $forum_id);
                $forumDetails = $this->objForum->getForum($forum_id);
                $this->setVarByRef('forumDetails', $forumDetails);
                $forumSummaryStats = $this->objForumStats->getStats($forum_id, $this->contextCode);
                $this->setVarByRef('forumSummaryStats', $forumSummaryStats);
                $tangents = $this->objForumStats->getTangentsNum($forum_id);
                $this->setVarByRef('tangents', $tangents);
                $posters = $this->objForumStats->getPosters($forum_id, $this->contextCode);
                $this->setVarByRef('posters', $posters);
                $posterStats = $this->objForumStats->getPostersNum($forum_id, $this->contextCode);
                $this->setVarByRef('posterStats', $posterStats);
                $posterTopics = $this->objForumStats->getPosterTopics($forum_id, $this->contextCode);
                $this->setVarByRef('posterTopics', $posterTopics);
                $posterTangents = $this->objForumStats->getPosterTangents($forum_id, $this->contextCode);
                $this->setVarByRef('posterTangents', $posterTangents);
                $userRatesOther = $this->objForumStats->getUserRateOtherPosts($forum_id);
                $this->setVarByRef('userRatesOther', $userRatesOther);
                $userRatesSelf = $this->objForumStats->getUserRateSelfPosts($forum_id);
                $this->setVarByRef('userRatesSelf', $userRatesSelf);
                $userWordCount = $this->objForumStats->getUserWordCount($forum_id);
                $this->setVarByRef('userWordCount', $userWordCount);
                //$this->objForumStats
                return 'forum_statistics.php';
        }

        /**
         * Method to show Moderation Options for a Topic
         * @param string $id Record Id of the Topic
         *
         */
        public function moderateTopic($id) {
                $topic = $this->objTopic->getTopicDetails($id);
                // If topic does not exist, redirect with message
                if ($topic == FALSE) {
                        return $this->nextAction(NULL, array('error' => 'moderatetopicdoesnotexist'));
                }
                $this->setVarByRef('topic', $topic);
                $otherForums = $this->objForum->otherForums($topic['forum_id'], $this->contextCode);
                $this->setVarByRef('otherForums', $otherForums);
                if ($topic['topic_tangent_parent'] == '0') {
                        $tangents = $this->objTopic->getTangents($id);
                        $topicParent = '';
                } else {
                        $tangents = array(); // Create Array of No Values
                        $topicParent = $this->objTopic->getTopicDetails($topic['topic_tangent_parent']);
                }
                $forum = $this->objForum->getForum($topic['forum_id']);
                $this->setVarByRef('tangents', $tangents);
                $this->setVarByRef('topicParent', $topicParent);
                $this->setVarByRef('forum', $forum);
                // Bread Crumbs
                $forumLink = new link($this->uri(array('action' => 'forum', 'id' => $topic['forum_id'])));
                $forumLink->link = $forum['forum_name'];
                $topicLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id'])));
                $topicLink->link = $topic['post_title'];
                $this->objMenuTools->addToBreadCrumbs(array($forumLink->show(), $topicLink->show(), $this->objLanguage->languageText('mod_forum_moderatetopic', 'forum')));
                return 'forum_moderatetopic.php';
        }

        /**
         * Method to control all functionality relating to a moderator deleting a topic
         *
         */
        public function moderateDeleteTopic() {
                // Get Topic Info
                $topicInfo = $this->objTopic->getTopicDetails($_POST['id']);
                // Check if a delete request is confirmed
                if ($_POST['delete'] != '1') {
                        $returnArray = array('id' => $_POST['id'], 'message' => 'deletecancelled');

                        // Attempt to get default option for tangents and add to array to preserve user's work.
                        if (isset($_POST['tangentoption'])) {
                                $returnArray['option'] = $_POST['tangentoption'];
                        }
                        // If not return to moderation page, with message, delete cancelled.
                        return $this->nextAction('moderatetopic', $returnArray);
                } else {
                        // Check if we are deleting a tangent.
                        if ($topicInfo['topic_tangent_parent'] != '0') {
                                $results = $this->objTopic->deleteTopic($_POST['id']);
                                $this->objForum->updateForumAfterDelete($topicInfo['forum_id']);
                                return $this->nextAction('viewtopic', array('id' => $topicInfo['topic_tangent_parent'], 'message' => 'tangentdeleted'));
                        } else { // Deleting a topic with tangents
                                if (isset($_POST['tangentoption']) && $_POST['tangentoption'] == 'delete') {
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        $results = $this->objTopic->deleteTangents($_POST['id']);
                                        $this->objForum->updateForumAfterDelete($topicInfo['forum_id']);
                                        return $this->nextAction('forum', array('id' => $topicInfo['forum_id'], 'message' => 'topictangentsdeleted'));
                                } else if (isset($_POST['tangentoption']) && $_POST['tangentoption'] == 'move') {
                                        $this->objTopic->moveTangentsToAnotherTopic($_POST['id'], $_POST['topicmove']);
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        $this->objForum->updateForumAfterDelete($topicInfo['forum_id']);
                                        return $this->nextAction('forum', array('id' => $topicInfo['forum_id'], 'message' => 'topicdeletedtangentsmoved'));
                                } else if (isset($_POST['tangentoption']) && $_POST['tangentoption'] == 'newtopic') {
                                        $this->objTopic->moveAllTangentsToRootTopic($_POST['id']);
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        return $this->nextAction('forum', array('id' => $topicInfo['forum_id'], 'message' => 'topicdeletedtangentsmovedtoroot'));
                                } else { // Simply delete topic - has no tangents
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        $this->objForum->updateForumAfterDelete($topicInfo['forum_id']);
                                        return $this->nextAction('forum', array('id' => $topicInfo['forum_id'], 'message' => 'topicdeleted'));
                                }
                        }
                }
        }

        /**
         * Method to move a topic as a tangent of another topic - moderation option
         */
        public function moderateMoveTangent() {
                $id = $_POST['id'];
                $topicmove = $_POST['topicmove'];
                // Get Topic Info
                $topicInfo = $this->objTopic->getTopicDetails($_POST['id']);
                $this->objTopic->moveTopicToTangent($id, $topicmove);
                $this->objTopic->moveTangentsToAnotherTopic($id, $topicmove);
                return $this->nextAction('forum', array('id' => $topicInfo['forum_id'], 'message' => 'topicmovetotangent', 'topic' => $id));
        }

        public function moderateMoveNewForum() {
                $topic = $this->getParam('id');
                $forum = $this->getParam('forummove');

                if ($topic != '' && $forum != '') {
                        $this->objTopic->switchTopicForum($topic, $forum);
                }
                return $this->nextAction('forum', array('id' => $forum, 'message' => 'topicmovedtonewforum', 'topic' => $topic));
        }

        /**
         * Method to move a tangent as a new topic
         */
        public function moderateMoveNewTopic() {
                // Get Topic Info
                $topicInfo = $this->objTopic->getTopicDetails($_POST['id']);
                // Check if a delete request is confirmed
                if ($_POST['delete'] != '1') {
                        // If not return to moderation page, with message, delete cancelled.
                        return $this->nextAction('moderatetopic', array('id' => $_POST['id'], 'message' => 'movenewtopiccancelled'));
                } else {
                        $this->objTopic->moveTangentToRootTopic($_POST['id']);
                        return $this->nextAction('forum', array('id' => $topicInfo['forum_id'], 'message' => 'tangentmovedtonewtopic', 'topic' => $_POST['id']));
                }
        }

        /**
         * Method to Update the Sticky Status of a Topic
         */
        public function moderateStickyTopic($topic_id, $status) {
                $id = $topic_id;
                $sticky = $status;
                if ($sticky == '1') {
                        $this->objTopic->makeTopicSticky($id);
                } else {
                        $this->objTopic->removeTopicSticky($id);
                }
                return $this->nextAction('moderatetopic', array('id' => $id, 'message' => 'topicstickychanged'));
        }

        /**
         * Method to determine if we are dealing with a workgoup forum via session Id, and whether the user is part of that workgroup (has access)
         */
        public function getWorkGroupDetails() {
                // Get the Workgroup
                $this->workgroupId = $this->objWorkGroup->getWorkgroupId();
                $this->workgroupDescription = $this->objWorkGroup->getDescription($this->workgroupId);
                if ($this->objWorkGroupUser->memberOfWorkGroup($this->userId, $this->workgroupId) || $this->objUser->isContextLecturer($this->userId, $this->contextCode)) {
                        $this->forumtype = 'workgroup';
                } else {
                        $this->forumtype = 'context';
                }
        }

        /**
         * Method to get the forum for a workgroup or create one that does not exist. Also checks if user is falsely trying to create one.
         */
        public function checkWorkgroupForum() {
                $this->workgroupForum = $this->objForum->getWorkgroupForum($this->contextCode, $this->workgroupId);
                if ($this->workgroupForum == NULL) {
                        $this->workgroupForum = $this->objForum->autoCreateWorkgroupForum($this->contextCode, $this->workgroupId, $this->workgroupDescription);
                }
                if ($this->forumtype == 'workgroup') {
                        return $this->nextAction('forum', array('id' => $this->workgroupForum, 'type' => $this->forumtype));
                } else {
                        //echo 'not member of forum';
                        return $this->nextAction('noaccess', array('id' => $this->workgroupForum));
                        // Fixup or redirect to forum home
                }
        }

        /**
         * Method to determine if a forum is for a workgrup and whether the user has access to it. If not redirect to a page saying so.
         *
         * @param array $forum Array containing details of the forum
         */
        public function checkWorkgroupAccessOrRedirect($forum) {
                if ($this->usingWorkGroupsFlag && ($forum['forum_workgroup'] != NULL) && !($this->objWorkGroupUser->memberOfWorkGroup($this->userId, $forum['forum_workgroup']) || $this->objUser->isContextLecturer($this->userId, $this->contextCode))) {
                        return $this->nextAction('noaccess', array('id' => $forum['forum_workgroup']));
                } else {
                        $this->forumtype = 'workgroup';
                        $this->setVarByRef('forumtype', $this->forumtype);
                        $this->objPost->forumtype = $this->forumtype;
                }
                if ($forum['forum_workgroup'] == NULL) {
                        $this->forumtype = 'context';

                        $this->setVarByRef('forumtype', $this->forumtype);
                        $this->objPost->forumtype = $this->forumtype;
                }
        }

        /**
         * Method to show that the user has no access to a workgroup forum
         */
        public function showNoAccess($id) {
                return 'forum_workgroup_noaccess.php';
        }

        /**
         * Method to View a Mind Map of topics in a Forum
         * @param string $id Record Id of the Forum
         */
        public function viewMindMap($id) {
                $forum = $this->objForum->getForum($id);
                $this->setVarByRef('forum', $forum);
                $this->setVar('map', $this->uri(array('action' => 'generatemindmap', 'id' => $id)));
                return 'forum_mindmap.php';
        }

        /**
         * Method to Generate the XML of Mind Map of topics in a Forum
         * @param string $id Record Id of the Forum
         */
        public function generateMindMap($id) {
                $forum = $this->objForum->getForum($id);
                // Get Order and Sorting Values
                $order = $this->getParam('order', $this->getSession('sortorder', 'date'));
                $direction = $this->getParam('direction', $this->getSession('sortdirection', 'asc'));
                // Flag to Forum Class
                $this->objForum->order = $order;
                $this->objForum->direction = $direction;
                $allTopics = $this->objTopic->showTopicsInForum($id, $this->userId, $forum['archivedate'], $order, $direction);
                $this->loadClass('treemenu', 'tree');
                $this->loadClass('treenode', 'tree');
                $this->loadClass('freemindmap', 'tree');
                $this->loadClass('htmllist', 'tree');
                $treeMenu = new treemenu();
                $rootNode = & new treenode(array('text' => $forum['forum_name']), NULL);
                // start an array
                $nodeArray = array();
                // Reference the array element with the record id
                $nodeArray[0] = & $rootNode;
                foreach ($allTopics as $topic) {
                        $node = & new treenode(array('text' => htmlentities($topic['post_title']), 'link' => $this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id']))), NULL);
                        $nodeArray[0]->addItem($node);
                        // Reference the array element with the record id
                        $nodeArray[$topic['topic_id']] = & $node;
                        if ($topic['tangentcheck'] != '') {
                                $tangents = $this->objTopic->getTangents($topic['topic_id']);
                                foreach ($tangents as $tangent) {
                                        $tangentnode = & new treenode(array('text' => $tangent['post_title'], 'link' => $this->uri(array('action' => 'viewtopic', 'id' => $tangent['id']))), NULL);
                                        $nodeArray[$tangent['topic_tangent_parent']]->addItem($tangentnode);
                                }
                        }
                }
                $treeMenu->addItem($rootNode);
                $tree = &new freemindmap($treeMenu);
                header('content-type:text/xml');
                echo $tree->getMenu();
        }

        /**
         * Method to View a Topic as a Freemind Map
         * @param string $topic_id Record Id of the Topic
         */
        public function viewTopicMindMap($topic_id) {

                // Store View as a Preference
                $this->setSession('forumdisplay', 'viewtopicmindmap');
                // Update Views
                $this->objTopic->updateTopicViews($topic_id);
                // Get details on the topic
                $post = $this->objPost->getRootPost($topic_id);
                $this->setVarByRef('post', $post);
                $forum = $this->objForum->getForum($post['forum_id']);
                // Check if user has access to workgroup forum else redirect
                $this->checkWorkgroupAccessOrRedirect($forum);
                $this->setVar('map', $this->uri(array('action' => 'generatetopicmindmap', 'id' => $topic_id)));
                $this->setVarByRef('changeDisplayForm', $this->objTopic->showChangeDisplayTypeForm($topic_id, 'viewtopicmindmap'));
                // Mark the Topic as read for the current user if user is logged in
                if ($this->isLoggedIn) {
                        $this->objTopicRead->markTopicRead($topic_id, $this->userId);
                }
                // Check if forum is locked - if true - disable / editing replies
                if ($this->objForum->checkIfForumLocked($post['forum_id'])) {
                        $this->objPost->repliesAllowed = FALSE;
                        $this->objPost->editingPostsAllowed = FALSE;
                        $this->objPost->forumLocked = TRUE;
                        $this->setVar('forumlocked', TRUE);
                } else {
                        $this->setVar('forumlocked', FALSE);
                }
                // Bread Crumbs
                $forumLink = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'])));
                $forumLink->link = $forum['forum_name'];
                $this->objMenuTools->addToBreadCrumbs(array($forumLink->show(), $post['post_title']));

                return 'forum_topic_mindmap.php';
        }

        /**
         * Method to Generate the Freemind XML for a Topic
         * @param string $topic_id Record Id of the Topic
         */
        public function generateTopicMindMap($topic_id) {
                //************************************
                //         TODO!!! TANGENTS
                //************************************
                // Get the topic as a thread
                $thread = $this->objPost->getThread($topic_id);
                // Load the Tree Classes
                $this->loadClass('treemenu', 'tree');
                $this->loadClass('treenode', 'tree');
                $this->loadClass('freemindmap', 'tree');
                $this->loadClass('htmllist', 'tree');
                // Load Color Generator
                $objColorGenerator = $this->getObject('randomcolorgenerator', 'utilities');
                // Tree Menu for Nodes
                $treeMenu = new treemenu();
                // Load
                $rootNodes = array();
                // start an array - used as a referencing point
                $nodeArray = array();
                // Get Root Post and Topic Details
                $post = $this->objPost->getRootPost($topic_id);
                $this->setVarByRef('post', $post);
                // Replies starts off as allowed
                $repliesAllowed = TRUE;
                // Turn off replies if Topic is Locled
                if ($post['status'] == 'CLOSE') {
                        $repliesAllowed = FALSE;
                }

                // Get Forum Details
                $forum = $this->objForum->getForum($post['forum_id']);
                // Turn off replies if Topic is Locked
                if ($forum['forumlocked'] == 'Y') {
                        $repliesAllowed = FALSE;
                }

                // Loop through Posts to Create Tree
                foreach ($thread as $topic) {
                        // Create an Array with the nodes details
                        $nodeDetails = array();
                        // Add reply link and hook if replies allowed
                        if ($repliesAllowed) {
                                // Link to the Node
                                $nodeDetails['link'] = $this->uri(array('action' => 'postreply', 'id' => $topic['post_id']), NULL);
                                // Add a Hooktext - 'popup text';
                                $nodeDetails['hooktext'] = $this->objLanguage->languageText('mod_forum_freemindfollowlink', 'forum', 'Click on the link to post a reply');
                        }
                        // Randomly generate background color and set for node background
                        $nodeDetails['nodebackgroundcolor'] = $objColorGenerator->generateColor();
                        // Set Edge Width - 8 is nice and thick!
                        $nodeDetails['edgeWidth'] = 8;
                        if ($this->showFullName) {
                                // Start text of the node
                                $text = $topic['firstname'] . ' ' . $topic['surname'] . ' ';
                        } else {
                                $text = $topic['username'] . ' ';
                        }
                        // Check if Start of Topic or Reply
                        if ($topic['post_parent'] == '0') {
                                $message = $this->objLanguage->languageText('mod_forum_startedtopic', 'forum', 'started topic') . ':';
                                $rootNodes[] = $topic['post_id'];
                                $text .= $message . '&#xa;&#xa;' . strip_tags($topic['post_text']);
                                $nodeDetails['fontsize'] = 20;
                                $nodeDetails['fontbold'] = TRUE;
                        } else {
                                $message = $this->objLanguage->languageText('mod_forum_postedareply', 'forum', 'posted a reply') . ':';
                                $nodeDetails['style'] = 'bubble';
                                $text .= $message . '&#xa;&#xa;' . strip_tags($topic['post_text']);
                        }
                        // Add text to Node Array
                        $nodeDetails['text'] = $text;
                        $icons = array();
                        // Add a Cloud Colour - dynamic generation
                        if ($topic['replypost'] != '' && !in_array($topic['post_id'], $rootNodes)) {
                                $nodeDetails['cloud'] = TRUE;
                                $nodeDetails['cloudcolor'] = $objColorGenerator->generateColor();
                        } else {
                                $nodeDetails['cloud'] = FALSE;
                        }
                        // Create a Node
                        $node = & new treenode($nodeDetails);
                        // Check if Start of Topic or Reply
                        // If Start of topic, add to tree menu
                        // Else add as a child node
                        if ($topic['post_parent'] == '0') {
                                $treeMenu->addItem($node);
                        } else {
                                $nodeArray[$topic['post_parent']]->addItem($node);
                        }
                        // Reference the array element with the record id
                        $nodeArray[$topic['post_id']] = & $node;
                }
                $tree = &new freemindmap($treeMenu);
                header('content-type:text/xml');
                echo $tree->getMenu();
        }

        /**
         * Method to update a passthrough login, log user into required course
         */
        public function updatePassThroughLogin() {
                $objForumPassThrough = $this->getObject('forum_passthrough');
                switch ($this->getParam('action')) {
                        case 'forum':
                                $forum = $objForumPassThrough->getContextFromForum($this->getParam('id'));
                                break;
                        case 'viewtopic':
                        case 'flatview':
                        case 'singlethreadview':
                        case 'thread':
                        case 'moderatetopic':
                                $forum = $objForumPassThrough->getContextFromTopic($this->getParam('id'));
                                break;
                        case 'postreply':
                                $forum = $objForumPassThrough->getContextFromPost($this->getParam('id'));
                                break;
                        default:
                                $forum = '';
                                break;
                }
                if ($forum != '' && $forum != FALSE) {
                        $this->contextObject->joinContext($forum);
                }
                return;
        }

        /**
         * Method to Search through the Discussion Forum
         * @param string $term Term to search for
         * @param string $form Forum to search in
         */
        public function searchForum($term, $forum) {
                $errors = array();
                if (trim($term) == '') {
                        $errors[] = 'No Search Term was provided';
                        $term = '';
                }
                if ($forum != 'all') {
                        $forumInfo = $this->objForum->getForum($forum);
                        if ($forumInfo == FALSE) {
                                $errors[] = 'Forum you tried to search does not exist.';
                                $forum = 'all';
                        }
                }
                $objSearch = $this->getObject('forumsearch');
                $objSearch->defaultForum = $forum;
                $objSearch->searchTerm = $term;
                // Only perform search if no errors exist
                if (count($errors) == 0) {
                        $searchResults = $objSearch->searchForum($term, $forum);
                } else {
                        $searchResults = '';
                }
                $this->setVarByRef('searchForm', $objSearch->show());
                $this->setVarByRef('searchTerm', stripslashes($term));
                $this->setVarByRef('searchResults', $searchResults);
                $this->setVarByRef('errors', $errors);
                return 'forum_searchresults.php';
        }

        /**
         * Method to rebuild a topic tree via URL
         * @param string $id Record Id of the Topic
         * @param string $post Record Id of the Post
         */
        public function rebuildTopic($id, $post = NULL) {
                // Check if Topic is Broken
                $this->objPost->detectBrokenTopic($id);
                // Go to Topic
                return $this->nextAction('viewtopic', array('id' => $id, 'post' => $post));
        }

        /**
         *
         *
         *
         */
        public function moderatePost($id) {
                // Get Post Info
                $post = $this->objPost->getPostWithText($id);
                // If it does not exist, redirect with message
                if ($post == NULL) {
                        return $this->nextAction(NULL, array('message' => 'postyoutriedtomoderatedoesnotexist'));
                }
                // Redirect to Topic Moderation if First Post
                if ($post['postleft'] == 1) {
                        return $this->nextAction('moderatetopic', array('id' => $post['topic_id']));
                }
                // Turn Off Moderation Icon
                $this->objPost->showModeration = FALSE;
                $this->objPost->editingPostsAllowed = FALSE;
                // Detect if the topic is broken
                $this->objPost->detectBrokenTopic($post['topic_id']);
                // Turn off replies link
                $this->objPost->repliesAllowed = FALSE;
                // Turn off moderation link
                $this->objPost->showModeration = FALSE;
                $this->objPost->forumLocked = TRUE;
                // Reget the topic
                $post = $this->objPost->getPostWithText($id);
                $postDisplay = $this->objPost->displayPost($post);
                $this->setVarByRef('post', $post);
                $this->setVarByRef('postDisplay', $postDisplay);
                return 'forum_post_delete.php';
        }

        /**
         *
         *
         */
        public function deletePostConfirm() {
                $post = $this->objPost->getPostWithText($_POST['id']);
                if ($post == NULL) {
                        return $this->nextAction(NULL, array('message' => 'postyoutriedtodeletedoesnotexist'));
                }
                if ($_POST['confirmdelete'] == 'Y') {
                        $this->objPost->deletePostAndReplies($_POST['id']);
                        $this->objTopic->updateTopicAfterDelete($post['topic_id']);
                        $this->objForum->updateForumAfterDelete($post['forum_id']);
                        return $this->nextAction('viewtopic', array('id' => $post['topic_id'], 'message' => 'posthasbeendeleted'));
                } else {
                        return $this->nextAction('viewtopic', array('id' => $post['topic_id'], 'post' => $_POST['id'], 'message' => 'postdeletecancelled'));
                }
        }

        /**
         * subscribe a user to the forum
         */
        function subscribeUserToForum() {
                $forum_id = $this->getParam('forum_id');
                $this->objForumSubscriptions->subscribeUserToForum($forum_id, $this->objUser->userId());
                echo "<h1>forumed</h1>";
//                die();
        }

        /**
         * subscribe user to the topic
         */
        function subscribeUserToTopic() {
                $topic_id = $this->getParam('topic_id');
                $forum_id = $this->getParam('forum_id');
                $objTopic = &$this->getObject('dbtopicsubscriptions', 'forum');
                $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $this->objUser->userId());
                if ($objTopic->subscribeUserToTopic($topic_id, $this->objUser->userId())) {
                        echo "<h1>topiced</h1>";
                }
//                die();
        }

        /**
         * subscribe a user to the forum
         */
        function unsubscribeUser() {
                $forum_id = $this->getParam('forum_id');
                $topic_id = $this->getParam('topic_id');
                $objTopic = &$this->getObject('dbtopicsubscriptions', 'forum');
                $objTopic->unsubscribeUserFromTopic($this->objUser->userId(), $topic_id);
                $this->objForumSubscriptions->unsubscribeUserFromForum($forum_id, $this->objUser->userId());
                echo "<h1>unsubscribed</h1>";
        }

        /**
         * Remove the user from the subscription list
         */
        function usersubscription() {
//                if ($this->objUser->isLoggedIn()) {
                //get the the user's choice
                $userChoice = $this->getParam('subscription');
                if (!empty($userChoice)) {
                        switch ($userChoice) {
                                case 'nosubscription':
                                        return $this->unsubscribeUser();
                                        break;
                                case 'subscribetopic':
//                                $topic_id = $this->getParam('topic_id');
                                        return $this->subscribeUserToTopic();
                                        break;
                                case 'subscribetoall':
                                        return $this->subscribeUserToForum();
                                        break;
                        }
//                }
                }
        }

        /**
         * Method to send an eMail alert to users who are subscribed
         * 
         * @param type $subject
         * @param type $message
         * @param type $linkUrl
         */
        function sendEmailAlert($subject, $message, $linkUrl) {
                $recipients = $this->objManageGroups->contextUsers('Students', $this->contextCode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
                $objMailer = $this->getObject('mailer', 'mail');
                $message = html_entity_decode($message);
                $message = trim($message, "\x00..\x1F");
                $message = strip_tags($message);
                $list = array();
                foreach ($recipients as $recipient) {
                        $list[] = $recipient['emailaddress'];
                }
                $objMailer->setValue('to', $list);
                $objMailer->setValue('from', "");
                $objMailer->setValue('fromName', $this->objAltConfig->getSiteName());
                $objMailer->setValue('subject', $title);
                $objMailer->setValue('body', $message);
                $objMailer->setValue('AltBody', $message);
                $objMailer->send();



                $members = $this->objManageGroups->contextUsers('Students', $this->contextCode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
                $objMailer = $this->getObject('mailer', 'mail');
                foreach ($members as $member) {

                        $linkUrl = str_replace("amp;", "", $linkUrl);
                        $objMailer->setValue('to', array($member['emailaddress']));
                        $objMailer->setValue('from', "");
                        $objMailer->setValue('fromName', $this->objAltConfig->getSiteName());
                        $objMailer->setValue('subject', $subject);
                        $objMailer->setValue('body', strip_tags($body));
                        $objMailer->setValue('AltBody', strip_tags($body));

                        $objMailer->send();
                }

                /*
                 * 
                 */

                function randerLink() {

                        $url = urldecode($_REQUEST['url']);
                        $url = checkValues($url);
                        $return_array = array();

                        $base_url = substr($url, 0, strpos($url, "/", 8));
                        $relative_url = substr($url, 0, strrpos($url, "/") + 1);

// Get Data
                        $cc = new cURL();
                        $string = $cc->get($url);
                        $string = str_replace(array("\n", "\r", "\t", '</span>', '</div>'), '', $string);

                        $string = preg_replace('/(<(div|span)\s[^>]+\s?>)/', '', $string);
                        if (mb_detect_encoding($string, "UTF-8") != "UTF-8")
                                $string = utf8_encode($string);


// Parse Title
                        $nodes = extract_tags($string, 'title');
                        $return_array['title'] = trim($nodes[0]['contents']);

// Parse Base
                        $base_override = false;
                        $base_regex = '/<base[^>]*' . 'href=[\"|\'](.*)[\"|\']/Ui';
                        preg_match_all($base_regex, $string, $base_match, PREG_PATTERN_ORDER);
                        if (strlen($base_match[1][0]) > 0) {
                                $base_url = $base_match[1][0];
                                $base_override = true;
                        }

// Parse Description
                        $return_array['description'] = '';
                        $nodes = extract_tags($string, 'meta');
                        foreach ($nodes as $node) {
                                if (strtolower($node['attributes']['name']) == 'description')
                                        $return_array['description'] = trim($node['attributes']['content']);
                        }

// Parse Images
                        $images_array = extract_tags($string, 'img');
                        $images = array();
                        for ($i = 0; $i <= sizeof($images_array); $i++) {
                                $img = trim(@$images_array[$i]['attributes']['src']);
                                $width = preg_replace("/[^0-9.]/", '', $images_array[$i]['attributes']['width']);
                                $height = preg_replace("/[^0-9.]/", '', $images_array[$i]['attributes']['height']);

                                $ext = trim(pathinfo($img, PATHINFO_EXTENSION));

                                if ($img && $ext != 'gif') {
                                        if (substr($img, 0, 7) == 'http://')
                                                ;
                                        else if (substr($img, 0, 1) == '/' || $base_override)
                                                $img = $base_url . $img;
                                        else
                                                $img = $relative_url . $img;

                                        if ($width == '' && $height == '') {
                                                $details = @getimagesize($img);

                                                if (is_array($details)) {
                                                        list($width, $height, $type, $attr) = $details;
                                                }
                                        }
                                        $width = intval($width);
                                        $height = intval($height);


                                        if ($width > 199 || $height > 199) {
                                                if (
                                                        (($width > 0 && $height > 0 && (($width / $height) < 3) && (($width / $height) > .2)) || ($width > 0 && $height == 0 && $width < 700) || ($width == 0 && $height > 0 && $height < 700)
                                                        ) && strpos($img, 'logo') === false) {
                                                        $images[] = array("img" => $img, "width" => $width, "height" => $height, 'area' => ($width * $height), 'offset' => $images_array[$i]['offset']);
                                                }
                                        }
                                }
                        }
                        $return_array['images'] = array_values(($images));
                        $return_array['total_images'] = count($return_array['images']);

                        header('Cache-Control: no-cache, must-revalidate');
                        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                        header('Content-type: application/json');

                        echo json_encode($return_array);
                        exit;
                }

        }

        /**
         * Save a post edit
         * @access public
         */
        function savepostedit() {
                if ($this->objUser->isLoggedIn()) {
                        $_id = $this->getParam('_id');
                        $new_text = $this->getParam('new_text');
                        $postDetails = $this->objPost->getPostWithText($_id);
                        $this->objPostText->updatePostText($postDetails['post_id'], $postDetails['post_title'], $new_text);
                        $objMessage = $this->getObject('timeoutmessage', 'htmlelements');
                        $objMessage->setMessage(substr($this->objLanguage->languageText('mod_forum_postsaved', 'forum'), 0, 25));
                        $objMessage->setTimeOut(10000);
                        echo $objMessage->show();
                        die();
                } else {
                        die();
                }
        }

        /**
         * Show form for editing post text
         */
        function showeditpostpopup() {
                if ($this->objUser->isloggedIn()) {
                        $_id = $this->getParam('_id');
                        $post_id = $this->getParam('post_id');
                        $new_text = $this->getPAram('new_text');
                        $this->ckElement->cssClass = $_id;
                        $this->ckElement->cssId = $post_id;
                        $this->ckElement->value = $new_text;
                        echo $this->ckElement->show();
                } else {
                        die();
                }
        }

}

?>
