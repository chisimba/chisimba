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
        $this->objDisplay = $this->newObject('permissionsdisplay', 'sitepermissions');
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
                $templateContent .= $this->objDisplay->createActionLinks('permissions');
                $templateContent .= $this->objDisplay->createForm('rule', '', $name);
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                
            case 'edit':
                $param = $this->getParam('param');
                $id = $this->getParam('id');
                $templateContent = $this->objDisplay->showModuleForm();
                $templateContent .= $this->objDisplay->createActionLinks('permissions');
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
                                $actionRuleId = $this->objDbperm->addActionRule($ruleId, $actionId, '', '', $this->getSession('module_name'));
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
                
            case 'manage_conditions':
                $id = $this->getParam('id', NULL);
                $mode = $this->getParam('mode', NULL);
                $templateContent = $this->objDisplay->createActionLinks('conditions');
                $templateContent .= $this->objDisplay->showCondition($mode, $id);
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                break;
                
            case 'save_conditions':
                $id = $this->getParam('id', NULL);
                $name = $this->getParam('name');
                $typeId = $this->getParam('type');
                $target = $this->getParam('target');
                if(!isset($id)){
                    $this->objDbperm->addCondition($name, $typeId, $target);
                    $this->objXmlperm->checkGroups($target);
                }else{
                    $this->objDbperm->updateCondition($id, $name, $typeId, $target);
                    $this->objXmlperm->checkGroups($target);
                }
                return $this->nextAction('manage_conditions', array(), 'sitepermissions');
                break;
                
            case 'manage_acls':
                $id = $this->getParam('id', NULL);
                $mode = $this->getParam('mode', NULL);
                $templateContent = $this->objDisplay->createActionLinks('acls');
                $templateContent .= $this->objDisplay->showAcls($mode, $id);
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                break;
                
            case 'save_acls':
                $id = $this->getParam('id', NULL);
                $name = $this->getParam('name');
                $desc = $this->getParam('desc');
                if(!isset($id)){
                    $this->objDbperm->addAcl($name, $desc);
                }else{
                    $this->objDbperm->updateAcl($id, $name, $desc);
                }
                return $this->nextAction('manage_acls', array(), 'sitepermissions');
                break;
                
            case 'manage_acl_users':
                $aclId = $this->getParam('aclId');
                $mode = $this->getParam('mode', NULL);
                $id = $this->getParam('id', NULL);
                $templateContent = $this->objDisplay->createActionLinks('users');
                $templateContent .= $this->objDisplay->showUsers($aclId, $mode, $id);
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
                break;
                
            case 'save_acl_users':
                $aclId = $this->getParam('aclId');
                $id = $this->getParam('id');
                $type = $this->getParam('type');
                if($type == 1){
                    $typeId = $this->getParam('group');
                    if(!isset($id)){
                        $this->objDbperm->addAclUser($aclId, $type, $typeId);
                    }else{
                        $this->objDbperm->updateAclGroup($id, $typeId);
                    }
                    $this->objXmlperm->checkGroups($typeId);
                }else{
                    $typeId = $this->getParam('userid');
                    $this->objDbperm->addAclUser($aclId, $type, $typeId);
                }
                return $this->nextAction('manage_acl_users', array(
                    'aclId' => $aclId,
                ));
                break;
            
            case 'delete_acl_user':
                $aclId = $this->getParam('aclId');
                $id = $this->getParam('id');
                $this->objDbperm->deleteAclUser($id);
                return $this->nextAction('manage_acl_users', array(
                    'aclId' => $aclId,
                ));
                break;
                
            case 'listusers':
                $search = $this->getParam('search');
                if($search == 'name'){
                    $search = 'firstName';
                    $criteria = $this->getParam('name');
                }else{
                    $search = 'surname';
                    $criteria = $this->getParam('surname');
                }
                return $this->objDisplay->searchList($search, $criteria);
                break;
                
            default:
                $templateContent = $this->objDisplay->showModuleForm();
                $templateContent .= $this->objDisplay->createActionLinks('permissions');
                $templateContent .= $this->objDisplay->showRuleConditions();
                $templateContent .= $this->objDisplay->showRuleActions();
                $templateContent .= $this->objDisplay->createExitLinks();
                $this->setVarByRef('templateContent', $templateContent);
                return 'template_tpl.php';
        }
    }
}
?>