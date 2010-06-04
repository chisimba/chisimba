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
* This class automates the selection of a canvas within
* the currently active skin.
*
* @author Derek Keats
* @package canvas
*
*/
class canvaschooser extends controller
{
    /**
    *
    * @var string $canvas The name of the canvas to load
    * @access private
    * 
    */
    private $canvas;

    /**
    *
    * @var string $canvasType The type of the canvas to load
    * @access private
    * 
    */
    private $canvasType;

    /**
    *
    * Intialiser for the canvas chooser
    * @access public
    *
    */
    public function init()
    {
        $this->canvas = FALSE;
        $this->canvasType = $this->getParam('canvastype', FALSE);
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Identify and return the name of the canvas we should be using
     * to display our content. Loosely speaking this implenents a chain
     * of responsibility design pattern, calling guessCanvas() to estimate
     * which canvas to display. New methods to render canvases, together
     * with their circumstances should be added to guessCanvas().
     *
     * @param  string array $validCanvases An array of valid canvases sent by
     *  the skin that is being rendered. This is required for use of the
     *  canvas functionality.
     * @return string A valid canvas name
     * @access public
     *
     */
    public function getCanvas($validCanvases, $skinBase)
    {
        if (!$this->canvas) {
            // Look first in the parameters
            $canvas = $this->getParam('canvas', FALSE);
            if ($canvas) {
                if ($this->isValidCanvas($canvas, $validCanvases)) {
                    $this->setCanvas($canvas);
                    $ret = $skinBase . $canvas;
                } else {
                    $ret = "_default";
                }
            } else {
                $ret = $this->guessCanvas($skinBase);
            }
        } else {
            if ($this->isValidCanvas($this->canvas, $validCanvases)) {
                $ret =  $skinBase . $this->canvas;
            } else {
                $ret =  $skinBase . "_default";
            }
        }
        return $ret;
    }

    /**
     *
     * Setter method for setting the canvas property of the class
     *
     * @param string $canvas The name of the canvas
     * @return boolean TRUE
     * @access public
     *
     */
    public function setCanvas($canvas)
    {
        $this->canvas = $canvas;
        return TRUE;
    }

    /**
    *
    * Guess what canvas should be presented based on circumstances.
    *
    * @return string The guessed canvas name
    * @access private
    *
    */
    private function guessCanvas($skinBase)
    {
        if ($this->canvasType) {
            $retMethod = $this->canvasType;
            return $this->$_retMethod($skinBase);
        // Get the user preference first
        } elseif ($ret = $this->_user($skinBase)) {
            return $ret;
        } elseif ($ret = $this->_site($skinBase)) {
            return $ret;
        } else {
            // See if they have a user preference set
            return $skinBase . "_default";
        }
    }

    /**
     *
     * Return the code needed to insert a user skin, based on a user
     * set preference, or on querystring parameters such as
     * canvastype=user&canvasdir=purple
     *
     * @param string $skinBase The default skin base for the current canvas
     * @return string/boolean The canvas base or FALSE
     * @access public
     *
     */
    public function _user($skinBase)
    {
        if ($this->objUser->isLoggedIn()) {
            // Check for a canvasdir directory in the  querystring
            if ($whatCanvas = $this->getParam('canvasdir', FALSE)) {
                $canvasPref = $whatCanvas;
            } else {
                $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
                $canvasPref = $objUserParams->getValue("canvas");
                if (!$canvasPref) {
                    return FALSE;
                } else {
                    $this->setSession('canvasType', 'user');
                    $this->setSession('canvas', $canvasPref);
                    return 'usrfiles/users/' . $this->objUser->userId() . '/canvases/' . $canvasPref . '/';
                }
            }
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Return the code needed to insert a site skin, based on the site
    * parameter set in the configuration settings for canvas
    *
    * @param string $skinBase The default skin base for the current canvas
    * @return string/boolean The canvas base or FALSE
    * @access public
    *
    */
    public function _site($skinBase)
    {
        // Check for preferred site canvas
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $prefCanvas = $this->objSysConfig->getValue('canvas_preferredcanvas', 'canvas');
        if ($prefCanvas == "FALSE") {
            return FALSE;
        } else {
            return  $skinBase . $prefCanvas;
        }
    }

    public function _module($skinBase)
    {
        die("NOT READY YET");
    }

    public function _nodal($skinBase)
    {
        die("NOT READY YET");
    }

    /**
     *
     * Determine if the passed canvas is valid for the current skin. This
     * information is supplied as an array by the skin author.
     *
     * @param string $canvas The name of the canvas
     * @param  string array $validCanvases An array of valid canvases sent by
     *  the skin that is being rendered. This is required for use of the
     *  canvas functionality.
     * @return boolean TRUE|FALSE
     * @access private
     */
    private function isValidCanvas($canvas, $validCanvases)
    {
        if (in_array($canvas, $validCanvases)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>