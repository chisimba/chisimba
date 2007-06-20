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
           $this->objModule = &$this->getObject('modules');
            //the class for reading register.conf files
           $this->objModFile = &$this->getObject('modulefile');

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
    		
    		 $action = $this->getParm('action');
    		 switch ($action){
    		 	case NULL:
    		 	case 'list':
    		 	return 'front_tpl.php';
    		 	default:
                    throw new customException($this->objLanguage->languageText('mod_modulecatalogue_unknownaction','modulecatalogue').': '.$action);
                 break;
    		 }
    	}
    	catch (customException $e){
    		echo customException::cleanUp();
    	}
    }

}
?>