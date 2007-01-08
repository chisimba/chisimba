<?php

/**
 * Top level class for parsing and returning proxy information to be used within the Chisimba Framework
 *
 * @author Paul Scott
 * @access public
 * @copyright AVOIR, GNU/GPL 2007
 * @package utilities
 * @category chisimba
 * @filesource
 */

class proxyparser extends object
{
	/**
	 * Class to parse the proxy information from the config object into
	 * an array that can be used by any/all other modules that need to contact
	 * external websites for servers behind a proxy/firewall
	 *
	 * @author Paul Scott
	 * @access public
	 * @package utilities
	 */

	/**
	 * Proxy string from the config object
	 *
	 * @var string
	 */
	private $proxystring;

	/**
	 * Config object
	 *
	 * @var object
	 */
	public $objConfig;

	/**
	 * The proxy array to return for use
	 *
	 * @var array
	 */
	public $proxyArr = array();

	/**
	 * Standard init function to set up
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		//lets go and get the string so that we can do some magic on it...
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->proxystring = $this->objConfig->getItem('KEWL_PROXY');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}

	/**
	 * Public function to return the proxy info array
	 *
	 * @return array
	 */
	public function getProxy()
	{
		if(!isset($this->proxystring))
		{
			return NULL;
		}
		else {
			return $this->parseProxy($this->proxystring);
		}
	}

	/**
	 * Method to parse the proxy string into an array
	 *
	 * @param string $proxystring
	 * @return array
	 */
	private function parseProxy($proxystring)
	{
		$parsed = NULL;
    	$arr = NULL;
    	if (is_array($proxystring)) {
    		$proxystring = array_merge($parsed, $proxystring);
    		return $proxystring;
    	}
    	//find the protocol
    	if (($pos = strpos($proxystring, '://')) !== false) {
    		$str = substr($proxystring, 0, $pos);
    		$proxystring = substr($proxystring, $pos + 3);
    	} else {
    		$str = $proxystring;
    		$proxystring = null;
    	}
    	if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
    		$parsed['proxy_protocol']  = $arr[1];
    		$parsed['proxy_protocol'] = !$arr[2] ? $arr[1] : $arr[2];
    	} else {
    		$parsed['proxy_protocol']  = $str;
    		$parsed['proxy_protocol'] = $str;
    	}

    	if (!count($proxystring)) {
    		return $parsed;
    	}
    	// Get (if found): username and password
    	if (($at = strrpos($proxystring,'@')) !== false) {
    		$str = substr($proxystring, 0, $at);
    		$proxystring = substr($proxystring, $at + 1);
    		if (($pos = strpos($str, ':')) !== false) {
    			$parsed['proxy_user'] = rawurldecode(substr($str, 0, $pos));
    			$parsed['proxy_pass'] = rawurldecode(substr($str, $pos + 1));
    		} else {
    			$parsed['proxy_user'] = rawurldecode($str);
    		}
    	}
    	//server
    	if (($col = strrpos($proxystring,':')) !== false) {
    		$strcol = substr($proxystring, 0, $col);
    		$proxystring = substr($proxystring, $col + 1);
    		if (($pos = strpos($strcol, '+')) !== false) {
    			$parsed['proxy_host'] = rawurldecode(substr($strcol, 0, $pos));
    		} else {
    			$parsed['proxy_host'] = rawurldecode($strcol);
    		}
    	}

    	//now we are left with the port
    	$parsed['proxy_port'] = $proxystring;
    	$proxystring = NULL;

    	return $parsed;
	}
}
?>