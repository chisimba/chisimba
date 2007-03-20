<?php
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a google video in the form [GVID]username[/GVID]
*
* @author Derek Keats
*
*/

class parse4googlevid extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    function init()
    {

    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    function parse($str)
    {
        $str = stripslashes($str);
        //Get all the tags into an array
        preg_match_all('/\\[GVID]<a.*?href="(?P<gvlink>.*?)".*?>.*?<\/a>\\[\/GVID]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item) {
        	$link = $results['gvlink'][$counter];
        	$videoId = $this->getVideoCode($link);
        	$replacement = $this->getVideoObject($videoId);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }

    /**
     * 
     * Method to extract the video code from a Google video link
     * The video link is after ?docId=CODE, so we can extract the params
     * by splitting on ? and then the link by splitting on =
     * @param string $link The Google video link
     * @return string The video code on Google video
     * @access private 
     * 
     */
    private function getVideoCode($link)
    {
        $vCode = explode("?", $link);
        $vTxt = $vCode[1];
        $vCode = explode("=", $vTxt);
        $vTxt = $vCode[1];
        return $vTxt;
    }
    
    /**
     * 
     * Method to build the youtube video object code
     * @param string $videoId The id of the Google video
     * @return String The object code
     * @access private
     * 
     */
    private function getVideoObject($videoId) {
    	$ret = "<embed style=\"width:400px; height:326px;\" 
		  id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" 
		  src=\"http://video.google.com/googleplayer.swf?docId=$videoId&hl=en\" 
		  flashvars=\"playerMode=embedded\"> </embed>";
	return $ret;   
    }

}
?>