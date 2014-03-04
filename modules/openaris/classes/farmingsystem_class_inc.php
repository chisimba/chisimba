<?php
/**
 * ahis age Class
 *
 * file housing age class
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
 * @package   ahis
 * @author    Joseph Gatheru
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * ahis age group Class
 * 
 * class to connect to age group table
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: age_class_inc.php 12186 2009-01-21 14:55:17Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class farmingsystem extends dbtable {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_farmingsystems');
			$this->objUser = $this->getObject('user', 'security');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	public function addFarmingSystem($farmingsystem,$abbreviation,$description,$startdate,$enddate)
	{
		
			$sql = $this->insert(array(
			'farmingsystem' => $farmingsystem,
			'abbreviation' => stripslashes($abbreviation),
			'description' => stripslashes($description),
			'startdate' => $startdate,
			'enddate' => $enddate,
			'createdon' => date('Y-m-d H:i:s',time()),
			'createdby' => $this->objUser->userId(),
			'modifiedon' => date('Y-m-d H:i:s',time()),
			'modifiedby' => $this->objUser->userid()
			));
			if($sql)
			return true;
			else
			return false;
			
	} 
	public function editFarmingSystem($id,$farmingsystem,$abbreviation,$description,$startdate,$enddate)
	{
		
			$sql = $this->update('id',$id,array(
			'farmingsystem' => $farmingsystem,
			'abbreviation' => stripslashes($abbreviation),
			'description' => stripslashes($description),
			'startdate' => $startdate,
			'enddate' => $enddate,
			'createdon' => date('Y-m-d H:i:s',time()),
			'createdby' => $this->objUser->userId(),
			'modifiedon' => date('Y-m-d H:i:s',time()),
			'modifiedby' => $this->objUser->userid()
			));
			if($sql)
			return true;
			else
			return false;
			
	} 
 
	public function deleteFarmingSystem($id)
	{
	    $sql=$this->delete('id',$id);
			if($sql)
			return true;
			else
			return false;
	}
}