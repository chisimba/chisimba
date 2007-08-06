<?php
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
* @subpackage service
* @version    0.1
* @since      22 November 2004
* @author     Jonathan Abrahams
* @filesource
*/

class manageGroup extends object
{
    /**
    * @var string The unique id for the group being managed.
    */
    var $groupId;
    /**
    * @var groupadminmodel The object reference to the groupadmin object.
    */
    var $objGroupAdmin;
    
    /**
    * @var object HTML element
    */
    var $objElement;
    
    /**
    * Method to initialise the manage group object.
    */    
    function init()
    {
        $this->groupId = NULL;
        $this->viewType = 'dropdown';
        $this->objElement = NULL;
        
        $this->objGroupAdmin = $this->getObject( 'groupadminmodel', 'groupadmin' );
    }
    
    /**
    * Method to set the group to be managed, using the group
    *
    * @param string The full path to the group.
    */
    function setGroupPath( $groupPath )
    {
        $this->groupId = $this->objGroupAdmin->getLeafId( explode( '/', $groupPath) );
    }

    /**
    * Method to set the group to be managed, using the groupId.
    *
    * @param string The group id of the group.
    */    
    function setGroupId( $groupId )
    {
        $this->groupId = $groupId;
    }
    
    /**
    * Method to get the users of the group.
    *
    * @param  string The list of fields
    * @param  string The SQL Filter.
    * @return array  Associative array of users of the group.
    */
    function getUsers($fields=NULL,$filter=NULL)
    {   
        $groupId = $this->groupId;
        $fields = $fields ? implode(',',$fields) : array("tbl_users.id","tbl_users.userId"," 'firstName' || ' ' || 'surname' as fullName");
        $filter = NULL;
        $data = $this->objGroupAdmin->getGroupUsers( $groupId, $fields, $filter );
        return $data;
    }
    
    /**
    * Method to set the group to be managed, using a drop down.
    *
    * @return object The object element for the drop down.
    */      
    function setDropDown()
    {
        $this->objElement = $this->getObject( 'dropdown', 'htmlelements' );
        $this->objElement->name = 'manage_group';
        
        $data = $this->getUsers();
        
        foreach( $data as $item ) {
            $value = $item['userId'];
            $label = $item['fullName'];
            $this->objElement->addOption($value,$label);
        }
        
        return $this->objElement;
    }
   
    /**
    * Method to show the group to be managed.
    *
    * @return string The HTML elements.
    */       
    function show()
    {
        return $this->objElement->show();
    }
    
    /**
    * Method to get the selected user.
    *
    * @return array The row containing the users details.
    */ 
    function getUser()
    {
        $userId = $this->getParam('manage_group',NULL);
        $data = array();
        if( !is_null( $userId ) ) {
            $objUser = $this->objGroupAdmin->_objUsers;
            $data = $objUser->getRow('userId', $userId);
        }
        return $data;
    }
}
?>