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
	}
	
	public function server()
	{
		// map web services to methods
		$server = new XML_RPC_Server(
   			array('getPhoto' => array('function' => array($this, 'getImage')),
      			  'getMsg' => array('function' => array($this, 'getMessage')),
   			)
		);
		
		return $server;
	}
	
	public function getImage()
	{
		// We're just going to open an existing image

		if($img = @file_get_contents('/var/www/junk/Swizz.3gp'))
		{
			$val = new XML_RPC_Value($img, 'base64');
			return new XML_RPC_Response($val);
		}

		// Ooops, couldn't open the file so return an error message.
		return new XML_RPC_Response(0, $XML_RPC_erruser+1, // user error 1
		'There was an error opening the file.');
	}

	public function getMessage()
	{
		$msg = "Hello world";
		$val2 = new XML_RPC_Value($msg);
		return new XML_RPC_Response($val2);
	}
}
?>