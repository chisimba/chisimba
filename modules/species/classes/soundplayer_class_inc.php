<?php
/**
 *
 * Create a sound player for a URL Sound
 *
 * Create a sound player for a URL Sound using HTML5 Audio where possible, but
 * resorting back to other methods where HTML5 audio is not supported.
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
 * @package   species
 * @author    Derek Keats derek@localhost.local
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Create a sound player for a URL Sound
 *
 * Create a sound player for a URL Sound using HTML5 Audio where possible, but
 * resorting back to other methods where HTML5 audio is not supported.
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class soundplayer extends object
{
    /**
    *
    * Intialiser for the species operations class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
    }
         
    /**
     * 
     * Embed the found audio file in a player, detecting Firefox which
     * won't play MP3 files as native HTML5 audio
     * 
     * @param type $url
     * @return type
     * @access public
     * 
     */
    public function embedAudio($url)
    {
        if ($this->isFirefox()) {
            // Do a Flash player
            $objSoundPlayerBuilder = $this->newObject('buildsoundplayer', 'files');
            $objSoundPlayerBuilder->setSoundFile($url);
            return $objSoundPlayerBuilder->show();
        } else {
            // Do an HTML5 player
            $doc = new DOMDocument('UTF-8');
            $snd = $doc->createElement('audio');
            $snd->setAttribute('controls', 'controls');
            $file = $doc->createElement('source');
            $file->setAttribute('src', $url);
            $file->setAttribute('type', "audio/mpeg");
            $snd->appendChild($file);
            $doc->appendChild($snd);
            return $doc->saveHTML();
        }
    }
    
    /**
     * 
     * Check if a browser is Firefox (because Firefox cannot embed MP3 via
     * the HTML5 AUDIO tag)
     * 
     * @return boolean TRUE|FALSE
     * @access private
     * 
     */
    private function isFirefox()
    {
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }
        if(strlen(strstr($agent,"Firefox")) > 0 ){ 
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
}
?>