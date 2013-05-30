<?php

/**
 * Helper class for flow player 3 and up
 *
 * The helper class for flow player 3 and up helps with implementing some of
 * the Flowplayer's newer functionality, including iPad support and HTML 5
 * video.
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
 *
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
 *
 * Helper class for flow player 3 and up
 *
 * The helper class for flow player 3 and up helps with implementing some of
 * the Flowplayer's newer functionality, including iPad support and HTML 5
 * video.
 *
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       object
 */
class flowplayer extends object
{
    /**
    *
    * @var    string object $objConfig A string to hold the config object
    * @access public
    *
    */
    public $objConfig;


    /**
    *
    *
    *
    * @access public
    *
    */
    public function init()
    {
        //Set up the path for the error file
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    /**
    *
    * Method to render the FLV player
    *
    * @access Public
    * @return string The player applet code
    *
    */
    public function show($file, $ipadCompatibility=TRUE)
    {
        $this->loadPlayerJs($ipadCompatibility);
        $flowPlayer = $this->getFlowplayerUri();
        $ret = $this->getVideo($file);
        $ret .= $this->activateBasicPlayer($file, $flowPlayer, TRUE);
        return $this->addFluidity($ret);
    }

    /**
     *
     * Add a fluid div for auto resizing the video
     *
     * @param string $ret The video rendering scripts & anchor
     * @return string The rendered fluid div
     * @access public
     *
     */
    public function addFluidity($ret)
    {
        return '<div class="video-container">' . $ret . '</div>';
    }

    /**
     *
     * Load the flowplayer javascripts with optional iPad compatibility
     *
     * @param boolean $ipadCompatibility TRUE|FALSE
     * @return boolean TRUE|FALSE
     * @Access public
     *
     */
    public function loadPlayerJs($ipadCompatibility=TRUE)
    {
        $js = $this->getJavaScriptFile('flowplayer3/flowplayer-3.2.8.min.js',
          'files');
        $this->appendArrayVar('headerParams', $js);
        unset($js);
        if ($ipadCompatibility) {
            $js2 = $this->getJavaScriptFile('flowplayer3/flowplayer.ipad-3.2.8.min.js',
              'files');
            $this->appendArrayVar('headerParams', $js2);
        }
        unset($js2);
        return TRUE;
    }

    /**
     *
     * Get the Flowplayer SWF URI
     *
     * @return string The rendered player URI
     * @access public
     *
     */
    public function getFlowplayerUri()
    {
        $flowPlayer = $this->objConfig->getsiteRoot()
          . $this->getResourceUri('flowplayer3/flowplayer-3.2.9.swf',
          'files');
        return $flowPlayer;
    }

    /**
     * Get the anchor tag to insert the video
     *
     * @param string $file The URI of the file to play
     * @return string The rendered anchor tag
     * @access public
     *
     */
    public function getVideo($file)
    {
        $videoId = md5($file);
        $ret = '
        <a
                    href="' . $file . '"
                    style="display:block; width:100%; height:100%;"
                    id="' . $videoId . '">
        </a>';
        return $ret;
    }

    /**
     *
     * Return an active basic player with no configuration options other than
     * ipad compatibility on or off.
     *
     * @param string $file The URI of the file to play
     * @param string $flowPlayer The URI of the flow player SWF file
     * @param BOOLEAN $ipadCompatibility Whether or not to use ipad compatibility
     * @return string The rendered player script
     */
    public function activateBasicPlayer($file, $flowPlayer, $ipadCompatibility=TRUE)
    {
        $videoId = md5($file);
        if ($ipadCompatibility) {
            $ipad = '.ipad()';
        } else {
            $ipad = NULL;
        }
        // Needd to add so.addParam("wmode", "opaque");
        $ret = '
            <script>
                flowplayer("' . $videoId . '", { src:"' . $flowPlayer . '",'
                  . ' wmode: "opaque"})' . $ipad . ';
            </script>

            ';
        return $ret;
    }
}
?>
