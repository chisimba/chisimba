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
class tracrpcclient extends object
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
	
	public $tracURL;
	
	public $tracServ;

	/**
	 * Standard init function
	 *
	 * @param void
	 * @return void
	 */
	public function init()
	{
		require_once($this->getPearResource('XML/RPC.php'));
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$this->tracURL = $this->objSysConfig->getValue('trac_url', 'api');
		$this->tracServ = $this->objSysConfig->getValue('trac_server', 'api');
	}
	
	public function grabTracWikiPageHTML($pagename)
	{
		$msg = new XML_RPC_Message('wiki.getPageHTML', array(new XML_RPC_Value($pagename, "string")));
		$cli = new XML_RPC_Client($this->tracURL, $this->tracServ);
		$cli->setDebug(0);
		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			throw new customException($this->objLanguage->languageText("mod_filters_commserr", "filters").": ".$cli->errstr);
			exit;
		}
		if (!$resp->faultCode())
		{
			$val = $resp->value();
			//var_dump($val);
			$val = XML_RPC_decode($val);
			if(is_array($val))
			{
				return $val['faultString'];
			}
			else {
				return $val;
			}
			//var_dump($val);
		}
		else
		{
			/*
			* Display problems that have been gracefully caught and
			* reported by the xmlrpc server class.
			*/
			throw new customException($this->objLanguage->languageText("mod_filters_faultcode", "filters").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_filters_faultreason", "filters").": ".$resp->faultString());
		}
	}
}

?>