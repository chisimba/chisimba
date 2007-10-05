<?php

/**
 * Language Config class for chisimba. 
 * 
 * Provides language setup properties,
 * the main one being to call the PEAR Translation2 object and setup
 * all language table layouts.
 * Setup all locales
 * Allow MDB2 to take over language Item maintainance
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
 * @package   language
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007 Prince Mbekwa
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
/* -------------------- LANGUAGE CONFIG CLASS ----------------*/


/**
 * Description for define
 */
define('TABLE_PREFIX', 'tbl_');

/**
 * Language Config class for chisimba. Provides language setup properties,
 * the main one being to call the PEAR Translation2 object and setup
 * all language table layouts.
 * Setup all locales
 * Allow MDB2 to take over language Item maintainance
 *
 * @copyright (c) 2006 University of the Western Cape AVOIR
 * @Version   0.1
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 *            
 */
class languageConfig extends object
{
	/**
     * Public variable to hold the new language config object
     * @access public
     * @var    string
     */
    public $lang;

    /**
     * Public variable to hold the site config object
     * @access private
     * @var    string 
     */
    private $_siteConf;

    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var    string
     */
    public $_errorCallback;
    
    public $objMemcache;
    
    public $cacheTTL = 3600;

	/**
     * Constructor for the languageConf class.
     *
     */

	public function init(){
		try {
			if(extension_loaded('memcache'))
			{
				require_once 'classes/core/chisimbacache_class_inc.php';
				$this->objMemcache = TRUE;
			}
			require_once ('Translation2.php'); //$this->getPearResource('Translation2.php');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			die();
		}
	}

	/**
     * Setup for the languageConf class.
     * tell Translation2 about our db-tables structure,
     * setup primary language
     * setup the group of module strings we want to fetch from
     * add a Lang decorator to provide a fallback language
     * add another Lang decorator to provide another fallback language,
	 * in case some strings are not translated in all languages that exist in KINKY
	 *
	 * @param void
	 * @return void
	 * @access public
     */
	public function setup()
	{
		
		try {
			//Define table properties so that MDB2 knows about them
			$params = array(
			'langs_avail_table' => TABLE_PREFIX.'langs_avail',
			'lang_id_col'     => 'id',
			'lang_name_col'   => 'name',
			'lang_meta_col'   => 'meta',
			'lang_errmsg_col' => 'error_text',
			'strings_tables'  => array(
								'en' => TABLE_PREFIX.'en',

									),
			'string_id_col'      => 'id',
			'string_page_id_col' => 'pageID',
			'string_text_col'    => '%s'  //'%s' will be replaced by the lang code
			);
			$driver = 'MDB2';

			//instantiate class
			$this->_siteConf = $this->getObject('altconfig','config');
			$dsn = $this->_parseDSN(KEWL_DB_DSN); //$this->_siteConf->getDsn());
			$this->lang = &Translation2::factory($driver, $dsn, $params);
			if (PEAR::isError($this->lang)) {
				// echo $this->lang->getMessage(); die();
				throw new customException($this->lang->getMessage());
			}
			$this->lang =& $this->lang->getDecorator('CacheMemory');
			
			$this->lang =& $this->lang->getDecorator('SpecialChars');
			// control the charset to use
			$this->lang->setOption('charset', 'iso-8859-1');
			// add a UTF-8 decorator to automatically decode UTF-8 strings
			$this->lang =& $this->lang->getDecorator('UTF8');
			// add a default text decorator to deal with empty strings
			$this->lang = & $this->lang->getDecorator('DefaultText');
			$this->lang->setOption('prefetch', true); //default value is true
			if (PEAR::isError($this->lang)) {
				echo $this->lang->getMessage(); die();
			//	throw new Exception('Could not load Translation class');
			}
			// set primary language
			if(!is_object($this->lang)) throw new Exception('Translation class not loaded');
			$this->lang->setLang("en");
			
			// set the group of strings you want to fetch from
			$this->lang->setPageID('defaultGroup');
			$this->caller = $this->moduleName;
			// add a Lang decorator to provide a fallback language
			$this->lang =& $this->lang->getDecorator('Lang');
			$this->lang->setOption('fallbackLang', 'en');
			$this->lang->getDecorator('CacheLiteFunction');
			$this->lang->setOption('cacheDir', 'cache/');
			$this->lang->setOption('lifeTime', 86400);
			// replace the empty string with its stringID

			return $this->lang;
			//}
		}catch (Exception $e){
                    // Alterations by jsc on advice from paulscott
			//$this->errorCallback ('Caught exception: '.$e->getMessage());
                        echo $e->getMessage();
    		exit();

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
    	$this->_errorCallback = new ErrorException($exception,1,1,'languageConfig_class_inc.php');
        return $this->_errorCallback;
    }

    /**
     * Method to parse the DSN from a string style DSN to an array for portability reasons
     *
     * @access private
     * @param  string  $dsn
     * @return void   
     * @TODO   get the port settings too!
     */
    private function _parseDSN($dsn)
    {
    	$parsed = NULL;
    	$arr = NULL;
    	if (is_array($dsn)) {
    		$dsn = array_merge($parsed, $dsn);
    		return $dsn;
    	}
    	//find the protocol
    	if (($pos = strpos($dsn, '://')) !== false) {
    		$str = substr($dsn, 0, $pos);
    		$dsn = substr($dsn, $pos + 3);
    	} else {
    		$str = $dsn;
    		$dsn = null;
    	}
    	if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
    		$parsed['phptype']  = $arr[1];
    		$parsed['phptype'] = !$arr[2] ? $arr[1] : $arr[2];
    	} else {
    		$parsed['phptype']  = $str;
    		$parsed['phptype'] = $str;
    	}

    	if (!count($dsn)) {
    		return $parsed;
    	}
    	// Get (if found): username and password
    	if (($at = strrpos($dsn,'@')) !== false) {
    		$str = substr($dsn, 0, $at);
    		$dsn = substr($dsn, $at + 1);
    		if (($pos = strpos($str, ':')) !== false) {
    			$parsed['username'] = rawurldecode(substr($str, 0, $pos));
    			$parsed['password'] = rawurldecode(substr($str, $pos + 1));
    		} else {
    			$parsed['username'] = rawurldecode($str);
    		}
    	}
    	//server
    	if (($col = strrpos($dsn,':')) !== false) {
    		$strcol = substr($dsn, 0, $col);
    		$dsn = substr($dsn, $col + 1);
    		if (($pos = strpos($strcol, '+')) !== false) {
    			$parsed['hostspec'] = rawurldecode(substr($strcol, 0, $pos));
    		} else {
    			$parsed['hostspec'] = rawurldecode($strcol);
    		}
    	}

    	//now we are left with the port and databsource so we can just explode the string and clobber the arrays together
    	$pm = explode("/",$dsn);
    	$parsed['hostspec'] = $pm[0];
    	$parsed['database'] = $pm[1];
    	$dsn = NULL;

    	$parsed['hostspec'] = str_replace("+","/",$parsed['hostspec']);

    	return $parsed;
    }
}
?>