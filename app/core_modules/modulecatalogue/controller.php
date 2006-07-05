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
	 * object to read/write xml data
	 *
	 * @var object $objXML
	 */
	protected $objXML;
	
	/**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var string
    */
    public $_errorCallback;
	
	/**
	 * Standard initialisation function
	 */
	public function init() {
		try {
			$this->objLog = &$this->getObject('logactivity','logger');
			$this->objUser = &$this->getObject('user','security');
			$this->objConfig = &$this->getObject('altconfig','config');
			// the class for reading register.conf files
        	$this->objRegFile=$this->newObject('filereader','moduleadmin');
        	$this->objLanguage = &$this->getObject('language','language');
			$this->objDBModCat = &$this->getObject('dbmodcat','modulecatalogue');
			$this->objModFile = &$this->getObject('catalogueconfig','modulecatalogue');
			$this->objSideMenu = &$this->getObject('catalogue','modulecatalogue');
			//get list of categories
			$this->objSideMenu->addNodes(array('updates','all'));
			$var = $this->objModFile->getConfigParam('CATAGORY','');
			//var_dump($var);
			$result=$this->objSideMenu->addNodes($var);
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
				$activeCat = $this->getParam('cat','Updates');
			}
			$this->setVar('activeCat',$activeCat);
			//$this->setVar('letter',$this->getParam('letter','none'));
			$this->setLayoutTemplate('cat_layout.php');
			switch ($this->getParam('action')) {		//check action
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
				case 'install':
					$regResult=$this->registerModule($this->getParam('mod'));
					if ($regResult == 'OK'){
						$this->output = 'success';	//success
					}
					$this->setVar('output',$this->ouput);
					return $this->nextAction('list',array('cat'=>$activeCat));
				case 'xml':
					break;
					
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
    				if ( isset($this->registerdata['WARNING']) && ($this->getParam('confirm')!='1') ){
    					$this->output = $this->registerdata['WARNING'];
    					return FALSE;
    				}
    				//$regResult= $this->objModule->registerModule($this->registerdata);
    				$regResult='OK';
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
     * The error callback function, defers to configured error handler
     *
     * @param string $error
     * @return void
     */
    public function errorCallback($exception)
    {
    	$this->_errorCallback = new ErrorException($exception,1,1,'altconfig_class_inc.php');
        echo $this->_errorCallback;
    }
}
?>