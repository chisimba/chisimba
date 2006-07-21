<?php
/**
* @package toolbar
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class to update the dynamic site navigation.
*
* The class affects the side menus, main toolbar menus, lecturers page
* and admin page.
*
* The class provides functions to read the register files of modules
* and update or insert the toolbar and menu information into the table
* tbl_menu_category. The access permissions for the module are created
* if they don't already exist.
*
* @author Megan Watson
* @copyright 2005 (c) UWC
* @package toolbar
* @version 1
*/

class register extends object
{
    /**
    * Function to construct the class
    */
    function init()
    {
        $this->objFileReader =& $this->getObject('modulefile', 'modulecatalogue');
        $this->objModules =& $this->getObject('modules', 'modulecatalogue');
        $this->objModulesAdmin =& $this->getObject('modulesadmin', 'modulecatalogue');
        $this->objDbMenu =& $this->getObject('dbmenu');
    }

    /**
    * Method to update the dynamic navigation for the site.
    * The method empties or truncates the table tbl_menu_category and
    * finds a list of registered modules. The register.conf file of each
    * module is read and the relevant fields are used to refill the database
    * table.
    */
    function updateMenus()
    {
        $sql = "TRUNCATE TABLE tbl_menu_category";
        $this->objModulesAdmin->executeModSQL($sql);

        $modules = $this->objModules->getModules(2);
        foreach($modules as $line){
            $this->restoreDefaults($line['module_id']);
        }
    }

    /**
    * Method to reset the default permissions for a module
    * @param string $module The module name / Id
    */
    function setDefaultPermissions($module)
    {
        $data = $this->getModuleData($module);
        $this->restoreConditions($data, $module);
    }

    /**
    * Method to update the default permissions for a module
    */
    function updatePermissions()
    {
        $modules = $this->objModules->getModules(2);
        foreach($modules as $line){
            $module = $line['module_id'];
            $this->setDefaultPermissions($module);
        }
    }

    /**
    * Method to read data from the register.conf for a module.
    * @param string $module The module path.
    * @return array $regData The register data.
    */
    function getModuleData($module)
    {
        $filepath = $this->objFileReader->findRegisterFile($module);
        $regData = $this->objFileReader->readRegisterFile($filepath, FALSE);
        return $regData;
    }

    /**
    * Method to restore the modules default links and permissions.
    * @param string $module The specified module.
    */
    function restoreDefaults($module)
    {
        // Get Module Data
        $regData = $this->getModuleData($module);
        $this->readData($regData);
    }

    /*
    * Method to restore the default permissions for a link.
    * @param string $module The name of the module.
    * @return string $aclList The default permissions.
    */
    function restoreDefaultPerms($module)
    {
        // Get Module Data
        $data = $this->getModuleData($module);
        $aclList = $this->setPermissions($data, $module);
        return $aclList;
    }

    /**
    * Method to process the data from the register.conf for a module.
    * The method builds an array containing the modules default permissions.
    * @param string $module The module path.
    * @param array $data The processed data.
    */
    function readModuleData($module)
    {
        $objPerm = $this->getObject('permissions_model', 'permissions');
        $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
        $regData = $this->getModuleData($module);

        $data = array('acls'=>array(), 'groups'=>array(), 'cons'=>array(), 'toolbar'=>array(), 'sidemenu'=>array(), 'page'=>array());

        // Module settings
        if(isset($regData['MODULE_ISADMIN'])){
            $data['isAdmin'] = $regData['MODULE_ISADMIN'];
        }

        if(isset($regData['DEPENDS_CONTEXT'])){
            $data['isContext'] = $regData['DEPENDS_CONTEXT'];
        }

        // Module Permissions
        if(isset($regData['ACL'][0])){
            foreach($regData['ACL'] as $regAcl){
                $perms = explode('|', $regAcl);
                if(isset($perms[0]) && !empty($perms[0])){
                    $aclId = $objPerm->getId($module.'_'.$perms[0]);
                    $data['acls'][] = $aclId;
                }
                if(isset($perms[1]) && !empty($perms[1])){
                    $groups = explode(',', $perms[1]);
                    foreach($groups as $group){
                        $data['groups'][] = $module.'_'.$group;
                    }
                }
            }
        }

        // All groups
        if(isset($regData['USE_GROUPS'][0])){
            foreach($regData['USE_GROUPS'] as $regGroup){
                $data['groups'][] = $regGroup;
            }
        }

        // Context groups
        if(isset($regData['USE_CONTEXT_GROUPS'][0])){
            foreach($regData['USE_CONTEXT_GROUPS'] as $regCon){
                $data['cons'][] = $regCon;
            }
        }
        return $data;
    }

