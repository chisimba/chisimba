<?php

require_once 'MDB2.php';




/**

* DbHandler

*

* Handles installer db queries for installer

*

* @author Prince Mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class DbHandler

{

	/**

	* The dsn to connect to

	* @var string

	*/

	public  $dsn;

	

	/**

	* The database connection to query over.

	* @var object

	*/

	private $schema;

	

	/**

	* Errors this class has had.

	* @var array

	*/

	public  $errors = Array();

	

	/**

	* Whether this handler has done its action successfully or not

	* @var boolean

	*/

	public $success = false;

	

	/**

	* Version of the db needed for 5ive

	* @var string

	*/

	public $minVersion;

	
 	private  $objTableInfo; //handle for tableinfo class object
    private  $objKeyMaker; //handle for primary key generation
    private  $MODULE_ID; // replaces use of defined constant.
    private  $MODULE_NAME;
    private  $MODULE_DESCRIPTION;
    private  $update=FALSE;

    public  $output=''; // for any feedback messages from internal functions.
    public  $errorText=''; // for any feedback messages from internal functions.
	

	/**

	* Get a db handler class for the given dsn

	* 

	* @param string $dsn The dsn to connect to.

	* @return object the db handler for the given database

	* @access public

	*/

	public function &getDbHandler($dsn)

	{
		try{
			$this->schema =&MDB2::factory($dsn, array('debug' => 2, 'log_line_break' => '<br>'));
			$this->schema->setOption('portability', MDB2_PORTABILITY_FIX_CASE);
			MDB2::loadFile('Date');
			MDB2::loadFile('Iterator');
			
	   		if (PEAR::isError($this->schema)) {
	           	throw new Exception();
	        	
	   		}
	   		$this->schema->disconnect();
	   		$handler->success =true;	
			return $this;
		}catch (Exception $e){
			
			return false;
		}

	}//end getDbHandler()

	

	

	/**

	* Abstract emptyDatabase method

	* 

	* @return boolean Whether the database could be emptied or not.

	* @access public

	*/

	public function emptyDatabase($db,$dsn) {
		
		try {
			$this->schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));	
			$ret = $this->schema->db->CreateDatabase($db);
			
			if (PEAR::isError($ret)) {
	        	$ret = $this->schema->db->DropDatabase($db);
	        	        	
	        	$ret = $this->schema->db->CreateDatabase($db);
	        	
	   		}
	   		$this->schema->disconnect();
	   		$handler->success = true;
			return true;
		}catch (Exception $e){
			return false;
		}
	}

	

	

	/**

	* Get the number of tables in the database

	* 

	* @return int The number of database tables.

	* @access public

	*/

	public function getNumberOfTables()

	{

		return 0;

	}//end getNumberOfTables()

	

	

	/**

	* Check the version is allowed

	* 

	* @return string The version of the database

	* @access public

	*/

	public function checkVersion($dsn) 

	{
		try{

			$sql = 'SELECT version()';
	
			$this->schema =&MDB2::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
			
	   		if (PEAR::isError($this->schema)) {
	        	$error = $this->schema->getMessage() . ' ' . $this->schema->getUserInfo();
	        	throw new Exception();
	   		}
	
			// get a list of tables in the database
					
			$result =  $this->schema->getServerVersion(false);
			if (MDB2::isError($this->schema)) {
	
				$this->errors[] =  $this->schema->getMessage();
				 $schema->disconnect();
				$this->success = false;
				throw new Exception();
	
			} 
	
						
			$ver = $result[0];
					
	
			return version_compare($ver, $this->minVersion) >= 0;
		}catch (Exception $e){
			
			return false;
		}
		

	}//end version()

	

	

	/**

	* Set the dsn this db handler will use

	* 

	* @param string $dsn the dsn to use.

	* @return void

	* @access public

	*/

	public function setDSN($dsn) {

	try{	

		$this->dsn = $dsn;

		$schema =& MDB2_Schema::factory($dsn, array('debug' => true, 'log_line_break' => '<br>'));
   		if (PEAR::isError($schema)) {
        	$error = $schema->getMessage() . ' ' . $schema->getUserInfo();
			$this->errors[] = $$error.' when connecting to database.';

			throw new Exception("Exception caught");

		}

		$this->success = true;

		

		return true;
	}catch (Exception $e){
		
		return false;
	}

		

	}//end setDSN()

	/**
    * This is a method to execute additional SQL statements used to
    * register a module. Execute as many times as necessary
    * to execute all SQL needed to effect a new module.
    *
    * @param string $sql Any valid SQL query passed to the method.
    */
    function executeModSQL($sql)
    {
        $globalObjDb=&$this->objEngine->getDbObj();
        $globalObjDb->query($sql);
    } //end of executeModSQL() function

	/**
    * This is a method to register the module. It stores the module information in
    * the database table tbl_modules, creates any needed SQL tables,
    * adds languagetext elements, moves icons, etc. All based on info from
    * the module's 'register.conf' file.
    * @author Prince Mbekwa
    * @param array $registerdata - all the info from the register.conf file.
    * @returns boolean TRUE|FALSE
    */
    public function registerModule($registerdata)
    {
        if (defined('MODULE_ID'))
        {
            $moduleId=MODULE_ID;
        }
        elseif (isset($registerdata['MODULE_ID']))
        {
            $moduleId=$registerdata['MODULE_ID'];
        }
        else
        {
            return FALSE; // If we can't find the name of the module we're supposed to be registering, what are we doing here?
        }
        $this->MODULE_ID=$moduleId;
        $this->MODULE_NAME=$registerdata['MODULE_NAME'];
        $this->MODULE_DESCRIPTION=$registerdata['MODULE_DESCRIPTION'];

        $this->executeModSQL('BEGIN WORK'); //Start a transaction;

        //If the module already exists, do not register it, else register it
        if ($this->checkIfRegistered($this->MODULE_NAME, $moduleId) && !($this->update))
        {
            if ($this->update){ print "BOOOOM!"; die; }
            return FALSE;
        }
        else
        {
            // check for modules this one is dependant on
            if (isset($registerdata['DEPENDS'][0]))
            {
                foreach ($registerdata['DEPENDS'] as $dline)
                {
                    $test=$this->checkDependency($dline);
                    if ($test==FALSE)
                    {
                        $text=$this->objLanguage->languageText('mod_moduleadmin_needmodule');
                        $text=str_replace('{MODULE}',$dline,$text);
                        $this->output.='<b>'.$text.'</b><br />';
                            //"<b>Cannot register module - needs module $dline to be registered first!</b><br>\n";
                        return FALSE;
                    }
                }
            }
            // Now we add the tables
            if (isset($registerdata['TABLE'][0]))
            {
                $this->objTableInfo=$this->newObject('tableinfo','moduleadmin'); // create object to look at SQL tables
                $this->objKeyMaker=$this->newObject('primarykey','modulelist');
                foreach ($registerdata['TABLE'] as $dline)
                {
                    $test=$this->makeNewTable($dline);
                    if ($test==FALSE)
                    {
                        //"<b>Cannot register module - needs info to create table $dline first!</b><br>\n";
                        $text=$this->objLanguage->languageText('mod_moduleadmin_needinfo');
                        $text=str_replace('{MODULE}',$dline,$text);
                        $this->output.='<b>'.$text.'</b><br />';
                        return FALSE;
                    }
                    else
                    {
                        $sql="DELETE FROM tbl_modules_owned_tables WHERE kng_module='".$moduleId."' and tablename='".$dline."'";
                        $this->executeModSQL( $sql );
                        $sql="INSERT INTO tbl_modules_owned_tables (kng_module,tablename) VALUES ('".$moduleId."','".$dline."')";
                        $this->executeModSQL($sql); // Add the table to the records.
                    }
                }
            }

            // Here we load data into tables from files of SQL statements
            if (isset($registerdata['BIGDATA'][0]))
            {
                $this->objKeyMaker=$this->newObject('primarykey','modulelist');
                foreach ($registerdata['BIGDATA'] as $dline)
                {
                    $test=$this->loadData($dline);
                }
            }

            // Here we create a SOAP file
            //if (isset($registerdata['SOAP_CONTROLLER'][0])&&($registerdata['SOAP_CONTROLLER'][0]==1))
            //{
            //    $this->soapFileMaker($moduleId);
            //}

            // Create directory and subdirectory
            if(isset($registerdata['DIRECTORY'][0])){
                $path = $this->objConfig->contentBasePath().'/'.$registerdata['DIRECTORY'][0].'/';
                if(!is_dir($path)){
                    mkdir($path, 0777);
                }

                if(isset($registerdata['SUBDIRECTORY'][0])){
                    foreach($registerdata['SUBDIRECTORY'] as $line){
                        $subPath = $path.$line.'/';
                        if(!is_dir($subPath)){
                            mkdir($subPath, 0777);
                        }
                    }
                }
            }

            // Set up data for the site navigation: toolbar, sidemenus and pages
            $isAdmin = 0; $isContext = 0; $aclList = ''; $permList = array(); $groupArray = array();
            $groupArray2 = array();
            if(isset($registerdata['MODULE_ISADMIN'])){
                $isAdmin = $registerdata['MODULE_ISADMIN'];
            }
            if(isset($registerdata['DEPENDS_CONTEXT'])){
                $isContext = $registerdata['DEPENDS_CONTEXT'];
            }

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
            if (isset($registerdata['MENU_CATEGORY'][0]))
            {
                foreach ($registerdata['MENU_CATEGORY'] as $line)
                {
                    $line=strtolower($line);
                    $sql="INSERT INTO tbl_menu_category
                    (id,category,module,adminOnly,permissions,dependsContext)
                    values ('";
                    $sql.='init@'.time().rand(1000,9999)."','$line',";
                    $sql.="'$moduleId','$isAdmin',";
                    $sql.="'$aclList','$isContext')";
                    $this->executeModSQL($sql);
                }
            }// end menu category

            // Side menus
            if (isset($registerdata['SIDEMENU'][0]))
            {
                $objGroups = $this->getObject('groupAdminModel', 'groupadmin');
                foreach ($registerdata['SIDEMENU'] as $line)
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
                                    $permId = $objPerm->newAcl($moduleId.'_'.$perm, $moduleId
                                    .' '.$perm);
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
                    $sql="INSERT INTO tbl_menu_category (id, category, module,";
                    $sql.="adminOnly,permissions,dependsContext) values ('";
                    $sql.='init@'.time().rand(1000,9999)."','menu_$line',";
                    $sql.="'$moduleId','$admin',";
                    $sql.="'$groupList','$isContext')";
                    $this->executeModSQL($sql);
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
                    $sql = 'INSERT INTO tbl_menu_category (id, category, module,';
                    $sql .= "adminOnly, permissions, dependsContext) values ('";
                    $sql .= 'init@'.time().rand(1000,9999)."','page_$line',";
                    $sql .= "'$moduleId','$admin','$aclList','$isContext')";
                    $this->executeModSQL($sql);
                }
            }// end pages
            // end Site Navigation


            // Here we pass CONFIG data to the sysconfig module
            if (isset($registerdata['CONFIG']))
            {
                $this->objSysConfig=&$this->getObject('dbsysconfig','sysconfig');
                $this->objSysConfig->registerModuleParams($moduleId,$registerdata['CONFIG']);
            }

            // Icons
            if (isset($registerdata['ICON'][0]))
            {
                $this->moveIcons($moduleId,$registerdata['ICON']);
            }

            // Now the main data entry - building up arrays of the essential params

            $fields=array('module_id','module_authors','module_releasedate','module_version','module_path','isAdmin','isVisible','hasAdminPage');
            $values=array($moduleId,addslashes($registerdata['MODULE_AUTHORS']),$registerdata['MODULE_RELEASEDATE'],$registerdata['MODULE_VERSION'],$registerdata['MODULE_PATH'],$registerdata['MODULE_ISADMIN'],$registerdata['MODULE_ISVISIBLE'],$registerdata['MODULE_HASADMINPAGE']);
            foreach($fields as $key)
            {
                $sql_str[$key]=array_shift($values);
            }

            if (isset($registerdata['CONTEXT_AWARE'])){
                $sql_str['isContextAware']=$registerdata['CONTEXT_AWARE'];
            }
            if (isset($registerdata['DEPENDS_CONTEXT'])){
                $sql_str['dependsContext']=$registerdata['DEPENDS_CONTEXT'];
            }

            $this->localinsert($sql_str);
            if ($this->update){
                $this->localEdit($sql_str,"where module_id='$moduleId'");
            }
            //indicate success
            // put the language information for name and description
            $this->pokeLanguage();
            // insert the list of language codes used by the module if any
            if (defined('MODULE_LANGTERMS'))
            {
                $this->pokeTerms(MODULE_LANGTERMS);
            }
            $texts=$this->listTexts($registerdata); // get list of all specified texts
            if ($texts!=FALSE)
            {
                foreach ($texts as $key=>$line)
                {
                    $test=$this->checkText($key);
                    if ($test['flag']!=11)
                    {
                        $this->addText($key,$line['desc'],$line['content']);
                    }
                }
            }
            $texts=$this->listTexts($registerdata,'USES');
            if ($texts!=FALSE)
            {
                foreach ($texts as $key=>$line)
                {
                    $test=$this->checkText($key);
                    if ($test['flag']!=11)
                    {
                        $this->addText($key,$line['desc'],$line['content']);
                    }
                }
            }
            if (isset($registerdata['DEPENDS'][0]))
            {
                $this->recordDependentModules($moduleId,$registerdata['DEPENDS']);
            }
           // $sql="DELETE FROM tbl_languagetext WHERE whereUsed='".$moduleId."'";
        }


        $this->executeModSQL('COMMIT'); //End the transaction;
        return TRUE;
    } #end of registerModule() function

	/**
    * This is a method to check if the module is registered already.
    * Returns TRUE if the module is registered and FALSE if
    * it is not registered.
    * @param string $moduleName The name of the module, in
    * a form that is meaningful to users, for example
    * 'Show users who are logged in'
    * @param string $moduleId The identifier of the module,
    * in the form initial_modulecode where initial represents
    * the initials for the developer to avoid duplication.
    * @returns boolen TRUE|FALSE
    */
     function checkIfRegistered($moduleName, $moduleId=NULL)
     {
        if ($moduleId==NULL){
            $moduleId=$moduleName;
        }
        $line=$this->getRow('module_id',$moduleId);
        if ($line['module_id']==$moduleId)
         {
           $fn_ret=TRUE;
         }
         else
         {
           $fn_ret=FALSE;
         }
        return $fn_ret;
     }  #end of checkIfRegistered() function


}//end class

?>

