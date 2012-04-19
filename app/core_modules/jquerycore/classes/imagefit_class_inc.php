<?php
/**
 *
 * Imagefit class for jquery
 *
 * This class is a wrapper for the jquery imagefit plugin
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
 * @package   jquerycore
 * @author    Kevin Cyster kcyster@gmail.com
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
* Imagefit class for jquery
*
* This class is a wrapper for the jquery imagefit plugin
*
*
* @package   jquerycore
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class imagefit extends object
{
    /**
     * 
     * Variable to hold the id of the element
     * 
     * @access proteced
     * @var string
     */
    protected $cssId;

    /**
     *
     * Intialiser for the tooltip class
     * 
     * @access public
     * @return VOID
     */
    public function init()
    {
        $loadedPlugins = $this->getSession('plugins', array(), 'skin');
        $loadedPlugins[] = 'imagefit';
        $uniquePlugins = array_unique($loadedPlugins);
        $this->setSession('plugins', $uniquePlugins, 'skin');
    }
    
    /**
     *
     * Method to set the imagefit container element id.
     * 
     * @access public
     * @param string $cssId The id of the element to have a tooltip
     * @return VOID
     */
    public function setCssId($cssId)
    {
        if (!empty($cssId) && is_string($cssId))
        {
            $this->cssId = $cssId;
        }
    }
    
    /**
     *
     * Method to generate the tooltip javascript and add it to the page
     * 
     * @access public
     * @return VOID 
     */
    public function show()
    {
        $script = "<script type=\"text/javascript\">";
        $script .= "jQuery(window).load(function(){";
        $script .= "jQuery(\"#$this->cssId\").imagefit();";
        $script .= "});</script>";
        
        $this->appendArrayVar('headerParams', $script);
    }
}
?>