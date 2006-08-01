<?php
/**
 * Module administration with catalogue interface. Allows installation and Un-installation of modules
 * via a cagtalogue interface which groups similar modules. Also incorporates module patching.
 *
 * @author Nic Appleby
 * @category Chisimba
 * @package modulecatalogue
 * @version 1.0
 * @copyright GPL UWC 2006
 */

class modulecatalogue extends controller
{
	/**
	 * Object to connect to Module Catalogue table
	 *
	 * @var object $objDBModCat
	 */
	protected $objDBModCat;

	/**
	 * Object to read module information from register files
	 *
	 * @var object $objModFile
	 */
	protected $objModFile;

	/**
	 * Object to read catalogue configuration
	 *
	 * @var object $objCatalogueConfig
	 */
	protected $objCatalogueConfig;

	/**
	 * Side menu object
	 *
	 * @var object $objSideMenu
	 */
	public $objSideMenu;

	/**
	 * Logger object to log module calls
	 *
	 * @var object $objLog
	 */
	public $objLog;

	/**
	 * User object for security
	 *
	 * @var object $objUser
	 */
	public $objUser;

	/**
	 * Language object for multilingual support
	 *
	 * @var object $objLanguage
	 */
	public $objLanguage;

	/**
	 * The site configuration object
	 *
	 * @var object $config
	 */
	public $config;

	/**
	 * object to read/write module data to database
	 *
	 * @var object $objModule
	 */
	protected $objModule;

	/**
	 * object to read/write administrative module data to database
	 *
	 * @var object $objModuleAdmin
	 */
	protected $objModuleAdmin;

	/**
	 * object to check system configuration
	 *
	 * @var object $objSysConfig
	 */
	protected $objSysConfig;

	/**
	 * output varaiable to store user feedback
	 *
	 * @var string $output
	 */
	protected $output;

	/**
	 * object to manage module patches
	 *
	 * @var object $objPatch
	 */
	protected $objPatch;

	/**
	 * Standard initialisation function
	 */
	public function init() {
		try {
			$this->objLog = &$this->getObject('logactivity','logger');
			$this->objUser = &$this->getObject('user','security');
			$this->objConfig = &$this->getObject('altconfig','config');
			$this->objLanguage = &$this->getObject('language','language');
        	$this->objModuleAdmin = &$this->getObject('modulesadmin','modulecatalogue');
			$this->objModule = &$this->getObject('modules');
			//the class for reading register.conf files
        	$this->objModFile = &$this->getObject('modulefile');
        	$this->objPatch = &$this->getObject('patch','modulecatalogue');
			$this->objCatalogueConfig = &$this->getObject('catalogueconfig','modulecatalogue');
			$this->objSideMenu = &$this->getObject('catalogue','modulecatalogue');
			$this->objSideMenu->addNodes(array('updates','all'));
			$this->objCatalogueConfig->writeCatalogue();
			$xmlCat = $this->objCatalogueConfig->getNavParam('category');
			//get list of categories
			$catArray = $xmlCat['catalogue']['category'];
			natcasesort($catArray);
			$this->objSideMenu->addNodes($catArray);


		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
		}
	}

