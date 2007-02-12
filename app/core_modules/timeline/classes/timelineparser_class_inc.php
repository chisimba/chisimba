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

	/*
	* @var string $timeLineModuleLink Holds the link for the timeline module
	*/
    public $timeLineModuleLink;
    
    /**
    *
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {
		//Create the base URL to add to
        $this->timeLineModuleLink = $this->Uri(array(), "timeline");
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
	
	/*
	 * 
	 * A method to extract a querystring parameter and value from a URL supplied
	 * as a string. For example, when supplied with:
	 *   http://localhost/chsimba/index.php?action=read&text=mytext
	 *   $this->getParamFromStringUri("action") will return
	 *   "read". $this->getParamFromStringUri("someparam") will return
	 *   NULL.
	 *
	public function getParamFromStringUri($paramname)
	{
	    if (isset($this->uri)) {
	        if (!instr($$paramname, $this->uri)) {
	            return NULL;
	        } else {
	            //Try to extract it.
	            $matchPattern = "/" . $paramname . "=.[&\n]/i" ;
	        }
	    } else {
	        return NULL;
	    }
	}*/
    /**
     * 
     * Method to render the timelines
     * @return string The rendered iframe
     * 
     */
    public function show()
    {
    	$objIframe = $this->getObject('iframe', 'htmlelements');
    	$objIframe->width = "100%";
    	$objIframe->height="330";
        $ret = $this->timeLineModuleLink;
        $ret .= "&mode=plain&timeline=" . urlencode($this->uri);
        $objIframe->src=$ret;
        return $objIframe->show();
    }
}
?>