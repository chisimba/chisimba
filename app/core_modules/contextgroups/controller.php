<?php

/**
 * Controller class for the context groups module.
 * 
 * Purpose of this module is to allow for context member management.
 * It should hide the group information from the user.
 * Target user: Lecturers.
 * Precondition : User must be in a context.
 * Tasks: Add/remove Lecturers, students, or guests.
 * 
 * PHP versions 4 and 5
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
 * @package   contextgroups
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Controller class for the context groups module.
 * 
 * Purpose of this module is to allow for context member management.
 * It should hide the group information from the user.
 * Target user: Lecturers.
 * Precondition : User must be in a context.
 * Tasks: Add/remove Lecturers, students, or guests.
 * 
 * @category  Chisimba
 * @package   contextgroups
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class contextgroups extends controller
{

    /**
    * Method to initialise the module.
    */
    public function init()
    {
        $this->objContextUsers = $this->getObject('contextusers');
        $this->objContext = $this->getObject('dbcontext', 'context');
        
        // Load the Group Admin Model
        $this->objGroups = $this->getObject('groupAdminModel', 'groupadmin');
        $this->objGroupUsers = $this->getObject('groupusersdb', 'groupadmin');
        $this->objGroupsOps = $this->getObject('groupops', 'groupadmin');
		
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    * Dispatch Method
    * @param string $action Action to be taken
    */
    public function dispatch($action)
    {
        // Check if User is in a Context
        if ($this->objContext->getContextCode() == '') {
            return $this->nextAction(NULL, NULL, '_default');
        }
        
        // Set Layout Template
        $this->setLayoutTemplate('contextgroups_layout_tpl.php');
        
        switch ($action)
        {
            default:
                return $this->groupsHome();
            case 'searchforusers':
                return $this->searchForUsers();
            case 'viewsearchresults';
                return $this->getResults($this->getParam('page', 1));
            case 'addusers':
                return $this->updateUserRoles();
            case 'removeuser':
                return $this->removeUserFromGroup($this->getParam('userid'), $this->getParam('group'));
            case 'removeallusers':
                return $this->removeAllUsersFromGroup();
        }
    }
    
    /**
     * Method to place permissions handling
     * @param string $action Action to be taken
     * @return boolean Whether user has permission to access or not.
     */
    public function isValid($action)
    {
        $needPermissions = array('searchforusers', 'viewsearchresults', 'addusers', 'removeuser', 'removeallusers');
        
        if (in_array($action, $needPermissions)) {
            if ($this->objUser->isAdmin() || $this->objUser->isContextLecturer()) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    
    /**
    * Method to show the list of users in a context
    */
    private function groupsHome()
    {
        // Generate an array of users in the context, and send it to page template
        $this->prepareContextUsersArray();
        
        // Default Values for Search
        $searchFor = $this->getSession('searchfor', '');
        $this->setVar('searchfor', $searchFor);
        
        $field = $this->getSession('field', 'firstName');
        $course=$this->getSession('course','course');
        $group=$this->getSession('group','group');
        $this->setVar('field', $field);
        $this->setVar('course', $course);
        $this->setVar('group', $group);
        
        
        //Ehb-added-begin
         $currentContextCode=$this->objContext->getContextCode();
                $where="where contextCode<>"."'".$currentContextCode."'";
                $data=$this->objContext->getAll($where);
                $this->setVarByRef('data',$data);
                    //Ehb-added-End
      
        
        return 'home_tpl.php';
    }
    
    /**
    * Method to search for Users
    * This function sets them as a session and then redirects to the results
    */
    private function searchForUsers()
    {
        $searchFor = $this->getParam('search');
        $this->setSession('searchfor', $searchFor);
        
        $field = $this->getParam('field');
        $this->setSession('field', $field);
        
        
        //Ehb-added-begin
       $course=$this->getParam('course');
        $this->setSession('course', $course);
        
        $group=$this->getParam('group');
        $this->setSession('group',$group);
      //Ehb-added-End
        
        $order = $this->getParam('order');
        $this->setSession('order', $order);
                       
        $numResults = $this->getParam('results');
        $this->setSession('numresults', $numResults);
        
        return $this->nextAction('viewsearchresults');
    }
    
    /**
    * Method to Show the Results for a Search
    * @param int $page - Page of Results to show
    */
    private function getResults($page = 1)
    {
        $searchFor = $this->getSession('searchfor', '');
        $field = $this->getSession('field', 'firstName');
        
         //Ehb-added-begin
        $course=$this->getSession('course','course');
        $group=$this->getSession('group','group');
           //Ehb-added-End
        $order = $this->getSession('order', 'firstName');
        $numResults = $this->getSession('numresults', 20);
        
        
         
        
        $this->setVar('searchfor', $searchFor);
        $this->setVar('field', $field);
        $this->setVar('order', $order);
        $this->setVar('numresults', $numResults);
           //Ehb-added-begin
        $this->setVar('course', $course);
        $this->setVar('group', $group);
           //Ehb-added-End
        // Prevent Corruption of Page Value - Negative Values
        if ($page < 1) {
            $page = 1;
        }
        $currentContextCode=$this->objContext->getContextCode();
        $results = $this->objContextUsers->searchUsers($searchFor, $field, $order, $numResults, ($page-1),$course,$group);
       
        $this->setVarByRef('results', $results);
        
        $countResults = $this->objContextUsers->countResults();
        
        $this->setVarByRef('countResults', $countResults);
        
        $this->setVarByRef('page', $page);
        
        
        $paging = $this->objContextUsers->generatePaging($searchFor, $field, $order, $numResults, ($page-1));
        $this->setVarByRef('paging', $paging);
        $contextCode = $this->objContext->getContextCode();
        $this->setVarByRef('contextCode', $contextCode);
        
        //Ehb-added-begin
        $currentContextCode=$this->objContext->getContextCode();
                $where="where contextCode<>"."'".$currentContextCode."'";
                $data=$this->objContext->getAll($where);            
                $this->setVarByRef('data',$data);
                    //Ehb-added-End
        
        // Get Users into Arrays
        $this->prepareContextUsersArray();
        
        
        return 'searchresults_tpl.php';
    }
    
    /**
    * Method to Update User Roles
    */
    private function updateUserRoles()
    {
        $contextCode = $_POST['context'];
        
        if ($contextCode != $this->objContext->getContextCode()) {
            //return $this->nextAction('error');
            die ('Joined another Context. Adding Users in this way is forbidden. Please start all over again.');
        }
        
        $changedItems = $_POST['changedItems'];
        
        $changedItems = explode(',', $changedItems);
        array_shift($changedItems); 
        $changedItems = array_unique($changedItems); 
		
        $groups =  $this->objGroups->getTopLevelGroups();
		$contextGroupId = $this->objGroups->getId($contextCode);
		$subGroups = $this->objGroups->getSubgroups($contextGroupId);
       
		foreach($subGroups[0] as $subGroup)
		{
			$groupName =  $this->objGroupsOps->formatGroupName($subGroup['group_define_name']);
			switch ($groupName)
			{
				case 'Lecturers':
					$lecturerGroupId = $this->objGroups->getId($subGroup['group_define_name']);
					break;
				case 'Students':
					$studentGroupId = $this->objGroups->getId($subGroup['group_define_name']);
					break;
				case 'Guest':
					$guestGroupId = $this->objGroups->getId($subGroup['group_define_name']);
					break;
			} 
			
		}		
		        
        foreach ($changedItems as $item)
        {
			$permid = $this->objGroupsOps->getUserByUserId($item);
			$pkId = $permid['perm_user_id'];	
			
			//remove users 
			$this->objGroupsOps->removeUser($lecturerGroupId, $pkId);
			$this->objGroupsOps->removeUser($studentGroupId, $pkId);
			$this->objGroupsOps->removeUser($guestGroupId, $pkId);
           
            switch ($_POST[$item])
            {
                case 'none': // Already Removed from system
                    break;
                case 'lecturer': // add as lecturer
                    $this->objGroups->addGroupUser($lecturerGroupId, $pkId);
                    break;
                case 'student': // add as student
                    $this->objGroups->addGroupUser($studentGroupId, $pkId);
                    break;
                case 'guest': // add as guest
                    $this->objGroups->addGroupUser($guestGroupId, $pkId);
                    break;
                default:
                    break; // Should be impossible to get out here
            }
        }
       // die;
        return $this->nextAction(NULL, array('message'=>'usersupdated'));
    }
    
    /**
    * Method to remove a user from a group
    * @param string $userId User Id of the User
    * @param string $group Group to be deleted from - either lecturers, students or guest
    */
    private function removeUserFromGroup($userId=NULL, $group=NULL)
    {
        if ($userId == '') {
            return $this->nextAction(NULL, array('message'=>'nouseridprovidedfordelete'));
        }
        
        $group = ucfirst(strtolower($group));
        
        if (!in_array($group, array('Lecturers', 'Students', 'Guest'))) {
            return $this->nextAction(NULL, array('message'=>'nopropergroupprovidedfordelete'));
        }
        
        $groupId=$this->objGroups->getLeafId(array($this->objContext->getContextCode(), $group));
        $pkId = $this->objUser->PKId($userId);
        
        $this->objGroupUsers->deleteGroupUser($groupId, $pkId);
        
        return $this->nextAction(NULL, array('message'=>'userdeletedfromgroup', 'userid'=>$userId, 'group'=>$group));
    }
    
    
    /**
    * Method to Prepare a List of Users in a Context sorted by lecturer, student, guest
    * The results are sent to the template
    */
    private function prepareContextUsersArray()
    {
        // Get Context Code
        $contextCode = $this->objContext->getContextCode();
        $filter = " ORDER BY surname ";
        
        // Lecturers
        $gid=$this->objGroups->getLeafId(array($contextCode,'Lecturers'));		
        $lecturers = $this->objGroups->getGroupUsers($gid, array('userid', 'firstName', 'surname', 'title', 'emailAddress', 'country', 'sex', 'staffnumber'), $filter);
		
        $lecturersArray = array();
		if (count($lecturers) > 0)
		{
				foreach ($lecturers as $lecturer)
				{
					$lecturersArray[] = $lecturer['userid'];
				}
		}
        // Students
        $gid=$this->objGroups->getLeafId(array($contextCode,'Students'));
        $students = $this->objGroups->getGroupUsers($gid, array('userid', 'firstName', 'surname', 'title', 'emailAddress', 'country', 'sex', 'staffnumber'), $filter);
        $studentsArray = array();
		if (count($students) > 0)
		{
			foreach ($students as $student)
			{
				$studentsArray[] = $student['userid'];
			}
		}
        // Guests
        $gid=$this->objGroups->getLeafId(array($contextCode,'Guest'));
        $guests = $this->objGroups->getGroupUsers($gid, array('userid', 'firstName', 'surname', 'title', 'emailAddress', 'country', 'sex', 'staffnumber'), $filter);
        $guestsArray = array();
		if (count($guests) > 0)
		{
			foreach ($guests as $guest)
			{
				$guestsArray[] = $guest['userid'];
			}
		}
        // Send to Template
        $this->setVarByRef('lecturers', $lecturersArray);
        $this->setVarByRef('lecturerDetails', $lecturers);
        $this->setVarByRef('students', $studentsArray);
        $this->setVarByRef('studentDetails', $students);
        $this->setVarByRef('guests', $guestsArray);
        $this->setVarByRef('guestDetails', $guests);
    }
    
    /**
    * Method to remove all users from a group
    */
    private function removeAllUsersFromGroup()
    {
        $mode = $this->getParam('mode');
        if($mode == 'lecturer'){
            $userIds = $this->getParam('lecturerId');
            $groupId=$this->objGroups->getLeafId(array($this->objContext->getContextCode(), 'Lecturers'));
        }elseif($mode == 'student'){
            $userIds = $this->getParam('studentId');
            $groupId=$this->objGroups->getLeafId(array($this->objContext->getContextCode(), 'Students'));
        }else{
            $userIds = $this->getParam('guestId');
            $groupId=$this->objGroups->getLeafId(array($this->objContext->getContextCode(), 'Guest'));
        }
        if(count($userIds) > 0){
            foreach($userIds as $userId){
                $pkId = $this->objUser->PKId($userId);
                $this->objGroupUsers->deleteGroupUser($groupId, $pkId);
            }
        }
        return $this->nextAction(NULL);
    }

}
?>