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

class announcementsTools extends object
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
        $this->objAnnouncements = $this->getObject('dbAnnouncements');
        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->objEditor = $this->newObject('htmlarea', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');

        $this->userId = $this->objUser->userId();
        $this->userPkId = $this->objUser->PKId();
        $this->objContext = $this->getObject('dbcontext','context');
        $isInContext=$this->objContext->isInContext();
        if($isInContext)
        {
            $this->contextCode=$this->objContext->getContextCode();
            $this->contextid=$this->objContext->getField('id',$this->contextCode);
            $contextTitle = $this->objContext->getTitle();
        }
        else{
            $this->contextid="root";
            $this->contextCode="root";
            $contextTitle = "Site";
        }
    }

    /**
    * Method to get the blocks for the left side column
    *
    * @access public
    * @return string html
    */
    public function getRightBlocks()
    {
        //is alright to display this, but only in the right context
        $announcementsBlock= $this->objAnnouncements->showLatestSite();
        $blocktitle=$this->objLanguage->languageText('mod_announcements_latestsite', 'announcements');
        $announcementsBlock=$this->objFeatureBox->show($blocktitle, $announcementsBlock);
        
        //is alright to display this, but only in the right context
        if($this->contextCode != "root"){
            $announcementsBlocks= $this->objAnnouncements->showLatestCourse($this->contextCode);
            $blocktitles=$this->objLanguage->languageText('mod_announcements_latestcourse', 'announcements');
            $announcementsCourseBlock=$this->objFeatureBox->show($blocktitles, $announcementsBlocks);
        }
            
        if($this->contextCode != "root"){
            //do not dispaly if user is not admin or lecturer
            if($this->checkPermissions()){
                $quickpostBlock= $this->objAnnouncements->showQuickPost();
                $blocktitle=$this->objLanguage->languageText('mod_announcements_quickadd', 'announcements');
                return  $announcementsBlock.$announcementsCourseBlock.$quickpostBlock.'<br />';
            }else{
                return  $announcementsBlock.$announcementsCourseBlock.'<br />';
            }
            
        }else{
            return  $announcementsBlock.'<br />';
        }
    }
    
    
    
    
    public function getQuickAddBlock()
    {
    
//        $quickpostBlock= $this->objAnnouncements->showQuickPost();

        return  $quickpostBlock.'<br />';
    }
    
 /*   public function showLatestBlock()
    {
       
        $announcementsBlock= $this->objAnnouncements->showLatestSite();
    

        return  $announcementsBlock.'<br />';
    }
    
    public function showLatestCourseBlock($contextid)
    {
       
        $announcementsBlock= $this->objAnnouncements->showLatestCourse($contextid);
    

        return  $announcementsBlock.'<br />';
    }*/
    
    public function checkPermissions()
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