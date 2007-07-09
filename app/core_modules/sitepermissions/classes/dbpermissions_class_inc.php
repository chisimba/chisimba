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

class dbpermissions extends dbTable
{
    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
    }
    
/* ----- Functions for changeing tables ----- */

	/**
	* Method to dynamically switch tables
	*
	* @access private
	* @param string $table: The name of the table
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _changeTable($table)
	{
		try{
			parent::init($table);
			return TRUE;
		}catch(customException $e){
			customException::cleanUp();
			return FALSE;
		}
	}
	
	/**
	* Method to set the module table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setModules()
	{
        return $this->_changeTable('tbl_sitepermissions_modules');
    }

	/**
	* Method to set the rule table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setRule()
	{
        return $this->_changeTable('tbl_sitepermissions_rule');
    }

	/**
	* Method to set the action table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setAction()
	{
        return $this->_changeTable('tbl_sitepermissions_action');
    }
    
	/**
	* Method to set the condition table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setCondition()
	{
        return $this->_changeTable('tbl_sitepermissions_condition');
    }
    
	/**
	* Method to set the conditiontype table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setConditiontype()
	{
        return $this->_changeTable('tbl_sitepermissions_conditiontype');
    }

	/**
	* Method to set the rule condition table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setRuleCondition()
	{
        return $this->_changeTable('tbl_sitepermissions_rule_condition');
    }

	/**
	* Method to set the action rule table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setActionRule()
	{
        return $this->_changeTable('tbl_sitepermissions_action_rule');
    }

	/**
	* Method to set the acl table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setAcl()
	{
        return $this->_changeTable('tbl_sitepermissions_acl');
    }

	/**
	* Method to set the acl users table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setAclUsers()
	{
        return $this->_changeTable('tbl_sitepermissions_acl_users');
    }

	/**
	* Method to set the users table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setUsers()
	{
        return $this->_changeTable('tbl_users');
    }

/* ----- Functions for tbl_sitepermissions_conditiontype ----- */

    /**
    * Method to add a condition type
    *
    * @access public
    * @param string $type: The condition type name
    * @param string $class: The condition class
    * @param string $moduleName: The module creating the condition type
    * @return string|bool $typeId: The condition type id |False on failure
    **/
    public function addConditionType($type)
    {
        $conditiontype = $this->getConditionType($type);
        if($conditiontype){
            $typeId = $conditiontype['id'];
            return $typeId;
        }else{
            $this->_setConditiontype();        
            $fields['name'] = strtolower($type);
            $typeId = $this->insert($fields);
            return $typeId;
        }
        return FALSE;
    }
    
    /**
    * Method to get a condition type
    *
    * @access public
    * @param string $type: The condition type name
    * @return array|bool $data: Conditiontype data on success | False on failure
    */
    public function getConditionTypeByName($type)
    {
        $this->_setConditiontype();        
        $sql = "WHERE name = '".strtolower($type)."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }    

    /**
    * Method to get all condition types
    *
    * @access public
    * @return array|bool $data: Conditiontype data on success | False on failure
    */
    public function listConditionTypes()
    {
        $this->_setConditiontype();        
        $data = $this->getAll();
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }    

/* ----- Functions for tbl_sitepermissions_condition ----- */

    /**
    * Method to add a condition
    *
    * @access public
    * @param string $name: The condition name
    * @param string $type: The condition type
    * @param string $target: The condition target
    * @return string|bool $conditionId: The condition id |False on failure
    **/
    public function addCondition($name, $type, $target)
    {
        $condition = $this->getConditionByName($name);
        if($condition){
            $conditionId = $condition['id'];
            return $conditionId;
        }else{
            $conditionType = $this->getConditionTypeByName($type);
            if($conditionType){
                $typeId = $conditionType['id'];
                $this->_setCondition();        
                $fields['typeid'] = $typeId;
                $fields['name'] = strtolower($name);
                $fields['target'] = strtolower($target);
                $conditionId = $this->insert($fields);
                return $conditionId;
            }
        }
        return FALSE;
    }
    
    /**
    * Method to update a condition
    *
    * @access public
    * @param string $id: The id of the condition to update
    * @param string $name: The condition name
    * @param string $typeId: The id of the condition type
    * @param string $target: The condition target
    * @return void
    **/
    public function updateCondition($id, $name, $typeId, $target)
    {
        $this->_setCondition();        
        $fields['typeid'] = $typeId;
        $fields['name'] = strtolower($name);
        $fields['target'] = strtolower($target);
        $conditionId = $this->update('id', $id, $fields);
    }
    
    /**
    * Method to get a condition
    *
    * @access public
    * @param string moduleId: The id of the module the condition is for
    * @param string $name: The condition name
    * @return array|Bool $data: Condition data on success | False on failure
    */
    public function getConditionByName($name)
    {
        $this->_setCondition();        
        $sql = "WHERE name = '".strtolower($name)."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }    

    /**
    * Method to get a condition by id
    *
    * @access public
    * @param string $id: The condition id
    * @return array|Bool $data: Condition data on success | False on failure
    */
    public function getConditionById($id)
    {
        $this->_setCondition();        
        $sql = "WHERE id = '".$id."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }    

    /**
    * Method to list conditions by module
    *
    * @access public
    * @param string $name: The module(module) name
    * @return array|Bool $data: Condition data on success | False on failure
    */
    public function listModuleConditions($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];
            $this->_setRuleConditions();            
            $sql = "SELECT * FROM tbl_sitepermissions_rule_condition AS rc";
            $sql .= " LEFT JOIN tbl_sitepermissions_condition as c";
            $sql .= " ON rc.conditionid = c.id";
            $sql .= " WHERE rc.moduleid = '".$moduleId."'";
            $data = $this->getArray($sql);
            if(!empty($data)){
                return $data;
            }
        }
        return FALSE;
    }    

    /**
    * Method to list all conditions
    *
    * @access public
    * @return array|Bool $data: Condition data on success | False on failure
    */
    public function listConditions()
    {
        $this->_setCondition();
        $sql = "ORDER BY name";        
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }    

/* ----- Functions for tbl_sitepermissions_modules ----- */

    /**
    * Method to add a module
    *
    * @access public
    * @param string $name: The module name
    * @return string|bool $moduleId: The module id | False on failure
    **/
    public function addModule($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];
            return $moduleId;
        }else{
            $this->_setModules();        
            $fields['name'] = strtolower($name);
            return $moduleId = $this->insert($fields);
            $moduleId;
        }
        return FALSE;
    }
    
    /**
    * Method to get a module
    *
    * @access public
    * @param string $name: The module name
    * @return array|bool $data: Module data on success | False on failure
    */
    public function getModuleByName($name)
    {
        $this->_setModules();        
        $sql = "WHERE name = '".strtolower($name)."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }    

    /**
    * Method to delete a module
    *
    * @access public
    * @param string $name: The module name
    * @return bool $success: True on success | False on failure
    */
    public function deleteModule($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];
            $this->_setModules();            
            $this->delete('id', $moduleId);               
            $this->_setRule();
            $this->delete('moduleid', $moduleId);
            $this->_setAction();
            $this->delete('moduleid', $moduleId);            
            $this->_setActionRule();
            $this->delete('moduleid', $moduleId);            
            $this->_setRuleCondition();
            $this->delete('moduleid', $moduleId);            
        }
    }    

