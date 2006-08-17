<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package decisionTable
* @subpackage view
* @version 0.1
* @since 04 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/

/**
 * Class used for viewing a list of conditions of type context.
 *
 * @package decisiontable
 * @category view
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 *
 * @access public
 * @author Jonathan Abrahams
 */
class classParser extends object
{
    /**
     * Property used for storing the delimiter used when evaluating the
     * function and its parameters.
     *
     * @access private
     * @var string
     */
    var $_delimiterFunc = ' | ';
    /**
     * Property used for storing the prefix used for the class being parsed.
     * Prefix: view for the class used in the view layer
     * Prefix: none for the class used to perform the logic.
     *
     * @access private
     * @var string
     */
    var $_classPrefix = '';

    /**
     * Property used for storing the delimiter used when extracting the
     * parameters of the function.
     *
     * @access private
     * @var string
     */
    var $_delimiterParam = ',';
   // --- OPERATIONS ---
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
        $function = $arr[0];
        // Parameters, now extract the parameters.
        $params = ( empty( $arr[1] ) ) ?
            // Param array is set null if empty.
            array() :
            // Param array is extracted otherwise.
            explode( $this->_delimiterParam, $arr[1] );
        // Return the extracted function and params.
        return array( 'function'=>$function, 'params'=>$params );
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
        // Callback methods available
        $objConditionType = &$this->newObject( 'conditiontype', 'decisiontable' );
        // Call the correct the method from its class and in its module
        $arrType = $objConditionType->getType( $function );
        if( !empty( $arrType ) ) {
            $condition = &$this->newObject( $this->_classPrefix.$arrType['className'], $arrType['moduleName'] );
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
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        // Class prefix
        $this->_classPrefix = '';
    }    
}
?>