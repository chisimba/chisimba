<?php

/**
 * Rule Class (DB)
 * 
 * Database abstraction for the Decision Table Rule Class
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
 * Rule Class (DB)
 * 
 * Database abstraction for the Decision Table Rule Class
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
class dbDecisionTableRule extends dbTable {

    /**
     * Property used to store the unique id.
     *
     * @access public
     * @var    $_id  
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
        parent::init('tbl_decisiontable_decisiontable_rule');
    }

    /**
     * Method to upgrade tables
     *
     * @access public
     * @param  void  
     * @return void  
     */
    public function upgradeTable()
    {
        $ret = $this->listDbTables();
        if(in_array($this->_tableName,$ret)) {
        	$arrTableExists = $ret;
        }
        if( empty ( $arrTableExists ) ) {
            $sqldata = array();
            @include_once './core_modules/decisiontable/sql/'.$this->_tableName.'.sql';
            $this->query( $sqldata[0] );
        }

    }

    /**
     * Method to add a rule to the decisionTable.
     *
     * @access public        
     * @param  object         Rule          object.
     * @param  object         DecisionTable object.
     * @return uniqueId|false Return the unique Id or false if already exists.
     */
    public function add( $rule, $decisionTable )
    {
        // no Duplicates
        $checkDups  = $rule->_id."' AND ";
        $checkDups .= " decisiontableid = '".$decisionTable->_id;
        if( $this->valueExists( 'ruleid', $checkDups ) ) {
            return FALSE;
        }

        // Package it
        $arrDTaction = array();

		$arrDTaction['decisiontableId'] = $decisionTable->_id;
        $arrDTaction['ruleId'] = $rule->_id;
        // Insert it
        return $this->insert( $arrDTaction );
    }

    /**
     * Method to check for duplicates
     *
     * @access public
     * @param  string $rule         
     * @param  string $decisionTable
     * @return bool  
     */
    public function checkDuplicate($rule, $decisionTable )
     {
        return is_null( $this->retrieveId( $rule,$decisionTable ) ) ? FALSE : TRUE;
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

         $objRule = $this->newObject( 'rule' ,'decisiontable' );
         $join = $this->join( 'INNER JOIN', $objRule->_tableName , array( 'ruleId'=>'id' ) );
         $filter = " WHERE decisiontableId = '".$objDecisionTable->_id."'";
         // Get all Rules for this decisionTable

         $arr = $this->getAll($join.$filter, array( $objRule->_tableName.'id',  $objRule->_tableName.'name' ));
		 return $arr;
     }

    /**
     * Method to retrieve a rule for the decisionTable.
     *
     * @access public
     * @param  object The rule object.
     * @param  object The decisionTable object.
     * @return id     of rule for this decisionTable
     */
     public function retrieveId( &$objRule, &$objDecisionTable )
     {
         // Get the action for this decisionTable
         $join = $this->join( 'INNER JOIN', $objRule->_tableName, array( 'ruleid'=>'id' ) );
         $filter = " WHERE decisiontableid = '".$objDecisionTable->_id."'";
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