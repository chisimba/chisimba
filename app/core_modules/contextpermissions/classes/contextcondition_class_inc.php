<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextpermissions
* @subpackage access
* @version 0.1
* @since 04 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/
// Inheret methods from Conditions
$this->loadClass( 'condition', 'decisiontable');
/**
 * Class used for maintaining a list of conditions of type context.
 *
 * @package contextpermissions
 * @category access
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 *
 * @access public
 * @author Jonathan Abrahams
 */
class contextCondition extends condition
{
    // --- ATTRIBUTES ---
    /**
     * Object reference to the Group Admin module via its facet interface.
     *
     * @access private
     * @var groupAdminModel
     */
    var $_objGroupAdmin = NULL;

    /**
     * Object reference to the Security module using User object
     *
     * @access private
     * @var User
     */
    var $_objUser = NULL;

    /**
     * Object reference to the Context module using class DbContext.
     *
     * @access private
     * @var dbcontext
     */
    var $_objDBContext = NULL;

    /**
     * Object reference to the Permissions module via its facet interface.
     *
     * @access private
     * @var permissions_model
     */
    var $_objPermissions = NULL;

    /**
     * Property used for storing the delimiter used when evaluating the group path.
     *
     * @access private
     * @var string
     */
    var $_delimiter = '/';


   // --- OPERATIONS ---

    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        $this->_objGroupAdmin = &$this->getObject('groupadminmodel','groupadmin');
        $this->_objUser = &$this->getObject('user','security');
        $this->_objPermissions = &$this->getObject('permissions_model','permissions');
        $this->_objDBContext = &$this->getObject('dbcontext','context');
        parent::init();
        // Store the class type.
        $this->_class = 'contextcondition';
        $this->_moduleName = 'contextpermissions';
    }


    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array
     */
    function isAdmin()
    {
        return $this->_objUser->isAdmin();
    }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array
     */
    function dbFieldCheck($tableName='', $fieldName='')
    {
        if( $tableName=='' || $fieldName=='' ) {
            return FALSE;
        }
        $id = $this->getParam( 'id', FALSE ) ? $this->getParam( 'id' ) : '';

       $sql = "SELECT $fieldName FROM $tableName WHERE id='$id'";
       $row = $this->getArray($sql);
       if( !empty($row) ) {
           return ( $row[0][$fieldName] == 1 ) ? TRUE : FALSE ;
       }
        return FALSE;
    }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return true|false
     */
     function dependsOnContext( $dependOn='TRUE' )
     {
         if( $dependOn ) {
             $dependOn = $this->setValue($dependOn) ? $this->_objDBContext->isInContext() : TRUE;
         }
         return $dependOn;
     }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @param string Group name relative to the context.
     * @author Jonathan Abrahams
     * @return true|false
     */
     function isContextMember($relPath=NULL)
     {
        // RETURN False if not in context.
        if( !$this->_objDBContext->isInContext() ) {
            return FALSE;
        }
        // Absolute path of context.
        $absPath = array($this->_objDBContext->getContextCode());
        // Relative path to Context/
        if( $relPath ) {
            $relPath = explode( $this->_delimiter, $relPath );
        }
        // Build new full Path of the groups within the context
        $fullPath = implode( $this->_delimiter, array_merge($absPath,$relPath) );
        // Evaluate result: TRUE means is a member, FALSE means is not a member
        $result = $this->isMember($fullPath);
        // Returns the result of the evaluation.
        return $result;
     }

    /**
     * CallBack method to evaluate the value parameter for groups.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Full path to the group seperated by a delimiter.
     * @return true|false Returns result of the evaluation.
     * @version V0.1
     */
    function isMember($absPath=NULL)
    {
        // String to Array using delimiter.
        $arrPath = explode($this->_delimiter,$absPath );
        //var_dump($arrPath);
        // Get the groupId for the given path.
        $groupId = $this->_objGroupAdmin->getLeafId( $arrPath );
        // Get the current users PKid
        $userPKId = $this->_objUser->PKId();
        //echo "$userPKId $groupId<br/>";
        // Evaluate result: TRUE means is a member, FALSE means is not a member
        $result = $this->_objGroupAdmin->isSubGroupMember($userPKId, $groupId );
        //var_dump($result);
        // Returns the result of the evaluation.
        return $result;
    }

    /**
     * Callback method to evaluate the value parameter for permissions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Access control list reference name.
     * @return true|false Returns result of the evaluation.
     * @version V0.1
     */
    function hasPermission($aclName=NULL)
    {
        // Get the latest acls
        $this->_objPermissions->updateSession();
        // Evaluate result: TRUE means has access | FALSE means has no access
        $result = $this->_objPermissions->checkAclByName( $aclName );
        // Returns the result of the evaluation.
        return $result;
    }

    /**
     * Callback method to evaluate the value parameter for context permissions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Access control list reference name for this context.
     * @return true|false Returns result of the evaluation.
     * @version V0.1
     */
    function hasContextPermission($aclName=NULL)
    {
        // RETURN False if not in context.
        if( !$this->_objDBContext->isInContext() ) {
            return FALSE;
        }
        // get the context code
        $contextCode = $this->_objDBContext->getContextCode();

        // Get the latest acls
        $this->_objPermissions->updateSession(); // MUST GO IN FINAL RELEASE //
        // Evaluate result: TRUE means has access | FALSE means has no access
        $result = $this->_objPermissions->checkAclByName( $contextCode.'_'.$aclName );
        // Returns the result of the evaluation.
        return $result;
    }
} /* end of class contextCondition */
?>