<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * The group admin model class is used to maintain the groups hierachy.
 * It processes and maintains all groups data, and acts as the
 * interface for external modules, making availale all its functionality.
 * <PRE>
 * Public Inteface:
 * Groups table
 *   getId             - To get the unique id for the group.
 *   getLeafId         - To get the unique id following the a path to the group.
 *   getDescription    - To get the description of a group.
 *   getFullPath       - To get the full path to the root group.
 *   getName           - To get the name of the group.
 *   setDescription    - To set the description of a group.
 *   setName           - To set the name of the group.
 *   addGroup          - To insert a group into group hierachy.
 *   deleteGroup       - To remove a group from the group hierarchy.
 *   getGroups         - To get all the groups without hierarchy.
 *   getSubgroups      - To get the descendents from this group down.
 *   getGroupsToRoot   - To get the ancestors from this group up.
 * GroupUsers table    - userId refers to unique Id use PKId( userId )
 *   addGroupUser      - To insert a user into a group in the group hierarchy.
 *   deleteGroupUser   - To remove a user from a group in the group hierarchy.
 *   getUserDirectGroups- To get all the direct groups for this user.
 *   getUserGroups     - To get all the direct and subgroups for this user.
 *   getGroupUsers     - To get all the direct users for this group.
 *   getNotGroupUsers  - To get all the users not directly in this group.
 *   getSubGroupUsers  - To get all direct and subgroups for this user.
 *   isGroupMember     - To test if the user is a member of the direct group.
 *   isSubGroupMember  - To test if the user is a member of the direct and subgroups.
 * Users table
 *   getUsers          - To get all the users.
 *</PRE>
 *
 * @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package groupadmin
 * @subpackage service
 * @version 0.1
 * @since 22 November 2004
 * @author Paul Scott based on methods by Jonathan Abrahams
 * @filesource
 */

class groupAdminModel extends dbTable
{

    /**
     * $_objUsers an association to the userDb object.
     *
     * @access public
     * @var userDb
     */
    public $_objUsers;

    /**
     * an association to the groupuserDb object.
     *
     * @access public
     * @var groupuserDb $_objGroupUsers
     */
    public $_objGroupUsers;

    /**
     * Method to initialize the group admin model object.
     *
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // Create instance of groupusersDb object, and connect back to groupsDb
        $this->_objGroupUsers =& $this->getObject( 'groupusersdb', 'groupadmin' );
        $this->_objGroupUsers->connectGroups( $this );

        // Create instance of usersDb object, and connect groupusersDb to usersDb
        $this->_objUsers =& $this->getObject( 'usersdb', 'groupadmin' );
        $this->_objGroupUsers->connectUsers( $this->_objUsers );

        // Use the tbl_groupadmin_group table to store the group data.
        parent::init('tbl_groupadmin_group');
    }

    /**
     * Method to get the groupusers object
     *
     * @access public
     * @param void
     * @return property
     */
    public function & groupusers()
    {
        return $this->_objGroupUsers;
    }

    /**
    * Method to get to the users object
    *
    * @access public
    * @param void
    * @return property
    */
    public function & users()
    {
        return $this->_objUsers;
    }


    /**
    * Method to insert a group into group hierachy.
    * The group description should suggest who the group members are,
    * and the parent group creates the group hierarchy.
    *
    * @access public
    * @param string $name the group name.
    * @param string $description a short description of the group, suggesting the member list.
    * @param string $parentId the unique id of this groups immediate ancestor.( optional default is null=root )
    * @return string|false the newly generated unique id for this group if successful, otherwise false.
    */
    public function addGroup( $name,  $description, $parentId = null )
    {
        // CIRCULAR References:
        //  /-[group1]<--[group2]<--[group3]
        //  \--------------------------^
        // This condition can only come about with an update operation.
        // Group1 cannot have its parent as group3, since group3 could
        // not exist yet.

        $newGroup = array();
        $newGroup['name'] = $name;
        $newGroup['description'] = $description;
        $newGroup['parent_id']   = $parentId;

        $newGroup['last_updated_by'] = $this->_objUsers->userId();
        $newGroup['last_updated']    = date("Y:m:d H:i:s");

        return parent::insert( $newGroup );
    }

    /**
     * Method to remove a group from the group hierarchy.
     * It cascade deletes the subgroups as well.
     *
     * @access public
     * @param string $groupId The unique ID of an existing group.
     * @return true|false true if successful, otherwise false.
     */
    public function deleteGroup( $groupId )
    {
        $gid = $this->getSubgroups( $groupId );
        foreach ( $gid as $grpId ) {
            parent::delete( 'id', $grpId );
        }
    }

