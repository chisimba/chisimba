<?PHP
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class packages extends controller
{
	
	/**
     * Controller class for the packages module that extends the base controller
     *
     * @author Paul Scott <pscott@uwc.ac.za>
     * @author Prince Mbekwa <pmbekwa@uwc.ac.za>
     * @copyright 2007 AVOIR
     * @package packages
     * @category chisimba
     * @license GPL
     */
	
	 /**
     * Object to read module information from register files
     *
     * @var object $objModFile
     */
    protected $objModFile;
    
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
     * Language object for multilingual support
     *
     * @var object $objLanguage
     */
    public $objLanguage;
    
    public $objRpcServer;
    public $objRpcClient;


    /**
     * Constructor method to instantiate objects and get variables
     * 
     * @since  1.0.0
     * @return string
     * @access public
     */
    public function init()
    {
        try {
           $this->objConfig = $this->getObject('altconfig','config');
           $this->objLanguage = $this->getObject('language','language');
           $this->objModule = &$this->getObject('modules', 'modulecatalogue');
            //the class for reading register.conf files
           $this->objModFile = &$this->getObject('modulefile', 'modulecatalogue');
           $this->objRpcServer = $this->getObject('rpcserver'); 
           $this->objRpcClient = $this->getObject('rpcclient'); 

        }
        catch(customException $e) {
            //oops, something not there - bail out
            echo customException::cleanUp();
            //we don't want to even attempt anything else right now.
            die();
        }
    }

    /**
     * Method to process actions to be taken from the querystring
     *
     * @param string $action String indicating action to be taken
     * @return string template
     */
    public function dispatch($action = Null){
    	
    	try{
    		
    		 $action = $this->getParam('action');
    		 switch ($action){
    		 	case NULL:
    		 		$this->requiresLogin(FALSE);
    		 		$this->setVar('pageSuppressXML', TRUE);
    		 		$this->objRpcServer->serve();
    		 		die();
    		 		
    		 	case 'list':
    		 		$mlist = $this->objRpcClient->getModuleList();
    		 		$doc = simplexml_load_string($mlist);
    		 		$count = count($doc->array->data->value);
    		 		$i = 0;
    		 		while($i <= $count)
    		 		{
    		 			$modobj = $doc->array->data->value[$i];
    		 			if(is_object($modobj))
    		 			{
    		 				if($modobj->string == 'CVSROOT' || $modobj->string == 'CVS' || $modobj->string == '.' || $modobj->string == '..' 
    		 				   || $modobj->string == 'build.xml' || $modobj->string == 'COPYING' || $modobj->string == 'chisimba_modules.txt')
    		 				{
    		 					unset($modobj->string);
    		 				}
    		 				else {
    		 					$modulesarray[] = $modobj->string[0];
    		 				}
    		 			}
    		 			$i++;
    		 		}
    		 		
    		 		$this->setVarByRef('modulesarray', $modulesarray);
    		 		return 'modulelist_tpl.php';
    		 		break;
    		 	
    		 	case 'getmodule':
    		 		try {
    		 			$module = $this->getParam('zipmod');
    		 			if($module == '')
    		 			{
    		 				throw new customException($this->objLanguage->languageText("mod_packages_modempty", "packages"));
    		 			}
    		 			$modzip = $this->objRpcClient->getModuleZip($module);
    		 			$modzip = strip_tags($modzip);
    		 			// returned as a base64 encoded string
    		 			$moduleark = base64_decode($modzip);
    		 			$filename = $this->objConfig->getModulePath().$module.time().".zip";
    		 			if (!$handle = fopen($filename, 'wb')) 
    		 			{
         					throw new customException($this->objLanguage->languageText("mod_packages_nowritefile", "packages"));
         					exit;
    					}
						if (fwrite($handle, $moduleark) === FALSE) {
        					throw new customException($this->objLanguage->languageText("mod_packages_nowritefile", "packages"));
        					exit;
    					}
    					log_debug($this->objLanguage->languageText("mod_packages_successfulewrite", "packages")." ($filename)");
    					fclose($handle);
   					}
    		 		catch (customException $e)
    		 		{
    		 			customException::cleanUp();
    		 			exit;
    		 		}
					
    		 		// unzip the file to the module path...
    		 		$objZip = $this->getObject('wzip', 'utilities');
					$objZip->unzip($filename, $this->objConfig->getModulePath());
					unlink($filename);
					
					// return a template saying that all this was a success...
					//return 'success_tpl.php';
    		 		break;
    		 		
    		 	default:
                    throw new customException($this->objLanguage->languageText('mod_modulecatalogue_unknownaction','modulecatalogue').': '.$action);
                 break;
    		 }
    	}
    	catch (customException $e){
    		echo customException::cleanUp();
    	}
    }
    
    /**
     * Ovveride the login object in the parent class
     *
     * @param void
     * @return bool
     * @access public
     */
     public function requiresLogin() 
     {
        return FALSE;
     }

}
?>