/* ----- Functions for tbl_sitepermissions_rule ----- */

    /**
    * Method to add a rule
    *
    * @access public
    * @param string $name: The rule name
    * @param string $moduleId: The module id
    * @param string $moduleName: The module name
    * @return string|bool $ruleId: The rule id | False on failure
    **/
    public function addRule($name, $moduleId, $moduleName = '')
    {
        if($moduleId == '' && $moduleName == ''){
            return FALSE;    
        }
        if($moduleName != ''){
            $module = $this->getmoduleByName($moduleName);
            if($module){
                $moduleId = $module['id'];
            }else{
                return FALSE;
            }
        }
        $rule = $this->getRule($name, $moduleId);
        if($rule){
            $ruleId = $rule['id'];
            return $ruleId;
        }else{
            $this->_setRule();
            $fields['moduleid'] = $moduleId;   
            $fields['name'] = strtolower($name);
            $ruleId = $this->insert($fields);
            return $ruleId;
        }
        return FALSE;
    }
    
    /**
    * Method to get a rule
    *
    * @access public
    * @param string $name: The rule name
    * @param string $moduleId: The module id
    * @return array|bool $data: Rule data on success | False on failure
    */
    public function getRule($name, $moduleId)
    {
        $this->_setRule();        
        $sql = "WHERE name = '".strtolower($name)."'";
        $sql .= " AND moduleid = '".$moduleId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
        
    /**
    * Method to list rules for a module
    *
    * @access public
    * @param string $name: The module name
    * @return array|bool $data: Rule data on success | False on failure
    */
    public function listRules($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];
            $this->_setRule();        
            $sql = "WHERE moduleid = '".$moduleId."'";
            $sql .= " ORDER BY name";
            $data = $this->getAll($sql);
            if(!empty($data)){
                return $data;
            }
        }
        return FALSE;
    }
    
    /**
    * Method to delete rules for a module
    * 
    * @access public
    * @param string $id: The id of the rule to delete
    * @return void
    */
    public function deleteRule($id)
    {
        $this->_setRule();
        $this->delete('id', $id);
        $this->_setActionRule();
        $this->delete('ruleid', $id);
        $this->_setRuleCondition();
        $this->delete('ruleid', $id);
    }
    
