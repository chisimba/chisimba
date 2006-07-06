<?php
/**
 * Module administration with catalogue interface. Allows installation and Un-installation of modules
 * via a cagtalogue interface which groups similar modules. Also incorporates module patching.
 * 
 * @author Nic Appleby
 * @package core
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
	public $objModule;
	
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
			// the class for reading register.conf files
        	$this->objRegFile = &$this->newObject('filereader','moduleadmin');
        	$this->objLanguage = &$this->getObject('language','language');
        	$this->objModule = &$this->getObject('modulesadmin');
			$this->objDBModCat = &$this->getObject('dbmodcat','modulecatalogue');
			$this->objModFile = &$this->getObject('catalogueconfig','modulecatalogue');
			$this->objSideMenu = &$this->getObject('catalogue','modulecatalogue');
			//get list of categories
			$this->objSideMenu->addNodes(array('updates','all'));
			$var = $this->objModFile->getConfigParam('CATAGORY','');
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
			if (!$this->objUser->isAdmin()) {			//no access to non-admin users
				return 'noaccess_tpl.php';
			}
			if (!isset($activeCat)) {
				$activeCat = $this->getParm('cat','Updates');
			}
			$this->setVar('activeCat',$activeCat);
			//$this->setVar('letter',$this->getParam('letter','none'));
			$this->setLayoutTemplate('cat_layout.php');
			switch ($this->getParm('action')) {		//check action
				case null:
				case 'list':
					if (strtolower($activeCat) == 'updates') {
						return 'updates_tpl.php';
					} else {
						return 'front_tpl.php';
					}
				case 'moduleinfo':
					return 'info_tpl.php';
				case 'uninstall':
					if ($this->unInstallModule($this->getParm('mod'))) {
						$this->ouput = 'success';
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'install':
					$regResult=$this->registerModule($this->getParm('mod'));
					if ($regResult == 'OK'){
						$this->output = 'success';	//success
					}
					$this->setSession('output',$this->output);
					return $this->nextAction(null,array('cat'=>$activeCat));
				case 'xml':
					return 'front_tpl.php';
					
				default:
					die('unknown action.');
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
    public function registerModule($modname) {
    	try {
    		$filepath = $this->objModFile->findRegisterFile($modname);
    		if ($filepath) // if there were no file it would be FALSE
    		{
    			$this->registerdata=$this->objRegFile->readRegisterFile($filepath);
    			if ($this->registerdata) {
    				// Added 2005-08-24 as extra check
    				if ( isset($this->registerdata['WARNING']) && ($this->getParm('confirm')!='1') ){
    					$this->output = $this->registerdata['WARNING'];
    					return FALSE;
    				}
    				$regResult= $this->objModule->registerModule($this->registerdata);
    				return $regResult;
    			}
    		} else {
    			$this->output ='mod_moduleadmin_err_nofile';
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
    function unInstallModule($modname)
    {
        $filepath=$this->findRegisterFile($modname);
        $this->registerdata=$this->objRegFile->readRegisterFile($filepath);
        if (is_array($this->registerdata))
        {
            return $this->objModule->unInstall($modname,$this->registerdata);
        }
            else
            {
                $this->output=$this->confirmRegister('mod_moduleadmin_err_nofile');
                return FALSE;
            }
    } // end of function unInstallModule()


	/**
     * The error callback function, defers to configured error handler
     *
     * @param string $exception
     * @return void
     */
    public function errorCallback($exception)
    {
    	echo customException::cleanUp($exception);
    }
    
    public function getParm($name,$def=null) {
    	if (($res = $this->getParam($name))==null) {
    		return $this->getParam('amp;'.$name,$def);
    	} else {
    		return $res;
    	}
    }
}
?>