<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for manipulating modules on the filesystem
*
* @author Nic Appleby
* @category Chisimba
* @package Modulecatalogue
* @copyright AVOIR
* @license GNU/GPL
* @version $Id$
*/

class modulefile extends object {

	/**
	 * Configuration object
	 *
	 * @var object $config
	 */
	protected $config;

	/**
	 * Standard init function
	 *
	 */
	public function init() {
		$this->config = &$this->getObject('altconfig','config');
	}

	/**
	 * Method to get a list of all modules currently on the system
	 *
	 * @return array a list of the modules
	 */
	public function getLocalModuleList() {
        try {
        	$lookdir=$this->config->getModulePath();
        	$optionalmodlist = (array)$this->checkdir($lookdir) ;
        	$coremodlist = (array)$this->checkdir($this->config->getSiteRootPath().'/core_modules/');
        	$modlist = array_merge((array)$coremodlist,(array)$optionalmodlist);
        	natsort($modlist);
        	$modulelist = array();
        	foreach ($modlist as $line) {
            	switch ($line) {
            		case '.':
            		case '..':
            		case 'CVS':
            		case 'CVSROOT':
            			break; // don't bother with system-related dirs
            		default:
            			if (is_dir("$lookdir/$line")||is_dir($this->config->getSiteRootPath()."/core_modules/$line")) {
            				$modulelist[] = $line;
            			}
            	}
        	}
        	return $modulelist;
		} catch (Exception $e) {
			$this->config->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
	}

	/**
	 * This method checks all local modules for hte MODULE_CATEGORY tag in the register.conf
	 * and builds an array of all the local categories
	 *
	 * @return array
	 */
	public function getCategories() {
    	try {
    		$lookdir=$this->config->getModulePath();
    		$mod_list = $this->checkdir($lookdir);
    		$core_list = $this->checkdir($this->config->getSiteRootPath().'/core_modules/');
    		$modlist = array_merge((array)$core_list,(array)$mod_list);
    		$categorylist = array();
    		foreach ($modlist as $line) {
    			switch ($line) {
    				case '.':
    				case '..':
    				case 'CVS':
    				case 'CVSROOT':
    					break; // don't bother with system-related dirs
    				default:
    					$cat = $this->moduleCategory($line);
    					if ($cat) {
    						foreach ($cat as $category) {
    							($categorylist[] = strtolower($category));
    						}
    					}
    					break;
    			}
    		}
    		$categorylist = array_unique($categorylist);
    		return $categorylist;
		} catch (Exception $e) {
			$this->config->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    /**
     * Function to extract the module catalogue from the register file
     *
     * @param string $module the name of the module to check
     * @return string|false the category name if it exists or false
     */
    public function moduleCategory($module) {
    	try {
    		if (($fn = $this->findregisterfile($module)) && (filesize($fn)>0)) {
    			$fh = fopen($fn,"r");
    			$content = fread($fh,filesize($fn));
    			fclose($fh);
    			$cat = array();
    			while (preg_match('/MODULE_CATEGORY:\s*([a-z\-_]*)/i',$content,$match)) {
    				$cat[] = $match[1];
    				$content = str_replace($match[0],'__',$content);
    			}
    			return $cat;
    		} else {
    			return false;
    		}
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    public function contextAware($moduleId) {
    	try {
    		if (($fn = $this->findregisterfile($moduleId)) && (filesize($fn)>0)) {
    			$fh = fopen($fn,"r");
    			$content = fread($fh,filesize($fn));
    			fclose($fh);
    			if (preg_match('/CONTEXT_AWARE:\s*([a-z0-9\-_]*)/i',$content,$match)) {
    				if ($match[1] == '1') {
    					return TRUE;
    				}
    			}
    		}
    		return FALSE;
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    public function contextPlugin($moduleId) {
    	try {
    		if (($fn = $this->findregisterfile($moduleId)) && (filesize($fn)>0)) {
    			$fh = fopen($fn,"r");
    			$content = fread($fh,filesize($fn));
    			fclose($fh);
    			if (preg_match('/ISCONTEXTPLUGIN:\s*([a-z0-9\-_]*)/i',$content,$match)) {
    				if ($match[1] == '1') {
    					return TRUE;
    				}
    			}
    		}
    		return FALSE;
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    public function getContextAwareModules() {
    	$moduleList = $this->getLocalModuleList();
    	$contextAwareModules = array();
    	foreach ($moduleList as $module) {
    		if ($this->contextAware($module)) {
    			$contextAwareModules[] = $module;
    		}
    	}
    	return $contextAwareModules;
    }

    /**
    * This method takes one parameter, which it treats as a directory name.
    * It returns an array - listing the files in the specified dir.
    * @author James Scoble
    * @param string $file - directory/folder
    * @return array $list
    */
    protected function checkdir($file)
    {
        try {
        	if (!file_exists($file)) {
        		return FALSE;
        	}
        	$dirObj = dir($file);
        	while (false !== ($entry = $dirObj->read()))
        	{
        		$list[]=$entry;
        	}
        	$dirObj->close();
        	return $list;
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

     /**
    * Boolean test for the existance of file $fname, in directory $where
    * @author James Scoble
    * @param string $where file path
    * @param string $fname file name
    * @return boolean TRUE or FALSE
    */
    function checkForFile($where,$fname)
    {
        try {
        	if (file_exists("$where/$fname"))
        	{
        		return TRUE;
        	} else {
        		return FALSE;
        	}
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    /** This is a method to check for existance of registration file
    * @author James Scoble
    * @param string modname
    * @returns FALSE on error, string filepatch on success
    */
    function findregisterfile($modname)
    {
        try {
        	$endings=array('php','conf');
                $modulePath=$this->config->getModulePath(); // we need only call this once
        	if ((strlen($modulePath)>1) && file_exists($modulePath."/$modname")) { // added chech for undefined path
        		$path = $modulePath."/$modname/register.";
        	} else {
        		$path = $this->config->getSiteRootPath().'core_modules/'.$modname."/register.";
        	}
        	foreach ($endings as $line)
        	{
        		if (file_exists($path.$line))
        		{
        			return $path.$line;
        		}
        	}
        	return FALSE;
		} catch (Exception $e) {
		        	throw new customException($e->getMessage());

//			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    /** This is a method to check for existance of controller file
    * @param string modname
    * @return FALSE on error, string filepatch on success
    */
    function findController($modname)
    {
        try {
                $modulePath=$this->config->getModulePath(); // we need only call this once
        	if ((strlen($modulePath)>1) && file_exists($modulePath."/$modname")) { // added chech for undefined path
        		$path = $modulePath."/$modname/controller.php";
        	} else {
        		$path = $this->config->getSiteRootPath()."core_modules/$modname/controller.php";
        	}
        	if (file_exists($path)) {
        			return $path;
        	} else {
        		return FALSE;
        	}
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

     /** This is a method to check for existance of sql_updates.xml
    * @param string modname
    * @return FALSE on error, string filepath on success
    */
    public function findSqlXML($modname) {
        try {
                $modulePath=$this->config->getModulePath(); // we need only call this once
        	if ((strlen($modulePath)>1) && file_exists($modulePath."/$modname")) { // added chech for undefined path
        		$path = $modulePath()."/$modname/sql/sql_updates.xml";
        	} else {
        		$path = $this->config->getSiteRootPath().'core_modules/'.$modname."/sql/sql_updates.xml";
        	}
        	if (file_exists($path)) {
        			return $path;
        	} else {
        		return FALSE;
        	}
		} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

 	/** This is a method to check for existance of table definition sql file
    * @param string modname
    * @return FALSE on error, string filepath on success
    */
    public function findSqlFile($modname,$tablename) {
    	try {
                $modulePath=$this->config->getModulePath(); // we need only call this once
        	if ((strlen($modulePath)>1) && file_exists($modulePath."/$modname")) { // added chech for undefined path
                    $path = $modulePath()."/$modname/sql/$tablename.sql";
        	} else {
                    $path = $this->config->getSiteRootPath().'core_modules/'.$modname."/sql/$tablename.sql";
        	}
        	if (file_exists($path)) {
        			return $path;
        	} else {
        		return FALSE;
        	}
    	} catch (Exception $e) {
			$this->config->errorCallback('Caught Exception: '.$e->getMessage());
        	exit();
		}
    }

    /**
    * Reads the 'register.conf' file provided by the module to be registered
    * and uses file() to load the contents into an array, then read through it
    * line by line, looking for keywords.
    * These are then returned as an associative array.
    * @author James Scoble
    * @param string $filepath  path and filename of file.
    * @param boolean $useDefine determine use of defined constants
    * @return array $registerdata all the info from the register.conf file
    */
	public function readRegisterFile($filepath,$useDefine=FALSE) {
		try {
			if (file_exists($filepath)) {
				$registerdata=array();
				$lines=file($filepath);
				$cats = array();
				foreach ($lines as $line) {
					$params=explode(':',$line);
					$len = count($params);
					for ($i=0; $i<$len; $i++) {
						$params[$i] = trim($params[$i]);
					}
					switch ($params[0]) {
						case 'MODULE_ID':
						case 'MODULE_NAME':
						case 'MODULE_DESCRIPTION':
						case 'MODULE_AUTHORS':
						case 'MODULE_RELEASEDATE':
						case 'MODULE_VERSION':
						case 'MODULE_PATH':
						case 'MODULE_ISADMIN':
						case 'MODULE_ISVISIBLE':
						case 'MODULE_HASADMINPAGE':
						case 'MODULE_LANGTERMS':
						case 'CONTEXT_AWARE':
						case 'DEPENDS_CONTEXT':
							$registerdata[$params[0]]=rtrim($params[1]);
							break;
						case 'ICON': 				//images for each module
						case 'NEWPAGE': 			//Add a new page
						case 'NEWPAGECATEGORY': 	//Add a new page category
						case 'NEWSIDEMENU': 		//Add a new sidemenu
						case 'NEWTOOLBARCATEGORY': 	//Add a new toolbar category
						case 'MENU_CATEGORY': 		//when the menu should display a link to this
						case 'SIDEMENU': 			//the side menus in the content page
						case 'PAGE': 				//lecturer or admin page links
						case 'SYSTEM_TYPE': 		//system type for text abstraction _Kevin Cyster
						case 'SYSTEM_TEXT': 		//text items for text abstraction _Kevin Cyster
						case 'ACL': 				//access permissions for the module
						case 'USE_GROUPS': 			//access groups for the module
						case 'USE_CONTEXT_GROUPS': 	//access groups for a context dependent module
						case 'USE_CONDITION': 		//use an existing security condition
						case 'CONDITION': 			//create a security condition
						case 'CONDITION_TYPE': 		//Create a condition type
						case 'RULE': 				//Create a rule linking conditions and actions
						case 'DIRECTORY': 			//Create a directory in content folder
						case 'SUBDIRECTORY': 		//Create a subdirectory in above directory
						case 'TABLE': 				//Names of SQL tables
						case 'DEPENDS': 			//modules this module needs
						case 'CLASSES':
						case 'WARNING'; 			//Warning tag for modules with special requirements or functions
						case 'MODULE_CATEGORY':
						case 'BLOCK':				//module owned blocks
						case 'WIDEBLOCK':			//wide blocks
						case 'SOAP_CONTROLLER': 	//Boolean flag for SOAP controller
							$registerdata[$params[0]][]=rtrim($params[1]);
						break;
						case 'CONFIG': 				//configuration params
							$confArray=explode('|',$params[1]);
							$pdesc = (isset($confArray[2]))? trim($confArray[2]) : '';
							$pvalue = (isset($confArray[1]))? trim($confArray[1]) : '';
							$registerdata[$params[0]][]=array('pname'=>trim($confArray[0]),'pvalue'=>$pvalue,'pdesc'=>$pdesc);
							break;
						case 'TEXT': 				//Languagetext items
						$registerdata['TEXT'][]=$params[1]; // Need to think this one out some more.
						break;
						case 'USES':
						case 'USESTEXT': 			//Languagetext items not loaded but used.
						$registerdata['USES'][]=$params[1];
						break;
						default:
					} //  end of switch()
				} //    end of foreach
				return ($registerdata);
			} else {
				return FALSE;
			} // end of if
		} catch (Exception $e) {
			throw new customException($e->getMessage());
			exit(0);
		}
	}
}
?>
