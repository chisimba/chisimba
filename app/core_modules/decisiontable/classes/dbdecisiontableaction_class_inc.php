<?php

/**
 * Action Class (DB)
 * 
 * Database abstraction for the Decision Table Action Class
 * 
 * PHP version 5
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
class dbDecisionTableAction extends dbTable {

    /**
     * Property used to store the unique id.
     *
     * @access public
     * @var    string id
     */
    public $_id = NULL;

    /**
     * The object initialisation method.
     *
     * @access public
     * @param  void  
     * @return void  
     */
    public function init()
    {
        parent::init('tbl_decisiontable_decisiontable_action');
    }

    /**
     * Method to add an action to the decisionTable.
     *
     * @access public        
     * @param  action         Action        object.
     * @param  decisionTable  DecisionTable object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    public function add( $action, $decisionTable )
    {
        // no Duplicates
        $checkDups  = $action->_id."' AND ";
        $checkDups .= " decisiontableId = '".$decisionTable->_id;
        if( $this->valueExists( 'actionId', $checkDups ) ) {
            return FALSE;
        }

        // Package it
        $arrDTaction = array();

        $arrDTaction['decisiontableId'] = $decisionTable->_id;
        $arrDTaction['actionId'] = $action->_id;


        // Insert it
        return $this->insert( $arrDTaction );
    }

    /**
     * Method to retrieve all rules for the decisionTable.
     *
     * @access public
     * @param  object The decisionTable object.
     * @return array  of all actions for this decisionTable
     */
     public function retrieve( $objDecisionTable )
     {
         // Get the action for this decisionTable
         $objAction = $this->newObject( 'action','decisiontable'  );

         $join = $this->join( 'INNER JOIN', $objAction->_tableName , array( 'actionid'=>'id' ) );
         $filter = " WHERE decisiontableid = '".$objDecisionTable->_id."'";
         // Get all actions for this decisionTable
         $tables = array( $objAction->_tableName.'.id',  $objAction->_tableName.'.name' );
         $statement = $join.$filter;

         $arr = $this->getAll($join.$filter, array( $objAction->_tableName.'id',  $objAction->_tableName.'name' ));
		 return $arr;
     }

     /**
      * Method to check for duplicate entries
      *
      * @access public
      * @param  string $action       
      * @param  string $decisionTable
      * @return bool  
      */
     public function checkDuplicate($action, $decisionTable)
     {
        return is_null( $this->retrieveId( $action,$decisionTable ) ) ? FALSE : TRUE;
     }

     /**
     * Method to retrieve an action for the decisionTable.
     *
     * @access public
     * @param  object The action object.
     * @param  object The decisionTable object.
     * @return id     of action for this decisionTable
     */
     public function retrieveId( &$objAction, &$objDecisionTable )
     {
         // Get the action for this decisionTable
         $join = $this->join( 'INNER JOIN', $objAction->_tableName, array( 'actionid'=>'id' ) );
         $filter = " WHERE decisiontableid = '".$objDecisionTable->_id."'";
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