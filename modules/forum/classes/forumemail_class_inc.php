<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

//require_once('attachmentreader_class_inc.php');

define('EMAIL_HOST', '');
define('EMAIL_PORT', '');

// See: http://www.php.net/manual/en/function.imap-open.php -> Optional flags for names
define('EMAIL_OPTIONS', '');

define('EMAIL_LOGIN', '');
define('EMAIL_PASSWORD', '');

//define(CATCH_ALL_BASE, 'chisimba.tohir.co.za');
/**
 * Forum Email Class
 *
 * This class controls all functionality for sending emails from forum posts
 *
 * NB! At the moment, it only works for context forums, not lobby
 *
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package forum
 * @version 1
 */
class forumemail extends object {

        /**
         * @var Object $eMailReciever object to get emails from the server
         */
        var $eMailReciever;

        /**
         * @var Array $emailList List of Email Addresses
         */
        var $emailList;

        /**
         * @var string $contextCode Context Code
         */
        var $contextCode;
        var $emailBox;
        var $objUser;
        var $objPostText;
        var $dbForum;
        var $objUserContext;
        var $DbConfig;

        /**
         * Constructor
         */
        function init() {
                $this->loadClass('attachmentreader', 'mail');
                $this->objDbConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->dbPost = $this->getObject('dbpost', 'forum');
                $this->dbTopic = $this->getObject('dbtopic', 'forum');
                $this->objUser = $this->getObject('user', 'security');
                $this->objPost = & $this->getObject('dbpost');
                $this->dbForum = & $this->getObject('dbforum');
                $this->objUserContext = $this->getObject('usercontext', 'context');
                $this->contextGroups = & $this->getObject('managegroups', 'contextgroups');
                $this->objLanguage = & $this->getObject('language', 'language');
                //get the config parameters
                $emailHost = $this->objDbConfig->getValue('forum_mail_host', 'forum');
                $emailPort = $this->objDbConfig->getValue('forum_email_port', 'forum');
                $emailOptions = $this->objDbConfig->getValue('forum_email_options', 'forum');
                $emailUserName = $this->objDbConfig->getValue('forum_inbox_username', 'forum');
                $emailPassword = $this->objDbConfig->getValue('forum_inbox_password', 'forum');

                /**
                 * @TESTING
                 */
//                $this->emailBox = new AttachmentReader($emailHost, $emailPort, $emailOptions, $emailUserName, $emailPassword, CATCH_ALL_BASE);
//
//                $numMessages = $this->emailBox->getNumMessages();
//                if ($numMessages > 0) {
//                        for ($emailNum = 1; $emailNum <= $numMessages; $emailNum++) {
//
//                                // Retrieve Basic Details of Email From the Headers
//                                $emailDetails = $this->emailBox->getEmailDetails($emailNum);
//
//                                $split = explode('-', $emailDetails['subject']);
//                                //get the topic ID
//                                $topic_id = 'gen' . $split[1];
//                                //get the topic details
//                                $topicDetails = $this->dbTopic->getTopicDetails($topic_id);
//                                //get the post details
//                                $postDetails = $this->dbPost->getPostWithText($topicDetails['first_post']);
//                                if ($topicDetails == TRUE) {
//                                        $post_parent = $topicDetails['first_post'];
//                                        $forum_id = $topicDetails['forum_id'];
//                                        //get the forum object
//                                        $objForum = $this->getObject('dbforum', 'forum');
//                                        $forumDetails = $objForum->getForum($forum_id);
//                                        $post_title = $topicDetails['post_title'];
//                                        //
//                                        $partialMessage = explode('\n', $emailDetails['messageBody']);
//                                        $theOtherOne = $partialMessage[0];
//                                        $post_text = $emailDetails['messageBody'];
//                                        $language = $postDetails['language'];
//                                        $userDetails = $this->objUser->getRow('emailaddress', $emailDetails['sender']);
//                                        //check if the user is a member of the site
//                                        if ($userDetails) {
//                                                $userID = $userDetails['userid'];
//                                                $level = $postDetails['level'];
////                                                if ($this->objUserContext->isContextMember($this->objUser->userId(), $forumDetails['contextcode'])) {
////                                                        $post_id = $this->dbPost->insertSingle($post_parent, 0, $forum_id, $topic_id, $userID, $level);
////                                                        $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $post_parent, $userID);
////                                                        $this->dbTopic->updateLastPost($topic_id, $post_id);
////                                                        $this->dbForum->updateLastPost($forum_id, $post_id);
////                                                        $this->emailBox->deleteEmail($emailNum);
////                                                }
//                                        }
//                                } else {
//                                        echo "<h1>{$this->objLanguage->languageText('mod_forum_topicdoesnotexist', 'forum')}</h1>";
//                                }
////
////                                // Mark Email for deletion
////                                $this->emailBox->deleteEmail($emailNum);
//                        }
////                        // Expunge Deleted Mail
//                        unset($this->emailBox);
//                } else {
//                        echo "<h1>{$this->objLanguage->languageText('mod_forum_inboxempty', 'forum')}</h1>";
//                }
                /*
                 * END
                 */
                $this->objUser = & $this->getObject('user', 'security');
                $this->objTopic = $this->getObject('dbtopic', 'forum');
        }

