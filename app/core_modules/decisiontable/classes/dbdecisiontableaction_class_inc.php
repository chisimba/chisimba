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
* Class used to access the decision table action bridge table.
*/
class dbDecisionTableAction extends dbTable {
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
        parent::init('tbl_decisiontable_decisiontable_action');
    }

    /**
     * Method to add an action to the decisionTable.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param action Action object.
     * @param decisionTable DecisionTable object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    function add( $action, $decisionTable )
    {
        // no Duplicates
        $checkDups  = $action->_id."' AND ";
        $checkDups .= " decisiontableId = '".$decisionTable->_id;
        if( $this->valueExists( 'actionId', $checkDups ) ) {
            return FALSE;
        }

        // Package it
        $arrDTaction = array();
        $arrDTaction['decisionTableId'] = $decisionTable->_id;
        $arrDTaction['actionId'] = $action->_id;
        // Insert it
        return $this->insert( $arrDTaction );
    }

    /**
     * Method to retrieve all rules for the decisionTable.
     * @param object The decisionTable object.
     * @return array of all actions for this decisionTable
     */
     function retrieve( $objDecisionTable )
     {
         // Get the action for this decisionTable
         $objAction = $this->getObject( 'action' );
         $join = $this->join( 'INNER JOIN', $objAction->_tableName , array( 'actionId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         // Get all actions for this decisionTable
         return $this->getAll($join.$filter, array( $objAction->_tableName.'id',  $objAction->_tableName.'name' ));
     }

     function checkDuplicate($action, $decisionTable)
     {
        return is_null( $this->retrieveId( $action,$decisionTable ) ) ? FALSE : TRUE;
     }
    /**
     * Method to retrieve an action for the decisionTable.
     * @param object The action object.
     * @param object The decisionTable object.
     * @return id of action for this decisionTable
     */
     function retrieveId( &$objAction, &$objDecisionTable )
     {

         // Get the action for this decisionTable
         $join = $this->join( 'INNER JOIN', $objAction->_tableName, array( 'actionId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         $filter.= " AND ".$objAction->_tableName.".name = '".$objAction->_name."'";
         $arr = $this->getAll($join.$filter, array ( $objAction->_tableName.'id' ) );
         if( !empty($arr) ){
            return $arr[0]['id'];
         } else {
            return NULL;
         }
     }
}
?>