    /**
     * Method to get all the groups( no hierarchy ).
     *
     * @access public
     * @param array $fields ( optional ) Default fields are unique ID and group name.
     * @param string $filter ( optional ) a SQL WHERE clause.
     * @return array|false Group rows as an array of associate arrays, or FALSE on failure
     */
    public function getGroups( $fields = array( "id", "name" ), $filter = null )
    {
        $tblGroup = $this->_tableName;

        $sql = "SELECT ";
        $sql.= $fields ? implode( ',', $fields ) : "*";
        $sql.= " FROM $tblGroup";

        return parent::getArray( $sql.$filter );
    }

    /**
     * Method to get the ancestors from this group up the hierarchy.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @return array the list of all groups to root excluding the given group.
     */
    public function getGroupsToRoot( $groupId )
    {
        $toRoot = array();

        $this->_getGroupsToRoot( $groupId, $toRoot );
        return $toRoot;
    }

    /**
     * Method to get the description of a group.
     * The unique id is used to identify the group.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @return string the group description.
     */
    public function getDescription( $groupId )
    {
        $row = parent::getRow( 'id', $groupId );
        return $row['description'];
    }

    /**
     * Method to get the unique id following the a path to the group.
     *
     * Returns the groupId of the last group name in the array with the path.
     * The path must start at the root down to the group needed, if not found
     * null is returned.
     *<PRE>
     * Example: getLeafId( array( 'myContext', 'Lectures' );
     * Returns: the Id for the Lecturers group for the context myContext.
     *</PRE>
     * to identify the row.
     *
     * @access public
     * @param array $arrPath an array with the path to the leaf group.
     * @return string|null returns the groupId if successful, otherwise null.
     */
    public function getLeafId( $arrPath )
    {
        $leafId = null;
        $this->_getGroupPath( null, $arrPath, $leafId );
        return $leafId;
    }

    /**
     * Method to get the unique id for the group.
     *
     * Returns the unique id. The name(default) or description fields can be used
     * to identify the row.
     *
     * @access public
     * @param string $pkValue any value that identifies the group based on the pkField
     * @param string $pkField the field to find the value( optional default is group name ).
     * @return string the unique id
     */
    public function getId( $pkValue, $pkField = 'name' )
    {
        $row = parent::getRow( $pkField, $pkValue );
        return $row['id'];
    }

    /**
     * Method to get the full path to the root group.
     *
     * The unique id is used to identify the group.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @return string the groups full path.
     */
    public function getFullPath( $groupId )
    {
        $groupsToRoot = $this->getGroupsToRoot( $groupId );
        $fullPath ='';
        foreach(  array_reverse( $groupsToRoot ) as $gId ) {
            $fullPath.= $this->getName( $gId );
            $fullPath.="/";
        }
        $groupName = $this->getName($groupId);
        $fullPath.= $groupName;
        return $fullPath;
    }

    /**
     * Method to get the name of a group.
     *
     * The unique id is used to identify the group.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @return string the group name
     */
    public function getName( $groupId )
    {
        $row = parent::getRow( 'id', $groupId );
        return $row['name'];
    }

    /**
     * Method to get the descendents from this group down the hierarchy.
     *
     * The given group is the starting point, and is included in the list.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @return array the list of all subgroups inclusive of given group.
     */
    public function getSubgroups( $groupId )
    {
        $subgroups = array();
        $subgroups[]= $groupId;

        $this->_getSubgroups( $groupId, $subgroups );
        return $subgroups;
    }

    /**
     * Method to set the description of a group.
     *
     * The unique id will not change, only the description field value.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @param string $newDescription the updated description for this group.
     * @return true|false true if successful, otherwise false.
     */
    public function setDescription( $groupId, $newDescription )
    {
        $updates = array();
        $updates['description'] = $newDescription;

        $updates['last_updated_by'] = $this->_objUsers->userId();
        $updates['last_updated']    = date("Y:m:d H:i:s");

        return $this->update( 'id', $groupId, $updates );
    }

    /**
     * Method to set the name of a group.
     *
     * The unique id will not change, only the name field value.
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @param string $newName the updated name for this group.
     * @return true|false true if successful, otherwise false.
     */
    public function setName( $groupId, $newName )
    {
        $updates = array();
        $updates['name'] = $newName;

        $updates['last_updated_by'] = $this->_objUsers->userId();
        $updates['last_updated']    = date("Y:m:d H:i:s");

        return $this->update( 'id', $groupId, $updates );
    }

