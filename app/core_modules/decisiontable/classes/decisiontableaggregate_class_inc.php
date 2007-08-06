<?php

/**
 * Decision Table Aggregate class
 * 
 * Decision Table aggregate Class
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

$this->loadClass( 'decisiontablebase', 'decisiontable' );

/**
 * Decision Table Aggregate class
 * 
 * Decision Table aggregate Class
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
class decisionTableAggregate extends decisionTableBase
{
    /**
     * Object reference to the 'Whole' object.
     *
     * @access public
     * @var    object
     */
    public $_objParent = NULL;

    /**
     * Reference to the 'Parts' object.
     *
     * @access public
     * @var    object
     */
    public $_objParts = NULL;

    /**
     * Object reference to its child object.
     *
     * @access public
     * @var    object
     */
    public $_objChild = NULL;

    /**
     * Property used to store all aggregated objects
     *
     * @access public
     * @var    array 
     */
    public $_arrChildren = array();

    /**
     * The object initialization method.
     *
     * @access public 
     * @param  string 
     * @return nothing
     */
    public function init($tableName)
    {
        parent::init($tableName);

        $this->_arrChildren = array();
    }

    /**
     * Abstract method to connect to other objects.
     *
     * @access public
     * @param  object Reference name for the object
     * @return void  
     */
    public function connect( $object )
    {
        $this->_objParent = $object;
        $this->_objCreated->connect( $object );
    }

    /**
     * Method to create a new object.
     *
     * @access public
     * @param  string Reference name for the object
     * @return action Returns this object.
     */
    public function create( $name )
    {
        $this->_arrChildren = array();
        return parent::create( $name );
    }

    /**
     * Method to get the Id for the child of a parent.
     *
     * @access public  
     * @param  void    
     * @return property
     */
    public function retrieveId( )
    {
        $this->_id = $this->_objParts->retrieveId( $this, $this->_objParent );
        return $this->_id;
    }

    /**
     * Method to allow duplicate rule names
     *
     * @access public  
     * @param  void    
     * @return property
     */
    function checkDuplicate( )
    {
        return $this->_objParts->checkDuplicate( $this, $this->_objParent );
    }

    /**
     * Method to retrieve the conditions from the database,
     * and initialize the newly created object.
     *
     * @access public
     * @param  void  
     * @return array  Returns an array of condition objects for this rule
     */
    public function retrieve( )
    {
        foreach( $this->_objChild->retrieve( $this ) as $dbChild ) {
            // Insert the child object into this objects properties.
            $this->createChild($dbChild);
        }
        return $this;
    }

    /**
     * Method used to create child objects for the rule.
     *
     * @access public
     * @param  array  List of children found in database.
     * @return void  
     */
    public function createChild($dbChild)
    {
        $objectRow = $this->_objCreated->getRow( 'id', $dbChild[$this->_dbFK] );

        //Create the object
        $newObject = $this->_objCreated->create( $objectRow['name'] );
        $newObject->retrieveId();

        // Retrieve the conditions properties.
        $newObject->retrieve();
        // Insert the condition into this rules properties.
        $rule = $newObject;

        $this->setProperties( $newObject );
    }

    /**
     * Method to add a child to the object.
     *
     * @access public        
     * @param  object        
     * @return uniqueID|false the unique id new row, or false if it exists already.
     */
    public function add($objChild)
    {
        // Set this objects properties
        $this->setProperties( $objChild );
        // Add a condition to this rule.
        $this->_objChild->add( $objChild, $this );
    }

    /**
     * Method to delete the rule conditions.
     *
     * @access public    
     * @param  string     condId
     * @return true|false Return true if successfull, otherwise false.
     */
    public function deleteChild( $objCond )
    {
        return $this->_objChild->deleteChild(
            $this->_id,
            $objCond->_id );
    }

    /**
     * Method to set the properties using given parameters.
     *
     * @access public
     * @param  object
     * @return void  
     */
    public function setProperties( $objChild )
    {
        // Insert a copy of the child object.
        $this->_arrChildren[$objChild->_name] = $objChild;
    }

    /**
     * Method to get all the condition IDs.
     *
     * @access public
     * @param  void  
     * @return array 
     */
    public function getIDs()
    {
        return array_keys( $this->_arrChildren );
    }

    /**
     * Method to get the condition object for the id.
     *
     * @access public
     * @param  string $id
     * @return object
     */
    public function getID( $id )
    {
        return $this->_arrChildren[$id];
    }

    /**
     * Method to test if a condition is found in this rule.
     *
     * @access public    
     * @param  string     id
     * @return true|false
     */
    public function hasID( $id )
    {
        return in_array( $id, $this->getIDs() );
    }
}
?>