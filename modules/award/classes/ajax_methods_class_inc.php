<?php

/**
 * AWARD Ajax class
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
 * AWARD Ajax Class
 * 
 * Class to provide AWARD XML-RPC functionality to Chisimba clients
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiagree_class_inc.php 94 2008-08-07 11:03:58Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
 
 class ajax_methods extends object {
 	
 	/**
 	 * Standard Chisimba constructor to initialise
 	 * variables etc.
 	 */
 	public function init() {
 		$this->objUnit = $this->getObject('dbunit','awardapi');
        $this->objDbBranch = $this->getObject('dbbranch','awardapi');
 		$this->objSicDiv = $this->getObject('dbsicdiv','awardapi');
 		$this->objSicMajorDiv = $this->getObject('dbsicmajordiv','awardapi');
 		$this->objLanguage = $this->getObject('language','language');
 	}
 	
 	/**
 	 * Method to get a populated dropdown box of units
 	 * matching a text search string
 	 * 
 	 * @param string $str The search string to look for
 	 * @return string the HTML of the dropdown
 	 */
 	public function getUnitDropdownText($str) {
 		$content = $this->objUnit->getAll("WHERE name LIKE '$str%' ORDER BY name");
 		$objselectUnits = new dropdown('unit');
 		foreach ($content AS $c) {
 			$objselectUnits->addOption($c['id'], $c['name']);
 		}
 		if(!empty($content)) {
			$objselectUnits->setSelected($content[0]['id']);
	   	} else {
		   	$objselectUnits->addOption('-1',$this->objLanguage->languageText('mod_lrs_select', 'award'));
	   	}
	   	return $objselectUnits->show();
 	}
 	
 	/**
 	 * Method to get a populated fropdown of units
 	 * based on sic and type data
 	 * 
 	 * @param string $sic the id of the sic major div
 	 * @param string $sicDiv the id of the sic division
 	 * @param string $agreeType the id of the agreement type
 	 * @return string the HTML of the dropdown
 	 */
 	public function getUnitDropdownSic($sic, $sicDiv, $agreeType) {
 		$content = $this->objUnit->searchBySic($sic, $sicDiv, $agreeType);
	   	$objselectUnits = new dropdown('unit');
	   	foreach ($content AS $c) {
	    	$objselectUnits->addOption($c['id'], $c['name']);
	   	}
	   	if(!empty($content)) {
		   	$objselectUnits->setSelected($content[0]['id']);
	   	} else {
			$objselectUnits->addOption('-1',$this->objLanguage->languageText('mod_lrs_select', 'award'));
	   	}
       	return $objselectUnits->show();
 	}
 	
 	/**
 	 * Method to update the sicDiv when the sic Major
 	 * Div changes
 	 * 
 	 * @param string $sicId The sic major div id
 	 * @return the sic div dropdown
 	 */
 	public function updateSicDiv($sicId) {
 		$sicMajorDiv = $this->objSicMajorDiv->getRow('id',$sicId);
	   	$sicDivList = $this->objSicDiv->getAll("WHERE major_divid = '$sicId' ORDER BY description");
	   	$sicDivInput = new dropdown('sicDiv');
	   	$sicDivInput->extra = " onchange='javascript: unitSearchBySic();'";
	   	foreach($sicDivList as $sic) {
	   		if (strlen($sic['description']) > 50) {
	   			$sic['description'] = substr($sic['description'],0,47)."...";
	   		}
	   		$sicDivInput->addOption($sic['id'],"{$sicMajorDiv['code']}{$sic['code']} - {$sic['description']}");
	   	}
	   
       return $sicDivInput->show();
 	}
    
    /**
     * Method to lazily load the portion of the expired
     * agreement by trade union tree that the user is
     * currently viewing
     *
     * @param string $source 'root' or the id of the node to expand
     * @return string the javascript array of the data to add to the tree
     */
    public function lazyTree($unionId, $root) {
        $response = '[';
        if ($root == "source") {
            $partyBranches = $this->objDbBranch->getAll("WHERE partyid = '$unionId' ORDER BY name ASC");
            foreach ($partyBranches as $branch) {
                $branchName = strtoupper($branch['name']);
                $response .= "{'text': '$branchName'";
                //if branch has expired agrees
                $industries = $this->objDbBranch->getIndustriesWithExpiredByBranch($branch['id']);
                if (!empty($industries)) {
                    $response .= ", 'id': 'branch|{$branch['id']}', 'hasChildren': true";
                }
                $response .= "},";
            }
        } else {
            list($branch, $id) = explode("|", $root);
            if ($branch == "branch") {
                $industries = $this->objDbBranch->getIndustriesWithExpiredByBranch($id);
                if (!empty($industries)) {
                    foreach ($industries as $industry) {
                        $units = $this->objUnit->getUnitsByBranchInd($id,$industry['id']);
                        if (!empty($units)) {
                            $response .= "{'text': '{$industry['description']}', 'id': '$id|{$industry['id']}', 'hasChildren': true},";
                        }
                    }
                }
            } else {
                $units = $this->objUnit->getUnitsByBranchInd($branch,$id);
                foreach($units as $unit) {
                    $unitName = htmlentities(str_replace('&', 'and', ucwords(strtolower($unit['name']))), ENT_QUOTES);
                    $lastAgreement = $this->objLanguage->languageText('phrase_lastagreement');
                    $unitStr = "<table width=\"99%\"><tbody><tr><td width=\"50%\"><strong>$unitName</strong></td><td><strong>$lastAgreement:</strong> {$unit['max(agree.implementation)']}</td></tr></tbody></table>";
                    $response .= "{'text': '$unitStr'},";
                }
            }
        }
        $response .= ']';
        return $response;
    }
    
}
?>