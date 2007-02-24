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
				return $this->__getLocal($tlId);	    
    		}    	    
    	} else {
			return $this->__getRemote();    	    
    	}
        
    }
    
    /**
     * 
     * Method to return a local timeline by passing it the id field
     * from the database
     * 
     * @param string $id The id for the record to get
     * @access private
     * @return string An iframe containing the formatted timeline
     * 
     */
    private function __getLocal($id) {
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
		}
		$objIframe->height=$tlheight + 5;
        $ret = $this->uri(array(
		  "mode" => "plain",
          "action" => "viewtimeline",
          "focusDate" => $focusdate,
          "intervalPixels" => $intervalpixels,
          "intervalUnit" => $intervalunit,
          "tlHeight" => $tlheight,
		  "timeLine" => $url), "timeline");
        $objIframe->src=$ret;
        return $objIframe->show();
    }
    
    
    private function __getRemote() {
        $objIframe = $this->getObject('iframe', 'htmlelements');
    	$objIframe->width = "100%";
    	$objIframe->height="330";
        $uri = $this->uri;
        $ret = $this->uri(array("mode" => "plain",
          "action" => "viewtimeline", 
		  "timeLine" => $uri), "timeline");
        $objIframe->src=$ret;
        return $objIframe->show();
        
    }
}
?>