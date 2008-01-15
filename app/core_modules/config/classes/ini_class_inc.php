<?php

/**
 * INI File manipulation class
 * 
 * File to work with generated ini files in Chisimba
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   config
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
 * Config Object
 */
require_once('Config.php');

/**
 * INI File manipulation class
 * 
 * File to work with generated ini files in Chisimba
 * 
 * @category  Chisimba
 * @package   config
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
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
     * @var    string 
    */
    protected $_property;
    /**
     * The options value for altconfig read / write
     *
     * @access private
     * @var    string 
    */
    protected $_options;

    /**
     * The sysconfig object for sysconfig storage
     *
     * @access private
     * @var    array  
     */
    protected $_sysconfigVars;

    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var    string
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
     * @var    string 
    */
    protected $_root = false;
    /**
     * languagetext object
     *
     * @var object
     */
    public $Text;
    
    //Initialize class


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    function init() {
    	    //pull in our objects
    		$this->objConf = new Config();
    		$this->sysconf =  $this->getObject('altconfig','config');
            $this->objUser =  $this->getObject("user", "security");
             $this->Text = $this->newObject('language','language');
            $this->objConfig = $this->sysconf;
            
    }
     /**
     * Method to create initial IniFile config elements. This method will create 
     * blank ini params.
     * @example
     *          	   $config_container ="MAIL"/"Settings"	 
     *          $settings = array("name"=>"Bruce Banner","email"=> "hulk@angry.green.guy")
     *          	   $iniPath = "/config/"
     *          	   $iniName ="my.ini"	
     * @param   string $config_container. This describes the main header section of the iniFile 
     * @param   array  $settings.         The values that need initializing
     * @param   string $iniPath.          File path
     * @param   string $iniName.          File name
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
     * @param  string    $config   xml file or PHPArray to parse
     * @param  string    $property used to set property value of incoming config string
     *                             $property can either be:
     *                             1. PHPArray
     *                             2. XML
     * @return boolean   True/False result.
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
    		 $this->errorCallback ($this->Text('word_caught_exception').$e->getMessage());
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
     * @param  string  $values
     * @param  string  $index 
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
     * @param  string  values   to be saved
     * @param  string  property used to set property value of incoming config string
     *                          $property can either be:
     *                          1. PHPArray
     *                          2. XML
     * @return boolean TRUE for success / FALSE fail .
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
    		 $this->errorCallback ($this->Text('word_caught_exception').$e->getMessage());
    		 exit();
    	}

    }
   
    /**
    * Method to get a system configuration parameter.
    *
    * @var    string $pvalue The value code of the config item
    * @var    string $pname The name of the parameter being set, use UPPER_CASE
    * @return string $value The value of the config parameter
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
    		$this->errorCallback ($this->Text('word_caught_exception').$e->getMessage());
    		exit();
    	}
    } #function getItem
    /**
    * Method to get a system configuration parameter.
    *
    * @var    string $pvalue The value code of the config item
    * @var    string $pname The name of the parameter being set, use UPPER_CASE
    * @return string $value The value of the config parameter
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
    		$this->errorCallback ($this->Text('word_caught_exception').$e->getMessage());
    		exit();
    	}
    } #function setItem
    
    public function createAdmConfig($servarray)
    {
    	//check for the directory structure
		if(!file_exists($this->objConfig->getcontentBasePath().'adm/'))
		{
			mkdir($this->objConfig->getcontentBasePath().'adm/', 0777);
		}
		// write the server list file
		$cfile = $this->objConfig->getcontentBasePath().'adm/adm.xml';
		if(!file_exists($cfile))
		{
			$conf =& new Config_Container('section', 'adm'); //$servarray['name']);
			$conf_serv =& $conf->createSection($servarray['name']);
			$conf_serv->createDirective('servername', $servarray['name']);
			$conf_serv->createDirective('serverapiurl', $servarray['url']);
			$conf_serv->createDirective('serveremail', $servarray['email']);
			$conf_serv->createDirective('regtime', date('r'));
			
			$config = new Config();
			$config->setRoot($conf);
			// write the container to an XML document
  			$config->writeConfig($cfile, 'XML');
		}
		else {
			// update the xml with the new server
			$root =& $config->parseConfig($cfile, 'XML', array('name' => 'adm'));
			log_debug($root->toArray());
			$conf_serv =& $root->createSection($servarray['name']);
			$conf_serv->createDirective('servername', $servarray['name']);
			$conf_serv->createDirective('serverapiurl', $servarray['url']);
			$conf_serv->createDirective('serveremail', $servarray['email']);
			$conf_serv->createDirective('regtime', date('r'));
			
			$config = new Config();
			$config->setRoot($root);
			// write the container to an XML document
  			$config->writeConfig($cfile, 'XML');
		}
		
    }
    /**
    * The error callback function, defers to configured error handler
    *
    * @param  string $error
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