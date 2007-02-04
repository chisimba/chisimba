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
        $timeLineModuleLink = $this->Uri(array(), "timeline");
    }
    
    public function show()
    {
        $ret="";
        return $ret;
    }
?>
