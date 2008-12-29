<?php
 /**
 * Permissions_model class
 * 
 * The permissions module GUI is used to select acls for use in your table.
 * These acls are for your permission requirements.
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
 * @package   security
 * @author Jonathan Abrahams
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

  /** 
  * Class to manage the access control list data. ( NOT COMPLETED ).
  *
  * The permissions module GUI is used to select acls for use in your table.
  * These acls are for your permission requirements.
  *
  * EXAMPLE of use: <code>
  * $permissions =& $this->getObject('permissions_model','security');
  * //Fetch acls from your table.
  * $acls = array( 'aclAdmin'=>$row['aclAdmin'], 'aclSuperUser'=>$row['aclSuperUser'] ); 
  *
  * //Single check
  * $isAdmin = $permissions->checkAcl( $acls['aclAdmin'] ); 
  * Result: $isAdmin = true|false
  *
  * //Multiple check
  * $hasPermissions = $permissions->checkAcl( $acls );
  * Result: $hasPermissions['aclAdmin'] = true|false;
  * Result: $hasPermissions['aclSuperUser'] = true|false;
  * </code>
  *  @author Jonathan Abrahams
  */
class permissions_model extends dbTable
{
  /**
  * @var array $capabilityList a list acls the current users has permissions for.
  */
  var $capabilityList=array();

  var $userId;
  
  function init()
  {
    // The capabilityList is persistent for this session.
    // Initialize the capabilityList for this instance.
    if ($this->getSession('permissions')) {
       // The capability list is available so fetch it from the session variable.
       $this->_fetchCapabilityList();
    } else {
       // This is the first instance of the permission_model( normally at login after authentication )
       // The capabilityList must be generated, ie. get it from the tbl_acl table.
       // The session permissions variable is initialized as well.
       $this->_generateCapabilityList();
    }
  }
  
  /**
  * Method to check the acls, against the users capabilityList( acls the user has permission to).
  * @param string|array $acl a single acl can be checked(string), or multiple acls can be checked(array).
  * @return true|false|array the returned value depends on the $acl input parameter. 
  *  if a single acl is check a true|false will result.
  *  if multiple acls were checked the same array sent back with the corrosponding acl values set to true|false
  */ 
  function checkAcl( $acl )
  {
    // IF multiple acls in an arrays are checked.
    if ( is_array($acl) ) {
      // initialize the return array.
      $checked = array();
      // Each acl is check in the $acl input array.
      foreach( $acl as $key => $val )
      {
        // Each acl is checked and a corrosponding key is created for the result (true|false)
        $checked[$key] = in_array ( $val, $this->capabilityList );
      }
       // Return the checked acls with key values corrosponding to the input array
       // and the value are set true|false based on the check.
       return $checked;
    // IF a single acl in a string is checked
    } else if ( is_string($acl) ) {
       // Return true|false based on the check.
       return in_array ( $acl, $this->capabilityList );
    }
  }
  
  /**
  * Method to update the persistent sesssion variable.
  * It generates the cababilityList for the current user.
  * This method is called at login after the user authentication.
  */
  function _generateCapabilityList( )
  {
    // A list of ACLs for the current user
    // This list will be generated using a query on the tbl_acl,and tbl_groupadmin_groupuser
    // Tempary acls for testing purposes.
    // these are set for the current user.( for testing purposes )
    //$this->capabilityList = array ( 'admin','lecturer','student' );
    // Get the users acls from the permissions acl class.
    $acls = $this->getObject('permissions_acl','permissions');

    $this->capabilityList = $acls->getUserAcls( $_SESSION['userId'] );
    
    // Set the session variable
    //$this->setSession('permissions',$this->capabilityList);
    $_SESSION['permissions'] = $this->capabilityList;
//    print_r( $_SESSION );
  }
  
  /**
  * Method to fetch the capability list from the session permissions variable.
  */
  function _fetchCapabilityList()
  {
    // The assumption is the list has already been made persistent( made available in the session variable) at initialization.
    $this->capabilityList = $this->getSession('permissions');
  }
} 
?>
