<?php
/**
 *
 * Writing tools
 *
 * A suite of tools and plugins for other modules that provide functionality for learning and improving scientific writing. Much of the functionality of this module is made available via the Wiki module.
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
 * @package   writingtools
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbwritingtools.php,v 1.2 2008-01-08 13:07:15 dkeats Exp $
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
* Plugin for the wiki to parse content on save 
* for the module writingtools. It is used to generate
* wiki page templates, and perform other parsing on save
* based on writing tools input commands that take the form
* [WT: command], for example [WT: researchpaper]
*
* @author Derek Keats
* @package writingtools
*
*/
class pluginwiki extends object
{

    /**
    *
    * Intialiser for the writingtools controller
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
    }

}
?>
