<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Adaptor Pattern around the PEAR::Config Object
 * This class will provide the catalogue configuration for module registration
 * 
 *
 * @author Prince Mbekwa
 * @todo sysconfig properties' set and get
 * @todo module config (especially from module admin)
 * @package catalogue
 */
//grab the pear::Config properties
// include class
require_once 'Config.php';


class catalogueconfig extends object {
	
	/**
     * The pear config object 
     *
     * @access public
     * @var string
    */
		
    protected $_objPearConfig;
    
    /**
     * The path of the files to be read or written
     * @access public
     * @var string
     */
    public $_path = null;
    /**
     * The root object for configs read 
     *
     * @access private
     * @var string
    */
    protected $_root;
    /**
     * The root object for properties read 
     *
     * @access private
     * @var string
    */
    protected $_property;
    /**
     * The options value for altconfig read / write
     *
     * @access private
     * @var string
    */
    protected $_options;
    
    /**
     * The catalogueconfig object for catalogueconfig storage
     * 
     * @access private
     * @var array
     */
    protected $_catalogueconfigVars;
    
    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var string
    */
    public $_errorCallback;
    
    
	/**
    * Method to construct the class.
    */
    public function init()
    {
   		// instantiate object
        try{
        $this->_objPearConfig = new Config();
        }catch (Exception $e){
        	$this->errorCallback ('Caught exception: '.$e->getMessage());
        	exit();
        }
    }
    
    /**
     * Method to parse catalogue lists.
     * For use when reading configuration options
     *
     * @access protected
     * @param string $config xml file or PHPArray to parse
     * @param string $property used to set property value of incoming config string
     * $property can either be:
     * 1. PHPArray
     * 2. XML
     * @return boolean True/False result.
     * 
     */
    protected function readCatalogue($config,$property)
    {
    	
    	try {
    		// read catalogue data and get reference to root
    		if(!isset($this->_path)) $this->_path = "../resources";
    		
    		$this->_root =& $this->_objPearConfig->parseConfig("{$this->_path}catalogue.xml",$property);
    		
    		if (PEAR::isError($this->_root)) {
    			throw new Exception('Can not read Catalogue');
    		}
    		return $this->_root;    		
    	}catch (Exception $e)
    	{
    		 $this->errorCallback ('Caught exception: '.$e->getMessage());	
    		 exit();
    	}
		
    }
    /**
     * Method to wirte catalogue options.
     * For use when writing catalogue options
     *
     * @access public
     * @param string values to be saved
     * @param string property used to set property value of incoming catalogue string
     * $property can either be:
     * 1. PHPArray
     * 2. XML
     * @return boolean  TRUE for success / FALSE fail .
     * 
     */
    public function writeCatalogue($values,$property)
    {
    	// set xml root element
    	try {
    		$this->_options = array('name' => 'Settings');
    		$this->_root =& $this->_objPearConfig->parseConfig($values,"PHPArray");
    		if(!isset($this->_path)) $this->_path = "../resources";
    		$this->_objPearConfig->writeConfig("{$path}catalogue.xml",$property, $this->_options);
    		//update the _root object
    		$this->readConfig('','XML');
    		return true;
    	}catch (Exception $e)
    	{
    		 $this->errorCallback ('Caught exception: '.$e->getMessage());
    		 exit();
    	}
		
    }
}
?>