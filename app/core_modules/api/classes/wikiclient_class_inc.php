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
class wikirpcclient extends object
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
	}
	
	public function grabWikiPage($pagename, $wikiname, $url = '/index.php?module=api', $serv = 'fsiu.uwc.ac.za')
	{
		$msg = new XML_RPC_Message('chiswiki.getPage', array(new XML_RPC_Value($pagename, "string"), new XML_RPC_Value($wikiname, "string")));
		$cli = new XML_RPC_Client($url, $serv);
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
			return $val->serialize($val);
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