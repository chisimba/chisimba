<?php
/**
*
* Class to parse a string (e.g. page content) that contains a wordpress 
* link to a slideshare slide presentation or a [SS][/SS] pair. The pattern
* to match is [slideshare id=55016&doc=web2-seminar-22859&w=425]
*
* @author Derek Keats
*
*/

class parse4ss extends object
{
	/**
	* 
	* String to hold an error message
	* @accesss private 
	*/
	private $errorMessage;
    private $width;
    private $height;
    
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
        //Match straight URLs
        $this->width = "425";
        $this->height = "348";
        $regEx = "/\\[slideshare(.*?)\\]/";
        //[slideshare id=20847&doc=fantastic-photography-3404&w=425]
        preg_match_all($regEx, $str, $results, PREG_PATTERN_ORDER);
        //Extract all the matches
        $counter = 0;
        foreach ($results[0] as $item)
        {
            if ($this->isSlideShare($item)) {
                $exPat = $results[1][$counter];
                //Get an array containing the param=value data
                $arPat = explode("&", $exPat);
                $arTmp = explode("=", $arPat['0']);
                $id = $arTmp[1];
                $arTmp = explode("=", $arPat['1']);
                $doc = $arTmp[1];
                $arTmp = explode("=", $arPat['2']);
                $w = $arTmp[1];
                $this->width=$w;
                $replacement = $this->getSlideObject($id,$doc);
            } else {
              $objLanguage = $this->getObject('language', 'language');
                $replacement = "<span class=\"error\">" 
              . $objLanguage->languageText("mod_filters_error_notss", "filters")
              . ":<br />$item</span>";
            }
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }

    
    /**
     * 
     * Method to build the youtube video object code
     * @param string $videoId The id of the Youtube video
     * @return String The object code
     * @access private
     * 
     */
    private function getSlideObject($id,$doc)
    {
          return "<object type=\"application/x-shockwave-flash\" "
            ."data=\"https://s3.amazonaws.com:443/slideshare/ssplayer.swf?"
            . "id=$id&doc=$doc\" width=\"" . $this->width . "\" height=\"" 
            . $this->height ."\"><param name=\"movie\" value=\""
            . "https://s3.amazonaws.com:443/slideshare/ssplayer.swf?id=$id&doc=$doc\" /></object>";
    }
    
    private function extractParams($str)
    {
        $arPat = explode("&", $str);
    }
    
    /**
     * 
     * Simple method to validate if the contents is a valid slideshare 
     * wordpress link
     * 
     * @param string $item The string to evaluate
     * @return TRUE|FALSE  
     * 
     */
    private function isSlideShare($item)
    {
        if (!strstr($item, "&") || !strstr($item, "id=") || !strstr($item, "doc=")) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
 
    
}
?>