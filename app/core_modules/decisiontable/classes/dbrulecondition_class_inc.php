<?php

/**
 * Rule Condition Class (DB)
 * 
 * Database abstraction for the Decision Table Rule Condition Class
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
 * @package   decisiontable
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
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
 * Rule Condition Class (DB)
 * 
 * Database abstraction for the Decision Table Rule Condition Class
 * 
 * @category  Chisimba
 * @package   decisiontable
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
     * @param  condition      Condition object.
     * @param  rule           Rule      object.
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
     * @param  string     ruleId
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
     * @param  string     ruleId
     * @param  string     condId
     * @return true|false Return true if successfull, otherwise false.
     */
    function deleteChild( $ruleId, $condId )
    {
        return parent::delete( 'ruleid' , $ruleId."' AND conditionid = '$condId" );
    }

    /**
     * Method to retrieve all conditions for the rule.
     * @param  object The rule object.
     * @return array  of all Conditions for this rule
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