<?php 
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package permissions
* @subpackage access
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams 
* @filesource 
*/

/**
* The permissions acl class processes and maintains all acl data.
* <PRE>
* Public Inteface:
* ACL table
*    addAclUser        - To assign a user to an existing acl.
*    deleteAclUser     - To unassign a user from an acl.
*    addAclGroup       - To assign a group to an acl.
*    deleteAclGroup    - To unassign a group from an acl.
*    getAclUsers       - To get all the assigned users for this acl.
*    getAclGroups      - To get all the assigned groups for this acl.
*    getUserAcls       - To get this users assigned acls.
* </PRE>
* 
* @author Jonathan Abrahams 
*/
class permissions_acl extends dbTable 
{
    /**
    * @var user an object reference.
    */
    var $objUser;
    
    /**
    * Method to initialise an object.
    */
    function init()
    {
        $this->objUser = $this->getObject( 'user', 'security' );
        parent::init( 'tbl_permissions_acl' );
    } 

    /**
    * Method to assign a user to an existing acl.
    * 
    * @param string $acl The unique ID for the access control list.
    * @param string $userId The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return string|false the newly generated unique id for this acl row if successful, otherwise false.
    */
    function addAclUser( $acl, $userId )
    {
        $row = array();
        $row['acl_id'] = $acl;
        $row['user_id'] = $userId;
        $row['group_id'] = NULL;
        $row['last_updated'] = date( "Y:m:d H:i:s" );
        $row['last_updated_by'] = $this->objUser->userId();
        return parent::insert( $row );
    } 

    /**
    * Method to unassign a user from an acl.
    * 
    * @param string $aclId The unique ID for the access control list.
    * @param string $userId The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return true|false TRUE on success, FALSE on failure
    */
    function deleteAclUser( $aclId, $userId )
    {
        return parent::delete( 'user_id', "$userId' AND acl_id = '$aclId" );
    } 

    /**
    * Method to assign a group to an acl.
    * 
    * @param string $aclId The unique ID of an existing acl.
    * @param string $groupId The unique ID of an existing group.
    * @return true|false TRUE on success, FALSE on failure
    */
    function addAclGroup( $aclId, $groupId )
    {
        $row = array();
        $row['acl_id'] = $aclId;
        $row['user_id'] = NULL;
        $row['group_id'] = $groupId;
        $row['last_updated'] = date( "Y:m:d H:i:s" );
        $row['last_updated_by'] = $this->objUser->userId();
        return $this->insert( $row );
    } 

    /**
    * Method to unassign a group from an acl.
    * 
    * @param string $aclId The unique ID of an existing acl.
    * @param string $groupId The unique ID of an existing group.
    * @return true|false TRUE on success, FALSE on failure
    */
    function deleteAclGroup( $aclId, $groupId )
    {
        return parent::delete( 'group_id', "$groupId' AND acl_id = '$aclId" );
    } 

    /**
    * Method to get all the assigned users for this acl.
    * 
    * @param string $aclId The unique ID for this access control list.
    * @param string $fields ( optional )
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    function getAclUsers( $aclId, $fields = null )
    {
        $permission_aclDb = $this->_tableName;
        $usersDb = 'tbl_users';

        $sql = "SELECT ";
        $sql .= $fields ? implode( ",", $fields ) : " $usersDb.id, 'firstName' || ' ' || 'surname' as fullName";
        $sql .= " FROM $permission_aclDb";
        $join = " INNER JOIN $usersDb";
        $join .= " ON ( user_id = $usersDb.id )";
        $filter = " WHERE acl_id = '$aclId'";

        return $this->getArray( $sql . $join . $filter );
    } 

    /**
    * Method to get all the assigned groups for this acl.
    * 
    * @param string $aclId The unique ID for this access control list.
    * @param string $fields ( optional )
    * @return array|false The group rows as an array of associate arrays, or FALSE on failure
    */
    function getAclGroups( $aclId, $fields = null )
    {
        $permission_aclDb = $this->_tableName;
        $groupsDb = 'tbl_groupadmin_group';

        $sql = "SELECT ";
        $sql .= $fields ? implode( ",", $fields ) : "$groupsDb.id, name";
        $sql .= " FROM $permission_aclDb";
        $join = " INNER JOIN $groupsDb";
        $join .= " ON ( group_id = $groupsDb.id )";
        $filter = " WHERE acl_id = '$aclId'";

        return $this->getArray( $sql . $join . $filter );
    } 

    /**
    * Method to get this users assigned acls.
    * 
    * @param string $userId The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return array The list of unique ID for acls as an array.
    */
    function getUserAcls( $userId )
    {
        $permAclDb = $this->_tableName; 
        // Get the all groups the user belongs to, include subgroups
        $groupAdmin = &$this->getObject( 'groupAdminModel', 'groupadmin' );
        $subGroups = $groupAdmin->getUserGroups ( $userId ); 
        // Groups higher up the group-tree has access as well.
        // Eg. Context1/students/[user1]
        // Will give user access to Context1 and Context1/students
        $accessToGroups = array();
        foreach( $subGroups as $groupId ) {
            $groupToRoot = $groupAdmin->getGroupsToRoot( $groupId );
            $accessToGroups = array_unique ( array_merge( $subGroups, $groupToRoot ) );
        } 
        // Format the group list for the db query..
        $lstAccessGroups = "'" . implode( "','", $accessToGroups ) . "'"; 
        // User assigned acls
        $sql = "SELECT acl_id ";
        $sql .= " FROM $permAclDb";
        $sql .= " WHERE ( user_id = '$userId' )";
        $sql .= " OR ( group_id IN ( $lstAccessGroups ) ) ";

        $result = array();
        foreach ( parent::getArray( $sql ) as $acl ) {
            $result[] = $acl['acl_id'];
        } 
        // Return the list of all acls this user has access to..
        return $result ;
    } 
} 

?>