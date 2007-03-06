<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a simplemap and render the simplemap in the page
*
* @author Derek Keats
*
*/

class parse4simplemap extends object
{
    
    function init()
    {
        $this->objSMParser = $this->getObject('smapparser', 'simplemap');
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
        preg_match_all('/\\[SIMPLEMAP]<a.*?href="(?P<simplemaplink>.*?)".*?>.*?<\/a>\\[\/SIMPLEMAP]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $replacement = $this->objSMParser->getRemote($results['simplemaplink'][$counter]);
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
        preg_match_all('/\\[SIMPLEMAP_LOCAL]<a.*?href="(?P<simplemaplink>.*?)".*?>.*?<\/a>\\[\/SIMPLEMAP_LOCAL]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $replacement = $this->objSMParser->getLocal($results['simplemaplink'][$counter]);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }
    
    /**
     * 
     * A parse method as required by the washout filter parser that washes all the output for 
     * parseable filters
     * 
     * @access Public
     * @param string $str The text to parse
     * @return string The parsed text 
     * 
     */
    public function parse($str)
    {
    	//check for type
    	preg_match_all('/\\[SIMPLEMAP]<a.*?href="(?P<simplemaplink>.*?)".*?>.*?<\/a>\\[\/SIMPLEMAP]/', $str, $remoteresults, PREG_PATTERN_ORDER);
    	preg_match_all('/\\[SIMPLEMAP_LOCAL]<a.*?href="(?P<simplemaplink>.*?)".*?>.*?<\/a>\\[\/SIMPLEMAP_LOCAL]/', $str, $localresults, PREG_PATTERN_ORDER);
    	if(!empty($remoteresults['simplemaplink']))
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