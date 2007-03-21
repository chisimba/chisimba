<?php
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load del.icio.us tags in the form [TAGS]username[TAGS]
*
* @author Derek Keats
*
*/

class parse4deltags extends object
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
        preg_match_all('/\\[DELTAGS](.*?)\\[\/DELTAGS]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
        	$replacement = $this->getTagJs($results[1][$counter]);
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
    public function getTagJs($deliciousUser)
    {
    	$ret = "<script type=\"text/javascript\"" 
		  . "src=\"http://del.icio.us/feeds/js/tags/$deliciousUser?icon;size=12-35;"
		  . "color=87ceeb-0000ff;title=my%20del.icio.us%20tags;name;showadd\"></script>";
        return $ret;
    }

}
?>