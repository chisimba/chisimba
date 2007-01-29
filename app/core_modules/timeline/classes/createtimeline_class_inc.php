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
class createtimeline 
{

    /**
    *
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {
        //Put your code here
    }
    
    public function show()
    {
 
        $ret="";
        $ret .= $this->_getDiv();
        return $ret;
    }

    /**
    *
    * Your method description. Please follow correct
    * PHP Documentor style
    *
    */
    private function _addHeaderScript()
    {
        //Create the script tag to add to the header
        //$scriptTag = $this->getJavascriptFile(‘timeline-api.js’, ‘timeline’);
        //$this->getResourceUri(‘timeline-api.js’, ‘timeline’);
        
        
    }
    
    public function getScript()
    {
        return "\n\n<script language=\"javascript\"><!--
var tl;
var eventSource = new Timeline.DefaultEventSource();
function onLoad() {
  var bandInfos = [
    Timeline.createBandInfo({
        eventSource:    eventSource,
        date:           \"Jan 1 1918 00:00:00 GM\T\",
        width:          \"100%\", 
        intervalUnit:   Timeline.DateTime.YEAR, 
        intervalPixels: 100
    })
  ];
  tl = Timeline.create(document.getElementById(\"my-timeline\"), bandInfos);
  Timeline.loadXML(\"http://localhost/chisimba/experiments/timeline/madiba.xml\", function(xml, url) { eventSource.loadXML(xml, url); });
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
    }

    
    private function _getDiv()
    {
        return "\n\n<div id=\"my-timeline\" style=\"height: 150px; border: 1px solid #aaa\"></div>\n\n";
    }
}
?>