    /**
    * Method insert the dynamic navigation data in the table.
    * @param array $regData The navigation data for the module.
    */
    function readData($regData)
    {
        $moduleId = $regData['MODULE_ID'];

        $isAdmin = 0; $isContext = 0; $aclList = '';
        if(isset($regData['MODULE_ISADMIN'])){
            $isAdmin = $regData['MODULE_ISADMIN'];
        }
        if(isset($regData['DEPENDS_CONTEXT'])){
            $isContext = $regData['DEPENDS_CONTEXT'];
        }

        $aclList = $this->setPermissions($regData, $moduleId);

        // Menu category
        if (isset($regData['MENU_CATEGORY'][0]))
        {
            foreach ($regData['MENU_CATEGORY'] as $line)
            {
                $line=strtolower($line);
                $this->sql($line,$moduleId,$isAdmin,$aclList,$isContext);
            }
        }// end menu category

        // Side menus
        if (isset($regData['SIDEMENU'][0]))
        {
            $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
            foreach ($regData['SIDEMENU'] as $line)
            {
                $admin = $isAdmin;
                $groupList = '';
                $line=strtolower($line);

                $actions = array();
                $actions = explode('|', $line);

                if(isset($actions[1]) && !empty($actions[1])){
                    $line = str_replace($actions[1],'',$line);

                    $conGroups = ''; $siteGroups = ''; $acls = '';
                    $access = explode(',',$actions[1]);
                    $admin = 0;

                    foreach($access as $val){
                        // check for context groups
                        if(!(strpos($val, 'con_') === FALSE)){
                            if(!empty($conGroups)){
                                $conGroups .= ',';
                            }
                            $conGroups .= ucwords(str_replace('con_','',$val));

                        // check for module permissions, create if don't exist
                        }else if(!(strpos($val, 'acl_') === FALSE)){
                            $perm = str_replace('acl_','',$val);
                            $permId = $objPerm->getId($moduleId.'_'.$perm);
                            if(empty($permId)){
                                $permId = $objPerm->newAcl($moduleId.'_'.$perm, $moduleId.' '.$perm);
                            }
                            if(!empty($acls)){
                                $acls .= ',';
                            }
                            $acls .= $permId;

                        // check for module groups, create if don't exist
                        }else{
                            // check for sitewide access
                            if(strtolower($val) == 'site'){
                                $siteGroups .= 'site';
                            }else{
                                $grId = $objGroups->getId($val);
                                $group = ucwords($val);
                                if(empty($grId)){
                                    $group = $moduleId.'_'.ucwords($val);
                                    $grId = $objGroups->getId($group);
                                    if(empty($grId)){
                                        $objGroups->addGroup($group, $moduleId.' '.$val);
                                    }
                                }
                                if(!empty($siteGroups)){
                                    $siteGroups .= ',';
                                }
                                $siteGroups .= $group;
                            }
                        }
                    }
                    // build permissions string
                    $groupList = $acls.'|'.$siteGroups.'|_con_'.$conGroups;
                }else{
                    $groupList = $aclList;
                }
                $this->sql('menu_'.$line,$moduleId,$admin,$groupList,$isContext);
            }
        }// end side menu

        // admin and lecturer pages
        if(isset($regData['PAGE'][0])){
            foreach($regData['PAGE'] as $line){
                $actions = explode('|',$line);
                $pages = explode(',',$actions[0]);
                $admin = 0;
                foreach($pages as $page){
                    if(!(strpos($page, 'admin')===FALSE)){
                        $admin = 1;
                    }
                    if(!(strpos($page, 'lecturer')===FALSE)){
                        $admin = 0;
                    }
                }
                $this->sql('page_'.$line,$moduleId,$admin,$aclList,$isContext);
            }
        }// end pages
    }

