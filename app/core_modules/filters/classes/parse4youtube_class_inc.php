<?php
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a yout tube video and render the video in the page
*
* @author Derek Keats
*
*/

class parse4youtube extends object
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
    public function parse($str)
    {
        preg_match_all('/\\[YOUTUBE]<a.*?href="(?P<youtubelink>.*?)".*?>.*?<\/a>\\[\/YOUTUBE]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $link = $results['youtubelink'][$counter];
            $videoId = $this->getVideoCode($link);
            $replacement = $this->getVideoObject($videoId);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }
    
    /**
     * 
     * Method to extract the video code from a youtube video link
     * The video link is after ?v=CODE, so we can extract the params
     * by splitting on ? and then the link by splitting on =
     * @param string $link The youtube video link
     * @return string The video code on Youtube
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
     * @param string $videoId The id of the Youtube video
     * @return String The object code
     * @access private
     * 
     */
    private function getVideoObject($videoId)
    {
        return "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/" 
          .  $videoId . "\"></param><param name=\"wmode\" value=\"transparent\"></param>"
		  . "<embed src=\"http://www.youtube.com/v/" . $videoId . "\" type=\"application/x-shockwave-flash\"" 
		  .	" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>";
    }
    
}