<?php
/**
 * AWARD branch data access class
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
 * @version   CVS: $Id: dbbranch_class_inc.php 99 2008-08-07 15:06:18Z nic $
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
 * AWARD branch data access class
 * 
 * Class to provide AWARD Party Branch information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbbranch_class_inc.php 99 2008-08-07 15:06:18Z nic $
 * @link      http://avoir.uwc.ac.za
 */

class dbbranch extends dbTable
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

		    parent::init('tbl_award_branch');
            $this->objUnitBranch = $this->getObject('dbunitbranch', 'awardapi');
            $this->objUnit = $this->getObject('dbunit', 'awardapi');
            $this->objWage = $this->getObject('dbwage', 'awardapi');
			$this->indexFacet = $this->getObject('awardindex_facet', 'award');
            $ppType = $this->getObject('dbpayperiodtypes','awardapi');
            $payPeriod = $ppType->getDefaultPPType();
            $this->ppCalc = $payPeriod['factor'];
        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        } //echo "dbbranch ";
     }
    
    public function getBranchUnits($id) { 
        $sql = "SELECT unit.id AS id, unit.name AS name, count(agree.id) AS sample,
						MAX(agree.implementation) as lastagree
                FROM tbl_award_unit AS unit, tbl_award_unit_branch AS unitbranch,
                    tbl_award_agree AS agree
                WHERE unitbranch.unitid = unit.id AND unitbranch.branchid = '$id'
                    AND agree.unitid = unit.id
                GROUP BY name
                ORDER by name";
        $units = $this->objUnitBranch->getArray($sql);
        return $units;
    }
	
    public function deleteBranch($id) {
	    $unitBranches = $this->objUnitBranch->getAll("WHERE branchid = '$id'");
	    foreach ($unitBranches as $unitBranch) {
	        $this->objUnit->deleteUnit($unitBranch['unitid']);
	    }
	    $this->objUnitBranch->delete('branchid',$id); // not neccessary?
		$this->delete('id',$id);
	}
	
	/**
     * Method to get the average wage for a given year within particular sector, be it SIC, SOC or a combination
     *
     * @param string $sicId the id of the sic to look at or 'all' for all industries
     * @param string $socId the id of the soc to look at or 'all' for all occupations
     * @param string $minorSocId the id of the sub occupation group to look at or 'all' for all sub divisions
     * @param string $agreeType the id of the agreee type to look at or 'all' for all types
     * @param string $year the year to calculate for
     * @return array containing keys sample => the size of the sample (number of agreements in statistic)
     * 								 average => the average highest wage for the given year and circumstances
	*/
    public function getAverageWagesForBranch($branchId, $socId, $indexId, $agreeType, $year, $sicId='all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch  ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch  ";
    		$sqlextra = '';
    	}
		$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND unitbranch.unitid = unit.id AND unitbranch.branchid = '$branchId'
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " ORDER BY average";
    	$result = $this->getArray($sql);

    	$wageSubTotal = 0;
    	$oldWageSubTotal = 0;
    	$indexSubTotal = 0;
    	$units = array();
    	$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$indexInc = 0;

    	foreach ($result as $line) {			
    		if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->objWage->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageSubTotal += $line['average'];
				$oldWageSubTotal += $oldWage;
				if ($indexInc != 0 && $indexInc != -100) {
					$indexSubTotal += $indexInc;
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$wageCount++;
			}
    	}

    	if ($wageCount > 3) {
    		$ave = $wageSubTotal/$wageCount;
			$oldAve = $oldWageSubTotal/$wageCount;
			$increaseAve = ($ave-$oldAve)/$oldAve*100;
			if ($indexCount > 3) {
				$realAve = $increaseAve - ($indexSubTotal/$indexCount);
			} else {
				$realAve = 0;
			}
    	} else {
    		$increaseAve = $ave = $realAve = 0;
    	}
    	$total['realAve'] 		= $realAve;
		$total['average'] 		= $ave;
		$total['increaseAve'] 	= $increaseAve;
    	$total['sample'] 		= $wageCount;
    	$total['workers'] 		= $workers;

		return $total;
    }
	/**
     * Method to get the median wage for a given year within particular sector, be it SIC, SOC or a combination
     *
     * @param string $sicId the id of the sic to look at or 'all' for all industries
     * @param string $socId the id of the soc to look at or 'all' for all occupations
     * @param string $minorSocId the id of the sub occupation group to look at or 'all' for all sub divisions
     * @param string $agreeType the id of the agreee type to look at or 'all' for all types
     * @param string $year the year to calculate for
     * @return array containing keys sample => the size of the sample (number of agreements in statistic)
     * 								 average => the average highest wage for the given year and circumstances
	*/
    public function getMedianWagesForBranch($branchId, $socId, $indexId, $agreeType, $year, $sicId='all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch  ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch  ";
    		$sqlextra = '';
    	}
		$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND unitbranch.unitid = unit.id AND unitbranch.branchid = '$branchId'
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " ORDER BY average";
    	$result = $this->getArray($sql);

    	$wageArray = array();
    	$units = array();
		$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$indexInc = 0;
    		
    	foreach ($result as $line) {
			if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->objWage->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageArray[] = $line['average'];
				$oldWageArray[] = $oldWage;
				if ($indexInc != 0 && $indexInc != -100) {
					$indexArray[] = $indexInc;
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$wageCount++;
			}
    	}
    	if ($wageCount > 3) {
			sort($oldWageArray, SORT_NUMERIC);
			sort($indexArray, SORT_NUMERIC);
			
			if (($wageCount % 2) != 0) {
    			$ave = $wageArray[(($wageCount+1)/2)-1];
				$oldAve = $oldWageArray[(($wageCount+1)/2)-1];
    		} else {
    			$ave = ($wageArray[($wageCount/2)-1] + $wageArray[($wageCount/2)]) /2;
				$oldAve = ($oldWageArray[($wageCount/2)-1] + $oldWageArray[($wageCount/2)]) /2;
    		}
    		$increaseAve = ($ave-$oldAve)/$oldAve*100;
			if ($indexCount > 3) {
				if (($indexCount % 2) != 0) {
					$indexMed = $indexArray[(($indexCount+1)/2)-1];
				} else {
					$indexMed = ($indexArray[($indexCount/2)-1] + $indexArray[($indexCount/2)]) /2;
				}
				$realAve = $increaseAve-$indexMed;
			} else {
				$realAve = 0;
			}
    	
    	} else {
    		$increaseAve = $ave = $realAve = 0;
    	}
    	$total['realAve'] 		= $realAve;
		$total['average'] 		= $ave;
		$total['increaseAve'] 	= $increaseAve;
    	$total['sample'] 		= $wageCount;
    	$total['workers'] 		= $workers;
		
		return $total;
    }
	
	/**
     * Method to get the average lowest wage for a given year within particular sector, be it SIC, SOC or a combination
     *
     * @param string $sicId the id of the sic to look at or 'all' for all industries
     * @param string $socId the id of the soc to look at or 'all' for all occupations
     * @param string $minorSocId the id of the sub occupation group to look at or 'all' for all sub divisions
     * @param string $year the year to calculate for
     * @return array containing keys sample => the size of the sample (number of agreements in statistic)
     * 								 average => the average lowest wage for the given year and circumstances
     */

    public function getAverageMinWagesForBranch($branchId, $socId, $indexId, $agreeType, $year, $sicId='all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id as id, agree.id AS agree_id, MIN(wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id as id, agree.id AS agree_id, MIN(wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch ";
    		$sqlextra = '';
    	}
		$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND unitbranch.unitid = unit.id
				AND unitbranch.branchid = '$branchId'
				AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				$sqlextra";

		if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " GROUP BY unit.id";
    	$result = $this->getArray($sql);

		$wageSubTotal = 0;
    	$oldWageSubTotal = 0;
    	$indexSubTotal = 0;
    	$units = array();
    	$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$indexInc = 0;

    	foreach ($result as $line) {			
    		if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->objWage->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageSubTotal += $line['average'];
				$oldWageSubTotal += $oldWage;
				if ($indexInc != 0 && $indexInc != -100) {
					$indexSubTotal += $indexInc;
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$wageCount++;
			}
    	}

    	if ($wageCount > 3) {
    		$ave = $wageSubTotal/$wageCount;
			$oldAve = $oldWageSubTotal/$wageCount;
			$increaseAve = ($ave-$oldAve)/$oldAve*100;
			if ($indexCount > 3) {
				$realAve = $increaseAve - ($indexSubTotal/$indexCount);
			} else {
				$realAve = 0;
			}
    	} else {
    		$increaseAve = $ave = $realAve = 0;
    	}
    	$total['realAve'] 		= $realAve;
		$total['average'] 		= $ave;
		$total['increaseAve'] 	= $increaseAve;
    	$total['sample'] 		= $wageCount;
    	$total['workers'] 		= $workers;
		
    	return $total;
    }
	
	public function getAverageWagesForUnion($unionId, $socId, $indexId, $agreeType, $year, $sicId='all', $sicDivId='all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch,
					tbl_award_unit_sic AS sic ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch ";
    		$sqlextra = '';
    	}
		$sql .=	"WHERE unit.id = agree.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND unitbranch.unitid = unit.id
				AND unitbranch.branchid = branch.id AND branch.partyid = '$unionId'
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.unitid = unit.id AND sic.major_divid = '$sicId' ";
    	}
    	if ($sicDivId != 'all') {
    		$sql .= " AND sic.divid = '$sicDivId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " ORDER BY average";
    	$result = $this->getArray($sql);

    	$wageSubTotal = 0;
    	$oldWageSubTotal = 0;
    	$indexSubTotal = 0;
    	$units = array();
    	$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$indexInc = 0;

    	foreach ($result as $line) {			
    		if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->objWage->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageSubTotal += $line['average'];
				$oldWageSubTotal += $oldWage;
				if ($indexInc != 0 && $indexInc != -100) {
					$indexSubTotal += $indexInc;
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$wageCount++;
			}
    	}

    	if ($wageCount > 3) {
    		$ave = $wageSubTotal/$wageCount;
			$oldAve = $oldWageSubTotal/$wageCount;
			$increaseAve = ($ave-$oldAve)/$oldAve*100;
			if ($indexCount > 3) {
				$realAve = $increaseAve - ($indexSubTotal/$indexCount);
			} else {
				$realAve = 0;
			}
    	} else {
    		$increaseAve = $ave = $realAve = 0;
    	}
    	$total['realAve'] 		= $realAve;
		$total['average'] 		= $ave;
		$total['increaseAve'] 	= $increaseAve;
    	$total['sample'] 		= $wageCount;
    	$total['workers'] 		= $workers;

    	return $total;
    }
	
	public function getAverageMinWagesForUnion($unionId, $socId, $indexId, $agreeType, $year, $sicId='all', $sicDivId='all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, MIN(wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch,
					tbl_award_unit_sic AS sic ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, MIN(wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch ";
    		$sqlextra = '';
    	}
		$sql .=	"WHERE unit.id = agree.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND unitbranch.unitid = unit.id
				AND unitbranch.branchid = branch.id AND branch.partyid = '$unionId'
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.unitid = unit.id AND sic.major_divid = '$sicId' ";
    	}
    	if ($sicDivId != 'all') {
    		$sql .= " AND sic.divid = '$sicDivId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " GROUP BY unit.id";
    	$result = $this->getArray($sql);

    	$wageSubTotal = 0;
    	$oldWageSubTotal = 0;
    	$indexSubTotal = 0;
    	$units = array();
    	$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$indexInc = 0;

    	foreach ($result as $line) {			
    		if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->objWage->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageSubTotal += $line['average'];
				$oldWageSubTotal += $oldWage;
				if ($indexInc != 0 && $indexInc != -100) {
					$indexSubTotal += $indexInc;
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$wageCount++;
			}
    	}

    	if ($wageCount > 3) {
    		$ave = $wageSubTotal/$wageCount;
			$oldAve = $oldWageSubTotal/$wageCount;
			$increaseAve = ($ave-$oldAve)/$oldAve*100;
			if ($indexCount > 3) {
				$realAve = $increaseAve - ($indexSubTotal/$indexCount);
			} else {
				$realAve = 0;
			}
    	} else {
    		$increaseAve = $ave = $realAve = 0;
    	}
    	$total['realAve'] 		= $realAve;
		$total['average'] 		= $ave;
		$total['increaseAve'] 	= $increaseAve;
    	$total['sample'] 		= $wageCount;
    	$total['workers'] 		= $workers;
		
    	return $total;
    }
	
	public function getMedianWagesForUnion($unionId, $socId, $indexId, $agreeType, $year, $sicId='all', $sicDivId='all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch,
					tbl_award_unit_sic AS sic ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname, tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch ";
    		$sqlextra = '';
    	}
		$sql .=	"WHERE unit.id = agree.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND unitbranch.unitid = unit.id
				AND unitbranch.branchid = branch.id AND branch.partyid = '$unionId'
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.unitid = unit.id AND sic.major_divid = '$sicId' ";
    	}
    	if ($sicDivId != 'all') {
    		$sql .= " AND sic.divid = '$sicDivId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " ORDER BY average";
    	$result = $this->getArray($sql);

    	$wageArray = array();
    	$units = array();
		$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$indexInc = 0;
    		
    	foreach ($result as $line) {
			if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->objWage->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageArray[] = $line['average'];
				$oldWageArray[] = $oldWage;
				if ($indexInc != 0 && $indexInc != -100) {
					$indexArray[] = $indexInc;
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$wageCount++;
			}
    	}
    	if ($wageCount > 3) {
			sort($oldWageArray, SORT_NUMERIC);
			sort($indexArray, SORT_NUMERIC);
			
			if (($wageCount % 2) != 0) {
    			$ave = $wageArray[(($wageCount+1)/2)-1];
				$oldAve = $oldWageArray[(($wageCount+1)/2)-1];
    		} else {
    			$ave = ($wageArray[($wageCount/2)-1] + $wageArray[($wageCount/2)]) /2;
				$oldAve = ($oldWageArray[($wageCount/2)-1] + $oldWageArray[($wageCount/2)]) /2;
    		}
    		$increaseAve = ($ave-$oldAve)/$oldAve*100;
			if ($indexCount > 3) {
				if (($indexCount % 2) != 0) {
					$indexMed = $indexArray[(($indexCount+1)/2)-1];
				} else {
					$indexMed = ($indexArray[($indexCount/2)-1] + $indexArray[($indexCount/2)]) /2;
				}
				$realAve = $increaseAve-$indexMed;
			} else {
				$realAve = 0;
			}
    	
    	} else {
    		$increaseAve = $ave = $realAve = 0;
    	}
    	$total['realAve'] 		= $realAve;
		$total['average'] 		= $ave;
		$total['increaseAve'] 	= $increaseAve;
    	$total['sample'] 		= $wageCount;
    	$total['workers'] 		= $workers;
		
    	return $total;
    }

    public function getIndustries($branchId,$socId,$year,$agreeTypeId = 'all') {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT sic.description AS industry, count(agree.id) AS sample, SUM(agree.workers) AS workers, AVG(wage.weeklyrate/benefits.value) AS avgwage, sic.id AS id ";
    	} else {
    		$sql = "SELECT sic.description AS industry, count(agree.id) AS sample, SUM(agree.workers) AS workers, AVG(wage.weeklyrate*$this->ppCalc) AS avgwage, sic.id AS id ";
    	}
    	$sql .= "FROM tbl_award_sicmajordiv AS sic, tbl_award_unit_sic AS unitsic, tbl_award_unit AS unit,
                      tbl_award_unit_branch AS unitbranch,
                      tbl_award_agree AS agree, tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
                      tbl_award_socname AS socname, tbl_award_benefits AS benefits
    			 WHERE sic.id = unitsic.major_divid AND unitsic.unitid = unit.id AND unit.id = unitbranch.unitid
                       AND unitbranch.branchid = '$branchId'
                       AND agree.unitid = unit.id AND wage.agreeid = agree.id
                       AND YEAR(agree.implementation) = '$year'
                       AND wagesocname.id = wage.id
                       AND wagesocname.socnameid = socname.id AND socname.major_groupid = '$socId'
                       AND benefits.nameid = 'init_7' AND benefits.agreeid = agree.id ";
    	if ($agreeTypeId != 'all') {
			$sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	$sql .= " GROUP BY industry";

    	return $this->getArray($sql);

    }

    public function getIndustriesNoBranch($tuId,$socId,$year,$agreeTypeId = 'all') {
    	if ($this->ppCalc == 0) { 
    		$sql = "SELECT sic.description AS industry, count(agree.id) AS sample, SUM(agree.workers) AS workers,
    				AVG(wage.weeklyrate/benefits.value) AS avgwage, sic.id AS id ";
    		
    	} else {
    		$sql = "SELECT sic.description AS industry, count(agree.id) AS sample, SUM(agree.workers) AS workers,
    				AVG(wage.weeklyrate*$this->ppCalc) AS avgwage, sic.id AS id ";
    		
    	}
    	$sql .= "FROM tbl_award_sicmajordiv AS sic, tbl_award_unit_sic AS unitsic, tbl_award_unit AS unit,
    				  tbl_award_unit_branch AS unitbranch, tbl_award_branch AS branch,
    				  tbl_award_agree AS agree, tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
    				  tbl_award_socname AS socname, tbl_award_benefits AS benefits
    			 WHERE sic.id = unitsic.major_divid AND unitsic.unitid = unit.id AND unit.id = unitbranch.unitid
    				   AND branch.partyid = '$tuId' AND branch.id = unitbranch.branchid
                       AND agree.unitid = unit.id AND wage.agreeid = agree.id
    				   AND YEAR(agree.implementation) = '$year'
                       AND wagesocname.id = wage.id
    				   AND wagesocname.socnameid = socname.id AND socname.major_groupid = '$socId'
    				   AND benefits.nameid = 'init_7' AND benefits.agreeid = agree.id ";
    	if ($agreeTypeId != 'all') {
            $sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	$sql .= " GROUP BY sic.description";
    	return $this->getArray($sql);
    }
	
    public function getIndustriesByBranch($branchId) {
    	$sql = "SELECT DISTINCT sic.description AS description, sic.id AS id
    			FROM tbl_award_sicmajordiv AS sic, tbl_award_unit_sic AS unitsic,
    				tbl_award_unit_branch AS unitbranch
    			WHERE sic.id = unitsic.major_divid AND unitsic.unitid = unitbranch.unitid
    				AND unitbranch.branchid = '$branchId'";
    	$ret = $this->getArray($sql);
    	return $ret;
    }
	
	public function getIndustriesWithExpiredByBranch($branchId) {
		$expired = date('Y') - 2;
		$ret = array();
		$industries = $this->getIndustriesByBranch($branchId);
		foreach ($industries as $ind) {
			$sql = "SELECT COUNT(agree.id) AS agreecount
					FROM tbl_award_unit_sic AS unitsic,
						 tbl_award_unit_branch AS unitbranch,
						 tbl_award_agree as agree
					WHERE unitsic.unitid = unitbranch.unitid
						AND agree.unitid = unitsic.unitid
						AND unitbranch.branchid = '$branchId'
						AND YEAR(agree.implementation) > '$expired'";
			$has = $this->getArray($sql);
		
			if ($has[0]['agreecount'] == 0) {
				$ret[] = $ind;
			}
		}
    	return $ret;
    }
}
?>