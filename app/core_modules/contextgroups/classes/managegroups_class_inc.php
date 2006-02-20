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
        $this->lectGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Lecturers' ) );
        $this->studGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Students' ) );
        $this->guestGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Guest' ) );

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
    function createAcls( $contextCode, $title )
    {
        foreach( $this->_arrAcls as $aclName=>$row ) {
            $newAclId = $this->_objPermissions->newAcl(
                $contextCode.'_'.$aclName,
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
    function createGroups( $contextCode, $title )
    {
        // Context node
        $contextGroupId = $this->_objGroupAdmin->addGroup($contextCode,$title,NULL);
        // For each subgroup
        foreach( $this->_arrSubGroups as $groupName=>$groupId ) {
            $newGroupId = $this->_objGroupAdmin->addGroup(
                $groupName,
                $contextCode.' '.$groupName,
                $contextGroupId);
            $this->_arrSubGroups[$groupName]['id'] = $newGroupId;
        } // End foreach subgroup

        // Add groupMembers
        $this->addGroupMembers();
        
        // Now create the ACLS
        $this->createAcls( $contextCode, $title );
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
    function importGroupMembers( $contextCode, $members )
    {
        // For each subgroup
        foreach( $members as $groupName=>$users ) {
            // Context node or Site node
            $fullPath = $contextCode ?
                // IF context code give insert into context
                array( $contextCode, $groupName ) :
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
                $this->objEngine->setErrorMessage( 'Could not find requested group $groupName in context $contextCode!' );
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
    function deleteGroups( $contextCode )
    {
        // Delete groups
        $groupId=$this->_objGroupAdmin->getLeafId( array($contextCode) );
        $groupId=$this->_objGroupAdmin->deleteGroup($groupId);
        // Delete the acls for the context
        $this->deleteAcls( $contextCode );
    }

    /**
    * Method to delete the acls for this context.
    */
    function deleteAcls( $contextCode )
    {
        foreach( $this->_arrAcls as $aclName => $row ) {
            $aclId = $this->_objPermissions->getId( $contextCode.'_'.$aclName, 'name' );
            $this->_objPermissions->deleteAcl( $aclId );
        }
    }
    
    /**
    * Method to return all the contexts the user is a member of.
    * @param string UserId
    * @return array List of all context codes the user is a member of.
    */
    function userContextCodes($userId=NULL)
    {
        // Get the users PKId.
        $userId = $this->_objUser->PKId( $userId );
        // Get all contextCodes
        $objContext = &$this->getObject('dbcontext','context');
        $arrContextCodeRows = $objContext->getAll('ORDER BY contextCode');
        $arrContextCodes = array();
        // Now check for membership
        foreach( $arrContextCodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextCode']));
            // Check membership
            $isMember = $this->_objGroupAdmin->isSubGroupMember($userId,$groupId);
            // if member add to list
            if( $isMember ) {
                $arrContextCodes[] = $row['contextCode'];
            }
        }
        // Users context list
        return $arrContextCodes;
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
        // Get all contextCodes
        if (empty($fields))
            $fields[]="*";
        else
            $fields[] = "contextCode";

        $sql = "SELECT ";
        $sql.= implode( ',', $fields );
        $sql.= " FROM ".$objContext->_tableName;
        $filter = NULL;
        $orderBy = " ORDER BY contextCode";

        $arrContextCodeRows = $objContext->getArray($sql.$filter.$orderBy);
        if($this->_objUser->isAdmin()){
            return $arrContextCodeRows;
        }
        //$arrContextCodes = array();
		$arrContext = array();
        // Now check for membership
        foreach( $arrContextCodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextCode']));
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
    function roleContextCodes($userId,$role)
    {
        // Get the users PKId.
        $userId = $this->_objUser->PKId( $userId );
        // Get all contextCodes
        $objContext = &$this->getObject('dbcontext','context');
        $arrContextCodeRows = $objContext->getAll();
        // Now check for membership
        foreach( $arrContextCodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextCode'],$role));
            // Check membership
            $isMember = $this->_objGroupAdmin->isSubGroupMember($userId,$groupId);
            // if member add to list
            if( $isMember ) {
                $arrContextCodes[] = $row['contextCode'];
            }
        }
        // User role in context list
        return $arrContextCodes;
    }

    /**
    * Method to return all the contexts the user has a role membership of.
    * @param string (Optional)The role of the user in the context( Lecturers, Students, Guests )
    * @param string (Optional)The context code.
    * @param array (Optional)Select the fields from the tbl_groupadmin_groupuser and tbl_user tables.
    * @return array List of all the users in the given role for this context.
    */
    function contextUsers( $role=NULL, $contextCode=NULL, $fields=NULL )
    {
        // Get the current contextCode if requried
        $contextCode = $contextCode ? $contextCode : $this->_objDBContext->getContextCode();
        // Define the full path to the group.
        $fullPath = $role ? array( $contextCode, $role ) : array( $contextCode );
        // Get the groupId for the given context.
        $groupId = $this->_objGroupAdmin->getLeafId( $fullPath );
        // Fields to retrieve.
        $fields = $fields ? $fields : array( "tbl_users.userId", " CONCAT( firstName, ' ', surname ) as fullName " );

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
        // Get all contextCodes
        $objContext = &$this->getObject('dbcontext','context');

        if (empty($fields))
            $fields[]="*";
        else
            $fields[] = "contextCode";
            
        $sql = "SELECT ";
        $sql.= implode( ',', $fields );
        $sql.= " FROM ".$objContext->_tableName;
        $filter = " WHERE isClosed<>'1'";
        $orderBy = NULL;
        $arrContextCodeRows = $objContext->getArray($sql.$filter.$orderBy);

        // Now check for membership / non Membership
        $arrMemberCodes = array();
        $arrNonMemberCodes = array();
        foreach( $arrContextCodeRows as $row ) {
            // Corrosponding groupId
            $groupId = $this->_objGroupAdmin->getLeafId(array($row['contextCode']));
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
