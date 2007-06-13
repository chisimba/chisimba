<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a mySpace video and render the video in the page
*
* @author Derek Keats
*
*/

class parse4myspacevideo extends object
{
    
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
    public function parse($str)
    {
        preg_match_all('/\\[MYSPACEVID]<a.*?href="(?P<youtubelink>.*?)".*?>.*?<\/a>\\[\/MYSPACEVID]/', $str, $results, PREG_PATTERN_ORDER);
        //Match straight URLs
        preg_match_all('/\\[MYSPACEVID](.*?)\\[\/MYSPACEVID]/', $str, $results2, PREG_PATTERN_ORDER);
        
        //Get all the ones in links
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $link = $results['youtubelink'][$counter];
            $videoId = $this->getVideoCode($link);
            $replacement = $this->getVideoObject($videoId);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item) {
        	$link = $results2[1][$counter];
            $videoId = $this->getVideoCode($link);
            $replacement = $this->getVideoObject($videoId);
            $str = str_replace($item, $replacement, $str);
            $counter++;
    	}
        
        return $str;
    }
    
    /**
     * 
     * Method to extract the video code from a myspace video link
     * @param string $link The myspace video link
     * @return string The video code on mySpace
     * @access private 
     * 
     */
    private function getVideoCode($link)
    {
        $vCode = explode("?", $link);
        $vTxt = $vCode[1];
        $vCode = explode("=", $vTxt);
        $vTxt = $vCode[2];
        return $vTxt;
    }
    
    /**
     * 
     * Method to build the youtube video object code
     * @param string $videoId The id of the Youtube video
     * @return String The object code
     * @access private
     * 
     */
    private function getVideoObject($videoId)
    {
		$vid = "<embed src=\"http://lads.myspace.com/videos/vplayer.swf\" flashvars=\"m="
		  . $videoId . "&amp;type=video\" type=\"application/x-shockwave-flash\""
		  . " width=\"430\" height=\"346\"></embed>";
		return $vid;
    }
    
}