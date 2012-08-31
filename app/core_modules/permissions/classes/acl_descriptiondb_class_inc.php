<?php 
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package permissions
* @subpackage access
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams 
* @filesource 
*/

/**
* The acl_descriptiondb class is used to access the acl description table data.
* This class is responsible for maintaining this data.
*/
class acl_descriptiondb extends dbTable 
{
    /**
    * Method to initialize the acl_descriptiondb object.
    */
    function init()
    {
        parent::init( 'tbl_permissions_acl_description' );
    } 

    /**
    * Method to create a new access control list.
    * 
    * @param string $name the access control list name.
    * @param string $description a short description, suggesting the member list.
    * @return string|false the newly generated unique id if successful, otherwise false.
    */
    function newAcl ( $name, $description )
    {
        $objUser = $this->getObject( 'user', 'security' );

        if ( $this->valueExists( 'name', $name ) ) {
            return FALSE;
        } else {
            $row = array();
            $row['name'] = $name;
            $row['description'] = $description;
            $row['last_updated'] = date( "Y:m:d H:i:s" );
            $row['last_updated_by'] = $objUser->userId();
            return parent::insert( $row );
        } 
    } 

    /**
    * Method to remove an access control list.
    * 
    * @param string $acl The unique ID.
    * @return true|false true if successful, otherwise false.
    */
    function deleteAcl ( $acl )
    {
        return parent::delete( 'id', $acl );
    } 

    /**
    * Method to get the description of the access control list.
    * 
    * @param string $acl The unique ID.
    * @return string the access control list description.
    */
    function getDescription( $acl )
    {
        $row = $this->getRow( 'id', $acl );
        return $row['description'];
    } 

    /**
    * Method to get the unique id for the access control list.
    * 
    * @param string $pkValue a value to be found in the pkField.
    * @param string $pkField the fieldname to search for the value( optional default is name ).
    * @return string the unique id
    */
    function getId( $pkValue, $pkField = 'name' )
    {
        $row = $this->getRow( $pkField, $pkValue );

        if ( empty ( $row ) ) {
            return FALSE;
        } else {
            return $row['id'];
        } 
    } 

    /**
    * Method to get all the access control list items.
    * 
    * @param array $fields Default fields are unique ID and name.
    * @return array|false an array of associate arrays(rows+fields), or FALSE on failure
    */
    function getAcls( $fields = null )
    {
        $aclDescriptionDb = $this->_tableName;
        $sql = "SELECT ";
        $sql .= $fields ? implode( ",", $fields ) : "id, description";
        $sql .= " FROM $aclDescriptionDb";
        return $this->getArray( $sql );
    } 
} 

?>