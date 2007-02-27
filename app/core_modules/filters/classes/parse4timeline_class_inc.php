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
            $replacement = $this->objTlParser->getRemote();
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    function showLocal($str)
    {
        preg_match_all('/\\[TIMELINE_LOCAL]<a.*?href="(?P<timelinelink>.*?)".*?>.*?<\/a>\\[\/TIMELINE_LOCAL]/', $str, $results, PREG_PATTERN_ORDER);
        /*preg_match_all('/\\[TIMELINE_LOCAL](<timelinelink>)\\[\/TIMELINE_LOCAL]/', $str, $results, PREG_PATTERN_ORDER);*/
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $replacement = $this->objTlParser->getLocal($results['timelinelink'][$counter]);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }
    
    public function parse($str)
    {
    	//check for type
    	preg_match_all('/\\[TIMELINE]<a.*?href="(?P<timelinelink>.*?)".*?>.*?<\/a>\\[\/TIMELINE]/', $str, $remoteresults, PREG_PATTERN_ORDER);
    	preg_match_all('/\\[TIMELINE_LOCAL]<a.*?href="(?P<timelinelink>.*?)".*?>.*?<\/a>\\[\/TIMELINE_LOCAL]/', $str, $localresults, PREG_PATTERN_ORDER);
    	if(!empty($remoteresults['timelinelink']))
    	{
    		$type = 'remote';
    	}
    	else {
    		$type = 'local';
    	}
    	switch($type)
    	{
    		case 'remote':
    			return $this->show($str);
    			break;
    		case 'local':
    			return $this->showLocal($str);
    			break;
    			
    	}
    	
    }

}