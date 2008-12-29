<?php

/**
 * Class to view the members online counter.
 * 
 * @example 
 * <PRE>
 *   $objOnlineCount = $this->getObject('onlinecount','contextgroups');
 *   $objOnlineCount->setContextGroup('Students');
 *   $studs = $objOnlineCount->show();
 * </PRE>
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
 * @category  Chisimba
 * @package   contextgroups
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Class to view the members online counter.
 * 
 * @example 
 * <PRE>
 *   $objOnlineCount = $this->getObject('onlinecount','contextgroups');
 *   $objOnlineCount->setContextGroup('Students');
 *   $studs = $objOnlineCount->show();
 * </PRE>
 * 
 * @category  Chisimba
 * @package   contextgroups
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class onlineCount extends dbTable
{

    /**
     * Description for var
     * @var    object 
     * @access private
     */
    var $_objDBContext;

    /**
     * Description for var
     * @var    object 
     * @access private
     */
    var $_objGroupAdmin;

    /**
     * Description for var
     * @var    array  
     * @access private
     */
    var $_icons = array();

    /**
     * Description for var
     * @var    array  
     * @access private
     */
    var $_langText = array();

    /**
     * Description for var
     * @var    string 
     * @access private
     */
    var $_contextGroup = '';
    
    /**
    * Method to initialise the object.
    */
    function init()
    {
        $this->_objDBContext = $this->getObject('dbcontext','context');
        $this->_objGroupAdmin = $this->getObject('groupAdminModel','groupadmin');
        $this->_icons = array(
            'Lecturers'=>'lecturer',
            'Students' =>'student',
            'Guest'    =>'guest' );

        $this->_langText = array(
            'Lecturers'=>'mod_contextgroups_onlinelect',
            'Students' =>'mod_contextgroups_onlinestud',
            'Guest'    =>'mod_contextgroups_onlineguest' );

        parent::init('tbl_loggedinusers');
    }
    
    /**
    * Method to count the members logged in for the current context.
    * @return integer|false return the count of online users or false if not in context
    */
    function count()
    {
        if( $this->_objDBContext->isInContext() ) {
            // Get userIds of logged in users
            $userList = $this->getOnlineUsers();
            // Get memberIds for this context.
            $memberList = $this->getContextUsers();
            // Count the intersection of these users.
            return count( array_intersect( $memberList, $userList ) );
        } else {
            // Return false when not in context.
            return FALSE;
        }
    }

    /**
    *Method to set the context group to be counted.
    * @param  string   Member group Lecturers, Students, Guest.
    * @return nothing.
    */
    function setContextGroup( $groupName )
    {
        $this->_contextGroup = $groupName;
    }

    /**
    *Method to get all users that are online.
    * @return array of all online users ids.
    */
    function getOnlineUsers()
    {
        $sql = 'SELECT * FROM tbl_loggedinusers ';
        $join = $this->join(' INNER JOIN ',
            'tbl_users',
            array('userId'=>'userId'),
            'tbl_loggedinusers');
        $onlineUsers = $this->getArray( $sql.$join );
        
        $userIds = $this->_objGroupAdmin->getField( $onlineUsers, 'id' );
        return $userIds;
    }

    /**
    *Method to get all members for this context.
    * @return array|false of ids for members in current context, otherwise false.
    * @param  string      $contextCode
    */
    function getContextUsers($contextCode)
    {
        
        if( $this->_objDBContext->isInContext() ) 
    {
            if($contextCode == '')
            {
                $this->_contextCode = $this->_objDBContext->getContextCode();
            } else {
                $this->_contextCode = $contextCode;
            }
            $path = array( $this->_contextCode, $this->_contextGroup );
            $groupId = $this->_objGroupAdmin->getLeafId( $path );
            // Get userIds of members
            $groupUsers = $this->_objGroupAdmin->getGroupUsers( $groupId );
            $userIds = $this->_objGroupAdmin->getField( $groupUsers, 'id' );
            return $userIds;
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the user count for a context
    * @param  string $contextCode
    * @return int   
    */
    public function getUserCount($contextCode)
    {
        $objManageGroups =  $this->newObject('managegroups', 'contextgroups');

        $cnt =  count($objManageGroups->contextUsers('Students', $contextCode));

        return $cnt;

    }

    /**
    * Method to show the counted members.
    * return HTML Icon and Language text for the group being counted.
    */
    function show()
    {
        $icon     = $this->_icons[$this->_contextGroup] ;
        $langText = $this->_langText[$this->_contextGroup];

        $objLanguage = $this->getObject('language', 'language');
        $label = $objLanguage->code2Txt( $langText,array('authors'=>'','readonlys'=>'') );
        
        $objIcon = $this->newObject('geticon','htmlelements');
        $objIcon->setIcon( $icon );
        $objIcon->alt= $label;
        $objIcon->align = "absmiddle";
        
        $count = $this->count();
        
        return $count."&nbsp;".$objIcon->show()."&nbsp;".$label;
    }
}
?>