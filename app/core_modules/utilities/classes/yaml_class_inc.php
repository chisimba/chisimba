<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * This class is used to load a file using Yaml
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class yaml extends object 
{
	/**
	*
	* @var $objYaml
	* @access public
	*
	*/
	public $objYaml;
	
	/**
	 * Standard init method
	 */
	public function init()
	{
		include($this->getResourcePath('yaml/spyc.php'));
		$this->objYaml = new Spyc;
	}
	
	/**
	 * Method to parse the file to the Yaml object
	 *
	 * @param string $file
	 * @return loaded Yaml file
	 */
	public function parseYaml($file)
	{
		if(file_exists($file))
		{
			return $this->objYaml->load($file);
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Method to save the Yaml file
	 *
	 * @param string $array
	 * @return saved Yaml file
	 */
	public function saveYaml($array,$indent = false,$wordwrap = false)
	{
		return $this->objYaml->dump($array, $indent, $wordwrap);
	}
}
?>