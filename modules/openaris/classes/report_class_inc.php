<?php

/**
 * ahis report Class
 *
 * File containing the report generation class
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
 * @version   $Id: report_class_inc.php 13795 2009-06-30 08:22:23Z nic $
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
 * ahis report Class
 * 
 * view class to generate ahis reports
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: report_class_inc.php 13795 2009-06-30 08:22:23Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class report extends object {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			//$this->objPassive = $this->getObject('passive');
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objGeo3 = $this->getObject('geolevel3');
			$this->objGeo2 = $this->getObject('geolevel2');
			$this->objTerritory = $this->getObject('territory');
			$this->objUser = $this->getObject('user', 'security');
			$this->objAge = $this->getObject('age');
			$this->objSex = $this->getObject('sex');
            $this->objOutbreak = $this->getObject('outbreak');
            $this->objDiagnosis = $this->getObject('diagnosis');
            $this->objControl = $this->getObject('control');
			$this->objProduction = $this->getObject('production');
            $this->objOutbreak = $this->getObject('outbreak');
            $this->objSpecies = $this->getObject('species');
            $this->objDisease = $this->getObject('disease');
            $this->objAhisUser = $this->getObject('ahisuser');
			$this->objAnimalPopulation= $this->getObject('dbanimalpop');
			$this->objMeatInspect = $this->getObject('db_meat_inspection');
			$this->objSlaughter= $this->getObject('ahis_slaughter');
            $this->objCausative = $this->getObject('causative');
			$this->objQuality = $this->getObject('quality');
			$this->objAnimalmovement = $this->getObject('animalmovement');
            $this->objLivestockimport = $this->getObject('livestockimport');
			$this->objLivestockexport = $this->getObject('livestockexport');
			$this->objActive = $this->getObject('active');
			$this->objVaccineInventory=$this->getObject('vaccineinventory');
			$this->objAnimalDeworming=$this->getObject('animaldeworming');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	
	/**
	 * Method to generate a table of the reports in the database for viewing
	 * online or saving to PDF
	 *
	 * @param integer  $year The year to consider reports from
	 * @param integer  $month The month to consider reports from
	 * @param string   $reportTypeThe id of the type of reports to consider
	 * @param boolean  $static whether or not the static css should be applied
	 * @return string  the html table
	 */
	public function generateReport($year, $month, $reportType, $static = FALSE) {
		
		$objTable = $this->newObject('htmltable', 'htmlelements');
		
		switch ($reportType) {
			case 'init_01': 			//passive surveillance
				
				//$objTable->width = '3500px';
				$objTable->cellspacing = '2px';
				if ($static) {	
					$objTable->border = '1px';
				}
				
				$objTable->cssClass = "stat";
				$headerArray = array($this->objLanguage->languageText('phrase_geolevel3'),$this->objLanguage->languageText('phrase_outbreakref'),
									 $this->objLanguage->languageText('mod_ahis_reportofficer','openaris'),$this->objLanguage->languageText('word_disease'),
									 $this->objLanguage->languageText('mod_ahis_isreporteddate','openaris'),$this->objLanguage->languageText('mod_ahis_vetdate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_investigationdate','openaris'),$this->objLanguage->languageText('mod_ahis_diagnosisdate','openaris'),
									 $this->objLanguage->languageText('word_location'),$this->objLanguage->languageText('word_latitude'),$this->objLanguage->languageText('word_longitude'),
									 $this->objLanguage->languageText('word_species'),$this->objLanguage->languageText('word_age'),$this->objLanguage->languageText('word_sex'),
									 $this->objLanguage->languageText('word_production'),$this->objLanguage->languageText('phrase_control'),
									 $this->objLanguage->languageText('phrase_diagnosis'),$this->objLanguage->languageText('word_susceptible'),
									 $this->objLanguage->languageText('phrase_newcases'),$this->objLanguage->languageText('word_deaths'),$this->objLanguage->languageText('word_slaughtered'),
									 $this->objLanguage->languageText('word_recovered'),$this->objLanguage->languageText('word_destroyed'),$this->objLanguage->languageText('phrase_outbreak'),
									 $this->objLanguage->languageText('word_vaccinated'),$this->objLanguage->languageText('word_prophylactic'),
									 $this->objLanguage->languageText('mod_ahis_vacsource','openaris'),$this->objLanguage->languageText('mod_ahis_batch','openaris'),
									 $this->objLanguage->languageText('mod_ahis_manufacturedate','openaris'),$this->objLanguage->languageText('mod_ahis_expiredate','openaris'));
				$objTable->addHeader($headerArray);
				
				$passiveRecords = $this->objPassive->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ORDER BY reportdate");
				$class = 'odd';
				foreach ($passiveRecords as $report) {
					
					$latitude = "{$report['latdeg']}°{$report['latmin']}'{$report['latdirec']}";
					$longitude = "{$report['longdeg']}°{$report['longmin']}'{$report['longdirec']}";
					
					$objTable->startRow($class);
					
					$geo2 = $this->objGeo2->getRow('id', $report['geo2id']);
					$geo3 = $this->objGeo3->getRow('id', $geo2['geo3id']);
					$objTable->addCell($geo3['name']);
					
					$objTable->addCell($report['refno']);
					$objTable->addCell($this->objUser->fullname($report['reporterid']));
					
					$disease = $this->objDisease->getRow('id', $report['diseaseid']);
					$objTable->addCell($disease['name']);
					
					$objTable->addCell($report['reporteddate']);
					$objTable->addCell($report['vetdate']);
					$objTable->addCell($report['investigationdate']);
					$objTable->addCell($report['diagnosisdate']);
					
					$location = $this->objTerritory->getRow('id', $report['locationid']);
					$objTable->addCell($location['name']);
					
					$objTable->addCell($latitude);
					$objTable->addCell($longitude);
					
					$species = $this->objSpecies->getRow('id', $report['speciesid']);
					$objTable->addCell($species['name']);
					
					$age = $this->objAge->getRow('id', $report['ageid']);
					$objTable->addCell($age['name']);
					
					$sex = $this->objSex->getRow('id',$report['sexid']);
					$objTable->addCell($sex['name']);
					
					$production = $this->objProduction->getRow('id', $report['productionid']);
					$objTable->addCell($production['name']);
					
					$control = $this->objControl->getRow('id', $report['controlmeasureid']);
					$objTable->addCell($control['name']);
					
					$basis = $this->objDiagnosis->getRow('id', $report['basisofdiagnosisid']);
					$objTable->addCell($basis['name']);
					
					$objTable->addCell($report['susceptible']);
					$objTable->addCell($report['newcases']);
					$objTable->addCell($report['deaths']);
					$objTable->addCell($report['slaughtered']);
					$objTable->addCell($report['recovered']);
					$objTable->addCell($report['destroyed']);
					
					$status = $this->objOutbreak->getRow('id', $report['statusid']);
					$objTable->addCell($status['name']);
					
					$objTable->addCell($report['vaccinated']);
					$objTable->addCell($report['prophylactic']);
					$objTable->addCell($report['vaccinesource']);
					$objTable->addCell($report['vaccinebatch']);
					$objTable->addCell($report['vaccinemanufacturedate']);
					$objTable->addCell($report['vaccineexpirydate']);
					
					$objTable->endRow();
					$class = ($class == 'even')? 'odd' : 'even';
				}
				break;
				
		}
		$css = "<style>
					table.stat th, table.stat td {
					    font-size : 8px;
					    font-family : 'Myriad Web',Verdana,Helvetica,Arial,sans-serif;
						line-height: 1.5em;
                    }
                </style>";
				
		return "<br />$css".$objTable->show();
	}
	
	/**
	 * Method to retrieve the data from the database and return it as a
	 * comma separated list, to be openend in excel or other csv reading application
	 *
	 * @param integer  $year The year to consider reports from
	 * @param integer  $month The month to consider reports from
	 * @param string   $reportTypeThe id of the type of reports to consider
	 * @return string  the csv string
	 */
	public function generateCSV($year, $month, $reportType) {
		switch ($reportType) {
			case 'init_01': 			//passive surveillance
				
				$headerArray = array($this->objLanguage->languageText('phrase_geolevel3'),
									 $this->objLanguage->languageText('phrase_geolevel2'),
									 $this->objLanguage->languageText('phrase_outbreakref'),
									 $this->objLanguage->languageText('mod_ahis_reportofficer','openaris'),
									 $this->objLanguage->languageText('mod_ahis_reportdate','openaris'),
									 $this->objLanguage->languageText('phrase_outbreak'),
									 $this->objLanguage->languageText('mod_ahis_dateprepared','openaris'),
									 $this->objLanguage->languageText('mod_ahis_ibardate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_dvsdate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_isreporteddate','openaris'),
									 $this->objLanguage->languageText('phrase_quality'),
									 $this->objLanguage->languageText('word_remarks'),
									 $this->objLanguage->languageText('mod_ahis_vetdate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_dateoccurence','openaris'),
									 $this->objLanguage->languageText('mod_ahis_diagnosisdate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_investigationdate','openaris'),
									 $this->objLanguage->languageText('word_location'),
									 $this->objLanguage->languageText('word_latitude'),
									 $this->objLanguage->languageText('word_longitude'),
									 $this->objLanguage->languageText('word_disease'),
									 $this->objLanguage->languageText('word_causative'),
									 $this->objLanguage->languageText('word_species'),
									 $this->objLanguage->languageText('phrase_age'),
									 $this->objLanguage->languageText('word_sex'),
									 $this->objLanguage->languageText('word_production'),
									 $this->objLanguage->languageText('phrase_control'),
									 $this->objLanguage->languageText('phrase_diagnosis'),
									 $this->objLanguage->languageText('word_susceptible'),
									 $this->objLanguage->languageText('word_cases'),
									 $this->objLanguage->languageText('word_deaths'),
									 $this->objLanguage->languageText('word_vaccinated'),
									 $this->objLanguage->languageText('word_slaughtered'),
									 $this->objLanguage->languageText('word_destroyed'),
									 $this->objLanguage->languageText('word_production'),
									 $this->objLanguage->languageText('phrase_newcases'),
									 $this->objLanguage->languageText('word_recovered'),
									 $this->objLanguage->languageText('word_prophylactic'),
									 $this->objLanguage->languageText('mod_ahis_vacsource','openaris'),
									 $this->objLanguage->languageText('mod_ahis_batch','openaris'),
									 $this->objLanguage->languageText('mod_ahis_manufacturedate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_expiredate','openaris'),
									 $this->objLanguage->languageText('mod_ahis_panvactested', 'openaris'));
				
				$passiveRecords = $this->objPassive->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ");
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($passiveRecords as $report) {
					
					$latitude = "{$report['latdeg']}°{$report['latmin']}'{$report['latdirec']}";
					$longitude = "{$report['longdeg']}°{$report['longmin']}'{$report['longdirec']}";										
					$geo2 = $this->objGeo2->getRow('id', $report['geo2id']);
					$geo3 = $this->objGeo3->getRow('id', $geo2['geo3id']);
					$age = $this->objAge->getRow('id', $report['ageid']);
					$sex = $this->objSex->getRow('id',$report['sexid']);
					$production = $this->objProduction->getRow('id', $report['productionid']);
					$control = $this->objControl->getRow('id', $report['controlmeasureid']);
					$quality = $this->objQuality->getRow('id', $report['qualityid']);
					$causative = $this->objCausative->getRow('id', $report['causativeid']);
					$basis = $this->objDiagnosis->getRow('id', $report['basisofdiagnosisid']);
					$status = $this->objOutbreak->getRow('id', $report['statusid']);
					$location = $this->objTerritory->getRow('id', $report['locationid']);
					$disease = $this->objDisease->getRow('id', $report['diseaseid']);
					$species = $this->objSpecies->getRow('id', $report['speciesid']);
					$panvac = ($report['vaccinetested'])? "Yes" : "No";
					
					$row = array($geo3['name'],
								 $geo2['name'],
								 $report['refno'],
								 $this->objUser->fullname($report['reporterid']),
								 $report['reportdate'],
								 $status['name'],
								 $report['prepareddate'],
								 $report['ibardate'],
								 $report['dvsdate'],
								 $report['reporteddate'],
								 $quality['name'],
								 $report['remarks'],
								 $report['vetdate'],
								 $report['occurencedate'],
								 $report['diagnosisdate'],
								 $report['investigationdate'],
								 $location['name'],
								 $latitude,
								 $longitude,
								 $disease['name'],
								 $causative['name'],
								 $species['name'],
								 $age['name'],
								 $sex['name'],
								 $production['name'],
								 $control['name'],
								 $basis['name'],
								 $report['susceptible'],
								 $report['cases'],
								 $report['deaths'],
								 $report['vaccinated'],
								 $report['slaughtered'],
								 $report['destroyed'],
								 $report['production'],
								 $report['newcases'],
								 $report['recovered'],
								 $report['prophylactic'],
								 $report['vaccinesource'],
								 $report['vaccinebatch'],
								 $report['vaccinemanufacturedate'],
								 $report['vaccineexpirydate'],
								 $panvac
							);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
			case 'init_02': 			//animal population
				
				$headerArray = array($this->objLanguage->languageText('word_district'),'Animal Classification','Number Of Animals','Animal Production','Source');
				
				$populationRecords = $this->objAnimalPopulation->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ");
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($populationRecords as $report) {
					
					$row = array($report['district'],$report['classification'],$report['number'],$report['production'],$report['source']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
				case 'init_03': 			//meat inspection
				
				$headerArray = array($this->objLanguage->languageText('phrase_districtname'),'Inspection Date','Number Of Cases','Number At Risk');
				
				$inspectionRecords = $this->objMeatInspect->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ");
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($inspectionRecords as $report) {
					
					$row = array($report['district'],$report['inspection_date'],$report['num_of_cases'],$report['num_of_risks']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
				case 'init_04': 			//slaughter
				
				$headerArray = array($this->objLanguage->languageText('phrase_districtname'),'Number Of Cattle','Number of Sheep','Number of Goats','Number of Pigs','Number of Poultry','Other','Number','Remarks');
				
				$slaughterRecords = $this->objSlaughter->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ");
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($slaughterRecords as $report) {
					
					$row = array($report['district'],$report['num_cattle'],$report['num_sheep'],$report['num_goats'],$report['num_pigs'],$report['num_poultry'],$report['other'],$report['name_of_other'],$report['remarks']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
		//animal movement report generation
		case 'init_06':
				
				$headerArray = array($this->objLanguage->languageText('word_district'),'Animal Classification','Purpose','Animal of Origin','Animal Destination','Remarks');
				
				$movementRecords = $this->objAnimalmovement->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ");
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($movementRecords as $report) {
					
					$row = array($report['district'],$report['classification'],$report['purpose'],$report['origin'],$report['destination'],$report['remarks']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
			
		//livestock import report generation		
		case 'init_07': 			
				
				$headerArray = array($this->objLanguage->languageText('phrase_districtname'),'Point of Entry','Animal Origin','Animal Destination','Animal Classification','Egg Units','Milk Units','Cheese Units','Poultry Units', 'Beef Units','Count of Species of Live Animal');
				
				$importRecords = $this->objLivestockimport->getALL();
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($importRecords as $report) {
					
					$row = array($report['district'],$report['entrypoint'],$report['origin'],$report['destination'],$report['classification'],$report['eggs'],$report['milk'],$report['cheese'],$report['poultry'],$report['beef'],$report['count']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
		//livestock export report generation		
		case 'init_08': 
				
				$headerArray = array($this->objLanguage->languageText('phrase_districtname'),'Point of Entry','Animal Origin','Animal Destination','Animal Classification','Egg Units','Milk Units','Cheese Units','Poultry Units', 'Beef Units','Count of Species of Live Animal');
				
				$exportRecords = $this->objLivestockexport->getALL();
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($exportRecords as $report) {
					
					$row = array($report['district'],$report['entrypoint'],$report['origin'],$report['destination'],$report['classification'],$report['eggs'],$report['milk'],$report['cheese'],$report['poultry'],$report['beef'],$report['count']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
		case 'init_10': 
				
				$headerArray = array($this->objLanguage->languageText('phrase_districtname'),'Vaccine Name','Total Doses in hand','Total Doses at Start of Month','Month Start Date','Total Doses at End of Month','Month End Date','Total Doses Received in Month','Doses Used', 'Doses Wasted');
				
				$vaccineInventoryRecords = $this->objVaccineInventory->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ORDER BY reportdate");
				$csv = implode(",", $headerArray)."\n";

				foreach ($vaccineInventoryRecords as $report) {
					
					$row = array($report['district'],$report['vaccinename'],$report['doses'],$report['dosesstartofmonth'],$report['startmonth'],$report['dosesendofmonth'],$report['endmonth'],$report['dosesreceived'],$report['dosesused'],$report['doseswasted']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;
				
				//deworming report generation
		 case 'init_09': 
				
				$headerArray = array($this->objLanguage->languageText('phrase_districtname'),'Animal Classification','Number of animals Dewormed','Control Measure','Remarks');
				
				$dewormingRecords = $this->objAnimalDeworming->getALL("WHERE YEAR(reportdate) = '$year'
														   AND MONTH(reportdate) = '$month'
														   ");
				$csv = implode(",", $headerArray)."\n";
				
				foreach ($dewormingRecords as $report) {
					
					$row = array($report['district'],$report['classification'],$report['numberofanimals'],$report['antiemitictype'],$report['remarks']);
					
					$csv .= implode(",", $row)."\n";
				}
				return $csv;

		default:
		
		      $headerArray = array($this->objLanguage->languageText('phrase_campaignname'),
								   $this->objLanguage->languageText('mod_ahis_reportofficer','openaris'),
								   $this->objLanguage->languageText('word_disease'),
								   $this->objLanguage->languageText('phrase_surveytype'),
								   $this->objLanguage->languageText('word_comments'),
								   $this->objLanguage->languageText('phrase_testtype'),
								   $this->objLanguage->languageText('word_sensitivity'),
								   $this->objLanguage->languageText('word_specificity'),
								   $this->objLanguage->languageText('word_location'),
								   $this->objLanguage->languageText('phrase_geolevel2'),
								   $this->objLanguage->languageText('word_farm'),
								   $this->objLanguage->languageText('phrase_farmingsystem'),
								   $this->objLanguage->languageText('phrase_sampleid'),
								   $this->objLanguage->languageText('phrase_animalid'),
								   $this->objLanguage->languageText('word_species'),
								   $this->objLanguage->languageText('word_age'),
								   $this->objLanguage->languageText('word_sex'),
								   $this->objLanguage->languageText('phrase_sampletype'),
								   $this->objLanguage->languageText('phrase_testtype'),
								   $this->objLanguage->languageText('phrase_dateoftest'),
								   $this->objLanguage->languageText('phrase_testresult'),
								   $this->objLanguage->languageText('word_specification'),
								   $this->objLanguage->languageText('phrase_vaccinationhistory'),
								   $this->objLanguage->languageText('word_number'),
								   $this->objLanguage->languageText('word_remarks'));
									 
				$activeRecords = $this->objActive->getactive($year,$month);
				$data = $this->objAhisUser->getList();

				$csv = implode(",", $headerArray)."\n";


				if(!empty($activeRecords)){
				foreach ($activeRecords as $report) {
				foreach($data as $var){if($report['reporterid']==$var['userid']){
					
					$row = array($report['campname'],$var['name'],$report['disease'],$report['surveytype'],$report['comments'],
					$report['testtype'],$report['sensitivity'],$report['specificity'],$report['territory'],
					$report['geolevel2'],$report['farmname'],$report['farmingtype'],$report['sampleid'],$report['animalid'],
					$report['species'],$report['age'],$report['sex'],$report['sampletype'],$report['testtype'],
					$report['testdate'],$report['testresult'],$report['specification'],$report['vachist'],$report['number'],
					$report['remarks']);
					
					$csv .= implode(",", $row)."\n";
					}
				}
				}
			

				}
				return $csv;
	}
	}
}