<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package permissions
* @subpackage controller
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams
* @filesource
*/

/**
* class permissions
* The permissions controller is responsible for initializing and controlling the
* program flow. The user interacts with the permission GUI, and the action are
* processed by the permissions controller.
* @access public
* @author Jonathan Abrahams
*/
class permissions extends controller
{
    /**
    * @var groupAdminModel an object reference.
    */
    var $objGAModel;
    /**
    * @var permissionsacl an object reference.
    */
    var $objPermAcl;

    /**
    * Method that initializes the objects
    * @access private
    * @return nothing
    */
    function init( )
    {
        $this->objGAModel  = $this->getObject('groupAdminModel','groupadmin');
        $this->objPermAcl  = $this->getObject('permissions_model', 'permissions');
    }

    /**
    * Method to handel the messages.
    * @param string $action the message.
    * @access private
    * @return string the template file name.
    */
    function dispatch( $action )
    {
		$this->setVar('pageSuppressXML',true);
        //$this->setLayoutTemplate("admin_layout_tpl.php");
        switch ($action) {
            case 'main' :
                return $this->showMain();
            case 'create' :
                return $this->showCreate();
            case 'create_form' :
                return $this->processCreateForm();
            case 'edit' :
                return $this->showEdit();
            case 'edit_form' :
                return $this->processEditForm();
        }
        return $this->showMain();
    }

    /**
    * Method to show the main page.
    * @access private
    * @return string the template file name.
    */
    function showMain( )
    {
        return 'main_tpl.php';
    }

    /**
    * Method to show the create access control list view
    * @access private
    * @return string the template file name.
    */
    function showCreate( )
    {
        return 'main_tpl.php';
    }

    /**
    * Method to process the create form post/get elements
    * @access private
    * @return string the template file name.
    */
    function processCreateForm( )
    {
        $clickedAddUserAcl  = $this->getParam('btnCreateUserAcl');
        $clickedAddGroupAcl = $this->getParam('btnCreateGroupAcl');
        $clickedCreateAcl   = $this->getParam('btnCreateAcl');
        $clickedDeleteAcl   = $this->getParam('btnDeleteAcl');
        $clickedCheckAcl    = $this->getParam('btnCheckAcl');
        $clickedEditAcl     = $this->getParam('btnEditAcl');

        $userId      = $this->getParam( 'userId' );
        $groupId     = $this->getParam( 'groupId' );
        $aclName     = $this->getParam( 'tinName' );
        $aclDescript = $this->getParam( 'tinDescription' );

        $aclId = $this->getParam( 'aclId' );
        $this->setVarByRef('aclId', $aclId );

        if ( $clickedEditAcl ) {
            return $this->showEdit();
        }
        if ( $clickedCreateAcl ) {
            $this->objPermAcl->newAcl( $aclName, $aclDescript );
            return $this->showMain();
        }
        if ( $clickedDeleteAcl ) {
            $this->objPermAcl->deleteAcl( $aclId );
            return $this->showMain();
        }
        if ( $clickedAddUserAcl ) {
            $this->objPermAcl->addUser( $acl, $userId );
            return $this->showMain();
        }
        if ( $clickedAddGroupAcl ) {
            $this->objPermAcl->addGroup( $acl, $groupId );
            return $this->showMain();
        }
    }

    /**
    * Method to show the edit group members view
    * @access private
    * @return string the template file name.
    */
    function showEdit( )
    {
        return 'edit_acl_tpl.php';
    }
    
    /**
    * Method to process the edit form post/get elements
    * @access private
    * @return string the template file name.
    */
    function processEditForm( )
    {
        $clickedUserInsert  = $this->getParam('btnUserInsert')=='clicked';
        $clickedUserRemove  = $this->getParam('btnUserRemove')=='clicked';
        $clickedUserSave    = $this->getParam('btnUserSave');
        $clickedGroupInsert = $this->getParam('btnGroupInsert')=='clicked';
        $clickedGroupRemove = $this->getParam('btnGroupRemove')=='clicked';
        $clickedGroupSave   = $this->getParam('btnGroupSave');

        $userIn      = $this->getParam( 'lstUserIn' );
        $userNotIn   = $this->getParam( 'lstUserNotIn' );
        $groupIn     = $this->getParam( 'lstGroupIn' );
        $groupNotIn  = $this->getParam( 'lstGroupNotIn' );

        $aclId = $this->getParam( 'aclId' );
        $this->setVarByRef('aclId', $aclId );

        if ( $clickedUserInsert ) {
            $this->objPermAcl->addAclUser( $aclId, $userNotIn );
        }
        if ( $clickedUserRemove ) {
            $this->objPermAcl->deleteAclUser( $aclId, $userIn );
        }
        if ( $clickedUserSave ) {
            return $this->showCreate();
        }
        if ( $clickedGroupInsert ) {
            $this->objPermAcl->addAclGroup( $aclId, $groupNotIn );
        }
        if ( $clickedGroupRemove ) {
            $this->objPermAcl->deleteAclGroup( $aclId, $groupIn );
        }
        if ( $clickedGroupSave ) {
            return $this->showCreate();
        }
        return $this->showEdit();
    }
}
?>