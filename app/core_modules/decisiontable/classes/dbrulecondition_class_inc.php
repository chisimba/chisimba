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
* @since 21 February 2005
* @author Jonathan Abrahams
* @filesource
*/
/**
* Class used to access the rule condition bridge table.
*/
class dbRuleCondition extends dbTable {
    // --- ATTRIBUTES ---
    /**
     * Property used to store the unique id.
     */
    var $_id = NULL;

    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        parent::init('tbl_decisiontable_rule_condition');
    }
    
    /**
     * Method to add a condition to the rule.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param condition Condition object.
     * @param rule Rule object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    function add( $condition, $rule )
    {
        // no Duplicates
        $checkDups  = $condition->_id."' AND ";
        $checkDups .= " ruleId = '".$rule->_id;
        if( $this->valueExists( 'conditionId', $checkDups ) ) {
            return FALSE;
        }
        
        // Package it
        $arrRuleCond = array();
        $arrRuleCond['ruleId'] = $rule->_id;
        $arrRuleCond['conditionId'] = $condition->_id;
        // Insert it
        return $this->insert( $arrRuleCond );
    }

    /**
     * Method to delete all the rule conditions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string ruleId
     * @return true|false Return true if successfull, otherwise false.
     */
    function delete( $ruleId )
    {
        return parent::delete( 'ruleId', $ruleId );
    }

    /**
     * Method to delete the given rule conditions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string ruleId
     * @param string condId
     * @return true|false Return true if successfull, otherwise false.
     */
    function deleteChild( $ruleId, $condId )
    {
        return parent::delete( 'ruleId' , $ruleId."' AND conditionId = '$condId" );
    }

    /**
     * Method to retrieve all conditions for the rule.
     * @param object The rule object.
     * @return array of all Conditions for this rule
     */
     function retrieve( $objRule )
     {
         // Get all Conditions for this rule
         $join = $this->join( 'INNER JOIN', $objRule->_tableName, array( 'ruleId'=>'id' ) );
         $filter = " WHERE ruleId = '".$objRule->_id."'";
         $fields = array( $objRule->_tableName.'id',  $objRule->_tableName.'name' );
         return $this->getAll($join.$filter, $fields );
     }
}
?>
