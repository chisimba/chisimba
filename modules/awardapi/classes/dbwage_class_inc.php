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
 * @version   $Id$
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class dbwage extends dbTable
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
                parent::init('tbl_award_wage');
                $this->objWageSocName = $this->getObject("dbwagesocname", 'awardapi');
                $this->objPayPeriodTypes = $this->getObject('dbpayperiodtypes','awardapi');
				$this->indexFacet = $this->getObject('awardindex_facet', 'award');
                $payPeriod = $this->objPayPeriodTypes->getDefaultPPType();
                $this->ppCalc = $payPeriod['factor'];
            } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }

        public function deleteWage($id) {
            // delete wage_socname
            $this->objWageSocName->delete('id',$id);

            // delete wage
            $this->delete('id',$id);
        }

        public function getWageThreshold($TUIndex,$threshold = NULL,$year = NULL) {
                $limit = '';
				if ($year == NULL) {
					$year = date('Y');
				}
                if ($threshold == NULL) {
                    $threshold = 99999;
                    $limit = " LIMIT 20";
                }
                if ($this->ppCalc == 0) {
                    $threshold /= 40;
                    $sql = "SELECT agree.id AS agreeid, agree.name AS agreename, (wage.weeklyrate/benefits.value) AS wagerate,
							socname.name AS occupation, agree.implementation, sic.description AS industry,
							agree.unitid as unitid ";
                } else {
					$threshold /= $this->ppCalc;
                    $sql = "SELECT agree.id AS agreeid, agree.name AS agreename, (wage.weeklyrate*$this->ppCalc) AS wagerate,
							socname.name AS occupation, agree.implementation, sic.description AS industry,
							agree.unitid AS unitid ";
                }
                $sql .= "FROM tbl_award_agree AS agree, tbl_award_wage AS wage, tbl_award_unit_branch AS unitbranch,
                            tbl_award_branch AS branch, tbl_award_party AS party, tbl_award_socmajorgroup AS soc,
                            tbl_award_socname AS socname, tbl_award_wage_socname AS wagesocname, tbl_award_sicmajordiv AS sic,
                            tbl_award_unit AS unit, tbl_award_unit_sic AS unitsic, tbl_award_benefits AS benefits
                        WHERE wage.agreeid = agree.id AND wage.weeklyrate < '$threshold' AND unitbranch.unitid = agree.unitid
                            AND unitbranch.branchid = branch.id AND branch.partyid = party.id AND party.id = '$TUIndex'
                            AND wagesocname.id = wage.id AND wagesocname.socnameid = socname.id AND socname.major_groupid = soc.id
                            AND unit.id = agree.unitid AND unitsic.unitid = unit.id AND sic.id = unitsic.major_divid
                            AND YEAR(agree.implementation) = '$year'
                            AND benefits.nameid = 'init_7' AND benefits.agreeid = agree.id
                        GROUP BY agree.unitid
                        ORDER BY wage.weeklyrate ASC, agree.implementation DESC";
                $sql .= $limit;
                return $this->getArray($sql);
    }

       public function __getWageThresholdNoTU($threshold = NULL,$year = NULL) {
        if ($year == null) {
            $year = date('Y');
        }
        $limit = '';
        if ($threshold == null) {
            $threshold = 1000;
            $limit = " LIMIT 20";
        }
        $sql = "SELECT agree.id AS agreeId, agree.name AS agreeName, wage.weeklyrate AS wageRate, soc.description AS socName
                FROM tbl_award_agree AS agree, tbl_award_wage AS wage, tbl_award_socmajorgroup AS soc,
                    tbl_award_socname AS socname, tbl_award_wage_socname AS wagesocname
                WHERE wage.agreeid = agree.id AND wage.weeklyrate < '$threshold'
                    AND wagesocname.id = wage.id AND wagesocname.socnameid = socname.id AND socname.major_groupid = soc.id
                    AND YEAR(agree.implementation) = $year
                ORDER BY wage.weeklyrate ASC";
        $sql .= $limit;
        return $this->getArray($sql);
    }
	
	public function getAgreementWage($unitId, $socId, $agreeType, $year) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT unit.id, (wage.weeklyrate /benefits.value) AS wage, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id, (wage.weeklyrate * {$this->ppCalc}) AS wage, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				AND unit.id = '$unitId' 
				$sqlextra";

    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
     	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " GROUP BY unit.id";
    	$result = $this->getArray($sql);
		
		return (isset($result[0]))? $result[0]['wage'] : false;
	}
	
	public function getAgreementInfo($unitId, $socId, $agreeType, $year) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT unit.id, (wage.weeklyrate /benefits.value) AS wage, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id, (wage.weeklyrate * {$this->ppCalc}) AS wage, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				AND unit.id = '$unitId' 
				$sqlextra";

    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
     	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " GROUP BY unit.id";
    	$result = $this->getArray($sql);
		
		return (isset($result[0]))? $result[0] : false;
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

    public function getAverageMinWages($sicId,$socId,$indexId,$minorSicId,$wageTypeId,$agreeType,$year,$subSicId) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id as id, agree.id AS agree_id, MIN(wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id as id, agree.id AS agree_id, MIN(wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		if ($minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicdiv AS minorsic ";
     	}
     	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicmajorgroup AS mgroupsic ";
     	}
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				$sqlextra";

		if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND minorsic.id = sic.divid AND minorsic.id = '$minorSicId' ";
    	}
    	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND mgroupsic.id = sic.major_groupid AND mgroupsic.id = '$subSicId' ";
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
    		$oldWage = $this->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
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
				$units[$line['id']] = $line['id'];
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
    	$total['units'] 		= $units;
    	$total['workers'] 		= $workers;
		
    	return $total;
    }
    
    /**
     * Method to get the median wage for a given year within particular sector, be it SIC, SOC or a combination
     *
     * @param string $sicId the id of the sic to look at or 'all' for all industries
     * @param string $socId the id of the soc to look at or 'all' for all occupations
     * @param string $minorSocId the id of the sub occupation group to look at or 'all' for all sub divisions
     * @param string $year the year to calculate for
     * @return array containing keys sample => the size of the sample (number of agreements in statistic)
     * 								 average => the median wage for the given year and circumstances
     */
    function getMedianWages($sicId,$socId,$indexId,$minorSicId,$wageTypeId,$agreeType,$year,$subSicId) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		if ($minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicdiv AS minorsic ";
     	}
     	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicmajorgroup AS mgroupsic ";
     	}
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND minorsic.id = sic.divid AND minorsic.id = '$minorSicId' ";
    	}
    	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND mgroupsic.id = sic.major_groupid AND mgroupsic.id = '$subSicId' ";
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
    		$oldWage = $this->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
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
				$units[$line['id']] = $line['id'];
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
    	$total['units'] 		= $units;
    	$total['workers'] 		= $workers;
		
    	return $total;
    }

    /**
     * Method to get the average highest wage for a given year within particular sector, be it SIC, SOC or a combination
     *
     * @param string $sicId the id of the sic to look at or 'all' for all industries
     * @param string $socId the id of the soc to look at or 'all' for all occupations
     * @param string $minorSocId the id of the sub occupation group to look at or 'all' for all sub divisions
     * @param string $agreeType the id of the agreee type to look at or 'all' for all types
     * @param string $year the year to calculate for
     * @return array containing keys sample => the size of the sample (number of agreements in statistic)
     * 								 average => the average highest wage for the given year and circumstances
     */

    function getAverageMaxWages($sicId,$socId,$indexId,$minorSicId,$wageTypeId,$agreeType,$year,$subSicId) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id as id, agree.id AS agree_id, MAX(wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id as id, agree.id AS agree_id, MAX(wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		if ($minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicdiv AS minorsic ";
     	}
     	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicmajorgroup AS mgroupsic ";
     	}
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				$sqlextra";

		if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND minorsic.id = sic.divid AND minorsic.id = '$minorSicId' ";
    	}
    	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND mgroupsic.id = sic.major_groupid AND mgroupsic.id = '$subSicId' ";
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
    		$oldWage = $this->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
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
				$units[$line['id']] = $line['id'];
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
    	$total['units'] 		= $units;
    	$total['workers'] 		= $workers;
		
    	return $total;
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

    function getAverageWages($sicId,$socId,$indexId,$minorSicId,$wageTypeId,$agreeType,$year,$subSicId) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		if ($minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicdiv AS minorsic ";
     	}
     	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicmajorgroup AS mgroupsic ";
     	}
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND minorsic.id = sic.divid AND minorsic.id = '$minorSicId' ";
    	}
    	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND mgroupsic.id = sic.major_groupid AND mgroupsic.id = '$subSicId' ";
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
    		$oldWage = $this->getAgreementWage($line['id'], $socId, $agreeType, $year-1);
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
				$units[$line['id']] = $line['id'];
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
    	$total['units'] 		= $units;
    	$total['workers'] 		= $workers;

    	return $total;
    }
	
	/**
     * Method to get the weighted average wage for a given year within particular sector, be it SIC, SOC or a combination
     *
     * @param string $sicId the id of the sic to look at or 'all' for all industries
     * @param string $socId the id of the soc to look at or 'all' for all occupations
     * @param string $minorSocId the id of the sub occupation group to look at or 'all' for all sub divisions
     * @param string $agreeType the id of the agreee type to look at or 'all' for all types
     * @param string $year the year to calculate for
     * @return array containing keys sample => the size of the sample (number of agreements in statistic)
     * 								 average => the average highest wage for the given year and circumstances
     */

    function getWeightedAverageWages($sicId,$socId,$indexId,$minorSicId,$wageTypeId,$agreeType,$year,$subSicId) {
    	if ($this->ppCalc == 0) {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate /benefits.value) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id AS id, agree.id AS agree_id, (wage.weeklyrate * {$this->ppCalc}) AS average, agree.implementation AS agree_start,
    				agree.length AS length, agree.typeid AS agree_type, agree.workers AS workers
    				FROM tbl_award_socmajorgroup AS soc, tbl_award_unit AS unit, tbl_award_unit_sic AS sic,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname,
     				tbl_award_socname AS socname ";
    		$sqlextra = '';
    	}
		if ($minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicdiv AS minorsic ";
     	}
     	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
     		$sql .= ", tbl_award_sicmajorgroup AS mgroupsic ";
     	}
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND unit.id = sic.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = socname.id
     			AND socname.major_groupid = soc.id
				AND YEAR(agree.implementation) = $year
				$sqlextra";

    	if ($sicId != 'all') {
    		$sql .= " AND sic.major_divid = '$sicId' ";
    	}
    	if ($socId != 'all') {
    		$sql .= " AND soc.id = '$socId' ";
    	}
    	if ($minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND minorsic.id = sic.divid AND minorsic.id = '$minorSicId' ";
    	}
    	if ($subSicId != 'all' && $minorSicId != 'all' && $sicId != 'all') {
    		$sql .= " AND mgroupsic.id = sic.major_groupid AND mgroupsic.id = '$subSicId' ";
    	}
    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " ORDER BY average";
    	$result = $this->getArray($sql);

    	$wageSubSumProduct = 0;
    	$oldWageSubSumProduct = 0;
    	$indexSubTotal = 0;
    	$units = array();
    	$agreements = array();
    	$wageCount = 0;
    	$indexCount = 0;
    	$workers = 0;
		$sumWorkers = 0;
		$sumOldWorkers = 0;
		$indexInc = 0;

    	foreach ($result as $line) {			
    		if ($indexId != null) {
    			$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['agree_start'], $line['length'], $indexId);
    		}
    		$oldWage = $this->getAgreementInfo($line['id'], $socId, $agreeType, $year-1);
			if ($oldWage) {
				$wageSubSumProduct += $line['average']*$line['workers'];
				$oldWageSubSumProduct += $oldWage['wage']*$oldWage['workers'];
				//$oldWageSubSumProduct += $oldWage['wage']*$line['workers'];
				$sumWorkers += $line['workers'];
				$sumOldWorkers += $oldWage['workers'];
				if ($indexInc != 0 && $indexInc != -100) {
					$indexSubTotal += $indexInc*$line['workers'];
					$indexCount++;
				}
				if (!in_array($line['agree_id'], $agreements)) {
					$workers += $line['workers'];
					$agreements[] = $line['agree_id'];
				}
				$units[$line['id']] = $line['id'];
				$wageCount++;
			}
    	}

    	if ($wageCount > 3 && $sumWorkers > 0) {
    		$ave = $wageSubSumProduct/$sumWorkers;
			$oldAve = ($sumOldWorkers > 0)? $oldWageSubSumProduct/$sumOldWorkers : 0;
			//$oldAve = $oldWageSubSumProduct/$sumWorkers;
			$increaseAve = ($oldAve > 0)? ($ave-$oldAve)/$oldAve*100 : 0;
			if ($indexCount > 3) {
				$realAve = $increaseAve - ($indexSubTotal/$sumWorkers);
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
    	$total['units'] 		= $units;
    	$total['workers'] 		= $workers;
		
    	return $total;
    }
    
    public function getUnitWages($unitId) {
     	$sql = "SELECT DISTINCT socname.id AS id, socname.name AS name
     			FROM tbl_award_wage_socname AS wagesocname, tbl_award_agree AS agree, tbl_award_wage AS wage,
     				tbl_award_socname AS socname
     			WHERE agree.unitid = '$unitId' AND agree.id = wage.agreeid AND wage.id = wagesocname.id
     				AND wagesocname.socnameid = socname.id";
     	$socs = $this->getArray($sql);
     	$result = array();
     	foreach ($socs AS $soc) { 
     		$sql = "SELECT wage.weeklyrate AS rate, agree.implementation AS date, agree.length AS months, agree.id AS agreeid
     				FROM tbl_award_wage AS wage, tbl_award_agree AS agree, tbl_award_wage_socname AS wagesocname
     				WHERE agree.unitid = '$unitId' AND agree.id = wage.agreeid AND wage.id = wagesocname.id
     					AND wagesocname.socnameid = '{$soc['id']}'
     				ORDER BY agree.implementation ASC";
     	 	$result[$soc['name']] = $this->getArray($sql);
     	}
     	return $result;
     }

}
?>