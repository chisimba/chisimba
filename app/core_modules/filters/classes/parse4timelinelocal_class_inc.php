<?php
/**
*
* Class to parse a string (e.g. page content) that contains a timeline by
* its id render the timeline in the page
*
* @author Derek Keats
*
*/

class parse4timelinelocal extends object
{
    
    public function init()
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
    public function parse($str)
    {
    	$str = stripslashes($str);
        preg_match_all('/\\[TIMELINE_LOCAL](.*?)\\[\/TIMELINE_LOCAL]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $replacement = $this->objTlParser->getLocal($results[1][$counter]);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
}