        /**
         * Method to set the Context Code to use
         * @param String $context Context Code
         */
        function setContextCode($context) {
                $this->contextCode = $context;
                $this->objMailer = &$this->getObject('mailer', 'mail');
        }

        /**
         * Method to prepare the list of email addresses to use
         * At the moment, it takes the list of all users in a context.
         */
        function prepareListEmail($topic_id) {

                // Create an empty array for the email addresses
                $this->emailList = array();

                // Get Users Subscribed to Topic
                $objTopicSubscribers = & $this->getObject('dbtopicsubscriptions');
                $topicSubscribers = $objTopicSubscribers->getUsersSubscribedTopic($topic_id);

                // Add the Email to the array
                foreach ($topicSubscribers as $user) {
                        if ($this->objUser->email() == $user['emailaddress']) {
                                continue;
                        } else {
                                array_push($this->emailList, $user['emailaddress']);
                        }
                }

                $objTopic = & $this->getObject('dbtopic');
                $topicDetails = $objTopic->listSingle($topic_id);

                // Get Users Subscribed to Forum
                $objForumSubscribers = & $this->getObject('dbforumsubscriptions');
                $forumSubscribers = $objForumSubscribers->getUsersSubscribedForum($topicDetails['forum_id']);

                // Add the Email to the array
                foreach ($forumSubscribers as $user) {
                        if ($this->objUser->email() == $user['emailaddress']) {
                                continue;
                        } else {
                                array_push($this->emailList, $user['emailaddress']);
                        }
                }

                // Remove duplicate emails
                $this->emailList = array_unique($this->emailList);
        }

        /**
         * Method to send an email to the users
         * @param String $topic_id Record Id of the first post
         * @param String $title Title of the Post
         * @param String $text Text of the Post
         * @param String $forum Name of the Forum
         * @param String $senderId Record Id of the Sender
         * @param String $replyUrl Url for the User to Reply to
         */
        function sendEmail($parent_id, $title, $text, $forum, $senderId, $replyUrl) {
                $postDetails = $this->objPost->getPostWithText($parent_id);
                $topicDetails = $postDetails['topic_id'];
//                echo $postDetails['topic_id'];
                $this->prepareListEmail($postDetails['topic_id']);
//                $this->emailList = array(0=>'wsifumba@gmail.com');
//                $this->emailList = array(
//                    0 => 'wsifumba@gmail.com'
//                );
                // Only bother to send emails if there are more than one user.
                if (count($this->emailList) > 0) {
                        $this->loadClass('link', 'htmlelements');

                        //set eMail subject to topic ID/ without (gen)
                        $subject = '[' . $forum . ']-' . str_replace('gen', '', $postDetails['topic_id']);

                        $line1 = $this->objLanguage->languageText('mod_forum_emailtextline1', 'forum', '{NAME} has posted the following message to the {FORUM} discussion forum') . ':';
                        $line1 = str_replace('{NAME}', $this->objUser->fullname($senderId), $line1);
                        $line1 = str_replace('{FORUM}', $forum, $line1);

                        //Convert '&' to '&amp;'
                        $replyUrl = str_replace('&', '&amp;', $replyUrl);

                        // Create a link
                        $replyLink = new link($replyUrl);
                        $replyLink->link = $replyUrl;

                        $line2 = $this->objLanguage->languageText('mod_forum_emailtextline2', 'forum', 'To reply to this message, go to: {URL}');
                        $line2 = str_replace('{URL}', $replyLink->show(), $line2);

                        $message = '------------------------------------------------<br />' . "\r\n";
                        $message .= $title . "<br />\r\n";
                        $message .= ucfirst($this->objLanguage->languageText('word_by', 'forum', 'By')) . ' ' . $this->objUser->fullname($senderId) . "<br />\r\n";
                        $message .= '------------------------------------------------<br />' . "\r\n";
                        //$message .= '<p>'.$line1.'</p>'."\r\n\r\n";
                        $message .= str_replace('&nbsp;', ' ', $text) . "\r\n\r\n";
                        $message .= '<hr />' . "\r\n\r\n";
                        $message .= '<p>' . $line2 . '</p>' . "\r\n\r\n";

                        $body = '<html><head></head><body>' . $message . '</body></html>';

                        $from = $this->objDbConfig->getValue('forum_inbox_username');
                        $fromName = $this->objUser->fullname($senderId);

                        // Setup Alternate Message - Convert '&amp;' back to '&'
                        $altMessage = str_replace('&amp;', '&', $message);

                        // Add alternative message - same version minus html tags
                        $messagePlain = strip_tags($altMessage);
                        $this->objMailer->setValue('to', $from);
                        $this->objMailer->setValue('bcc', $this->emailList);
                        $this->objMailer->setValue('from', $from);
                        $this->objMailer->setValue('fromName', $fromName);
                        $this->objMailer->setValue('subject', $subject);
                        $this->objMailer->setValue('useHTMLMail', TRUE);
                        $this->objMailer->setValue('body', $messagePlain);
                        $this->objMailer->setValue('htmlbody', $message);
                        return $this->objMailer->send();
                } else {
//                        var_dump($this->emailList);
                }
        }

}

?>