    /**
    * Set up permissions for the module.
    * Set up a module specific ACL, set up module specific groups and add them
    * to the acl. If there is no ACL, set up groups.
    * @param array $regData The register data for the module.
    * @param string $moduleId The name of the module.
    * @return string $aclList The permissions list.
    */
    function setPermissions($regData, $moduleId)
    {
        $objPerm = $this->getObject('permissions_model', 'permissions');
        $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
        $aclList = '';

        if(isset($regData['ACL'][0])){
            foreach($regData['ACL'] as $regAcl){
                $perms = explode('|', $regAcl);

                if(isset($perms[0]) && !empty($perms[0])){
                    $aclId = $objPerm->getId($moduleId.'_'.$perms[0]);
                    if(empty($aclId)){
                        $aclId = $objPerm->newAcl($moduleId.'_'.$perms[0], $moduleId.' '.$perms[0]);
                    }
                    if(empty($aclList)){
                        $aclList = $aclId;
                    }else{
                        $aclList .= ','.$aclId;
                    }

                    if(isset($perms[1]) && !empty($perms[1])){
                        $groups = explode(',', $perms[1]);
                        foreach($groups as $group){
                            $groupId = $objGroups->getId($moduleId.'_'.$group);
                            if(empty($groupId)){
                                $groupId = $objGroups->addGroup($moduleId.'_'.$group, $moduleId
                                .' '.$group);
                                $objPerm->addAclGroup($aclId, $groupId);
                            }
                        }
                    }
                }else{
                    if(isset($perms[1]) && !empty($perms[1])){
                        $groups = explode(',', $perms[1]);
                        foreach($groups as $group){
                            $groupId = $objGroups->getId($moduleId.'_'.$group);
                            if(empty($groupId)){
                                $groupId = $objGroups->addGroup($moduleId.'_'.$group, $moduleId
                                .' '.$group);
                            }
                        }
                    }
                }
            }
        }

        // Link existing groups with access to the module.
        // First check if the group exists and create it if it doesn't.
        if(isset($regData['USE_GROUPS'][0])){
            $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
            $groupList = '';

            foreach($regData['USE_GROUPS'] as $group){
                $grId = $objGroups->getId($group);
                if(empty($grId)){
                    $objGroups->addGroup($group, $moduleId.' '.$group);
                }
                if(empty($groupList)){
                    $groupList = $group;
                }else{
                    $groupList .= ','.$group;
                }
            }
            $aclList .= '|'.$groupList;
        }

        // Link existing groups with access to a context dependent module
        if(isset($regData['USE_CONTEXT_GROUPS'][0])){
            $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
            $contextGroupList = '';

            foreach($regData['USE_CONTEXT_GROUPS'] as $conGroup){
                if(empty($contextGroupList)){
                    $contextGroupList = $conGroup;
                }else{
                    $contextGroupList .= ','.$conGroup;
                }
            }
            $aclList .= '|_con_'.$contextGroupList;
        }

        return $aclList;
    }

