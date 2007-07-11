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
$this->loadClass('classparser', 'decisiontable');
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
class viewCondition extends classParser
{
    /**
     * Object reference to the condition object.
     *
     * @access private
     * @var object
     */
    var $_objCondition = NULL;

    /**
     * Property used for storing the list of callback method available.
     *
     * @access private
     * @var array
     */
    var $_methods = array();

    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        $this->_classPrefix = 'view';
    }

    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function connect( &$objCondition )
    {
        $this->_objCondition = &$objCondition;
    }

    /**
     * The show the control for this object
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array htmlelements
     */
    function elements()
    {
        $result = $this->callBack( $this->parser( $this->_objCondition->_params ) );
        return $result;
    }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array
     */
    function setValue($value='TRUE')
    {
        $objRadio = $this->newObject('radio','htmlelements');
        $objRadio->name = 'value';
        $objRadio->addOption('setValue | TRUE', 'TRUE');
        $objRadio->addOption('setValue | FALSE', 'FALSE');
        $selected = 'setValue | '.$value;
        $objRadio->setSelected( $selected );

        $lblName = "Set Value";
        return array('lblName'=>$lblName,'element'=>$objRadio->show());
    }
} /* end of class viewCondition */
?>