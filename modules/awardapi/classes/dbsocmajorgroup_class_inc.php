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
 * AWARD XML_RPC & data access class
 * 
 * Class to provide AWARD SIC Major Div information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
class dbsocmajorgroup extends dbTable {

    /** 
    * Class Constructor
    *
    * @access public
    * @return void
    */
    public function init() {
        try {
            parent::init('tbl_award_socmajorgroup');
			$this->objPayPeriodTypes = $this->getObject('dbpayperiodtypes','awardapi');
            $this->objUser = $this->getObject('user', 'security');
			$this->indexFacet = $this->getObject('awardindex_facet', 'award');
            $this->objDbWages = $this->getObject('dbwage', 'awardapi');
			
        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
	
    public function getName($id) {
    	$record = $this->getRow('id',$id);
    	return $record['description'];
    }
	
	public function getOccupationFromId($socId) {
    	$sql = "SELECT name FROM tbl_award_socname WHERE id = '$socId'";
    	$a_result = $this->getArray($sql);
    	$result = current($a_result);
    	return $result['name'];
    }
	
	public function getAgreementWage($unitId, $socId, $agreeType, $year) {
    	$payPeriodType = $this->objPayPeriodTypes->getDefaultPPType();
    	$ppCalc = $payPeriodType['factor'];
    	if ($ppCalc == 0) {
    		$sql = "SELECT unit.id, (wage.weeklyrate /benefits.value) AS wage, agree.workers AS workers
    				FROM tbl_award_unit AS unit, tbl_award_benefits AS benefits,
     				tbl_award_agree AS agree, tbl_award_agree_types AS agreetype, tbl_award_wage AS wage,
					tbl_award_wage_socname ";
    		$sqlextra = " AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	} else {
    		$sql = "SELECT DISTINCT unit.id, (wage.weeklyrate * {$ppCalc}) AS wage, agree.workers AS workers
    				FROM tbl_award_unit AS unit, tbl_award_agree AS agree,
					tbl_award_agree_types AS agreetype, tbl_award_wage AS wage, tbl_award_wage_socname ";
    		$sqlextra = '';
    	}
		
     	$sql .=	"WHERE unit.id = agree.unitid
     			AND agree.id = wage.agreeid AND tbl_award_wage_socname.id = wage.id
     			AND tbl_award_wage_socname.socnameid = $socId
				AND YEAR(agree.implementation) = $year
				AND unit.id = '$unitId' 
				$sqlextra";

    	if ($agreeType != 'all') {
    		$sql .= ($agreeType == 'cb')? " AND agree.typeid = agreetype.id AND (agreetype.id = '2' || agreetype.id = '3' || agreetype.id = '4')" : " AND agree.typeid = agreetype.id AND agreetype.id = '$agreeType'";
    	}
    	$sql .= " GROUP BY unit.id";
    	$result = $this->getArray($sql);
		
		return (isset($result[0]))? $result[0] : false;
	}
	
	public function getSocMinimum($agreeTypeId, $year, $socText, $industry, $subSic) {
    	$indexId = $this->indexFacet->getIndexId('CPI');
		$payPeriodType = $this->objPayPeriodTypes->getDefaultPPType();
    	$payPeriodCalc = $payPeriodType['factor'];
    	if ($payPeriodCalc == 0) {
    		$sql = "SELECT socname.id AS socid, MIN(wage.weeklyrate/benefits.value)";
    	} else {
    		$sql = "SELECT socname.id AS socid, MIN(wage.weeklyrate*$payPeriodCalc)";
    	}
    	$sql .= " AS rate, COUNT(wage.id) AS sample, agree.unitid AS unitid, agree.implementation AS start,
						agree.length AS length
    	    	FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_unit AS unit,
					 tbl_award_socname AS socname, tbl_award_agree AS agree, tbl_award_benefits AS benefits";
		if ($industry != 'all') {
			$sql .= ", tbl_award_unit_sic AS sic";
		}
		$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
    				AND wage.agreeid = agree.id And agree.unitid = unit.id
    				AND YEAR(agree.implementation) = $year
					AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	if ($agreeTypeId != 'all') {
    		$sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	if ($socText) {
    	    $sql .= " AND socname.name LIKE '%$socText%'";
    	}
    	if ($industry != 'all') {
			$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
        }
        if ($subSic != 'all') {
            $sql .= " AND sic.divid = '$subSic'";
        }
        $sql .= " GROUP BY socid ORDER BY socname.name";
    	$socs = $this->getArray($sql);
		
		foreach ($socs as $soc) {
			$oldWage = $this->getAgreementWage($soc['unitid'], $soc['socid'], $agreeTypeId, $year-1);
			$indexInc = $this->indexFacet->getIndexIncreaseAgree($soc['start'], $soc['length'], $indexId);
			$realAve = $increaseAve = 0;
			if ($oldWage) {
				$increaseAve = ($soc['rate'] - $oldWage['wage'])/$oldWage['wage']*100;
				if ($indexInc != -100 && $indexInc != 0) {
					$realAve = $increaseAve - $indexInc;
				}
			}
			
			$total[$soc['socid']]['real'] 		= $realAve;
			$total[$soc['socid']]['amount'] 	= $soc['rate'];
			$total[$soc['socid']]['increase'] 	= $increaseAve;
			$total[$soc['socid']]['sample'] 	= $soc['sample'];
		}
		
		return $total;
    }
	
	public function getSocMaximum($agreeTypeId, $year, $socText, $industry, $subSic) {
    	$indexId = $this->indexFacet->getIndexId('CPI');
		$payPeriodType = $this->objPayPeriodTypes->getDefaultPPType();
    	$payPeriodCalc = $payPeriodType['factor'];
    	if ($payPeriodCalc == 0) {
    		$sql = "SELECT socname.id AS socid, MAX(wage.weeklyrate/benefits.value)";
    	} else {
    		$sql = "SELECT socname.id AS socid, MAX(wage.weeklyrate*$payPeriodCalc)";
    	}
    	$sql .= " AS rate, COUNT(wage.id) AS sample, agree.unitid AS unitid, agree.implementation AS start,
						agree.length AS length
    	    	FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_unit AS unit,
					 tbl_award_socname AS socname, tbl_award_agree AS agree, tbl_award_benefits AS benefits";
		if ($industry != 'all') {
			$sql .= ", tbl_award_unit_sic AS sic";
		}
		$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
    				AND wage.agreeid = agree.id And agree.unitid = unit.id
    				AND YEAR(agree.implementation) = $year
					AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'";
    	if ($agreeTypeId != 'all') {
    		$sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	if ($socText) {
    	    $sql .= " AND socname.name LIKE '%$socText%'";
    	}
    	if ($industry != 'all') {
			$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
        }
        if ($subSic != 'all') {
            $sql .= " AND sic.divid = '$subSic'";
        }
        $sql .= " GROUP BY socid ORDER BY socname.name";
    	$socs = $this->getArray($sql);
		
		foreach ($socs as $soc) {
			$oldWage = $this->getAgreementWage($soc['unitid'], $soc['socid'], $agreeTypeId, $year-1);
			$indexInc = $this->indexFacet->getIndexIncreaseAgree($soc['start'], $soc['length'], $indexId);
			$realAve = $increaseAve = 0;
			if ($oldWage) {
				$increaseAve = ($soc['rate'] - $oldWage['wage'])/$oldWage['wage']*100;
				if ($indexInc != -100 && $indexInc != 0) {
					$realAve = $increaseAve - $indexInc;
				}
			}
			
			$total[$soc['socid']]['real'] 		= $realAve;
			$total[$soc['socid']]['amount'] 	= $soc['rate'];
			$total[$soc['socid']]['increase'] 	= $increaseAve;
			$total[$soc['socid']]['sample'] 	= $soc['sample'];
		}
		
		return $total;
    }

    public function getSocMedian($agreeTypeId, $year, $socText, $industry, $subSic) {
    	$indexId = $this->indexFacet->getIndexId('CPI');
		$total = array();
		$payPeriodType = $this->objPayPeriodTypes->getDefaultPPType();
    	$payPeriodCalc = $payPeriodType['factor'];
    	$sql = "SELECT DISTINCT socname.id AS socid
    			FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
				tbl_award_socname AS socname, tbl_award_agree AS agree, tbl_award_unit AS unit";
		if ($industry != 'all') {
			$sql .= ", tbl_award_unit_sic AS sic";
		}
		$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
    				AND wage.agreeid = agree.id AND YEAR(agree.implementation) = $year
					AND unit.id = agree.unitid";
    	if ($agreeTypeId != 'all') {
    		$sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	if ($socText) {
    	    $sql .= " AND socname.name LIKE '%$socText%'";
    	}
    	if ($industry != 'all') {
			$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
        }
        if ($subSic != 'all') {
            $sql .= " AND sic.divid = '$subSic'";
        }
        $sql .= " ORDER BY socname.name";
    	$socs = $this->getArray($sql);
    	
		foreach ($socs as $soc) {	
			if ($payPeriodCalc == 0) {
			    $sql = "SELECT wage.weeklyrate/benefits.value +0";
			} else {
			    $sql = "SELECT wage.weeklyrate*$payPeriodCalc +0";
			}
			$sql .= " AS rate, agree.implementation AS start_date, agree.length AS length,
						 agree.unitid AS unitid
					FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
						 tbl_award_socname AS socname, tbl_award_agree AS agree,
						 tbl_award_benefits AS benefits, tbl_award_unit AS unit";
			if ($industry != 'all') {
					$sql .= ", tbl_award_unit_sic AS sic";
			}
			$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
						AND wage.agreeid = agree.id AND unit.id = agree.unitid
						AND YEAR(agree.implementation) = $year
						AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'
						AND socname.id = '{$soc['socid']}'";
			if ($agreeTypeId != 'all') {
			    $sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
			}
			if ($industry != 'all') {
				$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
			}
			if ($subSic != 'all') {
				$sql .= " AND sic.divid = '$subSic'";
			}
			
			$sql .= " ORDER BY rate";
			$data = $this->getArray($sql);
			$wageCount = $indexCount = 0;
			$wageArray = $oldWageArray = $indexArray = array();
			
			foreach ($data AS $line) {
				$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['start_date'], $line['length'], $indexId);
				$oldWage = $this->getAgreementWage($line['unitid'], $soc['socid'], $agreeTypeId, $year-1);
				//echo "this->objDbWages->getAgreementWage({$line['unitid']}, {$soc['socid']}, $agreeTypeId, $year-1) <br />";
				if ($oldWage) { //echo "hit <br />";
					$wageArray[] = $line['rate'];
					$oldWageArray[] = $oldWage['wage'];
					if ($indexInc != 0 && $indexInc != -100) {
						$indexArray[] = $indexInc;
						$indexCount++;
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
			$total[$soc['socid']]['real'] 		= $realAve;
			$total[$soc['socid']]['amount'] 	= $ave;
			$total[$soc['socid']]['increase'] 	= $increaseAve;
			$total[$soc['socid']]['sample'] 	= $wageCount;
		}
		return $total;
    }
	
	public function getSocAverage($agreeTypeId, $year, $socText, $industry, $subSic) {
    	$indexId = $this->indexFacet->getIndexId('CPI');
		$total = array();
		$payPeriodType = $this->objPayPeriodTypes->getDefaultPPType();
    	$payPeriodCalc = $payPeriodType['factor'];
    	$sql = "SELECT DISTINCT socname.id AS socid
    			FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
				tbl_award_socname AS socname, tbl_award_agree AS agree, tbl_award_unit AS unit";
		if ($industry != 'all') {
			$sql .= ", tbl_award_unit_sic AS sic";
		}
		$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
    				AND wage.agreeid = agree.id AND YEAR(agree.implementation) = $year
					AND unit.id = agree.unitid";
    	if ($agreeTypeId != 'all') {
    		$sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	if ($socText) {
    	    $sql .= " AND socname.name LIKE '%$socText%'";
    	}
    	if ($industry != 'all') {
			$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
        }
        if ($subSic != 'all') {
            $sql .= " AND sic.divid = '$subSic'";
        }
        $sql .= " ORDER BY socname.name";
    	$socs = $this->getArray($sql);
    	
		foreach ($socs as $soc) {	
			if ($payPeriodCalc == 0) {
			    $sql = "SELECT wage.weeklyrate/benefits.value +0";
			} else {
			    $sql = "SELECT wage.weeklyrate*$payPeriodCalc +0";
			}
			$sql .= " AS rate, agree.implementation AS start_date, agree.length AS length,
						 agree.unitid AS unitid
					FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
						 tbl_award_socname AS socname, tbl_award_agree AS agree,
						 tbl_award_benefits AS benefits, tbl_award_unit AS unit";
			if ($industry != 'all') {
					$sql .= ", tbl_award_unit_sic AS sic";
			}
			$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
						AND wage.agreeid = agree.id AND unit.id = agree.unitid
						AND YEAR(agree.implementation) = $year
						AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'
						AND socname.id = '{$soc['socid']}'";
			if ($agreeTypeId != 'all') {
			    $sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
			}
			if ($industry != 'all') {
				$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
			}
			if ($subSic != 'all') {
				$sql .= " AND sic.divid = '$subSic'";
			}
			
			$sql .= " ORDER BY rate";
			$data = $this->getArray($sql);
			$wageCount = $indexCount = 0;
			$wageSubTotal = $oldWageSubTotal = $indexSubTotal = 0;
			
			foreach ($data AS $line) {
				$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['start_date'], $line['length'], $indexId);
				$oldWage = $this->getAgreementWage($line['unitid'], $soc['socid'], $agreeTypeId, $year-1);
				if ($oldWage) {
					$wageSubTotal += $line['rate'];
					$oldWageSubTotal += $oldWage['wage'];
					if ($indexInc != 0 && $indexInc != -100) {
						$indexSubTotal += $indexInc;
						$indexCount++;
					}
					$wageCount++;
				}
			}
			if ($wageCount > 3) {
				$ave = $wageSubTotal/$wageCount;
				$oldAve = $oldWageSubTotal/$wageCount;
				
				$increaseAve = ($ave-$oldAve)/$oldAve*100;
				if ($indexCount > 3) {
					$indexAve = $indexSubTotal/$indexCount;
					$realAve = $increaseAve-$indexAve;
				} else {
					$realAve = 0;
				}
    	
			} else {
				$increaseAve = $ave = $realAve = 0;
			}
			$total[$soc['socid']]['real'] 		= $realAve;
			$total[$soc['socid']]['amount'] 	= $ave;
			$total[$soc['socid']]['increase'] 	= $increaseAve;
			$total[$soc['socid']]['sample'] 	= $wageCount;
		}
		return $total;
    }
	
	public function getSocWeightedAverage($agreeTypeId, $year, $socText, $industry, $subSic) {
    	$indexId = $this->indexFacet->getIndexId('CPI');
		$total = array();
		$payPeriodType = $this->objPayPeriodTypes->getDefaultPPType();
    	$payPeriodCalc = $payPeriodType['factor'];
    	$sql = "SELECT DISTINCT socname.id AS socid
    			FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
				tbl_award_socname AS socname, tbl_award_agree AS agree, tbl_award_unit AS unit";
		if ($industry != 'all') {
			$sql .= ", tbl_award_unit_sic AS sic";
		}
		$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
    				AND wage.agreeid = agree.id AND YEAR(agree.implementation) = $year
					AND unit.id = agree.unitid";
    	if ($agreeTypeId != 'all') {
    		$sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
    	}
    	if ($socText) {
    	    $sql .= " AND socname.name LIKE '%$socText%'";
    	}
    	if ($industry != 'all') {
			$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
        }
        if ($subSic != 'all') {
            $sql .= " AND sic.divid = '$subSic'";
        }
        $sql .= " ORDER BY socname.name";
    	$socs = $this->getArray($sql);
    	
		foreach ($socs as $soc) {	
			if ($payPeriodCalc == 0) {
			    $sql = "SELECT wage.weeklyrate/benefits.value +0";
			} else {
			    $sql = "SELECT wage.weeklyrate*$payPeriodCalc +0";
			}
			$sql .= " AS rate, agree.implementation AS start_date, agree.length AS length,
						 agree.unitid AS unitid, agree.workers AS workers
					FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname,
						 tbl_award_socname AS socname, tbl_award_agree AS agree,
						 tbl_award_benefits AS benefits, tbl_award_unit AS unit";
			if ($industry != 'all') {
					$sql .= ", tbl_award_unit_sic AS sic";
			}
			$sql .= " WHERE wage.id = wagesocname.id AND socname.id = wagesocname.socnameid
						AND wage.agreeid = agree.id AND unit.id = agree.unitid
						AND YEAR(agree.implementation) = $year
						AND benefits.agreeid = agree.id AND benefits.nameid = 'init_7'
						AND socname.id = '{$soc['socid']}'";
			if ($agreeTypeId != 'all') {
			    $sql .= ($agreeTypeId == 'cb')? " AND (agree.typeid = '2' || agree.typeid = '3' || agree.typeid = '4')" : " AND agree.typeid = '$agreeTypeId'";
			}
			if ($industry != 'all') {
				$sql .= " AND agree.unitid = sic.unitid AND sic.major_divid = '$industry'";
			}
			if ($subSic != 'all') {
				$sql .= " AND sic.divid = '$subSic'";
			}
			
			$sql .= " ORDER BY rate";
			$data = $this->getArray($sql);
			$wageCount = $indexCount = $sumWorkers = $sumOldWorkers = 0;
			$wageSubSumProduct = $oldWageSubSumProduct = $indexSubSumProduct = 0;
			
			foreach ($data AS $line) {
				$indexInc = $this->indexFacet->getIndexIncreaseAgree($line['start_date'], $line['length'], $indexId);
				$oldWage = $this->getAgreementWage($line['unitid'], $soc['socid'], $agreeTypeId, $year-1);
				if ($oldWage) {
					$wageSubSumProduct += $line['rate']*$line['workers'];
					$oldWageSubSumProduct += $oldWage['wage']*$oldWage['workers'];
					//$oldWageSubSumProduct += $oldWage['wage']*$line['workers'];
					$sumWorkers += $line['workers'];
					$sumOldWorkers += $oldWage['workers'];
					if ($indexInc != 0 && $indexInc != -100) {
						$indexSubSumProduct += $indexInc*$line['workers'];
						$indexCount++;
					}
					
					$wageCount++;
				}
	    	}
	
	    	if ($wageCount > 3 && $sumWorkers > 0) {
	    		$ave = $wageSubSumProduct/$sumWorkers;
				$oldAve = ($sumOldWorkers > 0)? $oldWageSubSumProduct/$sumOldWorkers : 0;
				//$oldAve = $oldWageSubSumProduct/$sumWorkers;
				$increaseAve = ($oldAve > 0)? ($ave-$oldAve)/$oldAve*100 : 0;
				if ($indexCount > 3) {
					$realAve = $increaseAve - ($indexSubSumProduct/$sumWorkers);
				} else {
					$realAve = 0;
				}
	    	} else {
	    		$increaseAve = $ave = $realAve = 0;
	    	}
			
			$total[$soc['socid']]['real'] 		= $realAve;
			$total[$soc['socid']]['amount'] 	= $ave;
			$total[$soc['socid']]['increase'] 	= $increaseAve;
			$total[$soc['socid']]['sample'] 	= $wageCount;
		}
		return $total;
    }
	
}
?>