    /**
    * Method to restore the default conditions
    */
    function restoreConditions($registerdata, $moduleId)
    {
        $aclList = '';
        $groupArray = array();
        $groupArray2 = array();
        $permList = array();

        /* Set up permissions for the module.
            Set up a module specific ACL, set up module specific groups and add them
            to the acl.
            If there is no ACL, set up groups.
        */
        if(isset($registerdata['ACL'][0])){
            $objPerm = $this->getObject('permissions_model', 'permissions');
            $objGroups = $this->getObject('groupAdminModel', 'groupadmin');

            foreach($registerdata['ACL'] as $regAcl){
                $perms = explode('|', $regAcl);

                if(isset($perms[0]) && !empty($perms[0])){
                    $aclId = $objPerm->newAcl($moduleId.'_'.$perms[0], $moduleId.' '.$perms[0]);
                    if(empty($aclList)){
                        $aclList = $aclId;
                    }else{
                        $aclList .= ','.$aclId;
                    }
                    $permList[] = $perms[0];

                    if(isset($perms[1]) && !empty($perms[1])){
                        $groups = explode(',', $perms[1]);
                        foreach($groups as $group){
                            $groupId = $objGroups->addGroup($moduleId.'_'.$group, $moduleId
                            .' '.$group);
                            $objPerm->addAclGroup($aclId, $groupId);
                            $groupArray[] = $group;
                        }
                    }
                }else{
                    if(isset($perms[1]) && !empty($perms[1])){
                        $groups = explode(',', $perms[1]);
                        foreach($groups as $group){
                            $groupId = $objGroups->addGroup($moduleId.'_'.$group, $moduleId
                            .' '.$group);
                            $groupArray[] = $group;
                        }
                    }
                }
            }
        }

        // Link existing groups with access to the module.
        // First check if the group exists and create it if it doesn't.
        if(isset($registerdata['USE_GROUPS'][0])){
            $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
            $groupList = '';

            foreach($registerdata['USE_GROUPS'] as $group){
                $grId = $objGroups->getId($group);
                if(empty($grId)){
                    $objGroups->addGroup($group, $moduleId.' '.$group);
                }
                $groupArray2[] = $group;
                if(empty($groupList)){
                    $groupList = $group;
                }else{
                    $groupList .= ','.$group;
                }
            }
            $aclList .= '|'.$groupList;
        }

        // Link existing groups with access to a context dependent module
        if(isset($registerdata['USE_CONTEXT_GROUPS'][0])){
            $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
            $contextGroupList = '';

            foreach($registerdata['USE_CONTEXT_GROUPS'] as $conGroup){
                if(empty($contextGroupList)){
                    $contextGroupList = $conGroup;
                }else{
                    $contextGroupList .= ','.$conGroup;
                }
            }
            $aclList .= '|_con_'.$contextGroupList;
        }

        /* Create conditions.
            Create a condition in the decisiontable, returns the condition object.
            Populate an array with condition objects for use in creating rules.
        */
        $conditions = array();
        if(isset($registerdata['CONDITION'][0])){
            $objCond =& $this->getObject('condition','decisiontable');
            foreach($registerdata['CONDITION'] as $condition){
                $array = explode('|', $condition);
                if(isset($array[2]) && !empty($array[2])){
                            $list = explode(',', $array[2]);
                }else{
                    $list = '';
                }
                $paramList = array();

                if($array[1] == 'hasPermission'){
                    foreach($permList as $perm){
                        foreach($list as $val){
                            if($perm == $val){
                                $val = $moduleId.'_'.$perm;
                                $paramList[] = $val;
                            }
                        }
                    }
                }else if($array[1] == 'isMember'){
                    foreach($list as $val){
                        foreach($groupArray as $perm){
                            if($perm == $val){
                                $val = $moduleId.'_'.$perm;
                                $paramList[] = $val;
                            }
                        }
                        foreach($groupArray2 as $perm2){
                            if($perm2 == $val){
                                $val = $perm2;
                                $paramList[] = $val;
                            }
                        }
                    }
                }else{
                    $paramList = $list;
                }

                $name = $array[0];
                if(!empty($paramList)){
                    $paramList = implode(',', $paramList);
                            $params = $array[1].$objCond->_delimiterFunc.$paramList;
                    }else{
                    $params = $array[1];
                }
                $conditions[$name] = $objCond->create($name, $params);
            }
        }

        // Use existing conditions
        if(isset($registerdata['USE_CONDITION'][0])){
            $objCond =& $this->getObject('condition','decisiontable');
            foreach($registerdata['USE_CONDITION'] as $condition){
                $array = explode('|', $condition);
                $name = $array[0];
                $conditions[$name] = $objCond->create($name);
            }
        }

        /* Create rules.
            Create the decisiontable for the module.
            Create the action in the decisiontable, returns the action object.
            Create the rule in the decisiontable, returns the rule object.
            Add the action object to the rule object.
            Add the condition object to the rule object.
        */
        if(isset($registerdata['RULE'][0])){
            $objDecisionTable =& $this->getObject('decisiontable','decisiontable');
            $objAction =& $this->getObject('action','decisiontable');
            $objAction->connect($objDecisionTable);
            $objRule =& $this->getObject('rule','decisiontable');
            $objRule->connect($objDecisionTable);
            $i = 1;

            // Create the decision table
            $modTable = $objDecisionTable->create($moduleId);

            foreach($registerdata['RULE'] as $rule){
                $ruleName = $moduleId.' rule '.$i++;
                $array = explode('|', $rule);
                $actionList = explode( ',', $array[0] );
                $conditionList = explode( ',', $array[1] );

                // Create rule object and add to the decision table
                $rule = $objRule->create($ruleName);
                // Add the rule to the decision table.
                $objDecisionTable->addRule( $rule );

                // Create action object and add to decision table.
                foreach( $actionList as $anAction ) {
                    $arrActions[$anAction] = $objAction->create($anAction);
                    // Add the action to the decision table.
                    $objDecisionTable->add( $arrActions[$anAction] );
                    // Add the rule to the action
                    $arrActions[$anAction]->add($rule);
                }

                // Add the condition to the rule
                foreach( $conditionList as $aCondition ) {

                    $rule->add($conditions[$aCondition]);
                }
            }
        }
        // end Permissions and Security

    }

    /**
    * Method to write the sql to insert the data into the table.
    * The method bypasses the dbTable method since the data is site
    * specific and shouldn't be mirrored.
    */
    function sql($category, $module, $admin, $aclList, $context)
    {
        $sql = 'INSERT INTO tbl_menu_category ';
        $sql .= '(id, category, module, adminOnly, permissions, dependsContext) ';
        $sql .= "values('init@".time().rand(1000,9999)."','$category','$module',";
        $sql .= "'$admin', '$aclList', '$context')";
        $this->objModulesAdmin->executeModSQL($sql);
    }
}
?>