<?php 
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
/**
* 
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package permissions
* @subpackage access
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams 
* @filesource 
*/

/**
* The permission model class is responsible for processing and managing the access
* control list (ACL) data and tables.
* 
* @author Jonathan Abrahams 
*/
class permissions_model extends dbTable
{
    /**
    * 
    * @var array $capabilityList a list of acls for the current logged in user, which
    * is stored in the session variable.
    */
    var $capabilityList;
    /**
    * 
    * @var acl _descriptiondb an object reference.
    */
    var $_objAclDescriptionDb;
    /**
    * 
    * @var permissions _acl an object reference.
    */
    var $_objAcl;
    /**
    * 
    * @var user an object reference.
    */
    var $_objUserDb;

    /**
    * Method to initialize the permission model object
    * 
    * @access private 
    */
    function init()
    {
        $this->_objAcl = $this->getObject( 'permissions_acl', 'permissions' );
        $this->_objUserDb = $this->getObject( 'user', 'security' );
        $this->_objAclDescriptionDb = $this->getObject( 'acl_descriptiondb', 'permissions' ); 
        // The capabilityList is persistent for this session.
        // Initialize the capabilityList for this instance.
        if ( $this->getSession( 'permissions' ) ) {
            // The capability list is available so fetch it from the session variable.
            $this->fetchSession();
        } else {
            // This is the first instance of the permission_model( normally at login after authentication )
            // The capabilityList must be generated, ie. get it from the tbl_acl table.
            // The session permissions variable is initialized as well.
            $this->updateSession();
        } 
    } 
    // -------------- ALC DESCRIPTION table methods -------------//
    /**
    * Method to create new acls.
    * 
    * @param string $name the unique name of the access control list.
    * @param string $description a description for the access control list.
    * @access public 
    */
    function newAcl ( $name, $description )
    {
        return $this->_objAclDescriptionDb->newAcl( $name, $description );
    } 

    /**
    * Method to delete existing acls.
    * 
    * @param string $acl the unique id for the access control list.
    * @see acl_descriptiondb::deleteAcl()
    * @access public 
    */
    function deleteAcl ( $acl )
    {
        return $this->_objAclDescriptionDb->deleteAcl( $acl );
    } 

    /**
    * Method to get all acls.
    * 
    * @param string $fields ( optional )
    * @see acl_descriptiondb::getAcls()
    * @access public 
    */
    function getAcls ( $fields = null )
    {
        return $this->_objAclDescriptionDb->getAcls( $fields );
    } 

    /**
    * Method to get a single acl by its name(default).
    * 
    * @param string $pkValue the value of the primary key field( Default: name ).
    * @param string $pkField ( optional ) Default name.
    * @see acl_descriptiondb::getId()
    * @access public 
    */
    function getId ( $pkValue, $pkField = 'name' )
    {
        return $this->_objAclDescriptionDb->getId( $pkValue, $pkField );
    } 

    /**
    * Method to get an acls description.
    * 
    * @param string $acl the unique id for the access control list.
    * @see acl_descriptiondb::getDescription()
    * @access public 
    */
    function getDescription ( $acl )
    {
        return $this->_objAclDescriptionDb->getDescription( $acl );
    } 
    // -------------- ALC table methods -------------//
    /**
    * Method to assign a user to an existing acl.
    * 
    * @param string $acl The unique ID for the access control list.
    * @param string $userId The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return string|false the newly generated unique id for this acl row if successful, otherwise false.
    * @see permissions_acl::addAclUser()
    * @access public 
    */
    function addAclUser( $acl, $userId )
    {
        return $this->_objAcl->addAclUser( $acl, $userId );
    } 

    /**
    * Method to unassign a user from an acl.
    * 
    * @param string $acl The unique ID for the access control list.
    * @param string $userId The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return true|false TRUE on success, FALSE on failure
    * @see permissions_acl::deleteAclUser()
    * @access public 
    */
    function deleteAclUser( $acl, $userId )
    {
        return $this->_objAcl->deleteAclUser( $acl, $userId );
    } 

    /**
    * Method to assign a group to an acl.
    * 
    * @param string $acl The unique ID of an existing acl.
    * @param string $groupId The unique ID of an existing group.
    * @return true|false TRUE on success, FALSE on failure
    * @see permissions_acl::addAclGroup()
    * @access public 
    */
    function addAclGroup( $acl, $groupId )
    {
        return $this->_objAcl->addAclGroup( $acl, $groupId );
    } 

