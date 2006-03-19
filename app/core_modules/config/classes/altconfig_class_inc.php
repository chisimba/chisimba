<?PHP
/**
 * Adaptor Pattern around the PEAR::Config Object
 * This class will provide the kng configuration to Engine
 *
 * @author Prince Mbekwa,Paul Scott
 * @package config
 */
//grab the pear::Config properties
// include class
include("Config.php");
require_once('../classes/core/errorhandler_class_inc.php');

class altconfig
{
	/**
     * The pear config object 
     *
     * @access public
     * @var string
    */
    protected $_objPearConfig;
    /**
     * The root object for configs read 
     *
     * @access private
     * @var string
    */
    protected $_root;
    /**
     * The options value for altconfig read / write
     *
     * @access private
     * @var string
    */
    protected $_options;
    
    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var string
    */
    public $_errorCallback;

    public function __construct()
    {
        // instantiate object
        $this->_objPearConfig = new Config();
        if(!isset($this->_root))
        {
        	$this->readConfig('','XML');
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
    protected function readConfig($config,$property)
    {
    	// read configuration data and get reference to root
    	$this->_root =& $this->_objPearConfig->parseConfig("../config/Config.xml",$property);
		if ($this->_root==TRUE) {
			$this->errorCallback('Can not find file Config.xml. Run the installer to recitify');
			return false; 
		}else{
			return true;
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
    public function writeConfig($values,$property)
    {
    	// set xml root element
		$this->_options = array('name' => 'Settings');
		$this->_root =& $this->_objPearConfig->parseConfig($values,"PHPArray");
		$this->_objPearConfig->writeConfig("../config/Config.xml",$property, $this->_options);
		$this->readConfig('','XML');
    	return true;
		
    }
    /**
     * Method to read sysconfig Properties options.
     * For use when reading sysconfig Properties options
     *
     * @access public
     * @param string property used to set property value of incoming config string
     * $property can either be:
     * 1. PHPArray
     * 2. XML
     * @return boolean  TRUE for success / FALSE fail .
     * 
     */
    public function readProperties($property)
    {
    	// read configuration data and get reference to root
    	$this->_root =& $this->_objPearConfig->parseConfig("../config/sysconfig_properties.xml",$property);
		if ($this->_root==TRUE) {
			$this->errorCallback('Can not find file sysconfig_properties');
			return false; 
		}else{
			return true;
		}
    	    	
    }
    /**
     * Method to write sysconfig Properties options.
     * For use when writing sysconfig Properties options
     *
     * @access public
     * @param PHParray $propertyValues which consists of : 
     * @var string $pmodule The module code of the module owning the config item
	 * @var string $pname The name of the parameter being set, use UPPER_CASE
	 * @var string $plabel A label for the config parameter, usually a language string
	 * @var string $value The value of the config parameter
	 * @var boolean $isAdminConfigurable TRUE | FALSE Whether the parameter is admin configurable or not
     * @param string property used to set property value of incoming config string
     * $property can either be:
     * 1. PHPArray
     * 2. XML
     * @return boolean  TRUE for success / FALSE fail .
     * 
     */
    public function writeProperties($propertyValues,$property)
    {
    	// set xml root element
		$this->_options = array('name' => 'sysConfigSettings');
		$this->_root =& $this->_objPearConfig->parseConfig($propertyValues,"PHPArray");
		$this->_objPearConfig->writeConfig("../config/sysconfig_properties.xml",$property, $this->_options);
		
    	if ($this->_objPearConfig==TRUE) {
			$this->errorCallback('Can not write file sysconfig_properties');
			return false; 
		}else{
			return true;
		}
    	    	
    }
    
    /**
    * The property get name of the getSiteName
    *@access public
    *@return the name of the site as string
    */
    public function getSiteName()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITENAME");
        //finally unearth whats inside
        $siteName = $SettingsDirective->getContent();
        return $siteName;
        // KEWL_SITENAME;
    }
    /**
    * The property set name of the getSiteName
    *@access public 
    *@param value of the change to be made
    *@return bool true / false
    */
    public function setSiteName($value)
    {
        //return $this->getValue("sitename");
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITENAME");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        
    }
    /**
    * Get short name of the institutionShortName
    *@access public
    * @return the short name of the site as string
    */
    public function getinstitutionShortName()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_INSTITUTION_SHORTNAME");
        //finally unearth whats inside
        $institutionShortName = $SettingsDirective->getContent();
        return $institutionShortName;
        // KEWL_INSTITUTION_SHORTNAME;
    }
    /**
    * Set short name of the institutionShortName
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setinstitutionShortName($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITENAME");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        
    }
    
    /**
    * Get name of the institution
    * @access public
    * @return the short name of the institution as string
    */
    public function getinstitutionName()
    { 
    	//Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_INSTITUTION_NAME");
        //finally unearth whats inside
        $institutionName = $SettingsDirective->getContent();
        return $institutionName;
        // KEWL_INSTITUTION_NAME;
    }
     /**
    * Set name of the institution
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setinstitutionName($value)
    {
       
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_INSTITUTION_NAME");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_INSTITUTION_NAME;
    }
    /**
    * The email address of the website
    *@access public
    * @return the email address for the site as string
    */
    public function getsiteEmail()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITEEMAIL");
        //finally unearth whats inside
        $getsiteEmail = $SettingsDirective->getContent();
        return $getsiteEmail;
        // KEWL_SITEEMAIL;
    }
    /**
    * The email address of the website
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setsiteEmail($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITEEMAIL");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_SITEEMAIL;
    }
    
    /**
    * The script timeout
    * @access public
    * @return the script timout in seconds
    */
    public function getsystemTimeout()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SYSTEMTIMEOUT");
        //finally unearth whats inside
        $getsystemTimeout = $SettingsDirective->getContent();
        return $getsystemTimeout;
        // KEWL_SYSTEMTIMEOUT;
    }
    
