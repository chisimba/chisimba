<?php
/**
* WTM Events class
*
* This file provides a database access class for the WTM module's
* events database. Its purpose is allow administrators to manage
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


class dbwtm_events extends dbtable
{

	/**
    * Constructor method to define the events table
    */
    function init() 
    {
        parent::init('tbl_wtm_events');
    }
	
    /**
    * Return all events stored in the DB
    * @return array of the events
    */
    function listAll() 
    {
        return $this->getAll();
    }
	
	/**
    * Return a single event
    * @param text $id event id
    * @return array of the values
    */
    function listSingle($id) 
    {
        return $this->getAll("WHERE id='" . $id . "'");
    }
	
    /**
    * Insert a new event
    * @param text $buildingid building ID
    * @param text $event event name
	* @param date $date event date
	* @param text $description event text description
	* @param text $imagename event image name
	* @param text $videoname event video name
    */
    function insertSingle($buildingid,$event,$date,$description,$imagename,$videoname) 
    {
        $id = $this->insert(array(
				'buildingid' => $buildingid,
				'event' => $event,
				'date' => $date,
				'description' => $description,
				'imagename' => $imagename,
				'videoname' => $videoname
        ));
        return $id;
    }
	
    /**
    * Update an existing building
	* @param text $id event ID
    * @param text $buildingid building ID
    * @param text $event event name
	* @param date $date event date
	* @param text $description event text description
	* @param text $imagename event image name
	* @param text $videoname event video name
    */
    function updateSingle($id,$buildingid,$event,$date,$description,$imagename,$videoname) 
    {
        $this->update("id", $id, array(
				'buildingid' => $buildingid,
				'event' => $event,
				'date' => $date,
				'description' => $description,
				'medianame' => $imagename,
				'videoname' => $videoname,
				'modified' => TRUE
        ));
    }
    /**
    * Delete an event
    * @param text $id event ID
    */
    function deleteSingle($id) 
    {
        $this->delete("id", $id);
    }

}
?>