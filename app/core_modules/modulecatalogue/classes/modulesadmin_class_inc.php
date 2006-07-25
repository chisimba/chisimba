<?PHP
/* -------------------- dbTable class for dbmanagerdb ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Class for manipulating modules with administrative functionality.
* Dividing the class in two like this avoids loading this
* file when only the basic user functionality is needed.
* @author Nic Appleby
* @copyright AVOIR
* @license GNU/GPL
* @category Chisimba
* @package modulecatalogue
* @version $Id$
*/

class modulesadmin extends dbTableManager
{
	/**
	 * Current module ID
	 *
	 * @var string $module_id
	 */
	private $module_id;

	/**
	 * Current module's name
	 *
	 * @var string $module_name
	 */
	private $module_name;

	/**
	 * Descritpion of current module
	 *
	 * @var string $module_description
	 */
    private $module_description;

    /**
     * Object interface to TableInfo class
     *
     * @var object objTableInfo
     */
    private $objTableInfo;

    /**
     * Code of last error encountered
     *
     * @var integer $_lastError
     */
    private $_lastError;

    /**
     * Object interface to KeyMaker class
     *
     * @var object $objKeyMaker
     */
    private $objKeyMaker;

    private $update=FALSE;
    public $output;

    /**
	 * object to manipulate the modules table
	 *
	 * @var object $objModules
	 */
	protected $objModules;

	/**
	 * User data object
	 *
	 * @var object $objUser
	 */
	protected $objUser;

	/**
	 * System configuration object
	 *
	 * @var object $objConfig
	 */
	public $objConfig;

