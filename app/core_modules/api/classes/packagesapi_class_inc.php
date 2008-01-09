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
        	$this->objModules = $this->getObject('modules', 'modulecatalogue');
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
		// lets check to see if this module has dependencies...
		$depends = $this->objCatalogueConfig->getModuleDeps($mod->scalarval());
		// log_debug($depends);
		//$depends = $depends[0];
		$depends = explode(',', $depends);
		// log_debug($depends);
		// Recursively download the dependencies
		// generate a list of paths to zip up
		foreach($depends as $paths)
		{
			$paths = trim($paths);
			$path = $this->objConfig->getModulePath().$paths.'/';
			if(file_exists($path))
			{
				$dep[] = $this->objConfig->getModulePath().$paths.'/';
			}
			elseif(file_exists($this->objConfig->getsiteRootPath().'core_modules/'.$paths.'/'))
			{
				$dep[] = $this->objConfig->getsiteRootPath().'core_modules/'.$paths.'/';
			}
			else {
				$dep[] = FALSE;
			}
		}
		// add the actual module path in there too
		$path = $this->objConfig->getModulePath().$mod->scalarval().'/';
		$dep[] = $path;
		//log_debug($dep);
		foreach($dep as $deps)
		{
			if(substr($deps, -2) == '//')
			{
				unset($deps);
			}
			//check for core_modules and unset that too
			if(preg_match("/core_modules/i", $deps))
			{
				unset($deps);
			}
			$depe[] = $deps;
		}
		$depe = array_filter($depe);
		//log_debug($depe);
		
		$filepath = $this->objConfig->getModulePath().$mod->scalarval().'.zip';
		if(!file_exists($path))
		{
			log_debug("grabbing a core module -> ".$mod->scalarval());
			// try the core modules....
			$path = $this->objConfig->getsiteRootPath().'core_modules/'.$mod->scalarval().'/';
			$filepath = $this->objConfig->getsiteRootPath().'core_modules/'.$mod->scalarval().'.zip';
			//zip up the module
			$objZip = $this->getObject('wzip', 'utilities');
			//$zipfile = $objZip->packFilesZip($filepath, $path, TRUE, FALSE);
			$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getsiteRootPath().'core_modules/');
			$filetosend = file_get_contents($zipfile);
			$filetosend = base64_encode($filetosend);
			$val = new XML_RPC_Value($filetosend, 'string');
			unlink($filepath);
			log_debug("Sent ".$mod->scalarval()." to client");
			return new XML_RPC_Response($val);
			// Ooops, couldn't open the file so return an error message.
			return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
		}
		//zip up the module(s)
		// finally zip up the mods needed and send to client...
		$filetosend = $this->zipDependencies($depe, $mod->scalarval());
		
		/*$objZip = $this->getObject('wzip', 'utilities');
		//$zipfile = $objZip->packFilesZip($filepath, $path, TRUE, FALSE);
		$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getModulePath());
		$filetosend = file_get_contents($zipfile);
		$filetosend = base64_encode($filetosend);*/
		$val = new XML_RPC_Value($filetosend, 'string');
		unlink($filepath);
		log_debug("Sent ".$mod->scalarval()." to client");
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}
	
	public function zipDependencies($modulesarr, $mod)
	{
		$objZip = $this->getObject('wzip', 'utilities');
		$filepath = $this->objConfig->getModulePath().$mod.'.zip';
		$modulesarr = implode(',', $modulesarr);
		// log_debug($modulesarr);
		$zipfile = $objZip->addArchive($modulesarr, $filepath, $this->objConfig->getModulePath());
		$filetosend = file_get_contents($zipfile);
		$filetosend = base64_encode($filetosend);
		return $filetosend;
	}

	/**
	 * Method to grab a specified set of modules as a zip file
	 *
	 * @param array $module
	 * @return string - base64 encoded string of the zipfile
	 */
	public function getMultiModuleZip($module)
	{
		//grab the module array
		$mod = $module->getParam(0);
		$mod = $mod->scalarval();
		log_debug($mod);
		$path = $this->objConfig->getModulePath();
		//zip up the module
		$objZip = $this->getObject('wzip', 'utilities');
		$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getModulePath());
		$filetosend = file_get_contents($zipfile);
		$filetosend = base64_encode($filetosend);
		$val = new XML_RPC_Value($filetosend, 'string');
		unlink($filepath);
		log_debug("Sent ".$mod->scalarval()." to client");
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
		$this->objCatalogueConfig->writeCatalogue();
	    $mArray = $this->objCatalogueConfig->getModuleDetails();
	    $data = array();
	    foreach ($mArray as $mod) {
	       $det[0] = new XML_RPC_Value($mod[0], 'string');
	       $det[1] = new XML_RPC_Value($mod[1], 'string');
	       $det[2] = new XML_RPC_Value($mod[2], 'string');
	       $det[3] = new XML_RPC_Value($mod[3], 'string');
	       $data[] = new XML_RPC_Value($det, 'array');
	    }
	    $val = new XML_RPC_Value($data,'array');
	    return new XML_RPC_Response($val);
	}

	/**
	 * Method to grab a specified skin from the RPC Server
	 *
	 * @param string $skinName
	 */
	public function getSkin($skinName)
	{
		//grab the module array
		$skin = $skinName->getParam(0);
		$skin = $skin->scalarval();
		// log_debug($skin);
		// grok the skin path...
		$path = $this->objConfig->getskinRoot();
		$filepath = $this->objConfig->getskinRoot().$skin.".zip";
		//zip up the skin
		$objZip = $this->getObject('wzip', 'utilities');
		$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getSkinRoot());
		$filetosend = file_get_contents($zipfile);
		$filetosend = base64_encode($filetosend);
		$val = new XML_RPC_Value($filetosend, 'string');
		unlink($filepath);
		log_debug("Sent Skin: ".$skin->scalarval()." to client");
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}
	
	/**
	 * Method to return a list of available skins for remote download
	 *
	 */
	public function getSkinList()
	{
		$path = $this->objConfig->getskinRoot();
		chdir($path);
		foreach(glob('*') as $skins)
		{
			$sklist .= $skins."|";
		}	
		$val = new XML_RPC_Value($sklist, 'string');
		log_debug("Sent Skin List to client");
		return new XML_RPC_Response($val);
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
	}

	/**
	 * Method to update the systemtypes.xml document
	 *
	 */
	public function updateSystemTypesFile()
	{
		$types = $this->objConfig->getsiteRootPath().'config/systemtypes.xml';
		$contents = file_get_contents($types);
		$filetosend = base64_encode($contents);
		$val = new XML_RPC_Value($filetosend, 'string');
		log_debug("Sent systemtypes.xml to client");
		return new XML_RPC_Response($val);
	}
}
?>