	/**
	 * Dispatch function
	 *
	 * @return mixed
	 */
	public function dispatch() {
		try {
			$this->output = '';
			$action = $this->getParm('action');
			if (($action != 'firsttimeregistration') && (!$this->objUser->isAdmin())) {	//no access to non-admin users
				return 'noaccess_tpl.php';
			}
			if (!isset($activeCat)) {
				$activeCat = $this->getParm('cat','Updates');
			}
			$this->setVar('activeCat',$activeCat);
			//$this->setVar('letter',$this->getParam('letter','none'));
			$this->setLayoutTemplate('cat_layout.php');
			switch ($action) {		//check action
				case xml:
							break;
				case null:
				case 'list':
					if (strtolower($activeCat) == 'updates') {
						$this->setVar('patchArray',$this->objPatch->checkModules());
						return 'updates_tpl.php';
					} else {
						return 'front_tpl.php';
					}
				case 'uninstall':
					if ($this->uninstallModule($this->getParm('mod'))) {
						$this->output = str_replace('[MODULE]',$mod,$this->objLanguage->languageText('mod_modulecatalogue_uninstallsuccess','modulecatalogue'));
					} else {
						if ($this->output == '') {
							$this->output = $this->objModuleAdmin->output;
						}
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'install':
					$mod = $this->getParm('mod');
					$regResult = $this->installModule(trim($mod));
					if ($regResult){
						$this->output = str_replace('[MODULE]',$mod,$this->objLanguage->languageText('mod_modulecatalogue_installsuccess','modulecatalogue'));	//success
					} else {
						if ($this->output == '') {
							$this->output = $this->objModuleAdmin->output;
						}
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'info':
					$filepath = $this->objModFile->findRegisterFile($this->getParm('mod'));
    				if ($filepath) { // if there were no file it would be FALSE
    					$this->registerdata=$this->objModFile->readRegisterFile($filepath);
    					if ($this->registerdata){
    						return 'info_tpl.php';
    					}
    				} else {
    					$this->setVar('output',$this->objLanguage->languageText('mod_modulecatalogue_noinfo','modulecatalogue'));
						return 'front_tpl.php';
    				}
				case 'textelements':
					$texts = $this->objModuleAdmin->moduleText($this->getParm('mod'));
					$this->setVar('moduledata',$texts);
					$this->setVar('modname',$this->getParm('mod'));
					return 'textelements_tpl.php';
				case 'addtext':
					$modname = $this->getParm('mod');
					$texts = $this->objModuleAdmin->moduleText($modname,'fix');
					$texts = $this->objModuleAdmin->moduleText($modname);
					$this->output=$this->objModule->output;
					$this->setVar('output',$this->output);
					$this->setVar('moduledata',$texts);
					$this->setVar('modname',$modname);
					return 'textelements_tpl.php';
				case 'replacetext':
					$modname = $this->getParm('mod');
					$texts=$this->objModuleAdmin->moduleText($modname,'replace');
					$texts=$this->objModuleAdmin->moduleText($modname);
					$this->output=$this->objModule->output;
					$this->setVar('output',$this->output);
					$this->setVar('moduledata',$texts);
					$this->setVar('modname',$modname);
					return 'textelements_tpl.php';
				case 'batchinstall':
					$selectedModules=$this->getArrayParam('arrayList');
					if (count($selectedModules)>0) {
						$this->batchRegister($selectedModules);
					} else {
						$this->output ='<b>'.$this->objLanguage->languageText('mod_modulecatalogue_noselect','modulecatalogue').'</b>';
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'batchuninstall':
					$selectedModules=$this->getArrayParam('arrayList');
					if (count($selectedModules)>0) {
						$this->batchDeregister($selectedModules);
					} else {
						$this->output ='<b>'.$this->objLanguage->languageText('mod_modulecatalogue_noselect','modulecatalogue').'</b>';
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'updateall':
					$this->objModuleAdmin->updateAllText();
					return $this->nextAction('list');
				case 'firsttimeregistration':
					$this->objSysConfig = &$this->getObject('dbsysconfig','sysconfig');
					$check = $this->objSysConfig->getValue('firstreg_run','modulecatalogue');
					log_debug('in controller - now trying firstreg..');
					if (!$check){
						$this->firstRegister();
					}
					// Show next installation step
					log_debug('back in modcat controller after firstreg. try to redirect now');
					return $this->nextAction(null,null,'splashscreen');
				case 'update':
					$modname = $this->getParam('mod');
                	$this->output = $this->objPatch->applyUpdates($modname);
                	$this->setVar('module',$modname);
                	$this->setVar('output',$this->output);
                	$this->setVar('patchArray',$this->objPatch->checkModules());
                	return 'updates_tpl.php';
				default:
					throw new customException($this->objLanguage->languageText('mod_modulecatalogue_unknownaction','modulecatlogue').': '.$action);
					break;
			}
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
		}
	}

	/**
    * This method is a 'wrapper' function - it takes info from the
    * 'register.conf' file provided by the module to be registered,
    * and passes it to its namesake function in the modulesadmin
    * class - which is where the SQL entries actually happen.
    * @author James Scoble
    * @param string $modname the module_id of the module to be used
    * @return string $regResult
    */
    private function installModule($modname) {
    	try {
    		$filepath = $this->objModFile->findRegisterFile($modname);
    		if ($filepath) { // if there were no file it would be FALSE
    			$this->registerdata=$this->objModFile->readRegisterFile($filepath);
    			if ($this->registerdata) {
    				// Added 2005-08-24 as extra check
    				if ( isset($this->registerdata['WARNING']) && ($this->getParm('confirm')!='1') ){
    					$this->output = $this->registerdata['WARNING'];
    					return FALSE;
    				}
    				return $this->objModuleAdmin->installModule($this->registerdata);
    			}
    		} else {
    			$this->output = $this->objLanguage->languageText('mod_modulecatalogue_errnofile','modulecatalogue');
    			return FALSE;
    		}
    	} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
		}
    } // end of function

     /**
    * This method is a 'wrapper' function - it takes info from the 'register.conf'
    * file provided by the module to be registered, and passes it to its namesake
    * function in the modulesadmin class - which is where the SQL entries actually
    * happen. It uses file() to load the register.php file into an array, then
    * chew through it line by line, looking for keywords.
    *
    * @author James Scoble
    * @param string $modname the module_id of the module to be used
    * @returns boolean TRUE or FALSE
    */
    private function uninstallModule($modname) {
    	try {
    		$filepath=$this->objModFile->findRegisterFile($modname);
    		$this->registerdata=$this->objModFile->readRegisterFile($filepath);
    		if (is_array($this->registerdata)) {
    			return $this->objModuleAdmin->uninstallModule($modname,$this->registerdata);
    		} else {
    			$this->output = $this->objLanguage->languageText('mod_modulecatalogue_errnofile','modulecatalogue');
    			return FALSE;
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * Method to handle registration of multiple modules at once
    * @param array $modArray
    */
    private function batchRegister($modArray) {
    	try {
    		foreach ($modArray as $line) {
    			if (!$this->smartRegister($line)) {
    				throw new customException($this->objLanguage->languageText('mod_modulecatalogue_insterror','modulecatalogue')." $line: {$this->output}");
    			}
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This method is designed to handle the registeration of multiple modules at once.
    * @param string $modname
    */
    private function smartRegister($modname) {
    	try {
    		$isReg = $this->objModule->checkIfRegistered($modname,$modname);
    		if ($isReg){
    			return TRUE;
    		}
    		$filepath = $this->objModFile->findRegisterFile($modname);
    		if ($filepath) { //if there were no file it would be FALSE
    			$registerdata=$this->objModFile->readRegisterFile($filepath);
    			if ($registerdata){
    				if (isset($registerdata['DEPENDS'])){
    					foreach ($registerdata['DEPENDS'] as $line) {
    						$result=$this->smartRegister($line);
    						if ($result==FALSE) {
    							$this->output = $this->objModuleAdmin->output."\n";
    							$this->output .= str_replace('{MODULE}',$line,$this->objLanguage->languageText('mod_modulecatalogue_needmodule','modulecatalogue'))."\n";
    							return FALSE;
    						}
    					}
    				}
    				$regResult= $this->objModuleAdmin->installModule($registerdata);
    				if ($regResult){
    					$this->output = str_replace('[MODULE]',$modname,$this->objLanguage->languageText('mod_modulecatalogue_regconfirm','modulecatalogue'));
    				}
    				return $regResult;
    			}
    		} else {
    			$this->output .= $this->objLanguage->languageText('mod_modulecatalogue_errnofile','modulecatalogue')."\n";
    			return FALSE;
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * Method to handle deregistration of multiple modules at once
    * @param array $modArray
    */
    private function batchDeregister($modArray) {
    	try {
    		foreach ($modArray as $line) {
    			if (!$this->smartDeregister($line)) {
    				throw new customException($this->objLanguage->languageText('mod_modulecatalogue_uninsterror','modulecatalogue')." $line: {$this->objModuleAdmin->output}");
    			}
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * This method is designed to handle the deregisteration of multiple modules at once.
    * @param string $modname
    */
    private function smartDeregister($modname) {
    	try {
    		$isReg=$this->objModule->checkIfRegistered($modname,$modname);
    		if ($isReg==FALSE){
    			return TRUE;
    		}
    		$filepath=$this->objModFile->findRegisterFile($modname);
    		if ($filepath) { // if there were no file it would be FALSE
    			$registerdata=$this->objModFile->readRegisterFile($filepath);
    			if ($registerdata) {
    				// Here we get a list of modules that depend on this one
    				$depending=$this->objModule->checkForDependentModules($modname);
    				if (count($depending)>0) {
    					foreach ($depending as $line) {
    						$result=$this->smartDeregister($line);
    						if ($result==FALSE) {
    							return FALSE;
    						}
    					}
    				}
    				$regResult= $this->objModuleAdmin->unInstall($modname,$registerdata);
    				if ($regResult) {
    					$this->output .= str_replace('[MODULE]',$modname,$this->objLanguage->languageText('mod_modulecatalogue_deregconfirm','modulecatalogue'));
    				}
    				return $regResult;
    			}
    		} else {
    			$this->output = $this->objLanguage->languageText('mod_modulecatalogue_errnofile','modulecatalogue');
    			return FALSE;
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

	/**
    * This is a method to handle first-time registration of the basic modules
    */
    private function firstRegister() {
    	try {
    		$root = $this->objConfig->getsiteRootPath();
    		if (!file_exists($root.'config/config.xml')){
    			throw new customException("could not find config.xml! tried {$root}config/config.xml");
    		}
    		$mList=file($root.'installer/dbhandlers/default_modules.txt');
    		foreach ($mList as $line) {
    			if ($line[0]!='#') {
    				if (!$this->installModule(trim($line))) {
    					throw new customException("Error installing module $line: {$this->objModuleAdmin->output}\n{$this->objModuleAdmin->getLastError()}");
    				}
    			}
    		}
    		// Flag the first time registration as having been run
    		$this->objSysConfig->insertParam('firstreg_run','modulecatalogue',TRUE);
    		log_debug('first time registration performed, variable set. First time registration cannot be performed again unless system variable \'firstreg_run\' is unset.');
    		// Make certain the user-defined postlogin module is registered.

    		$postlogin = $this->objSysConfig->getValue('KEWL_POSTLOGIN_MODULE','_site_');
    		if (($postlogin!='')&&(!($this->objModule->checkIfRegistered($postlogin)))){
    			$this->installModule($postlogin);
    			log_debug("Postlogin module $postlogin has been installed!");
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

    /**
     * Method to determine whether the module requires the user to be logged in.
     *
     * @return TRUE|FALSE false if the user is carrying out first time module registration, else true.
     */
    public function requiresLogin() {
    	try {
    		if ($this->getParm('action') == 'firsttimeregistration') {
    			return FALSE;
    		} else {
    			return TRUE;
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
     * kind of a hack wrapper method to get the messed up params from the header via getParam in the engine
     *
     * @param string $name parameter name
     * @param string $def default param value
     * @return string Parameter value or default if it doesnt exist
     */
    public function getParm($name,$def=null) {
    	try {
    		if (($res = $this->getParam($name)) == null) {
    			return $this->getParam('amp;'.$name,$def);
    		} else {
    			return $res;
    		}
    	} catch (Exception $e) {
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }
}
?>