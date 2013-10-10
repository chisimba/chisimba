<?php
/**
*
* WTM building database class
*
* This file provides a database access class for the WTM module's
* building database. Its purpose is allow administrators to manage
* the database.
* 
* PHP version 5
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the
* Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*
* @category Chisimba
* @package WTM
* @author Yen-Hsiang Huang <wtm.jason@gmail.com>
* @copyright 2007 AVOIR
* @license http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version CVS: $Id: demo_class_inc.php,v 1.4 2007-08-03 10:33:34 Exp $
* @link http://avoir.uwc.ac.za
*/

/**
* Security check: the $GLOBALS is an array used to control access to certain constants.
* Here it is used to check if the file is opening in engine, if not it
* stops the file from running.
*
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name $kewl_entry_point_run
*/
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}


class dbwtm_buildings extends dbtable
{
	public $objDBManager;
	
	/**
    * Constructor method to define the buildings table
    */
    function init() 
    {
        parent::init('tbl_wtm_buildings');
    }
	
    /**
    * Return all buildings stored in the DB
    * @return array of the buildings
    */
    function listAll() 
    {
        return $this->getAll();
    }
	
	/**
    * Return a single building
    * @param text $id building id
    * @return array of the values
    */
    function listSingle($id) 
    {
        return $this->getAll("WHERE id='" . $id . "'");
    }
	
    /**
    * Insert a new building
    * @param text $building building name
    * @param integer $longcoordinate longitude coordinate
	* @param integer $latcoordinate latitude coordinate
	* @param integer $xexpand longitude expand
	* @param integer $yexpand latitude expand
    */
    function insertSingle($building,$longcoordinate,$latcoordinate,$xexpand,$yexpand) 
    {
        $id = $this->insert(array(
				'building' => $building,
				'longcoordinate' => $longcoordinate,
				'latcoordinate' => $latcoordinate,
				'xexpand' => $xexpand,
				'yexpand' => $yexpand,
        ));
        return $id;
    }
	
    /**
    * Update an existing building
	* @param text $id building ID
    * @param text $building building name
    * @param integer $longcoordinate longitude coordinate
	* @param integer $latcoordinate latitude coordinate
	* @param integer $xexpand longitude expand
	* @param integer $yexpand latitude expand
    */
    function updateSingle($id,$building,$longcoordinate,$latcoordinate,$xexpand,$yexpand) 
    {
        $this->update("id", $id, array(
				'building' => $building,
				'longcoordinate' => $longcoordinate,
				'latcoordinate' => $latcoordinate,
				'xexpand' => $xexpand,
				'yexpand' => $yexpand,
				'modified' => TRUE
        ));
    }
    /**
	* Delete a record
    * @param string $id building ID
    */
    function deleteSingle($id) 
    {
        $this->delete("id", $id);
    }
	
}
?>