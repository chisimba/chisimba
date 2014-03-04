<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check
/**
*
* Class for the YouTube API. YouTube offers open access to key parts of the 
* YouTube video repository and user community, via an open API interface 
* and RSS feeds. Using the YouTube APIs, you can easily integrate online 
* videos from YouTube's rapidly growing repository of videos into an 
* application. You need to have an API key to use this feature.
*
* The basic format of an API call is very simple: 
* http://www.youtube.com/api2_rest?method=youtube.videos.list_by_tag
* &dev_id=dev_id&tag=tag&page=page&per_page=per_page
*
* @author Derek Keats
* @category Chisimba
* @package youtube
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class youtubeapi extends object 
{
    /**
    *
    * Property to store the Youtube API url
    * @access private
    *  
    */
    private $apiUrl;
    /**
    *
    * Property to store the Youtube API rest base which
    * is added to the apiUrl to build the call
    * @access private
    *  
    */
    private $apiRestBase;
    /**
    *
    * Property to store the Youtube XML ROP base which
    * is added to the apiUrl to build the call
    * @access private
    *  
    */    
    private $apiXmlRpcBase;
    /**
    *
    * Property to store the Youtube API key, which is
    * needed to make the API calls
    * @access private
    *  
    */
    private $apiKey;
    
    /**
    *
    * Property to store the simple XML data returned
    *  
    */
    public $xml;
    /**
    *
    * Property to store the config options
    *  
    */
    public $config = array();
    /**
    *
    * Property to store the tag
    *  
    */
    public $tag;
    /**
    *
    * Property to store the API call querystring for Youtube
    *  
    */
    public $apiCall;
    /**
    *
    * Property to store the data returned from XML call 
    *  
    */
    public $apiRet;
    /**
    *
    * Property to store error report
    *  
    */
    public $errorStr;
    /**
    *
    * Property to store the starting page for a particular
    * call to the API
    * @access public
    *  
    */
    public $page;
    /**
    *
    * Property to store the number of videos per page for a particular
    * call to the API
    * @access public
    *  
    */
    public $perPage;
    
    public $ytIdentifier;
    public $ytMethod;
    /**
    *
    * Standard init method used here to set values of 
    * various properties
    *
    */
    public function init()
    {
        //Set up the base URL for the API call
        $this->apiUrl = 'http://www.youtube.com/';
        //Define the rest call code
        $this->apiRestBase = 'api2_rest?';
        //Defint the XML RPC code
        $this->apiXmlRpcBase = 'api2_xmlrpc?';
        //Get the apiKey
        $this->apiKey="DP44qovp8v8";
        //Set the default method
        $this->ytMethod = NULL;
        //set the default identifier
        $this->ytIdentifier = NULL;
        $this->page = $this->getParam('ytpage', 1);
        $this->hitsPerPage = $this->getParam('hitsperpage', 24);
        
    }
    
 
    /**
    *
    * Method to access properties in this class
    * 
    * @param string $item The property whose value is being requested
    * @access public
    *
    */
    public function get($item) {
        return $this->$item;
    }
    
    /**
    *
    * Method to set properties in this class
    * 
    * @param string $key The property whose value is being set
    * @param string $value The value of the property being set
    * @access public
    *
    */
    public function set($key, $value)
    {
        $this->$key = $value;
    }
    
    /**
    * 
    * Setup method for apiCall
    * 
    * @return string $callStr The URL call to the Youtube API
    * @access public
    * 
    */
    public function setupCall($ytMethod)
    {
        //Get the method to use and default to by_tag
        if ($ytmethod==NULL || $ytmethod=="") {
            $ytMethod = $this->getParam('ytmethod', $this->ytMethod);
        }
        
        //Get the tag or user or other identifier and default to digitalfreedom
        $ytIdentifier = $this->getParam('ytidentifier', $this->ytIdentifier);
        switch ($ytMethod) {
                case "by_tag":
                    $callStr = $this->videosListByTag($ytIdentifier);
                    break;
                case "by_user":
                    $callStr = $this->videosListByUser($ytIdentifier);
                    break;
                case "by_playlist":
                    $callStr = $this->videosListByPlaylist($ytIdentifier);
                    break;
                    
                default:
                    $callStr = $this->videosListByTag('digitalfreedom');
                    break;
        }
        return $callStr;
    }
    
    /**
    * 
    * A method to return the API call as a URL for the API method
    * 
    * @return string The URL for the api call
    * @access public
    *  
    */
    public function getCall()
    {
        return $this->apiUrl 
          . $this->apiCall;
    }
    
    
    /**
    * 
    * Method to setup the call for videos to be listed by tag
    * thus corresponding to the Youtube method
    * youtube.videos.list_by_tag (with paging)
    * 
    * @param string $tag The tag to get videos listed by
    * @return String $ret The api call to get videos by tag
    * @access public
    *  
    */
    public function videosListByTag($tag)
    {
        $ret = $this->apiUrl
          . $this->apiRestBase 
          . "method=youtube.videos.list_by_tag"
          . "&dev_id=" . $this->apiKey
          . "&tag=$tag&page="
          . $this->page. "&per_page="
          . $this->hitsPerPage;
        return $ret;
    }
    
    /**
    * 
    * Method to setup the call for videos to be listed by user
    * thus corresponding to the Youtube method
    * youtube.videos.list_by_user (with paging)
    * 
    * @param string $user The user to get videos listed by
    * @return String $ret The api call to get videos by tag
    * @access public
    *  
    */
    public function videosListByUser($user)
    {
        $ret = $this->apiUrl
          . $this->apiRestBase 
          . "method=youtube.videos.list_by_user"
          . "&dev_id=" . $this->apiKey
          . "&user=$user&page="
          . $this->page . "&per_page="
          . $this->hitsPerPage;
        return $ret;
    }
    
    /**
    * 
    * Method to setup the call for videos to be listed by playlist
    * thus corresponding to the Youtube method
    * youtube.videos.list_by_user (with paging)
    * 
    * @param string $user The user to get videos listed by
    * @return String $ret The api call to get videos by tag
    * @access public
    *  
    */
    public function videosListByPlaylist($playlist)
    {
        $ret = $this->apiUrl
          . $this->apiRestBase 
          . "method=youtube.videos.list_by_playlist"
          . "&dev_id=" . $this->apiKey
          . "&id=$playlist&page="
          . $this->page . "&per_page="
          . $this->hitsPerPage;
        return $ret;
    }
    
    /**
    * 
    * Method to look up the details of a video by the videoId using the
    * method outlined below:
    * api2_rest?method=youtube.videos.get_details&dev_id=dev_id&video_id=video_id
    * 
    * @param string $videoId The ID of the video of which we want the details
    * @return String $ret The api call to get the video information 
    * @access public
    *  
    */
    public function getVideoDetailsByVideoId($videoId)
    {
        $ret = $this->apiUrl
          . $this->apiRestBase 
          . "method=youtube.videos.get_details"
          . "&dev_id=" . $this->apiKey
          . "&video_id=" . $videoId;
        return $ret;
    }
    
    /**
    * 
    * Method to look up the details of a video author by the author using the
    * method outlined below:
    * api2_rest?method=youtube.users.get_profile&dev_id=dev_id&user=user
    * 
    * @param string $author The author to look up
    * @return String $ret The api call to get the author details
    * @access public
    *  
    */
    public function getVideoDetailsByAuthor($author)
    {
        $ret = $this->apiUrl
          . $this->apiRestBase 
          . "method=youtube.users.get_profile"
          . "&dev_id=" . $this->apiKey
          . "&user=" . $author;
        return $ret;
    }
    
    /**
    * 
    * Method to make the api call and return the XML object
    * It uses the Chisimba Curl class so that it works with 
    * proxy servers as configured in the Chisimba system
    * configuration.
    * 
    * @param string $callStr the URL formatted call string for the 
    * Youtube API
    * @return string object $apiXml The XML returned by simplexml_load_string
    * @access public
    *  
    */
    public function show($callStr=NULL)
    {
        //Send request and get XML back using the curl class to work with proxy server
        $objCurl = $this->getObject('curl', 'utilities');
        $apiRet = $objCurl->exec($callStr);
        $apiXml = simplexml_load_string($apiRet);
        unset($apiRet);
        return $apiXml;
    }

}
?>