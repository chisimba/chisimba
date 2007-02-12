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
* This class is used to build the scripts and timeline pages needed to run
* the library and create a timeline.
* 
* Timeline is from the MIT Simile project at
*     http://simile.mit.edu/timeline/
*
* The following parameters can be passed in the querystring:
*   timeLine - Fully qualified URI for the timeline XML file
*   focusDate - THe date to centre the timeline on
*   intervalPixels - The number of pixels for the width of the interval
*   intervalUnit - The timelines unit for the interval (e.g. DAY, MONTH, YEAR)
*   tlHeight - the height in pixels of the timeline
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright AVOIR and UWC
* @licence GNU/GPL
*
*/
class createtimeline extends object
{

    /**
    * 
    * @var $objConfig String object property for holding the 
    * configuration object
    * @access public
    * 
    */
    public $objConfig;
    /**
    * 
    * @var $objSconfig String object property for holding the 
    * configuration object for the module uri
    * @access public
    * 
    */
    public $objSconfig;
    
    /**
    * 
    * @var string $demoTimeline Holds the value of the demo timeline
    * 
    */
    public $demoTimeline;
    
    /**
    * 
    * @var string $timeline Holds the value of the timeline to display
    * 
    */
    public $timeline;
    /**
    * 
    * @var string $timeline Holds the value of the timeline to display
    * 
    */
    public $tlHeight;
    
    /**
    *
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {
    	//die($this->getResourceUri('deadfile.js'));
        //Create the configuration object
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        //Create the config reader and get the location of demo maps
        $objSconfig =  $this->getObject('altconfig', 'config');
        $demoData =  $objSconfig->getItem('MODULE_URI') . "timeline/resources/demodata/madiba.xml";
        //Set the value of the default demo timeline
        $this->timeLine = $this->getParam('timeLine', $demoData);
        //Set the date that is the default focus
        $this->focusDate= $this->getParam('focusDate', 'Jan 1 1965 00:00:00 GMT');
        //Set the default interval pixels
        $this->intervalPixels = $this->getParam('intervalPixels', '80');
        //Get the default interval unit for the divisions
        $this->intervalUnit = $this->getParam('intervalUnit', 'YEAR');
        //Get the default height for the timeline
        $this->tlHeight = $this->getParam('tlHeight', '250');
    }
    
    /**
    * 
    * Render the timeline as a string and return it for display
    * 
    * @return string The rendered dtimeline
    * @access public
    * 
    */
    public function show()
    {
        $ret="";
        $ret .= $this->_getDiv();
        $ret .= $this->_getDisplayFrame();
        return $ret;
    }

	/**
	* 
	* Standard setter method
	* 
	* @return TRUE
	* @access public
	* 
	*/
	public function setValue($item, $value)
	{
	    $this->$item=$value;
	    return TRUE;
	}
    
    /**
     * 
     * Standard getter method
     * 
     * @param string $item The name of the property to return
     * @return the value of the parameter requested
     * @access public
     * 
     */
	public function getValue($item)
	{
	    return $this->$item;
	}
    
    /**
     * 
     * Method to return the Javascript to create a basic timeline
     * @return string The javascript for rendering the timeline
     * 
     */
    public function getScript()
    {
        return "\n\n<script language=\"javascript\"><!--
			var tl;
			var eventSource = new Timeline.DefaultEventSource();
			function onLoad() {
			  var bandInfos = [
			    Timeline.createBandInfo({
			        eventSource:    eventSource,
			        date:           \"" . $this->focusDate . "\",
			        width:          \"100%\", 
			        intervalUnit:   Timeline.DateTime." . $this->intervalUnit . ", 
			        intervalPixels: " . $this->intervalPixels . "
			    })
			  ];
			  tl = Timeline.create(document.getElementById(\"my-timeline\"), bandInfos);
			  Timeline.loadXML(\"" . $this->timeLine . "\", function(xml, url) { eventSource.loadXML(xml, url); });
			}
			
			var resizeTimerID = null;
			function onResize() {
			    if (resizeTimerID == null) {
			        resizeTimerID = window.setTimeout(function() {
			            resizeTimerID = null;
			            tl.layout();
			        }, 500);
			    }
			}
			-->
			</script>\n\n";
		//end of script
    }

	/**
	* 
	* Method to return the DIV tag that needs to be in the page for the
	* timeline to display
	* 
	* @return String the timeline DIV as a string
	* 
	*/
    private function _getDiv()
    {
        return "\n\n<div id=\"my-timeline\" style=\"height: " . $this->tlHeight . "px; border: 1px solid #aaa\"></div>\n\n";
    }
    
    private function _getDisplayFrame()
    {
        $showTextDisplay = $this->getParam('showLinkFrame', FALSE);
        if (!$showTextDisplay==FALSE) {
            $objFrame = $this->getObject('iframe', 'htmlelements');
            $objFrame->src="";
            $objFrame->width="99%";
            $objFrame->height="400px";
            $objFrame->align="center";
            $objFrame->id="TimelineDisplayFrame";
            $objFrame->name="TimelineDisplayFrame";
            return "<br />" . $objFrame->show();
        } else {
            return NULL;
        }
    }
}
?>
