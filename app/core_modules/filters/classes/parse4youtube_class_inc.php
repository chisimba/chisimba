<?php

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a yout tube video and render the video in the page
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   filters
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */
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

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *
    */
    public function parse($str)
    {
    	//Match the ones that are in links
        preg_match_all('/\\[YOUTUBE]<a.*?href="(?P<youtubelink>.*?)".*?>.*?<\/a>\\[\/YOUTUBE]/', $str, $results, PREG_PATTERN_ORDER);
        //Match straight URLs
        preg_match_all('/\\[YOUTUBE](.*?)\\[\/YOUTUBE]/', $str, $results2, PREG_PATTERN_ORDER);
        //Match filters based on the youtube module
        preg_match_all('/\\[YOUTUBE:(.*?)\\]/', $str, $results3, PREG_PATTERN_ORDER);
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
        //Instantiate the modules class to check if youtube is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the youtube API module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('youtube', 'youtube');
        if ($isRegistered){
            $flagOk = TRUE;
        } else {
            $flagOk = FALSE;
        }
        //Parse the youtube page tags
        $counter = 0;

        foreach ($results3[0] as $item) {
            $exPat = $results3[1][$counter];
            //Get an array containing the param=value data
            //The format is [YOUTUBE: by_tag,tag,2,1,24]
            if ($flagOk == TRUE) {
            	// moved the instantiation to inside the flag check as per bug #0002236
            	//Instantiate the youtubeapi class
        		$objYouTube = $this->getObject('youtubeapi', 'youtube');
        		//Get the view class for youtubeapi
        		$vw = $this->getObject('youtubetpl','youtube');

        		$arCodes = $this->extractYoutubeCodes($exPat);
                $ytmethod = $arCodes['ytmethod'];
                $identifier = $arCodes['ytidentifier'];
                $cols = $arCodes['cols'];
                $page = $arCodes['ytpage'];
                $hitsperpage = $arCodes['hitsperpage'];
                $vw->set('hitsPerPage', $hitsperpage);
                $objYouTube->set('ytMethod', $ytmethod);
                $objYouTube->set('ytIdentifier', $identifier);
                $vw->set('ytIdentifier', $identifier);
                $objYouTube->set('ytpage', $page);
                $objYouTube->set('hitsPerPage', $hitsperpage);
                $vw->set('cols', $cols);
                //$objYouTube->ytIdentifier = $identifier;
                $apiXml = $objYouTube->show($objYouTube->setupCall());
                $replacement = $vw->showVideos($apiXml);
            } else {
                $replacement = $item . "<br .<span class=\"error\">"
                  .  $this->objLanguage->languageText('mod_filters_error_noyoutube', 'filters')
                  . "</span>";
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
    * @param  string $link The youtube video link
    * @return string The video code on Youtube
    * @access public
    *
    */
    public function getVideoCode($link)
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
    * @param  string $videoId The id of the Youtube video
    * @return String The object code
    * @access public
    *
    */
    public function getVideoObject($videoId)
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
    * @param  string  $link The link to check
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

    /**
    *
    * Method to build an array of keys and values to build the call to
    * the youtube API
    *
    * The tag format is [YOUTUBE: ytmethod, identifier, page, hitsperpage]
    * Example: [YOUTUBE: by_tag, digitalfreedom, cols, 1, 12]
    *
    * @param  string $exPat The extracted pattern containing a comma delimited
    *                       string in the form: ytmethod, identifier, page, hitsperpag
    *
    * @return string array An arrray of keys and values
    *
    */
    public function extractYoutubeCodes($exPat)
    {
        $arCodes = explode(",", $exPat);
        $arRet = array(
          'ytmethod' => $arCodes[0],
          'ytidentifier' => $arCodes[1],
          'cols' => $arCodes[2],
          'ytpage' => $arCodes[3],
          'hitsperpage' => $arCodes[4]);
        return $arRet;
    }

}
?>