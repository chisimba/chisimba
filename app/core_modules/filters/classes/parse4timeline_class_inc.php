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
    function show($str)
    {
        preg_match_all('/\\[TIMELINE]<a.*?href="(?P<timelinelink>.*?)".*?>.*?<\/a>\\[\/TIMELINE]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $this->objTlParser->$results['timelinelink'][$counter];
            $replacement = $this->objTlParser->show();
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }
    
    public function parse($str)
    {
    	return $this->show($str);
    }

}