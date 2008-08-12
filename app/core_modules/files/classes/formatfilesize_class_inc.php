<?php

/**
 * Format File Size Class
 * Convert the size in bytes for a file into a more user-friendly format
 * 
 * PHP version 3
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
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, U
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */
/**
* Format File Size
* Convert the size in bytes for a file into a more user-friendly format 
* http://www.drquincy.com/resources/code/php/formatfilesize/
*/
class formatfilesize
{
    /**
    * Constructor
    */
    function formatfilesize()
    {  }
    
    /**
    * Method to format the size of a file
    * @param  int    $bytes Size of the file
    * @return string Formatted Size of file
    */
    function formatsize($bytes)
    {
        $size = $bytes / 1024;
        if($size < 1024)
        {
            $size = number_format($size, 2);
            $size .= 'k';
        } else {
            if($size / 1024 < 1024) 
            {
                $size = number_format($size / 1024, 2);
                $size .= ' MB';
            } 
            else if ($size / 1024 / 1024 < 1024)  
                {
                $size = number_format($size / 1024 / 1024, 2);
                $size .= ' GB';
            } 
        }
        return $size; 
    }
    
}
?>
