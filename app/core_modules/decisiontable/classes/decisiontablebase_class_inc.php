<?php

/**
 * Decision Table Base class
 * 
 * Decision Table Base Class
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
 * @package   decisiontable
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
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
 * Decision Table Base class
 * 
 * Decision Table Base Class
 * 
 * @category  Chisimba
 * @package   decisiontable
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class decisionTableBase extends dbTable
{
    /**
     * Property used to store the unique row id.
     * @var string unique id
     */
    public $_id = '';

    /**
     * Property used for storing the objects reference name.
     *
     * @access private
     * @var    string 
     */
    var $_name = '';

    /**
     * Property used to enable automatic inserts and  retrieves of the object Id.
     *
     * @access private
     * @var    string 
     */
    var $enableAutoInsertRetrieveId = TRUE;

    /**
     * Property used to store all database data.
     *
     * @access private
     * @var    array  
     */
    var $_dbData = array();

    // --- OPERATIONS ---

    /**
     * The object initialization method.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init($tableName)
    {
        parent::init($tableName);
        //$this->upgradeTable(); -still uses old sql - wont work
        $this->_id = NULL;
        $this->_name = '';
        $this->_dbData = array();
    }

    /**
     * The object initialization method.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing
     */
    function upgradeTable()
    {
        $ret = $this->listDbTables();
        if(in_array($this->_tableName,$ret)) {
        	$arrTableExists = $ret;
        }
        elseif (empty ( $arrTableExists )) {
        	$sqldata = array();
            @include_once './core_modules/decisiontable/sql/'.$this->_tableName.'.sql';
            $this->query( $sqldata[0] );
        }
    	/*
    	$sqlTableExists = sprintf( 'SHOW TABLES LIKE "%s"', $this->_tableName);
        $arrTableExists = $this->getArray( $sqlTableExists );
        if( empty ( $arrTableExists ) ) {
            $sqldata = array();
            @include_once './modules/decisiontable/sql/'.$this->_tableName.'.sql';
            $this->query( $sqldata[0] );
        }
        */
    }
    /**
     * Abstract method to connect to other objects.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @param  object  Reference name for the object
     * @return nothing
     */
    function connect( $object )
    {
    }

    /**
     * Method to create a new object.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param  string Reference name for the object
     * @return action Returns this object.
     */
    function create( $name )
    {
        // Set the name and reset other properties.
        $this->_id = NULL;
        // JC $this->_name = strtolower($name);
		$this->_name = $name;
        $this->_dbData = array('name'=>$name);
        // Get the ID;
        if( $this->enableAutoInsertRetrieveId ) {
            $this->autoInsertRetrieveId();
        }
        return $this;
    }

    /**
     * Method to insert or retrieve the object.
     *
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing Updates the id property
     */
    function autoInsertRetrieveId()
    {   // Retrieve ID by insert new object or retrieve existing object
        if( $this->insert()==NULL ) {
            $this->retrieveId();
        }
    }

    /**
     * Abstract method to get the Id for this object.
     */
    function retrieveId( )
    {
        $row = $this->getRow('name',$this->_name);
        return $row['id'];
    }

    /**
     * Method to insert the object into the database
     * @return the uniqueId|NULL
     */
    function insert( )
    {
        @assert( $this->_name <> '' ); // Must check, otherwise inserts nulls
        if ( !$this->checkDuplicate() ) {
            $this->_id = parent::insert( $this->_dbData );
            return $this->_id;
        } else {
            return NULL;
        }
    }

    /**
     * Method to test if the value exists
     * @return true|false
     */
    function checkDuplicate( )
    {
        return $this->valueExists( 'name', $this->_name );
    }

    /**
     * Method to delete the object and all its children objects.
     *
     * @access public    
     * @author Jonathan Abrahams
     * @param  string     Delete object by name( optional )
     * @return true|false Return true if successfull, otherwise false.
     */
    function delete( $name = NULL )
    {
        // Delete by name
        $delObject = $name ? $this->create( $name ) : $this;
        return parent::delete( 'id', $delObject->_id );
    }

    /**
     * Method to update the name of the object.
     *
     * @access public    
     * @author Jonathan Abrahams
     * @param  string     objects new name
     * @return true|false Return true if successfull, otherwise false.
     */
    function updateName( $newName )
    {
        $this->_name = $newName;
        if( !$this->checkDuplicate() ) {
            return parent::update( 'id', $this->_id, array( 'name' => $newName ) );
        } else {
            return NULL;
        }
    }
} /* end of class base */
?>