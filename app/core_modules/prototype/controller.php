<?php
/**
 * 
 * Prototype/Scriptaculous module
 * 
 * The Prototype/Scriptaculous module provides no functionality to end users.
 * It exist purely to house the prototype / scriptaculous library which is
 * thus made available to legacy modules that still need it.
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
 * @package   helloforms
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: controller.php,v 1.4 2007-11-25 09:13:27 dkeats Exp $
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
 * Prototype/Scriptaculous module
 *
 * The Prototype/Scriptaculous module provides no functionality to end users.
 * It exist purely to house the prototype / scriptaculous library which is
 * thus made available to legacy modules that still need it.
*
* @author Derek Keats
* @package wall
*
*/
class prototype extends controller
{


    /**
    * 
    * Intialiser for the controller
    * @access public
    * @return VOID
    * 
    */
    public function init()
    {

    }
    
    
    /**
     * 
     * The standard dispatch method for the wall module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     *
     * @access public
     * @return A call to the appropriate method
     * 
     */
    public function dispatch()
    {
        header('Location: index.php');
    }
}
?>