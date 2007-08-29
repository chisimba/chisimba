<?php

/**
 * Packages interface class
 * 
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
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
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Packages XML-RPC Class
 * 
 * Class to provide Chisimba Packages XML-RPC functionality
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class packagesapi extends object
{

	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
        	$this->objUser = $this->getObject('user', 'security');
        	$this->objCatalogueConfig = $this->getObject('catalogueconfig','modulecatalogue');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	


	/**
	 * Method to grab a specified module as a zipfile
	 *
	 * @param string $module
	 * @return string - base64 encoded string of the zipfile
	 */
	public function getModuleZip($module)
	{
		//grab the module name
		$mod = $module->getParam(0);
		$path = $this->objConfig->getModulePath().$mod->scalarval().'/';
		$filepath = $this->objConfig->getModulePath().$mod->scalarval().'.zip';
		//log_debug("grabbing $path");
		//zip up the module
		$objZip = $this->getObject('wzip', 'utilities');
		$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getModulePath());
		log_debug($zipfile);
		$filetosend = file_get_contents($zipfile);
		$filetosend = base64_encode($filetosend);
		log_debug($filetosend);
		$val = new XML_RPC_Value($filetosend, 'string');
		unlink($filepath);
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}

	/**
	 * Method to grab a specified module's description
	 *
	 * @param string $module
	 * @return string
	 */
	public function getModuleDescription($module)
	{
		//grab the module name
		$mod = $module->getParam(0);
		$name = $this->objCatalogueConfig->getModuleName($mod->scalarval());
		$desc = $this->objCatalogueConfig->getModuleDescription($mod->scalarval());
		$data[0] = new XML_RPC_Value((string)$name[0],'string');
		$data[1] = new XML_RPC_Value((string)$desc[0],'string');
		$val = new XML_RPC_Value($data, 'array');
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}

	/**
	 * Method to delete a module zipfile from the server
	 *
	 * @param void
	 * @return void
	 */
	public function deleteModZip()
	{
		chdir($this->objConfig->getModulePath());
		foreach(glob('*.zip') as $files)
		{
			log_debug("cleaning up: ".$files);
			unlink($files);
		}
	}

	/**
	 * Method to return an XML-RPC message
	 *
	 * @param string $message
	 * @return XML-RPC response object
	 */
	public function getMessage($message)
	{
		$message = $message->getParam(0);
		return new XML_RPC_Response($message);
	}

	/**
	 * Method to return a list of available modules on the RPC server
	 *
	 * @param void
	 * @return XML-RPC Response object (string)
	 */
	public function getModuleList()
	{
		$dataDir = $this->objConfig->getModulePath();
		try {
      		$dir  = new DirectoryIterator($dataDir);
	        foreach ($dir as $file)
      		{
      			if($file->isDir())
      			{
        			$fileName[] = new XML_RPC_Value($file->getFilename(), 'string');
      			}
      		}
		}
		catch (customException $e)
		{
			customException::cleanUp();
		}
		$val = new XML_RPC_Value($fileName, 'array');
		return new XML_RPC_Response($val);

	}

	public function getModuleDetails() {
	    $mArray = $this->objCatalogueConfig->getModuleDetails();
	    $data = array();
	    foreach ($mArray as $mod) {
	       $det[0] = new XML_RPC_Value($mod[0], 'string');
	       $det[1] = new XML_RPC_Value($mod[1], 'string');
	       $det[2] = new XML_RPC_Value($mod[2], 'string');
	       $data[] = new XML_RPC_Value($det, 'array');
	    }
	    $val = new XML_RPC_Value($data,'array');
	    return new XML_RPC_Response($val);
	}

	/**
	 * Method to grab a specific system type (blog, elearn, cms etc)
	 *
	 * @param string $systemType
	 */
	public function getSystemType($systemType)
	{

	}

	/**
	 * Method to grab a specified skin from the RPC Server
	 *
	 * @param string $skinName
	 */
	public function getSkin($skinName)
	{

	}

	public function checkVersion($modulename)
	{

	}
}
?>