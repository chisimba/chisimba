<?php

/**
 * Decision Table
 * 
 * Decision Table Class
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
// end security check


/**
 * Decision Table
 * 
 * Decision Table Class
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
 class decisionTable extends dbTable
 {
    /**
     * Property used to store the unique id.
     *
     * @access public
     * @var    string unique id
     */
    public $_id = '';

    /**
     * Property used for storing the decision tables reference name.
     *
     * @access private
     * @var    string 
     */
    public $_name = '';

    /**
     * Object reference to the dbDecisionTableAction object.
     *
     * @access private              
     * @var    dbDecisionTableAction
     */
    public $_objDBDecisionTableAction = NULL;

    /**
     * List of actions set for this module.
     *
     * @access public
     * @var    array  $arrActions
     */
    public $_arrActions;

    /**
     *Initialize the decision table structure.
     *
     * @access public
     * @param  void  
     * @return void  
     */
    public function init( )
    {
        parent::init('tbl_decisiontable_decisiontable');
        $this->_objDBDecisionTableAction = $this->newObject( 'dbdecisiontableaction','decisiontable' );
        $this->_objDBDecisionTableRule = $this->newObject( 'dbdecisiontablerule','decisiontable'  );

        $this->_arrActions = array();
    }

    /**
     * Method to test if the action existists.
     *
     * @access public    
     * @param  string     the action.
     * @return true|false True if action found, otherwise False.
     */
    public function hasAction( $action )
    {
        return isset( $this->_arrActions[$action] );
    }

    /**
    * Method to test if the action is valid.
    *
    * @access public    
    * @param  string     the action.
    * @param  string     the default to be used if action does not exist.
    * @return true|false True if action valid, otherwise False.
    */
    public function isValid( $action, $default = TRUE )
    {
        if( $this->hasAction( $action ) ) {
            return $this->_arrActions[ $action ]->isValid();
        } else {
            return $default;
        }
    }

    /**
    * Method to test if the view element should shown.
    *
    * @access public   
    * @param  object    the view element object.
    * @return HTML|NULL shows the view element if required otherwise returns null.
    */
    public function showElement( $objElement )
    {
       // JC return $this->isValid( strtolower( $objElement->name ) ) ? $objElement->show() : NULL;
	   return $this->isValid( $objElement->name ) ? $objElement->show() : NULL;
    }

    /**
     * Method to create a new decisionTable.
     *
     * @access public
     * @param  string Reference name for the decisionTable
     * @return action Returns this object.
     */
    public function create($name, $action = NULL )
    {
        // Store the decisionTable name.
        $this->_name = $name;

        // No duplicates, and retrieve if existing.
        if( $this->valueExists( 'name', $this->_name ) ) {
            $decisionTableRow = $this->getRow('name',$this->_name );
            $this->_id = $decisionTableRow['id'];
        } else {
            // Package decisionTable properties.
            $decisionTable = array();
            $decisionTable['name'] = $this->_name;

            // Insert into database.
            $this->_id = $this->insert( $decisionTable );
        }

        // Any actions?
        if( $action ) {
            $this->add($action);
        }
        return $this;
    }

    /**
     * Method to delete all dependents of this decisiontable.
     *
     * @access public
     * @param  void  
     * @return bool  
     */
    public function delete()
    {
        foreach( $this->_arrActions as $objAction ) {
            $objAction->delete();
        }

        $arrRules = $this->_objDBDecisionTableRule->retrieve($this);
        $objRule = $this->newObject('rule');
        $objRule->connect($this);
        foreach( $arrRules as $dbRule ) {
            $objRule->create($dbRule['name']);
            $objRule->retrieveId();
            $objRule->delete();
        }

        return parent::delete('id', $this->_id );
    }

    /**
     * Method to delete all dependents of this decisiontable.
     *
     * @access public
     * @param  void  
     * @return NULL  
     */
    public function retrieveId()
    {
        return NULL;
    }


    /**
     * Method to add an action to the decisionTable.
     * A copy of the action is stored.
     *
     * @access public
     * @param  action
     * @return void  
     */
    public function add($action)
    {
        if( is_array($action) ) {
            foreach( $action as $anAction ) {
                // Set this objects properties
                $this->setProperties( $anAction );
                // Add a action to this decisionTable.
                $this->_objDBDecisionTableAction->add( $anAction, $this );
            }
        } else {
            // Set this objects properties
            $this->setProperties( $action );
            // Add a action to this decisionTable.
            $this->_objDBDecisionTableAction->add( $action, $this );
        }
    }

    /**
     * Method to add an action to the decisionTable.
     * A copy of the action is stored.
     *
     * @access public
     * @param  action
     * @return void  
     */
    public function addRule($rule)
    {
        // Add a rule to this decisionTable.
        $this->_objDBDecisionTableRule->add( $rule, $this );
    }

    /**
     * Method to set the properties.
     *
     * @access public
     * @param  object A action objects stored for this decisionTable
     * @return void   sets the object properties.
     */
    public function setProperties( $objAction )
    {
        // Insert a copy of the action.
        $this->_arrActions[$objAction->_name] = $objAction;
    }

    /**
     * Method to retrieve the rules from the database
     *
     * @access public
     * @param  void  
     * @return array  Returns an array of rules for this decisionTable
     */
    public function retrieveRules( )
    {
        return $this->_objDBDecisionTableRule->retrieve($this);
    }

    /**
     * Method to retrieve the rules from the database,
     *
     * @access public  
     * @param  $objRule
     * @return array   
     */
    public function retrieveRuleId( $objRule )
    {
        // Retrieve the decision table rule
        $objRule->_id = $this->_objDBDecisionTableRule->retrieveId( $objRule, $this );
    }

    /**
     * Method to retrieve the actions from the database,
     * and initialize the newly created object.
     *
     * @access public
     * @param  string The name of the
     * @return array  Returns an array of action objects for this decisionTable
     */
    public function retrieve( $name = NULL )
    {
        // Get the action object
        // JC $objAction = $this->newObject('action');
        // JC $objAction->connect($this);
        // Create the decision table if given
        if( $name ) {
            $this->create( $name );
        }
        // Array of action objects
        $arrAction = array();

        // Fetch all action IDs for this decisionTable from db
        $arrActions = $this->_objDBDecisionTableAction->retrieve( $this );
        // Create new action objects.
        foreach( $arrActions as $decisionTableAction ) {
	        $objAction = $this->newObject('action','decisiontable' );
	        $objAction->connect($this);
            // Fetch the action.
            $actionRow = $objAction->getRow( 'id', $decisionTableAction['actionid'] );
           // Get the action
            if( $actionRow ) {
                //Create the object
                $newAction = $objAction->create( $actionRow['name'] );
                // Retrieve the rule properties.
                $newAction->retrieve();

               // Insert the Action into this decisionTable properties.
                $this->setProperties( $newAction );
            } else {
                return FALSE;
            }
        }
        return $this;
    }
 }
?>