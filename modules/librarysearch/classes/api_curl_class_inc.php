<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * This object will handle the submitting and retrieving responses
 * ,using CURL Post/Get Screen Scrapping, from the data sources
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

class api_curl extends object
{
    /**
     * _method
     * Stateful, either GET or POST
     *
     * @var _method
     */
    private $_method;

	/**
	 * Class Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function init()
	{
		try {
			$this->objLanguage =$this->getObject('language', 'language');

		} catch (Exception $e){
			throw customException($e->getMessage());
			exit();
		}
	}


    /**
     *
     * Method to return the url for session based connections
     * e.g. web.ebscohost.com contains a page with a session enabled link to the search interface (Arrgh)
     *
     * @return string The initial URI
     */

    public function startSession($uri) {
            //get the proxy info if set
            $objProxy = $this->getObject('proxyparser', 'utilities');
            $proxyArr = $objProxy->getProxy();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            //curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            //TODO: If the param array contains proxy information we should use these instead

            if(!empty($proxyArr) && $proxyArr['proxy_protocol'] != '')
            {
                curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'].":".$proxyArr['proxy_port']);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'].":".$proxyArr['proxy_pass']);
            }

            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
    }


	/**
	 *
	 * Method to retrieve the results from a search to a datasource
     * @param string or array $searchKeyArr an array of search keys to search against.
     *                        The first element in the array will represent the search key
     *                        The next elements in the array will represent the
     *
     * @param array $paramArr an array of CURL parameters to use when submitting the request
     *
	 * @return Array an array of classfiles and fullpaths arr['filename'] = 'fullpathtofile'
	 */

	public function submitSearch($searchKeyArr, $uri, $paramArr = '') {
            //get the proxy info if set
            $objProxy = $this->getObject('proxyparser', 'utilities');
            $proxyArr = $objProxy->getProxy();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            //curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            //TODO: If the param array contains proxy information we should use these instead
            
            if(!empty($proxyArr) && $proxyArr['proxy_protocol'] != '')
            {
                curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'].":".$proxyArr['proxy_port']);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'].":".$proxyArr['proxy_pass']);
            }
            
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
	}


}

?>
