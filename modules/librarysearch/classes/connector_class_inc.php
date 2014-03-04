<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * This object will handle the submitting and retrieving responses from the data sources
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

class connector extends object
{
    /**
     * _api
     * Stateful, holds the string value of the API to use when submitting and retrieving data
     *
     * @var _api
     */
    private $_api;

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
     * Method to set the API
     * @param string $path The path to the directory
     * @return Array an array of classfiles and fullpaths arr['filename'] = 'fullpathtofile'
     */

    public function setAPI($api) {
        $this->_api = $api;
    }

    /**
     *
     * Method to get the current API in use
     * @return string The current API
     */

    public function getAPI() {
        return $this->_api;
    }

    /**
     *
     * Method to get the current API in use
     * @return string The current API
     */

    public function submitSearch($searchKeyArr, $uri, $paramArr) {
        if (strtolower($this->_api) == 'curl') {
            $this->objCurl = $this->getObject('api_curl', 'librarysearch');
            $resultDoc = $this->objCurl->submitSearch($searchKeyArr, $uri, $paramArr);

            var_dump($resultDoc); exit;

            //Parse the resulting Doc here:
            //$resultDoc
        }
    }

}

?>
