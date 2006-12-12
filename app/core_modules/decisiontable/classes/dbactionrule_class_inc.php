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
* Class used to access the action rule bridge table.
*/
class dbActionRule extends dbTable {
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
        parent::init('tbl_decisiontable_action_rule');
    }

    /**
     * Method to add a rule to the action.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param rule Rule object.
     * @param action Action object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    function add( $rule, $action )
    {
        // no Duplicates
        $checkDups  = $rule->_id."' AND ";
        $checkDups .= " actionid = '".$action->_id;
        if( $this->valueExists( 'ruleid', $checkDups ) ) {
            return FALSE;
        }

        // Package it
        $arrActRule = array();
        $arrActRule['actionid'] = $action->_id;
        $arrActRule['ruleid'] = $rule->_id;
        // Insert it
        return $this->insert( $arrActRule );
    }

    /**
     * Method to delete all the action rules
     * Modified 12/12/2006 to enable delete on any field instead of just action.
     * Could be done better by just removing function and using parent default but
     * kept in not to break any code reliant on the function.
     *
     * @access public
     * @author Jonathan Abrahams
     * @author Serge Meunier
     * @param string actionId
     * @return true|false Return true if successfull, otherwise false.
     */
    function delete( $value, $deleteKey = 'actionid' )
    {
        return parent::delete( $deleteKey, $value );
    }

    /**
     * Method to delete the given action rule.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string actionId
     * @param string RuleId
     * @return true|false Return true if successfull, otherwise false.
     */
    function deleteChild( $actionId, $ruleId )
    {
        return parent::delete( 'actionid' , $actionId."' AND ruleid = '$ruleId" );
    }

    /**
     * Method to retrieve all rules for the action.
     * @param object The action object.
     * @return array of all Rules for this action
     */
     function retrieve( $objAction )
     {
         // Get all Conditions for this rule
         $join = $this->join( 'INNER JOIN', $objAction->_tableName, array( 'actionid'=>'id' ) );
         $filter = " WHERE actionid = '".$objAction->_id."'";
         $fields = array( $objAction->_tableName.'id',  $objAction->_tableName.'name' );
         // Get all Rules for this action
         $arr = $this->getAll($join.$filter, $fields );
		 return $arr;
     }
}
?>
