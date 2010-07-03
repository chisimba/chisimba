<?php

/**
 * Mischellaneous HTML Elements
 * 
 * Helper class to cleanly generate various simple HTML elements.
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
 * @package   htmlelements
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za/
 * @see       http://www.w3.org/
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
 * Mischellaneous HTML Elements
 * 
 * Helper class to cleanly generate various simple HTML elements.
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za/
 * @see       http://www.w3.org/
 */
class mischtml extends object
{
    /**
     * Initialises object properties.
     *
     * @access public
     */
    public function init()
    {
    }

    /**
     * Generates a script element linking to an external file to import code from.
     *
     * @access public
     * @param  string $uri  The URI of the script to include.
     * @param  string $type The MIME type of the script.
     * @return string The generated HTML.
     */
    public function importScript($uri, $type='text/javascript') {
        $uri  = htmlspecialchars($uri);
        $type = htmlspecialchars($type);
        $html = "<script src='$uri' type='$type'></script>";

        return $html;
    }
}

?>
