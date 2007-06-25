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
            $extracted = $results[1][$counter];
        	if (strstr($extracted, "|")) {
        		$arParams = explode("|", $results[1][$counter]);
        		$repl = $arParams['0'];
        		$width = $arParams['1'];
	        	if (count($arParams) >= 2) {
        			$height = $arParams['2'];
	        	} else {
	        		$height = "500";
	        	}
        	} else {
        		$height = "500";
        		$width = "100%";
        		$repl = $results[1][$counter];
        	}
    		$replacement = "<EMBED src=\"" . $repl . "\" href=\"" 
    		  . $repl ."\" width=\"$width\" height=\"$height\"></EMBED>";
   	   		$str = str_replace($item, $replacement, $str);
       		$counter++;
        }
        return $str;
    }
}