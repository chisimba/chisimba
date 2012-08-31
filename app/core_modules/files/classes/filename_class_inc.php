<?php

/**
 * Filename class
 *
 * Miscellaneous filename functions.
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
 * @author    Jeremy O'Connor
 * @copyright (C) 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

class filename extends object
{
    /**
    * Init method.
    *
    */
    public function init()
    {
    }

    /**
    * 
    * Make a valid Windows/Linux filename from a string.
    *
    * @access Public
    * @param string $s String
    * @return string Filename
    * 
    */
    public function makeFileName($s)
    {
        return preg_replace('#[[:cntrl:]\x80-\xFF]\\\/\:\*\?\"\<\>\|]#', '_', $s); //'\temp0_ \/:*?"<>|'
    }
}
?>