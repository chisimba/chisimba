<?php
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a page into an iframe in the form [IFRAME]URL|width|height[/IFRAME]
*
* @author Derek Keats
*
*/

class parse4iframe extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
    {

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
        //Get all the tags into an array
        preg_match_all('/\\[IFRAME](.*?)\\[\/IFRAME]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
        	$arParams = explode("|", $results[1][$counter]);
        	$url = $arParams[0];
        	$width = $arParams[1];
        	$height = $arParams[2];
        	$replacement = $this-> getIframe($url, $width, $height);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    /**
    * 
    * Method to get the javascript for displaying delicious tags
    * for $deliciousUser
    * 
    * @param string $deliciousUser The username on del.icio.us
    * @return string The javascript
    * 
    */
    public function getIframe($url, $width, $height)
    {
    	return "<iframe src=\"$url\" width=\"$width\" height=\"$height\"></iframe>"; 
    }

}
?>