    /**
     * Standard initilisation method
     *
     */
    public function init()
    {
        try {
        	parent::init('tbl_modules');
        	$this->objLanguage = $this->getObject('language','language');
        	$this->objConfig = $this->getObject('altconfig','config');
            $this->objModules = &$this->getObject('modules');
            $this->objModFile = &$this->getObject('modulefile');
            $this->objUser = &$this->getObject('user','security');
        } catch (Exception $e) {
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
     * Method to check whether a module dependency is registered
     *
     * @param string $moduleId the id of the dpendency
     * @return TRUE|FALSE
     */
    public function checkDependency($moduleId) {
    	try {
    		return $this->objModules->checkIfRegistered($moduleId);
    	} catch (Exception $e) {
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to register the module. It stores the module information in
    * the database table tbl_modules, creates any needed SQL tables,
    * adds languagetext elements, moves icons, etc. All based on info from
    * the module's 'register.conf' file.
    * @param array $registerdata - all the info from the register.conf file.
    * @returns mixed OK | FALSE
    */
    public function installModule(&$registerdata) {
        try {
            if (isset($registerdata['MODULE_ID'])) {
                $moduleId=$registerdata['MODULE_ID'];
            } else {
            	$this->_lastError = 1001;
                return FALSE; // If we can't find the name of the module we're supposed to be registering, what are we doing here?
            }
            $this->module_id=$registerdata['MODULE_ID'];
            $this->module_name=$registerdata['MODULE_NAME'];
            $this->module_description=$registerdata['MODULE_DESCRIPTION'];
    		//$this->objModules->beginTransaction();

            //If the module already exists, do not register it, else register it
            if ($this->objModules->checkIfRegistered($moduleId) && !$this->update) {
                $this->_lastError = 1002;
            	return FALSE;
            } else {
                // check for modules this one is dependant on
                if (isset($registerdata['DEPENDS'])) {
                    foreach ($registerdata['DEPENDS'] as $depends) {
                        if (!$this->checkDependency($depends)) {
                            $text=$this->objLanguage->languageText('mod_modulecatalogue_needmodule','modulecatalogue');
                            $text=str_replace('{MODULE}',$depends,$text);
                            $this->output.='<b>'.$text.'</b><br />';
                            $this->_lastError = 1003;
                            return FALSE;
                        }
                    }
                }
                // Now we add the tables
                if (isset($registerdata['TABLE'])) {
                    foreach ($registerdata['TABLE'] as $table) {
                        if (!$this->makeTable($table)) {
                            $text=$this->objLanguage->languageText('mod_modulecatalogue_needinfo','modulecatalogue');
                            $text=str_replace('{MODULE}',$table,$text);
                            $this->output.='<b>'.$text.'</b><br />';
                            $this->_lastError = 1004;
                            return FALSE;
                        } else {
                            // Delete the table from the records.
                            $sqlcheck = $this->objModule->getAll();
                            if($this->objModules->count($sqlcheck['kng_module'],$moduleId) > 0)
                            {
                            	$sql="DELETE FROM tbl_modules_owned_tables WHERE kng_module='".$moduleId."' and tablename='".$table."'";
                                $this->objModules->query($sql);
                            }
                            // Add the table to the records.
                            $this->objModules->insert(array('kng_module' => $moduleId,'tablename' => $table),'tbl_modules_owned_tables');
                        }
                    }
                }
                // Here we load data into tables from files of SQL statements
                $this->loadData($moduleId);
                }
                // Create directory
                if(isset($registerdata['DIRECTORY'])){
                    foreach ($registerdata['DIRECTORY'] as $directory) {
                        $path =
                            $this->objConfig->getcontentBasePath()
                            .'/'.$directory
                            .'/';
                        if (!is_dir($path)) {
                            mkdir($path, 0777);
                        }
                    }
                }
                // Set up data for the site navigation: toolbar, sidemenus and pages
                $isAdmin = 0;
                $isContext = 0;
                $aclList = '';
                $permList = array();
                $groupArray = array();
                $groupArray2 = array();
                if(isset($registerdata['MODULE_ISADMIN'])){
                    $isAdmin = $registerdata['MODULE_ISADMIN'];
                }
                if(isset($registerdata['DEPENDS_CONTEXT'])){
                    $isContext = $registerdata['DEPENDS_CONTEXT'];
                }
                /*
                Set up permissions for the module.
                Set up a module specific ACL, set up module specific groups and add
                them to the acl.
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
                            } else {
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

                // Create a condition type
                if(isset($registerdata['CONDITION_TYPE'][0])){
                    $objType =& $this->getObject('conditiontype','decisiontable');
                    foreach($registerdata['CONDITION_TYPE'] as $val){
                        $array = explode('|', $val);
                        $class = $array[0];
                        if(isset($array[1])){
                            $types = explode(',', $array[1]);
                            foreach($types as $type){
                                $objType->create($type, $class, $moduleId);
                                $objType->insert();
                            }
                        }
                    }
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

                // Site Navigation

                // Menu category

                if (isset($registerdata['MENU_CATEGORY'])) {
                    foreach ($registerdata['MENU_CATEGORY'] as $menu_category) {
                        $menu_category=strtolower($menu_category);
                        $catArray = array('id'=>'init@'.time().rand(1000,9999),'category'=>$menu_category,'module'=>$moduleId,
                        		'adminonly'=>$isAdmin,'permissions'=>$aclList,'dependscontext'=>$isContext);
                        $this->objModules->insert($catArray,'tbl_menu_category');
                    }
                }// end menu category

                // Side menus
                if (isset($registerdata['SIDEMENU'])) {
                    $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
                    foreach ($registerdata['SIDEMENU'] as $sidemenu) {
                        $admin = $isAdmin;
                        $groupList = '';
                        $sidemenu=strtolower($sidemenu);
                        $actions = explode('|', $sidemenu);
                        if(isset($actions[1]) && !empty($actions[1])){
                            $sidemenu = str_replace($actions[1],'',$sidemenu);
                            $conGroups = '';
                            $siteGroups = '';
                            $acls = '';
                            $access = explode(',',$actions[1]);
                            $admin = 0;
                            foreach($access as $val){
                                // check for context groups
                                if(!(strpos($val, 'con_') === FALSE)){
                                    if(!empty($conGroups)){
                                        $conGroups .= ',';
                                    }
                                    $conGroups .= ucwords(str_replace('con_','',$val));
                                }
                                // check for module permissions, create if don't exist
                                else if(!(strpos($val, 'acl_') === FALSE)){
                                    $perm = str_replace('acl_','',$val);
                                    $permId = $objPerm->getId($moduleId.'_'.$perm);
                                    if(empty($permId)){
                                        $permId = $objPerm->newAcl($moduleId.'_'.$perm, $moduleId
                                        .' '.$perm);
                                    }
                                    if(!empty($acls)){
                                        $acls .= ',';
                                    }
                                    $acls .= $permId;

                                }
                                // check for module groups, create if don't exist
                                else{
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
                        }
                        else {
                            $groupList = $aclList;
                        }
                        $catArray = array('id'=>'init@'.time().rand(1000,9999),'category'=>'menu_'.$line,'module'=>$moduleId,
                        		'adminonly'=>$admin,'permissions'=>$groupList,'dependscontext'=>$isContext);
                        $this->objModules->insert($catArray,'tbl_menu_category');
                    }
                }// end side menu

                // admin and lecturer pages
                if(isset($registerdata['PAGE'][0])){
                    foreach($registerdata['PAGE'] as $line){
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
                        $catArray = array('id'=>'init@'.time().rand(1000,9999),'category'=>'page_'.$line,'module'=>$moduleId,
                        		'adminonly'=>$admin,'permissions'=>$aclList,'dependscontext'=>$isContext);
                        $this->objModules->insert($catArray,'tbl_menu_category');
                    }
                }// end pages

                // end Site Navigation

                // Here we pass CONFIG data to the sysconfig module
                if (isset($registerdata['CONFIG']))
                {
                    $this->objSysConfig=&$this->getObject('dbsysconfig','sysconfig');
                    //$this->objSysConfig->registerModuleParams($moduleId,$registerdata['CONFIG']);
                }

                // Icons
                if (isset($registerdata['ICON'][0]))
                {
                    $this->moveIcons($moduleId,$registerdata['ICON']);
                }

                // Now the main data entry - building up arrays of the essential params
                $sql_arr = array(
                    'module_id' => $moduleId
                    ,'module_authors' => addslashes($registerdata['MODULE_AUTHORS'])
                    ,'module_releasedate' => $registerdata['MODULE_RELEASEDATE']
                    ,'module_version' => $registerdata['MODULE_VERSION']
                    ,'module_path' => $registerdata['MODULE_PATH']
                    ,'isadmin' => $registerdata['MODULE_ISADMIN']
                    ,'isvisible' => $registerdata['MODULE_ISVISIBLE']
                    ,'hasadminpage' => $registerdata['MODULE_HASADMINPAGE']
                );
                if (isset($registerdata['CONTEXT_AWARE'])){
                    $sql_arr['iscontextaware']=$registerdata['CONTEXT_AWARE'];
                }
                if (isset($registerdata['DEPENDS_CONTEXT'])){
                    $sql_arr['dependscontext']=$registerdata['DEPENDS_CONTEXT'];
                }
                if (!$this->objModules->insert($sql_arr,'tbl_modules')) {
                	$this->_lastError = 1005;
                	return FALSE;
                }
                if ($this->update) {
                    $this->objModules->update('module_id',$moduleId,$sql_arr,'tbl_modules');
                }
                //indicate success
                //put the language information for name and description
                $this->registerModuleLanguageElements();
                // insert the list of language codes used by the module if any
                $texts=$this->listTexts($registerdata); // get list of all specified texts
                if ($texts !== false) {
                    foreach ($texts as $key=>$value)
                    {
                        $test=$this->checkText($key);
                        if ($test['flag']!=11)
                        {
                            $this->addText($key,$value['desc'],$value['content']);
                        }
                    }
                }
                $texts=$this->listTexts($registerdata,'USES');
                if ($texts !== false)
                {
                    foreach ($texts as $key=>$value)
                    {
                        $test=$this->checkText($key);
                        if ($test['flag']!=11)
                        {
                            $this->addText($key,$value['desc'],$value['content']);
                        }
                    }
                }
                if (isset($registerdata['DEPENDS'][0]))
                {
                    $this->registerDependentModules($moduleId,$registerdata['DEPENDS']);
                }

            //$this->objModules->commitTransaction(); //End the transaction;
        }

        catch (Exception $e) {
        	//$this->objModules->rollbackTransaction();
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
        return TRUE;
    }

     /**
    * This is a method to uninstall a module.
    * This method should check for modules that depend on the current module
    * and refuse to uninstall where there are dependencies. Instead of uninstalling
    * a module that has dependencies, it should give the option to remove the user
    * interface files and set the module isVisible flag to 0
    * @param string $moduleId the id of the module
    * @param string $registerdata - array of info from the registration file
    * @returns boolean TRUE or FALSE
    */
    public function uninstallModule($moduleId,&$registerdata)
    {
    	try {
    		if (is_null($moduleId)) {
    			$moduleId=$registerdata['MODULE_ID'];
    		}
    		$modTitle="mod_{$moduleId}_name";
    		$modDescription="mod_{$moduleId}_desc";
    		//Check if there are modules that depend on this one
    		$dependantModules=$this->objModules->getDependencies($moduleId);
    		if (!empty($dependantModules)) {
    			$str="<b>".$this->objLanguage->languageText('mod_modulecatalogue_hasdependants','modulecatalogue')."</b><br/>";
    			foreach ($dependantModules as $dependantModule) {
    				$str.=$dependantModule."<br />";
    			}
    			$this->output = $str;
    			$this->_lastError = 1003;
    			return FALSE;
    		} else {
    			//$this->objModules->beginTransaction(); //Start a transaction;
    			$this->objModules->delete('id',$modTitle,'tbl_en');
    			$this->objModules->delete('code',$modTitle,'tbl_languagetext');
    			$this->objModules->delete('id',$modDescription,'tbl_en');
    			$this->objModules->delete('code',$modDescription,'tbl_languagetext');

    			$texts=$this->listTexts($registerdata); // remove all specified texts
    			if ($texts!==FALSE) {
    				foreach ($texts as $key=>$value) {
    					$this->removeText($key);
    				}
    			}

    			// Remove groups and acls for the module
    			if(isset($registerdata['ACL'][0])){
    				$objPerms = $this->getObject('permissions_model','permissions');
    				$objGroups = $this->getObject('groupadminmodel','groupadmin');
    				foreach($registerdata['ACL'] as $perm){
    					$perms = explode('|', $perm);
    					$aclId = $objPerms->getId($moduleId.'_'.$perms[0]);
    					$objPerms->deleteAcl($aclId);
    					if(isset($perms[1]) && !empty($perms[1])){
    						$groups = explode(',', $perms[1]);
    						foreach($groups as $group){
    							$groupId = $objGroups->getId($moduleId.'_'.$group);
    							$objGroups->deleteGroup($groupId);
    						}
    					}
    				}
    			}

    			// Remove decisiontable rules and actions
    			$objDecisionTable =& $this->getObject('decisiontable','decisiontable');
    			$objDecisionTable->create($moduleId);
    			$objDecisionTable->retrieve();
    			$objDecisionTable->delete();

    			// Remove module specific conditions
    			if(isset($registerdata['CONDITION'])){
    				$objCond =& $this->getObject('condition','decisiontable');
    				foreach($registerdata['CONDITION'] as $condition){
    					$array = explode('|', $condition);
    					$name = $array[0];
    					if(isset($array[2]) && !empty($array[2])){
    						$params = $array[1].'|'.$array[2];
    					} else {
    						$params = $array[1];
    					}
    					$conditions[$name] = $objCond->create($name, $params);
    					$conditions[$name]->retrieveId();
    					$conditions[$name]->delete();
    				}
    			}

    			// Remove navigation links
    			$this->objModules->delete('module',$moduleId,'tbl_menu_category');

    			// Here we remove CONFIG data from the sysconfig module
    			$this->objSysConfig=&$this->getObject('dbsysconfig','sysconfig');
    			$this->objSysConfig->deleteModuleValues($moduleId);

    			// Drop tables
    			$droppedTables=$this->dropTables($moduleId);

    			$this->objModules->delete('kng_module',$moduleId,'tbl_modules_owned_tables');
    			$this->objModules->delete('module_id',$moduleId,'tbl_language_modules');
    			$this->objModules->delete('module_id',$moduleId,'tbl_modules_dependencies');
    			$this->objModules->delete('module_id',$moduleId,'tbl_modules');
    			//$this->objModules->commitTransaction();//End the transaction;

    			return TRUE;
    		}
    	} catch (Exception $e) {
    		//$this->objModules->rollbackTransaction();
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This method looks at the registration data and tries to create any tables specified
    * @param string $table The name of the table to be created
    * @param string $moduleId The id of the module
    * @returns boolean TRUE|FALSE
    */
    private function makeTable($table,$moduleId='NONE')
    {
        try {
        	$this->objKeyMaker=&$this->newObject('primarykey','modulecatalogue');
        	$this->objTableInfo=&$this->newObject('tableinfo','modulecatalogue');
        	if ($moduleId=='NONE'){
        		$moduleId=$this->module_id;
        	}
        	$this->objTableInfo->tablelist();
        	if ($this->objTableInfo->checktable($table))
        	{
        		return TRUE; // table already exists, don't try to create it over again!
        	}
        	$sqlfile=$this->objConfig->getsiteRootPath().'/modules/'.$moduleId.'/'.$table.'.sql';
        	if (!file_exists($sqlfile)){
        		$sqlfile=$this->objConfig->getsiteRootPath().'/modules/'.$moduleId.'/sql/'.$table.'.sql';
        	}
        	if (!file_exists($sqlfile)){
        		throw new Exception($sqlfile.' '.$this->objLanguage->languageText('mod_modulecatalogue_sqlnotfound','modulecatalogue'));
        	}
        	include($sqlfile);
        	$this->createTable($tablename,$fields,$options);
        	if (is_array($indexes)) {
        		$this->createTableIndex($tablename,$name,$indexes);
        	}
        	return TRUE;
        } catch (Exception $e) {
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to read data from a file and use it to populate (not create) a table.
    * @param string $moduleId the id of the module to be used
    * @return boolean TRUE or FALSE
    */
    public function loadData($moduleId) {
    	try {
    		$this->objLanguage = $this->getObject('language','language');
    		if ($moduleId==null){
    			$moduleId=$this->module_id;
    		}
    		$sqlfile=$this->objConfig->getsiteRootPath().'/modules/'.$moduleId.'/sql/defaultdata.xml';
    		if (!file_exists($sqlfile)){
    			$sqlfile=$this->objConfig->getsiteRootPath().'/modules/'.$moduleId.'/defaultdata.xml';
    			if (!file_exists($sqlfile)){
    				$this->_lastError = 1006;
    				return FALSE;
    			}
    		}
    		ini_set('max_execution_time','120');
    		if (!$objXml = simplexml_load_file($sqlfile)) {
    			throw new Exception($this->objLanguage->languageText('mod_modulecatalogue_badxml').' '.$sqlfile);
    		}
    		foreach ($objXml as $table=>$dummy) {
    			$sqlArray = array();
    			foreach ($dummy as $field=>$value) {
    				$sqlArray[$field]= $value;
    			}
    			$this->objModules->insert($sqlArray,$table);
    		}
    		return TRUE;
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

	/**
    * This is a method to move icons when registering
    * @param string $moduleId the module
    * @param array $icons the list of icons
    */
    private function moveIcons($moduleId,$icons) {
        try {
        	$srcdir=$this->objConfig->siteRootPath().'/modules/'.$moduleId.'/icons/';
        	$destdir=$this->objConfig->siteRootPath().'skins/'.$this->objConfig->defaultSkin().'/icons/';
        	foreach ($icons as $icon)
        	{
        		copy($srcdir.$icon,$destdir.$icon);
        	}
        } catch (Exception $e) {
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to put the module information into the language table
    * It first inserts the name of the module and then inserts the
    * description of the module into the English column
    * @todo This must be moved into the language class
    */
    private function registerModuleLanguageElements() {
    	try {
    		$modTitle="mod_".$this->module_id."_name";
    		$modDescription="mod_".$this->module_id."_desc";
    		$this->objModules->delete('id',$modTitle,'tbl_en');
    		$this->objModules->delete('id',$modDescription,'tbl_en');
    		$this->objModules->delete('code',$modTitle,'tbl_languagetext');
    		$this->objModules->delete('code',$modDescription,'tbl_languagetext');
    		$userId = $this->objUser->userId();
    		if($userId == '')
    		{
    			$userId = 0;
    		}
    		$time = $this->objModules->now();
    		$titleArray = array('id'=>$modTitle,'en'=>addslashes($this->module_name),'pageid'=>addslashes($this->module_id),'isinnextgen'=>true,
    				'datecreated'=>$time,'creatoruserid'=>$userId,'datelastmodified'=>$time,'modifiedbyuserid'=>$userId);
    		$descArray = array('id'=>$modDescription,'en'=>addslashes($this->module_description),'pageid'=>addslashes($this->module_id),'isinnextgen'=>true,
    				'datecreated'=>$time,'creatoruserid'=>$userId,'datelastmodified'=>$time,'modifiedbyuserid'=>$userId);
    		$this->objModules->insert($titleArray,'tbl_en');
    		$this->objModules->insert($descArray,'tbl_en');
    		$this->objModules->insert(array('code'=>$modTitle,'description'=>$this->module_name),'tbl_languagetext');
    		$this->objModules->insert(array('code'=>$modDescription,'description'=>$this->module_description),'tbl_languagetext');
    	} catch (customException $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to add language terms to the database
    * @param string $terms A comma delimited string of
    * terms that are used in the language database
    */
    private function registerModuleLanguageTerms($terms) {
        try {
        	$terms_arr=explode(',', $terms);
        	//$this->objModules->beginTransaction();
        	foreach ($terms_arr as $term) {
        		$this->objModules->insert(array('module_id$'=>$this->module_id,'code'=>$term),'tbl_language_modules');
        	}
        	//$this->objModules->commitTransaction();
        } catch (Exception $e) {
        	//$this->objModules->rollbackTransaction();
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * Registers modules that this module depends on
    * @param string $moduleId The module ID
    * @param $modulesNeeded array The modules this module depends on
    */
    private function registerDependentModules($moduleId,$modulesNeeded) {
        try {
        	//$this->objModules->beginTransaction();
        	foreach ($modulesNeeded as $moduleNeeded) {
        		$this->objModules->insert(array('module_id'=>$moduleId,'dependency'=>$moduleNeeded),'tbl_modules_dependencies');
        	}
        	//$this->objModules->commitTransaction();
        } catch (Exception $e) {
        	//$this->objModules->rollbackTransaction();
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to drop tables for the current module. This method
    * gets the list of owned tables from tbl_modules_owned_tables
    * and removes them one at a time
    * @param string $moduleId
    * @returns array $droppedTables list of the dropped tables
    */
    private function dropTables($moduleId)
    {
        try {
        	$sql = "SELECT tablename FROM tbl_modules_owned_tables WHERE kng_module='$moduleId'";
        	$rs = $this->objModules->getArray( $sql );
        	$rs_reversed=array_reverse($rs, TRUE);
        	$droppedTables=array();
        	foreach ($rs_reversed as $rec)
        	{
        		$table=$rec['tablename'];
        		$droppedTables[]=$table;
        		$this->dropTable($table);
        	}
        	return $droppedTables;
        } catch (Exception $e) {
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to check for specified text entries from both tbl_languagetext and tbl_english
    * @param $code
    * @returns array with elements flag = 0, 1, 10, or 11, content and desc
    */
    public function checkText($code) {
        try {
        	$flag['flag']=0;
        	$sql="SELECT * FROM tbl_en WHERE id='".$code."'";
        	$arr=$this->objModules->getArray($sql);
        	$flag1=0;
        	$content='';
        	foreach($arr as $el) {
        		$flag1=1;
        		$content=$el['en'];
        	}
        	$sql="SELECT * FROM tbl_languagetext WHERE code='".$code."'";
        	$arr=$this->objModules->getArray($sql);
        	$flag2=0;
        	$description='';
        	foreach($arr as $el) {
        		$flag2=10;
        		$description=$el['description'];
        	}
        	$flag['flag']=$flag1+$flag2;
        	$flag['content']=$content;
        	$flag['desc']=$description;
        	return $flag;
        } catch (Exception $e) {
        	$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
        }
    }

    /**
    * This is a method to build an array based on another one.
    * @param array $rdata
    * @param string $index type of text to be added
    * @returns FALSE or array $texts
    */
    public function listTexts($rdata,$index='TEXT') {
    	try {
    		$texts=array();
    		if (is_array($rdata) && array_key_exists($index,$rdata) && is_array($rdata[$index])) {
    			foreach ($rdata[$index] as $line) {
    				list($code,$description,$content) = explode('|',$line);
    				if ($content){
    					$texts[$code]['content']=$content;
    					$texts[$code]['desc']=$description;
    				} else {
    					$module=$rdata['MODULE_ID'];
    					$errorText = $this->objLanguage->languageText('mod_modulecatalogue_textproblem','modulecatalgoue');
    					$errorText = str_replace("{MODULE}",$module,$errorText);
    					$errorText = str_replace("{CODE}",$code,$errorText);
    					$this->errorText .= $errorText;
    				}
    			}
    			return $texts;
    		} else {
    			return FALSE;
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This is a method to add specified text entries from both tbl_languagetext and tbl_english
    * @author James Scoble
    * @param $code,$description,$content
    */
    private function addText($code,$description,$content,$modname = null) {
    	try {
    		if ($modname == null) {
    			$modname = $this->module_id;
    		}
    		if ($modname == null) {
    			throw new customException("Null value for module name in addText for item $code|$description|$content");
    		}
    		//$this->objModules->beginTransaction();
    		$this->removeText($code);
    		$code=addslashes($code);
    		$description=addslashes($description);
    		$content=addslashes($content);
    		$this->objModules->insert(array('code'=>$code,'description'=>$description),'tbl_languagetext');
    		$uid = $this->objUser->userId();
    		$now = $this->objModules->now();
    		$enArray = array('id'=>$code,'en'=>$content,'pageId'=>$modname,'isInNextgen'=>true,
    				'dateCreated'=>$now,'creatorUserId'=>$uid,'dateLastModified'=>$now,'modifiedByUserId'=>$uid);
    		$this->objModules->insert($enArray,'tbl_en');
    		//$this->objModules->commitTransaction();
    	} catch (Exception $e) {
    		//$this->rollbackTransaction();
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This is a method to remove specified text entries from both tbl_languagetext and tbl_english
    * @param $code
    */
    private function removeText($code) {
    	try {
    		$code=addslashes($code);
    		//$this->objModules->beginTransaction();
    		$this->objModules->delete('id',$code,'tbl_en');
    		$this->objModules->delete('code',$code,'tbl_languagetext');
    		//$this->objModules->commitTransaction();
    	} catch (Exception $e) {
    		//$this->rollbackTransaction();
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

     /**
    * This is a method to look through list of texts specified for module,
    * and see if they are registered or not.
    * @author James Scoble
    * @param string $modname
    * @param string $action - optional, if its 'fix' then the function tries
    * to add any texts that are missing.
    * returns array $mtexts
    */
    public function moduleText($modname,$action='readonly') {
    	try {
    		$mtexts = array();
    		$filepath = $this->objModFile->findRegisterFile($modname);
    		$rdata = $this->objModFile->readRegisterFile($filepath,FALSE);
    		$texts = $this->listTexts($rdata,'TEXT');
    		$uses = $this->listTexts($rdata,'USES');
    		if ($uses) {
    			$text = array_merge($texts,$uses);
    		} else {
    			$text = $texts;
    		}
    		//$this->objModule->beginTransaction(); //Start a transaction;
    		if (is_array($text)) {
    			foreach ($text as $code=>$data) {
    				$isreg=$this->checkText($code); // this gets an array with 3 elements - flag, content, and desc
    				$text_desc=$data['desc'];
    				$text_val=$data['content'];
    				if (($action=='fix')&&($isreg['flag']==0)) {
    					$this->addText($code,$text_desc,$text_val,$modname);
    				}
    				if ($action=='replace') {
    					$this->addText($code,$text_desc,$text_val,$modname);
    				}
    				$mtexts[]=array('code'=>$code,'desc'=>$text_desc,'content'=>$text_val,'isreg'=>$isreg,'type'=>'TEXT');
    			}
    		}
    		//$this->objModule->commitTransaction(); //End the transaction;
    		return $mtexts;
    	} catch (Exception $e) {
    		//$this->objModule->rollbackTransaction();
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This is a method to update the text elements in all registered modules at once
    */
    public function updateAllText() {
    	try {
    		$modulesArray = $this->objModules->getAll();
    		foreach ($modulesArray as $module) {
    			$this->moduleText($line['module_id'],'replace');
    		}
    		return TRUE;
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
     * Method to get the last error
     *
     * @return string description of last error
     */
    public function getLastError() {
    	try {
    		switch ($this->_lastError) {
    			case 1001:
    				return 'cannot find moduleid in register.conf';
    			case 1002:
    				return 'module already registered';
    			case 1003:
    				return 'module dependency check failed';
    			case 1004:
    				return 'could not get info to create table';
    			case 1005:
    				return 'could not write to table tbl_modules';
    			case 1006:
    				return 'could not read table sql file';
    			default:
    				return 'uknown error: what did you do?';
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
     * The error callback function, defers to configured error handler
     *
     * @param string $exception
     * @return void
     */
    public function errorCallback($exception) {
    	echo customException::cleanUp($exception);
    }
}
?>