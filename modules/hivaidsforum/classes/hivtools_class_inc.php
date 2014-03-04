<?php
/**
* hivtools class extends object
* @package hivaidsforum
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* hivtools class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class hivtools extends object
{
    /**
    * @var string $userId The user id for tracking the user
    * @access private
    */
    private $userId = '';

    /**
    * @var string $userPkId The unique id for tracking the user
    * @access private
    */
    private $userPkId = '';

    /**
    * Constructor method
    */
    public function init()
    {
        $this->objTopic = $this->getObject('dbhivforum', 'hivaidsforum');

        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');

        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        $this->objEditor = $this->newObject('htmlarea', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');

        $this->userId = $this->objUser->userId();
        $this->userPkId = $this->objUser->PKId();
    }

    /**
    * Method to get the blocks for the left side column
    *
    * @access public
    * @return string html
    */
    public function getLeftBlocks()
    {
        $topicBlock = $this->objTopic->showTopicList();
        $categoryBlock = $this->objTopic->showCategoryList();
        $recentBlock = $this->objTopic->showRecentPosts();
        $loginBlock = $this->showLoginBlock();
        $manageBlock = $this->showManageList();

        return $categoryBlock.$topicBlock.$loginBlock.$recentBlock.$manageBlock.'<br />';
    }
    
    /**
    * Method to display the login block
    *
    * @access private
    * @return string html
    */
    private function showLoginBlock()
    {
	    if($this->objUser->isLoggedIn()){
	       return '';
	    }
	    
        $hdLogin = $this->objLanguage->languageText('word_login');
        $objLogin =  $this->getObject('logininterface', 'security');
	    $block = $objLogin->renderLoginBox('hivaidsforum');
	    
	    return $this->objFeatureBox->show($hdLogin, $block);
    }

    /**
    * Method to display a list of functions for managing the forum
    * The functions include: adding categories, topics, moderating posts
    *
    * @access private
    * @return string html
    */
    private function showManageList()
    {
        // Check users permissions - must be site lecturer or admin
        if(!$this->checkPermissions()){
            return '';
        }

        $lbManage = $this->objLanguage->languageText('phrase_manageforum');
        $lbAddCat = $this->objLanguage->languageText('phrase_addcategory');
        $lbAddTopic = $this->objLanguage->languageText('phrase_addtopic');
        //$lbModerate = $this->objLanguage->languageText('phrase_moderateposts');

        $objLink = new link($this->uri(array('action' => 'addcategory')));
        $objLink->link = $lbAddCat;
        $str = $objLink->show();

        $objLink = new link($this->uri(array('action' => 'addtopic')));
        $objLink->link = $lbAddTopic;
        $str .= '<br />'.$objLink->show();

        /*$objLink = new link($this->uri(array('action' => 'moderate')));
        $objLink->link = $lbModerate;
        $str .= '<br />'.$objLink->show();*/

        return $this->objFeatureBox->show($lbManage, $str);
    }

    /**
    * Method to display a single post
    *
    * @access public
    * @param string $postId The id of the post in the db
    * @return string html
    */
    public function showSinglePost($postId)
    {
        return $this->objTopic->showSinglePost($postId);
    }

    /**
    * Method to display the topic and responses
    *
    * @access public
    * @return string html
    */
    public function showTopicPosts()
    {
        $topicId = $this->getSession('topicId');
        if(!empty($topicId)){
            return $this->objTopic->showPosts();
        }else{
            return $this->objTopic->showTopicList('round');
        }
    }

    /**
    * Method to display a form for replying to a post
    *
    * @access public
    * @return string html
    */
    public function showReplyPage()
    {
        $lbReply = ucwords($this->objLanguage->languageText('phrase_replytotopic'));
        $lbSubject = $this->objLanguage->languageText('word_subject');
        $lbMessage = $this->objLanguage->languageText('word_message');
        $lbSave = $this->objLanguage->languageText('word_save');

        $topicId = $this->getSession('topicId');
        $parentId = $this->getParam('parent_id');

        $topic = $this->objTopic->getTopicPost();
        $str = $this->objTopic->displayTopPost($topic);
        $postId = $topic['postid'];

        if(!empty($parentId)){
            $lbReply = ucwords($this->objLanguage->languageText('phrase_replytopost'));
            $parent = $this->objTopic->getPostParent($parentId);
            $str .= $this->objTopic->displayTopPost($parent);

            // hidden fields - parent post id if reply is to a post not the topic
            $postId = $parentId;
        }

        // Create reply form

        // subject
        $objLabel = new label($lbSubject, 'input_subject');
        $objInput = new textinput('subject');
        $formStr = '<p>'.$objLabel->show().':<br />'.$objInput->show().'</p>';

        // message
        $objLabel = new label($lbMessage, 'input_message');
        $this->objEditor->init('message');
        $formStr .= '<p>'.$objLabel->show().':<br />'.$this->objEditor->show().'</p>';

        // post button
        $objButton = new button('save', $lbSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'</p>';

        // hidden fields - top post id
        $objInput = new textinput('postid', $postId, 'hidden');
        $formStr .= $objInput->show();

        $objForm = new form('posttopic', $this->uri(array('action' => 'savepost')));
        $objForm->addToForm($formStr);
        $str .= $this->objFeatureBox->showContent($lbReply, $objForm->show());

        return $str;
    }

    /**
    * Method to display a form for adding a category
    *
    * @access public
    * @return string html
    */
    public function showAddCategory()
    {
        // Check users permissions - must be site lecturer or admin
        if(!$this->checkPermissions()){
            return '';
        }

        $lbAddCat = ucwords($this->objLanguage->languageText('phrase_addcategory'));
        $lbCategory = $this->objLanguage->languageText('phrase_categoryname');
        $lbDescription = $this->objLanguage->languageText('word_description');
        $lbVisible = $this->objLanguage->languageText('word_visible');
        $lbYes = $this->objLanguage->languageText('word_yes');
        $lbNo = $this->objLanguage->languageText('word_no');
        $lbSave = $this->objLanguage->languageText('word_save');

        // category name
        $objLabel = new label($lbCategory, 'input_forum');
        $objInput = new textinput('forum', '', '', '60');
        $formStr = '<p>'.$objLabel->show().':<br />'.$objInput->show().'</p>';

        // description
        $objLabel = new label($lbDescription, 'input_description');
        $objInput = new textinput('description', '', '', '80');
        $formStr .= '<p>'.$objLabel->show().':<br />'.$objInput->show().'</p>';

        // is visible
        $objLabel = new label($lbVisible, 'input_visible');
        $objRadio = new radio('visible');
        $objRadio->addOption('Y', $lbYes);
        $objRadio->addOption('N', $lbNo);
        $objRadio->setSelected('Y');
        $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;');
        $formStr .= '<p>'.$objLabel->show().':&nbsp;&nbsp;&nbsp;'.$objRadio->show().'</p>';

        $objButton = new button('save', $lbSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'</p>';

        $objForm = new form('newforum', $this->uri(array('action' => 'savecat')));
        $objForm->addToForm($formStr);

        return $this->objFeatureBox->showContent($lbAddCat, $objForm->show());
    }

    /**
    * Method to display a form for adding a topic
    *
    * @access public
    * @return string html
    */
    public function showAddTopic()
    {
        // Check users permissions - must be site lecturer or admin
        if(!$this->checkPermissions()){
            return '';
        }

        $lbAddTopic = ucwords($this->objLanguage->languageText('phrase_addtopic'));
        $lbSubject = $this->objLanguage->languageText('word_subject');
        $lbMessage = $this->objLanguage->languageText('word_message');
        $lbSave = $this->objLanguage->languageText('word_save');

        // Get forum details
        $forum = $this->objTopic->getForumDetails();
        $str = $this->objTopic->displayForum($forum);

        // Create form

        // subject
        $objLabel = new label($lbSubject, 'input_subject');
        $objInput = new textarea('subject', '', '2', '100');
        $formStr = '<p>'.$objLabel->show().':<br />'.$objInput->show().'</p>';

        // message
        $objLabel = new label($lbMessage, 'input_message');
        $this->objEditor->init('message');
        $formStr .= '<p>'.$objLabel->show().':<br />'.$this->objEditor->show().'</p>';

        // post button
        $objButton = new button('save', $lbSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'</p>';

        $objForm = new form('newtopic', $this->uri(array('action' => 'savetopic')));
        $objForm->addToForm($formStr);
        $str .= $this->objFeatureBox->showContent($lbAddTopic, $objForm->show());

        return $str;
    }

    /**
     * Display a page requesting the user to login
     *
     * @access public
     * @return string html
     */
    function showLoginRequired()
    {
        $objRound = $this->newObject('roundcorners', 'htmlelements');

        $lbHere = strtolower($this->objLanguage->languageText('word_here'));
        $objLink = new link($this->uri(array('action' => 'showregister'), 'hivaids'));
        $objLink->link = $lbHere;
        $lnHere = $objLink->show();
        
        $arrText = array('registerlink' => $lnHere);
        $hdLogin = $this->objLanguage->languageText('mod_hivaidsforum_loginrequired', 'hivaidsforum');
        $lbMsg = $this->objLanguage->code2Txt('mod_hivaidsforum_loginrequiredmessage', 'hivaidsforum', $arrText);

        $objHead = new htmlHeading();
        $objHead->str = $hdLogin;
        $objHead->type = 1;
        $str = $objHead->show();

        $str .= '<p>'.$lbMsg.'</p>';
        return $objRound->show($str);
    }

    /**
    * Method to check if user is a lecturer or site admin
    *
    * @access private
    * @return bool TRUE if yes
    */
    private function checkPermissions()
    {
        $perms = $this->getSession('isManager');
        if(!empty($perms) && !is_null($perms)){
            if($perms == 'yes'){
                return TRUE;
            }
            return FALSE;
        }
        // Check if user is a lecturer
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');

        $groupId = $objGroups->getLeafId(array('Lecturers'));
        if($objGroups->isGroupMember($this->userPkId, $groupId)){
            $this->setSession('isManager', 'yes');
            return TRUE;
        }

        $groupId = $objGroups->getLeafId(array('Site Admin'));
        if($objGroups->isGroupMember($this->userPkId, $groupId)){
            $this->setSession('isManager', 'yes');
            return TRUE;
        }
        $this->setSession('isManager', 'no');
        return FALSE;
    }
}
?>