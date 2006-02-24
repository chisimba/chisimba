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
$this->loadClass( 'decisiontableaggregate', 'decisiontable' );
/**
 * Class used to manage actions
 *
 * @access public
 * @author Jonathan Abrahams
 */
class action extends decisionTableAggregate
{
    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * The object initialization method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        $this->_objParts= $this->getObject( 'dbdecisiontableaction' );
        $this->_objChild = $this->getObject('dbactionrule','decisiontable');
        $this->_objCreated = $this->getObject( 'rule','decisiontable' );
        $this->_dbFK =  'ruleid';
        parent::init('tbl_decisiontable_action' );
    }

    /**
     * Method to evaluate the action and its rules.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return true|false
     */
    function isValid()
    {
        // Action is assumed FALSE.
        $result = FALSE;
        // Test all rules.
        foreach( $this->_arrChildren as $rule ) {

            // Action is ACCEPTED when any rule is TRUE.
            $result |= $rule->isValid();
        }
        // Return the result of the evaluation.

        return $result;
    }

} /* end of class action */
?>
