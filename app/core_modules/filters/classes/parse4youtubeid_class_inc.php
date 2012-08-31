<?php
/**
 *
 * Render a Youtube Video by ID
 *
 * Parse a string (e.g. page content) that contains a tube video id
 *  and render the video in the page
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Parse a string (e.g. page content) that contains a tube video id
 *  and render the video in the page
 *
 * @author Derek Keats
 *
 */

class parse4youtubeid extends object
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
        preg_match_all('/\\[YOUTUBEID](.*?)\\[\/YOUTUBEID]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $videoId = $results[1][$counter];
            $link = 'http://http://www.youtube.com/watch?v=' . $videoId;
            //Check if it is a valid link, if not return an error message
            if ($this->isYoutube($link)) {
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
    * Method to build the youtube video object code
    * @param  string $videoId The id of the Youtube video
    * @return String The object code
    * @access public
    *
    */
    public function getVideoObject($videoId)
    {
        $vidPlayer = '<div class="videoWrapper"><iframe class="youtube-player" '
          . 'type="text/html" width="640" height="385" '
          . 'src="http://www.youtube.com/embed/' . $videoId 
          .'" frameborder="0" allowfullscreen></iframe></div>';
        return $vidPlayer;
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
}
?>