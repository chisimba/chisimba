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
    
    public function init()
    {
        //There is nothing to do here
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
    	//Instantiate the modules class to check if simplemap is registered
    	$objModule = $this->getObject('modules','modulecatalogue');
    	//See if the simple map module is registered and set a param
    	$isRegistered = $objModule->checkIfRegistered('simplemap', 'simplemap');
    	if ($isRegistered){
	    	//Instantiate the simplemap parser
	    	$objSMParser = $this->getObject('smapparser', 'simplemap');
    	}
	    //Extract the URL
    	preg_match_all('/\\[SIMPLEMAP]<a.*?href="(?P<simplemaplink>.*?)".*?>.*?<\/a>\\[\/SIMPLEMAP]/', $str, $results, PREG_PATTERN_ORDER);
	    $counter = 0;
	    foreach ($results[0] as $item) {
	    	if ($isRegistered) {
	            $replacement = $objSMParser->getRemote($results['simplemaplink'][$counter]);
	    	} else {
    			$objLanguage = $this->getObject('language', 'language');
    	    	$replacement = $results[1][$counter] . "<br /><div class=\"error\"><h3>" 
    	      	  . $objLanguage->languageText("mod_filters_error_smapnotinstalled", "filters")
    	      	  . "</h3></div>";
	    	}
            $str = str_replace($item, $replacement, $str);
            $counter++;
    	}
        return $str;
    }

}
?>