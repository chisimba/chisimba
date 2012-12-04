<?php
/**
 *
 * Render a MP3 audio
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a MP3 file which may be local or on the web.
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
 * Render a MP3 audio
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a MP3 file which may be local or on the web.
 *
 *
 * @author Derek Keats
 *
 */

class parse4mp3 extends object
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
        //Match straight URLs
        preg_match_all('/\\[MP3](.*?)\\[\/MP3]/', $str, $results2, PREG_PATTERN_ORDER);
        //Match filters based on the youtube module
        preg_match_all('/\\[MP3:(.*?)\\]/', $str, $results3, PREG_PATTERN_ORDER);
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $link = trim($results2[1][$counter]);
            $link = $this->unHref($link);
            $replacement = $this->getAudioObject($link);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        $counter = 0;
        foreach ($results3[0] as $item)
        {
            $link = trim($results3[1][$counter]);
            $link = $this->unHref($link);
            $replacement = $this->getAudioObject($link);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }

    /**
    *
    * Method to build the audio player
     * 
    * @param  string $link The MP3 file link
    * @return string The MP3 player with the file
    * @access private
    *
    */
    private function getAudioObject($link)
    {
        $sndPlayer = $this->getObject('buildsoundplayer', 'files');
        $sndPlayer->setSoundFile($link);
        return $sndPlayer->showFlashVersion();
    }
    
    /**
     *
     * Some browsers turn the link active on paste into CKEDITOR.
     * This is a counter to that bad behaviour, it extracts the link
     * properly.
     * 
     * @param string $link The messed up link to parse
     * @return string The link as the link text of the anchor tag
     * @Access private
     * 
     */
    private function unHref($link)
    {
        preg_match( '/<a[^>]+>([^<]+)<\/a>/si', $link, $m );
        return trim( $m[1] );
    }


}
?>