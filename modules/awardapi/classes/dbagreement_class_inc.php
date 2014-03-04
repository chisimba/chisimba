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
 * @version   $Id: dbagreement_class_inc.php 111 2008-08-13 12:32:06Z nic $
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
 * AWARD agreement data access class
 * 
 * Class to provide AWARD Party Branch Unit information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbagreement_class_inc.php 111 2008-08-13 12:32:06Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbagreement extends dbTable
{

	/**
	* Class Constructor
	*
	* @access public
	* @return void
	*/
    public function init() {
    	try {
            parent::init('tbl_award_agree');
            $this->objBenefits = $this->getObject('dbbenefits');
			$this->objDbPayPeriodType = $this->getObject('dbpayperiodtypes');
            $this->objWages = $this->getObject('dbwage');
       } catch (Exception $e){
   		    throw customException($e->getMessage());
    	    exit();
 	   }
    }
        
    public function getLastAgreementDate($unitId) {
        $agreements = $this->getAll("WHERE unitid = '$unitId' ORDER BY implementation ASC");
        $agree = current($agreements);
        return $agree['implementation'];
    }
        
    public function getLastAgreement($unitId) {
        $agreements = $this->getAll("WHERE unitid = '$unitId' ORDER BY implementation ASC");
        $agree = current($agreements);
        return $agree;
    }
    
	public function getYearAgreement($unitId, $year) {
        $agreements = $this->getAll("WHERE unitid = '$unitId' AND YEAR(implementation) = $year ORDER BY implementation ASC");
        $agree = current($agreements);
        return $agree;
    }
        
    public function getAgreeCount($unitId) {
        $count = $this->getArray("SELECT COUNT(id) AS count FROM tbl_award_agree WHERE unitid = '$unitId'");
        return $count[0]['count'];
    }
	
	public function getPreviousAgreementId($unitId, $date) {
		$sql = "SELECT id
				FROM tbl_award_agree
				WHERE unitid = '$unitId' AND implementation < '$date'
				ORDER BY implementation DESC";
		$result = $this->getArray($sql);
		return (!empty($result)) ? $result[0]['id'] : false;
	}

	public function getAgreementCount($sicId,$socId,$agreeTypeId,$year) {
     	$sql = "SELECT DISTINCT agree.id
     		FROM tbl_award_agree AS agree, tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit,
 				 tbl_award_unit_sic AS sic, tbl_award_wage AS wage, tbl_award_wage_socname,
    			 tbl_award_socname AS socname
     		WHERE unit.id = agree.unitid
     		AND unit.id = sic.unitid
 			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
    		AND tbl_award_wage_socname.socnameid = socname.id
     		AND socname.major_groupid = soc.id
			AND YEAR(agree.implementation) = $year
			AND soc.id = '$socId'";
     	
     	if ($agreeTypeId != 'all') {
     		if ($agreeTypeId = 'cb') { 
     			$sql .= " AND (agree.typeid = 2 || agree.typeid = 3 ||agree.typeid = 4)";
     		} else {
     			$sql .= " AND agree.typeid = '$agreeTypeId'";
     		}
     	}
     	if ($sicId != 'all') {
     		$sql .= " AND sic.major_divid = '$sicId'";
     	}
     	$result = $this->getArray($sql);
     	return count($result);     	
    }
    
    public function getUnitParties($unitId) {
         $sql ="SELECT DISTINCT party.name
                FROM tbl_award_unit AS bu, tbl_award_party AS party,
                    tbl_award_unit_branch AS orgbranch, tbl_award_branch AS branch
                WHERE bu.id = '$unitId' AND orgbranch.unitid = bu.id
                    AND branch.id = orgbranch.branchid AND branch.partyid = party.id";
         $result = $this->getArray($sql);
         return $result;
     }
    
    public function getUnionName($unitId) {
        $objUnitBranch = $this->getObject('dbunitbranch');
        $objParty = $this->getObject('dbparty');
        $objBranch = $this->getObject('dbbranch');
        $relation = $objUnitBranch->getRow('unitid',$unitId);
        $branch = $objBranch->getRow('id',$relation['branchid']);
        $tu = $objParty->getRow('id',$branch['partyid']);
        return $tu['abbreviation'];
    }
        
    public function getUnionFullName($unitId) {
        $objUnitBranch = $this->getObject('dbunitbranch');
        $objParty = $this->getObject('dbparty');
        $objBranch = $this->getObject('dbbranch');    
        $relation = $objUnitBranch->getRow('unitid',$unitId);
        $branch = $objBranch->getRow('id',$relation['branchid']);
        $tu = $objParty->getRow('id',$branch['partyid']);
        return $tu['name'];
    }
        
    public function getWageList($id) {
        $sql = "SELECT wage.id AS id, socname.name AS name, wage.weeklyrate AS rate,
                    pptype.name AS period, wage.notes AS notes
                FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
                    tbl_award_socname AS socname, tbl_award_pay_period_types AS pptype
                WHERE wage.agreeid = '$id' AND wagesocname.id = wage.id
                    AND wagesocname.socnameid = socname.id AND pptype.id = wage.payperiodtypeid
                ORDER BY name";
        $wages = $this->objWages->getArray($sql);
        return $wages;
    }
		
	public function deleteAgree($id) {
        // delete conditions
        $this->objBenefits->delete('agreeid',$id);
                   
	    // delete wages
        $wages = $this->objWages->getAll("WHERE agreeid = '$id'");
        foreach ($wages AS $wage) {
            $this->objWages->deleteWage($wage['id']);
        }
        // delete agreement
        $this->delete('id',$id);
	}
        
		
	public function getMaxWageFromAgree($agreeId,$ppId) {
		$ppType = $this->objDbPayPeriodType->getRow('id',$ppId);
     	if ($ppType['factor'] == 0) {
     		$objBenefits = $this->getObject('dbbenefits');
     		$hours = $objBenefits->getHoursPerWeek($agreeId);
			$sql = "SELECT wage.id, MAX(wage.weeklyrate/$hours) AS rate, socname.name AS soc
				FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_socname AS socname
				WHERE wage.agreeid = '$agreeId' AND wagesocname.id = wage.id AND wagesocname.socnameid = socname.id
				GROUP BY wage.agreeid
				ORDER BY rate ASC";
     	} else {
     		$sql = "SELECT wage.id, MAX(wage.wage_rate*{$ppType['factor']}) AS rate, socname.name AS soc
				FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_socname AS socname
				WHERE wage.agreeid = '$agreeId' AND wagesocname.id = wage.id AND wagesocname.socnameid = socname.id
				GROUP BY wage.agreeid
				ORDER BY rate ASC";
     	}
     	$a_res = $this->getArray($sql);
     	return current($a_res);
    }
     
    public function getMinWageFromAgree($agreeId,$ppId) {
        $ppType = $this->objDbPayPeriodType->getRow('id',$ppId);
     	if ($ppType['factor'] == 0) {
     		$objBenefits = $this->getObject('dbbenefits');
     		$hours = $objBenefits->getHoursPerWeek($agreeId);
			$ppType['factor'] = (1/$hours);
		}
     	$sql = "SELECT wage.id, MIN(wage.weeklyrate*{$ppType['factor']}) AS rate, socname.name AS soc
				FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_socname AS socname
				WHERE wage.agreeid = '$agreeId' AND wagesocname.id = wage.id AND wagesocname.socnameid = socname.id
				GROUP BY wage.agreeid
				ORDER BY rate ASC";
     	$a_res = $this->getArray($sql);
     	return current($a_res);
    }

    public function getMedWageFromAgree($agreeId,$ppId) {
     	$ppType = $this->objDbPayPeriodType->getRow('id',$ppId);
     	if ($ppType['pay_period_calc'] == 0) {
     		$objBenefits = $this->getObject('dbbenefits');
     		$hours = $objBenefits->getHoursPerWeek($agreeId);
			$ppType['factor'] = (1/$hours);
		}
     	$sql = "SELECT wage.id, (wage.weeklyrate*{$ppType['factor']}) AS rate, socname.name AS soc
				FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_socname AS socname
				WHERE wage.agreeid = '$agreeId' AND wagesocname.id = wage.id AND wagesocname.socnameid = socname.id
				GROUP BY wage.agreeid
				ORDER BY rate ASC";
     	$a_res = $this->getArray($sql);
     	return current($a_res);
    }
	 
	public function getWageCount($sicId,$socId,$agreeTypeId,$year) {
     	$sql = "SELECT COUNT(wage.id) AS sample
     			FROM tbl_award_agree AS agree, tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit,
     				 tbl_award_unit_sic AS sic, tbl_award_wage AS wage, tbl_award_wage_socname,
     				 tbl_award_socname AS socname
     			WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				AND soc.id = '$socId'";
     	
     	if ($agreeTypeId != 'all') {
     		if ($agreeTypeId = 'cb') { 
     			$sql .= " AND (agree.typeid = 2 || agree.typeid = 3 ||agree.typeid = 4)";
     		} else {
     			$sql .= " AND agree.typeid = '$agreeTypeId'";
     		}
     	}
     	if ($sicId != 'all') {
     		$sql .= " AND sic.major_divid = '$sicId'";
     	}
     	
     	$result = $this->getArray($sql);
     	$result = current($result);
     	return $result['sample'];     	
    }
     
    public function getBUCount($sicId,$socId,$agreeTypeId,$year) {
     	$sql = "SELECT DISTINCT unit.id AS sample
     			FROM tbl_award_agree AS agree, tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit,
     				 tbl_award_unit_sic AS sic, tbl_award_wage AS wage, tbl_award_wage_socname,
     				 tbl_award_socname AS socname
     			WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				AND soc.id = '$socId'";
     	
     	if ($agreeTypeId != 'all') {
     		if ($agreeTypeId = 'cb') { 
     			$sql .= " AND (agree.typeid = 2 || agree.typeid = 3 ||agree.typeid = 4)";
     		} else {
     			$sql .= " AND agree.typeid = '$agreeTypeId'";
     		}
     	}
     	if ($sicId != 'all') {
     		$sql .= " AND sic.major_divid = '$sicId'";
     	}
     	
     	$result = $this->getArray($sql);
     	return count($result);     	
    }
        
}
?>