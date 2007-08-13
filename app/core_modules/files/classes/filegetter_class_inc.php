<?php

/**
 * Filegetter class
 * 
 * Class to load a file into a string
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
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */


/**
 * Filegetter class
 * 
 * A single-function class to load files into strings
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class filegetter
{
    /**
    * Method to load a file into a string when supplied with the full
    * file path to the file
    *
    * @param string $file the file to open and get
    */
    function getFileToString($file)
    {
        if (file_exists($file)) {
            //read it into a string and return the string
            $fp = fopen($file, "r")
                or die("fopen failed");   /* the file_exists should
                                             prevent this error but trap
                                             it anyway */
            $contents = fread($fp, filesize($file));
            fclose($fp);
            return $contents;
        } else {
            return False;
        }
    }
} // end class

?>
