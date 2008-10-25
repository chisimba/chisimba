<?php

/**
 * Soap Controller class (Top level)
 * 
 * This class is the Chisimba SOAP controller class. It is the "C" in the Chisimba MVC Architecture for SOAP operations, and handles all of the business logic
 * All modules that are part of the Chisimba framework using soap will extend this class, as they will make use of the methods herein.
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
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Logging object
 */
require_once "lib/logging.php";

/**
 * Class to encapsulate operations via a SOAP interface.
 * It is highly recommended that you create a derived version
 * of this class for each transactional layer, rather than using it directly.
 *
 * @author  Paul Scott
 * @example ./examples/soap.eg.php The example
 * @todo Finish this class.
 *          
 *       
 */

class soapcontroller extends object
{

}
?>