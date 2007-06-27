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
	public $objConfig;
	public $objLanguage;
	
	public function init()
	{
		require_once($this->getPearResource('XML/RPC/Server.php'));
		require_once($this->getPearResource('XML/RPC/Dump.php'));
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
	}
	
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
                								  
                		  'getModuleList' => array('function' => array($this, 'getModuleList'),
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
	
	public function deleteModZip()
	{
		foreach(glob('*.zip') as $files)
		{
			unlink($files);
		}
	}

	public function getMessage($message)
	{
		$message = $message->getParam(0);
		return new XML_RPC_Response($message);
	}
	
	public function getModuleList()
	{
		$dataDir = $this->objConfig->getModulePath();
		try {
      		$dir  = new DirectoryIterator($dataDir);
	        foreach ($dir as $file)
      		{
        		$fileName[] = new XML_RPC_Value($file->getFilename(), 'string'); //$file->getFilename();
      		}
		}
		catch (customException $e)
		{
			customException::cleanUp();
		}	
		$val = new XML_RPC_Value($fileName, 'array');
		return new XML_RPC_Response($val);
		
	}
}
?>