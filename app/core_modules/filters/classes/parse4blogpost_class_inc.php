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
 * @package filters
 * @version 0.1
 */
class parse4blogpost extends object
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
		require_once($this->getPearResource('XML/RPC.php'));
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objLanguage = $this->getObject('language', 'language');
		$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
		$this->objProxy = $this->getObject('proxy', 'utilities');
		
		// Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        
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
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        preg_match_all('/\\[BLOGPOST:(.*?)\\]/', $txt, $results);
       	$counter = 0;
       	foreach ($results[1] as $item) {
            //Parse for the parameters
            $str = trim($results[1][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
        	$ar= $this->objExpar->getArrayParams($str, ",");
        	//var_dump($ar); die();
        	$val = $this->getBlogPost($ar);
          	$replacement = $val;
        	$txt = str_replace($replaceable, $replacement, $txt);
        	$counter++;
        }
        return $txt;
    }
	
	/**
	 * Method to get a blog post from the metaweblog api
	 *
	 * @param void
	 * @return string
	 */
	private function getBlogPost($ar)
	{
		// get the server and url from the params
		if (isset($ar['server'])) {
            $this->server = $ar['server'];
        } else {
            $this->server = '127.0.0.1';
        }
        
        /*// get the server and url from the params
		if (isset($ar['url'])) {
            $this->url = $ar['url'];
        } else {
            $this->url = '/index.php?module=api';
        }*/
        
        if (isset($this->objExpar->endpoint)) {
            $this->url = $this->objExpar->endpoint;
        } else {
            $this->url = '/chisimba_framework/app/index.php?module=api';
        }
        
        // OK now get the post ID from the filter text
        if (isset($ar['postid'])) {
            $this->postid = $ar['postid'];
        } else {
            $this->postid = NULL;
        }
        
        //echo $this->url; die();//, //$this->url, $this->postid; die();
        $params = array(new XML_RPC_Value($this->postid, "string"), new XML_RPC_Value('username', "string"), new XML_RPC_Value('password', "string"));
        
        // Create the message
		$msg = new XML_RPC_Message('metaWeblog.getPost', $params);
		$cli = new XML_RPC_Client($this->url, $this->server, $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
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
			$xml = $val->serialize($val);
			$data = simplexml_load_string($xml);
			return $data->struct->member->value->string;
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

}
?>