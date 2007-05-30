<?php
/**
*
* Class to parse a string (e.g. page content) that contains a a URL for a 
* PDF file and embeds it in the page
*
* @author Derek Keats
*
*/

class parse4pdf extends object
{
    
    public function init()
    {
        //Nothing to do here
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
        preg_match_all('/\\[PDF](.*?)\\[\/PDF]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
    			//$objLanguage = $this->getObject('language', 'language');
    	    	$replacement = "<EMBED src=\"" . $results[1][$counter] 
    	    	  . "\" href=\"" . $results[1][$counter] ."\" width=\"100%\" height=\"500\"></EMBED>";
        	   $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
}