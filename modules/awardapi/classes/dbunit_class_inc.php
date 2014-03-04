<?php
/**
 * AWARD index data access class
 * 
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
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbunit_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core,api
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
 * AWARD unit branch data access class
 * 
 * Class to provide AWARD Party Branch Unit information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbunit_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbunit extends dbTable
{

	   /**
	    * Class Constructor
	    *
	    * @access public
	    * @return void
	    */
        public function init()
        {
        	try {
                parent::init('tbl_award_unit');
                $this->objAgree = $this->getObject('dbagreement');
				$this->objUnitSic = $this->getObject('dbunitsic');
				$this->objUnitBranch = $this->getObject('dbunitbranch');
                $this->objUnitRegion = $this->getObject('dbunitregion');

           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }
        
        public function getUnitOverview($str) {
            $units = $this->getArray("SELECT DISTINCT id, name FROM tbl_award_unit WHERE name LIKE '$str' ORDER BY name");
            $data = array();
            $count = 0;
            foreach ($units as $unit) {
                $data[$count] = $unit;
                $data[$count]['lastAgreement'] = $this->objAgree->getLastAgreementDate($unit['id']);
                //$data[$count]['region'] = 
                $data[$count]['agreeCount'] = $this->objAgree->getAgreeCount($unit['id']); 
                $data[$count]['union'] = $this->objAgree->getUnionName($unit['id']);
                //$data[$count]['sic'] = '?';
                $count++;
            }
            return $data;
        }
        
        public function insertUnit($name, $pbId, $sicMDId, $sicDId, $sicMGId, $sicGId, $sicSGId, $regId, $notes) {
            $id = $this->insert(array('name'=>$name,'notes'=>$notes,'active'=>1));
            $this->objUnitBranch->insert(array('unitid'=>$id,'branchid'=>$pbId));
            $this->objUnitRegion->insert(array('regionid'=>$regId,'unitid'=>$id));
            $this->objUnitSic->insert(array('unitid'=>$id,'major_divid'=>$sicMDId,'divid'=>$sicDId,'major_groupid'=>$sicMGId,'groupid'=>$sicGId,'sub_groupid'=>$sicSGId));
            return $id;
        }
        
        public function updateUnit($id, $name, $pbId, $sicMDId, $sicDId, $sicMGId, $sicGId, $sicSGId, $regId, $notes) {
            $this->update('id', $id, array('name'=>$name,'notes'=>$notes,'active'=>1));
            if ($this->objUnitBranch->valueExists('unitid',$id)) {
                $this->objUnitBranch->update('unitid', $id, array('branchid'=>$pbId));
            } else {
                $this->objUnitBranch->insert(array('unitid'=>$id,'branchid'=>$pbId));
            }
            if ($this->objUnitRegion->valueExists('unitid', $id)) {
                $this->objUnitRegion->update('unitid', $id, array('regionid'=>$regId));
            } else {
                $this->objUnitRegion->insert(array('regionid'=>$regId,'unitid'=>$id));
            }
            if ($this->objUnitSic->valueExists('unitid', $id)) {
                $this->objUnitSic->update('unitid', $id, array('major_divid'=>$sicMDId,'divid'=>$sicDId,'major_groupid'=>$sicMGId,'groupid'=>$sicGId,'sub_groupid'=>$sicSGId));
            } else {
                $this->objUnitSic->insert(array('unitid'=>$id,'major_divid'=>$sicMDId,'divid'=>$sicDId,'major_groupid'=>$sicMGId,'groupid'=>$sicGId,'sub_groupid'=>$sicSGId));
            }
        
        }
		
		public function deleteUnit($id) {
			$agrees = $this->objAgree->getAll("WHERE unitid = '$id'");
			foreach ($agrees as $agree) {
			    $this->objAgree->deleteAgree($agree['id']);
			}
			$this->objUnitBranch->delete('unitid',$id);
			$this->objUnitSic->delete('unitid',$id);
            $this->objUnitRegion->delete('unitid',$id);
			$this->delete('id',$id);
		}
		
		public function searchBySic($sicId, $sicDivId, $agreeTypeId) {
			$sql = "SELECT unit.id AS id, unit.name AS name
					FROM tbl_award_unit AS unit, tbl_award_unit_sic AS unitsic,
						 tbl_award_agree AS agree
					WHERE unit.id = agree.id AND agree.typeid = '$agreeTypeId'
						  AND unitsic.unitid = unit.id AND unitsic.major_divid = '$sicId'
						  AND unitsic.divid = '$sicDivId'
					ORDER BY name";
			$result = $this->getArray($sql);
			return $result;
		}

        public function getUnitsByBranch($branchId) {
        $sql ="SELECT unit.name AS name, unit.id AS id
                FROM tbl_award_unit_branch AS branch, tbl_award_unit AS unit
                WHERE branch.unitid = unit.id AND branch.branchid = '$branchId'";

        return $this->getArray($sql);
        }
		
		function getUnitsByBranchInd($branchId,$indId) {
        $expireYear = date('Y') - 2;
     	$sql ="SELECT DISTINCT unit.name AS name, unit.id AS id, MAX(agree.implementation)
     		   FROM tbl_award_unit_branch AS branch, tbl_award_unit AS unit,
     			    tbl_award_unit_sic AS sic, tbl_award_agree AS agree
     		   WHERE branch.unitid = unit.id AND branch.branchid = '$branchId'
					 AND sic.unitid = unit.id AND sic.major_divid = '$indId'
					 AND unit.id = agree.unitid
					 AND unit.id NOT IN (
						 SELECT unit.id
						 FROM tbl_award_unit AS unit, tbl_award_agree AS agree
						 WHERE agree.unitid = unit.id
						 AND YEAR(agree.implementation) > '$expireYear')
			   GROUP BY unit.name
			   ORDER BY MAX(agree.implementation) DESC";

     	return $this->getArray($sql);
     }
}
?>