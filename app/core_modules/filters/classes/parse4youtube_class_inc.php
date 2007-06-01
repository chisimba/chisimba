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
	/**
	* 
	* String to hold an error message
	* @accesss private 
	*/
	private $errorMessage;
    
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
    	//Match the ones that are in links
        preg_match_all('/\\[YOUTUBE]<a.*?href="(?P<youtubelink>.*?)".*?>.*?<\/a>\\[\/YOUTUBE]/', $str, $results, PREG_PATTERN_ORDER);
        //Match straight URLs
        preg_match_all('/\\[YOUTUBE](.*?)\\[\/YOUTUBE]/', $str, $results2, PREG_PATTERN_ORDER);
        //Get all the ones in links
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $link = $results['youtubelink'][$counter];
            //Check if it is a valid link, if not return an error message
            if ($this->isYoutube($link)) {
            	$videoId = $this->getVideoCode($link);
            	$replacement = $this->getVideoObject($videoId);
            } else {
            	$replacement = $this->errorMessage;
            }
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $link = $results2[1][$counter];
            //Check if it is a valid link, if not return an error message
            if ($this->isYoutube($link)) {
            	$videoId = $this->getVideoCode($link);
            	$replacement = $this->getVideoObject($videoId);
            } else {
            	$replacement = $this->errorMessage;
            }
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
    
    /**
    *
    *  A method to validate a link as a valid Youtube video link. It should start with http, 
    *  and have v= in it. It sets the value of the errorMessage property to be the appropriate
    *  error.
    * 
    * @param string $link The link to check
    * @return boolean TRUE|FALSE True if it is a valid link, false otherwise
    *  
    */
    private function isYoutube($link)
    {
    	$link=strtolower($link);
    	if (strstr($link,"http://") && strstr($link, "v=")) {
    		return TRUE;
    	} else {
   			$objLanguage = $this->getObject('language', 'language');
    		$this->errorMessage = "[YOUTUBE] <span class=\"error\">" 
    	  	  . $objLanguage->languageText("mod_filters_error_notyoutube", "filters")
    	  	  . "</span> [/YOUTUBE]";
    		return FALSE;
    	}
 
    }
    
}