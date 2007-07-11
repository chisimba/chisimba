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
        $checkDups .= " ruleid = '".$rule->_id;
        if( $this->valueExists( 'conditionid', $checkDups ) ) {
            return FALSE;
        }

        // Package it
        $arrRuleCond = array();
        $arrRuleCond['ruleid'] = $rule->_id;
        $arrRuleCond['conditionid'] = $condition->_id;
        // Insert it
        return $this->insert( $arrRuleCond );
    }

    /**
     * Method to delete all the rule conditions.
     * Modified 12/12/2006 to enable delete on any field instead of just action.
     * Could be done better by just removing function and using parent default but
     * kept in not to break any code reliant on the function.
     *
     * @access public
     * @author Jonathan Abrahams
     * @author Serge Meunier
     * @param string ruleId
     * @return true|false Return true if successfull, otherwise false.
     */
    function delete( $value, $deleteKey = 'ruleid' )
    {
        return parent::delete( $deleteKey, $value );
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
        return parent::delete( 'ruleid' , $ruleId."' AND conditionid = '$condId" );
    }

    /**
     * Method to retrieve all conditions for the rule.
     * @param object The rule object.
     * @return array of all Conditions for this rule
     */
     function retrieve( $objRule )
     {
         // Get all Conditions for this rule
         $join = $this->join( 'INNER JOIN', $objRule->_tableName, array( 'ruleid'=>'id' ) );
         $filter = " WHERE ruleid = '".$objRule->_id."'";
         $fields = array( $objRule->_tableName.'id',  $objRule->_tableName.'name' );
         $arr = $this->getAll($join.$filter, $fields );
		 return $arr;
     }
}
?>