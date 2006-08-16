<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextpermissions
* @subpackage view
* @version 0.1
* @since 09 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/

/**
 * Class to view the decision table module as a grid, listing all in the form
 *
 * <PRE>
 *              RULE1   RULE2   RULE3
 * CONDITION1     X               X
 * CONDITION2            X
 * CONDITION3            X        X
 *              RULE1   RULE2   RULE3
 * ACTION1        X
 * ACTION2               X
 * ACTION3                        X
 * </PRE>
 *
 * A rule is valid when all its conditions are valid.
 * An action is valid when any one of its rules are valid.
 *
 * @package contextpermissions
 * @subpackage view
 * @version 0.1
 * @access private
 * @author Jonathan Abrahams
 */
class viewGrid extends object
{
    /**
    * List of condtions for this module.
    * @var array $arrRules
    */
    var $_arrConditions;

    /**
    * List of rules set for this module.
    * @var array $arrRules
    */
    var $_arrRules;

    /**
    * List of actions set for this module.
    * @var array $arrActions
    */
    var $_arrActions;
    
    /**
     * Object reference to the table object.
     *
     * @access public
     * @var htmltable
     */
    var $objGrid = null;

    /**
     * Properties for the table.
     *
     * @access private
     * @var array
     */
    var $_properties = array();

    var $actionCondition = 'show_condition';
    var $actionRule = 'edit_rule';
    var $actionAction = 'show_action';

    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        // Create the table object.
        $this->objGrid = $this->newObject('htmltable','htmlelements');
        $this->objGrid->border=1;

        $this->objCond = $this->getObject( 'condition', 'decisiontable' );
        $this->objRule = $this->getObject( 'rule', 'decisiontable');
        $this->objAction = $this->getObject( 'action', 'decisiontable');

