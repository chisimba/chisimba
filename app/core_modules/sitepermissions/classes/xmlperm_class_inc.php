<?php
/* ------ data class extends dbTable for all site permissions database tables ------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the site permissions database tables
* @author Kevin Cyster
*/

class xmlperm extends dbTable
{
    /**
    * @var object $objConfig: The altconfig class in the config module
    * @access public
    */
    public $objConfig;
    
    /**
    * @var object $objXML: The simplexml object
    * @access public
    */
    public $objXML;
    
    /**
    * @var array $arrConditions: The array of preset conditions
    * @access public
    */
    public $arrConditions = array(
        'isadmin',
        'islecturer',
        'isstudent',
        'isguest',
        'iscontextlecturer',
        'iscontextstudent',
        'iscontextguest',
    );
    
    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig','config');
        $this->objDbperm = $this->getObject('dbpermissions', 'sitepermissions');        
        $this->objGroupadmin = $this->getObject('groupadminmodel', 'groupadmin');        
    }

    /**
    * Method to get the permissions xml file
    *
    * @access private
    * @return string $path: The module path
    */
    private function getXmlFile()
    {
        $moduleName = $this->getSession('module_name');
        if(file_exists($this->objConfig->getModulePath()."/$moduleName")){
            $path = $this->objConfig->getModulePath()."/$moduleName/permissions.xml";
        }else{
            $path = $this->objConfig->getSiteRootPath().'core_modules/'.$moduleName."/permissions.xml";
        }
        if(file_exists($path)){
            return $path;
        }else{
            return FALSE;
        } 
    }
    
    /**
    * Method to read the XML file
    *
    * @access private
    * @return objject $objXML: The permission XML object
    */
    private function readXmlFile()
    {
        $this->objXML = simplexml_load_file($this->getXmlFile());
        return $this->objXML;
    }
    
    /**
    * Method to update the permissions for a module
    *
    * @access public
    * @param string $moduleName: The name of the module to update permissions for
    * @return void
    */
    public function updatePermissions($moduleName = '')
    {
        if($moduleName == ''){
            $moduleName = $this->getSession('module_name');
        }
        $this->objDbperm->deleteModule($moduleName);
        $moduleId = $this->objDbperm->addModule($moduleName);
        $this->readXmlFile();
        
        // check for group and create if nonexistant
        $targets = $this->objXML->targets;
        foreach($targets->group as $group){
            $this->checkGroups($group->name);
        }
        
        // check for acl and create if nonexistant
        foreach($targets->acl as $acl){
            $aclId = $this->objDbperm->addAcl($acl->name, $acl->description);
            foreach($acl->users->group as $group){
                $groupId = $this->checkGroups($group);
                $this->objDbperm->addAclUser($aclId, '1', $group);
            }            
        }
    
        // add conditions
        $conditions = $this->objXML->conditions;
        foreach($conditions->condition as $condition){
            if(!in_array(strtolower($condition->name), $this->arrConditions)){
                $this->objDbperm->addCondition($condition->name, $condition->type, $condition->target);
            }
        }
        
        // add actions
        $actions = $this->objXML->actions;
        foreach($actions->name as $action){
            $this->objDbperm->addAction($action, $moduleId);
        }
        
        // add rules
        $rules = $this->objXML->rules;
        foreach($rules->rule as $rule){
            $ruleId = $this->objDbperm->addRule($rule->name, $moduleId);
            $this->objDbperm->addRuleCondition($ruleId, '', $moduleId, $rule->condition, '');
            foreach($rule->action as $action)
                $this->objDbperm->addActionRule($ruleId, '', $moduleId, $action, '');
        }
    }
    
    /**
    * Method to check and create groups
    *
    * @access public
    * @param string $groups: The pipe delimited string for the group
    * @return string $leafId: The id of the last group in the string
    */
    public function checkGroups($groups)
    {
        $array = explode('|', $groups);                
        $lineage = array();
        for($i = 0; $i <= count($array) - 1; $i++){
            $lineage[] = $array[$i];
            if($i == 0){
                $parent = array();
            }else{
                $parent[] = $array[$i - 1];
            }
            $leafId = $this->objGroupadmin->getLeafId($lineage);
            if(empty($leafId)){
                if(empty($parent)){                    
                    $leafId = $this->objGroupadmin->addGroup($array[$i], $array[$i]);
                }else{
                    $parentId = $this->objGroupadmin->getLeafId($parent);
                    $leafId = $this->objGroupadmin->addGroup($array[$i], $array[$i], $parentId);
                }
            }
        }
        return $leafId;
    }
}
?>