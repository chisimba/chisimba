<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * XML-RPC Server class
 *
 * @author Paul Scott
 * @copyright GPL
 * @package packages
 * @version 0.1
 */
class rpcserver extends object
{
	public function init()
	{
		require_once($this->getPearResource('XML/RPC/Server.php'));
		require_once($this->getPearResource('XML/RPC/Dump.php'));
		
		$this->objConfig = $this->getObject('altconfig', 'config');
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
		ini_set('max_execution_time', -1);
		//grab the module name
		$mod = $module->getParam(0);
		
		$path = $this->objConfig->getModulePath().$mod->scalarval().'/';
		$filepath = $this->objConfig->getModulePath().$mod->scalarval().'.zip';
		//zip up the module
		$objZip = $this->getObject('wzip', 'utilities');
		//echo $path, $filepath; die();
		$zipfile = $objZip->addArchive($path, $filepath, $this->objConfig->getModulePath());
		if($filetosend = file_get_contents($zipfile))
		{
			$val = new XML_RPC_Value($filetosend, 'base64');
			return new XML_RPC_Response($val);
		}
		
		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, // user error 1
		'There was an error opening the file.');
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
		//$fileName = array('some shit');
		$val = new XML_RPC_Value($fileName, 'array');
		return new XML_RPC_Response($val);
		
	}
}
?>