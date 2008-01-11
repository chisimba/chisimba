<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
/**
 * XML-RPC Client class
 *
 * @author Paul Scott
 * @author Nic Appleby
 * @copyright GPL
 * @package packages
 * @version 0.1
 */
class rpcclient extends object
{
	/**
	 * Language Object
	 *
	 * @var object
	 */
	public $objLanguage;

	/**
	 * Config object
	 *
	 * @var object
	 */
	public $objConfig;

	/**
	 * Sysconfig object
	 *
	 * @var object
	 */
	public $sysConfig;
	
	/**
	 * Port
	 */
	public $port = 80;
	
	/**
	 * Proxy info
	 */
	public $proxy;
	
	/**
	 * Proxy object
	 */
	public $objProxy;
	

	/**
	 * Standard init function
	 *
	 * @param void
	 * @return void
	 */
	public function init()
	{
		//require_once($this->getPearResource('XML/RPC.php'));
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
		$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$this->objProxy = $this->getObject('proxy', 'utilities');
		
		// get the proxy info if set
        $proxyArr = $this->objProxy->getProxy(NULL);
        if (!empty($proxyArr)) {
            $this->proxy = array(
                'proxy_host' => $proxyArr['proxyserver'],
                'proxy_port' => $proxyArr['proxyport'],
                'proxy_user' => $proxyArr['proxyusername'],
                'proxy_pass' => $proxyArr['proxypassword']
            );
        }
        else {
        	$this->proxy = array(
                'proxy_host' => '',
                'proxy_port' => '',
                'proxy_user' => '',
                'proxy_pass' => '',
            );
        }
	}

	/**
	 * Method to get a list of available modules from the rpc server
	 *
	 * @param void
	 * @return string
	 */
	public function getModuleList()
	{
		$msg = new XML_RPC_Message('getModuleList');
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}

	/**
	 * Method to get a list of available skins from the rpc server
	 *
	 * @param void
	 * @return string
	 */
	public function getSkinList()
	{
		$msg = new XML_RPC_Message('getSkinList');
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}
	
	
	
	/**
	 * Method to get a list of available modules from the rpc server
	 *
	 * @param void
	 * @return string
	 */
	public function getModuleDetails()
	{
		$msg = new XML_RPC_Message('getModuleDetails');
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}

	/**
	 * Method to get the desription of a particular module off the server
	 *
	 * @param string $moduleName
	 * @return string
	 */
	public function getModuleDescription($moduleName) {
	    $msg = new XML_RPC_Message('getModuleDescription', array(new XML_RPC_Value($moduleName, "string")));
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			//throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}

	public function checkConnection() {
	    $msg = new XML_RPC_Message('getMsg',array(new XML_RPC_Value('connected?','string')));
	    $mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
		    log_debug($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
			return FALSE;
		}
		if (!$resp->faultCode())
		{
			return TRUE;
		}
		else
		{
		    log_debug($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		    return FALSE;
		}
	}

	/**
	 * Grab a zip file of a module from the RPC Server
	 *
	 * @param string $modulename
	 * @return serialized base64 encoded string
	 */
	public function getModuleZip($modulename)
	{
		$msg = new XML_RPC_Message('getModuleZip', array(new XML_RPC_Value($modulename, "string")));
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		//log_debug($resp);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode().$this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}
	
	
	/**
	 * Grab a zip file of a skin from the RPC Server
	 *
	 * @param string $modulename
	 * @return serialized base64 encoded string
	 */
	public function getSkinZip($skinname)
	{
		log_debug("Getting skin $skinname");
		$msg = new XML_RPC_Message('getSkin', array(new XML_RPC_Value($skinname, "string")));
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		//log_debug($resp);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode().$this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}
	
	/**
	 * Grab a zip file of a set of modules from the RPC Server
	 *
	 * @param array $modulename
	 * @return serialized base64 encoded string
	 */
	public function getMultiModuleZip($modulename = array())
	{
		$msg = new XML_RPC_Message('getMultiModuleZip', array(new XML_RPC_Value($modulename, "array")));
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(1);

		// send the request message
		$resp = $cli->send($msg);
		//log_debug($resp);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode().$this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}
	
	/**
	 * update the systemtypes.xml document
	 *
	 * @return serialized base64 encoded string
	 */
	public function updateSysTypes()
	{
		$msg = new XML_RPC_Message('updateSystemTypes');
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		//log_debug($resp);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode().$this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
	}
	
	public function getRemoteEngineVer()
	{
		$msg = new XML_RPC_Message('getEngineVer');
		$mirrorserv = $this->sysConfig->getValue('package_server', 'packages');
		$mirrorurl = $this->sysConfig->getValue('package_url', 'packages');
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		//log_debug($resp);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr);
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
			throw new customException($this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode().$this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString());
		}
		
	}
}
?>