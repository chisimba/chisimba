<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

//require_once('attachmentreader_class_inc.php');

define('EMAIL_HOST', 'imap.gmail.com');
define('EMAIL_POST', '993');

// See: http://www.php.net/manual/en/function.imap-open.php -> Optional flags for names
define('EMAIL_OPTIONS', 'imap/ssl');


define('EMAIL_LOGIN', 'discussion@thumbzup.com');
define('EMAIL_PASSWORD', '4RuMfOr7hUm8zUp');

//This will be the [domain] part of the @ in [catchall]@[domain]
// discussion_topic_2@chisimba.tohir.co.za
define('CATCH_ALL_BASE', 'chisimba.tohir.co.za');

/**
 * Discussion Email Class
 *
 * This class controls all functionality for sending emails from discussion posts
 *
 * NB! At the moment, it only works for context discussions, not lobby
 *
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package discussion
 * @version 1
 */
class discussionemail extends object {

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
        var $dbDiscussion;
        var $objUserContext;

        /**
         * Constructor
         */
        function init() {
                $this->dbPost = $this->getObject('dbpost', 'discussion');
                $this->dbTopic = $this->getObject('dbtopic', 'discussion');
                $this->objUser = $this->getObject('user', 'security');
                $this->objPostText = & $this->getObject('dbposttext');
                $this->dbDiscussion = & $this->getObject('dbdiscussion');
                $this->objUserContext = $this->getObject('usercontext','context');
                $this->contextGroups = & $this->getObject('managegroups', 'contextgroups');
                $this->objLanguage = & $this->getObject('language', 'language');

                /**
                 * @TESTING
                 */

//                $this->loadClass('attachmentreader', 'mail');
//                $this->emailBox = new AttachmentReader(EMAIL_HOST, EMAIL_POST, EMAIL_OPTIONS, EMAIL_LOGIN, EMAIL_PASSWORD, CATCH_ALL_BASE);
////
//                $numMessages = $this->emailBox->getNumMessages();
//
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
//                                        $discussion_id = $topicDetails['discussion_id'];
//                                        //get the discussion object
//                                        $objDiscussion = $this->getObject('dbdiscussion','discussion');
//                                        $discussionDetails = $objDiscussion->getDiscussion($discussion_id);
//                                        $post_title = $topicDetails['post_title'];
//                                        //
//                                        $partialMessage = explode('\n', $emailDetails['messageBody']);
//                                        echo "<pre>";
//                                        var_dump($partialMessage);
//                                        echo "<pre/>";
//                                        echo "<br/>";
//                                        $theOtherOne = $partialMessage[0];
//                                        $post_text = $emailDetails['messageBody'];
//                                        $language = $postDetails['language'];
//                                        $userDetails = $this->objUser->getRow('emailaddress', $emailDetails['sender']);
//                                        //check if the user is a member of the site
//                                        if ($userDetails) {
//                                                $userID = $userDetails['userid'];
//                                                $level = $postDetails['level'];
//                                                echo "<pre>";
////                                                print_r($emailDetails);
//                                                echo "</pre>";
////                                                if ($this->objUserContext->isContextMember($this->objUser->userId(), $discussionDetails['contextcode'])) {
////                                                        $post_id = $this->dbPost->insertSingle($post_parent, 0, $discussion_id, $topic_id, $userID, $level);
////                                                        $this->objPostText->insertSingle($post_id, $post_title, $post_text, $language, $post_parent, $userID);
////                                                        $this->dbTopic->updateLastPost($topic_id, $post_id);
////                                                        $this->dbDiscussion->updateLastPost($discussion_id, $post_id);
////                                                        $this->emailBox->deleteEmail($emailNum);
////                                                }
//                                        }
//                                } else {
//                                        echo "<h1>{$this->objLanguage->languageText('mod_discussion_topicdoesnotexist','discussion')}</h1>";
//                                }
//
//                                // Mark Email for deletion
////                                $this->emailBox->deleteEmail($emailNum);
//                        }
//                        // Expunge Deleted Mail
//                        unset($this->emailBox);
//                } else {
//                        echo "<h1>{$this->objLanguage->languageText('mod_discussion_inboxempty','discussion')}</h1>";
//                }
                /*
                 * END
                 */
//                $this->objUser = & $this->getObject('user', 'security');
//                $this->objTopic = $this->getObject('dbtopic', 'discussion');
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
                        array_push($this->emailList, $user['emailaddress']);
                }

                $objTopic = & $this->getObject('dbtopic');
                $topicDetails = $objTopic->listSingle($topic_id);

                // Get Users Subscribed to Discussion
                $objDiscussionSubscribers = & $this->getObject('dbdiscussionsubscriptions');
                $discussionSubscribers = $objDiscussionSubscribers->getUsersSubscribedDiscussion($topicDetails['discussion_id']);

                // Add the Email to the array
                foreach ($discussionSubscribers as $user) {
                        array_push($this->emailList, $user['emailaddress']);
                }

                // Remove duplicate emails
                $this->emailList = array_unique($this->emailList);
        }

        /**
         * Method to send an email to the users
         * @param String $topic_id Record Id of the Topic
         * @param String $title Title of the Post
         * @param String $text Text of the Post
         * @param String $discussion Name of the Discussion
         * @param String $senderId Record Id of the Sender
         * @param String $replyUrl Url for the User to Reply to
         */
        function sendEmail($topic_id, $title, $text, $discussion, $senderId, $replyUrl) {
                $this->prepareListEmail($topic_id);
//                $this->emailList = array(
//                    0 => 'wsifumba@gmail.com'
//                );
                // Only bother to send emails if there are more than one user.
                if (count($this->emailList) > 0) {
                        $this->loadClass('link', 'htmlelements');

                        //set eMail subject to topic ID/ without (gen)
                        $subject = '[' . $discussion . ']-' . str_replace('gen', '', $topic_id);
                        $name = 'Not Needed';

                        $line1 = $this->objLanguage->languageText('mod_discussion_emailtextline1', 'discussion', '{NAME} has posted the following message to the {FORUM} discussion discussion') . ':';
                        $line1 = str_replace('{NAME}', $this->objUser->fullname($senderId), $line1);
                        $line1 = str_replace('{FORUM}', $discussion, $line1);

                        //Convert '&' to '&amp;'
                        $replyUrl = str_replace('&', '&amp;', $replyUrl);

                        // Create a link
                        $replyLink = new link($replyUrl);
                        $replyLink->link = $replyUrl;

                        $line2 = $this->objLanguage->languageText('mod_discussion_emailtextline2', 'discussion', 'To reply to this message, go to: {URL}');
                        $line2 = str_replace('{URL}', $replyLink->show(), $line2);

                        $message = '------------------------------------------------<br />' . "\r\n";
                        $message .= $title . "<br />\r\n";
                        $message .= ucfirst($this->objLanguage->languageText('word_by', 'discussion', 'By')) . ' ' . $this->objUser->fullname($senderId) . "<br />\r\n";
                        $message .= '------------------------------------------------<br />' . "\r\n";
                        //$message .= '<p>'.$line1.'</p>'."\r\n\r\n";
                        $message .= str_replace('&nbsp;', ' ', $text) . "\r\n\r\n";
                        $message .= '<hr />' . "\r\n\r\n";
                        $message .= '<p>' . $line2 . '</p>' . "\r\n\r\n";

                        $body = '<html><head></head><body>' . $message . '</body></html>';

                        $from = $topic_id . '@' . CATCH_ALL_BASE;
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
                        return NULL;
                }
        }

}

?>