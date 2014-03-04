<?php
/**
 * ahis passive Class
 *
 * passive class
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
 * @version   $Id: passive_class_inc.php 13884 2009-07-08 14:32:28Z nic $
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
 * ahis disease report Class
 * 
 * Class to access disease reports in the DB
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_class_inc.php 13884 2009-07-08 14:32:28Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class diseasereport extends dbtable {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_diseasereport');
			$this->objCountry 	= $this->getObject('country');
			$this->objUser 		= $this->getObject('user', 'security');
			$this->objDisease	= $this->getObject('diseases');
			$this->objPartition = $this->getObject('partitions');
			
			$this->objPartitionLevel 	= $this->getObject('partitionlevel');
			$this->objPartitionCategory = $this->getObject('partitioncategory');
			$this->objDiseaseControlMeasure = $this->getObject('diseasecontrolmeasure');
		   $this->objDiseasespecies = $this->getObject('diseasespecies');
		   $this->objSpeciesType = $this->getObject('speciestype'); 
		   $this->objSpeciesNew = $this->getObject('speciesnew');   
		   $this->objSpeciesNames= $this->getObject('speciesnames');      
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	
	
	
	/**
	 * Method to return the next outbreak reference number
	 *
	 * @return int Reference no.
	 */
	public function genOutbreakCode($countryId, $diseaseId, $year) {
		$year 	 = date('y',mktime(1,1,1,1,1,$year));
		$country = $this->objCountry->getRow('id', $countryId);
		$disease = $this->objDisease->getRow('id', $diseaseId);
		$count = $this->getRecordCount("WHERE countryid = '$countryId' AND diseaseid = '$diseaseId'");
		$count++;
		switch ($count) {
			case $count < 10:
				$count = '00'.$count;
				break;
			case $count > 9 && $count < 100:
				$count = '0'.$count;
				break;
		}
		return $country['iso_country_code'].$disease['disease_code'].$count.$year;
		
	}
	
	public function genOutbreakCountry($countryId) {
		$outbreaks = $this->getAll("WHERE countryid = '$countryId' ORDER BY outbreakcode");
		$arrayd = array();
		foreach ($outbreaks as $outbreak) {
			$arrayd[$outbreak['id']]= $outbreak['outbreakcode'];
		}
		return $arrayd;
	}
	public function getOutbreaks($country) {
		$outbreaks = $this->getAll("WHERE countryid = '$country' ORDER BY outbreakcode");
		$array = array();
		foreach ($outbreaks as $outbreak) {
			$partition = $this->objPartition->getRow('id', $outbreak['partitionid']);
			$partitionLevel = $this->objPartitionLevel->getRow('id', $partition['partitionlevelid']);
			$partitionType = $this->objPartitionCategory->getRow('id', $partitionLevel['partitioncategoryid']);
			$array[] = array('outbreakCode'=>$outbreak['outbreakcode'],
							 'partitionType'=>$partitionType['partitioncategory'],
							 'partitionLevel'=>$partitionLevel['partitionlevel'],
							 'partitionName'=>$partition['partitionname'],
							 'month'=>date('F', strtotime($outbreak['reportdate'])),
							 'year'=>date('Y', strtotime($outbreak['reportdate'])));
		}
		return $array;
	}
	
	public function getdiseasename(){
		$data = $this->getAll("ORDER BY outbreakcode");
		$array = array();
		foreach($data as $vars){
			$disease = $this->objDisease->getRow('id',$vars['diseaseid']);
			$array[] = array('diseasename'=>$disease['disease_name'],'outbreakcode'=>$vars['outbreakcode']);
	
		}
		return $array;
	
	}
	
	public function getDiseaseId($outbreakCode) {
		$report = $this->getRow('outbreakcode', $outbreakCode);
		return $report['diseaseid'];
	}
	
	public function getdisease($diseaseId,$district){

		$disease = $this->objDiseaseControlMeasure->getAll("WHERE outbreakcode ='$diseaseId' AND controlmeasureid='init_02'");
		$diseaseArray = array();
		//print_r($disease);exit;
		foreach ($disease as $dis) {
		    $val = $dis['outbreakcode'];
			$data = $this->getRow('outbreakcode',$val);
	
			$datv = $this->objDisease->getRow('id',$data['diseaseid']);
	
			//$diseaseArray[$datv['disease_name']] = $datv['disease_name']; 
			$diseaseArray[$datv['id']] = $datv['disease_name'];
		}
		//print_r($diseaseArray);
		return $diseaseArray;
	
	}
	
	public function getspecies($diseaseId,$district){

    $disease = $this->objDiseaseControlMeasure->getAll("WHERE outbreakcode ='$diseaseId' AND controlmeasureid='init_02'");
		$diseaseArray = array();
				$dat = array();
				
//print_r($disease);exit;
foreach($disease as $dis) {
      $val = $dis['outbreakcode'];
    $data = $this->getRow('outbreakcode',$val);
      $val1 = $data['diseaseid'];
	$datv = $this->objDiseasespecies->getAll("WHERE diseaseid ='$val1'");
	//print_r($datv);
    foreach($datv as $ddatv){
    $speciesId = $ddatv['speciesid'];
$ddat =$this->objSpeciesType->getRow('id',$speciesId);
$sdat =$this->objSpeciesNew->getAll("WHERE speciestypeid = '$ddat[id]'");
   foreach($sdat as $ssdat){
  $ndat = $this->objSpeciesNames->getAll("WHERE species_id = '$ssdat[id]'");
  foreach($ndat as $nndat){
       $dat[$nndat['id']]=$nndat['common_name'];
       }
   }

    }
		}
		//print_r($dat);
		return $dat;
	
	}
	
	
	public function getOutbreak($countryId){
	
		$sql = "SELECT dr.id AS id, dr.outbreakcode AS outbreakcode
				FROM tbl_ahis_diseasereport AS dr, tbl_ahis_diseasecontrolmeasure AS dcm,
					 tbl_ahis_partitions AS p
				WHERE p.id = dr.partitionid AND p.countryid = '$countryId'
					AND dr.outbreakcode = dcm.outbreakcode
					AND dcm.controlmeasureid = 'init_02'";
					
		return $this->getArray($sql);
	}
}