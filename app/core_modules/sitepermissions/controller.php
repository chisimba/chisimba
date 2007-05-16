<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check
/**
 * The site permissions controller manages
 * the permissions
 * 
 * @author Kevin Cyster 
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package site permissiosn
 */

class sitepermissions extends controller {

    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLangauge;
    
    /**
    * @var object $objDisplay: The display class in the sitepermissions module
    * @access public
    */
    public $objDisplay;
    
    /**
    * @var object $objDbperm: The dbpermissions class in the site permissions module
    * @access public
    */
    public $objDbperm;
    
    /**
    * Method to initialise the controller
    * 
    * @access public
    * @return void
    */
    public function init()
    {
        $this->objLanguage = $this->getObject( 'language', 'language' );
        $this->objDisplay = $this->newObject('display', 'sitepermissions');
        $this->objDbperm = $this->newObject('dbpermissions', 'sitepermissions');        
        $this->objXmlperm = $this->newObject('xmlperm', 'sitepermissions');        
    }
    
    /**
    * Method the engine uses to kickstart the module
    * 
    * @access public
    * @param string $action: The action to be performed
    * @return void
    */
    function dispatch( $action )
    {
        // Test for first-time entry
        if( !$this->getSession( 'module_name' , FALSE ) ) {
            $this->setSession( 'module_name', 'sitepermissions' );
        }
        // Update session with dropdown
        if( $this->getParam( 'module_name', FALSE ) ) {
            $this->setSession( 'module_name', $this->getParam( 'module_name' ) );
        }
        switch ( $action ) {
            case 'add':
                $name = $this->getParam('name');
                $templateContent = $this->objDisplay->showModuleForm();
                $templateContent .= $this->objDisplay->createActionLinks();
                $templateContent .= $this->objDisplay->createForm('rule', '', $name);
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                
            case 'edit':
                $param = $this->getParam('param');
                $id = $this->getParam('id');
                $templateContent = $this->objDisplay->showModuleForm();
                $templateContent .= $this->objDisplay->createActionLinks();
                $templateContent .= $this->objDisplay->createForm($param, $id, '');
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
            
            case 'save':
                $mode = $this->getParam('mode');
                switch($mode){
                    case 'rule':
                        $id = $this->getParam('id');
                        $name = $this->getParam('item');
                        if($id != ''){
                            $this->objDbperm->deleteRule($id);  
                        }
                        $ruleId = $this->objDbperm->addRule($name, '', $this->getSession('module_name'));
                        $arrConditions = $this->getParam('rulecondition');
                        if(!empty($arrConditions)){
                            foreach($arrConditions as $conditionId){
                                $ruleConditionId = $this->objDbperm->addRuleCondition($ruleId, $conditionId, '', '', $this->getSession('module_name'));
                            }
                        }
                        $arrActions = $this->getParam('actionrule');
                        if(!empty($arrActions)){
                            foreach($arrActions as $actionId){
                                $actionRuleId = $this->objDbperm->addActionRule($ruleId, $actionId, '', $this->getSession('module_name'));
                            }
                        }
                        break;
                    case 'condition':
                        $conditionId = $this->getParam('id');
                        $this->objDbperm->deleteRuleConditions($conditionId, '', $this->getSession('module_name'));
                        $arrRules = $this->getParam('rulecondition');
                        if(!empty($arrRules)){
                            foreach($arrRules as $ruleId){
                                $ruleConditionId = $this->objDbperm->addRuleCondition($ruleId, $conditionId, '', '', $this->getSession('module_name'));
                            }
                        }
                        break;
                    case 'action':
                        $actionId = $this->getParam('id');
                        $this->objDbperm->deleteActionRules($actionId, '', $this->getSession('module_name'));
                        $arrRules = $this->getParam('actionrule');
                        if(!empty($arrRules)){
                            foreach($arrRules as $ruleId){
                                $actionRuleId = $this->objDbperm->addActionRule($ruleId, $actionId, '', $this->getSession('module_name'));
                            }
                        }
                        break;
                }
                return $this->nextAction('');
                break;
                
            case 'delete':
                $mode = $this->getParam('mode');
                $id = $this->getParam('id');
                switch($mode){
                    case 'rule':
                        $this->objDbperm->deleteRule($id);
                        return $this->nextAction('');
                        break;
                    case 'condition':
                        $this->objDbperm->deleteRuleConditions($id, '', $this->getSession('module_name'));
                        return $this->nextAction('');
                        break;
                    case 'action':
                        $this->objDbperm->deleteActionRules($id, '', $this->getSession('module_name'));
                        return $this->nextAction('');
                        break;
                }
            
            case 'update':
                $this->objXmlperm->updatePermissions();
                return $this->nextAction('');
                break;

            default:
                $templateContent = $this->objDisplay->showModuleForm();
                $templateContent .= $this->objDisplay->createActionLinks();
                $templateContent .= $this->objDisplay->showRuleConditions();
                $templateContent .= $this->objDisplay->showRuleActions();
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
        }
    }
}
?>