<?php
/**
 * usersdb class
 *
 * The usersDb class is used to access the users table data.
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
 * @author    Jonathan Abrahams
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
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
* @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package    groupadmin
* @subpackage access
* @version    0.1
* @since      22 November 2004
* @author     Jonathan Abrahams
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
        $this->_objUser = $this->getObject( 'user', 'security' );
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
    * @param  string      ( optional ) Default is unique ID for user.
    * @param  string      ( optional ) a SQL WHERE clause.
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    */
    function getUsers( $fields = null, $filter = null ) {
        // Get the table name
        $tblUsers = $this->_tableName;
        // Get the information from the database
        $sql = "SELECT ";
        // Select the given fields or all the fields.
        $sql.= $fields ? implode ( ',' , $fields ) : "id, CONCAT(firstName,' ',surname) as fullname" ;
        $sql.= " FROM $this->_tableName";
        
        $filter = $filter ? $filter : " ORDER BY UPPER(firstName)";
        
        //Return the users
        return $this->getArray($sql.$filter);
    }
    
}
?>