    /**
     * Method to insert a user into a group in the group hierarchy.
     *
     * @see groupusersdb::addGroupUser()
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @param string The unique ID of an existing user. NB use PKid( userId ) method in user class
     * @return object
     */
    public function addGroupUser( $groupId, $userId )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->addGroupUser( $groupId, $userId );
    }

    /**
     * Method to delete a user from a group in the group hierarchy.
     *
     * @see groupusersdb::deleteGroupUser()
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @param string The unique ID of an existing user. NB use PKid( userId ) method in user class
     * @return true |false TRUE on success, FALSE on failure
     */
    public function deleteGroupUser( $groupId, $userId )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->deleteGroupUser( $groupId, $userId );
    }

    /**
     * Method to get all the direct users for this group.
     *
     * @see groupusersdb::getGroupUsers()
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @param string ( optional ) Default is unique ID of the user.
     * @param string ( optional ) a SQL WHERE clause.
     * @return array|false The user rows as an array of associate arrays, or FALSE on failure
     */
    public function getGroupUsers( $groupId, $fields = null, $filter = null )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->getGroupUsers( $groupId, $fields, $filter );
    }

    /**
     * Method to get all the users not directly in this group.
     *
     * @see groupusersdb::getNotGroupUsers()
     *
     * @access public
     * @param string The unique ID of an existing group.
     * @param string ( optional ) Default is unique ID of the user.
     * @param string ( optional ) a SQL WHERE clause.
     * @return array|false The user rows as an array of associate arrays, or FALSE on failure
     */
    public function getNotGroupUsers( $groupId, $fields = null, $filter = null )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->getNotGroupUsers( $groupId, $fields, $filter );
    }

    /**
     * Method to get all direct and subgroups for this user.
     *
     * @see groupusersdb::getSubGroupUsers()
     *
     * @access public
     * @param string The unique ID of the group.
     * @param string ( optional ) Default is unique ID of the user.
     * @param string ( optional ) a SQL WHERE clause.
     * @return array|false The user rows as an array of associate arrays, or FALSE on failure
     */
    public function getSubGroupUsers( $groupId, $fields = null, $filter = null )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->getSubGroupUsers( $groupId, $fields, $filter );
    }

    /**
     * Method to get the users direct membership to groups.
     *
     * This is users direct membership only.
     * <PRE>
     * + Root
     * |-+ Group1
     *   |-- [UserA]
     *   |-+ Group2
     *     |-+ Group3
     *
     * UserA has membership to Group1(direct)
     * </PRE>
     *
     * @see groupusersdb::getUserDirectGroups()
     * @access public
     * @param string The unique ID of an existing user. NB use PKid( userId ) method in user class
     * @return array The list of unique IDs for groups as an array.
     */
    public function getUserDirectGroups( $userId )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->getUserDirectGroups( $userId );
    }

    /**
     * Method to get the users group membership.
     * <PRE>
     * This is users direct and inherited membership.
     *
     * + Root
     * |-+ Group1
     *   |-- [UserA]
     *   |-+ Group2
     *     |-+ Group3
     *
     * UserA has membership to Group1(direct), Group2, Group3
     * </PRE>
     * @see groupusersdb::getUserGroups()
     * @access public
     * @param string The unique ID of the user. NB use PKid( userId ) method in user class
     * @return array The list of unique ID for groups as an array.
     */
    public function getUserGroups( $userId )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->getUserGroups( $userId );
    }

    /**
     * Method to test if the user is a member of this group directly.
     *
     * @see groupusersdb::isGroupMember()
     * @access public
     * @param string The unique ID of the user. NB use PKid( userId ) method in user class
     * @param string The unique ID of the group.
     * @return true|false returns TRUE if user is a member, otherwise FALSE
     */
    public function isGroupMember( $userId, $groupId )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->isGroupMember( $userId, $groupId );
    }

    /**
     * Method to test if the user is a member of this group or its subgroups.
     *
     * @see groupusersdb::isSubGroupMember()
     * @access public
     * @param string The unique ID of the user. NB use PKid( userId ) method in user class
     * @param string The unique ID of the group.
     * @return true|false returns TRUE if user is a member, otherwise FALSE
     */
    public function isSubGroupMember( $userId, $groupId )
    {
        $objGroupUsers =& $this->groupusers();
        return $objGroupUsers->isSubGroupMember( $userId, $groupId );
    }


    /**
    * Method to get all the users.
    *
    * The filter is applied to the user data.
    *
    * @access public
    * @param string ( optional ) Default is unique ID for user.
    * @param string ( optional ) a SQL WHERE clause.
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    public function getUsers( $fields = null, $filter = null )
    {
        $objUsers =& $this->users();
        return $objUsers->getUsers( $fields, $filter );
    }

    /**
     * Method to get a field from an multi dimensional array.
     *
     * The result of a dbTable::getArray() is usually passed as rows.
     *
     * @access public
     * @param array is associated array
     * @param string the field to get
     * @return array|false the only the required field as an array, otherwise FALSE
     */
    public function getField( $rows, $field )
    {
        $rowFields = array();
        foreach( $rows as $row ) {
            // Multi-dimensional
            if( is_array( $row ) ) {
                $rowFields[] = $row[$field];
            } else {
                return false;
            }
        }
        return $rowFields;
    }

    /**
     * Method to find the children nodes for the given node.
     *
     * It returns zero to many nodes.
     * @access public
     * @param string the reference node.
     * @return array array|false The child group rows as an array of associate arrays, or FALSE on failure
     */
    public function getChildren ( $node )
    {
        return $this->getAll( "WHERE parent_id = '$node'" );
    }

    /**
     * Method to find the parent node for the given node.
     *
     * It returns zero or one node.
     *
     * @access public
     * @param string the current node ( groupId ).
     * @return array|false The parent group rows as an array of associate arrays, or FALSE on failure
     */
    public function getParent ( $node )
    {
        return $this->getAll( "WHERE id = '$node'" );
    }

    /**
     * Method to find the root nodes.
     * It returns one to many nodes.
     *
     * @access public
     * @param void
     * @return array array|false The root group rows as an array of associate arrays, or FALSE on failure
     */
    public function getRoot ( )
    {
        return $this->getAll( "WHERE parent_id IS NULL  ORDER BY name" );
    }


    /**
     * Method to recursivly follow the path down the tree.
     *
     * Returns the node Id of the last node name in the path.
     *
     * @access private
     * @param string the current node ( groupId ).
     * @param array the names of the nodes to follow down the tree.
     * @param string the unique ID of the group leaf node.
     */
    private function _getGroupPath( $curNode, &$path, &$leaf )
    {

        if( is_null( $curNode ) ) {
            $result = $this->getRoot( );
        } else {
            $result = $this->getChildren( $curNode );
        }

        if ( $result ) {
            // Queue like first start with the first group name,
            // and move down following the path.
            $front = array_shift( $path );
            // Foreach of its children
            foreach( $result as $group ) {
                $groupId = $group['id'];
                // This method of using the name may lead to problems
                // The name is not unique, see example below
                // Root
                // |-+ CourseA
                // | |-+ Lecturers
                // |-+ CourseB
                // | |-+ Lecturers
                // |-+ CourseA
                // | |-+ Lecturers
                // The last matching path found will be returned.
                // (i.e. 'Root', 'CourseA', 'Lecturers' ) the last one.
                // I.E. CourseA should not have duplicates!!!
                $groupName = $group['name'];
                if( $front == $groupName ) {
                    $leaf = empty( $path ) ? $groupId : null;
                    $this->_getGroupPath( $groupId, $path, $leaf );
                }
            }
        }
    }

    /**
     * Method to recursivly search up the tree.
     *
     * @access private
     * @param string the current node ( groupId ).
     * @param array the array containing all the nodes found.
     */
    private function _getGroupsToRoot( $curNode, &$toRoot )
    {
        $result = $this->getParent( $curNode );

        if( $result ) {
            // Only one parent, foreach not really required
            foreach( $result as $group ) {
                $groupId = $group['parent_id'];
                // Stop if this is Root
                if ( $groupId ) {
                    $toRoot[] = $groupId;
                    $this->_getGroupsToRoot( $groupId, $toRoot );
                }
            }
        }
    }

    /**
     * Method to recursivly search down the tree.
     *
     * @access private
     * @param string the current node.
     * @param array the array containing all the nodes found.
     */
    private function _getSubgroups( $curNode, &$subgroups )
    {
        $result = $this->getChildren( $curNode );

        if ( $result ) {
            // Foreach of its children.
            foreach( $result as $group ) {
                $groupId = $group['id'];
                $subgroups[]= $groupId;
                $this->_getSubgroups( $groupId, $subgroups );
            }
            // Stops after all children found
        }
    }
}
?>