        $this->_arrActions = array();
        $this->_arrRules = array();
        $this->_arrConditions = array();
        $this->objDecisionTable = null;

    }

    /**
     * Method to show the grid.
     * @access public
     * @author Jonathan Abrahams
     * @return show the grid.
     */
    function show()
    {
        extract( $this->_properties );

        // Add the Condition/Rule headers.
        $this->addHeader( $lblCondition, $lblRule, 'condition_'.$this->actionRule );
        
        // Condition Rows and intersection with Rules
        $this->buildConditionGrid( $this->actionCondition );
        
        // Blank for Row
        $this->objGrid->startRow();
        $colBlank = $colCount + 1;
        $this->objGrid->addCell('&nbsp', NULL,NULL,NULL,NULL,"colspan=$colBlank align=center");
        $this->objGrid->endRow();

        //Second Header for Action/Rule.
        $this->addHeader($lblAction, $lblRule, 'action_'.$this->actionRule );

        // Action Rows and intersection with Rules
        $this->buildActionGrid( $this->actionAction );

        return $this->objGrid->show();
    }

   /**
     * Method to build the condition grid.
     *
     * <PRE>
     * CONDITION      X               X
     * CONDITION              X
     * CONDITION              X       X
     * </PRE>
     *
     * @param string The action link for Condition.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing Inserts new rows into the table.
     */
    function buildConditionGrid( $actionCondition )
    {
        extract( $this->_properties );
        $arrRules = $this->_arrRules;
        $arrCondition = $this->_arrConditions;
        
        $oddeven = 'odd';
        foreach( $arrCondition as $idCondition => $objCondition ) {
            $oddeven = $oddeven=='odd' ? 'even' : 'odd';
            $this->objGrid->startRow();
            $this->addLeftCell( $objCondition, $actionCondition, $idCondition );

            // The intersection of Rows and Columns.
            $ordered =  array_keys( $arrRules );
            sort( $ordered );
            foreach( $ordered as $idRule ) {
                $objRule = $arrRules[$idRule];
                $this->addGridCell($objRule, $idCondition, $oddeven );
            }
            $this->objGrid->endRow();
        }
    }

   /**
     * Method to build the action grid.
     *
     * <PRE>
     * ACTION   X           X
     * ACTION       X
     * ACTION       X   X
     * </PRE>
     *
     * @param string The action link for Action.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing Inserts new rows into the table.
     */
    function buildActionGrid( $actionAction )
    {
        extract( $this->_properties );
        $arrRules = $this->_arrRules;
        $arrAction = $this->_arrActions;
        
        $oddeven = 'odd';
        foreach( $arrAction as $idAction => $objAction ) {
            $oddeven = $oddeven=='odd' ? 'even' : 'odd';
            // The rows
            $this->objGrid->startRow();
            $this->addLeftCell( $objAction, $actionAction, $idAction );

            // The intersection of Rows and Columns.
            $ordered = array_keys( $arrRules );
            sort( $ordered );
            foreach( $ordered as $idRule ) {
                $objRule = $arrRules[$idRule];
                $this->addGridCell( $objAction, $idRule, $oddeven );
            }
            $this->objGrid->endRow();
        }
    }
    
    /**
     * Method to add the action or condition left cell.
     *
     * <PRE>
     * ACTION/CONDITION
     * </PRE>
     *
     * @param object The object action or condition.
     * @param string The action for the link
     * @param string The id for the link.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing Inserts new rows into the table.
     */
    function addLeftCell( $objLeft, $action, $id, $class = 'heading' )
    {
        extract( $this->_properties );
        $this->objGrid->addCell(
            $this->lnkText( $objLeft, $action, $id ),
            $colWidth, NULL, NULL, $class );
    }

    /**
     * Method to add a grid cell.
     *
     * @param object The object action or rule used as the lookup.
     * @param string The id of the rule or condition to be found.
     * @param string The class for the row.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing Inserts new cell into the table.
     */
    function addGridCell( $objLookup, $id, $class )
    {
        extract( $this->_properties );
        $this->objGrid->addCell(
            $this->showX( $objLookup, $id ),
            $colWidth, NULL , 'center', $class );
    }

    /**
     * Method to show the X on the grid.
     *
     * @param object The object action or rule used as the lookup.
     * @param string The id of the rule or condition to be found.
     * @access public
     * @author Jonathan Abrahams
     * @return Show 'X' if TRUE , otherwise '-'
     */
    function showX( &$objLookup, $id )
    {
        $isFound = $objLookup->hasID($id);
        if( $isFound ) {
            $objFound = $objLookup->getID($id);
            $isValid = $objFound->isValid();
            $X = 'X';
            return $isValid ? "<strong>$X</strong>" : "<span class=error>$X</span>";
        } else {
            return '&nbsp;';
        }
    }

   /**
     * Method to add table header.
     *
     * <PRE>
     * LEFT TITLE     RULE1   RULE2   RULE3
     * </PRE>
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function addHeader( $titleLeft, $titleTop, $actionTop )
    {
        extract( $this->_properties );
        $arrTop = $this->_arrRules;
        
        // The header for the Rules
        $this->objGrid->startRow();
        $this->objGrid->addCell('');
        $this->objGrid->addCell($titleTop, $colWidth,NULL,NULL,'heading',"colspan=$colCount align=center");
        $this->objGrid->endRow();

        $this->objGrid->startRow();
        // Insert the Action or Condition title.
        $this->objGrid->addCell($titleLeft, $colWidth,NULL,NULL,'heading');
        // Insert the rule column headings.
        $ordered = array_keys( $arrTop );
        sort( $ordered );
        foreach( $ordered as $idTop ) {
            $objTop = $arrTop[$idTop];
            $this->objGrid->addCell( $this->lnkText($objTop,$actionTop,$idTop), $colWidth,NULL, NULL,'heading' );
        }
        $this->objGrid->endRow();
    }

    /**
     * Method to connect to the decision table object.
     *
     * @param object Reference to the decision table object.
     * @param array all required properties to view the object.
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function connect(&$object, $properties )
    {
        $decisionTable = $object->retrieve();
        
        $objRule = &$this->getObject( 'rule', 'decisiontable' ); $objRule->connect( $object );
        $objCond = &$this->getObject( 'condition', 'decisiontable' );

        // Only action for this module
        $this->_arrActions = $decisionTable->_arrActions;
		//var_dump($this->_arrActions);
        
        foreach( $decisionTable->_objDBDecisionTableRule->retrieve($decisionTable) as $rule ) {
            $aRule = $objRule->create( $rule['name'] );
            $this->_arrRules[$aRule->_name] = $aRule->retrieve();
        }

        foreach( $this->objCond->getAll() as $cond ) {
            $aCond = $objCond->create( $cond['name'] );
            $this->_arrConditions[$aCond->_name] = $aCond->retrieve();
        }
        
        // Inintialize column Conditions/Actions width
        $this->_properties['colWidth'] = '20%';
        $this->_properties['colCount'] = count( $this->_arrRules );
        $this->_properties = array_merge( $this->_properties, $properties );
    }

    /**
    * Method to create a link.
    * @param string The action, rule, or condition name.
    * @param string The action to perform.
    * @param string The reference id for the object.
    * @return string The HTML link element.
    */
    function lnkText($objLink, $action, $id )
    {
        $objLnk = $this->newObject('link','htmlelements');
        $objLnk->href = $this->uri(array('action'=>$action,'class'=>$action,'id'=>$id ));
        $objLnk->link = $objLink->_name;
        $valid = $objLink->isValid() ? 'Valid' : 'Invalid';
        if( isset( $objLink->_params ) ) {
            $objLnk->extra = sprintf( " title='%s - %s'", $valid, $objLink->_params );
        } else {
            $objLnk->extra = sprintf( " title='%s'", $valid );
        }
        return $objLnk->show();
    }
    
} /* end of class viewTable */
?>
