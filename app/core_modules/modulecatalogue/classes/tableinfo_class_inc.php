<?php

/**
 * This file houses the tableinfo class which extends dbtable.
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
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */


/**
 * This class is used to gatehr information about tables for use in the modulecatalogue
 *
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2007 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class tableinfo extends dbtable
{

    /**
     * Init function for class
     */
    public function init()
    {
        parent::init('tbl_users');
    }

   /**
    * This is a method to return a list of the tables in the database.
    *
    * @access  public
    * @param   void
    * @returns array $list
    */
    public function tablelist()
    {
        return $this->listDbTables();
    }

    /**
    * This is a method to check if a given table's name is in an array
    * by default the array used is class variable $tables
    *
    * @access  public
    * @param   string $name
    * @param   array  $list (optional)
    * @returns TRUE or FALSE
    */
    public function checktable($name,$list=NULL)
    {
        if (is_null($list)){
            $list = $this->tablelist();
        }
        return in_array($name, $list);
    }
}
?>