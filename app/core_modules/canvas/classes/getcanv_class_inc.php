<?php
/**
 *
 * Chisimba canvas module to get canvases for viewing
 *
 * Provides a means to load canvas thumbnails into the canvas viewer.
 *
 * 
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
 * Chisimba canvas module to get canvases for viewing
 *
 * Provides a means to load canvas thumbnails into the canvas viewer.
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
    * Holds the configuration object
    *
    * @var    object
    * @access public
    */
    public $objConfig;

    /**
    * Holds the canvas directory
    *
    * @var    object
    * @access public
    */
    public $canvasDir;

    /**
    * Holds the language object
    *
    * @var    object
    * @access public
    */
    public $objLanguage;
    /**
    *
    * Intialiser for the canvas getter
    *
    * @access public
    *
    */
    public function init()
    {
        try {
            $this->objLanguage = &$this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objConfig=$this->newObject('altconfig','config');
            $this->canvasDir = FALSE;
            // Load the link class
            $this->loadClass('link', 'htmlelements');
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    /**
     *
     * Load the greybox modal window script into the page and add the jQuery to
     * activate those links with rel=gb_image[].
     *
     * @return boolean TRUE
     * @access private
     *
     */
    private function loadHeaderScript()
    {
        try {
            $this->appendArrayVar('headerParams', $this->getJavaScriptFile('alertbox/facebox.js', 'htmlelements'));
            $this->appendArrayVar('headerParams', '<script type="text/javascript">
 jQuery(document).ready(function($) {
  $(\'a[rel*=facebox]\').facebox()
})
</script>');
            /*$objGreyboxLoader = & $this->getObject('greyboxloader', 'greybox');
            $objGreyboxLoader->loadAll();
            return TRUE;*/
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    /**
     *
     * Get the canvas thumbnails of a particular type, and render them with the alertbox
     * for viewing details.
     *
     * @param string $cType The type of canvas, e.g. personal or skin
     * @return string/boolean The rendered canvas thumbnails or FALSE
     * @access public
     * 
     */
    public function getCanvases($cType)
    {
        $this->loadHeaderScript();
        $cType = strtolower($cType);
        if ($cType == 'personal') {
            return $this->getPersonal();
        } elseif ($cType == 'skin') {
            return $this->getSkin();
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Get the personal canvas thumbnails for view
     *
     * @return string The rendered canvas thumbnails
     * @access private
     *
     */
    private function getPersonal()
    {
        $canvases ="";
        $canvasArray = $this->getPersonlCanvasesAsArray();
        foreach ($canvasArray as $canvas) {
            $canvases .= $this->getPersonalCanvasView($canvas);
        }
        return $canvases;
    }

    private function getSkin()
    {
        $this->curSkin = $this->getSession('skinName', FALSE);
        $canvasArray = $this->getSkinCanvasesArray($this->curSkin);
        $canvases ="";
        foreach ($canvasArray as $canvas) {
            $canvases .= $this->getSkinCanvasView($canvas, "skin");
        }
        return $canvases;
    }

    private function getSkinCanvasesArray($curSkin) {
        $canvasDir = $this->objConfig->getSiteRootPath() . "skins/" . $curSkin . "/canvases/";
        if ($handle = opendir($canvasDir)) {
            while (FALSE !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." &&$file != ".svn") {
                    if (is_dir($canvasDir.$file)) {
                        $ret[] = $file;
                    }
                }
            }
            return $ret;
         } else {
            return FALSE;
         }
    }

    /**
     *
     * Get an array of the list of all personal canvases in the user's canvases
     * directory as uploaded with file manager.
     *
     * @return string array or boolean The array of canvases or FALSE if none found.
     * @access private
     *
     */
    private function getPersonlCanvasesAsArray()
    {
        try {
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
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    /**
     *
     * Get the default personal canvas directory for the logged-in user
     *
     * @return string The user canvas directory
     * @access private
     *
     */
    private function getPersonalCanvasDir()
    {
        try {
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
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    /**
     *
     * Get the default personal canvas URL for the logged-in user
     *
     * @return string The user canvas URL or boolean FALSE if none found
     * @access private
     *
     */
    private function getPersonalCanvasUri()
    {
        try {
            if ($this->objUser->isLoggedIn()) {
                // Check for cached value
                if (isset($this->canvasUri)) {
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
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    /**
     *
     * Render a canvas thumbnail with the facebox popup for detail
     *
     * @param string $canvas The canvas to render
     * @return string The rendered thumbnail
     * @access private
     *
     */
    private function getPersonalCanvasView($canvas)
    {
        try {
            $dirToOpen = $this->getPersonalCanvasUri();
            $jsonFile = $this->getPersonalCanvasDir() . $canvas . '/canvas.json';
            $jsonFile = file_get_contents($jsonFile);
            $jsonObj = json_decode($jsonFile);
            $divTag = "<div class='canvasthumb'>";
            $divClose = "</div>";
            $canvasName = $jsonObj->name . "<br />";
            $canvasName = $this->getSkinChooserLink($canvas);
            $anchor = "<a href='" . $dirToOpen
              . $canvas . "/" . $jsonObj->preview->fullview . "' rel='facebox'>"; //gb_imageset[personal_canvases]
            $anchorClose = "</a>";
            $imageLink = "<img src='" . $dirToOpen
              . $canvas ."/" . $jsonObj->preview->thumb . "' />";
            $by = $this->objLanguage->languageText("word_by");
            $author = $by .": " . $jsonObj->author->authorname;
            $downloadLink = $jsonObj->downloadfrom;
            // @TODO add the download link
            return $divTag . $canvasName . $anchor . $imageLink . $anchorClose . $author . $divClose;
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    private function getSkinCanvasView($canvas)
    {
        try {
            $dirToOpen = "skins/" . $this->curSkin . "/canvases/";
            $jsonFile = $dirToOpen . $canvas . '/canvas.json';
            $jsonFile = file_get_contents($jsonFile);
            $jsonObj = json_decode($jsonFile);
            $divTag = "<div class='canvasthumb'>";
            $divClose = "</div>";
            $canvasName = $jsonObj->name;
            $canvasName = $this->getSkinChooserLink($canvasName, 'skin');
            $anchor = "<a href='" . $dirToOpen
              . $canvas . "/" . $jsonObj->preview->fullview . "' rel='facebox'>"; //gb_imageset[skin_canvases]
            $anchorClose = "</a>";
            $imageLink = "<img src='" . $dirToOpen
              . $canvas ."/" . $jsonObj->preview->thumb . "' />";
            $by = $this->objLanguage->languageText("word_by");
            $author = $by .": " . $jsonObj->author->authorname;
            $downloadLink = $jsonObj->downloadfrom;
            // @TODO add the download link
            return $divTag . $canvasName . $anchor . $imageLink . $anchorClose . $author . $divClose;
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }
    }

    private function getSkinChooserLink($canvas, $canvasType='personal')
    {
        $ln = $this->uri(
          array('action' => 'ask',
            'type' => $canvasType,
            'key' => 'canvas',
            'value' => $canvas),
            'canvas');
        $objLink = new link($ln);
        $objLink->link = $canvas;
        $objLink->rel='facebox'; //gb_page_center[640, 480]
        return $objLink->show();
    }
}
?>