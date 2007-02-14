<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for testing output plugins.
* There is no end user functionality here
*
* @author Derek Keats
* @package outputplugins
*
*/
class outputplugins extends controller
{
    /**
     * Intialiser for the controller
     */
    public function init()
    {

    }
    
    
    /**
     * 
     * The standard dispatch method for the _MODULECODE module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
    	//Get test data for parsing a timeline
        //Create the config reader and get the location of demo maps
        $objSconfig =  $this->getObject('altconfig', 'config');
        $timeLine =  $objSconfig->getItem('MODULE_URI') . "timeline/resources/demodata/madiba.xml";
        
        $str = "Here is some text from Wikipedia to fill up space. A plugin (or " .
        		"plug-in) is a computer program that interacts with a main (or host) " .
        		"application (a web browser or an email program, for example) to " .
        		"provide a certain, usually very specific, function on-demand. Here " .
        		"for example, is a timeline, parsed by the timeline parser plugin <br />" .
        		"[TIMELINE]" . $timeLine . "[/TIMELINE]<br /> It is really kind of cute " .
        		"is it not? <br /><br />The host application provides services which the " .
        		"plugins can use, including a way for plugins to register themselves " .
        		"with the host application and a protocol by which data is exchanged " .
        		"with plugins. Plugins are dependent on these services provided by " .
        		"the main application and do not usually work by themselves. Conversely, " .
        		"the main application is independent of the plugins, making it possible " .
        		"for plugins to be added and updated dynamically without changes to " .
        		"the main application.";
        $objParseForAll = $this->getObject('washout', 'utilities');
        $this->setVarByRef("str", $str);
        return "dump_tpl.php";
    }
    
}