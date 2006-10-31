<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextgroups
* @subpackage view
* @version 0.1
* @since 14 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/

/**
* Class to view the members online counter.
* Example of its use
* <PRE>
*   $objOnlineCount = $this->getObject('onlinecount','contextgroups');
*   $objOnlineCount->setContextGroup('Students');
*   $studs = $objOnlineCount->show();
* </PRE>
* @author Jonathan Abrahams
*/
class onlineCount extends dbTable
{

    var $_objDBContext;
    var $_objGroupAdmin;

    var $_icons = array();
    var $_langText = array();
    var $_contextGroup = '';
    
    /**
    * Method to initialise the object.
    */
    function init()
    {
        $this->_objDBContext = &$this->getObject('dbcontext','context');
        $this->_objGroupAdmin = &$this->getObject('groupAdminModel','groupadmin');
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
    *@param string Member group Lecturers, Students, Guest.
    *@return nothing.
    */
    function setContextGroup( $groupName )
    {
        $this->_contextGroup = $groupName;
    }

    /**
    *Method to get all users that are online.
    *@return array of all online users ids.
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
    *@return array|false of ids for members in current context, otherwise false.
    *@param string $contextCode
    */
    function getContextUsers($contextCode)
    {
        
    	if( $this->_objDBContext->isInContext() ) {
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
    * Method to show the counted members.
    * return HTML Icon and Language text for the group being counted.
    */
    function show()
    {
        $icon     = $this->_icons[$this->_contextGroup] ;
        $langText = $this->_langText[$this->_contextGroup];

        $objLanguage = &$this->getObject('language', 'language');
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
