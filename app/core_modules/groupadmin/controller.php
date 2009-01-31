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
                // get a list of all the groups
                $groups = $this->objOps->getAllGroups ();
                // formulate the groups list into something pretty
                $grps = $this->objOps->layoutGroups ( $groups );
                $this->setVarByRef ( 'grps', $grps );

                return 'viewgrps_tpl.php';

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