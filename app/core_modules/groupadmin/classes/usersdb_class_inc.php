<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage access
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams
* @filesource
*/
  /**
  * The usersDb class is used to access the users table data.
  * This class is responsible for accessing the users data, 
  * to meet the groupadmin data needs.
  * @author Jonathan Abrahams
  */
class usersdb extends dbTable
{
    /**
    * @var user user object reference.
    */
    var $_objUser;
    
    /**
    * Method to initialize the groupuserDb object.
    */
    function init( ) {
        parent::init('tbl_users');
        $this->_objUser = &$this->getObject( 'user', 'security' );
    }

    /**
    * Method to get the logged in users Id.
    */
    function userId() {
        return $this->_objUser->userId();
    }
    
    /**
    * Method to get all the users.
    *
    * The filter is applied to the user data.
    *
    * @param string ( optional ) Default is unique ID for user.
    * @param string ( optional ) a SQL WHERE clause.
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    function getUsers( $fields = null, $filter = null ) {
        // Get the table name
        $tblUsers = $this->_tableName;
        // Get the information from the database
        $sql = "SELECT ";
        // Select the given fields or all the fields.
        $sql.= $fields ? implode ( ',' , $fields ) : "id, CONCAT( firstName, ' ', surname ) as fullname" ;
        $sql.= " FROM $this->_tableName";
        
        $filter = $filter ? $filter : " ORDER BY UPPER(firstName)";
        
        //Return the users
        return $this->getArray($sql.$filter);
    }
    
}
?>
