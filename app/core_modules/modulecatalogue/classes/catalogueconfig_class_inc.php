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
        $this->objLanguage = &$this->getObject('language','language');
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
    			$this->_path .= "config/";
    		} else {
    			$this->_path .= "/config/";
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
    		$this->_path = "{$this->objConfig->getsiteRootPath()}/config/";

    		//$xmlStr .= "	<catalogue>\n";
    		//$categories = $objModFile->getCategories();
    		//if (is_array($categories)) {
    		//		foreach ($categories as $cat) {
    		//			$xmlStr .= "		<category>$cat</category>\n";
    		//		}
    		//}
    		//$xmlStr .= "	</catalogue>\n";
    		$modules = $objModFile->getLocalModuleList();
    		$id = 001;
    		foreach ($modules as $mod) {
    			if ($mod) {
    				$xmlStr .= "	<module>
    	<id>$id</id>\n";
    				$reg = $objModFile->readRegisterFile($objModFile->findregisterfile($mod));
    				if (is_array($reg)) {
    				$from = $this->objLanguage->languageText('phrase_frommodule');
    				if (isset($reg['MODULE_ID'])){
    					$module_id = htmlentities($reg['MODULE_ID']);
    				} else {
    					$module_id = 'unknown';
    					log_debug($this->objLanguage->languageText('mod_modulecatalogue_missingtag','modulecatalogue').": MODULE_ID");
    				}
    				if (isset($reg['MODULE_NAME'])){
    					$module_name = htmlentities($reg['MODULE_NAME']);
    				} else {
    					$module_name = $module_id;
    				}
    				if (isset($reg['MODULE_AUTHORS'])){
    					$module_authors = htmlentities($reg['MODULE_AUTHORS']);
    				} else {
    					$module_authors = '';
    					log_debug($this->objLanguage->languageText('mod_modulecatalogue_missingtag','modulecatalogue').": MODULE_AUTHORS $from $module_name");
    				}
    				if (isset($reg['MODULE_RELEASEDATE'])){
    					$module_releasedate = htmlentities($reg['MODULE_RELEASEDATE']);
    				} else {
    					$module_releasedate = '';
    					log_debug($this->objLanguage->languageText('mod_modulecatalogue_missingtag','modulecatalogue').": MODULE_RELEASEDATE $from $module_name");
    				}
    				if (isset($reg['MODULE_DESCRIPTION'])){
    					$module_description = htmlentities($reg['MODULE_DESCRIPTION']);
    				} else {
    					$module_description = '';
    					log_debug($this->objLanguage->languageText('mod_modulecatalogue_missingtag','modulecatalogue').": MODULE_DESCRIPTION $from $module_name");
    				}
    				if (isset($reg['MODULE_VERSION'])){
    					$module_version = htmlentities($reg['MODULE_VERSION']);
    				} else {
    					$module_version = '';
    					log_debug($this->objLanguage->languageText('mod_modulecatalogue_missingtag','modulecatalogue').": MODULE_VERSION $from $module_name");
    				}

    				$xmlStr .= "		<module_id>$module_id</module_id>
    	<module_name>$module_name</module_name>
    	<module_authors>$module_authors</module_authors>
        <module_releasedate>$module_releasedate</module_releasedate>
        <module_description>$module_description</module_description>
        <module_version>$module_version</module_version>\n";
    				if (isset($reg['MODULE_CATEGORY'])) {
    					foreach ($reg['MODULE_CATEGORY'] as $cat) {
    						$cat = htmlentities($cat);
    						$xmlStr .= "		<module_category>$cat</module_category>\n";
    					}
    				}
        		} else {
        			$mod = htmlentities($mod);
    				$xmlStr .= "		<module_id>$mod</module_id>\n";
    			}
    			$xmlStr .= "	</module>\n";
    			$id++;
    		}
    		}
    		$xmlStr .= '</settings>';
    		if(!file_exists($this->_path))
    		{
    			mkdir($this->_path);
    		}
    		if(file_exists($this->_path.'catalogue.xml'))
    		{
    			unlink($this->_path.'catalogue.xml');
    			touch($this->_path.'catalogue.xml');
    			chmod($this->_path . 'catalogue.xml',0666);
    		}
    		if(!file_exists($this->_path.'catalogue.xml'))
    		{
    			touch($this->_path.'catalogue.xml');
    			chmod($this->_path . 'catalogue.xml',0666);
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

				$this->_path = $this->objConfig->getsiteRootPath()."config/catalogue.xml";

				$xml = simplexml_load_file($this->_path);
				if($pname !="all"){
					$query = "//module[module_category='{$pname}']";
				}else{
				  $query = "//module";

				}
				$entries = $xml->xpath($query);


				foreach ($entries as $module) {
					$moduleName = $this->objLanguage->abstractText((string)$module->module_name);
					if (empty($moduleName)) {
						$result[(string)$module->module_id] = ucwords((string)$module->module_id);
					} else {
						$result[(string)$module->module_id] = ucwords($moduleName);
					}
				}
				if (!isset($result)) {
        			return FALSE;
        		}else {
       				return $result;
        		}

    	}catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}
    }

     /**
    * Method to get basic module data for all modules.
    *
    * @return  array of key module_id with values being the module name and description
    */
    public function getModuleDetails()
    {
    	try {

				$this->_path = $this->objConfig->getsiteRootPath()."config/catalogue.xml";

				$xml = simplexml_load_file($this->_path);
				$entries = $xml->xpath("//module");

				foreach ($entries as $module) {
					$moduleDesc = $this->objLanguage->abstractText((string)$module->module_description);
					$moduleName = $this->objLanguage->abstractText((string)$module->module_name);
					if (empty($moduleName)) {
						$result[] = array((string)$module->module_id,ucwords((string)$module->module_id),ucwords((string)$module->module_id));
					} else {
						$result[] = array((string)$module->module_id,ucwords($moduleName),ucwords($moduleDesc));
					}
				}
				if (!isset($result)) {
        			return FALSE;
        		}else {
       				return $result;
        		}

    	}catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}
    }


     /**
    * Method to get modulelist for catalogue categories.
    *
    * @var string $pname The name of the parameter being set
    * @var string $type either search module_id,description or both
    * @return  $value
    */
    public function searchModulelist($str,$type)
    {
    	try {
				$this->_path = $this->objConfig->getsiteRootPath()."config/catalogue.xml";
				//echo "$str $type<br/>";
				$xml = simplexml_load_file($this->_path);
				switch ($type) {
					case 'name':
						$query = "//module[contains(module_id,'$str') or contains(module_name,'$str')]";
						break;
					case 'description':
						$query = "//module[contains(module_description,'$str')]";
						break;
					default:
						$query = "//module[contains(module_id,'$str') or contains(module_description,'$str') or contains(module_name,'$str')]";
						break;
				}
				$entries = $xml->xpath($query);

        		foreach ($entries as $module) {
					$moduleName = $this->objLanguage->abstractText((string)$module->module_name);
					if (empty($moduleName)) {
						$result[(string)$module->module_id] = ucwords((string)$module->module_id);
					} else {
						$result[(string)$module->module_id] = ucwords($moduleName);
					}
				}
				if (!isset($result)) {
        			return FALSE;
        		}else {
       				return $result;
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
    		$this->_path = $this->objConfig->getsiteRootPath()."config/catalogue.xml";
    		$xml = simplexml_load_file($this->_path);
    		$query = "//module[module_id='$modname']/module_description";
    		$entries = $xml->xpath($query);

    		if (!isset($entries)) {
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
     * Method to get module name from the catalogue
     *
     * @author Nic Appleby
     * @param string $moduleId module id
     * @return string module name|FALSE if none exists
     */
    public function getModuleName($moduleId) {
    	try {
    		$this->_path = $this->objConfig->getsiteRootPath()."config/catalogue.xml";
    		$xml = simplexml_load_file($this->_path);
    		$query = "//module[module_id='$moduleId']/module_name";
    		$entries = $xml->xpath($query);

    		if (!isset($entries)) {
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


    	} catch (Exception $e){
    		$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();
    	}
    }

    public function getCategories() {
        try {
            $sysTypes = $this->objConfig->getsiteRoot()."installer/dbhandlers/systemtypes.xml";
            $doc = simplexml_load_file($sysTypes);
            $types = array();
            for ($i=1;$i<count($doc->systemtypes->category);$i++) {
                $types[] = (string)$doc->systemtypes->category[$i];
            }
            return $types;
        } catch (Exception $e){
            $this->errorCallback('Caught exception: '.$e->getMessage());
            exit();
        }
    }

    public function getCategoryList($category) {
        try {
            $path = $this->objConfig->getsiteRootPath()."config/catalogue.xml";
			$cat = simplexml_load_file($path);
			$types = array();
            if ($category == 'all') {
                $modules = $cat->xpath("//module");
                foreach($modules as $mod) {
                    $types[(string)$mod->module_id] = $this->objLanguage->abstractText((string)$mod->module_name);
                }
            } else {
                $sysTypes = $this->objConfig->getsiteRootPath()."installer/dbhandlers/systemtypes.xml";
                $doc = simplexml_load_file($sysTypes);
                $modules = $doc->xpath("//category[categoryname='$category']");
                foreach ($modules[0]->module as $mod) {
    		        $moduleId = (string)$mod;
    		        $mn = $cat->xpath("//module[module_id='$moduleId']/module_name");
    		        if (!$mn && (file_exists($this->objConfig->getModulePath().$moduleId) || file_exists($this->objConfig->getsiteRootPath()."core_modules/$moduleId"))) {
    		            log_debug("Could not find $moduleId in the catalogue. Rewriting catalogue.");
    		            $this->writeCatalogue();
    		            $cat = simplexml_load_file($path);
    		            $mn = $cat->xpath("//module[module_id='$moduleId']/module_name");
    		        }
    		        $types[$moduleId] = ucwords($this->objLanguage->abstractText((string)$mn[0]));
                }
            }
            return $types;
        } catch (Exception $e){
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
    	echo customException::cleanUp($exception);
    	exit();
    }

}
?>