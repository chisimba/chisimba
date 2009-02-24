<?php
/**
 * groupusersdb class
 * 
 * The groupusersdb class is used to access the groupusers table data.
 *
 * PHP version 5
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
 * 
 * @category  Chisimba
 * @package   groupadmin
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * The groupusersdb class is used to access the groupusers table data.
 * This class is responsible for maintaining the groupusers data.
 * <PRE>
 * Public Inteface:
 *   addGroupUser      - To insert a user into a group in the group hierarchy.
 *   deleteGroupUser   - To remove a user from a group in the group hierarchy.
 *   getUserDirectGroups- To get all the direct groups for this user.
 *   getUserGroups     - To get all the direct and subgroups for this user.
 *   getGroupUsers     - To get all the direct users for this group.
 *   getNotGroupUsers  - To get all the users not directly in this group.
 *   getSubGroupUsers  - To get all direct and subgroups for this user.
 *   isGroupMember     - To test if the user is a member of the direct group.
 *   isSubGroupMember  - To test if the user is a member of the direct and subgroups.
 *</PRE>
 * @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package    groupadmin
 * @subpackage access
 * @version    0.1
 * @since      22 November 2004
 * @author     Paul Scott based on methods by Jonathan Abrahams
 * @filesource
 */

class groupusersdb extends dbTable
{
    /**
    * @var usersDb $_objUsers the connection to users database.
    */
    var $_objUsers;

    /**
    * @var usersDb $_objGroups the connection to groups database.
    */
    var $_objGroups;

    /**
    * Method to initialize the groupuserDb object.
    */
    function init( ) {
        // Initialize the connection to the database
        parent::init('tbl_groupadmin_groupuser');
        // The connectUsers, and connectGroups methods must be called as well.
        // The objects of users and groups are connected to by these methods.
		$this->_objUsers = $this->getObject('user', 'security');
    }

    /**
    * Method to connect to the groups object.
    *
    * @param groupadminmodel &$groups a reference to the groupadmin object, which access the groups table.
    */
    function connectGroups( &$groups ) {
        $this->_objGroups =& $groups;
    }

    /**
    * Method to connect to the users object
    *
    * @param usersDb &$users a reference to the usersDb object, which access the users table.
    */
    function connectUsers( &$users ) {
        $this->_objUsers =& $users;
    }

    /**
    * Method to get the groups object
    *
    * @return groupadmin return a reference to the groupadmin object
    */
    function & groups() {
        return $this->_objGroups;
    }

    /**
    * Method to get the groups object
    *
    * @return usersDb return a reference to the usersDb object
    */
    function & users() {
        return $this->_objUsers;
    }

  ////////////////////////////////////////////////////////////////////
 //------------------ GROUPUSERS table methods --------------------//
////////////////////////////////////////////////////////////////////
    /**
    * Method to insert a user into a group in the group hierarchy.
    *
    * @param string The unique ID of an existing group.
    * @param string The unique ID of an existing user. NB use PKid( userId ) method in user class
    */
    function addGroupUser( $groupId, $userId ) {
        $newGroupUser = array ();
        $newGroupUser['user_id']  = $userId;
        $newGroupUser['group_id'] = $groupId;

        $newGroupUser['last_updated_by'] = $this->_objUsers->userId();
        $newGroupUser['last_updated']    = date("Y-m-d H:i:s");

        return $this->insert( $newGroupUser );
    }

