<?php

/* -------------------- discussion class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}
// end security check

/**
 * Discussion Controller
 * This class controls all functionality to run the Discussion Discussion module
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package discussion
 * @version 1
 */
class discussion extends controller {

        /**
         *
         * @var string Used to determine whether the discussion is for context
         * or workgroup
         *
         * @access protected
         *
         */
        protected $discussiontype;
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
                $this->objUser = & $this->getObject('user', 'security');
                $this->userId = $this->objUser->userId();
                $this->isLoggedIn = $this->objUser->isLoggedIn();
                $this->objLanguage = & $this->getObject('language', 'language');

                // Discussion Classes
                $this->objDiscussion = & $this->getObject('dbdiscussion');
                $this->objDiscussionType = & $this->getObject('dbdiscussiontypes');
                $this->objTopic = & $this->getObject('dbtopic');
                $this->objPost = & $this->getObject('dbpost');
                $this->objPostText = & $this->getObject('dbposttext');
                $this->objTopicRead = & $this->getObject('dbtopicread');

                // Discussion Attachments
                $this->objTempAttachments = & $this->getObject('dbtempattachments');
                $this->objPostAttachments = & $this->getObject('dbpostattachments');

                // Discussion Ratings
                $this->objDiscussionRatings = & $this->getObject('dbdiscussion_ratings');
                $this->objPostRatings = & $this->getObject('dbpost_ratings');

                // Discussion Email Class
                $this->objDiscussionEmail = & $this->getObject('discussionemail');

                // Load Discussion Subscription classes
                $this->objDiscussionSubscriptions = & $this->getObject('dbdiscussionsubscriptions');
                $this->objTopicSubscriptions = & $this->getObject('dbtopicsubscriptions');

                // Discussion Statistics
                $this->objDiscussionStats = & $this->getObject('discussionstats');

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
                $this->objDiscussionEmail->setContextCode($this->contextCode);

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
                        $this->discussiontype = 'context';
                }
                $this->setVarByRef('discussiontype', $this->discussiontype);

                // Set to post since some links are generated within the post
                $this->objPost->discussiontype = $this->discussiontype;

                // Load Menu Tools Class
                $this->objMenuTools = & $this->getObject('tools', 'toolbar');
                $this->objDateTime = & $this->getObject('dateandtime', 'utilities');
                $this->objFiles = & $this->getObject('dbfile', 'filemanager');
                $this->loadClass('link', 'htmlelements');

                if (strtolower($this->getParam('passthroughlogin')) == 'true') {
                        $this->updatePassThroughLogin();
                }
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->showFullName = $this->objSysConfig->getValue('SHOWFULLNAME', 'discussion');
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
                $this->ignoreGroupMembership = $this->objSysConfig->getValue('IGNORE_GROUP_MEMBERSHIP', 'discussion');
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

                // Check if User is allowed to view discussion without being logged in
                $allowPrelogin = $objSysConfig->getValue('ALLOW_PRELOGIN', 'discussion');

                // If turned off, user requires login for ALL actions
                if ($allowPrelogin != '1') {
                        return TRUE;
                }

                // Else user requires login for some actions
                switch ($this->getParam('action', NULL)) {
                        case NULL:
                        case 'discussion':
                        case 'viewtopic':
                        case 'thread':
                        case 'singlethreadview':
                        case 'flatview':
                        case 'viewtranslation':
                        /*
                          case 'downloadattachment':
                         */
                        case 'searchdiscussion':
                        case 'viewtopicmindmap':
                        case 'generatetopicmindmap':
                        case 'viewmindmap':
                        case 'generatemindmap':
                        case 'loadtranslation':
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
                $this->setLayoutTemplate('discussion_layout.php');
                //$this->setVar('pageSuppressXML', TRUE);

                switch ($action) {
                        case 'savepostratingup' :
                                return $this->savePostRatingUp();

                        case 'savepostratingdown':
                                return $this->savePostRatingDown();

                        case 'updatediscussionsetting':
                                return $this->updatediscussionsetting();

                        case 'test':
                                return 'test.php';
                        case 'discussion':
                                return $this->showDiscussion($this->getParam('id'));

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
                                return $this->discussionAdministration();

                        case 'creatediscussion':
                                return $this->createDiscussion();

                        case 'savediscussion':
                                return $this->saveDiscussion();

                        case 'editdiscussion':
                                return $this->editDiscussion($this->getParam('id'));

                        case 'editdiscussionsave':
                                return $this->editDiscussionSave();

                        case 'deletediscussion':
                                return $this->deleteDiscussion($this->getParam('id'));

                        case 'deletediscussionconfirm':
                                return $this->deleteDiscussionConfirm();

                        case 'changevisibilityconfirm':
                                return $this->updateDiscussionVisibility();

                        case 'setdefaultdiscussion':
                                return $this->setDefaultDiscussion();

                        case 'attachments':
                                return $this->showAttachments($this->getParam('id'), $this->getParam('discussion'));

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

                        /*
                          case 'downloadattachment':
                          return $this->downloadAttachment($this->getParam('id'), $this->getParam('topic'));
                         */

//                        case 'savepostratings':
//                                return $this->savePostRatings();

                        case 'workgroup':
                                return $this->checkWorkgroupDiscussion();

                        case 'noaccess':
                                return $this->showNoAccess($this->getParam('id'));

                        // case 'sendemail':
                        // return $this->sendEmail();

                        case 'statistics':
                                return $this->showDiscussionStatistics($this->getParam('id'));

                        case 'moderatetopic':
                                return $this->moderateTopic($this->getParam('id'));

                        case 'moderate_deletetopic':
                                return $this->moderateDeleteTopic();

                        case 'moderate_movetotangent':
                                return $this->moderateMoveTangent();

                        case 'moderate_movetodiscussion':
                                return $this->moderateMoveNewDiscussion();

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

                        case 'searchdiscussion':
                                return $this->searchDiscussion($this->getParam('term', NULL), $this->getParam('discussion', 'all'));

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
                                return $this->discussionHome();
                }
        }

        /**
         * Method to show the 'Home Page' of the Discussion Discussion - listing all
         * discussions in a context and the last post
         *
         * @access public
         * @return string Template - discussion_list.php
         *
         */
        public function discussionHome() {

                // Check if link comes internal to discussion or not.
                if (isset($_SERVER['HTTP_REFERER']) && substr_count($_SERVER['HTTP_REFERER'], 'type=workgroup') > 0 && $this->getParam('type') != 'context') {
                        return $this->nextAction('workgroup');
                }
                $discussionNum = $this->objDiscussion->getNumDiscussions($this->contextCode);
                if ($discussionNum == 0) {
                        $newdiscussion = $this->objDiscussion->autoCreateDiscussion($this->contextCode, $this->contextTitle);
                        return $this->nextAction('discussion', array('id' => $newdiscussion));
                } else {
                        $allDiscussions = $this->objDiscussion->showAllDiscussions($this->contextCode);
                        $this->setVarByRef('discussions', $allDiscussions);
                        return 'discussion_list.php';
                }
        }

        /**
         * View a single discussion - with a list of all topics in that discussion
         *
         * @param string $id - Record ID of the Discussion
         * @return string Template - discussion_view.php
         */
        public function showDiscussion($id) {
                $discussion = $this->objDiscussion->getDiscussion($id);

                $this->objMenuTools->addToBreadCrumbs(array($discussion['discussion_name']));

                if ($this->ignoreGroupMembership == 'false') {
                        // Check if type is being passed for workgroup, else redirect to get the type
                        if ($discussion['discussion_workgroup'] != NULL && $this->getParam('type') != 'workgroup') {
                                return $this->nextAction('workgroup');
                        }

                        // Check if user has access to workgroup discussion
                        if ($this->usingWorkGroupsFlag && ($discussion['discussion_workgroup'] != NULL) && !($this->objWorkGroupUser->memberOfWorkGroup($this->userId, $discussion['discussion_workgroup']) || $this->objUser->isContextLecturer($this->userId, $this->contextCode))) {
                                return $this->nextAction('noaccess', array('id' => $discussion['discussion_workgroup']));
                        }
                }
                // Check if the discussion exists, if not, go to the Discussion Home Page
                if ($discussion == '') {
                        return $this->discussionHome();
                }

                $this->setVarByRef('discussionid', $id);
                $this->setVarByRef('discussion', $discussion);

                // Get Order and Sorting Values
                $order = $this->getParam('order', $this->getSession('sortorder', 'date'));

                $direction = $this->getParam('direction', $this->getSession('sortdirection', 'asc'));


                // Set as Session
                $this->setSession('sortorder', $order);
                $this->setSession('sortdirection', $direction);

                // Flag to Discussion Class
                $this->objDiscussion->order = $order;
                $this->objDiscussion->direction = $direction;

                $page = $this->getParam('page', 1);

                $limitPerPage = 30;

                // Prevent Users from adding alphabetical items to page
                if (!is_numeric($page)) {
                        $page = 1;
                }

                // Prevent URL by hacking
                // If page limit is too high, set to 1
                if ($page > $this->objTopic->getNumDiscussionPages($id, $limitPerPage, FALSE)) {
                        $page = 1;
                }

                $limit = ' LIMIT ' . ($page - 1) * $limitPerPage . ', ' . $limitPerPage;



                $paging = $this->objTopic->prepareTopicPagingLinks($id, $page, $limitPerPage);
                $this->setVarByRef('paging', $paging);

                $allTopics = $this->objTopic->showTopicsInDiscussion($id, $this->userId, $discussion['archivedate'], $order, $direction, NULL, $limit);
                $topicsNum = count($allTopics);

                //add to activity log
                if ($this->eventsEnabled) {
                        $message = $this->objUser->getsurname() . " " . $this->objLanguage->languageText('mod_discussion_hasentered', 'discussion') . " " . $discussion['discussion_name'];
                        $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                            'link' => $this->uri(array()),
                            'contextcode' => $this->contextCode,
                            'author' => $this->objUser->fullname(),
                            'description' => $message));
                }
                $this->setVarByRef('topicsNum', $topicsNum);
                $this->setVarByRef('topics', $allTopics);
                return 'discussion_view.php';
        }

        /**
         * Post a new message. This shows the form to do that.
         *
         * @param string $id - Record ID of the Discussion message will be posted in
         * @return string Template - discussion_newtopic.php
         */
        public function newTopicForm($id) {
                $discussion = $this->objDiscussion->getDiscussion($id);
                // Start checking whether to show the link
                // Check if the discussion is locked
                if ($discussion['discussionlocked'] != 'Y') {
                        // Check if students can start topic
                        if ($discussion['studentstarttopic'] == 'Y') {
                                $returnTemplate = 'discussion_newtopic.php';

                                // Else check if user is lecturer or admin
                        } else if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                $returnTemplate = 'discussion_newtopic.php';
                        } else {
                                $returnTemplate = 'discussion_studentaccess.php';
                        }
                } else {
                        $returnTemplate = 'discussion_studentaccess.php';
                }
                $discussionTypes = $this->objDiscussionType->getDiscussionTypes();
                $this->setVarByRef('discussionid', $id);
                $this->setVarByRef('discussion', $discussion);
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
                $discussionSubscription = $this->objDiscussionSubscriptions->isSubscribedToDiscussion($id, $this->objUser->userId());
                $this->setVarByRef('discussionSubscription', $discussionSubscription);
                return $returnTemplate;
        }

        /**
         * Save the new posted message in the discussion
         *
         * @return redirects to the topic with a message to indicate the topic has been saved
         */
        public function saveNewTopic() {

                $tempPostId = $_POST['temporaryId'];
//                $this->saveTempAttachmentIfAny($tempPostId);
                $discussion_id = $_POST['discussion'];
                $type_id = $_POST['discussionType'];
                $post_parent = 0;
                $post_title = $_POST['title'];
                $post_text = $_POST['message'];
                $language = $_POST['language'];
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
                                return $this->nextAction('postreply', array('id' => $_POST['parent'], 'type' => $this->discussiontype, 'message' => 'missing', 'tempid' => $_POST['temporaryId']));
                        } else { // Redirect to Start New Topic Form
                                return $this->nextAction('newtopic', array('id' => $discussion_id, 'type' => $this->discussiontype, 'message' => 'missing', 'tempid' => $_POST['temporaryId']));
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
                        $discussion_id, $type_id, $tangentParent, // tangent parent
                        $this->userId, $post_title
                );

                $this->objDiscussion->updateLastTopic($discussion_id, $topic_id);
                $post_id = $this->objPost->insertSingle($post_parent, $post_tangent_parent, $discussion_id, $topic_id, $this->userId);
                $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $original_post, $this->userId);
                $this->objTopic->updateFirstPost($topic_id, $post_id);
                $this->objDiscussion->updateLastPost($discussion_id, $post_id);
                // Insert topic details into lucene search
                $this->objTopic->insertTopicSearch($topic_id, $post_title, $post_text, $this->userId, $discussion_id);
                // Handle Sticky Topics
                if (isset($_POST['stickytopic']) && $_POST['stickytopic'] == '1') {
                        $this->objTopic->makeTopicSticky($topic_id);
                }
                // Attachment Handling
