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
    		 					//echo "Bum module";
    		 					unset($modobj->string);
    		 					//continue;
    		 				}
    		 				else {
    		 					$modulesarray[] = $modobj->string[0];
    		 				}
    		 			}
    		 			$i++;
    		 		}
    		 		//print_r($modulesarray);
    		 		foreach ($modulesarray as $string)
    		 		{
    		 			echo $string."<br />";
    		 		}
    		 		die();
    		 	//return 'front_tpl.php';
    		 	
    		 	case 'getmodule':
    		 		$module = $this->getParam('zipmod');
    		 		if($module == '')
    		 		{
    		 			die("module cannot be empty");
    		 		}
    		 		$modzip = $this->objRpcClient->getModuleZip($module);
    		 		// returned as a base64 encoded string
    		 		$moduleark = base64_decode($modzip);
    		 		
    		 		$filename = $this->objConfig->getModulePath().$module.".zip";
					if (is_writable($filename)) {
					    if (!$handle = fopen($filename, 'a')) {
         					echo "Cannot open file ($filename)";
         					exit;
    					}
					    if (fwrite($handle, $moduleark) === FALSE) {
        					echo "Cannot write to file ($filename)";
        					exit;
    					}

    					echo "Success, wrote data to file ($filename)";
    					fclose($handle);
					} else {
    					echo "The file $filename is not writable";
					}
    		 		
    		 		
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