<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a timeline and render the timeline in the page
*
* @author Derek Keats
*
*/

class parse4timeline extends object
{
    
    function init()
    {
        $this->objTlParser = $this->getObject('timelineparser', 'timeline');
    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    function parseAll($str)
    {
    	return ">>>>>>>>>>>>>$str";
        $search = '/\\[TIMELINE](.*)[\/TIMELINE]/';
        $replace = "TIMELINE FOUND \\0 TIMELINEFOUND";
        return preg_replace($search, $replace, $str);
    }

}
?>