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
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: age_class_inc.php 12186 2009-01-21 14:55:17Z nic $
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
 * ahis disease diagnosis Class
 * 
 * class to connect to disease species number table
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: age_class_inc.php 12186 2009-01-21 14:55:17Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class diseasespeciesnumber extends dbtable {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_diseasespeciesnumber');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function getCases($diseaseId) {
		$sql = "SELECT SUM(num.cases) AS number
				FROM tbl_ahis_diseasespeciesnumber AS num, tbl_ahis_diseasereport AS rep
				WHERE rep.diseaseid = '$diseaseId' AND rep.outbreakcode = num.outbreakcode";
		$number = $this->getArray($sql);
		return $number[0]['number'];
	}
	
	public function getDeaths($diseaseId) {
		$sql = "SELECT SUM(num.deaths) AS number
				FROM tbl_ahis_diseasespeciesnumber AS num, tbl_ahis_diseasereport AS rep
				WHERE rep.diseaseid = '$diseaseId' AND rep.outbreakcode = num.outbreakcode";
		$number = $this->getArray($sql);
		return $number[0]['number'];
	}
	
	public function getDestroyed($diseaseId) {
		$sql = "SELECT SUM(num.destroyed) AS number
				FROM tbl_ahis_diseasespeciesnumber AS num, tbl_ahis_diseasereport AS rep
				WHERE rep.diseaseid = '$diseaseId' AND rep.outbreakcode = num.outbreakcode";
		$number = $this->getArray($sql);
		return $number[0]['number'];
	}
	
	public function getSlaughtered($diseaseId) {
		$sql = "SELECT SUM(num.slaughtered) AS number
				FROM tbl_ahis_diseasespeciesnumber AS num, tbl_ahis_diseasereport AS rep
				WHERE rep.diseaseid = '$diseaseId' AND rep.outbreakcode = num.outbreakcode";
		$number = $this->getArray($sql);
		return $number[0]['number'];
	}
	
	public function getSpecies($outbreakCode) {
		$sql = "SELECT sn.id, sn.speciesname
				FROM tbl_ahis_speciesnew AS sn, tbl_ahis_diseasespeciesnumber AS dsn
				WHERE sn.id = dsn.speciesid AND dsn.outbreakcode = '$outbreakCode'
				ORDER BY sn.speciesname";
		return $this->getArray($sql);
	}
	
}