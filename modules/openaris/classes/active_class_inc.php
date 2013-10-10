<?php
/**
 * ahis active Class
 *
 * active class
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
 * @author    Rosina Ntow<rntow@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: active_class_inc.php 12627 2009-02-26 14:29:10Z nic $
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
 * ahis active Class
 * 
 * Class to access active surveillance reports in the DB
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Rosina Ntow<rntow@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: active_class_inc.php 
 * @link      http://avoir.uwc.ac.za
 */
class active extends dbtable {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			
			$this->objHerd = $this->getObject('newherd');
			$this->objSample = $this->getObject('sampledetails');
			$this->objGeo2 = $this->getObject('geolevel2');
			$this->objGeo3 = $this->getObject('geolevel3');
			$this->objSpecies = $this->getObject('species');
			parent::init('tbl_ahis_active_surveillance');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function listcamp()
    {
       $sql = "SELECT * FROM tbl_ahis_active_surveillance";
        return $this->getArray($sql);
    }
	
	
	public function getallname($campaign){
	
	$sql="SELECT * FROM tbl_ahis_active_surveillance AS d WHERE 
	     d.campname = '$campaign'";
	     
	return $this->getArray($sql);
	
	
	}
	
	public function getcamp($campaign){
	
	$sql="SELECT * FROM tbl_ahis_active_surveillance AS d WHERE 
	     d.campname = '$campaign'";
	     
	return $this->getArray($sql);
	}
 
   public function getactive($year,$month){
        $sql = "SELECT active.*,farm.*,sample.* FROM tbl_ahis_active_surveillance AS active,";
        $sql.= " tbl_ahis_newherd AS farm, tbl_ahis_sampledetails AS sample";

        $sql.= " WHERE active.id = farm.activeid";
        $sql.= " AND farm.id= sample.newherdid";
        $sql.= " AND YEAR(sample.samplingdate) = '$year' AND MONTH(sample.samplingdate) = '$month'";
        $sql.= " ORDER BY active.campname ASC";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
   
   }
   
   /**
	 * Method to return all active surveillance data in JSON
	 * format to be used in the Google Earth GIS plugin
	 *
	 * @return JSON array of all data
	 * */
	public function getJSONData() {
		$allData = $this->getAll("ORDER BY campname ASC");
		$count = 0;
		$results = array();

		foreach ($allData as $row) {
			$herds = $this->objHerd->getAll("WHERE activeid = '{$row['id']}'");
			foreach ($herds as $herd) {
				$latitude = $herd['latdeg'] + ($herd['latmin']/60);
				if ($herd['latdirec'] == "S") {
					$latitude *= -1;
				}
				$longitude = $herd['longdeg'] + ($herd['longmin']/60);
				if ($herd['longdirec'] == "W") {
					$longitude *= -1;
				}
				
				$geo2 = $this->objGeo2->getRow('name', $herd['geolevel2']);
				$geo3 = $this->objGeo3->getName($geo2['geo3id']);
				
				$samples = $this->objSample->getAll("WHERE newherdid = '{$herd['id']}'");
				foreach ($samples as $sample) {
					
					//log_debug("GIS: $latitude  $longitude {$row['latdirec']}{$row['longdirec']}");
					$results[] = array(
						'row' 			=> "$count",
						'lat'			=> $latitude,
						'long'			=> $longitude,
						'year'			=> date('Y', strtotime($sample['testdate'])),
						'month'			=> date('n', strtotime($sample['testdate'])),
						'refno'			=> $row['campname'],
						'geolayer3'		=> $geo3,
						'geolayer2'		=> $herd['geolevel2'],
						'locationname'	=> $herd['territory'],
						'animal'		=> $sample['species'],
						'diseasetype'	=> $row['disease'],
						'surveytype'	=> $row['surveytype'],
						'testdate'		=> $sample['testdate'],
						'age'			=> $sample['age'],
						'sex'			=> $sample['sex'],
						'number'		=> $sample['number'],
						'farm'			=> $herd['farmname'],
						'farmingsystem'	=> $herd['farmingtype'],
						'sampletype'	=> $sample['sampletype'],
						'testtype'		=> $sample['testtype'],
						'testresult'	=> $sample['testresult']
					);
					$count++;
				}
			}
		}
		return json_encode(array('results'=>$results));
	}
}