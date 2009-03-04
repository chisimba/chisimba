<?php
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
/**
 *
 * @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package    groupadmin
 * @subpackage controller
 * @version    0.1
 * @since      22 November 2004
 * @author     Jonathan Abrahams
 * @filesource
 */
/**
 * Class to manage groups.
 *
 * @package    groupadmin
 * @subpackage controller
 * @access     public
 * @author     Jonathan Abrahams
 */
class groupadmin extends controller {
    /**
     *
     * @var groupAdminModel an object reference.
     */
    public $objGroupAdminModel;
    /**
     *
     * @var langauge an object reference.
     */
    public $objLanguage;
    /**
     *
     * @var strValidate an object reference.
     */
    public $objStrValidate;
    /**
     *
     * @var string the current groups Id
     */
    public $groupId;
    /**
     *
     * @var array a list of current groups members
     */
    public $memberList;
    /**
     *
     * @var array a list of current groups non-members
     */
    public $usersList;
    /**
     *
     * @var bool the validation status of a form.
     */
    public $valid;

    /**
     * Method that initializes the objects
     *
     * @access private
     * @return nothing
     */
    public function init() {
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        // Get the activity logger class
        $this->objLog = $this->newObject ( 'logactivity', 'logger' );
        // Log this module call
        $this->objLog->log ();
        $this->objOps = $this->getObject ( 'groupops' );
		$this->objGroups = $this->getObject('groupadminmodel');
		 $this->jQuery =$this->newObject('jquery', 'htmlelements');
		 $this->jQuery->loadLiveQueryPlugin();
    }

    /**
     * Method to handle the messages.
     *
     * @param  string  $action the message.
     * @access private
     * @return string  the template file name.
     */
    function dispatch($action) {
        // check that the current user has access to do this stuff.
        $hasAccess = $this->objUser->isContextLecturer ();
        $hasAccess |= $this->objUser->isAdmin ();
        if (! $hasAccess) {
            // This module is very sensitive, so get the intruder out asap, with no way to get back here!
            throw new customException ( $this->objLanguage->languageText ( "mod_groupadmin_insufficientperms", "groupadmin" ) );
        }
        switch ($action) {
            default :
				$this->setLayoutTemplate('main_layout_tpl.php');
				return "main_tpl.php";
                // get a list of all the groups
                $groups = $this->objOps->getAllGroups ();
                // formulate the groups list into something pretty
                $grps = $this->objOps->layoutGroups ( $groups );
                $this->setVarByRef ( 'grps', $grps );

                return 'viewgrps_tpl.php';
				
				
			case 'searchusers':
				$items = $this->objOps->getSearchableUsers();				
				
				$q = $this->getParam('q');
				foreach ($items as $key=>$value) {
					if (strpos(strtolower($key), $q) !== false) {
						echo "$key|$value\n";
						
					}
				}
				exit(0);
				
			case 'ajaxadduser':
				$userId = $this->objUser->getUserId($this->getParam('username'));
				//echo $userId; 
				$groupId = $this->getParam('groupid');
				echo $groupId;
				$res = $this->objGroups->addGroupUser($groupId, $userId);
				var_dump($userId);
				exit(0);
			
			case 'ajaxremoveuser':
				$userId = $this->getParam('userid');
				$groupId = $this->getParam('groupid');				
				$res = $this->objGroups->deleteGroupUser($groupId, $userId);
				var_dump($userId);
				exit(0);
				
			case 'ajaxgetgroupname':
				//echo 'here is the content for group..'.$this->getParam('groupid');
				$details = $this->objOps->getGroupInfo($this->getParam('groupid'));
				echo '<span class="subdued">Add to users to </span>'.$details[0]['group_define_name'];
				exit(0);
				
			case 'ajaxgetgroupcontent':
				//echo 'here is the content for group..'.$this->getParam('groupid');
				$groupId = $this->getParam('groupid');
				/*$subGroups = $this->objGroups->getSubgroups($groupId);
				if($subGroups)
				{
					echo $this->objOps->doSubGroups($subGroups);
				} else {
					echo $this->objOps->loadGroupContent($groupId);
				}*/
				echo $this->objOps->loadGroupContent($groupId);
				exit(0);
			case 'contextgroups':
				$this->setLayoutTemplate('main_layout_tpl.php');
				return 'contextgroups_tpl.php';
			/*	
			case 'ajaxgetsiteadmins':
				echo $this->objOps->getSiteAdmins();
				//echo "here are some site admins";
				exit(0);
				
			case 'ajaxgetlecturers':
				echo $this->objOps->getSiteLecturers();
				exit(0);
				
			case 'ajaxgetstudents':
				echo $this->objOps->getSiteStudents();
				exit(0);
			
			case 'ajaxgetsiteadminslist':
				echo $this->objOps->getSiteAdminsList();
				exit(0);
				
			case 'ajaxgetlecturerlist':				
				echo $this->objOps->getLecturerList();
				exit(0);
				
			case 'ajaxgetstudentist':
				echo $this->objOps->getStudentsList();
				exit(0);
				*/
            case 'editgrp' :
                // get the group id
                $grId = $this->getParam ( 'id', NULL );
                $adduser = $this->getParam ( 'adduser', NULL );
                // get the group info
                $grpInfo = $this->objOps->getGroupInfo ( $grId );

                if( $this->getParam( 'button' ) == 'save' ) {
                    $rightData = $this->getParam( 'rightList' );
                    $this->objOps->addUserToGroup($rightData, $grId);
                } elseif ( $this->getParam( 'button' ) == 'cancel' ) {
                    $this->nextAction(NULL);
                }
                // get the users IN the group already
                $usersin = $this->objOps->getUsersInGroup ( $grId );
                // format them nicely
                if (! empty ( $usersin )) {
                    $usersin = $this->objOps->layoutUsers ( $usersin, $grId );
                }

                if ($adduser !== NULL) {
                    // get all the POTENTIAL users that are not in ANY Group
                    $nongrpu = $this->objOps->getNonGrpUsers ();
                    // get all the users that are in groups, but not this one (yet)
                    $permusers = $this->objOps->getAllPermUsers ();
                    // clobber the nongrpusers and all other users together as the potentials for this grp
                    $potusers = array_merge ( $nongrpu, $permusers );
                    $this->setVarByRef ( 'potusers', $potusers );
                    $this->setVarByRef ( 'adduser', TRUE );
                }

                $this->setVarByRef ( 'groupinfo', $grpInfo );
                $this->setVarByRef ( 'usersin', $usersin );

                return 'usergroup_tpl.php';

            case 'addusertogrp' :
                $grpId = $this->getParam ( 'grpid' );

            case 'removeuser' :
                $grid = $this->getParam('grid');
                $id = $this->getParam('id');

                $this->objOps->removeUser($grid, $id);
                $this->nextAction('editgrp', array('id' => $grid));
                break;


        }
    }
} //end of class
?>