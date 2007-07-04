<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * XML-RPC Server class
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @copyright GPL
 * @package packages
 * @version 0.1
 */
class rpcserver extends object
{
	/**
	 * Config object
	 *
	 * @var object
	 */
	public $objConfig;

	/**
	 * Language object
	 *
	 * @var object
	 */
	public $objLanguage;

	/**
	 * Standard init function (constructor)
	 *
	 * @param void
	 * @return void
	 */
	public function init()
	{
		require_once($this->getPearResource('XML/RPC/Server.php'));
		require_once($this->getPearResource('XML/RPC/Dump.php'));
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objCatalogueConfig = $this->getObject('catalogueconfig','modulecatalogue');
	}

	/**
	 * Method to start the RPC server
	 *
	 * @param void
	 * @return string - xml service
	 */
	public function serve()
	{
		// map web services to methods
		$server = new XML_RPC_Server(
   					array('getModuleZip' => array('function' => array($this, 'getModuleZip'),
   											      'signature' =>
                     									array(
                         									array('string', 'string'),
                     									),
                								  'docstring' => 'Grab a module'),
                		  'getModuleDescription' => array('function' => array($this, 'getModuleDescription'),
   											      'signature' =>
                     									array(
                         									array('string', 'string'),
                     									),
                								  'docstring' => 'Grab a module description'),

                		  'getModuleList' => array('function' => array($this, 'getModuleList'),
                								  'docstring' => 'Grab the module list'),


      			  		  'getModuleDetails' => array('function' => array($this, 'getModuleDetails'),
                								  'docstring' => 'Grab the module list'),


      			  		  'getMsg' => array('function' => array($this, 'getMessage'),
      			  		  					'signature' =>
                     							array(
                         							array('string', 'string'),
                         							//array('boolean', 'string', 'boolean'),
                     							),
                								'docstring' => 'What would you like to see?')
   					), 1);

		return $server;
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
		//zip up the module
		$objZip = $this->getObject('wzip', 'utilities');
		$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getModulePath());
		$filetosend = file_get_contents($zipfile);
		$filetosend = base64_encode($filetosend);
		$val = new XML_RPC_Value($filetosend, 'string');
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
		$objModFile = $this->getObject('modulefile','modulecatalogue');
		//log_debug ("NIC: ".(string)$module." - ".$mod." - ".$mod->scalarval());
		if ($regData = $objModFile->readRegisterFile($objModFile->findregisterfile($mod->scalarval()))) {
			$data[0] = new XML_RPC_Value($regData['MODULE_NAME'], 'string');
		    $data[1] = new XML_RPC_Value($regData['MODULE_DESCRIPTION'], 'string');
	    } else {
	        $data[0] = $data[1] = new XML_RPC_Value($mod->scalarval(), 'string');
	    }
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