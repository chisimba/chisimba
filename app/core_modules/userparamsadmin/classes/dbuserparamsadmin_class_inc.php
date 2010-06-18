<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}


/**
*
* Model class for writing the user paramters as used by the userparamsadmin
* module into an INI file. The class provides data access features for
* administering the list  of user parameters stored in this INI file. The
* INI file is stored in  usrfiles/users/<userid>/userconfig_properties.ini
*
* English translation of internal documentation from Klingon by Lt Worf.
*
* @author Prince Mbekwa
* @todo userconfig properties' set and get
* @todo module config (especially from module admin)
* @package config
*/
require_once('Config.php');
class dbuserparamsadmin extends object
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
     * Insert array()
     *
     * @var array
     */

	protected  $insert =array();
	/**
     * Update array holder
     *
     * @var array
     */

	protected $update = array();
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

	public $file;

	//Initialize class
	function init()
	{
		//pull in our objects
		$this->objConf = new Config();
		$this->file = $this->getObject('mkdir','files');
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objUser = $this->getObject('user', 'security');
		$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$this->uid = $this->objUser->userId();
		$this->readConfig();
	}

     /**
      *
      * Set the userId to a value other than that of the
      * logged in user.
      *
      * @param stri8ng $userId The userId to set
      * @return VOID
      * @access public
      *
      */
     public function setUserId($userId)
     {
         $this->uid = $userId;
     }

    /**
     *
     * Change the userId based on the username passed in
     * @param string $un The username
     * @return VOID
     * @access public
     *
     */
    public function setUid($un = NULL, $id = NULL)
    {
       // if(is_null($un)) {
       //     $this->uid = $id;
        //}
        //else {
            $this->uid = $this->objUser->getUserId($un);
        //}
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
				throw new customException($this->objLanguage->languageText("mod_userparamsadmin_writeerror", "userparamsadmin"));
				log_debug($this->objLanguage->languageText("mod_userparamsadmin_writeerror", "userparamsadmin"));
			}
		} catch (customException $e){
			customException::cleanUp();
			exit;
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
	public function readConfig($config=false,$property='PHPArray')
	{
		try {
			// read configuration data and get reference to root
			$path = $this->objConfig->getcontentBasePath();
			$path .=  "users/";
			$path .= $this->uid .'/';
			if (!file_exists($path.'userconfig_properties.ini')) {
				$values = array(
				'Google API key'=>'',
				'ICQ number'=>'',
				'Yahoo ID'=>'',
				'Skype ID'=>'',
				'MSN ID' =>''
				);
				$result = $this->file->mkdirs($path);
				if ($result==true) {
					$result = $this->createConfig('Settings',$values,$path,'userconfig_properties.ini');
				}
			}
			$this->_root =& $this->objConf->parseConfig("{$path}".'userconfig_properties.ini','IniFile');
			if (PEAR::isError($this->_root)) {
				throw new customException($this->objLanguage->languageText("mod_userparamsadmin_cannotreadfile", "userparamsadmin"));
			}
			return $this->_root;
		} catch (customException $e)  {
			customException::cleanUp();
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
	public function writeProperties($mode, $userId, $pname, $ptag)
	{
		try {
			// set xml root element
			$this->_options = array('name' => 'userConfigSettings');
			$id = $this->getParam('id', NULL);
			// if edit use update
			if ($mode=="edit") {
				$this->setItem($pname,$ptag);
			}#if
			// if add use insert
			if ($mode=="add") {
				$this->insert = array(
				$pname => $ptag,
				);
				$this->writeConfig($this->insert);
			}
			if ($this->objConf != TRUE) {
				throw new customException($this->objLanguage->languageText("mod_userparamsadmin_cannotreadfile", "userparamsadmin"));
			}else{
				return TRUE;
			}
		}catch (customException $e){
			customException::cleanUp();
			exit();
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
	public function writeConfig($values,$property='IniFile')
	{
		// set xml root element
		try {
			$this->_options = array('name' => 'Settings');
			// read configuration data and get reference to root
			$path = $this->objConfig->getcontentBasePath();
			$path .=  "users/". $this->objUser->userId().'/';
			if ($this->_root == false) {
				$this->readConfig();
			}
			$this->_root =& $this->objConf->parseConfig($values,'PHPArray');
			if (($path !== false) && (file_exists($path.'userconfig_properties.ini'))) {
				unlink($path.'userconfig_properties.ini');
			}
			$this->objConf->writeConfig("{$path}".'userconfig_properties.ini',$property, $this->_options);
			$this->readConfig();
			return TRUE;
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit();
		}
	}

    /**
     *
     * A method to return the value of the parameter requested
     * @param string $pname The parameter code for the parameter to return
     * @return string the value of the parameter
     */
    public function getValue($pname)
    {

        try {
            $Settings =& $this->_root->getItem("section", "Settings");
            $this->SettingsDirective =& $Settings->getItem("directive", "{$pname}");
            if ($this->SettingsDirective) {
                $pname = $this->SettingsDirective->getContent();
            } else {
                $pname=NULL;
            }
            return $pname;
        } catch (Exception $e) {
            customException::cleanUp();
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
	public function getItem($pname, $pvalue)
	{
        die($pname . $pvalue);
		try {
			//Read conf
			$read = NULL;
			if ($this->_root == NULL) {
				$read = $this->readConfig();
			}
			if ($read == FALSE) {
                return $read;
			}
			//Lets get the parent node section first
			$Settings =& $this->_root->getItem("section", "Settings");
			//Now onto the directive node
			//check to see if one of them isset to search by
			if(isset($pname)) {
				$this->SettingsDirective =& $Settings->getItem("directive", "{$pname}");
				$pname = $this->SettingsDirective->getContent();
				return $pname;
			}
			if(isset($pvalue)){
				$this->SettingsValue =& $Settings->getItem("directive", "{$pvalue}");
				$pvalue = $this->SettingsValue->getContent();
				return $pvalue;
			}
		} catch (Exception $e) {
			customException::cleanUp();
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
	public function setItem($pname, $pvalue)
	{
		try {
			// Read conf
			$read = NULL;
			if ($this->_root == NULL) {
				$read = $this->readConfig();
			}
			//Lets get the parent node section first
			$Settings =& $this->_root->getItem("section", "Settings");
			//Now onto the directive node
			//check to see if one of them isset to search by
			$this->SettingsDirective = $Settings->getItem("directive", "{$pname}");
			if ($this->SettingsDirective) {
				$this->SettingsDirective->setContent($pvalue);
				$result = $this->objConf->writeConfig();
			} else {
				$this->insert = array($pname => $pvalue);
				$result = $this->writeConfig($this->insert);
			}
			return $result;
		} catch (Exception $e) {
			customException::cleanUp();
			exit();
		}
	}

    /**
     *
     * The module author feels he is too important to add comments
     *
     */
	public function delete($pname)
	{
		try {
			$path = $this->objConfig->getcontentBasePath();
			$path .=  "users/";
			$path .= $this->objUser->userId().'/';
			$this->_root = $this->objConf->parseConfig("{$path}".'userconfig_properties.ini','inifile', array('name' => 'conf'));
			//Lets get the parent node section first
			$Settings =& $this->_root->getItem("section", "Settings");
			//Now onto the directive node
			//check to see if one of them isset to search by
			$directive = $Settings->getItem("directive", "{$pname}");
			$directive->removeItem();
			//var_dump($directive); die();
			$result =$this->objConf->writeConfig();
			return $result;
		} catch (Exception $e) {
			customException::cleanUp();
			exit();
		}
	}


   /** Added by jameel for the websearch module
    * Method to check if a configuration parameter is set
    *
    * @var string $module The module code of the module owning the config item
    * @var string $name The name of the parameter being set
    */
    public function checkIfSet($pname, $userId=NULL)
    {

        if ($pname >= 1 && $userId >= 1 ) {
            return true;
        } else {
            return false;
       } #if
    } #function checkIfSet
}
