<?php
/**
* hivaidsforum class extends controller
* @package hivaidsforum
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Controller class for hivaidsforum module
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class hivaidsforum extends controller
{
    /**
    * Method to construct the class.
    */
    public function init()
    {
        try{
            $this->objUser = $this->getObject('user', 'security');
            $this->hivTools = $this->getObject('hivtools', 'hivaidsforum');
            $this->dbHivForum = $this->getObject('dbhivforum', 'hivaidsforum');

            //Get the activity logger class and log this module call
            $objLog = $this->getObject('logactivity', 'logger');
            $objLog->log();
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Standard dispatch function
    *
    * @access public
    * @param string $action The action to be performed
    * @return string Template to be displayed
    */
    public function dispatch($action)
    {
        switch($action){
            case 'showpost':
                $postId = $this->getParam('postId');
                $display = $this->hivTools->showSinglePost($postId);
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
                break;

            case 'showreply':
                // check if user is logged in
                if(!$this->objUser->isLoggedIn()){
                    $display = $this->hivTools->showLoginRequired();
                }else{
                    $display = $this->hivTools->showReplyPage();
                }
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
                break;

            case 'savepost':
                $this->dbHivForum->saveTopicPost();
                return $this->nextAction('');
                break;

            case 'addcategory':
                $display = $this->hivTools->showAddCategory();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
                break;

            case 'savecat':
                $forumId = $this->dbHivForum->saveCategory();
                return $this->nextAction('showcat', array('catId' => $forumId));
                break;

            case 'addtopic':
                $display = $this->hivTools->showAddTopic();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
                break;

            case 'savetopic':
                $topicId = $this->dbHivForum->saveTopic();
                return $this->nextAction('showtopic', array('topicId' => $topicId));
                break;

            case 'moderate':
                break;

            case 'showcat':
                $forumId = $this->getParam('catId');
                $this->setSession('forumId', $forumId);

            case 'showtopic':
                $topicId = $this->getParam('topicId');
                $this->setSession('topicId', $topicId);

            default:
                $display = $this->hivTools->showTopicPosts();
                $this->setVarByRef('display', $display);
                return 'home_tpl.php';
        }
    }

    /**
    * Method to allow user to view the forum without being logged in
    *
    * @access public
    */
    public function requiresLogin($action)
    {
        switch($action){
            //case 'showreply':
            //    return TRUE;
            case 'moderate':
            case 'addtopic':
            case 'savecat':
            case 'addcategory':
            case 'savepost':
                return TRUE;

            default:
                return FALSE;
        }
    }
} // end of controller class
?>