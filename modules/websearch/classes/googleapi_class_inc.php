<?php
/* -------------------- googleApi class ----------------*/
// Load the nusoap library
require_once "lib/nusoap/nusoap.php";

/**
* Class to provide a google search interface, for the google
* API. This code is based in part on the code provided by
* Daniel Marjos <dmarjos at ciudad-eolica dot com dot ar>
* and available at 
*           http://www.phpclasses.org/browse/package/1325.html
* 
* @Author Derek Keats,Jameel Sauls
*/
class googleApi extends object 
{
    /**
    * @var string $searchengine="googleApi": The name of the search engine
    *   to store in the database
    */
    public $searchengine = "googleApi";

    /**
    * @var object $soapclient: String to hold the nusoap object
    */
    public $objSoapClient;

    /**
    * @var string $theResultSet: holds the results of the search as given by google api
    */
    public $theResultSet;

    /**
    * $var array $theResults: holds the results, and is intended to do the traversing
    */
    public $theResults = array();

    /**
    * @var integer $theRowShown: internal field. Holds the index to the last row shown
    */
    public $theRowShown = 0;

    /**
    * @var integer $theMaxResults: internal field. Holds the given max results 
    * parameter to the constructor
    */
    public $theMaxResults;
    
    /**
    * @var integer $pages: internal field. Holds the calculated
    * number of pages
    */
    public $pages;
    
    /**
    * @var boolean $flgError: indicates if was there error or not
    */
    public $flgError = false;
    
    /**
    * @var string $error: The error message
    */
    public $error;

    /**
    * @var string $theSearchQuery: the Search query as returned by Google Api
    */
    public $theSearchQuery;

    /**
    * @var integer $theEstimatedResultsCount: The number of results found by the Api
    */
    public $theEstimatedResultsCount;
    
    /**
    * 
    */
    public $googleAcccountLink = "https://www.google.com/accounts/NewAccount?continue=http://api.google.com/createkey&followup=http://api.google.com/createkey";

    /**
    * Constructor method to define the table
    */
    public function init()
    { 
        // create a instance of the SOAP client object for google API
        $this->objSoapClient = new soapclient("http://api.google.com/search/beta2");
        //Make an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        $objConfig = $this->getObject('config', 'altconfig');
        $this->objUser = &$this->getObject('user', 'security');
        $this->imgLocation = $objConfig->siteRoot()
         . "modules/" . $this->getParam('module', null)
         . "/resources/images/";
    } 

    
    /**
    * Carry out the search. This was clsGoogleApi in the original code by
    * Daniel Marjos.
    */
    public function doSearch($searchTerm, $start, $maxResults)
    {
        //Save the search term
        $objDb = $this->getObject('dbsearch');
        $objDb->saveRecord();

        
        $this->flgError = FALSE;
        $objUprm = & $this->getObject('dbuserparamsadmin', 'userparamsadmin');
        $googleKey = $objUprm->getValue('Google API key', $this->objUser->userId());
        //Check for google key for the loggedin user
        if ($googleKey == "") {
            $this->flgError = TRUE;
            $this->error = $this->objLanguage->languageText("mod_smartsearch_nokey", "websearch");
            return FALSE;
        }
        $start = (int) $start; //Seems to think its a string otherwise
        $params = array('key' => $googleKey, 
            'q' => $searchTerm, // search term
            'start' => $start, // start from result n
            'maxResults' => $maxResults, // show a total of n results
            'filter' => false, // remove similar results
            'restrict' => '', // restrict by topic
            'safeSearch' => false, // remove adult links
            'lr' => '', // restrict by language
            'ie' => '', // input encoding
            'oe' => '' // output encoding
            ); 
        // invoke the method on the server
        $this->theResultSet = $this->objSoapClient->call("doGoogleSearch", 
          $params, "urn:GoogleSearch", "urn:GoogleSearch");
        $this->theMaxResults = $this->theResultSet['estimatedTotalResultsCount'];
        $this->pages = round($this->theMaxResults / 10);
        // print the results of the search
        if ( isset($this->theResultSet['faultstring']) ) {
            $this->flgError = true;
            $this->error = $this->theResultSet['faultstring'];
            return FALSE; 
        } else {
            //var_dump($this->theResultSet);
            //die();
            $this->flgError = false;
            $this->theRowShown = 0;
            $this->theSearchQuery = $this->theResultSet['searchQuery'];
            $this->theEstimatedResultsCount = $this->theResultSet['estimatedTotalResultsCount'];
            if (is_array($this->theResultSet['resultElements'])) {
                $this->theResults = array();
                foreach ($this->theResultSet['resultElements'] as $r) {
                    $result["URL"] = $r['URL'];
                    $result["cached-size"] = $r['cachedSize'];
                    $result["snippet"] = $r['snippet'];
                    $result["directoryCategory"] = $r['directoryCategory'];
                    $result["relatedInformationPresent"] = $r['relatedInformationPresent'];
                    $result["directoryTitle"] = $r['directoryTitle'];
                    $result["summary"] = utf8_decode($r['summary']);
                    $result["title"] = utf8_decode($r['title']);
                    $this->theResults[] = $result;
                } 
            } 
            return $this->theResults;
        } 
    } 
    
    /**
    * Method to return the querystring with the module=modulecode
    * part removed. I use preg_replace for case insensitive replacement.
    */
    public function _cleanParams()
    {
        $str = $_SERVER['QUERY_STRING'];
        $str = preg_replace("/module=/i", null, $str);
        $module = $this->getParam('module', null);
        $str = preg_replace("/$module/i", null, $str);
        return $str;
    } #function _cleanParams
    
    /**
    * Method to get the last search by the user
    */
    public function _getLastSearch($context = null)
    {
        $objDb = $this->getObject('dbsearch');
        $filter = " WHERE userid='" . $this->objUser->userId() . "' ";
        $ar = $objDb->getLastEntry($filter);
        return $ar[0]['searchterm'];
    } 
} #end of class
 //test

?>