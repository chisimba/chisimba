<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Class used for maintaining a list of conditions.
*
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package decisiontable
* @subpackage access
* @version 0.1
* @author Jonathan Abrahams
* @filesource
*/
$this->loadClass( 'decisiontablebase', 'decisiontable' );
/**
 * Class used for maintaining a list of condition types.
 *
 * @package decisiontable
 * @category access
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 *
 * @access public
 * @author Jonathan Abrahams
 * @version V0.1
 */
class conditionType extends decisionTableBase
{
    // --- ATTRIBUTES ---
    /**
     * Property used for storing the class or type of condition.
     *
     * @access private
     * @var string
     */
    var $_conditionTypeClassName = '';

    /**
     * Property used for storing the module name of condition.
     *
     * @access private
     * @var string
     */
    var $_conditionTypeModuleName = '';

    // --- OPERATIONS ---
    function init()
    {
        parent::init('tbl_decisiontable_conditiontype');

        $this->create( 'setValue', 'condition','decisiontable' ); $this->insert();
    }

    /**
     * Method to create a new condition type.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Reference name for the condition
     * @version V0.1
     * @see condition::test()
     * @see condition::evaluate()
     */
    function create($name, $className, $moduleName )
    {
        // Disable insert / Retrieve
        $tmp = $this->enableAutoInsertRetrieveId;
        $this->enableAutoInsertRetrieveId = FALSE;
        parent::create( $name );
        $this->enableAutoInsertRetrieveId = $tmp;

        // Store Condition Type.
        $this->_conditionTypeClassName = $className;
        $this->_conditionTypeModuleName = $moduleName;

        // Db fields and values to store
        $this->_dbData['className'] = $className ;
        $this->_dbData['moduleName'] = $moduleName ;

        // Ok ready to do insert or retrive.
        if( $this->enableAutoInsertRetrieveId ) {
            $this->autoInsertRetrieveId();
        }
        return $this;
    }

    /**
     * Method to retrieve the parameters from the database,
     * and initialize the newly created object.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return condition Returns this condition object, and retrieves the parameters from the database.
     * @version V0.1
     */
    function retrieve($name)
    {
        $retrieved = $this->getRow('name',$name);
        if( !empty( $retrieved ) ) {
            $this->create( $name,$retrieved['className'], $retrieved['moduleName'] );
            $this->_id = $retrieved['id'];
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Method to get the type information.
     * @param string The name of the function( condition Type )
     * @return array The array containing the class name and the module containing the function.
     */
    function getType( $function )
    {
        $this->retrieve($function);
        $arrType = array();
        $arrType['className'] = $this->_conditionTypeClassName;
        $arrType['moduleName'] = $this->_conditionTypeModuleName;
        return $arrType;
    }
    /**
     * Method to update the parameters in the database, and re-initialize the object.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return condition Returns this condition object, and retrieves the parameters from the database.
     * @version V0.1
     */
    function update( )
    {
        // Package condition properties.
        $conditionType = array();
        $conditionType['className'] = $this->_conditionTypeClassName;
        $conditionType['moduleName'] = $this->_conditionTypeModuleName;

        // Update the parameters
        parent::update( 'id', $this->_id, $conditionType );
        return $this;
    }

} /* end of class condition */
?>