<?php
/**
 * ahis ahisuser Class
 *
 * ahis user class
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
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: ahisuser_class_inc.php 13811 2009-06-30 14:38:44Z nic $
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
 * ahis ahisuser Class
 * 
 * class to access additional user info table
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: ahisuser_class_inc.php 13811 2009-06-30 14:38:44Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class ahisuser extends dbtable {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_users');
			$this->objUser = $this->getObject('user','security');
			$this->objTerritory = $this->getObject('territory');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	/**
	 * Method to return a user's territory
	 *
	 * @param string $userId the id of the user, leave out for current user
	 * @return string the id of the territory
	 */
	public function getGeo2Id($userId = NULL) {
		$id = $this->objUser->PKId($userId);
		$row = $this->getRow('id', $id);
		$locationRow = $this->objTerritory->getRow('id', $row['locationid']);
		return $locationRow['geo2id'];
	}
	
	/**
	 * Method to return a list of all ARIS users
	 *
	 * @return array ARIS users
	 */
	public function getList() {
		$sql = "SELECT u.userid AS userid, CONCAT(u.firstname,' ',u.surname) AS name
				FROM tbl_users AS u, tbl_ahis_users AS au
				WHERE u.id = au.id
				ORDER BY name";
		return $this->objUser->getArray($sql);
	}
	
	/**
	 * Method to return a list of all ARIS users of a certain role
	 *
	 * @param string $role The role to be searched for
	 * @return array ARIS users
	 */
	public function getListByRole($role) {
		$sql = "SELECT u.userid AS userid, CONCAT(u.firstname,' ',u.surname) AS name
				FROM tbl_users AS u, tbl_ahis_users AS au
				WHERE u.id = au.id AND au.roleid = '$role'
				ORDER BY name";
		return $this->objUser->getArray($sql);
	}
	
	/**
	 * Method to get contact info for user
	 *
	 * @param string $userId The id of the user
	 * @return array of contact details
	 */
	public function getUserContact($userId) {
		$sql = "SELECT fax, phone, email
				FROM tbl_ahis_users AS au, tbl_users AS u
				WHERE u.id = au.id AND u.userid = '$userId'";
		return $this->getArray($sql);
	}
	
	/**
	 * Method to check whether a user is a asuperuser
	 *
	 * @param string $userId The user id of the user to check
	 * @return true|false
	 */
	public function isSuperUser($userId) {
		$id = $this->objUser->PKId($userId);
		$row = $this->getRow('id', $id);
		return ($row['superuser'] == 1) || ($userId == 1);
	}
	
}