    /**
    * The script timeout
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setsystemTimeout($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SYSTEMTIMEOUT");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_SYSTEMTIMEOUT;
    }
    /**
    * The URL root of the site
    * @access public
    * @return the the site root, normally / as string
    */
    public function getsiteRoot()
    {
         //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITE_ROOT");
        //finally unearth whats inside
        $getsiteRoot = $SettingsDirective->getContent();
        return $getsiteRoot;
        // KEWL_SITE_ROOT;
    }
    /**
    * The URL root of the site
   	*@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setsiteRoot($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITE_ROOT");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_SITE_ROOT;
    }
    
    /**
    * The folder name of the default skin
    * @access public
    * @return the default skin name (normally default)
    * leading and trailing forward slash (/)  as string
    */
    public function getdefaultSkin()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_SKIN");
        //finally unearth whats inside
        $getdefaultSkin = $SettingsDirective->getContent();
        return $getdefaultSkin;
        // KEWL_DEFAULT_SKIN;
    }
    /**
    * The folder name of the default skin
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setdefaultSkin($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_SKIN");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_DEFAULT_SKIN;
    }
    /**
    * The name of the default language (normally english)
    * @access public
    * @return the name of the default language as string
    */
    public function getdefaultLanguage()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_LANGUAGE");
        //finally unearth whats inside
        $getdefaultLanguage = $SettingsDirective->getContent();
        return $getdefaultLanguage;
        // KEWL_DEFAULT_LANGUAGE;
    }
    /**
    * The name of the default language (normally english)
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setdefaultLanguage($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_LANGUAGE");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_DEFAULT_LANGUAGE;
    }
    /**
    * The abbreviation of the default language (normally EN)
    * @access public
    * @return the abbreviation of the default language as string
    */
    public function getdefaultLanguageAbbrev()
    {
         //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_LANGUAGE_ABBREV");
        //finally unearth whats inside
        $getdefaultLanguageAbbrev = $SettingsDirective->getContent();
        return $getdefaultLanguageAbbrev;
        // KEWL_DEFAULT_LANGUAGE_ABBREV;
    }
    /**
    * The abbreviation of the default language (normally EN)
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setdefaultLanguageAbbrev($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_LANGUAGE_ABBREV");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_DEFAULT_LANGUAGE_ABBREV;
    }
    /**
    * The default extension for banners (jpg, gif, png)
    * @access public
    * @return default extension for banners (jpg, gif, png) as string
    */
    public function getbannerExtension()
    {
         //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_BANNER_EXT");
        //finally unearth whats inside
        $getbannerExtension = $SettingsDirective->getContent();
        return $getbannerExtension;
        // KEWL_BANNER_EXT;
    }
    /**
    * The default extension for banners (jpg, gif, png)
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setbannerExtension($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_BANNER_EXT");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_BANNER_EXT;
    }
    /**
    * The default site root path as string
    * @access public
    * @return default site root path as string
    */
    public function getsiteRootPath()
    {
         //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITEROOT_PATH");
        //finally unearth whats inside
        $getsiteRootPath = $SettingsDirective->getContent();
        return $getsiteRootPath;
        // KEWL_SITEROOT_PATH;
    }
    /**
    * The default site root path as string
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setsiteRootPath($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_SITEROOT_PATH");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_SITEROOT_PATH;
    }
    /**
    *
    * The default layout template name as string
    * @access public
    * @return string Name of default layout template
    */
    public function getdefaultLayoutTemplate()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_LAYOUT_TEMPLATE");
        //finally unearth whats inside
        $getsiteRootPath = $SettingsDirective->getContent();
        return $getsiteRootPath;
        // KEWL_DEFAULT_LAYOUT_TEMPLATE;
    }
    /**
    *
    * The default layout template name as string
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setdefaultLayoutTemplate($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_LAYOUT_TEMPLATE");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_DEFAULT_LAYOUT_TEMPLATE;
    }
    /**
    * The default Page template name as string
    * @access public
    * @return string Name of default page template
    */
    public function getdefaultPageTemplate()
    {

        // Check if Parameter is set
        if ($this->checkIfSet('KEWL_DEFAULT_PAGE_TEMPLATE')) {
            // Get Value if it is set
            //Lets get the parent node section first 
        	$Settings =& $this->_root->getItem("section", "Settings");
        	//Now onto the directive node
        	$SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_PAGE_TEMPLATE");
        	//finally unearth whats inside
        	$template = $SettingsDirective->getContent();
            
            // Prevent system from using KEWL_DEFAULT_PAGE_TEMPLATE as a default value
            if (strtoupper($template) == 'KEWL_DEFAULT_PAGE_TEMPLATE') {
                // change to default
                $this->setdefaultPageTemplate('default_page_tpl.php');
                $this->readConfig('','XML');
                $Settings =& $this->_root->getItem("section", "Settings");
	        	//Now onto the directive node
	        	$SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_PAGE_TEMPLATE");
                return $SettingsDirective->getContent();
            } else { // return given template
                return $template;
            }
        } else {
            // Insert Parameter if not set
            $this->setdefaultPageTemplate('default_page_tpl.php');
            $this->readConfig('','XML');
            $Settings =& $this->_root->getItem("section", "Settings");
            //Now onto the directive node
            $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_PAGE_TEMPLATE");
            return $SettingsDirective->getContent();
        }
    }
     /**
    *
    * The default layout template name as string
    *@access public
   	*@param value of the change to be made
    *@return bool true / false
    */
    public function setdefaultPageTemplate($value)
    {
    	$Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_DEFAULT_PAGE_TEMPLATE");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
    	
    }
    /**
    * Whether to allow users to register themselves
    * @access public
    * @param value to be changed
    * @return TRUE or FALSE
    */
    public function setallowSelfRegister($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_ALLOW_SELFREGISTER");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_ALLOW_SELFREGISTER;
    }
    /**
    * Whether to allow users to register themselves
    *
    * @return TRUE or FALSE
    */
    public function getallowSelfRegister()
    { 
    	//Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_ALLOW_SELFREGISTER");
        //finally unearth whats inside
        $getallowSelfRegister = $SettingsDirective->getContent();
        return $getallowSelfRegister;
        // KEWL_ALLOW_SELFREGISTER;
    }
    /**
    * Returns name of post-login module
    * @access public
    * @return name of post-login module
    */
    public function getdefaultModuleName()
    {
        //Lets get the parent node section first 
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_POSTLOGIN_MODULE");
        //finally unearth whats inside
        $getdefaultModuleName = $SettingsDirective->getContent();
        return $getdefaultModuleName;
        // KEWL_POSTLOGIN_MODULE;
    }
    /**
     * @access public
     * @param value to be changed
     * @return TRUE or FALSE
    */     
    
    public function setdefaultModuleName($value)
    {
         $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_POSTLOGIN_MODULE");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
    }

    /**
     * Method to get Value of LDAP
     * @access PUBLIC
     * @Returns whether LDAP functionality should be used
    */
    public function getuseLDAP()
    {
        if (function_exists("ldap_connect")){
        	//Lets get the parent node section first 
	        $Settings =& $this->_root->getItem("section", "Settings");
	        //Now onto the directive node
	        $SettingsDirective =& $Settings->getItem("directive", "LDAP_USED");
	        //finally unearth whats inside
	        $getuseLDAP = $SettingsDirective->getContent();
	        return $getuseLDAP;
        } else {
            return FALSE;
        }
    }
    /**
     * Method to set LDAP as used
     * @access public
     * @param value to be changed
     * @return TRUE or FALSE
    */   
    public function setuseLDAP($value)
    {
    	 $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "LDAP_USED");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
    	
    }

    /**
    * Returns the country 2-letter code
    * Defaults to 'ZA'
    * @access public
    * @returns string $code
    */
    public function getCountry()
    {
    	//Lets get the parent node section first 
    	$Settings =& $this->_root->getItem("section", "Settings");
    	//Now onto the directive node
    	$SettingsDirective =& $Settings->getItem("directive", "KEWL_SERVERLOCATION");
    	//finally unearth whats inside
    	$getCountry = $SettingsDirective->getContent();
    	        
        if ($getCountry==NULL){
            $getCountry='ZA';
            
        }
        return $getCountry;
    }
   

    /**
    * *---------------- FILE SYSTEM PROPERTIES -----------*
    */

    /**
    * Returns the base path for all user files
    * @access public
    * @return base path for user files
    */
       
    public function getcontentBasePath()
    {
        //Lets get the parent node section first 
    	$Settings =& $this->_root->getItem("section", "Settings");
    	//Now onto the directive node
    	$SettingsDirective =& $Settings->getItem("directive", "KEWL_CONTENT_BASEPATH");
    	//finally unearth whats inside
    	$getcontentBasePath = $SettingsDirective->getContent();
    	
    	return $getcontentBasePath;
        // KEWL_CONTENT_BASEPATH;
    }
    
    /**
    * Returns the path for content files
    * @access public
    */
    public function getcontentPath()
    {
        //Lets get the parent node section first 
    	$Settings =& $this->_root->getItem("section", "Settings");
    	//Now onto the directive node
    	$SettingsDirective =& $Settings->getItem("directive", "KEWL_CONTENT_PATH");
    	//finally unearth whats inside
    	$getcontentPath = $SettingsDirective->getContent();
    	
    	return $getcontentPath;
    }
    /**
     * Set the path for content files
     * @access public
     * @param value to be changed
     * @return TRUE or FALSE
    */   
    
    public function setcontentPath($value)
    {
         $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_CONTENT_PATH");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_CONTENT_PATH;
    }

    /**
    * Returns the root path for content files
    * @access public
    * @return content root path
    */
    public function getcontentRoot()
    {
       //Lets get the parent node section first 
    	$Settings =& $this->_root->getItem("section", "Settings");
    	//Now onto the directive node
    	$SettingsDirective =& $Settings->getItem("directive", "KEWL_CONTENT_PATH");
    	//finally unearth whats inside
    	$getcontentRoot = $SettingsDirective->getContent();
    	
    	return $getcontentRoot;
        // KEWL_CONTENT_PATH;
    }
    /**
    * Set the root path for content files
    * @access public
    * @param value to be changed
    * @return TRUE or FALSE
    */   
    public function setcontentRoot($value)
    {
        $Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_CONTENT_PATH");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
        // KEWL_CONTENT_PATH;
    }
    /**
     * Gets error reporting Setting
     * 
     * @access public
     * @return geterror_reporting setting
     */

    public function geterror_reporting()
    {
    	//Lets get the parent node section first 
    	$Settings =& $this->_root->getItem("section", "Settings");
    	//Now onto the directive node
    	$SettingsDirective =& $Settings->getItem("directive", "KEWL_ERROR_REPORTING");
    	//finally unearth whats inside
    	$geterror_reporting = $SettingsDirective->getContent();
    	
    	return $geterror_reporting;
        
    }
    /**
    * Set  error reporting Settings
    * @access public
    * @param value to be changed
    * @return TRUE or FALSE
    */   
    
    public function seterror_reporting($value)
    {
       	$Settings =& $this->_root->getItem("section", "Settings");
        //Now onto the directive node
        $SettingsDirective =& $Settings->getItem("directive", "KEWL_ERROR_REPORTING");
        //finally save value
        $SettingsDirective->setContent($value);
        $bool = $this->_objPearConfig->writeConfig(); 
        return $bool;
    }

    /**
    * The error callback function, defers to configured error handler
    *
    * @param string $error
    * @return void
    * @access public
    */
    protected function errorCallback($error)
    {
    	$this->_errorCallback = new ErrorException($error,1,1,'altconfig_class_inc.php');
        return $this->_errorCallback;
    }


}
?>