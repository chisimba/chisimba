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
* Class used to access the decision table rule bridge table.
*/
class dbDecisionTableRule extends dbTable {
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
        parent::init('tbl_decisiontable_decisiontable_rule');
        //$this->upgradeTable();
    }
    
    /**
     * Method to upgrade tables
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function upgradeTable()
    {
        $sqlTableExists = sprintf( 'SHOW TABLES LIKE "%s"', $this->_tableName);
        $arrTableExists = $this->getArray( $sqlTableExists );
        if( empty ( $arrTableExists ) ) {
            $sqldata = array();
            @include_once './modules/decisiontable/sql/'.$this->_tableName.'.sql';
            $this->query( $sqldata[0] );
        }    
    }    
    /**
     * Method to add a rule to the decisionTable.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param object Rule object.
     * @param object DecisionTable object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    function add( $rule, $decisionTable )
    {
        // no Duplicates
        $checkDups  = $rule->_id."' AND ";
        $checkDups .= " decisiontableId = '".$decisionTable->_id;
        if( $this->valueExists( 'ruleId', $checkDups ) ) {
            return FALSE;
        }
        
        // Package it
        $arrDTaction = array();
        $arrDTaction['decisionTableId'] = $decisionTable->_id;
        $arrDTaction['ruleId'] = $rule->_id;
        // Insert it
        return $this->insert( $arrDTaction );
    }
    
     function checkDuplicate($rule, $decisionTable )
     {
        return is_null( $this->retrieveId( $rule,$decisionTable ) ) ? FALSE : TRUE;
     }

    /**
     * Method to retrieve all rules for the decisionTable.
     * @param object The decisionTable object.
     * @return array of all actions for this decisionTable
     */
     function retrieve( $objDecisionTable )
     {
     
         // Get the action for this decisionTable
         $objRule = $this->getObject( 'rule' );
         $join = $this->join( 'INNER JOIN', $objRule->_tableName , array( 'ruleId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         // Get all Rules for this decisionTable
         return $this->getAll($join.$filter, array( $objRule->_tableName.'id',  $objRule->_tableName.'name' ));
     }

    /**
     * Method to retrieve a rule for the decisionTable.
     * @param object The rule object.
     * @param object The decisionTable object.
     * @return id of rule for this decisionTable
     */
     function retrieveId( &$objRule, &$objDecisionTable )
     {
         // Get the action for this decisionTable
         $join = $this->join( 'INNER JOIN', $objRule->_tableName, array( 'ruleId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         $filter.= " AND ".$objRule->_tableName.".name = '".$objRule->_name."'";
         $arr = $this->getAll($join.$filter, array ( $objRule->_tableName.'id' ) );

         if( !empty($arr) ){
            return $arr[0]['id'];
         } else {
            return NULL;
         }
     }     
}
?>
