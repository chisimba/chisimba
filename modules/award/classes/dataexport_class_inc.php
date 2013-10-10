<?php

/**
 * AWARD data export class
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
 * AWARD Ajax class
 * 
 * Class to provide methods for Ajax Functions
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiagree_class_inc.php 94 2008-08-07 11:03:58Z nic $
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
 * AWARD Data export class
 * 
 * Class to provide AWARD data export for admin users to use as a diagnostic tool
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2010 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiagree_class_inc.php 94 2008-08-07 11:03:58Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
 
class dataexport extends dbtable {

    public function init() {
        parent::init('tbl_award_agree');
        
        $this->objRegion = $this->getObject('dbregion', 'awardapi');
        $this->objAgree = $this->getObject('dbagreement', 'awardapi');
        $this->objAgreeType = $this->getObject('dbagreetypes', 'awardapi');
        $this->objBenefitName = $this->getObject('dbbenefitnames', 'awardapi');
        $this->objBenefit = $this->getObject('dbbenefits', 'awardapi');
        $this->objIndex = $this->getObject('awardindex_facet');
        
    }
    
    public function getSicCode($unitId) {
        $sql = "SELECT CONCAT(sicmajordiv.code, sicdiv.code,
                        sicmajorgroup.code, sicgroup.code, sicsubgroup.code) AS code
                FROM tbl_award_unit_sic AS unitsic,
                    tbl_award_sicmajordiv AS sicmajordiv, tbl_award_sicdiv AS sicdiv,
                    tbl_award_sicmajorgroup AS sicmajorgroup, tbl_award_sicgroup AS sicgroup,
                    tbl_award_sicsubgroup AS sicsubgroup
                WHERE unitsic.unitid = '$unitId' AND sicmajordiv.id = unitsic.major_divid
                    AND sicdiv.id = unitsic.divid AND sicmajorgroup.id = unitsic.major_groupid
                    AND sicgroup.id = unitsic.groupid and sicsubgroup.id = unitsic.sub_groupid";
        $sicCode = current($this->getArray($sql));
        return $sicCode['code'];
    }
    
    public function getSicLabel($unitId) {
        $sql = "SELECT CONCAT(sicmajordiv.description, '; ', sicdiv.description) AS label
                FROM tbl_award_unit_sic AS unitsic,
                    tbl_award_sicmajordiv AS sicmajordiv, tbl_award_sicdiv AS sicdiv
                WHERE unitsic.unitid = '$unitId' AND sicmajordiv.id = unitsic.major_divid
                    AND sicdiv.id = unitsic.divid";
        $sic = current($this->getArray($sql));
        return str_replace(',', ' -', $sic['label']); 
    }
    
    public function getUnits($years) {
        if (empty($years)) {
            return FALSE;
        }
        
        $select = "SELECT DISTINCT unit.id, unit.name
                    FROM tbl_award_unit AS unit, tbl_award_agree AS agree ";
        
        $whereClause = "WHERE agree.unitid = unit.id AND (";
        foreach ($years as $year) {
            $whereClause .= "YEAR(agree.implementation) = $year OR ";
        }
        $where = substr($whereClause, 0, strlen($whereClause) - 4) . ") ORDER BY unit.name";
        $sql = $select.$where;
        return $this->getArray($sql);
    }
        
    public function exportConditions($years) {
        $units = $this->getUnits($years);
        if (!$units || empty($units)) {
            return FALSE;
        }
        
        $conditions = $this->objBenefitName->getAll("ORDER BY typeid, id");
        
        foreach ($units as $unit) {
            $row[$unit['id']]['id'] = $unit['id'];
            $row[$unit['id']]['name'] = str_replace(',', ' ', $unit['name']);
            $row[$unit['id']]['party'] = $this->objAgree->getUnionName($unit['id']);
            $agreeTypes = current($this->objAgreeType->getTypesByUnit($unit['id']));
            $row[$unit['id']]['agreetype'] = $agreeTypes['name'];
            $row[$unit['id']]['siccode'] = $this->getSicCode($unit['id']);
            $row[$unit['id']]['siclabel'] = $this->getSicLabel($unit['id']);
            $row[$unit['id']]['region'] = $this->objRegion->getRegionName($unit['id']);
            foreach ($years as $year) {
                $agree = $this->objAgree->getYearAgreement($unit['id'], $year);
                $agreeId[$year] = $agree['id'];
            }
            foreach ($conditions as $benefit) {
                foreach ($years as $year) {
                    $row[$unit['id']][$benefit['id'].'-'.$year] = $this->objBenefit->getBenefitValue($agreeId[$year], $benefit['id']);
                }    
            }
        }
        $first = "ID, BU Name, Parties, Agreement Type, SIC Code, SIC Label, Region, ";
        
        foreach ($conditions as $benefit) {
            foreach ($years as $year) {
                $first .= str_replace(',', ';', $benefit['name']). " - {$year}, ";
            }
        }
        $csv = substr($first, 0, strlen($first) - 2) . "\n";
        
        foreach ($row as $unit) {
            $csv .= implode(', ', $unit). "\n";
        }
        
        return $csv;
    }
    
    public function exportWages($years, $indexId) {
        $units = $this->getUnits($years);
        if (!$units || empty($units)) {
            return FALSE;
        }
        $sqlYear = '';
        $first = "ID, BU Name, Parties, Agreement Type, SIC Code, SIC Label, Region, Occupational Level, Occupation, ";
        foreach ($years as $year) {
            $first .= "$year - Duration of Agreement, Implementation Date, No. of Workers, Wage Rate, Inflation Index Start, Inflation Index End, ";
            $sqlYear .= "YEAR(agree.implementation) = $year OR ";
        }
        //$csv = substr($first, 0, strlen($first) - 2) . "\n";
        $csv = "{$first}Sort String\n";
        
        foreach ($units as $unit) {
            $agreeTypes = current($this->objAgreeType->getTypesByUnit($unit['id']));
            $line = $unit['id'].", ".str_replace(',', ' ', $unit['name']).", ".
                    $this->objAgree->getUnionName($unit['id']).", ".
                    $agreeTypes['name'].", ".$this->getSicCode($unit['id']).", ".
                    $this->getSicLabel($unit['id']).", ".$this->objRegion->getRegionName($unit['id']).",";
            
            $sql = "SELECT DISTINCT socname.id AS occid, socname.name AS occ,
                        soc.description AS level,
                        CASE socname.name
                            WHEN 'Unmapped' THEN 'ZZZ'
                            ELSE socname.name
                        END AS sortstring
                    FROM tbl_award_agree AS agree, tbl_award_wage_socname AS wsname,
                        tbl_award_wage AS wage, tbl_award_socname AS socname,
                        tbl_award_socmajorgroup AS soc
                    WHERE agree.unitid = '{$unit['id']}' AND wsname.id = wage.id
                        AND wage.agreeid = agree.id AND wsname.socnameid = socname.id
                        AND socname.major_groupid = soc.id AND (";
            
            /*$sql = "SELECT socname.id AS occid, socname.name AS occ
                    FROM tbl_award_agree AS agree, tbl_award_wage_socname AS wsname,
                        tbl_award_wage AS wage, tbl_award_socname AS socname
                    WHERE agree.unitid = '{$unit['id']}' AND wsname.id = wage.id
                        AND wage.agreeid = agree.id AND wsname.socnameid = socname.id
                        AND socname.id != 10 AND (";
            */
            $sql .= substr($sqlYear, 0, strlen($sqlYear) - 4) . ") ORDER BY occ";
            $occupations = $this->getArray($sql);
            
            foreach ($occupations as $occ) {
                $occLine = $line. str_replace(',', ';', $occ['level']).", ". str_replace(',', ';', $occ['occ']).", ";
                foreach ($years as $year) {
                    $sql = "SELECT agree.implementation AS implementation,
                                agree.length AS length, agree.workers AS workers,
                                wage.weeklyrate AS weeklyrate
                            FROM tbl_award_agree AS agree, tbl_award_wage AS wage,
                                tbl_award_wage_socname AS wsname
                            WHERE wsname.socnameid = '{$occ['occid']}'
                                AND wsname.id = wage.id AND agree.unitid = {$unit['id']}
                                AND YEAR(agree.implementation) = $year";
                    $data = $this->getArray($sql);
                    if (!empty($data)) {
                        $data = current($data);
                        $occLine .= "{$data['length']}, {$data['implementation']}, {$data['workers']}, {$data['weeklyrate']}, ";
                        $ts = strtotime($data['implementation']);
                        $endTs = strtotime("+{$data['length']} month", $ts);
                        $occLine .= $this->objIndex->getCurrentIndexValue(date('m', $ts), date('Y', $ts) , $indexId).", ";
                        $occLine .= $this->objIndex->getCurrentIndexValue(date('m', $endTs), date('Y', $endTs), $indexId).", ";
                    } else {
                        $occLine .= "--, --, --, --, --, --, ";
                    }
                }
                $csv .= "$occLine". str_replace(',', ';', $occ['sortstring'])."\n";
            }
        }
        return $csv;
    }
}