<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextgroups
* @subpackage access
* @version 0.1
* @since 15 February 2005
* @author Jonathan Abrahams
* @filesource
*/
/**
* Class used to manage the groups for the context.
*/
class manageGroups extends object
{
    /**
    * @var dbContext Reference to context module.
    */
    var $_objDBContext = NULL;
    /**
    * @var groupAdminModel Reference to groupadmin module.
    */
    var $_objGroupAdmin = NULL;
    /**
    * @var permissions Reference to permissions module.
    */
    var $_objPermissions = NULL;

    /**
    * @var user Reference to user class in security module.
    */
    var $_objUser = NULL;

    /**
    * @var array the list of subgroups and its members
    */
    var $_arrSubGroups = array();

    /**
    * @var array the list of ACLS and its groups.
    */
    var $_arrAcls = array();

    /**
    * Method to initialise the object
    */
    function init()
    {
        $this->_objDBContext = &$this->getObject('dbcontext','context');
        $this->_objGroupAdmin = &$this->getObject('groupadminmodel','groupadmin');
        $this->_objPermissions = &$this->getObject('permissions_model','permissions');
        $this->_objUser = &$this->getObject('user','security');

        $this->currentUser = $this->_objUser->PKId();
        $this->lectGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getcontextcode(), 'Lecturers' ) );
        $this->studGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getcontextcode(), 'Students' ) );
        $this->guestGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getcontextcode(), 'Guest' ) );

        $this->_arrAcls= array();
        $this->_arrAcls['isAuthor']['id'] = NULL;
        $this->_arrAcls['isAuthor']['groups'] = array('Lecturers');

        $this->_arrAcls['isEditor']['id'] = NULL;
        $this->_arrAcls['isEditor']['groups'] = array('Lecturers');

        $this->_arrAcls['isReader']['id'] = NULL;
        $this->_arrAcls['isReader']['groups'] = array('Lecturers','Students','Guest');

        $this->_arrAcls['isPrivate']['id'] = NULL;
        $this->_arrAcls['isPrivate']['groups'] = array( 'Lecturers','Students' );

        $this->_arrSubGroups = array();
        $this->_arrSubGroups['Lecturers']['id'] = NULL;
        $this->_arrSubGroups['Lecturers']['members'] = array($this->currentUser);

        $this->_arrSubGroups['Students']['id'] = NULL;
        $this->_arrSubGroups['Students']['members'] = array();

        $this->_arrSubGroups['Guest']['id'] = NULL;
        $this->_arrSubGroups['Guest']['members'] = array();
    }
    /**
    * Method to create the acls for a new context
    * @param string The context code of a new context.
    * @param string The Title of a new context.
    */
    function createAcls( $contextcode, $title )
    {
        foreach( $this->_arrAcls as $aclName=>$row ) {
            $newAclId = $this->_objPermissions->newAcl(
                $contextcode.'_'.$aclName,
                'Access control list for '.$title );
            $this->_arrAcls[$aclName]['id'] = $newAclId;
        }
        // Add the groups to the acls
        $this->addAclGroups();
    }

    /**
    * Method to create the groups for a new context
    * @param string The context code of a new context.
    * @param string The Title of a new context.
    */
    function createGroups( $contextcode, $title )
    {
        // Context node
        $contextGroupId = $this->_objGroupAdmin->addGroup($contextcode,$title,NULL);
        // For each subgroup
        foreach( $this->_arrSubGroups as $groupName=>$groupId ) {
            $newGroupId = $this->_objGroupAdmin->addGroup(
                $groupName,
                $contextcode.' '.$groupName,
                $contextGroupId);
            $this->_arrSubGroups[$groupName]['id'] = $newGroupId;
        } // End foreach subgroup

        // Add groupMembers
        $this->addGroupMembers();

        // Now create the ACLS
        $this->createAcls( $contextcode, $title );
    } // End createGroups

    /**
    * Method to import group members into context group.
    * <PRE>
    * $members = array();
    * $members['Lecturers'] = array(... PKId of members ... );
    * $members['Students'] = array(... PKId of members ... );
    * </PRE>
    * @param string|NULL the context code or NULL if it should be site wide.
    * @param array the list of users with pkids.
    * @return nothing.
    */
    function importGroupMembers( $contextcode, $members )
    {
        // For each subgroup
        foreach( $members as $groupName=>$users ) {
            // Context node or Site node
            $fullPath = $contextcode ?
                // IF context code give insert into context
                array( $contextcode, $groupName ) :
                // IF context code is NULL insert into site groups
                array( $groupName );
            $contextGroupId = $this->_objGroupAdmin->getLeafId( $fullPath );
            // Is valid groupId
            if( $contextGroupId ) {
                foreach( $users as $userPKId ) {
                    // No duplicates
                    $isMember = $this->_objGroupAdmin->isGroupMember( $userPKId, $contextGroupId );
                    if( !$isMember ) {
                        $this->_objGroupAdmin->addGroupUser( $contextGroupId, $userPKId );
                    }
                } // End foreach user
            } else { // End check valid groupId
                $this->objEngine->setErrorMessage( 'Could not find requested group $groupName in context $contextcode!' );
                break;
            }
        } // End foreach subgroup
    }

    /**
    * Method to add members to the groups for a new context
    */
    function addGroupMembers( )
    {
        foreach( $this->_arrSubGroups as $groupName=>$row ) {
            foreach( $row['members'] as $userPKId ){
                $this->_objGroupAdmin->addGroupUser( $row['id'], $userPKId );
            } // End foreach member
        } // End foreach subgroup
    } // End addGroupMembers

    /**
    * Method to add groups to the Access control lists for a new context
    */
    function addAclGroups( )
    {
        foreach( $this->_arrAcls as $aclName=>$row ) {
            foreach( $row['groups'] as $groupName ){
                $groupId = $this->_arrSubGroups[$groupName]['id'];
                $this->_objPermissions->addAclGroup( $row['id'], $groupId );
            } // End foreach group
        } // End foreach acl
    } // End addAclGroups

    /**
    * Method to delete the groups when the context is being deleted.
    */
    function deleteGroups( $contextcode )
    {
        // Delete groups
        $groupId=$this->_objGroupAdmin->getLeafId( array($contextcode) );
        $groupId=$this->_objGroupAdmin->deleteGroup($groupId);
        // Delete the acls for the context
        $this->deleteAcls( $contextcode );
    }

    /**
    * Method to delete the acls for this context.
    */
    function deleteAcls( $contextcode )
    {
        foreach( $this->_arrAcls as $aclName => $row ) {
            $aclId = $this->_objPermissions->getId( $contextcode.'_'.$aclName, 'name' );
            $this->_objPermissions->deleteAcl( $aclId );
        }
    }

    /**
    * Method to return all the contexts the user is a member of.
    * @param string UserId
    * @return array List of all context codes the user is a member of.
    */
    function usercontextcodes($userId=NULL)
    {
        // Get the users PKId.
        $userId = $this->_objUser->PKId( $userId );
        // Get all contextcodes
        $objContext = &$this->getObject('dbcontext','context');
        $arrcontextcodeRows = $objContext->getAll('ORDER BY contextcode');
        $arrcontextcodes = array();
        // Now check for membership
        foreach( $arrcontextcodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextcode']));
            // Check membership
            $isMember = $this->_objGroupAdmin->isSubGroupMember($userId,$groupId);
            // if member add to list
            if( $isMember ) {
                $arrcontextcodes[] = $row['contextcode'];
            }
        }
        // Users context list
        return $arrcontextcodes;
    }

    /**
    * Method to return all the contexts the user is a member of.
    * @param string UserId
    * @param array (Optional) The list of fields to get.
    * @return array List of all context codes the user is a member of.
    */
    function userContexts($userId=NULL, $fields=array() )
    {
        // Get the users PKId.
        $userId = $this->_objUser->PKId( $userId );

        $objContext = &$this->getObject('dbcontext','context');
        // Get all contextcodes
        if (empty($fields))
            $fields[]="*";
        else
            $fields[] = "contextcode";

        $sql = "SELECT ";
        $sql.= implode( ',', $fields );
        $sql.= " FROM ".$objContext->_tableName;
        $filter = NULL;
        $orderBy = " ORDER BY contextcode";

        $arrcontextcodeRows = $objContext->getArray($sql.$filter.$orderBy);
        if($this->_objUser->isAdmin()){
            return $arrcontextcodeRows;
        }
        //$arrcontextcodes = array();
		$arrContext = array();
        // Now check for membership
        foreach( $arrcontextcodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextcode']));
            // Check membership
            $isMember = $this->_objGroupAdmin->isSubGroupMember($userId,$groupId);
            // if member add to list
            if( $isMember ) {
                $arrContext[] = $row;
            }
        }
        // Users context list
        return $arrContext;
    }

   /**
    * Method to return all the contexts the user has a role membership of.
    * @param string UserId
    * @param string The role of the user Lecturer, Student, Guest
    * @return array List of all context codes the user is a member of.
    */
    function rolecontextcodes($userId,$role)
    {
        // Get the users PKId.
        $userId = $this->_objUser->PKId( $userId );
        // Get all contextcodes
        $objContext = &$this->getObject('dbcontext','context');
        $arrcontextcodeRows = $objContext->getAll();
        // Now check for membership
        foreach( $arrcontextcodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextcode'],$role));
            // Check membership
            $isMember = $this->_objGroupAdmin->isSubGroupMember($userId,$groupId);
            // if member add to list
            if( $isMember ) {
                $arrcontextcodes[] = $row['contextcode'];
            }
        }
        // User role in context list
        return $arrcontextcodes;
    }

    /**
    * Method to return all the contexts the user has a role membership of.
    * @param string (Optional)The role of the user in the context( Lecturers, Students, Guests )
    * @param string (Optional)The context code.
    * @param array (Optional)Select the fields from the tbl_groupadmin_groupuser and tbl_user tables.
    * @return array List of all the users in the given role for this context.
    */
    function contextUsers( $role=NULL, $contextcode=NULL, $fields=NULL )
    {
        // Get the current contextcode if requried
        $contextcode = $contextcode ? $contextcode : $this->_objDBContext->getcontextcode();
        // Define the full path to the group.
        $fullPath = $role ? array( $contextcode, $role ) : array( $contextcode );
        // Get the groupId for the given context.
        $groupId = $this->_objGroupAdmin->getLeafId( $fullPath );
        // Fields to retrieve.
        $fields = $fields ? $fields : array( "tbl_users.userId", "  'firstName' || ' ' || 'surname'  as fullName " );

        $arrGroupMembers = $this->_objGroupAdmin->getSubGroupUsers( $groupId, $fields );
        // Array of userId and fullnames
        return $arrGroupMembers;
    }

    /**
    * Method to return all the public contexts the user IS/NOT a membership of.
    * @param string (Optional)The userId
    * @param true|false (Optional) The TRUE is member, FALSE is not a member of the public contexts.
    * @param array (Optional) The list of fields to get.
    * @return array List of all the public contexts the user is/not a member of.
    */
    function publicContexts( $userId=NULL, $isMember=FALSE, $fields=array() )
    {
        // Get the users PKId.
        $userId = $this->_objUser->PKId( $userId );
        // Get all contextcodes
        $objContext = &$this->getObject('dbcontext','context');

        if (empty($fields))
            $fields[]="*";
        else
            $fields[] = "contextcode";

        $sql = "SELECT ";
        $sql.= implode( ',', $fields );
        $sql.= " FROM ".$objContext->_tableName;
        $filter = " WHERE isClosed<>'1'";
        $orderBy = NULL;
        $arrcontextcodeRows = $objContext->getArray($sql.$filter.$orderBy);

        // Now check for membership / non Membership
        $arrMemberCodes = array();
        $arrNonMemberCodes = array();
        foreach( $arrcontextcodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextcode']));
            // Check membership
            $isGroupMember = $this->_objGroupAdmin->isSubGroupMember($userId,$groupId);
            // if member add to member list
            if( $isGroupMember ) {
                $arrMemberCodes[] = $row;
            // else add to non-member list
            } else {
                $arrNonMemberCodes[] = $row;
            }
        }
        return $isMember ? $arrMemberCodes : $arrNonMemberCodes;
    }
} // End publicContext Class
?>
