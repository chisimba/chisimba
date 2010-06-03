<?php
/**
 *
 * Canvas
 *
 * Chisimba canvas module.
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
 * @package   canvas
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbcanvas.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
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
 * Canvas
 *
 * Chisimba canvas module.
*
* @author Derek Keats
* @package canvas
*
*/
class getcanv extends object
{
    /**
    * Holds the user object
    *
    * @var    object
    * @access public
    */
    public $objUser;

    /**
    *
    * Intialiser for the canvas getter
    * @access public
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig=$this->newObject('altconfig','config');
        $this->canvasDir = FALSE;
    }

    public function getCanvases($cType)
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('alertbox/facebox.js', 'htmlelements'));
        $this->appendArrayVar('headerParams', '<script type="text/javascript">
 jQuery(document).ready(function($) {
  $(\'a[rel*=facebox]\').facebox()
})
</script>');
        $cType = strtolower($cType);
        if ($cType == 'personal') {
            return $this->getPersonal();
        } elseif ($cType == 'skin') {
            return $this->getSkin();
        } else {
            return FALSE;
        }
    }

    private function getPersonal()
    {
        $canvases ="";
        $canvasArray = $this->getPersonlCanvasesAsArray();
        foreach ($canvasArray as $canvas) {
            $canvases .= $this->getCanvasView($canvas);
        }
        return $canvases;
    }

    private function getSkin()
    {
        return "SKIN REQUESTED";
    }

    private function getPersonlCanvasesAsArray()
    {
        if ($this->objUser->isLoggedIn()) {
            $ret = array();
            $canvasDir = $this->getPersonalCanvasDir();
            if ($handle = opendir($canvasDir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($canvasDir.$file)) {
                            $ret[] = $file;
                        }
                    }
                }
            }
            return $ret;
        } else {
            return FALSE;
        }
    }

    private function getPersonalCanvasDir()
    {
        if ($this->objUser->isLoggedIn()) {
            // Check for cached value
            if ($this->canvasDir) {
                $canvasDir = $this->canvasDir;
            } else {
                $canvasDir = $this->objConfig->getSiteRootPath()
                  . 'usrfiles/users/' . $this->objUser->userId()
                  . '/canvases/';
                $this->canvasDir = $canvasDir;
            }
        } else {
            $canvasDir = FALSE;
        }
        return $canvasDir;
    }

    private function getPersonalCanvasUri()
    {
        if ($this->objUser->isLoggedIn()) {
            // Check for cached value
            if ($this->canvasUri) {
                $canvasUri = $this->canvasUri;
            } else {
                $canvasUri = 'usrfiles/users/' . $this->objUser->userId()
                  . '/canvases/';
                $this->canvasUri = $canvasUri;
            }
        } else {
            $canvasUri = FALSE;
        }
        return $canvasUri;
    }

    private function getCanvasView($canvas)
    {
        $dirToOpen = $this->getPersonalCanvasUri();
        $jsonFile = $this->getPersonalCanvasDir() . $canvas . '/canvas.json';
        $jsonFile = file_get_contents($jsonFile);
        $jsonObj = json_decode($jsonFile);
        $divTag = "<div class='canvasthumb'>";
        $divClose = "</div>";
        $canvasName = $jsonObj->name . "<br />";
        $anchor = "<a href='" . $dirToOpen
          . $canvas . "/" . $canvas . ".png' rel='facebox'>";
        $anchorClose = "</a>";
        $imageLink = "<img src='" . $dirToOpen
          . $canvas ."/" . $canvas 
          . "_th.png' />";
        $author = "By: " . $jsonObj->author->authorname;
        return $divTag . $canvasName . $anchor . $imageLink . $anchorClose . $author . $divClose;
    }
}
?>