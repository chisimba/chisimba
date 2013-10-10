<?php
/**
* Tools class for the DBBS library providing various functions
*/

// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check

/**
 * Tools class for the DBBS library providing various functions
 * 
 * @author Megan Watson
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package dbbslibrary
 */

class dbbslibtools extends object 
{
    
    /**
    * The constructor
    */
    public function init()
    {
        $this->objBlocks = $this->getObject('blocks', 'blocks');
    }
    
    /**
    * Method to display the left menu items
    *
    * @access public
    * @return string html
    */
    public function leftMenu()
    {
        $blBrowse = $this->objBlocks->showBlock('browselibrary', 'dbbspostlogin', '','','', FALSE);
        $blManage = $this->objBlocks->showBlock('managelibrary', 'dbbspostlogin', '','','', FALSE);
        
        $str = $blBrowse;//.$blManage;
        $str .= '<br />';
        return $str;
    }
    
    /**
    * Temporary fix for context permissions.
    * The method builds an array of groups in which the user is a member. The group determines the users level of access in the site.
    *
    * @access public
    * @return void
    */
    public function setGroupPermissions()
    {
        $this->objUser = $this->getObject('user', 'security');
        if($this->objUser->isLoggedIn()){
            $access = $this->getSession('accessLevel');
            if(!(isset($access) && !empty($access))){
                $this->objGroup = $this->getObject('groupadminmodel', 'groupadmin');
                $this->userPkId = $this->objUser->PKId();
                
                $accessLevel = array();
                $accessLevel[] = 'user';
                $groupId = $this->objGroup->getLeafId(array('ETD Managers'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'manager';
                }
                $groupId = $this->objGroup->getLeafId(array('ETD Editors'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'editor';
                }
                $groupId = $this->objGroup->getLeafId(array('ETD Exam Board'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'board';
                }
                $groupId = $this->objGroup->getLeafId(array('Students'));
                if($this->objGroup->isGroupMember($this->userPkId, $groupId)){
                    $accessLevel[] = 'student';
                }
                $this->setSession('accessLevel', $accessLevel);
                $access = $accessLevel;
            }
        }
        return $access;
    }
}	
?>