/* ----- Functions for tbl_sitepermissions_rule_condition ----- */

    /**
    * Method to add a bridge between rules and conditions
    *
    * @access public
    * @param string $ruleId: The rule id
    * @param string $conditionId: The condition id
    * @param string $moduleId: The module id
    * @param string $conditionName: The condition name
    * @param string $moduleName: The module name
    * @return string|bool $ruleConditionId: The rule condition id | False on failure
    **/
    public function addRuleCondition($ruleId, $conditionId, $moduleId, $conditionName = '', $moduleName = '')
    {
        if($ruleId == '' && $conditionId == '' && $moduleId == ''){
            return FALSE;    
        }
        if($conditionId == '' && $conditionName == ''){
            return FALSE;    
        }
        if($moduleId == '' && $moduleName == ''){
            return FALSE;    
        }
        if($conditionName != ''){
            $condition = $this->getConditionByName($conditionName);
            if($condition){
                $conditionId = $condition['id'];
            }else{
                return FALSE;
            }
        }
        if($moduleName != ''){
            $module = $this->getModuleByName($moduleName);
            if($module){
                $moduleId = $module['id'];
            }else{
                return FALSE;
            }
        }
        $this->_setRuleCondition();
        $fields['moduleid'] = $moduleId;
        $fields['ruleid'] = $ruleId;    
        $fields['conditionid'] = $conditionId;
        $ruleConditionId = $this->insert($fields);
        return $ruleConditionId;
    }

    /**
    * Method to list all rules and conditions for a module
    *
    * @access public
    * @param string $name: The module name
    * @return array|bool $data: Rule data on success | False on failure
    **/
    public function listRuleConditions($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];            
            $this->_setRuleCondition();
            $sql = "SELECT *, c.name AS cname, r.name AS rname";
            $sql .= " FROM tbl_sitepermissions_rule_condition AS rc";
            $sql .= " LEFT JOIN tbl_sitepermissions_condition AS c";
            $sql .= " ON rc.conditionid = c.id";
            $sql .= " LEFT JOIN tbl_sitepermissions_rule AS r";
            $sql .= " ON rc.ruleid = r.id";
            $sql .= " WHERE rc.moduleid = '".$moduleId."'";
            $data = $this->getArray($sql);
            if(!empty($data)){
                return $data;
            }
        }
        return FALSE;
    }
    
    /**
    * Method to delete rule conditions
    * 
    * @access public
    * @param string $id: The id of the condition to delete condition rules for
    * @return void
    */
    public function deleteRuleConditionById($id)
    {
        $this->_setRuleCondition();
        $this->delete('conditionid', $id);
    }

    /**
    * Method to delete rule conditions for a module
    * 
    * @access public
    * @param string $conditionId: The id of the condition
    * @param string $moduleId: The id of the module
    * @param string $moduleName: The name of the module
    * @return void
    */
    public function deleteRuleConditions($conditionId, $moduleId = '', $moduleName = '')
    {
        if($moduleId == '' && $moduleName == ''){
            return FALSE;    
        }
        if($moduleName != ''){
            $module = $this->getModuleByName($moduleName);
            if($module){
                $moduleId = $module['id'];
            }else{
                return FALSE;
            }
        }
        $data = $this->getRuleConditions($moduleId, $conditionId);
        if(!empty($data)){
            $this->_setRuleCondition();
            foreach($data as $line){
                $this->delete('id', $line['id']); 
            }
        }       
    }
    
    /**
    * Method to get the rule condition for a module
    *
    * @access public
    * @param string $moduleId: The module id
    * @param string $conditionId: The condition id
    * @return array|bool $data: Rule condition data on success | False on failure
    */
    public function getRuleConditions($moduleId, $conditionId)
    {
        $this->_setRuleCondition();
        $sql = "WHERE moduleid = '".$moduleId."'";
        $sql .= " AND conditionid = '".$conditionId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ----- Functions for tbl_sitepermissions_action ----- */

    /**
    * Method to add an action
    *
    * @access public
    * @param string $name: The action name
    * @param string $moduleId: The module id
    * @param string $ruleId: The rule id
    * @return string|bool $actionId: The action id | False on failure
    **/
    public function addAction($name, $moduleId)
    {
        $action = $this->getAction($name, $moduleId);
        if($action){
            $actionId = $action['id'];
            return $actionId;
        }else{
            $this->_setAction();
            $fields['moduleid'] = $moduleId;
            $fields['name'] = strtolower($name);
            $actionId = $this->insert($fields);
            return $actionId;
        }
        return FALSE;
    }

    /**
    * Method to get an action
    *
    * @access public
    * @param string $name: The action name
    * @param string $moduleId: The module id
    * @return array|bool $data: Rule data on success | False on failure
    */
    public function getAction($name, $moduleId)
    {
        $this->_setAction();        
        $sql = "WHERE name = '".strtolower($name)."'";
        $sql .= " AND moduleid = '".$moduleId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to list actions for a module
    *
    * @access public
    * @param string $name: The module name
    * @return array|bool $data: Action data on success | False on failure
    */
    public function listActions($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];
            $this->_setAction();        
            $sql = "WHERE moduleid = '".$moduleId."'";
            $sql .= " ORDER BY name";
            $data = $this->getAll($sql);
            if(!empty($data)){
                return $data;
            }
        }
        return FALSE;
    }
    
