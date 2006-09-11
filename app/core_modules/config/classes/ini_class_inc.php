<?php
/* ----------- data class extends dbTable for tbl_userparamsadmin------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* 
* Model class for writing INIFiles.The class provides data access features for administering the list
* of system ini parameters 
* @example
* 	   $config_container ="MAIL"/"Settings"	 
*     $settings = array("name"=>"Bruce Banner","email"=> "hulk@angry.green.guy")
* 	   $iniPath = "/config/"
* 	   $iniName ="my.ini"	.
* @author Prince Mbekwa
* @todo userconfig properties' set and get
* @todo module config (especially from module admin)
* @package iniconfig
*/
require_once('Config.php');
class ini extends object 
{

    /**
    * Constructor method to define the table
    */
    public $objConf = null;
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
     * The sysconfig object for sysconfig storage
     *
     * @access private
     * @var array
     */
    protected $_sysconfigVars;

    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var string
    */
    public $_errorCallback;
    
    /**
     * sysconfig object
     *
     * @var varient
     */
    
    protected $sysconf = null;
    /**
     * Set ini directives
     *
     * @var varient
     */
    
    protected $SettingsDirective = null;
    /**
     * Holds values to be inserted into Ini
     *
     * @var string
     */
    
    protected $SettingsValue = null;
    /**
     * user object
     *
     * @var object
     */
    protected $objUser;
    /**
     * The root object for configs read
     *
     * @access private
     * @var string
    */
    protected $_root = false;
    /**
     * languagetext object
     *
     * @var object
     */
    public $Text;
    
    //Initialize class
    function init() {
    	    //pull in our objects
    		$this->objConf = new Config();
    		$this->sysconf = & $this->getObject('altconfig','config');
            $this->objUser = & $this->getObject("user", "security");
             $this->Text = &$this->newObject('language','language');
            
    }
     /**
     * Method to create initial IniFile config elements. This method will create 
     * blank ini params.
     * @example
     * 	   $config_container ="MAIL"/"Settings"	 
     *     $settings = array("name"=>"Bruce Banner","email"=> "hulk@angry.green.guy")
     * 	   $iniPath = "/config/"
     * 	   $iniName ="my.ini"	
     * @param string $config_container. This describes the main header section of the iniFile 
     * @param array $settings. The values that need initializing
     * @param string $iniPath. File path
     * @param string $iniName. File name
     */
    public function createConfig($config_container=false,$settings,$iniPath=false,$iniName)
    {
    	try {
	    	//define the main header setting
	   		if (isset($config_container)) {
	   			$Section =& new Config_Container("section", "Settings");
	   		}else{
	   			$Section =& new Config_Container("section", "{$config_container}");
	
	   		}
	    	
	        // create variables/values
	       if (is_array($settings)) {
	        	foreach ($settings as $key => $value) {
	        		$Section->createDirective("{$key}", "{$value}");
	        	}
	         } 
	       
		    // reassign root
			$this->objConf->setRoot($Section);
	
			// write configuration to file
			$result = $this->objConf->writeConfig("{$iniPath}"."{$iniName}", "INIFile");
			
			if ($result==false) {
				throw new Exception($this->Text('word_read_fail'));
			}
    	}catch (Exception $e){
    		$this->errorCallback($e);
    	}
    }
   
