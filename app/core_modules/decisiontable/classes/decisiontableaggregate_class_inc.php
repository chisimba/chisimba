<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package decisiontable
* @subpackage access
* @version 0.1
* @since 03 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/
$this->loadClass( 'decisiontablebase', 'decisiontable' );
/**
 * Abstract class used to manage conditions, rules, actions, and decisiontables.
 *
 * @access public
 * @author Jonathan Abrahams
 */
class decisionTableAggregate extends decisionTableBase
{
    // --- ATTRIBUTES ---
    /**
     * @var object Object reference to the 'Whole' object.
     */
    var $_objParent = NULL;

    /**
     * @var object Reference to the 'Parts' object.
     */
    var $_objParts = NULL;

    /**
     * @var object Object reference to its child object.
     */
    var $_objChild = NULL;

    /**
     * Property used to store all aggregated objects
     *
     * @access private
     * @var array
     */
    var $_arrChildren = array();

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

        $this->_arrChildren = array();
    }

    /**
     * Abstract method to connect to other objects.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param object Reference name for the object
     * @return nothing
     */
    function connect( &$object )
    {
        $this->_objParent = &$object;
        $this->_objCreated->connect( $object );
    }

    /**
     * Method to create a new object.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Reference name for the object
     * @return action Returns this object.
     */
    function create( $name )
    {
        $this->_arrChildren = array();
        return parent::create( $name );
    }

    /**
     * Method to get the Id for the child of a parent.
     */
    function retrieveId( )
    {
        $this->_id = $this->_objParts->retrieveId( $this, $this->_objParent );
        return $this->_id;
    }

    /**
     * Method to allow duplicate rule names
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
     * @author Jonathan Abrahams
     * @return array Returns an array of condition objects for this rule
     * @version V0.1
     */
    function retrieve( )
    {
        foreach( $this->_objChild->retrieve( $this ) as $dbChild ) {
            // Insert the child object into this objects properties.
            $this->createChild($dbChild);
        }
        return $this;
    }

    /**
     * Method used to create child objects for the rule.
     * @param array List of children found in database.
     */
    function createChild($dbChild)
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
     * @access public
     * @author Jonathan Abrahams
     * @param object
     * @return uniqueID|false the unique id new row, or false if it exists already.
     */
    function add($objChild)
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
     * @author Jonathan Abrahams
     * @param string condId
     * @return true|false Return true if successfull, otherwise false.
     */
    function deleteChild( $objCond )
    {
        return $this->_objChild->deleteChild(
            $this->_id,
            $objCond->_id );
    }

    /**
     * Method to set the properties using given parameters.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param object
     * @return nothing
     * @version V0.1
     */
    function setProperties( $objChild )
    {
        // Insert a copy of the child object.
        $this->_arrChildren[$objChild->_name] = $objChild;
    }
    /**
     * Method to get all the condition IDs.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array
     */
    function getIDs()
    {
        return array_keys( $this->_arrChildren );
    }

    /**
     * Method to get the condition object for the id.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return object
     */
    function getID( $id )
    {
        return $this->_arrChildren[$id];
    }

    /**
     * Method to test if a condition is found in this rule.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return true|false
     */
    function hasID( $id )
    {
        return in_array( $id, $this->getIDs() );
    }
} /* end of class base */
?>