<?php
/**
 * This class provides a set of methods and properties related to 
 * assigning and using permissions on KEWL.NextGen. All permissions
 * should make use of this class.
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
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 */

class permission {

    /**
    * Method do unpack a permission string into an array to
    * be used in allocating permissions
    * @param string $permissionString: a string of permissions 
    * in the form ....TBD
    */
    function unpack($permissionString) {
        $permArray=explode(",", $permissionString);
        return $permArray;
    }

}
?>
