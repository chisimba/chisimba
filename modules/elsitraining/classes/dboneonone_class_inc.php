<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * A demonstration model class for Hello Chisimba
 *
 * This file provides a sample database access class for the hello chisimba
 * module. Its purpose is simply to teach newcomers to Chisimba how to do
 * database access.
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
 * @package   hellochisimba
 * @author    dexters mlambo
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   CVS: $Id:$
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
 * Data access for hellochisimba module
 *
 * Data access class for hellochisimba module. This class
 * extends dbTable to access the table tbl_hellochisimba
 *
 * @category  Chisimba
 * @package   hellochisimba
 * @author    dexters mlambo
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 *
 */
class dboneonone extends dbtable
{
    /**
    *
    * Standard init method to define table
    *
    */
    public function init()
    {
      //Set the table in the parent class
      parent::init('tbl_elsitraining_oneonone');
    }

    /**
    *
    * simple method to record that a user has been greeted
    * by writing the userid and time to the database table
    *
    */
    public function recordTraining($data)
    {
        //insert data from for schedule registration
        $result = $this->insert($data);
        return $result;
    }
}
?>