//                $this->handleAttachments($post_id, $_POST['temporaryId']);
                // Email Post
                $discussionDetails = $this->objDiscussion->getDiscussion($discussion_id);
                $discussionSubscription = $this->objDiscussionSubscriptions->isSubscribedToDiscussion($discussion_id, $this->objUser->userId());
                // Manage Subscriptions
                if (isset($_POST['subscriptions'])) {
                        if ($_POST['subscriptions'] == 'discussionsubscribe') { // First check subscriptions to discussion
                                if (!$discussionSubscription) { // If not subscribed to discussion, subscribe user now.
                                        $this->objDiscussionSubscriptions->subscribeUserToDiscussion($discussion_id, $this->objUser->userId());
                                }
                        } else if ($_POST['subscriptions'] == 'topicsubscribe') { // Now check if the user wants to subscribe to the topic
                                if ($discussionSubscription) { // If user was subscribed to discussion, remove subscription
                                        $this->objDiscussionSubscriptions->unsubscribeUserFromDiscussion($discussion_id, $this->objUser->userId());
                                }
                                // Now subscribe user to topic
                                $this->objTopicSubscriptions->subscribeUserToTopic($topic_id, $this->objUser->userId());
                        } else if ($_POST['subscriptions'] == 'nosubscriptions') { // Else remove subscription from topic
                                $this->objDiscussionSubscriptions->unsubscribeUserFromDiscussion($discussion_id, $this->objUser->userId());
                        }
                }
                if ($discussionDetails['subscriptions'] == 'Y') {
                        //http://localhost/nextgen/index.php?module=discussion&action=postreply&id=gen8Srv57Nme40_1&type=context
                        $replyUrl = $this->uri(array('action' => 'flatview', 'id' => $topic_id));
                        $emailSuccess = $this->objDiscussionEmail->sendEmail($topic_id, $post_title, $post_text, $discussionDetails['discussion_name'], $this->userId, $replyUrl);
                        $emailSuccess = NULL;
                } else {
                        $emailSuccess = NULL;
                }
                // End Email Post
                return $this->nextAction('viewtopic', array('message' => 'save', 'id' => $topic_id, 'post' => $post_id, 'type' => $this->discussiontype, 'email' => $emailSuccess));
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
                $action = $this->getSession('discussiondisplay', 'flatview');
                return $this->nextAction($action, array('id' => $topic_id, 'message' => $message, 'post' => $post, 'type' => $this->discussiontype, 'type' => $this->discussiontype));
        }

        /**
         * Method to show a thread in a tree format with indentations
         *
         * @param $topic_id Record Id of the Topic
         */
        public function showThread($topic_id) {
                // Store View as a Preference
                $this->setSession('discussiondisplay', 'thread');
                // Get details on the topic
                $post = $this->objPost->getRootPost($topic_id);
                $this->objTopic->updateTopicViews($topic_id);
                // Check if the topic exists, else call an error message
                if ($post == NULL) {
                        // error message, post doesn't exist
                        return $this->nextAction(NULL, array('error' => 'topicdoesntexist', 'type' => $this->discussiontype));
                } else {
                        // Send the topic to the template
                        $this->setVarByRef('post', $post);
                        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                        // Check if user has access to workgroup discussion else redirect
                        $this->checkWorkgroupAccessOrRedirect($discussion);
                        // Check if discussion is locked - if true - disable / editing replies
                        if ($this->objDiscussion->checkIfDiscussionLocked($post['discussion_id'])) {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                                $this->objPost->discussionLocked = TRUE;
                                $this->setVar('discussionlocked', TRUE);
                        } else {
                                $this->setVar('discussionlocked', FALSE);
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
                        // Check if ratings allowed in Discussion
                        if ($discussion['ratingsenabled'] == 'Y') {
                                $this->objPost->discussionRatingsArray = $this->objDiscussionRatings->getDiscussionRatings($post['discussion_id']);
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
                        $discussionLink = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'])));
                        $discussionLink->link = $discussion['discussion_name'];
                        $this->objMenuTools->addToBreadCrumbs(array($discussionLink->show(), $post['post_title']));
                        // return the template
                        return 'discussion_topic_threadedview.php';
                }
        }

        /**
         * This shows a topic post by post, one at a time
         * @param string $topic_id Record Id of the topic
         */
        public function showSingleThread($topic_id) {
                // Store View as a Preference
                $this->setSession('discussiondisplay', 'singlethreadview');
                $this->objTopic->updateTopicViews($topic_id);
                $post = $this->objPost->getRootPost($topic_id);
                if ($post == NULL) {
                        // error message, post doesn't exist
                        return $this->nextAction(NULL, array('error' => 'topicdoesntexist', 'type' => $this->discussiontype));
                } else {
                        $this->setVarByRef('post', $post);
                        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                        // Check if user has access to workgroup discussion else redirect
                        $this->checkWorkgroupAccessOrRedirect($discussion);
                        // Check if discussion is locked - if true - disable / editing replies
                        if ($this->objDiscussion->checkIfDiscussionLocked($post['discussion_id'])) {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                                $this->objPost->discussionLocked = TRUE;
                                $this->setVar('discussionlocked', TRUE);
                        } else {
                                $this->setVar('discussionlocked', FALSE);
                                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                        $this->objPost->showModeration = TRUE;
                                }
                        }
                        // Check if ratings allowed in Discussion
                        if ($discussion['ratingsenabled'] == 'Y') {
                                $this->objPost->discussionRatingsArray = $this->objDiscussionRatings->getDiscussionRatings($post['discussion_id']);
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
                        $discussionLink = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'])));
                        $discussionLink->link = $discussion['discussion_name'];
                        $this->objMenuTools->addToBreadCrumbs(array($discussionLink->show(), $post['post_title']));
                        return 'discussion_topic_singleview.php';
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
                                        //if the post is successfuly removed, update the discussion
                                        if ($this->objPost->delete('id', $post_id)) {
                                                $this->objDiscussion->updateDiscussionAfterDelete($postDetails['id']);
                                                //if the number of replies is successfuly updated, end function
                                                if ($this->objTopic->update('id', $topic_id, $values, 'tbl_discussion_topic')) {
                                                        die();
                                                }
                                        }
                                }
//                    $values = array(
//                        
//                    );
//                        $this->objTopic->update('topic_id',$topic_id);
//                var_dump($this->objPost->getPostDiscussionDetails($post_id));
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
                $this->setSession('discussiondisplay', 'flatview');
                // Update Views
                $this->objTopic->updateTopicViews($topic_id);
                // Get details on the topic
                $post = $this->objPost->getRootPost($topic_id);
                // Check if the topic exists, else call an error message
                if ($post == NULL) {
                        // error message, post doesn't exist
                        return $this->nextAction(NULL, array('error' => 'topicdoesntexist', 'type' => $this->discussiontype));
                } else {
                        // Send the topic to the template
                        $this->setVarByRef('post', $post);
                        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                        // Check if user has access to workgroup discussion else redirect
                        $this->checkWorkgroupAccessOrRedirect($discussion);
                        // Check if discussion is locked - if true - disable / editing replies
                        if ($this->objDiscussion->checkIfDiscussionLocked($post['discussion_id'])) {
                                $this->objPost->repliesAllowed = FALSE;
                                $this->objPost->editingPostsAllowed = FALSE;
                                $this->objPost->discussionLocked = TRUE;
                                $this->setVar('discussionlocked', TRUE);
                        } else {
                                $this->setVar('discussionlocked', FALSE);
                                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                        $this->objPost->showModeration = TRUE;
                                }
                        }
                        // Check if ratings allowed in Discussion
                        if ($discussion['ratingsenabled'] == 'Y') {
                                $this->objPost->discussionRatingsArray = $this->objDiscussionRatings->getDiscussionRatings($post['discussion_id']);
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
                        $discussionLink = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'])));
                        $discussionLink->link = $discussion['discussion_name'];
                        $this->objMenuTools->addToBreadCrumbs(array($discussionLink->show(), $post['post_title']));
                        // return the template
                        return 'discussion_topic_flatview.php';
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
                // Get details of the Discussion
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                // Check if user has access to workgroup discussion else redirect
                $this->checkWorkgroupAccessOrRedirect($discussion);
                if ($discussion['discussionlocked'] == 'Y') {
                        return $this->nextAction('viewtopic', array('message' => 'cantreplydiscussionlocked', 'id' => $post['topic_id'], 'post' => $post, 'type' => $this->discussiontype));
                } else if ($post['status'] == 'CLOSE') {
                        return $this->nextAction('viewtopic', array('message' => 'cantreplytopiclocked', 'id' => $post['topic_id'], 'post' => $post, 'type' => $this->discussiontype));
                } else {
                        $postDisplay = $this->objPost->displayPost($post);
                        $this->setVarByRef('post', $post);
                        $this->setVarByRef('postDisplay', $postDisplay);
                        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                        $this->setVarByRef('discussion', $discussion);
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
                        $numTopicSubscriptions = $this->objTopicSubscriptions->getNumTopicsSubscribed($post['discussion_id'], $this->objUser->userId());
                        $this->setVarByRef('numTopicSubscriptions', $numTopicSubscriptions);
                        // Check whether the user is subscribed to the current topic
                        $topicSubscription = $this->objTopicSubscriptions->isSubscribedToTopic($post['topic_id'], $this->objUser->userId());
                        $this->setVarByRef('topicSubscription', $topicSubscription);
                        // Check whether the user is subscribed to the current discussion
                        $discussionSubscription = $this->objDiscussionSubscriptions->isSubscribedToDiscussion($post['discussion_id'], $this->objUser->userId());
                        $this->setVarByRef('discussionSubscription', $discussionSubscription);
                        return 'discussion_postreply.php';
                }
        }

        /**
         * Method to save a reply to a topic
         */
        public function saveReply() {

                $tempPostId = $this->objUser->userId() . '_' . mktime()/* $_POST['temporaryId'] */;
                //set the temporary ID so it can be used by other functions
                $this->setVarByref('attachment_tempid', $tempPostId);
                //get the attachment id
                $attachment_id = $this->getParam('attachment');
                //reply type
                $replyType = $this->objLanguage->languageText('word_reply', 'system');
                //parentID
                $postParent = $this->getParam('parent');
                $post_parent = $postParent;
                $post_tangent_parent = 0;
                //

                $parentPostDetails = $this->objPost->getRow('id', $postParent);
                //get the discussion ID
                $discussion_id = $this->getParam('discussionid');
                //get topic ID
                $topic_id = $this->getParam('topicid');
                //we need this because IE is failing to pass it over
                $this->setSession('current_topic_id', $topic_id);
                $type_id = $this->getSession('discussionType'); //$_POST['discussionType'];
                //get the post title
                $post_title = $this->getParam('posttitle');
                $post_text = $this->getParam('message');
                $language = $this->getParam('lang');
//                $language = 'en';
                if (strlen($postParent) == 0) {
                        $original_post = 1;
                } else {
                        $original_post = 0;
                }
                $level = $parentPostDetails['level'];
                // Some Server side validation - Redirect Form - Check details of the post
                // Validation
                //     - Remove Tags
                //     - &nbsp; becomes [nothing]
                //     - trim whitespace
                if (trim(strip_tags(str_replace('&nbsp;', '', $post_text))) == '') {
                        // Capture details to put in array - Preserve User's work
                        $details = array('replytype' => $replyType, 'title' => $post_title, 'language' => $language, 'temporaryId' => $tempPostId);
                        // set array as a session
                        $this->setSession($tempPostId, $details);
                        // Redirect back to for
//                        return $this->nextAction('postreply', array('id' => $postParent, 'type' => $this->discussiontype, 'message' => 'missing', 'tempid' => $tempPostId));
                } else {
                        $this->unsetSession($tempPostId);
                }
                $post_id = $this->objPost->insertSingle($post_parent, $post_tangent_parent, $discussion_id, $topic_id, $this->userId, $level);
                $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $original_post, $this->userId);
                $this->objTopic->updateLastPost($topic_id, $post_id);
                $this->objDiscussion->updateLastPost($discussion_id, $post_id);
//                // Email Post
                $discussionDetails = $this->objDiscussion->getDiscussion($discussion_id);
                $topicSubscription = $this->objTopicSubscriptions->isSubscribedToTopic($topic_id, $this->objUser->userId());
                $discussionSubscription = $this->objDiscussionSubscriptions->isSubscribedToDiscussion($discussion_id, $this->objUser->userId());
////                echo print_r($discussionSubscription).'<br/>'.print_r($topicSubscription).'<br/>'.print_r($discussionDetails);
//                //get the discussion
                $discussion = $this->objDiscussion->getDiscussion($discussion_id);
//                
                if (!empty($attachment_id)) {
                        //set the attachment ID to be used by other functions
                        $this->setVarByRef('attachment_id', $attachment_id);
                        $this->saveTempAttachmentIfAny($post_id, $attachment_id);
                        $this->handleAttachments($post_id, $attachment_id);
                }
                // Manage Subscriptions
                if (isset($discussion['subscriptions'])) {
                        if ($discussion['subscriptions'] == 'discussionsubscribe') { // First check subscriptions to discussion
                                if (!$discussionSubscription) { // If not subscribed to discussion, subscribe user now.
                                        $this->objDiscussionSubscriptions->subscribeUserToDiscussion($discussion_id, $this->objUser->userId());
                                }
                        } else if ($discussion['subscriptions'] == 'topicsubscribe') { // Now check if the user wants to subscribe to the topic
                                if ($discussionSubscription) { // If user was subscribed to discussion, remove subscription
                                        $this->objDiscussionSubscriptions->unsubscribeUserFromDiscussion($discussion_id, $this->objUser->userId());
                                }
                                // Now subscribe user to topic, if the user was not subscribed to the topic
                                if (!$topicSubscription) {
                                        $this->objTopicSubscriptions->subscribeUserToTopic($topic_id, $this->objUser->userId());
                                }
                        } else if ($discussion['subscriptions'] == 'nosubscriptions') { // Else remove subscription from topic
                                $this->objDiscussionSubscriptions->unsubscribeUserFromDiscussion($discussion_id, $this->objUser->userId());
                        }
                }
                $discussionDetails['subscriptions'] = "Y";
                if ($discussionDetails['subscriptions'] == 'Y') {
                        $replyUrl = $this->uri(array('action' => 'viewtopic', 'id' => $topic_id, 'post' => $post_id, 'context' => $this->contextCode), 'discussion');
//                        $emailSuccess = $this->objDiscussionEmail->sendEmail($topic_id, $post_title, $post_text, $discussionDetails['discussion_name'], $this->userId, $replyUrl);
                        $emailSuccess = NULL;
                } else {
                        $emailSuccess = NULL;
                }
                $this->objDiscussionEmail->sendEmail($topic_id, $post_title, $post_text, $discussionDetails['discussion_name'], $this->userId, $replyUrl);
                // Attachment Handling
//                $this->handleAttachments($post_id, $tempPostId);
//                return $this->nextAction('viewtopic', array('message' => 'replysaved', 'id' => $topic_id, 'post' => $post_id, 'type' => $this->discussiontype, 'email' => $emailSuccess));
//                return $this->uri(array('action' => 'viewtopic', 'id' => $parentPostDetails['topic_id']));
        }

        /**
         * Method to get a post and prepare it for editing form
         * @param string $id Record Id of the Post
         */
        public function editPost($id) {
                $id = $this->getParam('id');
                $post = $this->objPost->getPostWithText($id);

//                if ($post['replypost'] == NULL && $this->objPost->checkOkToEdit($post['datelastupdated'], $post['userid'])) {
                $this->setVarByRef('post', $post);
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                // Check if user has access to workgroup discussion else redirect
                $this->checkWorkgroupAccessOrRedirect($discussion);
                $this->setVarByRef('discussion', $discussion);
//                        $temporaryId = $this->objUser->userId() . '_' . mktime();
//                        $this->setVarByRef('temporaryId', $temporaryId);
                // Move posts from tbl_discussion_attachments to tbl_discussion_temp_attachments
//                        $attachments = $this->objPostAttachments->getAttachments($id);
//                        foreach ($attachments AS $attachment) {
//                                $this->objTempAttachments->insertSingle($temporaryId, $attachment['attachment_id'], $post['discussion_id'], $this->userId, mktime());
//                        }
//                        $acch = $this->objTempAttachments->getQuickList($temporaryId);
//                        return 'discussion_editpost.php';
//                } else {
//                        return $this->nextAction('viewtopic', array('message' => 'unabletoeditpost', 'id' => $post['topic_id'], 'post' => $post['post_id'], 'type' => $this->discussiontype));
//                }
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
                return $this->nextAction('viewtopic', array('message' => 'postupdated', 'id' => $topic_id, 'post' => $post_id, 'type' => $this->discussiontype));
        }

        // --------------------------------------------------------------------------------

        /**
         * Discussion Administration Page
         *
         * Shows the list of discussions in a context, allows you to create/edit discussion settings
         */
        public function discussionAdministration() {
                $contextDiscussions = $this->objDiscussion->getAllContextDiscussions($this->contextCode);
                $this->setVarByRef('discussionsList', $contextDiscussions);
                $visibleDiscussions = $this->objDiscussion->getContextDiscussions($this->contextCode);
                $this->setVarByRef('visibleDiscussions', $visibleDiscussions);
                $defaultDiscussion = $this->objDiscussion->getDefaultDiscussion($this->contextCode);
                $this->setVarByRef('defaultDiscussion', $defaultDiscussion);
                return 'discussion_administration.php';
        }

        /**
         * Method to show the form to create a new discussion
         */
        public function createDiscussion() {
                $action = 'create';
                $this->setVarByRef('action', $action);
                return 'discussion_createedit.php';
        }

        /**
         * Method to Save a Newly Created Discussion
         */
        public function saveDiscussion() {
                $discussion_context = $this->contextCode;
                $discussion_workgroup = '';
                $discussion_name = $this->getParam('name');
                $discussion_description = $this->getParam('description');
                $defaultDiscussion = 'N';
                $discussion_visible = $this->getParam('visible');
                $discussionLocked = 'N';
                $ratingsenabled = $this->getParam('ratings');
                $studentstarttopic = $this->getParam('student');
                $attachments = $this->getParam('attachments');
                $subscriptions = $this->getParam('subscriptions');
                // Needs to be worked on
                $moderation = 'N';
                $discussion = $this->objDiscussion->insertSingle($discussion_context, $discussion_workgroup, $discussion_name, $discussion_description, $defaultDiscussion, $discussion_visible, $discussionLocked, $ratingsenabled, $studentstarttopic, $attachments, $subscriptions, $moderation);
                return $this->nextAction('administration', array('message' => 'discussioncreated', 'id' => $discussion));
        }

        /**
         * Method to get a discussion's details in order to edit them
         *
         * @param $id Record Id of the Discussion
         */
        public function editDiscussion($id) {
                $discussion = $this->objDiscussion->getDiscussion($id);
                // Check if Discussion exists
                if ($discussion == false) {
                        return $this->nextAction('administration');
                } else {
                        $action = 'edit';
                        $this->setVarByRef('action', $action);
                        $this->setVarByRef('discussion', $discussion);
                        return 'discussion_createedit.php';
                }
        }

        /**
         * Update a discussion's settings
         *
         * @return string Template - redirects back to discussion admin
         */
        public function editDiscussionSave() {
                $discussion_id = $this->getParam('id');
                $discussion_name = stripslashes($this->getParam('name'));
                $discussion_description = stripslashes($this->getParam('description'));
                $discussion_visible = $this->getParam('visible');
                $discussionLocked = $this->getParam('lockdiscussion');
                if ($discussion_visible == 'default') {
                        $discussion_visible = 'Y';
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
                $this->objDiscussion->updateSingle($discussion_id, $discussion_name, $discussion_description, $discussion_visible, $discussionLocked, $ratingsenabled, $studentstarttopic, $attachments, $subscriptions, $moderation, $archiveDate);
                return $this->nextAction('administration', array('message' => 'discussionupdated', 'id' => $discussion_id));
        }

        /**
         * Dynamically change the discussion visibility
         */
        function updateDiscussionSetting() {
                $discussion_id = $this->getParam('discussion_id');
                $discussion_status = $this->getParam('discussion_status');
                $discussion_setting = $this->getParam('discussion_setting');
                if ($this->objUser->isLoggedIn()) {
                        echo $this->objDiscussion->updateDiscussion($discussion_id, $discussion_setting, $discussion_status);
                } else {
                        die();
                }
                die();
        }

        /**
         * Method to show a form for deleting a discussion
         * @param string $id Record Id of the Discussion
         */
        public function deleteDiscussion($id) {
                $discussion = $this->objDiscussion->getDiscussion($id);
                $this->setVarByRef('discussion', $discussion);
                return 'discussion_deletediscussion.php';
        }

        /**
         * Method to Delete a Discussion following confirmation
         */
        public function deleteDiscussionConfirm() {
                // Get the Id
                $id = $this->getParam('id');
                // Get Discussion Details
                $discussion = $this->objDiscussion->getDiscussion($id);
                if ($discussion == NULL) { // Check if Discussion Exists
                        return $this->nextAction('administration', array('message' => 'discussiondoesnotexist'));
                } else if ($discussion['defaultdiscussion'] == 'Y') { // Check if Default Discussion
                        return $this->nextAction('administration', array('message' => 'cannotdeletedefaultdiscussion'));
                } else {
                        $result = $this->objDiscussion->deleteDiscussion($id); // Delete Discussion

                        if ($result) { // Check whether deletion was successful
                                return $this->nextAction('administration', array('message' => 'discussiondeleted', 'discussion' => $discussion['discussion_name']));
                        } else {
                                return $this->nextAction('administration', array('message' => 'couldnotdeletediscussion'));
                        }
                }
        }

        /**
         * Method to update the visibility status of a discussion
         */
        public function updateDiscussionVisibility() {
                $id = $this->getParam('id');
                $visibility = $this->getParam('visible');
                // Get Discussion Details
                $discussion = $this->objDiscussion->getDiscussion($id);
                // Check if changes made
                if ($discussion['discussion_visible'] == strtoupper($visibility)) {
                        return $this->nextAction('administration', array('discussion' => $id, 'message' => 'visibilityunchanged'));
                } else {
                        $result = $this->objDiscussion->updateDiscussionVisibility($id, $visibility);

                        if ($result) {
                                return $this->nextAction('administration', array('discussion' => $id, 'message' => 'visibilityupdated'));
                        } else {
                                return $this->nextAction('administration', array('discussion' => $id, 'message' => 'errorvisibilityupdate'));
                        }
                }
        }

        /**
         * Set a Discussion as the default Discussion
         *
         * @return string Template - redirects back to discussion administration
         */
        public function setDefaultDiscussion() {
                $this->objDiscussion->setDefaultDiscussion($_POST['discussion'], $this->contextCode);
                return $this->nextAction('administration', array('message' => 'defaultdiscussionchanged'));
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
                return 'discussion_attachments.php';
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
         * @return string Template - discussion_view.php
         */
        public function downloadAttachment($id, $topic) {
                $files = $this->objPostAttachments->downloadAttachment($id);
                if (count($files) > 0) {
                        $location = $this->objFiles->getFullFilePath($files[0]['id']);
                        header('Content-Disposition: attachment; filename="' . $files[0]['filename'] . '"');
                        readfile($location);
                        //header('Location:'.$location); // Todo - Force Download
                } else {
                        return $this->nextAction('thread', array('id' => $topic, 'type' => $this->discussiontype, 'message' => 'invalidattachment'));
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
                $this->objPost->discussionLocked = TRUE;
                $post = $this->objPost->getPostWithText($post_id);
                $postDisplay = $this->objPost->displayPost($post);
                // Get a list of languages for the current post
                // Will be deleted from the list of languages to translate in
                $postLanguages = $this->objPostText->getPostLanguages($post_id);
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                $this->checkWorkgroupAccessOrRedirect($discussion);
                // Send the topic to the template
                $this->setVarByRef('post', $post);
                $this->setVarByRef('postDisplay', $postDisplay);
                $this->setVarByRef('postLanguages', $postLanguages);
                return 'discussion_translatepost_form.php';
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
                return $this->nextAction('viewtranslation', array('id' => $newId, 'message' => 'translationsaved', 'type' => $this->discussiontype));
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
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                $this->checkWorkgroupAccessOrRedirect($discussion);
                $this->setVarByRef('discussion', $discussion);
                return 'discussion_viewtranslation.php';
        }

        /**
         * Method to save a change in the topic status
         */
        public function changeTopicStatus() {
                $topic_id = $_POST['topic'];
                $status = $_POST['topic_status'];
                $reason = $_POST['reason'];
                $this->objTopic->changeTopicStatus($topic_id, $status, $reason, $this->userId);
                return $this->nextAction('viewtopic', array('message' => 'statuschanged', 'id' => $topic_id, 'type' => $this->discussiontype));
        }

        /**
         * Method to show the settings for a discussion (usually at the bottom of posts)
         * @param string $discussion_id Record Id of the Discussion
         * @param boolean $showDiscussionJump Flag to indicate whether the switching between discussion has to be shown
         * @return string Discussion Settings
         */
        public function showDiscussionFooter($discussion_id, $showDiscussionJump = TRUE) {
                $returnString = '';
                $fieldset = $this->newObject('fieldset', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('textinput', 'htmlelements');
                // By pass switching between discussions if we are dealing with workgroups
                if ($this->discussiontype != 'workgroup') {
                        $discussionForm = new form('discussionform', 'index.php');
                        $discussionForm->method = 'GET';
                        $discussions = $this->objDiscussion->getContextDiscussions($this->contextCode);
                        if (count($discussions) > 1 && $showDiscussionJump) {
                                $dd = new dropdown('id');
                                foreach ($discussions AS $discussion) {
                                        if ($discussion['id'] != $discussion_id) {
                                                $dd->addOption($discussion['id'], $discussion['discussion_name']);
                                        }
                                }
                                $dd->setSelected($discussion_id);
                                $button = new button('');
                                $button->setValue('Go');
                                $button->setToSubmit();
                                $discussionForm->addToForm('<p align="center">' . $this->objLanguage->languageText('mod_discussion_jumptoanotherdiscussion', 'discussion') . ': ' . $dd->show() . ' ' . $button->show() . '</p>');
                                $module = new textinput('module', 'discussion');
                                $module->fldType = 'hidden';
                                $action = new textinput('action', 'discussion');
                                $action->fldType = 'hidden';
                                $discussionForm->addToForm($module->show() . $action->show());
                                $fieldset->addContent($discussionForm->show());
                                $returnString .= $fieldset->show();
                        } else {
                                if ($showDiscussionJump) {
                                        $returnString .= '<hr />';
                                }
                        }
                }
                $discussionTable = $this->newObject('htmltable', 'htmlelements');
                $iconsFieldset = $this->newObject('fieldset', 'htmlelements');
                $iconsFieldset->setExtra(' class="discussionBlock"');
                $iconsFieldset->setLegend($this->objLanguage->languageText('mod_discussion_meaningoficons', 'discussion'));
                $icon = $this->getObject('geticon', 'htmlelements');
                $icon->setIcon('lock', NULL, 'icons/discussion/');
                $iconsFieldset->addContent($icon->show() . ' - ' . $this->objLanguage->languageText('mod_discussion_topicislocked', 'discussion'));
                $icon->setIcon('unlock', NULL, 'icons/discussion/');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' - ' . $this->objLanguage->languageText('mod_discussion_unlockedtopicexplained', 'discussion'));
                $icon->setIcon('unreadletter');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' -  ' . $this->objLanguage->languageText('mod_discussion_newunreadtopic', 'discussion'));
                $icon->setIcon('readletter');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' - ' . $this->objLanguage->languageText('mod_discussion_readtopic', 'discussion'));
                $icon->setIcon('readnewposts');
                $iconsFieldset->addContent('<br />' . $icon->show() . ' - ' . $this->objLanguage->languageText('mod_discussion_readtopicnewreplies', 'discussion'));
                $discussionTable->addCell($iconsFieldset->show(), '50%');
                // -----------------------------------------------
                $discussionFieldset = $this->newObject('fieldset', 'htmlelements');
                $discussionFieldset->setExtra(' class="discussionBlock"');
                $discussionFieldset->setLegend($this->objLanguage->languageText('mod_discussion_discussionSettings', 'discussion'));
                $discussionDetails = $this->objDiscussion->getRow('id', $discussion_id);
                if ($discussionDetails['discussionlocked'] == 'Y') {
                        $discussionFieldset->addContent('<div class="noRecordsMessage"><strong>' . $this->objLanguage->languageText('mod_discussion_discussionislocked', 'discussion') . '<br /><br />' . $this->objLanguage->languageText('mod_discussion_allfunctionalitydisabled', 'discussion') . '</strong></div>');
                } else {
                        $discussionFieldset->addContent('<ul>');
                        if ($discussionDetails['studentstarttopic'] == 'Y') {
                                $discussionFieldset->addContent('<li>' . ucfirst($this->objLanguage->code2Txt('mod_discussion_studentsstartopics', 'discussion')) . '</li>');
                        } else {
                                $discussionFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_discussion_onlylecturersstarttopics', 'discussion') . '</li>');
                        }
                        if ($discussionDetails['attachments'] == 'Y') {
                                $discussionFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_discussion_usersuploadattachments', 'discussion') . '</li>');
                        } else {
                                $discussionFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_discussion_userscannotupload', 'discussion') . '</li>');
                        }
                        if ($discussionDetails['ratingsenabled'] == 'Y') {
                                $discussionFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_discussion_usersrateposts', 'discussion') . '</li>');
                        } else {
                                $discussionFieldset->addContent('<li>' . $this->objLanguage->languageText('mod_discussion_ratingpostsdisabled', 'discussion') . '</li>');
                        }
                        $discussionFieldset->addContent('</ul>');
                }
                // Bottom Fieldset
                $discussionLinksFieldset = $this->getObject('fieldset', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $link = new link($this->uri(array('action' => 'administration')));
                $link->link = $this->objLanguage->languageText('mod_discussion_discussionadministration', 'discussion');
                $discussionLinksFieldset->addContent($link->show());
                $discussionLinksFieldset->addContent(' / ');
                $link = new link($this->uri(array('action' => 'editdiscussion', 'id' => $discussion_id)));
                $link->link = $this->objLanguage->languageText('mod_discussion_editdiscussionsettings', 'discussion');
                $discussionLinksFieldset->addContent($link->show());
                $discussionLinksFieldset->addContent(' / ');
                $link = new link($this->uri(array('action' => 'statistics', 'id' => $discussion_id)));
                $link->link = $this->objLanguage->languageText('mod_discussion_discussionstatistics', 'discussion');
                $discussionLinksFieldset->addContent($link->show());
                if ($this->objUser->isCourseAdmin($this->contextCode) && $this->discussiontype != 'workgroup' && $this->isLoggedIn) {
                        $showSettings = $discussionLinksFieldset->show();
                } else if ($this->discussiontype != 'workgroup') {
                        $link2 = new link($this->uri(array('type' => 'context')));
                        $link2->link = ucwords($this->objLanguage->code2Txt('mod_discussion_returntocontextdiscussions', 'discussion'));
                        $showSettings = '<fieldset><p>' . $link2->show() . '</p></fieldset>';
                } else {
                        $link = new link($this->uri(NULL, 'workgroup'));
                        $link->link = ucwords($this->objLanguage->code2Txt('mod_discussion_returntoworkgroup', 'discussion'));
                        $link2 = new link($this->uri(array('type' => 'context')));
                        $link2->link = ucwords($this->objLanguage->code2Txt('mod_discussion_returntocontextdiscussions', 'discussion'));
                        $showSettings = '<p>' . $link->show() . ' / ' . $link2->show() . '</p>';
                }
                $discussionTable->addCell($discussionFieldset->show() . $showSettings, '50%');
                $returnString .= $discussionTable->show();
                return $returnString;
        }

        /**
         * Method to save the ratings of a post
         */
        public function savePostRatingDown() {
                // Collect All Posted Values and put them in an array
//                $postedArray = $_POST;
                // Remove the submit button from the list
//                unset($postedArray['submitForm']);
                // Store the Topic Id - for redirecting
//                $topic = $this->getParam('') /* $_POST['topic'] */;
                // Remove the Topic Id from the list
//                unset($postedArray['topic']);
//                if (isset($_POST['currentPost'])) {
//                        $currentPost = $_POST['currentPost'];
//                        unset($postedArray['currentPost']);
//                } else {
//                        $currentPost = NULL;
//                }
                // Start Inserting Values
//                foreach ($postedArray AS $post => $value) {
//                        if ($value != 'n/a') {
                $post_id = $this->getParam('post_id');
                $currentRating = $this->objPostRatings->getPostRatings($post_id);
//                var_dump($currentRating);
                if ($currentRating >= 1) {
                        $currentRating -= 1;
                        return $this->objPostRatings->insertRecord($post_id, $currentRating, $this->userId);
                } else {
                        return FALSE;
                }
//                        }
//                }
//                return $this->nextAction('viewtopic', array('message' => 'ratingsaved', 'id' => $topic, 'post' => $currentPost, 'type' => $this->discussiontype));
        }

        /**
         * increase the post rating by one
         */
        function savePostRatingUp() {
                $post_id = $this->getParam('post_id');
                if (isset($post_id)) {
                        $currentRating = $this->objPostRatings->getPostRatings($post_id);
                        print_r($currentRating);
                        if (empty($currentRating)) {
                                $currentRating = 1;
                                return $this->objPostRatings->insertRecord($post_id, $currentRating, $this->userId);
                        } else {
                                $currentRating += 1;
                                return $this->objPostRatings->insertRecord($post_id, $currentRating, $this->userId);
                        }
                }
        }

        /**
         * Method to show statistics for a discussion discussion
         *
         * @param string $discussion_id Record Id of the discussion
         */
        public function showDiscussionStatistics($discussion_id) {
                $this->setVarByRef('id', $discussion_id);
                $discussionDetails = $this->objDiscussion->getDiscussion($discussion_id);
                $this->setVarByRef('discussionDetails', $discussionDetails);
                $discussionSummaryStats = $this->objDiscussionStats->getStats($discussion_id, $this->contextCode);
                $this->setVarByRef('discussionSummaryStats', $discussionSummaryStats);
                $tangents = $this->objDiscussionStats->getTangentsNum($discussion_id);
                $this->setVarByRef('tangents', $tangents);
                $posters = $this->objDiscussionStats->getPosters($discussion_id, $this->contextCode);
                $this->setVarByRef('posters', $posters);
                $posterStats = $this->objDiscussionStats->getPostersNum($discussion_id, $this->contextCode);
                $this->setVarByRef('posterStats', $posterStats);
                $posterTopics = $this->objDiscussionStats->getPosterTopics($discussion_id, $this->contextCode);
                $this->setVarByRef('posterTopics', $posterTopics);
                $posterTangents = $this->objDiscussionStats->getPosterTangents($discussion_id, $this->contextCode);
                $this->setVarByRef('posterTangents', $posterTangents);
                $userRatesOther = $this->objDiscussionStats->getUserRateOtherPosts($discussion_id);
                $this->setVarByRef('userRatesOther', $userRatesOther);
                $userRatesSelf = $this->objDiscussionStats->getUserRateSelfPosts($discussion_id);
                $this->setVarByRef('userRatesSelf', $userRatesSelf);
                $userWordCount = $this->objDiscussionStats->getUserWordCount($discussion_id);
                $this->setVarByRef('userWordCount', $userWordCount);
                //$this->objDiscussionStats
                return 'discussion_statistics.php';
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
                $otherDiscussions = $this->objDiscussion->otherDiscussions($topic['discussion_id'], $this->contextCode);
                $this->setVarByRef('otherDiscussions', $otherDiscussions);
                if ($topic['topic_tangent_parent'] == '0') {
                        $tangents = $this->objTopic->getTangents($id);
                        $topicParent = '';
                } else {
                        $tangents = array(); // Create Array of No Values
                        $topicParent = $this->objTopic->getTopicDetails($topic['topic_tangent_parent']);
                }
                $discussion = $this->objDiscussion->getDiscussion($topic['discussion_id']);
                $this->setVarByRef('tangents', $tangents);
                $this->setVarByRef('topicParent', $topicParent);
                $this->setVarByRef('discussion', $discussion);
                // Bread Crumbs
                $discussionLink = new link($this->uri(array('action' => 'discussion', 'id' => $topic['discussion_id'])));
                $discussionLink->link = $discussion['discussion_name'];
                $topicLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id'])));
                $topicLink->link = $topic['post_title'];
                $this->objMenuTools->addToBreadCrumbs(array($discussionLink->show(), $topicLink->show(), $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion')));
                return 'discussion_moderatetopic.php';
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
                                $this->objDiscussion->updateDiscussionAfterDelete($topicInfo['discussion_id']);
                                return $this->nextAction('viewtopic', array('id' => $topicInfo['topic_tangent_parent'], 'message' => 'tangentdeleted'));
                        } else { // Deleting a topic with tangents
                                if (isset($_POST['tangentoption']) && $_POST['tangentoption'] == 'delete') {
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        $results = $this->objTopic->deleteTangents($_POST['id']);
                                        $this->objDiscussion->updateDiscussionAfterDelete($topicInfo['discussion_id']);
                                        return $this->nextAction('discussion', array('id' => $topicInfo['discussion_id'], 'message' => 'topictangentsdeleted'));
                                } else if (isset($_POST['tangentoption']) && $_POST['tangentoption'] == 'move') {
                                        $this->objTopic->moveTangentsToAnotherTopic($_POST['id'], $_POST['topicmove']);
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        $this->objDiscussion->updateDiscussionAfterDelete($topicInfo['discussion_id']);
                                        return $this->nextAction('discussion', array('id' => $topicInfo['discussion_id'], 'message' => 'topicdeletedtangentsmoved'));
                                } else if (isset($_POST['tangentoption']) && $_POST['tangentoption'] == 'newtopic') {
                                        $this->objTopic->moveAllTangentsToRootTopic($_POST['id']);
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        return $this->nextAction('discussion', array('id' => $topicInfo['discussion_id'], 'message' => 'topicdeletedtangentsmovedtoroot'));
                                } else { // Simply delete topic - has no tangents
                                        $results = $this->objTopic->deleteTopic($_POST['id']);
                                        $this->objDiscussion->updateDiscussionAfterDelete($topicInfo['discussion_id']);
                                        return $this->nextAction('discussion', array('id' => $topicInfo['discussion_id'], 'message' => 'topicdeleted'));
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
                return $this->nextAction('discussion', array('id' => $topicInfo['discussion_id'], 'message' => 'topicmovetotangent', 'topic' => $id));
        }

        public function moderateMoveNewDiscussion() {
                $topic = $this->getParam('id');
                $discussion = $this->getParam('discussionmove');

                if ($topic != '' && $discussion != '') {
                        $this->objTopic->switchTopicDiscussion($topic, $discussion);
                }
                return $this->nextAction('discussion', array('id' => $discussion, 'message' => 'topicmovedtonewdiscussion', 'topic' => $topic));
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
                        return $this->nextAction('discussion', array('id' => $topicInfo['discussion_id'], 'message' => 'tangentmovedtonewtopic', 'topic' => $_POST['id']));
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
         * Method to determine if we are dealing with a workgoup discussion via session Id, and whether the user is part of that workgroup (has access)
         */
        public function getWorkGroupDetails() {
                // Get the Workgroup
                $this->workgroupId = $this->objWorkGroup->getWorkgroupId();
                $this->workgroupDescription = $this->objWorkGroup->getDescription($this->workgroupId);
                if ($this->objWorkGroupUser->memberOfWorkGroup($this->userId, $this->workgroupId) || $this->objUser->isContextLecturer($this->userId, $this->contextCode)) {
                        $this->discussiontype = 'workgroup';
                } else {
                        $this->discussiontype = 'context';
                }
        }

        /**
         * Method to get the discussion for a workgroup or create one that does not exist. Also checks if user is falsely trying to create one.
         */
        public function checkWorkgroupDiscussion() {
                $this->workgroupDiscussion = $this->objDiscussion->getWorkgroupDiscussion($this->contextCode, $this->workgroupId);
                if ($this->workgroupDiscussion == NULL) {
                        $this->workgroupDiscussion = $this->objDiscussion->autoCreateWorkgroupDiscussion($this->contextCode, $this->workgroupId, $this->workgroupDescription);
                }
                if ($this->discussiontype == 'workgroup') {
                        return $this->nextAction('discussion', array('id' => $this->workgroupDiscussion, 'type' => $this->discussiontype));
                } else {
                        //echo 'not member of discussion';
                        return $this->nextAction('noaccess', array('id' => $this->workgroupDiscussion));
                        // Fixup or redirect to discussion home
                }
        }

        /**
         * Method to determine if a discussion is for a workgrup and whether the user has access to it. If not redirect to a page saying so.
         *
         * @param array $discussion Array containing details of the discussion
         */
        public function checkWorkgroupAccessOrRedirect($discussion) {
                if ($this->usingWorkGroupsFlag && ($discussion['discussion_workgroup'] != NULL) && !($this->objWorkGroupUser->memberOfWorkGroup($this->userId, $discussion['discussion_workgroup']) || $this->objUser->isContextLecturer($this->userId, $this->contextCode))) {
                        return $this->nextAction('noaccess', array('id' => $discussion['discussion_workgroup']));
                } else {
                        $this->discussiontype = 'workgroup';
                        $this->setVarByRef('discussiontype', $this->discussiontype);
                        $this->objPost->discussiontype = $this->discussiontype;
                }
                if ($discussion['discussion_workgroup'] == NULL) {
                        $this->discussiontype = 'context';

                        $this->setVarByRef('discussiontype', $this->discussiontype);
                        $this->objPost->discussiontype = $this->discussiontype;
                }
        }

        /**
         * Method to show that the user has no access to a workgroup discussion
         */
        public function showNoAccess($id) {
                return 'discussion_workgroup_noaccess.php';
        }

        /**
         * Method to View a Mind Map of topics in a Discussion
         * @param string $id Record Id of the Discussion
         */
        public function viewMindMap($id) {
                $discussion = $this->objDiscussion->getDiscussion($id);
                $this->setVarByRef('discussion', $discussion);
                $this->setVar('map', $this->uri(array('action' => 'generatemindmap', 'id' => $id)));
                return 'discussion_mindmap.php';
        }

        /**
         * Method to Generate the XML of Mind Map of topics in a Discussion
         * @param string $id Record Id of the Discussion
         */
        public function generateMindMap($id) {
                $discussion = $this->objDiscussion->getDiscussion($id);
                // Get Order and Sorting Values
                $order = $this->getParam('order', $this->getSession('sortorder', 'date'));
                $direction = $this->getParam('direction', $this->getSession('sortdirection', 'asc'));
                // Flag to Discussion Class
                $this->objDiscussion->order = $order;
                $this->objDiscussion->direction = $direction;
                $allTopics = $this->objTopic->showTopicsInDiscussion($id, $this->userId, $discussion['archivedate'], $order, $direction);
                $this->loadClass('treemenu', 'tree');
                $this->loadClass('treenode', 'tree');
                $this->loadClass('freemindmap', 'tree');
                $this->loadClass('htmllist', 'tree');
                $treeMenu = new treemenu();
                $rootNode = & new treenode(array('text' => $discussion['discussion_name']), NULL);
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
                $this->setSession('discussiondisplay', 'viewtopicmindmap');
                // Update Views
                $this->objTopic->updateTopicViews($topic_id);
                // Get details on the topic
                $post = $this->objPost->getRootPost($topic_id);
                $this->setVarByRef('post', $post);
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                // Check if user has access to workgroup discussion else redirect
                $this->checkWorkgroupAccessOrRedirect($discussion);
                $this->setVar('map', $this->uri(array('action' => 'generatetopicmindmap', 'id' => $topic_id)));
                $this->setVarByRef('changeDisplayForm', $this->objTopic->showChangeDisplayTypeForm($topic_id, 'viewtopicmindmap'));
                // Mark the Topic as read for the current user if user is logged in
                if ($this->isLoggedIn) {
                        $this->objTopicRead->markTopicRead($topic_id, $this->userId);
                }
                // Check if discussion is locked - if true - disable / editing replies
                if ($this->objDiscussion->checkIfDiscussionLocked($post['discussion_id'])) {
                        $this->objPost->repliesAllowed = FALSE;
                        $this->objPost->editingPostsAllowed = FALSE;
                        $this->objPost->discussionLocked = TRUE;
                        $this->setVar('discussionlocked', TRUE);
                } else {
                        $this->setVar('discussionlocked', FALSE);
                }
                // Bread Crumbs
                $discussionLink = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'])));
                $discussionLink->link = $discussion['discussion_name'];
                $this->objMenuTools->addToBreadCrumbs(array($discussionLink->show(), $post['post_title']));

                return 'discussion_topic_mindmap.php';
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

                // Get Discussion Details
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                // Turn off replies if Topic is Locked
                if ($discussion['discussionlocked'] == 'Y') {
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
                                $nodeDetails['hooktext'] = $this->objLanguage->languageText('mod_discussion_freemindfollowlink', 'discussion', 'Click on the link to post a reply');
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
                                $message = $this->objLanguage->languageText('mod_discussion_startedtopic', 'discussion', 'started topic') . ':';
                                $rootNodes[] = $topic['post_id'];
                                $text .= $message . '&#xa;&#xa;' . strip_tags($topic['post_text']);
                                $nodeDetails['fontsize'] = 20;
                                $nodeDetails['fontbold'] = TRUE;
                        } else {
                                $message = $this->objLanguage->languageText('mod_discussion_postedareply', 'discussion', 'posted a reply') . ':';
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
                $objDiscussionPassThrough = $this->getObject('discussion_passthrough');
                switch ($this->getParam('action')) {
                        case 'discussion':
                                $discussion = $objDiscussionPassThrough->getContextFromDiscussion($this->getParam('id'));
                                break;
                        case 'viewtopic':
                        case 'flatview':
                        case 'singlethreadview':
                        case 'thread':
                        case 'moderatetopic':
                                $discussion = $objDiscussionPassThrough->getContextFromTopic($this->getParam('id'));
                                break;
                        case 'postreply':
                                $discussion = $objDiscussionPassThrough->getContextFromPost($this->getParam('id'));
                                break;
                        default:
                                $discussion = '';
                                break;
                }
                if ($discussion != '' && $discussion != FALSE) {
                        $this->contextObject->joinContext($discussion);
                }
                return;
        }

        /**
         * Method to Search through the Discussion Discussion
         * @param string $term Term to search for
         * @param string $form Discussion to search in
         */
        public function searchDiscussion($term, $discussion) {
                $errors = array();
                if (trim($term) == '') {
                        $errors[] = 'No Search Term was provided';
                        $term = '';
                }
                if ($discussion != 'all') {
                        $discussionInfo = $this->objDiscussion->getDiscussion($discussion);
                        if ($discussionInfo == FALSE) {
                                $errors[] = 'Discussion you tried to search does not exist.';
                                $discussion = 'all';
                        }
                }
                $objSearch = $this->getObject('discussionsearch');
                $objSearch->defaultDiscussion = $discussion;
                $objSearch->searchTerm = $term;
                // Only perform search if no errors exist
                if (count($errors) == 0) {
                        $searchResults = $objSearch->searchDiscussion($term, $discussion);
                } else {
                        $searchResults = '';
                }
                $this->setVarByRef('searchForm', $objSearch->show());
                $this->setVarByRef('searchTerm', stripslashes($term));
                $this->setVarByRef('searchResults', $searchResults);
                $this->setVarByRef('errors', $errors);
                return 'discussion_searchresults.php';
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
                $this->objPost->discussionLocked = TRUE;
                // Reget the topic
                $post = $this->objPost->getPostWithText($id);
                $postDisplay = $this->objPost->displayPost($post);
                $this->setVarByRef('post', $post);
                $this->setVarByRef('postDisplay', $postDisplay);
                return 'discussion_post_delete.php';
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
                        $this->objDiscussion->updateDiscussionAfterDelete($post['discussion_id']);
                        return $this->nextAction('viewtopic', array('id' => $post['topic_id'], 'message' => 'posthasbeendeleted'));
                } else {
                        return $this->nextAction('viewtopic', array('id' => $post['topic_id'], 'post' => $_POST['id'], 'message' => 'postdeletecancelled'));
                }
        }

        /**
         * subscribe a user to the discussion
         */
        function subscribeUserToDiscussion() {
                $discussion_id = $this->getParam('discussion_id');
                $this->objDiscussionSubscriptions->subscribeUserToDiscussion($discussion_id, $this->objUser->userId());
                echo "<h1>discussioned</h1>";
//                die();
        }

        /**
         * subscribe user to the topic
         */
        function subscribeUserToTopic() {
                $topic_id = $this->getParam('topic_id');
                $discussion_id = $this->getParam('discussion_id');
                $objTopic = &$this->getObject('dbtopicsubscriptions', 'discussion');
                $this->objDiscussionSubscriptions->unsubscribeUserFromDiscussion($discussion_id, $this->objUser->userId());
                if ($objTopic->subscribeUserToTopic($topic_id, $this->objUser->userId())) {
                        echo "<h1>topiced</h1>";
                }
//                die();
        }

        /**
         * subscribe a user to the discussion
         */
        function unsubscribeUser() {
                $discussion_id = $this->getParam('discussion_id');
                $topic_id = $this->getParam('topic_id');
                $objTopic = &$this->getObject('dbtopicsubscriptions', 'discussion');
                $objTopic->unsubscribeUserFromTopic($this->objUser->userId(), $topic_id);
                $this->objDiscussionSubscriptions->unsubscribeUserFromDiscussion($discussion_id, $this->objUser->userId());
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
                                        return $this->subscribeUserToDiscussion();
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
