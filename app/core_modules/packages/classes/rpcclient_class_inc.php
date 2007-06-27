<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
/**
 * XML-RPC Client class
 *
 * @author Paul Scott
 * @copyright GPL
 * @package packages
 * @version 0.1
 */
class rpcclient extends object
{
	public function init()
	{
		//require_once($this->getPearResource('XML/RPC.php'));
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
		$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
	}

	public function getModuleList()
	{
		$msg = new XML_RPC_Message('getModuleList');
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			echo 'Communication error: ' . $cli->errstr;
			exit;
		}
		if (!$resp->faultCode())
		{
			$val = $resp->value();
			return $val->serialize($val);
		}
		else
		{
			/*
			* Display problems that have been gracefully caught and
			* reported by the xmlrpc server class.
			*/
			echo 'Fault Code: ' . $resp->faultCode() . "n";
			echo 'Fault Reason: ' . $resp->faultString() . "n";
		}
	}
	
	public function getModuleZip($modulename)
	{
		$msg = new XML_RPC_Message('getModuleZip', array(new XML_RPC_Value($modulename, "string")));
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			echo 'Communication error: ' . $cli->errstr;
			exit;
		}
		if (!$resp->faultCode())
		{
			$val = $resp->value();
			return $val->serialize($val);
		}
		else
		{
			/*
			* Display problems that have been gracefully caught and
			* reported by the xmlrpc server class.
			*/
			echo 'Fault Code: ' . $resp->faultCode() . "n";
			echo 'Fault Reason: ' . $resp->faultString() . "n";
		}
	}
}
?>