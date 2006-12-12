<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Class used to manage actions
 *
 * @access public
 * @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package decisiontable
 * @subpackage access
 * @version 0.1
 * @since 03 Febuary 2005
 * @author Paul Scott based on methods by Jonathan Abrahams
 * @filesource
 */

$this->loadClass( 'decisiontableaggregate', 'decisiontable' );

class action extends decisionTableAggregate
{
    /**
     * The object initialization method.
     *
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->_objParts= $this->newObject( 'dbdecisiontableaction','decisiontable'  );
        $this->_objChild = $this->newObject('dbactionrule','decisiontable');
        $this->_objCreated = $this->newObject( 'rule','decisiontable' );
        $this->_dbFK =  'ruleid';
        parent::init('tbl_decisiontable_action' );
    }

    /**
     * Method to evaluate the action and its rules.
     *
     * @access public
     * @param void
     * @return true|false
     */
    public function isValid()
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

     /**
     * Method to delete the object and all its children objects - modified to take into account no cascading deletes.
     * Explicitly calls deletes to the bridging tables reliant on the action table
     *
     * @access public
     * @author Serge Meunier
     * @param string Delete object by name( optional )
     * @return true|false Return true if successfull, otherwise false.
     */
    function delete( $name = NULL )
    {
        // Delete by name
        $delObject = $name ? $this->create( $name ) : $this;

        $this->_objChild->delete($delObject->_id, 'ruleId');
        $this->_objParts->delete('ruleId', $delObject->_id);

        return parent::delete( 'id', $delObject->_id );
    }
}
?>
