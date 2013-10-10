<?php
require_once('httpclients_class_inc.php');
/**
 * Adaptor class to facilitate the HTTP Client
 *
 * @access public
 * @author Paul Scott
 * @filesource
 */

class client extends object
{
	/**
	 * Standard init function
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		try {
        	$this->objFilters = $this->getObject('filter','filters');
        	$this->objResponse = $this->getObject('httpresponse');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			die();
		}
	}

	/**
	 * Method to get a specified url and return it to the calling class
	 *
	 * @param mixed $url
	 * @param headers - may be a string in the form of header: value or an array for multiple sites
	 * @return string
	 * @example $this->getUrl('http://5ive.uwc.ac.za/, array('Accept: text/html', 'Accept-Language: en-us,en;q=0.5'));
	 */
	public function getURL($url, $headers = 'Accept: text/html', $proxy = array())
	{
		$http = new httpclients();
		$http->setUri($url,$headers);
        $response = $http->get();
        if ($response->isSuccessful()) {
        		return $response->getBody();
        } else {
        		return $response->getStatus();
        }
    }

    /**
     * Method to post data to a URL
     *
     * @param mixed $url
     * @param string|array $headers
     * @param mixed $postdata
     * @return string|mixed
     */
    public function postUrl($url, $headers = 'Accept: text/html', $postdata = '')
    {
    	$http = new httpclients();
    	// Set the URI to a POST data processor
    	$http->setUri($url);
    	if($postdata = '')
    	{
    		return FALSE;
    	}
    	else {
    		// Make the HTTP POST request and save the HTTP response
    		$httpResponse = $http->post($postdata);
    		return $httpResponse;
    	}
    }


}
?>