<?php

/**
 *
 * Block config class
 *
 * Class to handle block configuration passed as $configData
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
 * @package   blocks
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @author    Derek Keats <derek@dkeats.com> Refactored for working with external blocks
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blocks_class_inc.php 18382 2010-07-09 07:01:47Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}

/**
 * Block config class
 *
 * Class to handle block configuration passed as $configData
 *
 * @category  Chisimba
 * @package   blocks
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class blockconfig extends object {
    /**
     * Property to hold the language object
     *
     * @var object $objLanguage
     */
    public $objLanguage;

    /**
     * Constructor method for the blockConfig class
     */
    public function init() {
    }

    /**
     *
     * Turn the configData into an array of parameters
     * for use in blocks. This should be invoked in the show
     * method of the specific block.
     *
     * @param string $configData
     * @return string Array An array of param => value pairs.
     * 
     */
    public function getConfigArray($configData)
    {
        $arrayParams = explode("|", $configData);
        $retArray = array();
        foreach ($arrayParams as $paramString) {
            $arrayParm = explode("=", $paramString);
            $paramName = $arrayParm[0];
            $paramValue = $arrayParm[1];
            $retArray[$paramName] = $paramValue;
        }
        return $retArray;
    }
}
?>