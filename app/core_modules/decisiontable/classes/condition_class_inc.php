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
 * Class used for maintaining a list of conditions of type boolean.
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
class condition extends decisionTableBase
{
    // --- ATTRIBUTES ---
    /**
     * Property used for storing the function used for the condition.
     *
     * @access private
     * @var string
     */
    var $_arrFunction = array();

    /**
     * Property used for stores the callback function name to be evaluated.
     *
     * @access private
     * @var string
     */
    var $_function = '';
    
    /**
     * Property used for storing the parameters that are to be evaluated.
     *
     * @access private
     * @var string
     */
    var $_params = '';

    /**
     * Property used for storing the delimiter used when evaluating the
     * function and its parameters.
     *
     * @access private
     * @var string
     */
    var $_delimiterFunc = ' | ';

    /**
     * Property used for storing the delimiter used when extracting the
     * parameters of the function.
     *
     * @access private
     * @var string
     */
    var $_delimiterParam = ',';
    
    // --- OPERATIONS ---
    function init()
    {
        // Store the class type.
        parent::init('tbl_decisiontable_condition');
        $this->objConditionType = &$this->newObject( 'conditiontype', 'decisiontable' );
    }

    /**
     * Method to create a new instance.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Reference name for the condition
     * @return condition Returns this condition object.
     * @version V0.1
     */
    function create($name, $params='setValue')
    {   
        // Disable insert / Retrieve
        $tmp = $this->enableAutoInsertRetrieveId;
        $this->enableAutoInsertRetrieveId = FALSE;
        parent::create( $name );
        $this->enableAutoInsertRetrieveId = $tmp;
            
        // Set dbData and unPack params
        $this->setProperties( $params );
        
        // Ok ready to do insert or retrive.
        if( $this->enableAutoInsertRetrieveId ) {
            $this->autoInsertRetrieveId();
        }
        return $this;
    }

    /**
     * Method to set the properties
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string 
     * @version V0.1
     */
    function setProperties($params)
    {
        // Store Condition parameters( callback function ).
        $this->_params = $params;
        // Unpack function and its parameters
        $this->_arrFunction = $this->parser( $params );
        // Db fields and values to store 
        $this->_dbData['params'] = $params;
    }
    /**
     * Method to retrieve the parameters from the database,
     *
     * @access public
     * @author Jonathan Abrahams
     * @return condition Returns this condition object, and retrieves the parameters from the database.
     * @version V0.1
     */
    function retrieveId()
    {
        $retrieved = $this->getRow('name',$this->_name);
        $this->_id = $retrieved['id'];
        return $this->_id;
    }
    
    /**
     * Method to retrieve the parameters from the database,
     *
     * @access public
     * @author Jonathan Abrahams
     * @return condition Returns this condition object, and retrieves the parameters from the database.
     * @version V0.1
     */
    function retrieve()
    {
        $retrieved = $this->getRow('id',$this->_id);
        // Set dbData and unPack params
        $this->setProperties( $retrieved['params'] );
        
        return $this;
    }
    
    /**
     * Method to update the parameters in the database, and re-initialize the object.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return condition Returns this condition object, and retrieves the parameters from the database.
     * @version V0.1
     */
    function update( $params )
    {
        // Set dbData and unPack params
        $this->setProperties( $params );

        parent::update( 'id', $this->_id, $this->_dbData );
        return $this;
    }

    /**
     * Method to evaluate the value parameter.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Function and its Parameter list found in this class.
     * @return true|false
     */
    function evaluate($value)
    {
        // Call a class accessor method to return the property.
        $result = $this->callBack($this->parser($value));
        return $result ? TRUE : FALSE;
    }

    /**
     * Method to extract function name and parameters from the string.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Function and its Parameter list found in this class.
     * @return array Result of parsing the Function call string.
     */
    function parser($strCallBack)
    {
        // Syntax: function name | parameters.
        $arr = explode( $this->_delimiterFunc, $strCallBack );
        // Function name.
        $this->_function = $arr[0];
        // Parameters, now extract the parameters.
        $params = ( empty( $arr[1] ) ) ?
            // Param array is set null if empty.
            array() :
            // Param array is extracted otherwise.
            explode( $this->_delimiterParam, $arr[1] );
        // Return the extracted function and params.
        return array( 'function'=>$this->_function, 'params'=>$params );
    }

    /**
     * Method to make safe callbacks.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param array Function name, Parameter list for the function.
     * @return mixed|false returns the evaluated function.
     */
    function callBack($callFunction)
    {
        $function = $callFunction['function'];
        $params = $callFunction['params'];

        // Call the correct the method from its class and in its module
        $arrType = $this->objConditionType->getType( $function );
        if( !empty( $arrType ) ) {
            $condition = $this->newObject( $arrType['classname'], $arrType['modulename'] );
        } else {
            // Return FALSE if the conditionType not found
            return FALSE;
        }
        
        // Execute callback
        if( method_exists( $condition, $function ) ) {
            // Call a function found in this class.
            $callBack = array( $condition, $function );
            // Function should return true or false;
            $result = call_user_func_array( $callBack, $params );
        } else {
            // Return FALSE if function does not exist.
            $result = FALSE;
       }
        return $result;
    }

    /**
     * Method used to evaluate the value and returns either TRUE or FALSE.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return true|false
     * @version V0.1
     */
    function isValid()
    {
        return  $this->evaluate($this->_params) ? TRUE : FALSE;
    }
  
    /**
     * Method used to set the value.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param true|false new value.
     * @return true|false
     * @version V0.1
     */
    function setValue($value='TRUE')
    {
       // String to TRUE|FALSE
       if( $value ) {
           $value = $value=='TRUE' ? TRUE : FALSE;
       }
       return $value;
    }
} /* end of class condition */
?>
