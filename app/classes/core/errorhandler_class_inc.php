<?php

/**
 * PEAR Error handler
 * 
 * This file contains the errorhandler class that extends the PEAR error handler
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
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

/**
 * PEAR main class
 */
require_once('PEAR.php');

/**
 * PEAR error handler
 * 
 * Extension to the built in PEAR error handler
 * 
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <<pscott@uwc.ac.za>>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       http://pear.php.net/
 */

class errorhandler extends PEAR 
    {
    /**
     * The error
     * @var    mixed
     * @access public 
     */
    public $error;

    /**
     * error handler
     * @var    mixed
     * @access public 
     */
    public $handleErr;

    /**
     * constructor
     * 
     * Standard PHP5 constructor method
     * 
     * @param  string $errstr Error string
     * @param  integer $errno  Error number
     * @param  object  $object Error handler object
     * @return void   
     * @access public 
     */
    public function __construct($errstr, $errno, $object) {
        $error = new PEAR_Error($errstr, $errno);
        if (PEAR::isError($object)) {
            $this->error = $object->getMessage();
        }
    }

    /**
     * Method to handle the errors
     * 
     * Returns the error string in human readable format
     * 
     * @return string Return error string in human readable format
     * @access public 
     */
    public function handleError() {
        return $this->error;
    }

}
?>