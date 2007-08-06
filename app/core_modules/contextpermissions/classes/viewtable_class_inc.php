<?php

/**
 * Class to view the decision table module as a table, listing all in the form
 * 
 * <PRE>
 * actions -> rules -> conditions.
 * </PRE>
 * 
 * PHP version 3
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   contextpermissions
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Class to view the decision table module as a table, listing all in the form
 * 
 * <PRE>
 * actions -> rules -> conditions.
 * </PRE>
 * 
 * @category  Chisimba
 * @package   contextpermissions
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class viewTable extends object
{
    /**
     * Object reference to the decision table object.
     *
     * @access private      
     * @var    decisionTable
     */
    var $_objDecisionTable = null;

    /**
     * Properties for the table.
     *
     * @access private
     * @var    array  
     */
    var $_properties = array();

    /**
     * Method to show the table.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return show   the table
     */
    function show()
    {
        $objTable = $this->newObject('htmltable','htmlelements');
        extract( $this->_properties );

        // Build the Table showing the ACTIONS, RULES, and CONDITIONS
        $oddeven = 'odd';
        foreach ( $this->_objDecisionTable->_arrActions as $actionId=>$action ) {
            $isValidAction = $action->isValid();
            // The Header row
            $objTable->row_attributes = "class=heading";
            $objTable->startRow();
            $objTable->addCell($lblAction );
            $objTable->addCell($lblRule);
            $objTable->addCell($lblCondition);
            $objTable->endRow();

            // Each action
            $oddeven = $oddeven =='odd' ? 'even' : 'odd';
            $objTable->row_attributes = "class=$oddeven";
            $objTable->startRow();

            //Clickable Action link
            $objTable->addCell( $this->lnkText( $action->_name, 'show_action', $actionId),
                 NULL,NULL,NULL,$isValidAction?NULL:'error' );

            $objTable->addCell('');
            $objTable->addCell('');
            $objTable->endRow();

            // Each Rule for current action
            foreach( $action->_arrRules as $ruleId=>$rule ) {
                $isValidRule = $rule->isValid();
                $objTable->row_attributes = "class=$oddeven ";
                $objTable->startRow();
                $objTable->addCell('');

                //Clickable Rule link
                $objTable->addCell( $this->lnkText($rule->_name,'show_rule', $ruleId),
                    NULL, NULL,NULL,$isValidRule?NULL:'error' );

                // Each Condition for the current rule.
                $condList = array();
                foreach( $rule->_arrConditions as $condId=>$condition ) {
                    $isValidCond = $condition->isValid();
                    $condList[] = $this->lnkText($condition->_name,'show_condition',$condId);
                }
                $objTable->addCell( implode(', ', $condList ), NULL, NULL, NULL,$isValidCond?NULL:'error' );
                $objTable->endRow();
            }
        }
        return $objTable->show();
    }

    /**
     * Method to connect to the decision table object.
     *
     * @param  object  Reference to the decision table object.
     * @param  array   all       required properties to view the object.
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing
     */
    function connect($object, $properties )
    {
        $this->_objDecisionTable = $object;
        $this->_properties = $properties;
    }

    /**
    * Method to create a rule link.
    * @param  object Object type: action, rule, condition expected.
    * @param  string The    action to perform.
    * @param  string The    reference id for the object.
    * @return link   Object reference for HTML link element.
    */
    function lnkText($link, $action, $id )
    {
        $objLnk = $this->newObject('link','htmlelements');
        $objLnk->href = $this->uri(array('action'=>$action,'id'=>$id));
        $objLnk->link = $link;
        return $objLnk->show();
    }
    
} /* end of class viewTable */
?>