    /**
     * Method to parse config options.
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
    public function readConfig($config=false,$property='PHPArray',$Path,$FileName)
    {

    	try {
    		
              $this->_root =& $this->objConf->parseConfig("{$path}"."{$FileName}",'IniFile');
			
    		if (PEAR::isError($this->_root)) {
    			return false;
    		}
    	    	
    		return $this->_root;
    	}catch (Exception $e)
    	{
    		 $this->errorCallback ('Caught exception: '.$e->getMessage());
    	}

    }
    /**
     * Get all items in the ini file 
     *
     * @return array
     */
    public function getAll()
    {
    	
	    	if ($this->_root==false) {
	    		$this->readConfig();
	    		return $this->_root->toArray;
	    	}else{
	    		return $this->_root->toArray;
	    	}
    	
    }
    /**
     * Delete an item in the iniFile
     *
     * @param string $values
     * @param string $index
     * @return Boolean
     */
    public function delete($values,$index)
    {
    	if (is_array ( $values ) ) {
     		unset ( $values['root']['Settings'][$index] );
     		array_unshift ( $values, array_shift ( $values ) );
     		$this->writeConfig($values);
    		return true;
     	}
   		else {
     		return false;
     	}
   		
    }
    /**
     * Method to wirte config options.
     * For use when writing configuration options
     *
     * @access public
     * @param string values to be saved
     * @param string property used to set property value of incoming config string
     * $property can either be:
     * 1. PHPArray
     * 2. XML
     * @return boolean  TRUE for success / FALSE fail .
     *
     */
    public function writeConfig($config_container=false,$values,$property='IniFile',$Path=false,$FileName)
    {
    	try {
    		
    		
    		//define the main header setting
	   		if (isset($config_container)) {
	   			$Section =& new Config_Container("section", "Settings");
	   		}else{
	   			$Section =& new Config_Container("section", "{$config_container}");
	
	   		}
	    	// reassign root
			$this->objConf->setRoot($Section);
            $this->_root =& $this->objConf->parseConfig($values,'PHPArray');
			if (($Path !== false) && (file_exists($Path.$FileName))) {
			  	unlink($Path.$FileName);		
			}  		
    		$this->objConf->writeConfig("{$Path}"."{$FileName}",$property);

    		$this->readConfig();
    		return true;
    	}catch (Exception $e)
    	{
    		 $this->errorCallback ('Caught exception: '.$e->getMessage());
    		 exit();
    	}

    }
   
    /**
    * Method to get a system configuration parameter.
    *
    * @var string $pvalue The value code of the config item
    * @var string $pname The name of the parameter being set, use UPPER_CASE
    * @return  string $value The value of the config parameter
    */
    public function getItem($pname, $pvalue,$Directive)
    {
    	try {
    			//Read conf
    			if ($this->_root==false) {
    				$read = $this->readConfig();
    			}
    			if ($read==FALSE) {
    				return $read;
    			}

               //Lets get the parent node section first

        		$Settings =& $this->_root->getItem("section", "{$Directive}");
        		//Now onto the directive node
        		//check to see if one of them isset to search by
        		if(isset($pname)){
        		  $this->SettingsDirective =& $Settings->getItem("directive", "{$pname}");
        		  $pname = $this->SettingsDirective->getContent();
       			  return $pname;
        		}
        		if(isset($pvalue)){
        			$this->SettingsValue =& $Settings->getItem("directive", "{$pvalue}");
        			$pvalue = $this->SettingsValue->getContent();
       				return $pvalue;
        		}
        		

    	}catch (Exception $e){
    		$this->errorCallback ('Caught exception: '.$e->getMessage());
    		exit();
    	}
    } #function getItem
    /**
    * Method to get a system configuration parameter.
    *
    * @var string $pvalue The value code of the config item
    * @var string $pname The name of the parameter being set, use UPPER_CASE
    * @return  string $value The value of the config parameter
    */
    public function setItem($pname, $pvalue,$Directive)
    {
    	try {
    			//Read conf
    			if ($this->_root==false) {
    				$read = $this->readConfig();
    			}
    			
               //Lets get the parent node section first
				
        		$Settings =& $this->_root->getItem("section", "{$Directive}");
        		
        		//Now onto the directive node
        		//check to see if one of them isset to search by
        		  $this->SettingsDirective =& $Settings->getItem("directive", "{$pname}");
        		  $this->SettingsDirective->setContent($pvalue);
        		  $result =$this->objConf->writeConfig();
       			  return $result;
        		
        		

    	}catch (Exception $e){
    		$this->errorCallback ('Caught exception: '.$e->getMessage());
    		exit();
    	}
    } #function setItem
    /**
    * The error callback function, defers to configured error handler
    *
    * @param string $error
    * @return void
    * @access public
    */
    public function errorCallback($exception)
    {
    	echo customException::cleanUp($exception);
    	exit();
    }


} #end of class
?>