<?php

/**
 * Action Class (DB)
 * 
 * Database abstraction for the Decision Table Action Class
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
 * Action Class (DB)
 * 
 * Database abstraction for the Decision Table Action Class
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
     * @param  rule           Rule   object.
     * @param  action         Action object.
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
     * @param  string     actionId
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
     * @param  string     actionId
     * @param  string     RuleId  
     * @return true|false Return true if successfull, otherwise false.
     */
    function deleteChild( $actionId, $ruleId )
    {
        return parent::delete( 'actionid' , $actionId."' AND ruleid = '$ruleId" );
    }

    /**
     * Method to retrieve all rules for the action.
     * @param  object The action object.
     * @return array  of all Rules for this action
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