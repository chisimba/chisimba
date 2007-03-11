<?
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Timeline is a DHTML-based AJAXy widget for visualizing time-based events.
* This class is used to build the IFRAME that is used to include a timeline
* in a page.
* 
* Timeline is from the MIT Simile project at
*     http://simile.mit.edu/timeline/
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright AVOIR and UWC
* @licence GNU/GPL
*
*/
class timelineparser extends object
{  
    /**
    * 
    * @var string $demoTimeline Holds the value of the demo timeline
    * 
    */
    public $demoTimeline;
    
    /**
    * 
    * @var string $uri Holds the value of the timeline to display
    * 
    */
    public $uri;
   
    /**
    *
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {

    }
    
    /*
     * 
     * Method to set the uri parameter for the timeline to be 
     * parsed.
     * 
     * @access public
     * @return string The URI for the timeline to be parsed
     * 
     */
	public function setTimelineUri($uri)
	{
	    $this->uri = $uri;
	    return TRUE;
	}
	
	/*
	 * 
	 * Method to get the timeline Uri as stored
	 * 
	 * @access public
	 * @return string The URI or Null if not set
	 * 
	 */
	public function getTimelineUri()
	{
	    if (isset($this->uri)) {
	        return $this->uri;
	    } else {
	        return NULL;
	    }
	}
	
    /**
     * 
     * Method to render the timelines
     * @return string The rendered iframe
     * 
     */
    public function show()
    {
    	$method = $this->getParam("method", "remote");
    	if ($method == "local") {
    		$tlId = $this->getParam("id", NULL);
    		if ($tlId == NULL) {
    		    return "Replace this with language text for id missing";
    		} else {
				return $this->getLocal($tlId);	    
    		}    	    
    	} else {
			return $this->getRemote($this->uri);
    	}
        
    }
    
    /**
     * 
     * Method to return a local timeline by passing it the id field
     * from the database
     * 
     * @param string $id The id for the record to get
     * @access public
     * @return string An iframe containing the formatted timeline
     * 
     */
    public function getLocal($id) {
    	$objIframe = $this->getObject('iframe', 'htmlelements');
    	$objIframe->width = "100%";
    	$objDb = $this->getObject("dbstructure", "timeline");
    	$ar = $objDb->getRow("id", $id);
		if (isset($ar)) {
		    $id = $ar['id'];
		    $title = $ar['title'];
		    $description = $ar['description'];
		    $url = $ar['url'];
		    $this->uri = $url;
		    $focusdate = $ar['focusdate'];
		    $intervalpixels = $ar['intervalpixels'];
		    $intervalunit = $ar['intervalunit'];
		    $tlheight = $ar['tlheight'];
        	$ret = $this->uri(array(
			  "mode" => "plain",
	          "action" => "viewtimeline",
	          "focusDate" => $focusdate,
	          "intervalPixels" => $intervalpixels,
	          "intervalUnit" => $intervalunit,
	          "tlHeight" => $tlheight,
			  "timeLine" => $url), "timeline");
			$objIframe->height=$tlheight + 5;
		} else {
			$objIframe->height=200;
			$ret = $this->uri(array("action" => "sendnotfounderror"), "timeline");
		}
        $objIframe->src=$ret;
        return $objIframe->show();
    }
    
    /**
     * 
     * Method to get a remote timeline and show it with default settings
     * @return string the formatted IFRAME
     * @access public
     * 
     */
    public function getRemote($timeline) {
        $objIframe = $this->getObject('iframe', 'htmlelements');
    	$objIframe->width = "100%";
    	$objIframe->height="330";
     	//if ($this->urlExists($timeline)) {
	        $ret = $this->uri(array("mode" => "plain",
	          "action" => "viewtimeline", 
			  "timeLine" => $timeline), "timeline");
    	//} else {
		//	$objIframe->height=200;
		//	$ret = $this->uri(array("action" => "sendnotfounderror"), "timeline"); 
    	//}
		$objIframe->src=$ret;
        return $objIframe->show();
    }
    
    /**
    * Method to test whether an XML file is a Valid Timeline
    * @param string $timelinePath Path to Timeline
    * @return boolean
    */
    public function isValidTimeline($timelinePath)
    {
        $xml = simplexml_load_file($timelinePath);
        return $this->testTimeline($xml);
    }

    /**
    * Method to test whether XML content of a file is Valid Timeline
    * @param string $xml XML contents of a file
    * @return boolean
    * @access private
    */
    private function testTimeLine($xml)
    {
        $isTimeline = FALSE;
        
        // Test Root Element
        if ($xml->getName() == 'data') {
            $isTimeline = TRUE;
        } else {
            return FALSE;
        }
        
        // Check Children
        foreach ($xml->children() as $second_gen) 
        {
            if ($second_gen->getName() == 'event') {
                $isTimeline = TRUE;
                
                if (count($second_gen->children()) == 0) {
                    $isTimeline = TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
        
        return $isTimeline;
    }
    
    //WORKING HERE --Derek
    function urlExists($url) {
    	die($url);
	    $resURL = curl_init();
	    curl_setopt($resURL, CURLOPT_URL, $url);
	    curl_setopt($resURL, CURLOPT_BINARYTRANSFER, 1);
	    curl_setopt($resURL, CURLOPT_HEADERFUNCTION, 'curlHeaderCallback');
	    curl_setopt($resURL, CURLOPT_FAILONERROR, 1);
	    curl_exec ($resURL);
	    $intReturnCode = curl_getinfo($resURL, CURLINFO_HTTP_CODE);
	    curl_close ($resURL);
	    if ($intReturnCode != 200 && $intReturnCode != 302 && $intReturnCode != 304) {
	       return FALSE;
	    } else {
	        return TRUE;
	    }
    }
}
?>