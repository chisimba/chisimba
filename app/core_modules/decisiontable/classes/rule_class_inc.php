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
 * Class used to manage rules
 *
 * @access public
 * @author Jonathan Abrahams
 */
class rule extends decisionTableAggregate
{
    // --- OPERATIONS ---

    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        $this->_objParts = &$this->newObject( 'dbdecisiontablerule','decisiontable'  );
        $this->_objChild = &$this->newObject('dbrulecondition','decisiontable' );
        $this->_objCreated = &$this->newObject( 'condition','decisiontable'  );
        $this->_dbFK =  'conditionid';
        parent::init('tbl_decisiontable_rule' );
    }

    /**
     * Method to evaluate the rule and its conditions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return void
     */
    function isValid()
    {
        // Rule is assumed TRUE.
        $result = TRUE;
        // Test all conditions.
        foreach( $this->_arrChildren as $condition ) {
            // Rule FAILS when one condition is FALSE.
            $result &= $condition->isValid();
        }
        // Return the result of the evaluation.
        return $result;
    }


} /* end of class rule */
?>