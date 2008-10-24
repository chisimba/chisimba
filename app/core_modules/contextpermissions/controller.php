<?php

/**
 * context permissions controller
 * 
 * Class used for maintaining a list of conditions of type context.
 * 
 * PHP versions 4 and 5
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if ( !
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check


/**
 * context permissions controller
 * 
 * Class used for maintaining a list of conditions of type context.
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
class contextpermissions extends controller {

    /**
     * Description for var
     * @var    object
     * @access public
     */
    var $objDecisionTable;

    /**
     * Method to initialise the controller
     */
    function init()
    {
        $this->objDecisionTable = $this->getObject( 'decisiontable', 'decisiontable' );
        $this->objLanguage = $this->getObject( 'language', 'language' );
        
        $this->objAction = $this->getObject( 'action', 'decisiontable' );        
        $this->objAction->connect( $this->objDecisionTable );
        $this->objRule = $this->getObject( 'rule', 'decisiontable' );
        $this->objRule->connect( $this->objDecisionTable );        
        $this->objCond = $this->getObject( 'condition', 'decisiontable' );
        
    }
    /**
     * Method the engine uses to kickstart the module
     */
    function dispatch( $action )
    {
		$this->setVar('pageSuppressXML',true);
        
        // Test for first-time entry
        if( !$this->getSession( 'module_name' , FALSE ) ) {
            $this->setSession( 'module_name', 'contextpermissions' );
        }
        // Update session with dropdown
        if( $this->getParam( 'module_name', FALSE ) ) {
            $this->setSession( 'module_name', $this->getParam( 'module_name' ) );
        }
        // Create the decision table
        $this->objDecisionTable->create( $this->getSession( 'module_name' ) );
        $this->objDecisionTable->retrieve();
        switch ( $action ) {
            case 'create_action':
                $this->objAction->create('new_action', $this->objDecisionTable );
                if( !is_null($this->objAction->insert() ) ) {
                    $this->objDecisionTable->add( $this->objAction );
                }
                return $this->nextAction( 'show_main', array('class'=>'action', 'id'=>'new_action' ) );

            case 'create_rule':
                $this->objRule->create('new_rule');
                if( !is_null($this->objRule->insert() ) ) {
                    $this->objDecisionTable->addRule( $this->objRule );
                }
                return $this->nextAction( 'show_main', array('class'=>'condition_rule', 'id'=>'new_rule' ) );

            case 'create_condition':
                $this->objCond->create('new_cond');
                $this->objCond->insert();
                return $this->nextAction( 'show_main', array('class'=>'condition', 'id'=>'new_cond' ) );break;

            case 'condition_form':
                return $this->processConditionForm();

            case 'show_condition':
                return $this->showConditionForm();
            
            case 'generate_config':
                return $this->processGenerateConfig();
            case 'show_generate_config' :
                return $this->showGenerateConfig();
            
            case 'update_perms':
                return $this->processUpdatePerms();
            
            case 'edit_main':
                return $this->processEditMainForm();
            
            case 'controller_actions' : 
                $getActions = $this->getControllerActions();
                foreach( $getActions as $anAction ) {
                    $this->objAction->create($anAction, $this->objDecisionTable );
                    if( !is_null($this->objAction->insert() ) ) {
                        $this->objDecisionTable->add( $this->objAction );
                    }
                }
                return $this->nextAction( 'show_main', array() );
            case 'show_main' :
                
            default:
               return $this->showMain();
        }

    }
    /**
    * Overload the method to test if the action is valid.
    * @param  string     the action.
    * @param  string     the default to be used if action does not exist.
    * @return true|false True if action valid, otherwise False.
    */    
    function isValid( $action, $default = TRUE )
    {
        // Super user has access to all actions
        $objUser = $this->getObject('user', 'security');
        if( $objUser->isAdmin() )
            return TRUE;
            
        return parent::isValid( $action, $default );
    }
    
    /**
    * Method to get the current selected modules switch actions.
    * @return array List of actions found in the switch statement of the dispatch function.
    */
    function getControllerActions()
    {
        $moduleName = $this->getSession( 'module_name' );
        $farray=file("modules/$moduleName/controller.php");
        $inDispatch = false;
        $exit = false;
        $arrActions = array();
        foreach( $farray as $line ) {
            // Find the dispatch function
            if( preg_match( '/function/', $line ) )  {
                if( !$inDispatch ) {
                    $inDispatch = preg_match( '/function dispatch/', $line ) ? true : $inDispatch;
                } else {
                    $inDispatch = false;
                }
            }
            // Found dispatch method now look for cases
            if( $inDispatch && preg_match( "/case/", $line ) ) {
                $result = NULL;
                preg_match_all( "/case ('|\")(.*)('|\")/", $line , $result );
                if( isset( $result[2][0] ) ) 
                    $arrActions[] = $result[2][0];
                $exit = true;
            }
            
            // Found actions no need to continue
            if( !$inDispatch && $exit )
                break;
        }
        return $arrActions;

    }
    
    /**
    * Method to process edit main form.
    */
    function processEditMainForm()
    {

        $objName = $this->getParam( 'objName' );

        if( $this->getParam('button')=='delete' ) {

            switch( $this->getParam( 'class' ) ) {
                case 'condition':
                    $this->objCond->create( $this->getParam( 'id' ) );
                    $this->objCond->retrieveId();                    
                    $this->objCond->retrieve();
                    $this->objCond->delete( );

                    break;
                case 'condition_rule':
                case 'action_rule':
                    $this->objRule->create( $this->getParam( 'id' ) );
                    $this->objRule->retrieveId();
                    $this->objRule->delete( );
                    break;
                case 'action':
                    $this->objAction->create( $this->getParam( 'id' ) );
                    $this->objAction->retrieveId();
                    $this->objAction->delete( );
                    break;

            }
            return $this->nextAction( 'edit_main', array() );
        }

        if( $this->getParam('button')=='edit' && $this->getParam('class')=='condition' ) {
            // Get Current Condition
            $currCondName = $this->getParam( 'id' );
            $currCond = $this->objCond->create( $currCondName );
            $currCond->retrieveId();
            $currCond->retrieve();            
            // Update Name if required and reload
            if( $objName <> $currCondName ) {
                $currCond->updateName( $objName );
                $currCondName = $objName;
            }
            return $this->nextAction( 'show_condition', array( 'id'=>$currCondName ) );
        }
        // What class of object is being edited?
        $list = $this->getParam( 'List' );
        $this->setSession( 'list', $list );
        switch ( $this->getParam( 'class' ) ) {
            case 'condition':
                // Get Current Condition
                $currCondName = $this->getParam( 'id' );
                $currCond = $this->objCond->create( $currCondName );
                $currCond->retrieveId();
                $currCond->retrieve();
                // Update Name if required and reload
                if( $objName <> $currCondName ) {
                    $currCond->updateName( $objName );
                    $currCondName = $objName;
                }
                
                // Get All Rules
                $arrAllRules = $this->objDecisionTable->retrieveRules();
                // Get new Condition rules.
                $newCondRule = isset( $list['rule'] ) ? $list['rule'] : array();
                // For all rules check condition
                foreach( $arrAllRules as $aRule ) {
                    // Get each rule
                    $eachRuleName = $aRule['name'];
                    $eachRule = $this->objRule->create( $eachRuleName );
                    $eachRule->retrieveId();
                    $eachRule->retrieve();
                    // Check current condition rule set
                    $currSet = isset( $eachRule->_arrChildren[$currCondName] );
                    // Check new condition rule set
                    $newSet = isset( $newCondRule[$eachRuleName] );
                    // Remove or Insert condition
                    if( $currSet && !$newSet ) {
                        $eachRule->deleteChild( $currCond );
                    } else if( !$currSet && $newSet ) {
                        $eachRule->add( $currCond );
                    }
                }
                break;
            case 'action':
                // Get current action
                $currActionName = $this->getParam( 'id' );
                $currAction = $this->objAction->create( $currActionName );
                $currAction->retrieveId();
                // Update Name if required and reload
                if( $objName <> $currActionName ) {
                    $currAction->updateName( $objName );
                    $currActionName = $objName;
                }
                $currAction->retrieve() ;
                // Get All Rules
                $arrAllRules = $this->objDecisionTable->retrieveRules();
                // Get new Action rules
                $newActionRule = isset( $list['action'] ) ? $list['action'][$this->getParam( 'id' )] : array();
                // For all actions check rule
                foreach( $arrAllRules as $aRule ) {
                    // Get each rule
                    $eachRuleName = $aRule['name'];
                    $eachRule = $this->objRule->create( $eachRuleName );
                    $eachRule->retrieveId();
                    $eachRule->retrieve();
                    // Check current action rule set
                    $currSet = isset( $currAction->_arrChildren[$eachRuleName] );
                    $newSet = isset( $newActionRule[$eachRuleName] );
                    if( $currSet && !$newSet ) {
                        $currAction->deleteChild( $eachRule );
                    } else if( !$currSet && $newSet ) {
                        $currAction->add( $eachRule );
                    }
                }
                break;
            case 'condition_rule':
                // Get Current Rule.
                $currRuleName = $this->getParam( 'id' );
                $currRule = $this->objRule->create( $currRuleName );
                $currRule->retrieveId();
                $currRule->retrieve();

                // Update Name if required and reload
                if( $objName <> $currRuleName ) {
                    $currRule->updateName( $objName );
                }
                
                // Get All Conditions
                $arrAllConditions = $this->objCond->getAll();
                // Get new Rule conditions
                $newRuleCond = isset( $list['rule'] ) ? $list['rule'][$this->getParam( 'id' )] : array();
                // Process new Rules conditions
                // for all conditions check rule
                foreach( $arrAllConditions as $aCondition ) {
                    // Get condition name
                    $this->objCond->create( $aCondition['name']  );
                    $this->objCond->retrieveId();
                    $this->objCond->retrieve();
                    // Check current rule is condition set
                    $currSet = isset( $currRule->_arrChildren[$this->objCond->_name] );
                    // Check new rule condition set
                    $newSet  = isset( $newRuleCond[$this->objCond->_name] );

                    // Remove or Insert condition
                    if( $currSet&&!$newSet ) {
                        $currRule->deleteChild($this->objCond);
                    }else if( !$currSet&&$newSet ) {
                        $currRule->add( $this->objCond );
                    }
                }
                break;
            case 'action_rule':
                // Get Current Rule.
                $currRuleName = $this->getParam( 'id' );
                $currRule = $this->objRule->create( $currRuleName );
                $currRule->retrieveId();
                $currRule->retrieve();
                
                // Update Name if required and reload
                if( $objName <> $currRuleName ) {
                    $currRule->updateName( $objName );
                    $currRuleName = $objName;
                }
                
                // Get All Actions for this decision table.
                $objAllActions = $this->objDecisionTable->_arrActions;
                // Get new Rule actions
                $newRuleAct = isset( $list['action'] ) ? $list['action'] : array();

                // Process new Rules conditions
                // For all actions check rule
                foreach( $objAllActions as $objAction ) {
                    // Create the action
                    $actName = $objAction->_name;
                    
                    // Check current rule set in action
                    $currSet = isset( $objAction->_arrChildren[$currRuleName] );
                    // Check new rule action set
                    $newSet = isset( $newRuleAct[$actName] );

                    // Remove or Insert rule
                    if( $currSet&&!$newSet ) {
                        
                        $objAction->deleteChild($currRule);
                    }else if( !$currSet&&$newSet ) {
                        
                        $objAction->add( $currRule );
                    }
                }
                break;
        }

        return $this->nextAction( 'show_main', array() );
    }

    /**
    * Method to show the main template
    */
    function showMain()
    {
        $this->setVar('title', 
            ucfirst($this->objLanguage->code2Txt( 'mod_contextpermissions_ttlContextPermissions','contextpermissions') ));

        $this->setVar('lblCreateAction', 
            $this->objLanguage->languageText( 'mod_contextpermissions_lblCreateAction','contextpermissions','[Create new actions]'));
        $this->setVar('lblCreateRule',
            $this->objLanguage->languageText('mod_contextpermissions_lblCreateRule','contextpermissions','[Create new rules]'));
        $this->setVar('lblCreateCondition', 
            $this->objLanguage->languageText('mod_contextpermissions_lblCreateCondition','contextpermissions','[Create new conditions]'));

        $this->setVar('lblAction', 
            $this->objLanguage->languageText('mod_contextpermissions_lblAction','contextpermissions', '[Actions]') );
        $this->setVar('lblRule', 
            $this->objLanguage->languageText('mod_contextpermissions_lblRules','contextpermissions', '[Rules]') );
        $this->setVar('lblCondition', 
            $this->objLanguage->languageText('mod_contextpermissions_lblCondition','contextpermissions', '[Conditions]'));
        $this->setVar('lblGenerateConfig', 
            $this->objLanguage->languageText('mod_contextpermissions_lblGenerateConfig','contextpermissions', '[Generate Config]'));
        $this->setVar('lblUpdatePerms', 
            $this->objLanguage->languageText('mod_contextpermissions_lblUpdatePerms','contextpermissions', '[Update Permissions]'));
        $this->setVar('lblControllerActions',
            $this->objLanguage->languageText('mod_contextpermissions_lblControllerActions','contextpermissions', '[Get Actions]'));
        
        $this->setVar('decisionTable', $this->objDecisionTable );
        return 'main_tpl.php';
    }

    /**
    * Method to process the condition form template
    */
    function processConditionForm()
    {

        $condId = $this->getParam('id');
        $params = $this->getParam('value');
        $type = $this->getParam( 'type' );

        $this->objCond->create( $condId );
        $this->objCond->retrieveId();
        $condition  = $this->objCond->retrieve();

        // Update Type if required and reload
        if( $type <> $condition->_function ) {
            $condition->update( $type );
            return $this->nextAction('show_condition',array('id'=>$condId));
        }

        if( $this->getParam( 'button' ) == 'save' ) {
            if( $params<>'' ) {
                $condition->update($params);
                $this->setSession('msg',
                    $this->objLanguage->languageText('mod_contextpermissions_saved','contextpermissions','[Saved]') );
                return $this->nextAction('show_condition',array('id'=>$condId));
            } else {
                $this->setSession('msg',
                    $this->objLanguage->languageText('mod_contextpermissions_NotSaved','contextpermissions','[Not saved!]'));
                return $this->nextAction('show_condition',array('id'=>$condId));
            }
        } else if ( $this->getParam( 'button' ) == 'cancel' ) {
            return $this->nextAction('show_main', array() );
        }

    }
    /**
    * Method to show the main template
    */
    function showConditionForm()
    {
        $condId = $this->getParam('id');
        $this->setVar( 'id', $condId );

        $this->objCond->create( $condId );
        $this->objCond->retrieveId();
        $condition  = $this->objCond->retrieve();
        $this->setVar('title', 
            $this->objLanguage->languageText('mod_contextpermissions_ttlCondition','contextpermissions', '[Condition]').' : '.$condition->_name);
        $this->setVar('condition', $condition);
        $this->setVar('msg', $this->getSession('msg') );
        $this->unsetSession('msg');
        return 'condition_tpl.php';
    }
    /**
    * Method to process the generate config request.
    */
    function processGenerateConfig()
    {
        return $this->nextAction('show_generate_config', array() );
    }
    
    /**
    * Method to show the config
    */
    function showGenerateConfig()
    {
        $this->setVar('decisionTable', $this->objDecisionTable );
        $this->setVar( 'title', 
            $this->objLanguage->languageText( 'mod_contextpermissions_ttlRegConf','contextpermissions','[Registration configuration]') );
  
        return 'generate_config_tpl.php';
    }

    /**
    * Method to process the generate config request.
    */
    function processUpdatePerms()
    {
        $objRegister = $this->getObject('register','toolbar');
        $moduleName = $this->getSession( 'module_name' );
        $objRegister->setDefaultPermissions($moduleName);
        return $this->nextAction('show_main', array() );
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

    /**
    * Method to create a Create Rule icon link.
    * @param  string The action to perform for the create icon link.
    * @return string The result of geticon, it returns the icon as HTML.
    */
    function lnkIcnCreate($action)
    {
        $icn = $this->newObject( 'geticon', 'htmlelements' );
        $href = $this->uri ( array( 'action' => $action ) );
        $lnkIcn = $icn->getAddIcon( $href );
        return $lnkIcn;
    }
}

?>