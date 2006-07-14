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
	 * object that reads a module's register.conf file
	 *
	 * @var object $objRegFile
	 */
	protected $objRegFile;

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
	 * Standard initialisation function
	 */
	public function init() {
		try {
			$this->objLog = &$this->getObject('logactivity','logger');
			$this->objUser = &$this->getObject('user','security');
			$this->objConfig = &$this->getObject('altconfig','config');
			//the class for reading register.conf files
        	$this->objRegFile = &$this->newObject('filereader','modulecatalogue');
        	$this->objLanguage = &$this->getObject('language','language');
        	$this->objModuleAdmin = &$this->getObject('modulesadmin','modulecatalogue');
			$this->objModule = &$this->getObject('modules');
			$this->objModFile = &$this->getObject('modulefile');
			$this->objDBModCat = &$this->getObject('dbmodcat','modulecatalogue');
			$this->objCatalogueConfig = &$this->getObject('catalogueconfig','modulecatalogue');
			$this->objSideMenu = &$this->getObject('catalogue','modulecatalogue');
			//get list of categories
			$this->objSideMenu->addNodes(array('updates','all'));
			$var = $this->objCatalogueConfig->getNavParam('category');
			$this->objSideMenu->addNodes($var);

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
				case null:
				case 'list':
					if (strtolower($activeCat) == 'updates') {
						return 'updates_tpl.php';
					} else {
						return 'front_tpl.php';
					}
				case 'uninstall':
					if ($res = $this->uninstallModule($this->getParm('mod'))) {
						$this->output = 'success';
					} else {
						if ($this->output == '') {
							$this->output = $this->objModuleAdmin->output;
						}
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'install':
					$regResult = $this->installModule($this->getParm('mod'));
					if ($regResult == 'OK'){
						$this->output = 'success';	//success
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
    					$this->registerdata=$this->objRegFile->readRegisterFile($filepath);
    					if ($this->registerdata){
    						return 'info_tpl.php';
    					}
    				} else {
    					$this->setVar('output',$this->objLanguage->languageText('mod_modulecatalogue_noinfo','modulecatalogue'));
						return 'front_tpl.php';
    				}
				case 'textelements':
					$texts = $this->moduleText($this->getParm('mod'));
					$this->setVar('moduledata',$texts);
					$this->setVar('modname',$this->getParm('mod'));
					return 'textelements_tpl.php';
				case 'addtext':
					$modname = $this->getParm('mod');
					$texts = $this->moduleText($modname,'fix');
					$texts = $this->moduleText($modname);
					$this->output=$this->objModule->output;
					$this->setVar('output',$this->output);
					$this->setVar('moduledata',$texts);
					$this->setVar('modname',$modname);
					return 'textelements_tpl.php';
				case 'replacetext':
					$modname = $this->getParm('mod');
					$texts=$this->moduleText($modname,'replace');
					$texts=$this->moduleText($modname);
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
				case 'firsttimeregistration':
					$this->objSysConfig = &$this->getObject('dbsysconfig','sysconfig');
					$check = $this->objSysConfig->getValue('firstreg_run','modulecatalogue');
					if ($check!=TRUE){
						$this->firstRegister();
					}
					// Show next installation step
					return $this->nextAction(NULL,NULL,'installer');
				default:
					throw new customException('Modulecatalogue received unknown action: '.$action);
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
    			$this->registerdata=$this->objRegFile->readRegisterFile($filepath);
    			if ($this->registerdata) {
    				// Added 2005-08-24 as extra check
    				if ( isset($this->registerdata['WARNING']) && ($this->getParm('confirm')!='1') ){
    					$this->output = $this->registerdata['WARNING'];
    					return FALSE;
    				}
    				$regResult = $this->objModuleAdmin->installModule($this->registerdata);
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
    		$this->registerdata=$this->objRegFile->readRegisterFile($filepath);
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
    			$this->smartRegister($line);
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
    			$registerdata=$this->objRegFile->readRegisterFile($filepath);
    			if ($registerdata){
    				if (isset($registerdata['DEPENDS'])){
    					foreach ($registerdata['DEPENDS'] as $line) {
    						$result=$this->smartRegister($line);
    						if ($result==FALSE) {
    							return FALSE;
    						}
    					}
    				}
    				$regResult= $this->objModuleAdmin->installModule($registerdata);
    				if ($regResult=='OK'){
    					$this->output = $this->objLanguage->languageText('mod_modulecatalogue_regconfirm','modulecatalogue');
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
    * Method to handle deregistration of multiple modules at once
    * @param array $modArray
    */
    private function batchDeregister($modArray) {
    	try {
    		foreach ($modArray as $line) {
    			$this->smartDeregister($line);
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
    			$registerdata=$this->objRegFile->readRegisterFile($filepath);
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
    					$this->output .= $this->objLanguage->languageText('mod_modulecatalogue_deregconfirm','modulecatalogue');
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
    		$mList=file($this->objConfig->getsiteRootPath().'/installer/default_modules.txt');
    		foreach ($mList as $line) {
    			$this->installModule(trim($line));
    		}
    		// Flag the first time registration as having been run
    		$this->objSysConfig->insertParam('firstreg_run','modulecatalogue',TRUE);
    		// Make certain the user-defined postlogin module is registered.
    		$postlogin = $this->objSysConfig->getValue('KEWL_POSTLOGIN_MODULE','_site_');
    		if (($postlogin!='')&&(!($this->objModule->checkIfRegistered($postlogin)))){
    			$this->installModule($postlogin);
    		}
    	} catch (Exception $e) {
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
    private function moduleText($modname,$action='readonly') {
    	try {
    		$mtexts = array();
    		$filepath = $this->objModFile->findRegisterFile($modname);
    		$rdata = $this->objRegFile->readRegisterFile($filepath,FALSE);
    		$texts = $this->objModuleAdmin->listTexts($rdata,'TEXT');
    		$uses = $this->objModuleAdmin->listTexts($rdata,'USES');
    		if ($uses) {
    			array_push($texts,$uses);
    		}
    		$this->objModule->beginTransaction(); //Start a transaction;
    		if (is_array($texts)) {
    			foreach ($texts as $code=>$data) {
    				$isreg=$this->objModuleAdmin->checkText($code); // this gets an array with 3 elements - flag, content, and desc
    				$text_desc=$data['desc'];
    				$text_val=$data['content'];
    				if (($action=='fix')&&($isreg['flag']==0)) {
    					$this->objModuleAdmin->addText($code,$text_desc,$text_val,$modname);
    				}
    				if ($action=='replace') {
    					$this->objModuleAdmin->addText($code,$text_desc,$text_val,$modname);
    				}
    				$mtexts[]=array('code'=>$code,'desc'=>$text_desc,'content'=>$text_val,'isreg'=>$isreg,'type'=>'TEXT');
    			}
    		}
    		$this->objModule->commitTransaction(); //End the transaction;
    		return $mtexts;
    	} catch (Exception $e) {
    		$this->objModule->rollbackTransaction();
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
     * This is a method to update the text elements in all registered modules at once
     *
     */
    function replaceAllText()
    {
        $bigarray=$this->objModule->getAll();
        foreach ($bigarray as $line) {
            $texts = $this->moduleText($line['module_id'],'replace');
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