    /**
    * Method to unassign a group from an acl.
    * 
    * @param string $acl The unique ID of an existing acl.
    * @param string $groupId The unique ID of an existing group.
    * @return true|false TRUE on success, FALSE on failure
    * @see permissions_acl::deleteAclGroup()
    * @access public 
    */
    function deleteAclGroup( $acl, $groupId )
    {
        return $this->_objAcl->deleteAclGroup( $acl, $groupId );
    } 

    /**
    * Method to get all the assigned users for this acl.
    * 
    * @param string $acl The unique ID for this access control list.
    * @param string $fields ( optional )
    * @return array|false The user rows as an array of associate arrays, or FALSE on failure
    * @see permissions_acl::getAclUsers()
    * @access public 
    */
    function getAclUsers( $acl, $fields = null )
    {
        return $this->_objAcl->getAclUsers( $acl, $fields );
    } 

    /**
    * Method to get all the assigned groups for this acl.
    * 
    * @param string $acl The unique ID for this access control list.
    * @param string $fields ( optional )
    * @return array|false The group rows as an array of associate arrays, or FALSE on failure
    * @see permissions_acl::getAclGroups()
    * @access public 
    */
    function getAclGroups( $acl, $fields = null )
    {
        return $this->_objAcl->getAclGroups( $acl, $fields );
    } 

    /**
    * Method to get this users assigned acls.
    * 
    * @param string $userId The unique ID of an existing user. NB use PKid( userId ) method in user class
    * @return array The list of unique ID for acls as an array.
    * @see permissions_acl::getUserAcls()
    * @access public 
    */
    function getUserAcls( $userId )
    {
        return $this->_objAcl->getUserAcls( $userId );
    } 
    // ---------------------- PUBLIC method ---------------------//
    /**
    * Method to check if an acl is found in the capabilityList, which is the list of
    * acls the user has permissions to.
    * 
    * @return true|false|array return the permissions granted status of the given acls ( for single(true|false)
    * or mulitple(array returned as acl=>true|false ) acls )
    * @param string $|array $acl a single(string) or multiple(array) list of acls to be checked.
    * @access public 
    */
    function checkAcl( $acl )
    { 
        // IF multiple acls (arrays)
        if ( is_array( $acl ) ) {
            return $this->_chechArray( $acl ); 
            // IF a single acl (string)
        } else if ( is_string( $acl ) ) {
            // Return true|false based on the check.
            return in_array ( $acl, $this->capabilityList ); 
            // IF no access control list assume public access
        } else if ( is_null( $acl ) ) {
            return true;
        } 
    } 

    /**
    * Method to check for an acl by name.
    * 
    * @return true|false return the permission status of the given acls.
    * @param string $aclName a single(string) acl to be checked by name.
    * @access public 
    */
    function checkAclByName( $aclName )
    {
        $id = $this->getId( $aclName, 'name' );
        return $this->checkAcl( $id );
    } 

    /**
    * Method to access the acl table and find the acls assigned to the logged in users.
    * The session variable is updated.
    * 
    * @return nothing 
    * @access public 
    */
    function updateSession()
    { 
        // This list will be generated using a query on the tbl_acl,and tbl_groupadmin_groupuser
        // Get the users acls from the permissions acl class.
        $userId = $this->_objUserDb->userId();
        $userPkId = $this->_objUserDb->PKId( $userId );

        $this->capabilityList = $this->_objAcl->getUserAcls( $userPkId ); 
        // Set the session variable
        $this->setSession( 'permissions', $this->capabilityList );
    } 

    /**
    * Method to fetch the session variable containing the acls for the logged in user.
    * 
    * @return nothing 
    * @access public 
    */
    function fetchSession()
    { 
        // The assumption is the list has already been made persistent( made available in the session variable) at initialization.
        $this->capabilityList = $this->getSession( 'permissions' );
    } 
    // --------------------- PRIVATE methods --------------------//
    /**
    * Privarte method to check if a list of acls are found in the capabilityList.
    * 
    * @return array the permissions granted status in the form acl=>true|false.
    * @param array $acl multiple acls to be checked.
    * @access private 
    */
    function _chechArray( $acl )
    { 
        // initialize the return array.
        $checked = array(); 
        // Each acl is check in the $acl input array.
        foreach( $acl as $key => $val ) {
            if ( $val ) {
                // Each acl is checked and a corrosponding key is created for the result (true|false)
                $checked[$key] = in_array ( $val, $this->capabilityList );
            } else {
                // Assume Null give everyone access
                $checked[$key] = true;
            } 
        } 
        // Return the checked acls with key values corrosponding to the input array
        // and the value are set true|false based on the check.
        return $checked;
    } 
} 

?>