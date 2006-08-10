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
	 * The site configuration object
	 *
	 * @var object $config
	 */
	public $config;


	/**
    * Method to construct the class.
    */
    public function init()
    {
   		// instantiate object
        try{
        $this->_objPearConfig = new Config();
        $this->objConfig = &$this->getObject('altconfig','config');
        }catch (Exception $e){
        $this->errorCallback('Caught exception: '.$e->getMessage());
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
    protected function readCatalogue($property)
    {

    	try {
    		// read catalogue data and get reference to root
			$this->_path = $this->objConfig->getsiteRootPath();
    		if (preg_match('/\/$/',$this->_path)) {
    			$this->_path .= "modules/modulecatalogue/resources/";
    		} else {
    			$this->_path .= "/modules/modulecatalogue/resources/";
    		}
    		if (file_exists($this->_path.'catalogue.xml')) {
    			$this->_root =& $this->_objPearConfig->parseConfig("{$this->_path}catalogue.xml",$property);
    		} else {
    			throw new customException("Could not find catalogue.xml: looked in {$this->_path}catalogue.xml");
    		}
    		if (PEAR::isError($this->_root)) {
    			throw new customException("Can not read Catalogue. Please make sure that your site_path is set correctly\nlooked in {$this->_path}catalogue.xml");
    		}
    		return $this->_root;
    	}catch (Exception $e)
    	{
    		$this->errorCallback('Caught exception: '.$e->getMessage());
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
     * @return boolean  TRUE for success / FALSE fail .
     *
     */
    public function writeCatalogue()
    {
    	// set xml root element
    	try {
    		$objModFile = &$this->getObject('modulefile','modulecatalogue');
    		$xmlStr = "<?xml version='1.0' encoding='ISO-8859-1'?>\n<settings xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='catalogue.xsd'>\n";
    		if(!isset($this->_path)) {
    			$this->_path = "{$this->objConfig->getsiteRootPath()}/modules/modulecatalogue/resources/";
    		}
    		$xmlStr .= "	<catalogue>\n";
    		$categories = $objModFile->getCategories();
    		if (is_array($categories)) {
    				foreach ($categories as $cat) {
    					$xmlStr .= "		<category>$cat</category>\n";
    				}
    		}
    		$xmlStr .= "	</catalogue>\n";
    		$modules = $objModFile->getLocalModuleList();
    		$id = 001;
    		foreach ($modules as $mod) {
    			$reg = $objModFile->readRegisterFile($objModFile->findregisterfile($mod));
    			$xmlStr .= "	<module>
    	<id>$id</id>\n";
    		if ($reg) {
    		$xmlStr .= "		<module_id>{$reg['MODULE_ID']}</module_id>
    	<module_authors>{$reg['MODULE_AUTHORS']}</module_authors>
        <module_releasedate>{$reg['MODULE_RELEASEDATE']}</module_releasedate>
        <module_description>{$reg['MODULE_DESCRIPTION']}</module_description>
        <module_version>{$reg['MODULE_VERSION']}</module_version>\n";
    			if (is_array($reg['MODULE_CATEGORY'])) {
    				foreach ($reg['MODULE_CATEGORY'] as $cat) {
    					$xmlStr .= "		<module_category>$cat</module_category>\n";
    				}
    			}
        		} else {
    				$xmlStr .= "		<module_id>$mod</module_id>\n";
    			}
    			$xmlStr .= "	</module>\n";
    			$id++;
    		}
    		$xmlStr .= '</settings>';
    		try {
    			if(!file_exists($this->_path))
    			{
    				mkdir($this->_path);
    			}
    			touch($this->_path.'catalogue.xml');
    			chmod($this->_path . 'catalogue.xml',0777);
    		}
    		catch(Exception $e) {
    			$this->errorCallback('Caught exception: ' . $e->getMessage());
    		}
    		$fh = fopen($this->_path.'catalogue.xml','w');
    		fwrite($fh,$xmlStr);
			fclose($fh);
    		return true;
    	} catch (Exception $e)
    	{
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}

    }

    /**
    * Method to get modulelist for catalogue categories.
    *
    * @var string $pname The name of the parameter being set
    * @return  $value
    */
    public function getModulelist($pname)
    {
    	try {

				$this->_path = $this->objConfig->getsiteRootPath()."modules/modulecatalogue/resources/catalogue.xml";

				$xml = simplexml_load_file($this->_path);
				if($pname !="all"){
				 $query = "//module[module_category='{$pname}']/module_id";
				}else{
				  $query = "//module/module_id";
				}
				$entries = $xml->xpath($query);

        		if (!$entries) {
        			return FALSE;
        		}else{
       			$value = $entries;
       				return $value;
        		}

    	}catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}
    } #function insertParam

     /**
    * Method to get modulelist for catalogue categories.
    *
    * @var string $pname The name of the parameter being set
    * @return  $value
    */
    public function searchModulelist($str)
    {
    	try {

				$this->_path = $this->objConfig->getsiteRootPath()."modules/modulecatalogue/resources/catalogue.xml";

				$xml = simplexml_load_file($this->_path);
				$query = "//module[contains(module_id,'$str') or contains(module_description,'$str')]/module_id";
				$entries = $xml->xpath($query);

        		if (!$entries) {
        			return FALSE;
        		}else{
       			$value = $entries;
       				return $value;
        		}

    	} catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}
    }


    /**
     * Method to get module description from the catalogue
     *
     * @author Nic Appleby
     * @param string $modname module name
     * @return string module description|FALSE if none exists
     */
    public function getModuleDescription($modname) {
    	try {
    		$this->_path = $this->objConfig->getsiteRootPath()."modules/modulecatalogue/resources/catalogue.xml";
    		$xml = simplexml_load_file($this->_path);
    		$query = "//module[module_id='$modname']/module_description";
    		$entries = $xml->xpath($query);

    		if (!$entries) {
    			return FALSE;
    		} else {
    			return $entries;
    		}
    	} catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
    	}
    }

    /**
    * Method to get a system configuration parameter.
    *
    * @var string $pmodule The module code of the module owning the config item
    * @var string $pname The name of the parameter being set, use UPPER_CASE
    * @return  string $value The value of the config parameter
    */
    public function getNavParam($pmodule)
    {
    	try {

               	//Read conf
    			//if (!isset($this->_root)) {
    				$this->readCatalogue('XML');
    			//}
    			//Lets get the parent node section first

        		$Settings =& $this->_root->getItem("section", "settings");
        		//Now onto the directive node
        		//check to see if one of them isset to search by
        	    $Settings =& $Settings->getItem("section","catalogue");

        		if(isset($pmodule))$SettingsDirective =& $Settings->getItem("directive", "{$pmodule}");
        		$SettingsDirective =& $Settings->toArray();
        		//finally unearth whats inside
        		if (!$SettingsDirective) {
        			throw new Exception("Catalogue Navigation items are missing! {$pmodule}");
        		}else{
       			$value = $SettingsDirective;
       		   	return $value;
        		}


    	}catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}
    } #function insertParam

    /**
     * The error callback function, defers to configured error handler
     *
     * @param string $error
     * @return void
     */
    public function errorCallback($exception)
    {
    	echo customException::cleanUp($e);
    	exit();
    }

}
?>