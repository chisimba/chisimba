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
	 * Standard initialisation function
	 */
	public function init() {
		try {
			$this->objLog = &$this->getObject('logactivity','logger');
			$this->objUser = &$this->getObject('user','security');
			$this->objConfig = &$this->getObject('altconfig','config');
			$this->objLanguage = &$this->getObject('language','language');
			$this->objDBModCat = &$this->getObject('dbmodcat','modulecatalogue');
			$this->objModFile = &$this->getObject('modulefile','modulecatalogue');
			$this->objSideMenu = &$this->getObject('catalogue','modulecatalogue');
			//get list of categories
			$this->objSideMenu->addNodes(array('category'=>'updates'));
			$this->objSideMenu->addNodes(array('category'=>'all'));
			$this->objSideMenu->addNodes($this->objModFile->getCategories());
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
			$activeCat = $this->getParam('cat','Updates');
			$this->setVar('activeCat',$activeCat);
			$this->setVar('letter',$this->getParam('letter','none'));
			$this->setLayoutTemplate('cat_layout.php');
			switch ($this->getParam('action')) {		//check action
				case null:
				case 'list':
					if (strtolower($activeCat) == 'updates') {
						return 'updates_tpl.php';
					} else {
						return 'front_tpl.php';
					}
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