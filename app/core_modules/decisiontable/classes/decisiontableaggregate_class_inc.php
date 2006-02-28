<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Abstract class used to manage conditions, rules, actions, and decisiontables.
 * @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package decisiontable
 * @subpackage access
 * @version 0.1
 * @since 03 Febuary 2005
 * @author Paul Scott based on methods by Jonathan Abrahams
 * @filesource
 */
$this->loadClass( 'decisiontablebase', 'decisiontable' );

class decisionTableAggregate extends decisionTableBase
{
    /**
     * Object reference to the 'Whole' object.
     *
     * @access public
     * @var object
     */
    public $_objParent = NULL;

    /**
     * Reference to the 'Parts' object.
     *
     * @access public
     * @var object
     */
    public $_objParts = NULL;

    /**
     * Object reference to its child object.
     *
     * @access public
     * @var object
     */
    public $_objChild = NULL;

    /**
     * Property used to store all aggregated objects
     *
     * @access public
     * @var array
     */
    public $_arrChildren = array();

    /**
     * The object initialization method.
     *
     * @access public
     * @param string
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
     * @param object Reference name for the object
     * @return void
     */
    public function connect( &$object )
    {
        $this->_objParent = &$object;
        $this->_objCreated->connect( $object );
    }

    /**
     * Method to create a new object.
     *
     * @access public
     * @param string Reference name for the object
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
     * @param void
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
     * @param void
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
     * @param void
     * @return array Returns an array of condition objects for this rule
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
     * @param array List of children found in database.
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
     * @param object
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
     * @param string condId
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
     * @param object
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
     * @param void
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
     * @param string $id
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
     * @param string id
     * @return true|false
     */
    public function hasID( $id )
    {
        return in_array( $id, $this->getIDs() );
    }
}
?>