/* ----- Functions for tbl_sitepermissions_action_rule ----- */

    /**
    * Method to add a bridge between actions and rules
    *
    * @access public
    * @param string $ruleId: The rule id
    * @param string $actionId: The action id
    * @param string $moduleId: The module id
    * @param string $actionName: The action name
    * @param string $moduleName: The module name
    * @return string|bool $actionRuleId: The action rule id | False on failure
    **/
    public function addActionRule($ruleId, $actionId, $moduleId, $actionName = '', $moduleName = '')
    {
        if($ruleId == '' && $actionId == '' && $moduleId == ''){
            return FALSE;    
        }
        if($moduleId == '' && $moduleName == ''){
            return FALSE;    
        }
        if($moduleName != ''){
            $module = $this->getModuleByName($moduleName);
            if($module){
                $moduleId = $module['id'];
            }else{
                return FALSE;
            }
        }
        if($actionId == '' && $actionName == ''){
            return FALSE;    
        }
        if($actionName != ''){
            $action = $this->getAction($actionName, $moduleId);
            if($action){
                $actionId = $action['id'];
            }else{
                return FALSE;
            }
        }
        $this->_setActionRule();
        $fields['moduleid'] = $moduleId;
        $fields['ruleid'] = $ruleId;    
        $fields['actionid'] = $actionId;
        $actionRuleId = $this->insert($fields);
        return $actionRuleId;
    }

    /**
    * Method to list all actions and rules for a module
    *
    * @access public
    * @param string $name: The module name
    * @return array|bool $data: Rule data on success | False on failure
    **/
    public function listActionRules($name)
    {
        $module = $this->getModuleByName($name);
        if($module){
            $moduleId = $module['id'];            
            $this->_setActionRule();
            $sql = "SELECT *, a.name AS aname, r.name AS rname, a.id AS aid, r.id AS rid";
            $sql .= " FROM tbl_sitepermissions_action_rule AS ar";
            $sql .= " LEFT JOIN tbl_sitepermissions_action AS a";
            $sql .= " ON ar.actionid = a.id";
            $sql .= " LEFT JOIN tbl_sitepermissions_rule AS r";
            $sql .= " ON ar.ruleid = r.id";
            $sql .= " WHERE ar.moduleid = '".$moduleId."'";
            $data = $this->getArray($sql);
            if(!empty($data)){
                return $data;
            }
        }
        return FALSE;
    }
    
    /**
    * Method to delete action rules
    * 
    * @access public
    * @param string $id: The id of the action to delete action rules for
    * @return void
    */
    public function deleteActionRuleById($id)
    {
        $this->_setActionRule();
        $this->delete('actionid', $id);
    }
    
    /**
    * Method to delete action rules for a module
    * 
    * @access public
    * @param string $actionId: The id of the action
    * @param string $moduleId: The id of the module
    * @param string $moduleName: The name of the module
    * @return void
    */
    public function deleteActionRules($actionId, $moduleId = '', $moduleName = '')
    {
        if($moduleId == '' && $moduleName == ''){
            return FALSE;    
        }
        if($moduleName != ''){
            $module = $this->getModuleByName($moduleName);
            if($module){
                $moduleId = $module['id'];
            }else{
                return FALSE;
            }
        }
        $data = $this->getActionRules($moduleId, $actionId);
        if(!empty($data)){
            $this->_setActionRule();
            foreach($data as $line){
                $this->delete('id', $line['id']); 
            }
        }       
    }
    
    /**
    * Method to get the rule condition for a module
    *
    * @access public
    * @param string $moduleId: The module id
    * @param string $conditionId: The condition id
    * @return array|bool $data: Rule condition data on success | False on failure
    */
    public function getActionRules($moduleId, $actionId)
    {
        $this->_setActionRule();
        $sql = "WHERE moduleid = '".$moduleId."'";
        $sql .= " AND actionid = '".$actionId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ----- Functions for tbl_sitepermissions_acl ----- */

    /**
    * Method to add an access control lists
    *
    * @access public
    * @param string $name: The acl name
    * @param string $desc: The acl description
    * @return string|bool $aclId: The acl id | False on failure
    **/
    public function addAcl($name, $desc)
    {
        $acl = $this->getAclByName($name);
        if($acl){
            $aclId = $acl['id'];
            return $aclId;
        }else{
            $this->_setAcl();
            $fields['name'] = $name;
            $fields['description'] = $desc;
            $aclId = $this->insert($fields);
            return $aclId;
        }
        return FALSE;
    }

    /**
    * Method to update an access control list
    *
    * @access public
    * @param string $id: The ACL id
    * @param string $name: The acl name
    * @param string $desc: The acl description
    * @return void
    **/
    public function updateAcl($id, $name, $desc)
    {
        $this->_setAcl();
        $fields['name'] = $name;
        $fields['description'] = $desc;
        $this->update('id', $id, $fields);
    }

    /**
    * Method to get an access control list
    *
    * @access public
    * @param string $name: The acl name
    * @return array|bool $data: Acl data on success | False on failure
    */
    public function getAclByName($name)
    {
        $this->_setAcl();        
        $sql = "WHERE name = '".strtolower($name)."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
        
    /**
    * Method to get an access control list
    *
    * @access public
    * @param string $id: The acl id
    * @return array|bool $data: Acl data on success | False on failure
    */
    public function getAclById($id)
    {
        $this->_setAcl();        
        $sql = "WHERE id = '".strtolower($id)."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
        
    /**
    * Method to list access control lists
    *
    * @access public
    * @return array|bool $data: Acl data on success | False on failure
    */
    public function listAcls()
    {
        $this->_setAcl();        
        $data = $this->getAll();
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
        
    /**
    * Method to delete an access control list
    *
    * @access public
    * @param string $id: The acl id
    * @return void
    */
    public function deleteAcl($id)
    {
        $this->_setAcl();
        $this->delete('id', $id);
    }

/* ----- Functions for tbl_sitepermissions_acl_users ----- */

    /**
    * Method to add users to an access control list
    *
    * @access public
    * @param string $aclid: The id of the acl
    * @param string $type: The type of user
    * @param string $typeId: The user id
    * @return string|bool $aclId: The acl id | False on failure
    **/
    public function addAclUser($aclId, $type, $typeId)
    {
        $this->_setAclUsers();
        $fields = array();
        $fields['aclid'] = $aclId;
        $fields['type'] = $type;
        $fields['typeid'] = $typeId;
        $aclId = $this->insert($fields);
        return $aclId;
    }
    
    /**
    * Method to return acl groups
    *
    * @access public
    * @param string $aclId: The id of the acl to return groups for
    * @return array|bool $data: The acl user data on success | False on failure
    **/
    public function getAclGroups($aclId)
    {
        $this->_setAclUsers();
        $sql = "WHERE aclid = '".$aclId."'";
        $sql .= " AND type = '1'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to return acl users
    *
    * @access public
    * @param string $aclId: The id of the acl to return groups for
    * @return array|bool $data: The acl user data on success | False on failure
    **/
    public function getAclUsers($aclId)
    {
        $this->_setAclUsers();
        $sql = "WHERE aclid = '".$aclId."'";
        $sql .= " AND type = '2'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to update the acl group user
    *
    * @access public
    * @param string $id: The id of the acl group user
    * @param string $typeId: The new acl group user
    * @return void
    */
    public function updateAclGroup($id, $typeId)
    {
        $this->_setAclUsers();
        $this->update('id', $id, array(
            'typeid' => $typeId,
        ));
    }    

    /**
    * Method to delete an access control list user
    *
    * @access public
    * @param string $id: The acl user id
    * @return void
    */
    public function deleteAclUser($id)
    {
        $this->_setAclUsers();
        $this->delete('id', $id);
    }
    
/* ----- Functions for tbl_users ----- */

    /**
    * Method for geting users from the database.
    *
    * @access public
    * @param string $search: The field to search
    * @param string $criteria: The search criteria
    * @return array|bool $data: The data array on success | FALSE on failure
    */
    public function getUsers($search, $criteria = NULL)
    {
        $this->_setUsers();
        $sql = " SELECT * FROM tbl_users";
        if ($criteria != NULL) {
            $sql.= " WHERE ".$search." LIKE '".$criteria."%'";
        }
        $sql.= " ORDER BY ".$search;
        $sql.= " LIMIT 0, 10";
        
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }
}
?>