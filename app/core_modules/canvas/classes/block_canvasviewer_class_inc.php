<?php
/**
 *
 * Block for providing a viewer for canvases
 *
 * Block for providing a viewer for selecting the canvas you wish
 * to use (personal or site)
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
 * @version
 * @package    canvas
 * @subpackage blocks
 * @author     Derek Keats <derek@dkeats.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * 
 * Block for providing a viewer for canvases
 *
 * Block for providing a viewer for selecting the canvas you wish
 * to use (personal or site)
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_canvasviewer extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
    * Holds the language object
    *
    * @var    object
    * @access public
    */
    public $objLanguage;

    /**
    * Holds the user object
    *
    * @var    object
    * @access public
    */
    public $objUser;
    /**
     * Standard init function
     *
     * Instantiate language and user objects and create title
     *
     * @return NULL
     */
    public function init() 
    {
        try {
            $this->objLanguage = &$this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->title = $this->objLanguage->languageText("mod_canvas_viewer", "canvas");
        } catch(Exception $e) {
            throw customException($e->message());
            exit();
        }

    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        // Get the type of canvases
        $cType = $this->getParam('ctype', 'personal');
        // Get the viewer
        $objViewer = $this->getObject('getcanv', 'canvas');
        $ret = $objViewer->getCanvases($cType);
        return $ret;
    }
}
?>