    /**
    * Method to delete a user from a group in the group hierarchy.
    *
    * The user is a member of the group.
    *
    * @param  string The unique ID of an existing group.
    * @param  string The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return true   |false TRUE on success, FALSE on failure
    */
    function deleteGroupUser( $groupId, $userId  ) {
        return parent::delete( 'user_id', "$userId' AND group_id = '$groupId" );
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
    * @param  string $usersId
    *                         the id of an existing users.
    * @return array 
    *                the list of groupId's of the groups this user is a member of.
    */
    function getUserDirectGroups( $userId ) {
        // Get the table names.
        $tblGroupUsers = $this->_tableName;

        $sql = "SELECT group_id";
        $sql.= " FROM $tblGroupUsers";
        $sql.= " WHERE ( user_id = '$userId' )";

        return $this->getArray( $sql );
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
    *
    * @param  string The unique ID of the user. NB use PKid( userId ) method in user class
    * @return array  The list of unique ID for groups as an array.
    */
    function getUserGroups( $userId ) {
        // Get the objects.
        $groups =& $this->groups();
       

        // Get the direct groups the user is a member of.
        $directGroups = $this->getUserDirectGroups( $userId );

        // Get subgroups for direct groups.
        $userGroups = array( );
        foreach( $directGroups as $row ) {
            $groupId = $row['group_id'];
            if( !in_array( $groupId, $userGroups ) ) {
                $subGroups = $groups->getSubGroups( $groupId );
                $userGroups = array_unique( array_merge( $userGroups, $subGroups ) );
            }
        }

        return  $userGroups ;
    }
    /**
    * Method to get all the direct members for this group.
    *
    * @param  string      The unique ID of an existing group.
    * @param  string      (   optional ) Default is unique ID of the user.
    * @param  string      (   optional ) a SQL WHERE clause.
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    function getGroupUsers( $groupId, $fields = null , $filter = null ) {
        // Get the objects.
        $users =& $this->users();
        // Get the table names.
        $tblGroupUsers = $this->_tableName;
        $tblUser       = $users->_tableName;

        $sql = "SELECT ";
        $sql.= $fields
                ? ( is_array( $fields ) ? implode ( ',', $fields ) : $fields )
                : "$tblUser.id,userid , firstName, surname";
        $sql.= " FROM $tblGroupUsers";

        $join = " INNER JOIN $tblUser";
        $join.= " ON ( ( user_id = $tblUser.id ) AND ( group_id = '$groupId' ) )";
        $filter = $filter ? $filter : ereg('firstName',$sql)? " ORDER BY UPPER(firstName)" : NULL;

        return $this->getArray( $sql.$join.$filter );
    }

    /**
    * Method to get all the users not directly in this group.
    *
    * @param  string      The unique ID of an existing group.
    * @param  string      (   optional ) Default is unique ID of the user.
    * @param  string      (   optional ) a SQL WHERE clause.
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    function getNotGroupUsers( $groupId, $fields = null, $filter =  null ) {
        // Get the objects.
        $users =& $this->users();

        // Get the table names.
        $tblGroupUsers = $this->_tableName;
        $tblUser       = $users->_tableName;

        // Get the data from the database.
        $sql = "SELECT ";
        // Select the given fields or all the fields.
        $sql.= $fields ? implode ( ',', $fields ) : "$tblUser.id, 'firstName' || ' ' || 'surname' as fullname ";
        $sql.= " FROM $tblGroupUsers";
        // Get the users who belongs this group
        $join = " RIGHT JOIN $tblUser";
        $join.= " ON (user_id = $tblUser.id)";
        $join.= " AND (group_id = '$groupId')";
        $join.= " WHERE ( user_id IS NULL ) ";

        // Return the users for the given group in an array.
        return $this->getArray( $sql.$join.$filter );
    }

    /**
    * Method to test if the user is a member of this group directly.
    *
    * @param  string     The unique ID of the user. NB use PKid( userId ) method in user class
    * @param  string     The unique ID of the group.
    * @return true|false returns TRUE if user is a member, otherwise FALSE
    */
    function isGroupMember( $userId, $groupId ) {
        // Get the table name.
        $tblGroupUsers = $this->_tableName;

        $sql = "SELECT COUNT( * ) as count";
        $sql.= " FROM $tblGroupUsers";
        $sql.= " WHERE group_id = '$groupId' ";
        // only this user
        $sql.= " AND user_id = '$userId'";

        // Run query to check if the user is a member of this group
        $rows = $this->getArray( $sql );

        // Return True|False TRUE - user is a member, FALSE - user not a member
        return $rows[0]['count']>0;
    }

    /**
    * Method to test if the user is a member of this group or its subgroups.
    *
    * @param  string     The unique ID of the user. NB use PKid( userId ) method in user class
    * @param  string     The unique ID of the group.
    * @return true|false returns TRUE if user is a member, otherwise FALSE
    */
    function isSubGroupMember( $userId, $groupId ) {
        // Get the objects.
        $groups        =& $this->groups();
        // Get the table name.
        $tblGroupUsers = $this->_tableName;

        // Get Subgroups for this groupId
        $SubGroups = $groups->getSubgroups( $groupId );
        $lstGroups = "'".implode("', '", $SubGroups )."'";

        $sql = "SELECT COUNT( * ) as count";
        $sql.= " FROM $tblGroupUsers";
        $sql.= " WHERE group_id IN ( $lstGroups )";
        $sql.= " AND user_id = '$userId'";

        $rows = $this->getArray($sql);

        // Return True|False TRUE - user is a member, FALSE - user not a member
        return $rows[0]['count']>0;
    }

    /**
    * Method to get all direct and subgroups for this user.
    *
    * @param  string      The unique ID of the group.
    * @param  string      (   optional ) Default is unique ID of the user.
    * @param  string      (   optional ) a SQL WHERE clause.
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    function getSubGroupUsers($groupId, $fields=null, $filter = null) {
        // Get the objects.
        $groups        =& $this->groups();
        $users         =& $this->users();

        // Get the table names.
        $tblGroupUsers = $this->_tableName;
        $tblUser       = $users->_tableName;

        // Get Subgroups for this groupId
        $SubGroups = $groups->getSubgroups( $groupId );
        $lstGroups = "'".implode("', '", $SubGroups )."'";

        $sql = "SELECT ";
        $sql.= $fields ? implode (',', $fields ) : "firstname , surname ";
        $sql.= " FROM $tblGroupUsers, $tblUser";
        $sql.= " WHERE group_id IN ( $lstGroups )";
        $sql.= " AND $tblUser.id = user_id";
        //$sql.= " GROUP BY $tblUser.id ";
        // Return the users for the given group in an array.
        $result = $this->getArray( $sql.$filter );
        return $result;
    }
    
    /**
     * Method to get the  user's roles in the different groups
     * @param  string $userId
     * @return array 
     * @access public
     */
    public function getUserRoles($userId)
    {
        $sql = "WHERE user_id = '".$userId."' ";
           $arr = $this->getAll($sql);
           
           return  $arr;
    }
}
?>
