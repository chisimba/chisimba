<?php
/**
 *
 *  markitup limited functionality wrapper
 *
 *  Provides a holding point and some limited abstractions for the markitup
 *  wiki editor. Has no end-user functionality and should not be
 *  added to a menu.
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
 * @package   oembed
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 16076 2009-12-28 14:31:16Z dkeats $
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
* Controller class for the module jquery
*
* @author Derek Keats
* @package oembed
*
*/
class markitup extends controller
{

    /**
    *
    * Intialiser for the twitter controller
    * @access public
    *
    */
    public function init()
    {
    }


    /**
     *
     * The standard dispatch method for the jquery module.
     * This just redirects to the default module
     *
     */
    public function dispatch()
    {
        $location=$this->uri(array(), "_default");
        header("Location:$location");
    }

}
?>