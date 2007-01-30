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
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {
        //Create the configuration object
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Set the value of the default demo timeline
        $this->timeLine = $this->getParam('timeLine', 'experiments/timeline/madibademo.xml');
        //Set the date that is the default focus
        $this->focusDate= $this->getParam('focusDate', 'Jan 1 1965 00:00:00 GMT');
        //Set the default interval pixels
        $this->intervalPixels = $this->getParam('intervalPixels', '80');
        //Get the default interval unit for the divisions
        $this->intervalUnit = $this->getParam('intervalUnit', 'YEAR');
    }
    
    public function show()
    {

 
        $ret="";
        $ret .= $this->_getDiv();
        return $ret;
    }


    

    
    public function getScript()
    {
    	//Set it to display the demo if no timeline is set
    	if ($this->timeline=="") {
    	    $this->timeline = $this->demoTimeline;
    	}
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
			  Timeline.loadXML(\"" . $this->timeline . "\", function(xml, url) { eventSource.loadXML(xml, url); });
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
	* A method to return the url for the javascrpt library 
	*/

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
        return "\n\n<div id=\"my-timeline\" style=\"height: 150px; border: 1px solid #aaa\"></div>\n\n";
    }
}
?>
