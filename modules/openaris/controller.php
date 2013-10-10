<?php
/**
 * AHIS
 *
 * Controller for AHIS
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
 * @author    Nic Appleby <nappleby@uwc.ac.za>,Rosina Ntow<rntow@ug.edu.gh>,Patrick Kuti<pkuti@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 13885 2009-07-08 14:38:03Z nic $
 * @link      http://avoir.uwc.ac.za
 */
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 *  ahis class
 * 
 *  controller class for Chisimba AHIS
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 13885 2009-07-08 14:38:03Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class openaris extends controller {
     
    /**
     * Admin actions array
     * @var array used to store a list of admin only actions
     * @access private
     */
    private $adminActions;
     
    /**
     * Language object
     * @var object used to fetch language items
     * @access public
     */
    public $objLanguage;
    
    /**
     * User object
     * @var object used to fetch user info
     * @access public
     */
    public $objUser;
    
    /**
     * Logger object
     * @var object used to log module usage
     * @access public
     */
    public $objLogger;
     
     /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
			$this->objConfig = $this->getObject('altconfig', 'config');
            //Log this module call
            $this->objLog = $this->newObject('logactivity', 'logger');
            $this->objLog->log();
            $this->setVar('pageSuppressToolbar', TRUE);
            $this->setLayoutTemplate('ahis_layout_tpl.php');
            $this->objGeo3 = $this->getObject('geolevel3');
            $this->objGeo2 = $this->getObject('geolevel2');
            $this->objTerritory = $this->getObject('territory');
            $this->objAhisUser = $this->getObject('ahisuser');
            $this->objProduction = $this->getObject('production');
            $this->objTitle = $this->getObject('title');
            $this->objStatus = $this->getObject('status');
            $this->objDepartment = $this->getObject('department');
            $this->objRole = $this->getObject('role');
            $this->objSex = $this->getObject('sex');
            $this->objOutbreak = $this->getObject('outbreak');
            $this->objDiagnosis = $this->getObject('diagnosis');
            $this->objControl = $this->getObject('control');
            $this->objQuality = $this->getObject('quality');
            $this->objAge = $this->getObject('age');
            $this->objRole = $this->getObject('role');
            $this->objDepartment = $this->getObject('department');
            $this->objDiseaseReport = $this->getObject('diseasereport');
            $this->objReport = $this->getObject('reporttype');
            $this->objDisease = $this->getObject('disease');
            $this->objTest = $this->getObject('test');
            $this->objTestresult = $this->getObject('testresult');
            $this->objSample = $this->getObject('sample');
            $this->objSurvey = $this->getObject('survey');
            $this->objFarmingsystem = $this->getObject('farmingsystem');
            $this->objVaccination = $this->getObject('vaccinationhistory');
            $this->objBreed = $this->getObject('breed');
            $this->objSpecies = $this->getObject('species');
            $this->objAnimCat = $this->getObject('animalcat');
            $this->objTlu = $this->getObject('tluconversion');
            $this->objActive = $this->getObject('active');
            $this->objCausative = $this->getObject('causative');
            $this->objNewherd = $this->getObject('newherd');
            $this->objViewReport = $this->getObject('report');
            $this->objVacinventory = $this->getObject('vacinventory');
            $this->objAnimalPopCensus= $this->getObject('animal_population');
            $this->objSampledetails = $this->getObject('sampledetails');
            $this->objSampling = $this->getObject('sampling');
            $this->objAnimalProduction = $this->getObject('animalproduction');
			$this->objMeatInspect = $this->getObject('db_meat_inspection');
			$this->objAnimalPopulation= $this->getObject('dbanimalpop');
			$this->objSlaughter= $this->getObject('ahis_slaughter');
			$this->objLanguages= $this->getObject('languages');
			$this->objCountry= $this->getObject('country');
			$this->objCurrency= $this->getObject('currency');
			$this->objUnitOfArea= $this->getObject('unitofarea');
			$this->objLocalityType= $this->getObject('localitytypes');
			$this->objDiagnosticMethod= $this->getObject('diagnostic_method');
			$this->objOtherControlMeasures= $this->getObject('other_control_measures');
			$this->objSpeciesNames= $this->getObject('speciesnames');
			$this->objDiseases= $this->getObject('diseases');
			$this->objSpeciesEconomicFunction= $this->getObject('species_economic_function');
			
			$this->objAnimalmovement = $this->getObject('animalmovement');
			$this->objLivestockimport = $this->getObject('livestockimport');
			$this->objLivestockexport = $this->getObject('livestockexport');

			$this->objDiseaseLocality = $this->getObject('diseaselocality');
			$this->objDiseaseDiagnosis = $this->getObject('diseasediagnosis');
			$this->objDiseaseSpeciesNumber = $this->getObject('diseasespeciesnumber');
			$this->objDiseaseControlMeasure = $this->getObject('diseasecontrolmeasure');
			

			$this->objAnimaldeworming = $this->getObject('animaldeworming');
            $this->objAnimalvaccine = $this->getObject('animalvaccine');
			
			
			$this->objExchangerate = $this->getObject('exchangerate');
			$this->objExchangeratedetail = $this->getObject('exchangeratedetails');
			$this->objInfectionsources = $this->getObject('infectionsources');
			$this->objControlmeasures = $this->getObject('controlmeasures');
			$this->objSpeciesNew = $this->getObject('speciesnew');
			$this->objSpeciescategories = $this->getObject('speciescategories');
			$this->objAgents = $this->getObject('agents');
			$this->objDiseasespecies = $this->getObject('diseasespecies');
			
			$this->objPartition = $this->getObject('partitions');
			$this->objPartitionLevel = $this->getObject('partitionlevel');
			$this->objPartitionCategory = $this->getObject('partitioncategory');
			$this->objFarmingSystem = $this->getObject('farmingsystem');
			$this->objSpeciesType = $this->getObject('speciestype');          
			$this->objSpeciesAgeGroup = $this->getObject('speciesagegroup');	
			$this->objSpeciesTropicalLivestockUnit = $this->getObject('speciestropicallivestockunit');	
			$this->objOccurenceCode = $this->getObject('occurencecode');
			$this->objDiseaseAgent = $this->getObject('diseaseagent');	
                       
            								
		  $this->adminActions = array('admin', 'employee_admin', 'geography_level3_admin',
                                        'age_group_admin', 'title_admin', 'sex_admin', 'status_admin',
                                        'geography_level2_admin', 'prodution_admin', 'territory_admin',
                                        'report_admin', 'quality_admin', 'diagnosis_admin',
                                        'control_admin', 'outbreak_admin', 'geography_level3_delete',
                                        'geography_level3_add', 'geography_level3_insert', 'create_territory',
                                        'territory_insert', 'employee_admin', 'employee_insert', 'create_employee',
                                        'production_admin', 'production_add', 'production_insert', 'production_delete',
                                        'title_admin', 'title_add', 'title_insert', 'title_delete',
                                        'status_admin', 'status_add', 'sex_insert', 'sex_delete',
                                        'sex_admin', 'sex_add', 'sex_insert', 'sex_delete',
                                        'outbreak_admin', 'outbreak_add', 'outbreak_insert', 'outbreak_delete',
                                        'control_admin', 'control_add', 'control_insert', 'control_delete',
                                        'quality_admin', 'quality_add', 'quality_insert', 'quality_delete',
                                        'age_add', 'age_admin', 'age_insert', 'age_delete',
										'causative_add', 'causative_admin', 'causative_insert', 'causative_delete',
                                        'role_add', 'role_admin', 'role_insert', 'role_delete',
                                        'department_add', 'department_admin', 'department_insert', 'department_delete',
                                        'report_add', 'report_admin', 'report_insert', 'report_delete','disease_admin',
                                        'test_admin','testresult_admin','sample_admin','species_admin','breed_admin',
                                        'survey_admin','farmingsystem_admin','vaccinationhistory_admin','disease_add',
                                        'disease_insert','disease_delete','test_add','test_insert','disease_delete',
                                        'testresult_add','testresult_insert','testresult_delete','sample_add','sample_insert',
                                        'sample_delete','species_add','species_insert','species_delete','survey_add',
                                        'survey_insert','survey_delete','farmingsystem_add','farmingsystem_insert',
                                        'farmingsystem_delete','vaccinationhistory_add','vaccinationhistory_insert',
                                        'vaccinationhistory_delete','animalproduction_admin','animalproduction_add',
                                        'animalproduction_delete','add_language','language_save','country_add','country_save','currency_add','currency_save','unit_of_area_add','units_of_area_save','language_admin','country_admin','unit_of_area_admin','currency_admin','edit_language','language_update','country_admin','edit_country','country_update','country_delete','currency_admin','currency_edit','currency_update','currency_delete','unit_of_area_edit','unit_of_area_update','unit_of_area_delete','locality_type_add','locality_type_save','locality_type_admin','locality_type_edit','locality_type_delete','locality_type_update','diagnostic_method_add','diagnostic_method_save','diagnostic_method_admin','diagnostic_method_edit','diagnostic_method_delete','other_control_measures_add','other_control_measures_save','other_control_measures_admin','other_control_measures_edit','other_control_measures_delete','species_names_add','species_names_save','species_names_admin','species_names_edit','species_names_delete','species_names_update','species_economic_function_add','species_economic_function_save','species_economic_function_admin','species_economic_function_edit','species_economic_function_delete','diseases_add','diseases_save','diseases_admin','diseases_edit','diseases_delete','exchangerate_admin', 'exchangeratedetail_admin', 'exchangerate_delete', 'exchangeratedetails_delete', 'infectionsources_admin', 'infectionsources_delete', 'controlmeasures_admin', 'controlmeasures_delete', 'speciesnew_admin', 'speciesnew_delete', 'speciesategories_admin', 'speciescategories_delete',	'agents_admin', 'agents_delete','partition_add','partitioncategory_add','partitionlevel_add','farmingsystem_add','speciestypeadd','speciesagegroup_add','speciestropicallivestockunit_add');
										
        }
        catch(customException $e) {
        	customException::cleanUp();
        	exit;
        }
    }
    
    /**
     * Standard Chisimba dispatch method for parsing the querystring
     * 
     * @param  string $action The REQUEST string for action
     * @return void   
     * @access public 
     */
    public function dispatch($action = NULL) {
        $this->objTools = $this->getObject('tools','toolbar');
        
                
        if (!$this->objUser->isLoggedIn() && $action != 'jrupload') {
            $this->objTools->addToBreadCrumbs(array($this->objLanguage->languageText('word_login')));
            return 'login_tpl.php';
        }
        
        if (in_array($action, $this->adminActions) && !$this->objUser->isAdmin()) {
            $this->setVar('message', $this->objLanguage->languageText('mod_ahis_notadmin','openaris'));
            $this->setVar('location', $this->uri(array('action'=>'select_officer')));
            return 'redirect_tpl.php';
        }
        
        if (strstr($action, 'passive')) {
            $this->objTools->addToBreadCrumbs(array($this->objLanguage->languageText('phrase_passiveentry'),
                                                    $action));
        }
        
        switch ($action) {
			
			case 'tmp':
				echo $this->objPassive->nextRefNo();
				die;
				
			case 'jrupload':
				log_debug('GOT JAVAROSA DATA');
				$xmlString = file_get_contents("php://input");
				/*$xmlString = '<?xml version="1.0" ?><passive_outbreak><reporting_officer>Administrative user</reporting_officer><geo2>western cape</geo2><report_date>2009-07-08</report_date><outbreak_status>init_01</outbreak_status><prepared_date>2009-07-08</prepared_date><ibar_date>2009-07-08</ibar_date><dvs_date>2009-07-08</dvs_date><is_date>2009-07-08</is_date><tested_for_quality>init_02</tested_for_quality><remarks>AJTPGM</remarks><vet_date>2009-07-08</vet_date><occurence_date>2009-07-08</occurence_date><diagnosis_date>2009-07-08</diagnosis_date><investigation_date>2009-07-08</investigation_date><location>cape town</location><latitude><degrees>21</degrees><minutes>18.5</minutes><direction>N</direction></latitude><longitude><degrees>25</degrees><minutes>45.74</minutes><direction>E</direction></longitude><disease>init_06</disease><causative>init_04</causative><species>init_04</species><age_group>init_01</age_group><sex>init_03</sex><production_type>init_05</production_type><control_measure>init_01</control_measure><basis_of_diagnosis>init_01</basis_of_diagnosis><susceptible>2</susceptible><cases>2</cases><deaths>2</deaths><vaccinated>2</vaccinated><slaughtered>2</slaughtered><destroyed>2</destroyed><production>2</production><new_cases>2</new_cases><recovered>2</recovered><prophylactic>3</prophylactic><vaccine_source>JT</vaccine_source><vaccine_batch>.B</vaccine_batch><vaccine_manufacture_date>2009-07-08</vaccine_manufacture_date><vaccine_expire_date>2009-07-08</vaccine_expire_date><panvac_tested>1</panvac_tested></passive_outbreak>';*/
				$result = $this->objPassive->insertDataFromJavaRosa($xmlString);
				switch ($result) {
					case 0: echo "Data Submitted Successfully.";
							break;
					case 1: echo "Error: The specified Reporting Officer doesn't exist. Please contact an administrator.";
							break;
					case 2: echo "Error: The specified Geo Level 2 doesn't exist. Please contact and administrator.";
							break;
					case 3: echo "Error: The specified Location doesn't exist. Please contact and administrator.";
							break;
					default: echo "Error: There was a problem submitting the data. Please contact and administrator.";
							break;
						
				}
				break;
			
			case 'unset':
				$this->unsetPassiveSession();
				$this->unsetActiveSession();
				return $this->nextAction('select_officer');
				
			case 'home':
				return 'home_tpl.php';
				
        	case 'select_officer':
				$this->setVar('reportType', $this->getParam('reportType'));
                return 'select_officer_tpl.php';
            
            case 'report_filter':
                $reportType = $this->getParam('reportType');
				switch ($reportType) {
                    case "init_01":
                        return $this->nextAction('passive_surveillance');
					case "init_02":
                        return $this->nextAction('animal_population_add');
					case "init_03":
                        return $this->nextAction('addinspectiondata');
					case "init_04":
                        return $this->nextAction('animal_slaughter_add');
                    case "init_06":
						return $this->nextAction('animalmovement_add');
					case "init_07":
						return $this->nextAction('livestockimport_add');
					case "init_08":
						return $this->nextAction('livestockexport_add');
					case "init_09":
                        return $this->nextAction('animaldeworming_add');
                    case "init_10":
                        return $this->nextAction('vacinventory');
					case "init_05":
					default:
                        return $this->nextAction('active_surveillance');
                }
            
            case 'passive_surveillance':
                $this->setVar('arrayCountry', $this->objCountry->getAll("ORDER BY common_name"));
				    $partitionCategories = $this->objPartitionCategory->getAll("ORDER BY partitioncategory");
                $this->setVar('arrayAdmin1', $partitionCategories);
                $this->setVar('arrayROfficer', $this->objAhisUser->getListByRole('init_01'));
                $this->setVar('arrayDEOfficer', $this->objAhisUser->getListByRole('init_02'));
                $this->setVar('arrayVOfficer', $this->objAhisUser->getListByRole('init_03'));
                $this->setVar('arrayDisease', $this->objDiseases->getAll('ORDER BY short_name'));
                $this->setVar('arrayOccurence', $this->objOccurenceCode->getAll("ORDER BY occurencecode"));
                $this->setVar('arrayInfection', $this->objInfectionsources->getAll("ORDER BY possiblesource"));
                $this->setVar('arrayOutbreak', $this->objDiseaseReport->getAll("ORDER BY outbreakcode"));
                
                $this->setVar('countryId', $this->getSession('ps_countryId'));
				    $defaultCategory = current($partitionCategories);
                $partitionTypeId = $this->getSession('ps_partitionTypeId', $defaultCategory['id']);
				    $this->setVar('partitionTypeId', $partitionTypeId);
                $this->setVar('arrayAdmin2', $this->objPartitionLevel->getAll("WHERE partitioncategoryid = '$partitionTypeId' ORDER BY partitionlevel"));
                $partitionLevelId = $this->getSession('ps_partitionLevelId');
				    $this->setVar('partitionLevelId', $partitionLevelId);
                $this->setVar('arrayAdmin3', $this->objPartition->getAll("ORDER BY partitionname"));
                $this->setVar('partitionLevelId', $this->getSession('ps_partitionLevelId'));
                $this->setVar('partitionId', $this->getSession('ps_partitionId'));
                $this->setVar('datePrepared', $this->getSession('ps_datePrepared', date('Y-m-d')));
                $this->setVar('dateIBARSub', $this->getSession('ps_dateIBARSub', date('Y-m-d')));
                $this->setVar('dateIBARRec', $this->getSession('ps_dateIBARRec', date('Y-m-d')));
                $this->setVar('reportOfficerId', $this->getSession('ps_reportOfficerId'));
                $this->setVar('dataEntryOfficerId', $this->getSession('ps_reportOfficerId'));
                $this->setVar('valOfficerId', $this->getSession('ps_reportOfficerId'));
                
				$this->setVar('reportOfficerFax', '');
				$this->setVar('reportOfficerTel', '');
				$this->setVar('reportOfficerEmail', '');
				$this->setVar('dataEntryOfficerFax', '');
				$this->setVar('dataEntryOfficerTel', '');
				$this->setVar('dataEntryOfficerEmail', '');
				$this->setVar('valOfficerFax', '');
				$this->setVar('valOfficerTel', '');
				$this->setVar('valOfficerEmail', '');
				
				$this->setVar('outbreakReported', $this->getSession('ps_outbreakReported'));
				$this->setVar('validated', $this->getSession('ps_validated'));
				$this->setVar('comment', $this->getSession('ps_comment'));
				
				$this->setVar('createdBy', $this->objUser->username($this->getSession('createdById')));
				$this->setVar('createdDate', $this->getSession('createdDate', date('Y-m-d')));
				$this->setVar('modifiedBy', '');
				$this->setVar('modifiedDate', $this->getSession('modifiedDate', ''));
				
				$this->setVar('reportTypeId', $this->getSession('ps_reportTypeId'));
				$this->setVar('outbreakCode', $this->getSession('ps_outbreakCode'));
				$this->setVar('diseaseId', $this->getSession('ps_diseaseId'));
				$this->setVar('occurenceId', $this->getSession('ps_occurenceId'));
				$this->setVar('infectionId', $this->getSession('ps_infectionId'));
				
				$this->setVar('observationDate', $this->getSession('ps_observationDate', date('Y-m-d')));
                $this->setVar('vetDate', $this->getSession('ps_vetDate', date('Y-m-d')));
                $this->setVar('investigationDate', $this->getSession('ps_investigationDate', date('Y-m-d')));
                $this->setVar('sampleDate', $this->getSession('ps_sampleDate', date('Y-m-d')));
                $this->setVar('diagnosisDate', $this->getSession('ps_diagnosisDate', date('Y-m-d')));
                $this->setVar('interventionDate', $this->getSession('ps_interventionDate', date('Y-m-d')));
                
				$this->setVar('outbreakCode', '');
				

                return "passive_surveillance_tpl.php";
			
			case "ajax_getofficerinfo":
				$userId = $this->getParam('userid');
				$infos 	= $this->objAhisUser->getUserContact($userId);
				echo json_encode(current($infos));
				break;
			
			case "ajax_getpartitionlevels":
				$categoryId = $this->getParam('categoryId');
				echo json_encode($this->objPartitionLevel->getLevels($categoryId));
				break;
			
			case "ajax_getpartitionnames":
				$countryId 	= $this->getParam('countryId');
				$levelId 	= $this->getParam('levelId');
				echo json_encode($this->objPartition->getNames($countryId, $levelId));
				break;
			
			case "ajax_getoutbreakcode":
				$countryId 	= $this->getParam('countryId');
				$diseaseId 	= $this->getParam('diseaseId');
				$year 		= $this->getParam('year');
				echo json_encode(array('code'=>$this->objDiseaseReport->genOutbreakCode($countryId, $diseaseId, $year)));
				break;
			case "ajax_getoutbreakcountry":
				$countryId 	= $this->getParam('countryId');
				echo json_encode($this->objDiseaseReport->genOutbreakCountry($countryId));
				break;	
			case "ajax_getdisease":
				$outbreakCode = $this->getParam('outbreakcode');
				echo json_encode($this->objDiseases->getRow('id', $this->objDiseaseReport->getDiseaseId($outbreakCode)));
			  	break;
			case "ajax_getspecies":
				$outbreakCode = $this->getParam('outbreakCode');
				echo json_encode($this->objDiseaseSpeciesNumber->getSpecies($outbreakCode));
			  	break;   
			case "ajax_getvalues":
			   $filter = $this->getParam('filter');
			   $val = $this->getParam('condprovac',0);
			   $district = $this->getSession('ps_admin3');
			   $month = $this->getSession('ps_month');
			   $year = $this->getSession('ps_year');
			   echo json_encode(array('cumvac'=>$this->objVacinventory->getData($month,$year,$val,$filter,$district)));
			   break;
			case "ajax_valdirection":
			   $filter = $this->getParam('filter');
			   $countryId = $this->getParam('countryId',0);
			   $direction = $this->getParam('direction',0);
			   $valid = $this->objCountry->getData($filter,$direction,$countryId);
			    echo json_encode(current($valid));
			   break;      
			case 'save_disease_1':
				$outbreakCode = $this->getParam('outbreakCode');
				$countryId = $this->getParam('countryId', $this->getSession('ps_countryId'));
                $partitionTypeId = $this->getParam('partitionTypeId', $this->getSession('ps_partitionTypeId'));
                $partitionLevelId = $this->getParam('partitionLevelId', $this->getSession('ps_partitionLevelId'));
                $partitionId = $this->getParam('partitionId', $this->getSession('ps_partitionId'));
                $month = $this->getParam('month', $this->getSession('ps_month'));
                $year = $this->getParam('year', $this->getSession('ps_year'));
                $datePrepared = $this->getParam('datePrepared', $this->getSession('ps_datePrepared'));
                $dateIBARSub = $this->getParam('dateIBARSub', $this->getSession('ps_dateIBARSub'));
                $dateIBARRec = $this->getParam('dateIBARRec', $this->getSession('ps_dateIBARRec'));
                $reportOfficerId = $this->getParam('reportOfficerId', $this->getSession('ps_reportOfficerId'));
                $dataEntryOfficerId = $this->getParam('dataEntryOfficerId', $this->getSession('ps_dataEntryOfficerId'));
                $valOfficerId = $this->getParam('valOfficerId', $this->getSession('ps_valOfficerId'));
                $outbreak = $this->getParam('outbreak', $this->getSession('ps_outbreak'));
                $validated = $this->getParam('validated', $this->getSession('ps_validated'));
                $comments = $this->getParam('comments', $this->getSession('ps_comments'));
                if ($outbreak == 0) {
					//save zero report
					return $this->nextAction('passive_feedback', array('success'=>1));
				}
				$reportTypeId = $this->getParam('reportTypeId', $this->getSession('ps_reportTypeId'));
                $outbreakId = $this->getParam('outbreakId', $this->getSession('ps_outbreakId'));
                $diseaseId = $this->getParam('diseaseId', $this->getSession('ps_diseaseId'));
                $occurenceId = $this->getParam('occurenceId', $this->getSession('ps_occurenceId'));
                $infectionId = $this->getParam('infectionId', $this->getSession('ps_infectionId'));
                $observationDate = $this->getParam('observationDate', $this->getSession('ps_observationDate'));
                $vetDate = $this->getParam('vetDate', $this->getSession('ps_vetDate'));
                $investigationDate = $this->getParam('investigationDate', $this->getSession('ps_investigationDate'));
                $sampleDate = $this->getParam('sampleDate', $this->getSession('ps_sampleDate'));
                $diagnosisDate = $this->getParam('diagnosisDate', $this->getSession('ps_diagnosisDate'));
                $interventionDate = $this->getParam('interventionDate', $this->getSession('ps_interventionDate'));
				if ($this->getParam('reportTypeId') == 0) {
					$insert_array = array('reporterid' => $reportOfficerId,
										  'dataentryid' => $dataEntryOfficerId,
										  'validaterid' => $valOfficerId,
										  'countryid' => $countryId,
										  'partitionid' => $partitionId,
										  'reportdate' => date('Y-m-d', mktime(0,0,0,$month,1,$year)),
										  'prepareddate' => $datePrepared,
										  'ibarsubdate' => $dateIBARSub,
										  'ibarrecdate' => $dateIBARRec,
										  'validated' => $validated,
										  'comments' => $comments,
										  'outbreakcode' => $outbreakCode,
										  'diseaseid' => $diseaseId,
										  'occurenceid' => $occurenceId,
										  'infectionid' => $infectionId,
										  'observationdate' => $observationDate,
										  'vetdate' => $vetDate,
										  'investigationdate' => $investigationDate,
										  'samplesubdate' => $sampleDate,
										  'diagnosisdate' => $diagnosisDate,
										  'interventiondate' => $interventionDate,
										  'date_created' => date('Y-m-d'),
										  'created_by' => $this->objUser->userId());;
					$this->objDiseaseReport->insert($insert_array);
				} else {
					$outbreakId = $this->getParam('outbreakId');
					$report = $this->objDiseaseReport->getRow('id', $outbreakId);
					$outbreakCode = $report['outbreakcode'];
					$modify_array = array('observationdate' => $observationDate,
										  'vetdate' => $vetDate,
										  'investigationdate' => $investigationDate,
										  'samplesubdate' => $sampleDate,
										  'diagnosisdate' => $diagnosisDate,
										  'interventiondate' => $interventionDate,
										  'date_modified' => date('Y-m-d'),
										  'modified_by' => $this->objUser->userId());
					$this->objDiseaseReport->update('id', $report['id'], $modify_array);
				}
					
                
				$this->setSession('ps_countryId', $countryId);
				$this->setSession('ps_partitionTypeId', $partitionTypeId);
				$this->setSession('ps_partitionLevelId', $partitionLevelId);
				$this->setSession('ps_partitionNameId', $partitionId);
				$this->setSession('ps_month', $month);
				$this->setSession('ps_year', $year);
				$this->setSession('ps_datePrepared', $datePrepared);
				$this->setSession('ps_dateIBARSub', $dateIBARSub);
				$this->setSession('ps_dateIBARRec', $dateIBARRec);
				$this->setSession('ps_reportOfficerId', $reportOfficerId);
				$this->setSession('ps_dataEntryOfficerId', $dataEntryOfficerId);
				$this->setSession('ps_valOfficerId', $valOfficerId);
				$this->setSession('ps_outbreak', $outbreak);
				$this->setSession('ps_validated', $validated);
				$this->setSession('ps_comments', $comments);
				$this->setSession('ps_reportTypeId', $reportTypeId);
				$this->setSession('ps_outbreakId', $outbreakId);
				$this->setSession('ps_diseaseId', $diseaseId);
				$this->setSession('ps_occurenceId', $occurenceId);
				$this->setSession('ps_infectionId', $infectionId);
				$this->setSession('ps_observationDate', $observationDate);
				$this->setSession('ps_vetDate', $vetDate);
				$this->setSession('ps_investigationDate', $investigationDate);
				$this->setSession('ps_sampleDate', $sampleDate);
				$this->setSession('ps_diagnosisDate', $diagnosisDate);
				$this->setSession('ps_interventionDate', $interventionDate);
				$this->setSession('ps_outbreakCode',$outbreakCode);
				
				
				return $this->nextAction('disease_report_screen_2', array('outbreakCode'=>$outbreakCode));
				
			case "disease_report_screen_2":
				$outbreakCode = $this->getSession('ps_outbreakCode');
                $outbreakCode1 = $this->getParam('outbreakCode1', $outbreakCode);
				$this->setVar('outbreakCode1', $outbreakCode1);
				$this->setVar('outbreakCode',$outbreakCode);
				$this->setVar('numloc', $this->objDiseaseLocality->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));
				$country = $this->getSession('ps_countryId');
				$this->setVar('countryId', $country);
				$this->setVar('arrayLocalityType', $this->objLocalityType->getAll("ORDER BY locality_type"));
				$this->setVar('arrayFarmingSystem', $this->objFarmingSystem->getAll("ORDER BY farmingsystem"));
				$this->setVar('outbreaks', $this->objDiseaseReport->getOutbreaks($country));
                $this->setVar('diseaseLocalities', $this->objDiseaseLocality->getAll("WHERE outbreakcode = '$outbreakCode1' ORDER BY date_created"));
				$this->setVar('localityTypeId', $this->getSession('ps_localityTypeId'));
				$this->setVar('localityName', $this->getSession('ps_localityName'));
				$this->setVar('latitude', $this->getSession('ps_latitude'));
				$this->setVar('longitude', $this->getSession('ps_longitude'));
				$this->setVar('latDirec', $this->getSession('ps_latDirec'));
				$this->setVar('longDirec', $this->getSession('ps_longDirec'));
				$this->setVar('farmingSystemId', $this->getSession('farmingSystemId'));
				$this->setVar('createdBy', $this->objUser->username($this->getSession('createdById')));
				$this->setVar('createdDate', $this->getSession('createdDate', date('Y-m-d')));
				$this->setVar('modifiedBy', '');
				$this->setVar('modifiedDate', $this->getSession('modifiedDate', ''));
				return 'disease_report_2_tpl.php';
            
			case "add_diseaselocality":
				$outbreakCode = $this->getParam('outbreakCode');
				$insert_array = array('outbreakcode' => $outbreakCode,
								 'localitytypeid' => $this->getParam('localityTypeId'),
								 'name' => $this->getParam('localityName'),
								 'latitude' => $this->getParam('lattitude'),
								 'latdirection' => $this->getParam('latDirec'),
								 'longitude' => $this->getParam('longitude'),
								 'longdirection' => $this->getParam('longDirec'),
								 'farmingsystemid' => $this->getParam('farmingSystemId'),
								 'date_created' => date('Y-m-d'),
								 'created_by' => $this->objUser->userId()
								 );
				$this->objDiseaseLocality->insert($insert_array);
				return $this->nextAction('disease_report_screen_2', array('outbreakCode'=>$outbreakCode));
			
            case 'disease_report_screen_3':
            $outbreakCode1 = $this->getParam('outbreakCode1');
            $outbreakCode = $this->getSession('ps_outbreakCode');
				$this->setVar('outbreakCode1', $outbreakCode1);
				$this->setVar('outbreakCode',$outbreakCode);
				$this->setVar('numdiag', $this->objDiseaseDiagnosis->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));				
            if(!empty($outbreakCode1)){
				$this->setVar('diagnoses', $this->objDiseaseDiagnosis->getAll("WHERE outbreakcode = '$outbreakCode1' ORDER BY date_created"));
				}else{
				$this->setVar('diagnoses', $this->objDiseaseDiagnosis->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));
				}
				$country =$this->getSession('ps_countryId');

				$this->setVar('outbreaks', $this->objDiseaseReport->getOutbreaks($country));
				$this->setVar('arrayNatureOfDiagnosis', $this->objDiagnosticMethod->getAll("ORDER BY diagnostic_method"));
				$this->setVar('diagnosisId', $this->getSession('ps_diagnosisId'));
				$this->setVar('createdBy', $this->objUser->username($this->getSession('createdById')));
				$this->setVar('createdDate', $this->getSession('createdDate', date('Y-m-d')));
				$this->setVar('modifiedBy', '');
				$this->setVar('modifiedDate', $this->getSession('modifiedDate', ''));
				
                return 'disease_report_3_tpl.php';
            
			case "add_diseasediagnosis":
				$outbreakCode = $this->getSession('ps_outbreakCode');
				$insert_array = array('outbreakcode' => $outbreakCode,
								 'diagnosticmethodid' => $this->getParam('diagnosisId'),
								 'date_created' => date('Y-m-d'),
								 'created_by' => $this->objUser->userId()
								 );//print_r($insert_array);
				$this->objDiseaseDiagnosis->insert($insert_array);
				return $this->nextAction('disease_report_screen_3', array('outbreakCode'=>$outbreakCode));
			
            case 'disease_report_screen_4':
				$outbreakCode1 = $this->getParam('outbreakCode1');
				$outbreakCode = $this->getSession('ps_outbreakCode');
				$this->setVar('outbreakCode1', $outbreakCode1);
				$this->setVar('numspecies', $this->objDiseaseSpeciesNumber->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));
				if(!empty($outbreakCode1)) {
					$this->setVar('diseaseSpeciesNumber', $this->objDiseaseSpeciesNumber->getAll("WHERE outbreakcode = '$outbreakCode1' ORDER BY date_created"));
				} else {
					$this->setVar('diseaseSpeciesNumber', $this->objDiseaseSpeciesNumber->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));
				}
				$report = $this->objDiseaseReport->getRow('outbreakcode', $outbreakCode);

				$this->setVar('outbreakCode', $outbreakCode);
				$country = $this->getSession('ps_countryId');
				$diseaseId = $this->objDiseaseReport->getDiseaseId($outbreakCode);
				$this->setVar('outbreaks', $this->objDiseaseReport->getOutbreaks($country));
				$this->setVar('arraySpecies', $this->objDiseasespecies->getSpecies($diseaseId));
				$this->setVar('arrayAgeGroup', $this->objSpeciesAgeGroup->getUniqueGroups());
				$this->setVar('arraySex', $this->objSex->getAll("ORDER BY name"));
				
				$this->setVar('risk', $this->getSession('ps_risk'));
				$this->setVar('cases', $this->getSession('ps_cases'));
				$this->setVar('deaths', $this->getSession('ps_deaths'));
				$this->setVar('destroyed', $this->getSession('ps_destroyed'));
				$this->setVar('slaughtered', $this->getSession('ps_slaughtered'));
				$this->setVar('cumulativeCases', $this->objDiseaseSpeciesNumber->getCases($report['diseaseid']));
				$this->setVar('cumulativeDeaths', $this->objDiseaseSpeciesNumber->getDeaths($report['diseaseid']));
				$this->setVar('cumulativeDestroyed', $this->objDiseaseSpeciesNumber->getDestroyed($report['diseaseid']));
				$this->setVar('cumulativeSlaughtered', $this->objDiseaseSpeciesNumber->getSlaughtered($report['diseaseid']));

				$this->setVar('speciesId', $this->getSession('ps_speciesId'));
				$this->setVar('ageId', $this->getSession('ps_ageId'));
				$this->setVar('sexId', $this->getSession('ps_sexId'));
				$this->setVar('createdBy', $this->objUser->username($this->getSession('createdById')));
				$this->setVar('createdDate', $this->getSession('createdDate', date('Y-m-d')));
				$this->setVar('modifiedBy', '');
				$this->setVar('modifiedDate', $this->getSession('modifiedDate', ''));
				
                return 'disease_report_4_tpl.php';
			
			case "add_diseasespeciesnumber":
				$outbreakCode = $this->getParam('outbreakCode');
				$insert_array = array('outbreakcode' => $outbreakCode,
								 'speciesid' => $this->getParam('speciesId'),
								 'agegroupid' => $this->getParam('ageId'),
								 'sexid' => $this->getParam('sexId'),
								 'risk' => $this->getParam('risk'),
								 'cases' => $this->getParam('cases'),
								 'deaths' => $this->getParam('deaths'),
								 'destroyed' => $this->getParam('destroyed'),
								 'slaughtered' => $this->getParam('slaughtered'),
								 'date_created' => date('Y-m-d'),
								 'created_by' => $this->objUser->userId()
								 );
				$this->objDiseaseSpeciesNumber->insert($insert_array);
				return $this->nextAction('disease_report_screen_4', array('outbreakCode'=>$outbreakCode));
			
            case 'disease_report_screen_5':
            $outbreakCode1 = $this->getParam('outbreakCode1');
				$outbreakCode = $this->getSession('ps_outbreakCode');
				$this->setVar('outbreakCode1', $outbreakCode1);
			   $this->setVar('outbreakCode', $outbreakCode);
				$this->setVar('numcontrol', $this->objDiseaseControlMeasure->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));
            if(!empty($outbreakCode1)){
				$this->setVar('diseaseControlMeasures', $this->objDiseaseControlMeasure->getAll("WHERE outbreakcode = '$outbreakCode1' ORDER BY date_created"));
				}else{
				$this->setVar('diseaseControlMeasures', $this->objDiseaseControlMeasure->getAll("WHERE outbreakcode = '$outbreakCode' ORDER BY date_created"));
				}

				$country =$this->getSession('ps_countryId');			
				$this->setVar('outbreaks', $this->objDiseaseReport->getOutbreaks($country));
				$this->setVar('arrayControlMeasure', $this->objControlmeasures->getAll("ORDER BY controlmeasure"));
				$this->setVar('arrayOtherMeasure', $this->objOtherControlMeasures->getAll("ORDER BY control_measure"));
				$this->setVar('controlId', $this->getSession('ps_controlId'));
				$this->setVar('otherId', $this->getSession('ps_otherId'));
				$this->setVar('createdBy', $this->objUser->username($this->getSession('createdById')));
				$this->setVar('createdDate', $this->getSession('createdDate', date('Y-m-d')));
				$this->setVar('modifiedBy', '');
				$this->setVar('modifiedDate', $this->getSession('modifiedDate', ''));
				
                return 'disease_report_5_tpl.php';
            
			case "add_diseasecontrolmeasure":
				$outbreakCode = $this->getParam('outbreakCode');
				$insert_array = array('outbreakcode' => $outbreakCode,
								 'controlmeasureid' => $this->getParam('controlId'),
								 'othermeasureid' => $this->getParam('otherControlId'),
								 'date_created' => date('Y-m-d'),
								 'created_by' => $this->objUser->userId()
								 );
								// print_r($insert_array);
				$this->objDiseaseControlMeasure->insert($insert_array);
				return $this->nextAction('disease_report_screen_5', array('outbreakCode'=>$outbreakCode));
			
           
			case 'disease_report_screen_6':
				$this->setVar('outbreakCode', $this->getParam('outbreakCode'));
				return 'disease_report_6_tpl.php';
			
            case 'passive_save':
                $ps_array['reporterid'] = $this->getSession('ps_officerId');
                $ps_array['geo2id'] = $this->getSession('ps_geo2Id');
                $ps_array['reportdate'] = $this->getSession('ps_calendardate');
                $ps_array['refno'] = $this->getSession('ps_refNo');
                
                $ps_array['statusid'] = $this->getSession('ps_oStatusId');
                $ps_array['qualityid'] = $this->getSession('ps_qualityId');
                $ps_array['prepareddate'] = $this->getSession('ps_datePrepared', date('Y-m-d'));
                $ps_array['ibardate'] = $this->getSession('ps_dateIBAR', date('Y-m-d'));
                $ps_array['dvsdate'] = $this->getSession('ps_dateReceived', date('Y-m-d'));
                $ps_array['reporteddate'] = $this->getSession('ps_dateIsReported', date('Y-m-d'));
                $ps_array['remarks'] = $this->getSession('ps_remarks');
                
                $ps_array['vetdate'] = $this->getSession('ps_dateVet', date('Y-m-d'));
                $ps_array['occurencedate'] = $this->getSession('ps_dateOccurence', date('Y-m-d'));
                $ps_array['diagnosisdate'] = $this->getSession('ps_dateDiagnosis', date('Y-m-d'));
                $ps_array['investigationdate'] = $this->getSession('ps_dateInvestigation', date('Y-m-d'));
                $ps_array['latdeg'] = $this->getSession('ps_latdeg');
                $ps_array['longdeg'] = $this->getSession('ps_longdeg');
                $ps_array['latmin'] = $this->getSession('ps_latmin');
                $ps_array['longmin'] = $this->getSession('ps_longmin');
                $ps_array['latdirec'] = $this->getSession('ps_latdirec');
                $ps_array['longdirec'] = $this->getSession('ps_longdirec');
                
                $ps_array['locationid'] = $this->getSession('ps_locationId');
                $ps_array['diseaseid'] = $this->getSession('ps_diseaseId');
                $ps_array['causativeid'] = $this->getSession('ps_causativeId');
                $ps_array['speciesid'] = $this->getSession('ps_speciesId');
                $ps_array['ageid'] = $this->getSession('ps_ageId');
                $ps_array['sexid'] = $this->getSession('ps_sexId');
                $ps_array['productionid'] = $this->getSession('ps_productionId');
                $ps_array['controlmeasureid'] = $this->getSession('ps_controlId');
                $ps_array['basisofdiagnosisid'] = $this->getSession('ps_basisId');
                
                $ps_array['susceptible'] = $this->getSession('ps_susceptible');
                $ps_array['cases'] = $this->getSession('ps_cases');
                $ps_array['deaths'] = $this->getSession('ps_deaths');
                $ps_array['vaccinated'] = $this->getSession('ps_vaccinated');
                $ps_array['slaughtered'] = $this->getSession('ps_slaughtered');
                $ps_array['destroyed'] = $this->getSession('ps_destroyed');
                $ps_array['production'] = $this->getSession('ps_production');
                $ps_array['newcases'] = $this->getSession('ps_newcases');
                $ps_array['recovered'] = $this->getSession('ps_recovered');
                $ps_array['prophylactic'] = $this->getSession('ps_prophylactic');
                
                $ps_array['vaccinemanufacturedate'] = $this->getParam('dateManufactured');
                $ps_array['vaccineexpirydate'] = $this->getParam('dateExpire');
                $ps_array['vaccinesource'] = $this->getParam('source');
                $ps_array['vaccinebatch'] = $this->getParam('batch');
                $ps_array['vaccinetested'] = ($this->getParam('panvac') == 'on')? true : false;
                
                $result = $this->objPassive->insert($ps_array);
                
                return $this->nextAction('passive_feedback', array('success'=>$result));
            
            case 'passive_feedback':
                $success = $this->getParam('success');
                $this->setVar('success', $success);
                if ($success) {
                    $this->unsetPassiveSession();
                }
                
                return "passive_feedback_tpl.php";
                
            case 'view_reports':
                $outputType = $this->getParam('outputType', 2);
                $reportType = $this->getParam('reportType');
                $month = $this->getParam('month', date('m'));
				$error = $this->getParam('error');
                $year = $this->getParam('year', date('Y'));
                $currentyear = date('Y');
                $currentmonth = date('m');
                
				$reportName = $this->objReport->getRow('id', $reportType);
                $fileName = str_replace(" ", "_", "{$reportName['name']}_$month-$year");
                if($year > $currentyear ) {
					return $this->nextAction('view_reports', array('outputType'=>2,'reportType'=>$reportType,'month'=>$month,'year'=>$currentyear,'error'=>'year'));
				}
				
                switch ($outputType) {
                    case 1:
                        //csv
                        $csv = $this->objViewReport->generateCSV($year, $month, $reportType);
                        header("Content-Type: application/csv"); 
                        header("Content-length: " . sizeof($csv)); 
                        header("Content-Disposition: attachment; filename=$fileName.csv"); 
                        echo $csv;
                        break;
					
                    case 3:
                        //pdf
                        $html = $this->objViewReport->generateReport($year, $month, $reportType, 'true');
                        //$objPDF = $this->getObject('dompdfwrapper','dompdf');
                        //$objPDF->setPaper('a4', 'landscape');
                        //$objPDF->generatePDF($html, "$fileName.pdf");
                        echo "$html <br />not yet implemented";
                        break;
                    
					default:
                        $this->setVar('reportTypes', $this->objReport->getAll("ORDER BY name"));
                        $this->setVar('year', $year);
                        $this->setVar('month', $month);
                        $this->setVar('outputType', $outputType);
                        $this->setVar('reportType', $reportType);
                        $this->setVar('enter', $this->getParam('enter'));
						if ($error) {
							$this->setVar('error', $error);
						}
                        return "view_reports_tpl.php";
                }
				
                break;
			
			case 'gis_reports':
				$report = $this->getParam('report');
				$diseases = $this->objDisease->getAll('ORDER BY name');
				$geo3 = $this->objGeo3->getAll('ORDER BY name');
				$geo2 = $this->objGeo2->getAll('ORDER BY name');
				$species = $this->objSpecies->getAll('ORDER BY name');
				
				$this->setVar('geo2', $geo2);
				$this->setVar('geo3', $geo3);
				$this->setVar('species', $species);
				$this->setVar('diseases', $diseases);
				
				if ($report == 1) {
					$this->setVar('jsonData', $this->objActive->getJSONData());
					return "view_active_gis_reports_tpl.php";
				} else {
					$this->setVar('jsonData', $this->objPassive->getJSONData());
					return "view_gis_reports_tpl.php";
				}
                
            case 'active_surveillance':
               $this->setVar('campName', $this->getSession('ps_campName'));
               //$officerId = $this->getParam('officerId', $this->getSession('ps_officerId'));
               //$this->setSession('ps_officerName',$this->objUser->fullName($officerId));
               $this->setVar('geo2Id', $this->getSession('ps_geo2Id'));
               //$this->setVar('arrayGeo2', $this->objGeo2->getAll("ORDER BY name"));
               $this->setVar('userList', $this->objAhisUser->getList());
               $this->setVar('officerId', $this->getSession('ps_officerId'));
               $this->setVar('arraydisease', $this->objDisease->getAll("ORDER BY NAME"));
               $this->setVar('arraysurvey', $this->objSurvey->getAll("ORDER BY NAME"));
               $this->setVar('disease', $this->getSession('ps_disease'));
               $this->setVar('reportdate',$this->getSession('ps_calendardate',date('Y-m-d')));
               $this->setVar('surveyTypeId', $this->getSession('ps_surveyTypeId'));
               $this->setVar('comments', $this->getSession('ps_comments'));   
               return 'active_surveillance_tpl.php';  
                
            case 'active_addtest':
            
               $campName = $this->getParam('campName', $this->getSession('ps_campName'));
               $officerId = $this->getParam('officerId', $this->getSession('ps_officerId'));
               $geo2levelId = $this->getParam('geo2', $this->getSession('ps_geo2'));
               $disease = $this->getParam('disease', $this->getSession('ps_disease'));
               $surveyTypeId = $this->getParam('surveyTypeId', $this->getSession('ps_surveyTypeId'));
               $comments = $this->getParam('comments', $this->getSession('ps_comments'));
               $reportdate = $this->getParam('reportdate',$this->getSession('ps_reportdate'));
               $this->setSession('ps_campName',$campName);
	            $this->setSession('ps_officerId',$officerId);
	            $this->setSession('ps_disease',$disease);
	            $this->setSession('ps_surveyTypeId',$surveyTypeId);
	            $this->setSession('ps_comments',$comments);
	            $this->setSession('ps_geo2',$geo2levelId);
	            $this->setSession('ps_reportdate',$reportdate);

	            //$data =$this->objActive->getcamp($this->getSession('ps_campName'));
               //$this->setSession('ps_qualityId', $qualityId);
               //$this->setVar('activeid',$data[0]['id']);
               $this->setVar('arraydisease', $this->objDisease->getAll("ORDER BY NAME"));
               $this->setVar('arraytest', $this->objTest->getAll("ORDER BY NAME"));
               $this->setVar('campName', $this->getSession('ps_campName'));
               $this->setVar('disease', $this->getSession('ps_disease'));
               $this->setVar('testtype', $testype);
               return 'active_addtest_tpl.php';
               
            case 'active_insert':
               $campName = $this->getParam('campName', $this->getSession('ps_campName'));
               $officerId = $this->getParam('officerId', $this->getSession('ps_officerId'));
               $disease = $this->getParam('disease', $this->getSession('ps_disease'));
               $surveyTypeId = $this->getParam('surveyTypeId', $this->getSession('ps_surveyTypeId'));
               $comments = $this->getParam('comments', $this->getSession('ps_comments'));
               $geo2level = $this->getParam('geo2',$this->getSession('ps_geo2'));
               $reportdate = $this->getParam('reportdate',$this->getSession('ps_reportdate'));
               $this->setSession('ps_campName',$campName);
	            $this->setSession('ps_officerId',$officerId);
	            $this->setSession('ps_disease',$disease);
	            $this->setSession('ps_surveyTypeId',$surveyTypeId);
	            $this->setSession('ps_comments',$comments);
               $this->setSession('ps_geo2',$geo2level);
               $this->setSession('ps_reportdat',$reportdate);

                $ps_array = array();
                $ps_array['reporterid'] = $this->getSession('ps_officerId');
                $ps_array['campname'] = $this->getSession('ps_campName');
                $ps_array['disease'] =$this->getSession('ps_disease');
                $ps_array['surveytype'] = $this->getSession('ps_surveyTypeId');
                $ps_array['comments'] = $this->getSession('ps_comments');
                $ps_array['sensitivity'] = $this->getParam('sensitivity');
                $ps_array['specificity'] = $this->getParam('specificity');
                $ps_array['testtype'] = $this->getParam('testtype');
               //print_r($this->getSession('ps_officerId'));
                $result = $this->objActive->insert($ps_array);
                                            
                return $this->nextAction('active_addherd');           
                
            
            case 'active_feedback':
                $success = $this->getParam('success');
                $campname = $this->getParam('campname');
                $this->setSession('ps_campName',$campName);
                $this->setVar('success', $success);
                $this->setVar('campaign', $this->getSession('ps_campName'));
                if ($success) {
                    $this->unsetActiveSession();
                } 

                return $this->nextAction('home'); 
                         
                 
           case 'animal_feedback':
              $success = $this->getParam('success');
             if ($success) {
                    $this->unsetAnimalSession();
                } 
                 
             return $this->nextAction('select_officer');

                          
             
            
            case 'active_herddetails':
               $campName = $this->getParam('campName',$this->getSession('ps_campName'));

               $this->setSession('ps_campName', $campName);

               $disease = $this->getParam('disease');             
               $this->setSession('ps_diseases', $disease);
               $this->setVar('arrayCamp', $this->objActive->listcamp());
               $this->setVar('arraydisease', $this->objActive->getallname($this->getSession('ps_campName')));
               $this->setVar('disease', $this->getSession('ps_disease')); 
               $this->setVar('officerId', $this->getSession('ps_officerId'));

               return 'active_herddetails_tpl.php';   
                
            case 'active_addherd':
               
               $this->setVar('userList', $this->objAhisUser->getList());
               $geo2id = $this->getSession('ps_geo2');
               $data = $this->objActive->getallname($this->getSession('ps_campName'));
               $hdata = $this->objNewherd->getherd($data[0]['id']);
               //print_r($hdata);
			      $this->setVar('prompt',$this->getParam('prompt'));
               $this->setVar('id',$this->getParam('id'));
               $this->setVar('hdata',$hdata);
               $this->setVar('activeid',$data[0]['id']);
               $this->setVar('reportdate',$this->getSession('ps_reportdate'));
               $this->setVar('geo2',$this->getSession('ps_geo2'));
               $this->setVar('campName',$this->getSession('ps_campName'));
               $this->setVar('disease',$this->getSession('ps_disease'));
               $this->setVar('reporter',$this->getSession('ps_officerId'));
               $this->setVar('survey',$this->getSession('ps_surveyTypeId'));
               $this->setVar('arrayTerritory', $this->objTerritory->getgeo2($geo2id));
               $this->setVar('arrayFarmingsystem',$this->objProduction->getAll("ORDER BY name"));
               $this->setVar('arraygeo2',$this->objGeo2->getAll("ORDER BY NAME"));
               $this->setVar('arraygeo3',$this->objGeo3->getAll("ORDER BY NAME"));
               return 'active_addherd_tpl.php';
       
       
            case 'newherd_insert':

                $id = $this->getParam('id');
                $this->setSession('ps_activeid',$this->getParam('activeid'));
                $this->setSession('ps_farm',$this->getParam('farm'));
                $this->setSession('ps_farmingsystem',$this->getParam('farmingsystem'));
                $val = $this->objGeo2->getgeo($this->getSession('ps_geo2'));
				    $longdeg = $this->getParam('longdeg');
                $latdeg = $this->getParam('latdeg');
                $longmin = $this->getParam('longmin');
                $latmin = $this->getParam('latmin');
                $longdirec = $this->getParam('longdirection');
                $latdirec = $this->getParam('latdirection');
				    $prompt = $this->getParam('alt');
				   
                $arrayherd = array();
                $arrayherd['territory'] = $this->getParam('territory');
                $arrayherd['geolevel2'] = $val[0]['name'];
                $arrayherd['farmname'] = $this->getSession('ps_farm');
                $arrayherd['farmingtype'] = $this->getSession('ps_farmingsystem');
                $arrayherd['activeid'] = $this->getSession('ps_activeid');
                $arrayherd['longdeg'] = $longdeg;
                $arrayherd['latdeg'] = $latdeg;
                $arrayherd['longmin'] = $longmin;
                $arrayherd['latmin'] = $latmin;
                $arrayherd['longdirec'] = $longdirec;
                $arrayherd['latdirec'] = $latdirec;
				
				if ($id) {
                    $id = $this->objNewherd->update('id', $id, $arrayherd);
                    $code = 3;
                } else {
					$id = $this->objNewherd->insert($arrayherd);  
                    $code = 1;
                }             
				
                return $this->nextAction('active_addherd',array('prompt'=>$prompt));
                
            case 'newherd_delete':
               $id = $this->getParam('id');
               $this->objNewherd->delete('id', $id);
               return $this->nextAction('active_addherd', array('success'=>'2'));
          

            case 'active_sampleview':

               $newherdid = $this->getSession('ps_newherdid');
               $this->setSession('ps_number',$number);
               $data = $this->getSession('ps_newherd');
               $datan= $this->objSampledetails->getall();
               $this->setVar('newherdid',$newherdid);
               $this->setVar('calendardate', $this->getSession('ps_calendardate', date('Y-m-d')));
               $this->setVar('data',$data);
               $this->setVar('datan',$datan);
	            $this->setVar('number',$this->getSession('ps_number'));
	            $this->setVar('i',count($data));
               return 'active_sampleview_tpl.php';
               
            case 'active_addsample':
             
             
               $newherdid = $this->objNewherd->getherd($this->getSession('ps_activeid'));
               $this->setSession('ps_newherdid',$newherdid[0]['id']);
               $this->setSession('ps_newherd',$newherdid);

               $datan= $this->objSampledetails->getall();
               $this->setVar('datan',$datan);
               $this->setVar('prompt',$this->getParam('prompt'));
               $this->setVar('id',$this->getParam('id'));
               $this->setVar('reportdate',$this->getSession('ps_reportdate'));
               $this->setVar('geo2',$this->getSession('ps_geo2'));
               $this->setVar('arraygeo2',$this->objGeo2->getAll("ORDER BY NAME"));
               $this->setVar('userList', $this->objAhisUser->getList());
               $this->setVar('disease',$this->getSession('ps_disease'));
               $this->setVar('reporter',$this->getSession('ps_officerId'));
               $this->setVar('survey',$this->getSession('ps_surveyTypeId'));
               
               $this->setVar('farm',$this->getSession('ps_farm'));
               $this->setVar('farmingsystem',$this->getSession('ps_farmingsystem'));
               $this->setVar('campName', $this->getSession('ps_campName'));
               $this->setVar('newherdid',$this->getSession('ps_newherdid') );

               $this->setVar('newherd',$this->getSession('ps_newherd'));
               $this->setVar('arraySpecies',$this->objSpecies->getAll("ORDER BY NAME"));
               $this->setVar('arrayAge',$this->objAge->getAll("ORDER BY NAME"));
               $this->setVar('arraySex',$this->objSex->getAll("ORDER BY NAME"));
               $this->setVar('arraySample',$this->objSample->getAll("ORDER BY NAME"));
               $this->setVar('arrayTest',$this->objTest->getAll("ORDER BY NAME"));
               $this->setVar('arrayTestresult',$this->objTestresult->getAll("ORDER BY NAME"));
               $this->setVar('arrayVac',$this->objVaccination->getAll("ORDER BY NAME"));
               $this->setVar('calendardate', $this->getSession('ps_calendardate', date('Y-m-d')));
               return 'active_addsample_tpl.php';
               
           case 'sampleview_insert':
                $id = $this->getParam('id');
                $this->setSession('ps_newherdid',$this->getParam('farm'));
                $arrayherd = array();
                $arrayherd['newherdid'] = $this->getParam('newherdid',$this->getParam('farm'));
                $arrayherd['species'] = $this->getParam('species');
                $arrayherd['age'] = $this->getParam('age');
                $arrayherd['sex'] = $this->getParam('sex');
                $arrayherd['sampletype'] = $this->getParam('sampletype');
                $arrayherd['testtype'] = $this->getParam('testtype');
                $arrayherd['testresult'] = $this->getParam('testresult');
                $arrayherd['vachist'] = $this->getParam('vachistory');
                $arrayherd['sampleid'] = $this->getParam('sampleid');
                $arrayherd['animalid'] = $this->getParam('animalid');
                $arrayherd['samplingdate'] = $this->getSession('ps_calendardate', date('Y-m-d'));
                $arrayherd['number'] = $this->getParam('number');
                $arrayherd['remarks'] = $this->getParam('remarks');
                $arrayherd['specification'] = $this->getParam('spec');
                $arrayherd['testdate'] = $this->getParam('calendardate');
               $prompt = $this->getParam('alt');
                //print_r($arrayherd['samplingdate']);exit;
                $this->setSession('ps_data',$arrayherd);
               
                if ($id) {
                    $this->objSampledetails->update('id', $id, $arrayherd);
                    $code = 3;
                } else {
                    $this->objSampledetails->insert($arrayherd); 
                    $code = 1;
                } 
               
                return $this->nextAction('active_addsample', array('prompt'=>$prompt));
                
            case 'sampleview_delete':
               $id = $this->getParam('id');
               $this->objSampledetails->delete('id', $id);
               return $this->nextAction('active_addsample', array('success'=>'2'));

            case 'admin':

               return 'admin_tpl.php';
            
            case 'geography_level3_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objGeo3->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'geography_level3_add')));
                $this->setVar('addLinkText', "addgeo3");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_geo3adminheading','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_name'));
                $this->setVar('deleteAction', 'geography_level3_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('data', $data);
                $this->setVar('searchStr', $searchStr);
                $this->setVar('editAction', 'geography_level3_add');
                $this->setVar('success', $this->getParam('success'));
                $this->setVar('allowEdit', TRUE);
                return 'admin_overview_tpl.php';
            
            case 'geography_level2_admin':
                if ($this->objGeo3->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_nogeo3','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'geography_level3_admin')));
                    return 'redirect_tpl.php';
                }
                $searchStr = $this->getParam('searchStr');
                $data = $this->objGeo2->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'geography_level2_add')));
                $this->setVar('addLinkText', "addgeo2");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_geo2adminheading','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_name'));
                $this->setVar('deleteAction', 'geography_level2_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'geography_level2_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'geography_level3_add':
                $this->setVar('id',$this->getParam('id'));
                return 'geo3_add_tpl.php';
            
            case 'geography_level3_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($id) {
                    $this->objGeo3->update('id', $id, array('name'=>$name));
                    $success = '3';
                } else {
					if ($this->objGeo3->valueExists('name',$name)) {
						return $this->nextAction('geography_level3_admin', array('success'=>'4'));
					}
					$this->objGeo3->insert(array('name'=>$name));
					$success = '1';
				}
				return $this->nextAction('geography_level3_admin', array('success'=>$success));
				
            case 'geography_level3_delete':
                $id = $this->getParam('id');
                $this->objGeo3->delete('id', $id);
                return $this->nextAction('geography_level3_admin', array('success'=>'2'));
            
            case 'geography_level2_add':
                $geo3 = $this->objGeo3->getAll("ORDER BY name");
                $this->setVar('geo3',$geo3);
                $this->setVar('id',$this->getParam('id'));
                return 'geo2_add_tpl.php';
            
            case 'geography_level2_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                $geo3Id = $this->getParam('geo3id');
                
                if ($id) {
                    $this->objGeo2->update('id', $id, array('name'=>$name, 'geo3id' => $geo3Id));
                    $success = '3';
                } else {
                    if ($this->objGeo2->valueExists('name',$name)) {
					    return $this->nextAction('geography_level2_admin', array('success'=>'4'));
					}
					$this->objGeo2->insert(array('name'=>$name, 'geo3id' => $geo3Id));
                    $success = '1';
                }
                return $this->nextAction('geography_level2_admin', array('success'=>$success));
            
            case 'geography_level2_delete':
                $id = $this->getParam('id');
                $this->objGeo2->delete('id', $id);
                return $this->nextAction('geography_level2_admin', array('success'=>'2'));
            
            case 'territory_admin':
				$searchStr = $this->getParam('searchStr');
                $data = $this->objTerritory->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'create_territory')));
                $this->setVar('addLinkText', "addlocation");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_locationadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_name'));
                $this->setVar('deleteAction', 'territory_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'create_territory');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
			
			case 'territory_delete':
				$id = $this->getParam('id');
                $this->objTerritory->delete('id', $id);
                return $this->nextAction('territory_admin', array('success'=>'2'));
				
			case 'create_territory':
                if ($this->objGeo2->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_nogeo2','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'geography_level2_admin')));
                    return 'redirect_tpl.php';
                }
				$this->setVar('id', $this->getParam('id'));
                $geo2 = $this->objGeo2->getAll("ORDER BY name");
                $this->setVar('geo2',$geo2);
                return "add_territory_tpl.php";
            
            case 'territory_insert':
				$id = $this->getParam('id');
                $rec['name'] = $this->getParam('territory');
                $rec['northlatitude'] = $this->getParam('latitude_north');
                $rec['southlatitude'] = $this->getParam('latitude_south');
                $rec['eastlongitude'] = $this->getParam('longitude_east');
                $rec['westlongitude'] = $this->getParam('longitude_west');
                $rec['geo2id'] = $this->getParam('geo2');
                $rec['area'] = $this->getParam('area');
                $rec['unitofmeasure'] = $this->getParam('unit_of_measure');
                if ($id) {
                    $this->objTerritory->update('id', $id, $rec);
                    $success = '3';
                } else {
					if ($this->objTerritory->valueExists('name',$rec['name'])) {
						return $this->nextAction('territory_admin', array('success'=>'4'));
					}
                    $this->objTerritory->insert($rec);
                    $success = '1';
                }
                return $this->nextAction('territory_admin', array('success'=>$success));
            
            case 'employee_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objUser->getAll("WHERE firstname LIKE '%$searchStr%' OR surname LIKE '%$searchStr%' OR username LIKE '%$searchStr%' ORDER BY surname");
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('success', $this->getParam('success'));
                return 'admin_employee_tpl.php';
            
            case 'create_employee':
                if ($this->objTitle->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_notitle','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'title_admin')));
                    return 'redirect_tpl.php';
                }
                if ($this->objStatus->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_nostatus','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'status_admin')));
                    return 'redirect_tpl.php';
                }
                if ($this->objRole->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_norole','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'role_admin')));
                    return 'redirect_tpl.php';
                }
                if ($this->objDepartment->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_nodepartment','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'department_admin')));
                    return 'redirect_tpl.php';
                }
                if ($this->objCountry->getRecordCount() < 1) {
                    $this->setVar('message', $this->objLanguage->languageText('mod_ahis_nocountry','openaris'));
                    $this->setVar('location', $this->uri(array('action'=>'country_add')));
                    return 'redirect_tpl.php';
                }
                
                $this->setVar('id', $this->getParam('id'));
                $this->setVar('error', $this->getParam('error'));
                $this->setVar('titles', $this->objTitle->getAll("ORDER BY name"));
                $this->setVar('status', $this->objStatus->getAll("ORDER BY name"));
                $this->setVar('locations', $this->objCountry->getAll("ORDER BY common_name"));
                $this->setVar('departments', $this->objDepartment->getAll("ORDER BY name"));
                $this->setVar('roles', $this->objRole->getAll('ORDER BY name'));
				$superDisabled = ($this->objAhisUser->isSuperUser($this->objUser->userId()))? 0 : 1;
				$this->setVar('superDisabled', $superDisabled);
                return "add_employee_tpl.php";
            
            case 'employee_insert':
                $id = $this->getParam('id');
                $record['surname'] = $this->getParam('surname');
                $record['firstname'] = $this->getParam('name');
                $test = $this->objUser->getAll("WHERE firstname = '{$record['firstname']}' AND surname = '{$record['surname']}'");
                $record['username'] = $this->getParam('username');
                $password = $this->getParam('password');
                $ahisRecord['titleid'] = $this->getParam('titleid');
                $ahisRecord['statusid'] = $this->getParam('statusid');
                if ($ahisRecord['statusid'] == "init_02") {
                    $record['isactive'] = 0;
                }
                $ahisRecord['ahisuser'] = $this->getParam('ahisuser');
                if ($ahisRecord['ahisuser']) {
                    $ahisRecord['ahisuser'] = 1;
					$record['isactive'] = 1;
                    if ((!$record['username'] || !$password) && !$id) {
                        return $this->nextAction('create_employee', array('error'=>1, 'id'=>$id));
                    }
                } else {
                    $ahisRecord['ahisuser'] = 0;
                    $record['isactive'] = 0;
                }
				if ($this->getParam('superuser')) {
					$ahisRecord['superuser'] = 1;
				} else {
					$ahisRecord['superuser'] = 0;
				}
                $ahisRecord['locationid'] = $this->getParam('locationid');
                $ahisRecord['departmentid'] = $this->getParam('departmentid');
                $ahisRecord['roleid'] = $this->getParam('roleid');
                $ahisRecord['dateofbirth'] = $this->getParam('datebirth');
                $ahisRecord['datehired'] = $this->getParam('hireddate');
                $ahisRecord['fax'] = $this->getParam('fax');
                $ahisRecord['phone'] = $this->getParam('phone');
                $ahisRecord['email'] = $this->getParam('email');
                $ahisRecord['retired'] = $this->getParam('retired');
                if ($ahisRecord['retired']) {
                    $ahisRecord['retired'] = 1;
                    $record['isactive'] = 0;
                    $ahisRecord['dateretired'] = $this->getParam('retireddate');
                } else {
                    $ahisRecord['retired'] = 0;
                }

                if ($id) {
					if ($password) {
						$record['pass'] = sha1($password);
					}
                    $this->objUser->update('id', $id, $record);
					$user = $this->objUser->getRow('id',$id);
					$userid = $user['userid'];
                    $code = 3;
                } else {
                    if (!empty($test)) {
                        return $this->nextAction('employee_admin', array('success'=>'4'));
                    }
					$userid = $this->objUserAdmin->generateUserId();
                    $id = $this->objUserAdmin->addUser($userid, $record['username'], $password, NULL, $record['firstname'], $record['surname'], NULL, NULL, NULL, NULL, NULL, "useradmin", $record['isactive']);
					//$id = $this->objUser->insert($record);
                    $code = 1;
                }
				if ($this->getParam('adminuser')) {
					$urec = $this->objUserAdmin->getArray("SELECT perm_user_id FROM tbl_perms_perm_users WHERE auth_user_id = '$userid'");
					$groupAdmin = $this->getObject('groupadminmodel', 'groupadmin');
					$gid = $groupAdmin->getId("Site Admin");
					$groupAdmin->addGroupUser($gid, $urec[0]['perm_user_id']);
				}
                if ($this->objAhisUser->valueExists('id', $id)) {
                    $this->objAhisUser->update('id', $id, $ahisRecord);
                } else {
                    $ahisRecord['id'] = $id;
                    $this->objAhisUser->insert($ahisRecord);
                }
                
                return $this->nextAction('employee_admin', array('success'=>$code));
			
			
			case 'exchangerates_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objExchangerate->getAll("WHERE defaultcurrencyid LIKE '%$searchStr%' OR exchangecurrencyid LIKE '%$searchStr%' OR startdate LIKE '%$searchStr%' OR enddate LIKE '%$searchStr%' ORDER BY defaultcurrencyid");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'exchangerate_add')));
				$this->setVar('addLinkText', "Add exchange rate");
                $this->setVar('headingText', 'Exchange rate');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Default currency');
				$this->setVar('columnName2', 'Exchange currency');
				$this->setVar('columnName3', 'Start Date');
				$this->setVar('columnName4', 'End Date');
                $this->setVar('deleteAction', 'exchangerates_delete');
                $this->setVar('fieldName1', 'defaultcurrencyid');
				$this->setVar('fieldName2', 'exchangecurrencyid');
				$this->setVar('fieldName3', 'startdate');
				$this->setVar('fieldName4', 'enddate');
				$this->setVar('numoffields', '4');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'exchangerates_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
						
				
			case 'exchangerates_delete':
				$id = $this->getParam('id');
                $this->objExchangerate->delete('id', $id);
                return $this->nextAction('exchangerates_admin', array('success'=>'2'));
			
			case 'exchangerates_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('exchangerate',$this->objExchangerate->getRow('id',$id));
			 return 'ahis_exchangerateedit_tpl.php';
		
			case 'exchangerates_update':
				$id = $this->getParam('id');
				$default_currency = $this->getParam('defaultcurrencyid');
				$exchange_currency = $this->getParam('exchangecurrencyid');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				
				$this->objExchangerate->update('id', $id, array('defaultcurrencyid'=>$default_currency,'exchangecurrencyid'=>$exchange_currency,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('exchangerates_admin',array('success'=>3));
				
				
				
			case 'exchangeratedetails_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objExchangeratedetail->getAll("WHERE firstcurrency LIKE '%$searchStr%' OR secondcurrency LIKE '%$searchStr%'OR conversionfactor LIKE '%$searchStr%' OR startdate LIKE '%$searchStr%' OR enddate LIKE '%$searchStr%' ORDER BY firstcurrency");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'exchangeratedetails_add')));
                $this->setVar('addLinkText', "Add exchange rate details");
                $this->setVar('headingText', 'Exchange rate details');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'First currency');
				$this->setVar('columnName2', 'Second currency');
				$this->setVar('columnName3', 'Conversion factor');
				$this->setVar('columnName4', 'Start Date');
				$this->setVar('columnName5', 'End Date');
                $this->setVar('deleteAction', 'exchangeratedetails_delete');
                $this->setVar('fieldName1', 'firstcurrency');
				$this->setVar('fieldName2', 'secondcurrency');
				$this->setVar('fieldName3', 'conversionfactor');
				$this->setVar('fieldName4', 'startdate');
				$this->setVar('fieldName5', 'enddate');
				$this->setVar('numoffields', '5');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'exchangeratedetails_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
				
			case 'exchangeratedetails_delete':
				$id = $this->getParam('id');
                $this->objExchangeratedetail->delete('id', $id);
                return $this->nextAction('exchangeratedetails_admin', array('success'=>'2'));
			
			case 'exchangeratedetails_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('exchangeratedetails',$this->objExchangeratedetail->getExchangeratedetail($id));
			 return 'ahis_exchangeratedetailsedit_tpl.php';
		
			case 'exchangeratedetails_update':
				$id = $this->getParam('id');
				$first_currency = $this->getParam('firstcurrency');
				$second_currency = $this->getParam('secondcurrency');
				$conversion_factor = $this->getParam('conversionfactor');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				
				$this->objExchangeratedetail->update('id', $id, array('firstcurrency'=>$first_currency,'secondcurrency'=>$second_currency,'conversionfactor'=>$conversion_factor,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('exchangeratedetails_admin',array('success'=>3));
			
			case 'infectionsource_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objInfectionsources->getAll("WHERE possiblesource LIKE '%$searchStr%' OR abbreviation LIKE '%$searchStr%' OR description LIKE '%$searchStr%' ORDER BY possiblesource");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'infectionsources_add')));
                $this->setVar('addLinkText', "Infection sources");
                $this->setVar('headingText', 'Infection sources');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Possible source');
				$this->setVar('columnName2', 'Abbreviation');
				$this->setVar('columnName3', 'Description');
				$this->setVar('columnName4', 'End Date');
                $this->setVar('deleteAction', 'infectionsources_delete');
                $this->setVar('fieldName1', 'possiblesource');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'description');
				//$this->setVar('fieldName4', 'enddate');
				$this->setVar('numoffields', '3');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'infectionsources_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			
			case 'infectionsources_delete':
				$id = $this->getParam('id');
                $this->objInfectionsources->delete('id', $id);
                return $this->nextAction('infectionsource_admin', array('success'=>'2'));
				
			case 'infectionsources_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('infectionsources',$this->objInfectionsources->getRow('id',$id));
			 return 'ahis_infectionsourcesedit_tpl.php';
		
			case 'infectionsources_update':
				$id = $this->getParam('id');
				$possible_source = $this->getParam('possiblesource');
				$abbreviation = $this->getParam('abbreviation');
				$description = $this->getParam('description');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				
				$this->objInfectionsources->update('id', $id, array('possiblesource'=>$possible_source,'abbreviation'=>$abbreviation,'description'=>$description,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('infectionsource_admin',array('success'=>3));
			
			case 'controlmeasure_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objControlmeasures->getAll("WHERE controlmeasure LIKE '%$searchStr%' OR abbreviation LIKE '%$searchStr%' OR description LIKE '%$searchStr%' ORDER BY controlmeasure");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'controlmeasures_add')));
                $this->setVar('addLinkText', "Add control measure");
                $this->setVar('headingText', 'Control measures');
                $this->setVar('action', $action);
                $this->setVar('columnName1', $this->objLanguage->languageText('phrase_controlmeasure'));
				$this->setVar('columnName2', $this->objLanguage->languageText('phrase_abbreviation'));
				$this->setVar('columnName3', $this->objLanguage->languageText('phrase_description'));
                $this->setVar('deleteAction', 'controlmeasure_delete');
                $this->setVar('fieldName1', 'controlmeasure');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'description');
				$this->setVar('numoffields', '3');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'controlmeasures_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			
			case 'controlmeasure_delete':
				$id = $this->getParam('id');
                $this->objControlmeasures->delete('id', $id);
                return $this->nextAction('controlmeasure_admin', array('success'=>'2'));
			
			case 'controlmeasures_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('controlmeasures',$this->objControlmeasures->getRow('id',$id));
			 return 'ahis_controlmeasuresedit_tpl.php';
		
			case 'controlmeasures_update':
				$id = $this->getParam('id');
				$control_measure = $this->getParam('controlmeasure');
				$abbreviation = $this->getParam('abbreviation');
				$description = $this->getParam('description');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				
				$this->objControlmeasures->update('id', $id, array('controlmeasure'=>$control_measure,'abbreviation'=>$abbreviation,'description'=>$description,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('controlmeasure_admin',array('success'=>3));
			
			case 'newspecies_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objSpeciesNew->getAll("WHERE speciestypeid LIKE '%$searchStr%' OR speciesname LIKE '%$searchStr%' OR description LIKE '%$searchStr%' ORDER BY speciestypeid");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'speciesnew_add')));
                $this->setVar('addLinkText', "Add new species");
                $this->setVar('headingText', 'New species');
                $this->setVar('action', $action);
                $this->setVar('columnName1', $this->objLanguage->languageText('phrase_code'));
				$this->setVar('columnName2', $this->objLanguage->languageText('phrase_name'));
				$this->setVar('columnName3', $this->objLanguage->languageText('phrase_description'));
                $this->setVar('deleteAction', 'newspecies_delete');
                $this->setVar('fieldName1', 'speciescode');
				$this->setVar('fieldName2', 'speciesname');
				$this->setVar('fieldName3', 'description');
				$this->setVar('numoffields', '3');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'newspecies_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'genview_tpl.php';
				
			case 'newspecies_delete':
				$id = $this->getParam('id');
                $this->objSpeciesNew->delete('id', $id);
                return $this->nextAction('newspecies_admin', array('success'=>'2'));
			
			case 'newspecies_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('speciestypes',$this->objSpeciesType->getAll());
			 $this->setVar('speciesnew',$this->objSpeciesNew->getRow('id',$id));
			 return 'ahis_speciesedit_tpl.php';
		
			case 'newspecies_update':
				$id = $this->getParam('id');
				$species_type = $this->getParam('speciestypeid');
				$species_code = $this->getParam('speciescode');
				$species_name = $this->getParam('speciesname');
				$description = $this->getParam('description');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				$this->objSpeciesNew->update('id', $id, array('speciestypeid'=>$species_type,'speciescode'=>$species_code,'speciesname'=>$species_name,'description'=>$description,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('newspecies_admin',array('success'=>3));
				
			case 'speciescategory_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objSpeciescategories->getAll("WHERE category LIKE '%$searchStr%' OR abbreviation LIKE '%$searchStr%' OR description LIKE '%$searchStr%' ORDER BY category");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'speciescategories_add')));
                $this->setVar('addLinkText', "Add new species category");
                $this->setVar('headingText', 'New species category');
                $this->setVar('action', $action);
                $this->setVar('columnName1', $this->objLanguage->languageText('phrase_category'));
				$this->setVar('columnName2', $this->objLanguage->languageText('phrase_abbreviation'));
				$this->setVar('columnName3', $this->objLanguage->languageText('phrase_description'));
                $this->setVar('deleteAction', 'speciescategory_delete');
                $this->setVar('fieldName1', 'category');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'description');
				$this->setVar('numoffields', '3');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'speciescategory_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			
			case 'speciescategory_delete':
				$id = $this->getParam('id');
                $this->objSpeciescategories->delete('id', $id);
                return $this->nextAction('speciescategory_admin', array('success'=>'2'));
			
			case 'speciescategory_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('speciescategories',$this->objSpeciescategories->getRow('id',$id));
			  $this->setVar('speciesnames',$this->objSpeciesNew->getAll());
			 return 'ahis_speciescategoriesedit_tpl.php';
		
			case 'speciescategory_update':
				$id = $this->getParam('id');
				$species_name = $this->getParam('speciesnameid');
				$category = $this->getParam('category');
				$abbreviation = $this->getParam('abbreviation');
				$description = $this->getParam('description');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				$this->objSpeciescategories->update('id', $id, array('speciesnameid'=>$species_name,'category'=>$category,'abbreviation'=>$abbreviation,'description'=>$description,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('speciescategory_admin',array('success'=>3));
			
			case 'newagent_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objAgents->getAll("WHERE agentcode LIKE '%$searchStr%' OR agent LIKE '%$searchStr%' OR abbreviation LIKE '%$searchStr%' OR description LIKE '%$searchStr%' ORDER BY agentcode");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'agents_add')));
                $this->setVar('addLinkText', "Add new agent");
                $this->setVar('headingText', 'New agent admin');
                $this->setVar('action', $action);
                $this->setVar('columnName1', $this->objLanguage->languageText('phrase_code'));
				$this->setVar('columnName2', $this->objLanguage->languageText('phrase_agent'));
				$this->setVar('columnName3', $this->objLanguage->languageText('phrase_abbreviation'));
				$this->setVar('columnName4', $this->objLanguage->languageText('phrase_description'));
                $this->setVar('deleteAction', 'newagent_delete');
                $this->setVar('fieldName1', 'agentcode');
				$this->setVar('fieldName2', 'agent');
				$this->setVar('fieldName3', 'abbreviation');
				$this->setVar('fieldName4', 'description');
				$this->setVar('numoffields', '4');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'newagent_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
 			
			case 'newagent_delete':
				$id = $this->getParam('id');
                $this->objAgents->delete('id', $id);
                return $this->nextAction('newagent_admin', array('success'=>'2'));
			
			case 'newagent_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('agents',$this->objAgents->getAgent($id));
			 return 'ahis_agentsedit_tpl.php';
		
			case 'newagent_update':
				$id = $this->getParam('id');
				$agent_code = $this->getParam('agentcode');
				$agent = $this->getParam('agent');
				$abbreviation = $this->getParam('abbreviation');
				$description = $this->getParam('description');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				$this->objAgents->update('id', $id, array('agentcode'=>$agent_code,'agent'=>$agent,'abbreviation'=>$abbreviation,'description'=>$description,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('newagent_admin',array('success'=>3));
				
			case 'newdiseasespecies_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objDiseasespecies->getAll("WHERE speciesid LIKE '%$searchStr%' OR description LIKE '%$searchStr%' ORDER BY speciesid");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'diseasespecies_add')));
                $this->setVar('addLinkText', "Add new disease species");
                $this->setVar('headingText', 'Disease species admin');
                $this->setVar('action', $action);
                $this->setVar('columnName1', $this->objLanguage->languageText('phrase_diseasespecies'));
				$this->setVar('columnName2', $this->objLanguage->languageText('phrase_description'));
				
                $this->setVar('deleteAction', 'newdiseasespecies_delete');
                $this->setVar('fieldName1', 'speciesid');
				$this->setVar('fieldName2', 'description');
				$this->setVar('numoffields', '2');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'newdiseasespecies_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			
			case 'newdiseasespecies_delete':
				$id = $this->getParam('id');
                $this->objDiseasespecies->delete('id', $id);
                return $this->nextAction('newdiseasespecies_admin', array('success'=>'2'));
				
			case 'newdiseasespecies_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
				$this->setVar('diseases', $this->objDiseases->getAll());
				$this->setVar('speciestypes', $this->objSpeciesType->getAll());
			 $this->setVar('diseasespecies',$this->objDiseasespecies->getRow('id',$id));
			 return 'ahis_diseasespeciesedit_tpl.php';
		
			case 'newdiseasespecies_update':
				$id = $this->getParam('id');
				$disease = $this->getParam('diseaseid');
				$species = $this->getParam('speciestypeid');
				$description = $this->getParam('description');
				$dateStartPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateEndPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$createdby = $this->getParam('createdby');
				$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
				$modifiedby = $this->getParam('modifiedby');
				$this->objDiseasespecies->update('id', $id, array('diseaseid'=>$disease,'speciesid'=>$species,'description'=>$description,'startdate'=>$dateStartPicker,'enddate'=>$dateEndPicker,'datecreated'=>$dateCreatedPicker, 'createdby'=>$this->objUser->UserName(), 'datemodified'=>$dateModifiedPicker,'modifiedby'=>$this->objUser->UserName()));
			 	return $this->nextAction('newdiseasespecies_admin',array('success'=>3));
            
            case 'production_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objProduction->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'production_add')));
                $this->setVar('addLinkText', "addproduction");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_productionadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_name'));
                $this->setVar('deleteAction', 'production_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'production_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'production_add':
                $this->setVar('id', $this->getParam('id'));
                return 'production_add_tpl.php';
            
            case 'production_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objProduction->valueExists('name', $name)) {
                    return $this->nextAction('production_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objProduction->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objProduction->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('production_admin', array('success'=>$code));
            
            case 'production_delete':
                $id = $this->getParam('id');
                $this->objProduction->delete('id', $id);
                return $this->nextAction('production_admin', array('success'=>'2'));
            
            case 'title_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objTitle->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'title_add')));
                $this->setVar('addLinkText', "addtitle");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_titleadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_title'));
                $this->setVar('deleteAction', 'title_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'title_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'title_add':
                $this->setVar('id', $this->getParam('id'));
                return 'title_add_tpl.php';
            
            case 'title_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objTitle->valueExists('name', $name)) {
                    return $this->nextAction('title_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objTitle->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objTitle->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('title_admin', array('success'=>$code));
            
            case 'title_delete':
                $id = $this->getParam('id');
                $this->objTitle->delete('id', $id);
                return $this->nextAction('title_admin', array('success'=>'2'));
            
            case 'status_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objStatus->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'status_add')));
                $this->setVar('addLinkText', "addstatus");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_statusadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_status'));
                $this->setVar('deleteAction', 'status_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'status_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'status_add':
                $this->setVar('id', $this->getParam('id'));
                return 'status_add_tpl.php';
            
            case 'status_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objStatus->valueExists('name', $name)) {
                    return $this->nextAction('status_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objStatus->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objStatus->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('status_admin', array('success'=>$code));
            
            case 'status_delete':
                $id = $this->getParam('id');
                $this->objStatus->delete('id', $id);
                return $this->nextAction('status_admin', array('success'=>'2'));
            
            case 'animalproduction_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objAnimalProduction->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'animalproduction_add')));
                $this->setVar('addLinkText', "addanimalproductiontype");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_animalproductionadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_animalproduction'));
                $this->setVar('deleteAction', 'animalproduction_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'animalproduction_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'animalproduction_add':
                $this->setVar('id', $this->getParam('id'));
                return 'animalproduction_add_tpl.php';
            
            case 'animalproduction_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($id) {
                    $this->objAnimalProduction->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    if ($this->objAnimalProduction->valueExists('name', $name)) {
					    return $this->nextAction('animalproduction_admin', array('success'=>'4'));
					}
					$this->objAnimalProduction->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('AnimalProduction_admin', array('success'=>$code));
            
            case 'animalproduction_delete':
                $id = $this->getParam('id');
                $this->objAnimalProduction->delete('id', $id);
                return $this->nextAction('animalproduction_admin', array('success'=>'2'));
            case 'sex_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objSex->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'sex_add')));
                $this->setVar('addLinkText', "addsex");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_sexadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_sex'));
                $this->setVar('deleteAction', 'sex_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'sex_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'sex_add':
                $this->setVar('id', $this->getParam('id'));
                return 'sex_add_tpl.php';
            
            case 'sex_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objSex->valueExists('name', $name)) {
                    return $this->nextAction('sex_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objSex->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objSex->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('sex_admin', array('success'=>$code));
            
            case 'sex_delete':
                $id = $this->getParam('id');
                $this->objSex->delete('id', $id);
                return $this->nextAction('sex_admin', array('success'=>'2'));
            
            case 'outbreak_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objOutbreak->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'outbreak_add')));
                $this->setVar('addLinkText', "addoutbreak");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_outbreakstatusadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_outbreak'));
                $this->setVar('deleteAction', 'outbreak_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'outbreak_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'outbreak_add':
                $this->setVar('id', $this->getParam('id'));
                return 'outbreak_add_tpl.php';
            
            case 'outbreak_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objOutbreak->valueExists('name', $name)) {
                    return $this->nextAction('outbreak_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objOutbreak->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objOutbreak->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('outbreak_admin', array('success'=>$code));
            
            case 'outbreak_delete':
                $id = $this->getParam('id');
                $this->objOutbreak->delete('id', $id);
                return $this->nextAction('outbreak_admin', array('success'=>'2'));
            
            case 'diagnosis_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objDiagnosis->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'diagnosis_add')));
                $this->setVar('addLinkText', "addbasis");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_diagnosisadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_diagnosis'));
                $this->setVar('deleteAction', 'diagnosis_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'diagnosis_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'diagnosis_add':
                $this->setVar('id', $this->getParam('id'));
                return 'diagnosis_add_tpl.php';
            
            case 'diagnosis_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objDiagnosis->valueExists('name', $name)) {
                    return $this->nextAction('diagnosis_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objDiagnosis->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objDiagnosis->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('diagnosis_admin', array('success'=>$code));
            
            case 'diagnosis_delete':
                $id = $this->getParam('id');
                $this->objDiagnosis->delete('id', $id);
                return $this->nextAction('diagnosis_admin', array('success'=>'2'));
            
            case 'control_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objControl->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'control_add')));
                $this->setVar('addLinkText', "addcontrol");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_controladmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_control'));
                $this->setVar('deleteAction', 'control_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'control_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'control_add':
                $this->setVar('id', $this->getParam('id'));
                return 'control_add_tpl.php';
            
            case 'control_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objControl->valueExists('name', $name)) {
                    return $this->nextAction('control_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objControl->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objControl->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('control_admin', array('success'=>$code));
            
            case 'control_delete':
                $id = $this->getParam('id');
                $this->objControl->delete('id', $id);
                return $this->nextAction('control_admin', array('success'=>'2'));
            
            case 'quality_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objQuality->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'quality_add')));
                $this->setVar('addLinkText', "addquality");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_qualityadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_quality'));
                $this->setVar('deleteAction', 'quality_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'quality_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'quality_add':
                $this->setVar('id', $this->getParam('id'));
                return 'quality_add_tpl.php';
            
            case 'quality_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objQuality->valueExists('name', $name)) {
                    return $this->nextAction('quality_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objQuality->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objQuality->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('quality_admin', array('success'=>$code));
            
            case 'quality_delete':
                $id = $this->getParam('id');
                $this->objQuality->delete('id', $id);
                return $this->nextAction('quality_admin', array('success'=>'2'));
            
            case 'report_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objReport->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'report_add')));
                $this->setVar('addLinkText', "addreport");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_reportadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_report'));
                $this->setVar('deleteAction', 'report_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('superUser', $this->objAhisUser->isSuperUser($this->objUser->userId()));
                $this->setVar('editAction', 'report_add');
                $this->setVar('success', $this->getParam('success'));
                return 'report_admin_tpl.php';
            
            case 'report_add':
				if (!$this->objAhisUser->isSuperUser($this->objUser->userId())) {
					return $this->nextAction('admin');
				}
                $this->setVar('id', $this->getParam('id'));
                return 'report_add_tpl.php';
            
            case 'report_insert':
                if (!$this->objAhisUser->isSuperUser($this->objUser->userId())) {
					return $this->nextAction('admin');
				}
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objReport->valueExists('name', $name)) {
                    return $this->nextAction('report_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objReport->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objReport->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('report_admin', array('success'=>$code));
            
            case 'report_delete':
                if (!$this->objAhisUser->isSuperUser($this->objUser->userId())) {
					return $this->nextAction('admin');
				}
                $id = $this->getParam('id');
                $this->objReport->delete('id', $id);
                return $this->nextAction('report_admin', array('success'=>'2'));
            
            case 'age_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objAge->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'age_add')));
                $this->setVar('addLinkText', "addagegroup");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_ageadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_age'));
                $this->setVar('deleteAction', 'age_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'age_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'age_add':
                $this->setVar('id', $this->getParam('id'));
                return 'age_add_tpl.php';
            
            case 'age_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objAge->valueExists('name', $name)) {
                    return $this->nextAction('age_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objAge->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objAge->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('age_admin', array('success'=>$code));
            
            case 'age_delete':
                $id = $this->getParam('id');
                $this->objAge->delete('id', $id);
                return $this->nextAction('age_admin', array('success'=>'2'));
            
			case 'causative_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objCausative->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'causative_add')));
                $this->setVar('addLinkText', "addcausative");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_causativeadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_causative'));
                $this->setVar('deleteAction', 'causative_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'causative_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'causative_add':
                $this->setVar('id', $this->getParam('id'));
                return 'causative_add_tpl.php';
            
            case 'causative_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objCausative->valueExists('name', $name)) {
                    return $this->nextAction('causative_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objCausative->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objCausative->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('causative_admin', array('success'=>$code));
            
            case 'causative_delete':
                $id = $this->getParam('id');
                $this->objCausative->delete('id', $id);
                return $this->nextAction('causative_admin', array('success'=>'2'));
            
            case 'role_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objRole->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'role_add')));
                $this->setVar('addLinkText', "addrole");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_roleadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_role'));
                $this->setVar('deleteAction', 'role_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'role_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'role_add':
                $this->setVar('id', $this->getParam('id'));
                return 'role_add_tpl.php';
            
            case 'role_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objRole->valueExists('name', $name)) {
                    return $this->nextAction('role_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objRole->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objRole->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('role_admin', array('success'=>$code));
            
            case 'role_delete':
                $id = $this->getParam('id');
                $this->objRole->delete('id', $id);
                return $this->nextAction('role_admin', array('success'=>'2'));
            
            case 'department_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objDepartment->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'department_add')));
                $this->setVar('addLinkText', "adddepartment");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_departmentadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_department'));
                $this->setVar('deleteAction', 'department_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'department_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'department_add':
                $this->setVar('id', $this->getParam('id'));
                return 'department_add_tpl.php';
            
            case 'department_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objDepartment->valueExists('name', $name)) {
                    return $this->nextAction('department_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objDepartment->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objDepartment->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('department_admin', array('success'=>$code));
            
            case 'department_delete':
                $id = $this->getParam('id');
                $this->objDepartment->delete('id', $id);
                return $this->nextAction('department_admin', array('success'=>'2'));
            
            case 'disease_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objDisease->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'disease_add')));
                $this->setVar('addLinkText', "adddisease");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_diseaseadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_disease'));
                $this->setVar('deleteAction', 'disease_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'disease_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'disease_add':
                $this->setVar('id', $this->getParam('id'));
                return 'disease_add_tpl.php';
               
             case 'disease_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objDisease->valueExists('name', $name)) {
                    return $this->nextAction('disease_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objDisease->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objDisease->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('disease_admin', array('success'=>$code));
            case 'disease_delete':
                $id = $this->getParam('id');
                $this->objDisease->delete('id', $id);
                return $this->nextAction('disease_admin', array('success'=>'2'));
              
            case 'test_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objTest->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'test_add')));
                $this->setVar('addLinkText', "addtest");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_testadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_test'));
                $this->setVar('deleteAction', 'test_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'test_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
                
                
            case 'test_add':
                $this->setVar('id', $this->getParam('id'));
                return 'test_add_tpl.php';
             case 'test_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objTest->valueExists('name', $name)) {
                    return $this->nextAction('test_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objTest->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objTest->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('test_admin', array('success'=>$code));
            case 'test_delete':
                $id = $this->getParam('id');
                $this->objTest->delete('id', $id);
                return $this->nextAction('test_admin', array('success'=>'2'));
             
            case 'testresult_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objTestresult->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'testresult_add')));
                $this->setVar('addLinkText', "addtestresult");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_testresultadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_testresult'));
                $this->setVar('deleteAction', 'testresult_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'testresult_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'testresult_add':
                $this->setVar('id', $this->getParam('id'));
                return 'testresult_add_tpl.php';
             case 'testresult_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objTestresult->valueExists('name', $name)) {
                    return $this->nextAction('testresult_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objTestresult->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objTestresult->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('testresult_admin', array('success'=>$code));
            case 'testresult_delete':
                $id = $this->getParam('id');
                $this->objTestresult->delete('id', $id);
                return $this->nextAction('testresult_admin', array('success'=>'2'));
                 
            case 'sample_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objSample->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'sample_add')));
                $this->setVar('addLinkText', "addsample");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_sampleadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_sample'));
                $this->setVar('deleteAction', 'sample_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'sample_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'sample_add':
                $this->setVar('id', $this->getParam('id'));
                return 'sample_add_tpl.php';
            
             case 'sample_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objSample->valueExists('name', $name)) {
                    return $this->nextAction('sample_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objSample->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objSample->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('sample_admin', array('success'=>$code));
            case 'sample_delete':
                $id = $this->getParam('id');
                $this->objSample->delete('id', $id);
                return $this->nextAction('sample_admin', array('success'=>'2'));
             
            case 'survey_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objSurvey->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'survey_add')));
                $this->setVar('addLinkText', "addsurvey");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_surveyadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_survey'));
                $this->setVar('deleteAction', 'survey_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'survey_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
                
            case 'survey_add':
                $this->setVar('id', $this->getParam('id'));
                return 'survey_add_tpl.php';
            
             case 'survey_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objSurvey->valueExists('name', $name)) {
                    return $this->nextAction('survey_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objSurvey->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objSurvey->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('survey_admin', array('success'=>$code));
            case 'survey_delete':
                $id = $this->getParam('id');
                $this->objSurvey->delete('id', $id);
                return $this->nextAction('survey_admin', array('success'=>'2'));
             
            
            case 'farmingsystem_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objFarmingsystem->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'farmingsystem_add')));
                $this->setVar('addLinkText', "addfarm");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_farmingsystemadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_farming')." ".$this->objLanguage->languageText('word_system'));
                $this->setVar('deleteAction', 'farmingsystem_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'farmingsystem_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
           
            //case 'farmingsystem_add':
              //  $this->setVar('id', $this->getParam('id'));
               // return 'farmingsystem_add_tpl.php';
				
				
            
            case 'farmingsystem_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objFarmingsystem->valueExists('name', $name)) {
                    return $this->nextAction('farmingsystem_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objFarmingsystem->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objFarmingsystem->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('farmingsystem_admin', array('success'=>$code));
				
				
            case 'farmingsystem_delete':
                $id = $this->getParam('id');
                $this->objFarmingsystem->delete('id', $id);
                return $this->nextAction('farmingsystem_admin', array('success'=>'2'));
             
            
            case 'vaccinationhistory_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objVaccination->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'vaccination_add')));
                $this->setVar('addLinkText', "addvaccinename");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_vaccinationadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('phrase_vaccinationhistory'));
                $this->setVar('deleteAction', 'vaccination_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'vaccination_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'vaccination_add':
                $this->setVar('id', $this->getParam('id'));
                return 'vaccination_add_tpl.php';
             case 'vaccination_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objSurvey->valueExists('name', $name)) {
                    return $this->nextAction('vaccination_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objVaccination->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objVaccination->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('vaccinationhistory_admin', array('success'=>$code));
            case 'vaccination_delete':
                $id = $this->getParam('id');
                $this->objVaccination->delete('id', $id);
                return $this->nextAction('vaccinationhistory_admin', array('success'=>'2'));
             
            
            case 'species_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objSpecies->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'species_add')));
                $this->setVar('addLinkText', "addspecies");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_speciesadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_species'));
                $this->setVar('deleteAction', 'species_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'species_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
            
            case 'species_add':
                $this->setVar('id', $this->getParam('id'));
                return 'species_add_tpl.php';
             case 'species_insert':

                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objSpecies->valueExists('name', $name)) {
                    return $this->nextAction('species_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objSpecies->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objSpecies->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('species_admin', array('success'=>$code));
            case 'species_delete':
                $id = $this->getParam('id');
                $this->objSpecies->delete('id', $id);
                return $this->nextAction('species_admin', array('success'=>'2'));
             
            
            case 'breed_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objBreed->getAll("WHERE name LIKE '%$searchStr%' ORDER BY name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'breed_add')));
                $this->setVar('addLinkText', "addbreed");
                $this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_breedadmin','openaris'));
                $this->setVar('action', $action);
                $this->setVar('columnName', $this->objLanguage->languageText('word_breed'));
                $this->setVar('deleteAction', 'breed_delete');
                $this->setVar('fieldName', 'name');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'breed_add');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overview_tpl.php';
          
             case 'breed_add':
                $this->setVar('id', $this->getParam('id'));
                return 'breed_add_tpl.php';
             case 'breed_insert':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                if ($this->objBreed->valueExists('name', $name)) {
                    return $this->nextAction('breed_admin', array('success'=>'4'));
                }
                if ($id) {
                    $this->objBreed->update('id', $id, array('name'=>$name));
                    $code = 3;
                } else {
                    $this->objBreed->insert(array('name'=>$name));
                    $code = 1;
                }
                return $this->nextAction('breed_admin', array('success'=>$code));
            case 'breed_delete':
                $id = $this->getParam('id');
                $this->objBreed->delete('id', $id);
                return $this->nextAction('breed_admin', array('success'=>'2'));
				
			
			case 'animalmovement_add':
					$id=$this->getSession('ps_geo2Id');
			 		$this->setVar('dist',$this->objAnimalmovement->getDistrict($id));
                	$this->setVar('species', $this->objSpecies ->getAll("ORDER BY name"));
				return 'add_animalmovement_tpl.php';
				
			case 'livestockimport_add':
					$id=$this->getSession('ps_geo2Id');
			 		$this->setVar('dist',$this->objLivestockimport->getDistrict($id));
                	$this->setVar('species', $this->objSpecies ->getAll("ORDER BY name"));
					$this->setVar('geo2', $this->objGeo2 ->getAll("ORDER BY name"));
				return 'add_livestockimport_tpl.php';
				
			case 'livestockexport_add':
					$id=$this->getSession('ps_geo2Id');
			 		$this->setVar('dist',$this->objLivestockexport->getDistrict($id));
                	$this->setVar('species', $this->objSpecies ->getAll("ORDER BY name"));
					$this->setVar('geo2', $this->objGeo2 ->getAll("ORDER BY name"));
				return 'add_livestockexport_tpl.php';
				
			case 'animaldeworming_add':
					$id=$this->getSession('ps_geo2Id');
			 		$this->setVar('dist',$this->objAnimalmovement->getDistrict($id));
                	$this->setVar('species', $this->objSpecies ->getAll("ORDER BY name"));
					$this->setVar('control', $this->objControl ->getAll("ORDER BY name"));
					return 'animaldeworming_tpl.php';

			case 'vacinventory':
			      $this->setVar('repdate',$this->getSession('ps_repdate',date('Y-m-d')));
			      $this->setVar('ibardate',$this->getSession('ps_ibardate',date('Y-m-d')));
			      $this->setVar('officerId', $this->getSession('ps_officerId'));
			      $this->setVar('dataoff', $this->getSession('ps_dataoff'));	
			      $this->setVar('vetoff', $this->getSession('ps_vetoff'));			      		      
			      $this->setVar('userList', $this->objAhisUser->getList());
			      $this->setVar('status',$this->getParam('status'));
			      $data = $this->objCountry->getAll("ORDER BY official_name");
			      //print_r($data);exit;
			      $ptype = $this->objPartitionCategory->getAll("ORDER BY partitioncategory");
			      $plevel = $this->objPartitionLevel->getAll("ORDER BY partitionlevel");
			      $pname = $this->objPartition->getAll();
			      $this->setVar('arraycountry',$data);
			      $this->setVar('count',$this->getSession('ps_country'));
			      $this->setVar('ptype',$this->getSession('ps_admin1'));
			       $this->setVar('plevel',$this->getSession('ps_admin2'));
			      $this->setVar('pname',$this->getSession('ps_admin3'));
			      $this->setVar('month',$this->getSession('ps_month',date('m')));
			      $this->setVar('year1',$this->getSession('ps_year'));
			      $this->setVar('longitude',$this->getSession('ps_longitude'));
			      $this->setVar('lattitude',$this->getSession('ps_lattitude'));
		         $this->setVar('locname',$this->getSession('ps_locname'));
			      $this->setVar('loctype',$this->getSession('ps_loctype'));	      
			      $this->setVar('arrayrepoff', $this->objAhisUser->getListByRole('init_01'));
                $this->setVar('arraydataoff', $this->objAhisUser->getListByRole('init_02'));
                $this->setVar('arrayvetoff', $this->objAhisUser->getListByRole('init_03'));
			      $this->setVar('arraypartitiontype',$ptype);
			      $this->setVar('arraypartitionlevel',$plevel);
			      $this->setVar('arraypartition',$pname);
			      $this->setVar('dateyear',$this->getSession('ps_calendardate',date('Y-m-d')));
			      $this->setVar('phone',$this->getSession('ps_dphone'));
			      $this->setVar('fax',$this->getSession('ps_dfax'));
			      $this->setVar('email',$this->getSession('ps_demail'));
               $this->setVar('phone1',$this->getSession('ps_vphone'));
			      $this->setVar('fax1',$this->getSession('ps_vfax'));
			      $this->setVar('email1',$this->getSession('ps_vemail'));
			      $this->setVar('phone2',$this->getSession('ps_rphone'));
			      $this->setVar('fax2',$this->getSession('ps_rfax'));
			      $this->setVar('email2',$this->getSession('ps_remail'));
			      
			      $this->setVar('lataxis',$this->getSession('ps_lataxes'));
			      $this->setVar('longaxis',$this->getSession('ps_longaxes'));
			      
			      //$this->setSession('ps_calendardate', $this->getParam('calendardate'));
			      return 'vacinventory_tpl.php';
			case 'vacinventory_add':
			      $this->setSession('ps_officerId',$this->getParam('repOfficerId'));
			      $this->setSession('ps_dataoff',$this->getParam('dataOfficerId'));		
			      $this->setSession('ps_vetoff',$this->getParam('vetOfficerId'));	
			      $this->setSession('ps_repdate',$this->getParam('repdate'));	
			      $this->setSession('ps_dphone',$this->getParam('dataOfficerTel'));
			      $this->setSession('ps_dfax',$this->getParam('dataOfficerFax'));
			      $this->setSession('ps_demail',$this->getParam('dataOfficerEmail'));
			      $this->setSession('ps_vphone',$this->getParam('vetOfficerTel'));
			      $this->setSession('ps_vfax',$this->getParam('vetOfficerFax'));
			      $this->setSession('ps_vemail',$this->getParam('vetOfficerEmail')); 
			       $this->setSession('ps_rphone',$this->getParam('repOfficerTel'));
			      $this->setSession('ps_rfax',$this->getParam('repOfficerFax'));
			      $this->setSession('ps_remail',$this->getParam('repOfficerEmail')); 
			      $this->setSession('ps_ibardate',$this->getParam('ibardate'));
			      $this->setSession('ps_country',$this->getParam('countryId'));	
			      $this->setSession('ps_month',$this->getParam('month'));	
			      $this->setSession('ps_year',$this->getParam('year'));	
			      $this->setSession('ps_admin1',$this->getParam('partitionTypeId'));
				   $this->setSession('ps_admin2',$this->getParam('partitionLevelId'));
			      $this->setSession('ps_admin3',$this->getParam('partitionId'));
			      $this->setSession('ps_loctype',$this->getParam('loctype'));	
			      $this->setSession('ps_locname',$this->getParam('locname'));	
			      $this->setSession('ps_lattitude',$this->getParam('lattitude'));
			      $this->setSession('ps_longitude',$this->getParam('longitude'));	
	            $this->setSession('ps_longaxes',$this->getParam('longaxes'));
	            $this->setSession('ps_lataxes',$this->getParam('lataxes')); 
	            $lattval = $this->getSession('ps_lattitude'); 
	            $longval =$this->getSession('ps_longitude');
	            $countryId = $this->getSession('ps_country');
	            $filter = 'latt';
	            $status = $this->objCountry->getData($filter,$lattval,$countryId); 

	            $year = date('y');
	             $month = date('m');
	            $selyr = $this->getSession('ps_year');
	            $selmth = $this->getSession('ps_month'); 
	           if($selyr == $year && $selmth > $month ){
               return $this->nextAction('vacinventory',array('status'=>3));
	           } 
               if($status[0]['status']==1){
               return $this->nextAction('vacinventory',array('status'=>1));
               }
	             $filter = 'long';
	            $status = $this->objCountry->getData($filter,$longval,$countryId);
               if($status[0]['status']==2 && $longval !=0){
               return $this->nextAction('vacinventory',array('status'=>2));
               }
				   return $this->nextAction('vacinventory2');

			      
			case 'vacinventory2':
			
			      
			   	$ddata = $this->objDiseases->getAll("ORDER BY disease_name");
			   	$month = $this->getSession('ps_month');
			   	$year = $this->getSession('ps_year');
			      $district = $this->getSession('ps_admin3');
				  $countryid = $this->getSession('ps_country');
			   	$this->setVar('datemonth',$this->getSession('ps_month'));
			   	$this->setVar('arraycon',$this->objVacinventory->getCon($month,$year,$district));
			   	//$this->setVar('arraymonth',$this->objVacinventory->getData($month,$year));
               $this->setVar('repdate',$this->getSession('ps_repdate',date('Y-m-d')));
			      $this->setVar('ibardate',$this->getSession('ps_ibardate',date('Y-m-d')));		
			      $this->setVar('mandate',$this->getSession('ps_mandate',date('Y-m-d')));
			      $this->setVar('expdate',$this->getSession('ps_expdate',date('Y-m-d')));		   	   	
	     			$this->setVar('arraydisease',$ddata);
	     			$this->setVar('parttype', $this->getSession('ps_admin1'));	  
	     			$this->setVar('partlevel', $this->getSession('ps_admin2'));	
	     			$this->setVar('partname', $this->getSession('ps_admin3'));	     				     			     				     			   			
	     			$this->setVar('repoff', $this->getSession('ps_officerId'));
	     			$this->setVar('phone',$this->getSession('ps_phone'));
               $this->setVar('diseases',$this->getSession('ps_disease'));
               $this->setVar('vaccinesource',$this->getSession('ps_vacsource'));
               $this->setVar('lotnumber',$this->getSession('ps_lotno'));
               $this->setVar('planprovac',$this->getSession('ps_planprovac'));
               $this->setVar('condprovac',$this->getSession('ps_condprovac'));
               $this->setVar('cumprovac',$this->getSession('ps_cumprovac'));
               $this->setVar('planconvac',$this->getSession('ps_planconvac'));
               $this->setVar('condconvac',$this->getSession('ps_condconvac'));
               $this->setVar('cumconvac',$this->getSession('ps_cumconvac'));
               $this->setVar('comments',$this->getSession('ps_comments'));
	     			$this->setVar('fax',$this->getSession('ps_phone'));
	     			$this->setVar('email',$this->getSession('ps_phone'));
	     			$this->setVar('fax1',$this->getSession('ps_fax1'));
	     			$this->setVar('phone1',$this->getSession('ps_phone1'));
	     			$this->setVar('email',$this->getSession('ps_email1'));	     					     			     				     			
	            $this->setVar('vetoff', $this->getSession('ps_vetoff'));
	     			$this->setVar('dataoff', $this->getSession('ps_dataoff'));	   			  				     				     			
			      $this->setVar('userList', $this->objAhisUser->getList());
			      $this->setVar('arrayoutbreak',$this->objDiseaseReport->getOutbreak($countryid));
			      $this->setVar('arrayspecies',$this->objSpeciesNew->getAll("ORDER BY speciesname"));
			      return 'vacinventory2_tpl.php';
			      
	     case 'vacinventory2_add':
	     
	            
               //Set session of screen 2
               $this->setSession('ps_outrefno',$this->getParam('outbreakref'));
               $this->setSession('ps_disease',$this->getParam('diseaseId'));
               $this->setSession('ps_species',$this->getParam('speciesId'));
               $this->setSession('ps_vacsource',$this->getParam('vaccinesource'));
               $this->setSession('ps_lotno',$this->getParam('lotnumber'));
               $this->setSession('ps_mandate',$this->getParam('mandate'));
               $this->setSession('ps_expdate',$this->getParam('expdate'));
               $this->setSession('ps_planprovac',$this->getParam('planprovac'));
               $this->setSession('ps_condprovac',$this->getParam('condprovac'));
               $this->setSession('ps_cumprovac',$this->getParam('cumprovac'));
               $this->setSession('ps_planconvac',$this->getParam('planconvac'));
               $this->setSession('ps_condconvac',$this->getParam('condconvac'));
               $this->setSession('ps_cumconvac',$this->getParam('cumconvac'));
               $this->setSession('ps_comments',$this->getParam('comment'));
               
               

	     		   $data['repoff']= $this->getSession('ps_officerId');
	     		   $data['dataoff']= $this->getSession('ps_dataoff');
	     		   $data['vetoff']=$this->getSession('ps_vetoff');
	     		   $data['ibardate']=$this->getSession('ps_ibardate');
	     		   $data['repdate']=$this->getSession('ps_repdate');
	     		   $data['country']=$this->getSession('ps_country');
	     		   $data['month']=$this->getSession('ps_month');
	     		   $data['year']=$this->getSession('ps_year');
	     		   $data['parttype']=$this->getSession('ps_admin1');
	     		   $data['partlevel']=$this->getSession('ps_admin2');
	     		   $data['partname']=$this->getSession('ps_admin3');
	     		   $data['loctype']=$this->getSession('ps_loctype');
	     		   $data['locname']=$this->getSession('ps_locname');
	     		   $data['lattitude']=$this->getSession('ps_lattitude');
	     		   $data['longitude']=$this->getSession('ps_longitude');
	     		   $data['dphone']= $this->getSession('ps_dphone');
	     		   $data['dfax']=$this->getSession('ps_dfax');
	     		   $data['demail']= $this->getSession('ps_demail');
	     		   $data['vphone']= $this->getSession('ps_vphone');
	     		   $data['vfax']= $this->getSession('ps_vfax');
	     		   $data['vemail']=$this->getSession('ps_vemail');
	     		   $data['outrefno']=$this->getSession('ps_outrefno');
	     		   $data['disease']=$this->getSession('ps_disease');
	     		   $data['species']=$this->getSession('ps_species');
	     		   $data['vacsource']=$this->getSession('ps_vacsource');
	     		   $data['lotno']=$this->getSession('ps_lotno');
	     		   $data['mandate']=$this->getSession('ps_mandate');
	     		   $data['expdate']=$this->getSession('ps_expdate');
	     		   $data['planprovac']=$this->getSession('ps_planprovac');
	     		   $data['condprovac']=$this->getSession('ps_condprovac');
	     		   $data['cumprovac']=$this->getSession('ps_cumprovac');
	     		   $data['planconvac']=$this->getSession('ps_planconvac');
	     		   $data['condconvac']=$this->getSession('ps_condconvac');
	     		   $data['cumconvac']=$this->getSession('ps_cumconvac');	  
	     		   $data['comments']=$this->getSession('ps_comments');   		   
	     		   $sub = $this->getParam('fin');
	     		 //  print_r($data); print_r($sub); exit;
	     		   if($sub == 'Next')
	     		   {
	     		    $result = $this->objVacinventory->insert($data);
	     		   $this->unsetVaccineInventory2();
				   return $this->nextAction('vacinventory2');
	     		   }
	     		   if($sub == 'Finish'){
	     		   $result = $this->objVacinventory->insert($data);
	     		    $this->unsetVaccineInventory1();
	     		    $this->unsetVaccineInventory2();
				   return $this->nextAction('');
	     		   
	     		   
	     		   }


			      return 'vacinventory2_add_tpl.php';	
	      case 'vacinventory_clear':
	            $this->unsetVaccineInventory1();
	            return $this->nextAction('vacinventory');
	            
	      case 'vacinventory2_clear':
	             $this->unsetSession('ps_outrefno');
	             $this->unsetSession('ps_disease');
	             $this->unsetSession('ps_species');
	             $this->unsetSession('ps_vacsource');
	             $this->unsetSession('ps_lotno');
	             $this->unsetSession('ps_mandate');
	             $this->unsetSession('ps_expdate');
	             $this->unsetSession('ps_planprovac');
	             $this->unsetSession('ps_condprovac');
	             $this->unsetSession('ps_cumprovac');
	             $this->unsetSession('ps_planconvac');
	             $this->unsetSession('ps_condconvac');
	             $this->unsetSession('ps_cumconvac');
	             $this->unsetSession('ps_comments');
	            
				   return $this->nextAction('vacinventory2');
			case 'animalvaccine_add':
					$id=$this->getSession('ps_geo2Id');
			 		$this->setVar('dist',$this->objAnimalmovement->getDistrict($id));
               $this->setVar('species', $this->objSpecies ->getAll("ORDER BY name"));
					$this->setVar('control', $this->objControl ->getAll("ORDER BY name"));
					$this->setVar('vaccination', $this->objVaccination ->getAll("ORDER BY name"));
					$this->setVar('output',$this->getParam('output'));
				   //$this->setVar('calendardate', $this->getSession('ps_calendardate',date('Y-m-d')));
					return 'animalvaccine_tpl.php';
				
             case 'animal_population_add':
                $countlist = $this->objCountry->getAll("ORDER BY official_name");	
                $ptype = $this->objPartitionCategory->getAll("ORDER BY partitioncategory");
			    $plevel = $this->objPartitionLevel->getAll("ORDER BY partitionlevel");
			       $pname = $this->objPartition->getAll();                		       
			       $this->setVar('arrayCountry', $countlist);
			       $this->setVar('count',$this->getSession('ps_country'));
			       $this->setVar('ptype',$this->getSession('ps_admin1'));
			       $this->setVar('plevel',$this->getSession('ps_admin2'));
			       $this->setVar('pname',$this->getSession('ps_admin3'));
                $this->setVar('arrayAdmin1',$ptype);
                $this->setVar('arrayAdmin2', $plevel);
                $this->setVar('arrayAdmin3', $pname);
                $this->setVar('dyear',$this->getSession('ps_year'));
                $this->setVar('yearBox',$this->getSession('ps_calendardate',date('Y-m-d')));
                
                $this->setVar('arrayrepoff', $this->objAhisUser->getListByRole('init_01'));
                $this->setVar('arraydataoff', $this->objAhisUser->getListByRole('init_02'));
                $this->setVar('arrayvetoff', $this->objAhisUser->getListByRole('init_03'));
               $this->setVar('repoff', $this->getSession('ps_repOfficerId'));
			      $this->setVar('dataoff', $this->getSession('ps_dataoff'));
			      	
			      $this->setVar('vetoff', $this->getSession('ps_vetoff'));	
			      
			      $this->setVar('rphone',$this->getSession('ps_rphone'));
			       $this->setVar('rfax',$this->getSession('ps_rfax'));
			       $this->setVar('remail',$this->getSession('ps_remail'));
			      $this->setVar('dphone',$this->getSession('ps_dphone'));
			       $this->setVar('dfax',$this->getSession('ps_dfax'));
			       $this->setVar('demail',$this->getSession('ps_demail'));
			       $this->setVar('vphone',$this->getSession('ps_vphone'));
	             $this->setVar('vfax',$this->getSession('ps_vfax'));
			       $this->setVar('vemail',$this->getSession('ps_vemail'));	
			       $this->setVar('prodname',$this->getSession('ps_prodname'));
			       
			       $this->setVar('breed', $this->getSession('ps_breedId'));
			       $this->setVar('arrayrepoff', $this->objAhisUser->getListByRole('init_01'));
                $this->setVar('arraydataoff', $this->objAhisUser->getListByRole('init_02'));
                $this->setVar('arrayvetoff', $this->objAhisUser->getListByRole('init_03'));	      			      			      			      
			       $this->setVar('species',$this->getSession('ps_species'));				      			      
                $this->setVar('userList', $this->objAhisUser->getList());
                $speciesId= $this->getParam('speciesId', $this->getSession('ps_species')); 
                $animBreeds = $this->objBreed->getAll();
                $this->setVar('arraybreed', $animBreeds);             
                
                //$this->setVar('arrayBreed',$this->objBreed->getAll());
                $this->setVar('iDate',$this->getSession('ps_ibardate',date('Y-m-d')));
                $this->setVar('rDate', $this->getSession('ps_repdate', date('Y-m-d')));
			 		$id=$this->getSession('ps_geo2Id');
			 		
			 	  		 		
			 		 $this->setVar('countryId', $this->getSession('ps_countryId'));
                $this->setVar('admin1Id', $this->getSession('ps_admin1Id'));
                $this->setVar('admin2Id', $this->getSession('ps_admin2Id'));
                $this->setVar('admin3Id', $this->getSession('ps_admin3Id'));
			 		$rDate = $this->getParam('rDate', $this->getSession('ps_rDate'));
			 		$this->setVar('dist',$this->objAnimalPopulation->getDistrict($id));
			 	   $this->setVar('animprod',$this->objAnimalProduction->getAll("ORDER BY name"));
			 	   $this->setVar('arrayspecies', $this->objSpecies ->getAll("ORDER BY speciesname"));		
               //$this->setVar('breed', $this->objBreed ->getAll("ORDER BY name"));	
               
               //$this->setVar('species', $this->getSession('ps_species'));
               $this->setVar('dataEntryOfficerFax', $this->getSession('ps_dfax'));
					$this->setVar('dataEntryOfficerTel', $this->getSession('ps_dphone'));
					$this->setVar('dataEntryOfficerEmail', $this->getSession('ps_demail'));
					$this->setVar('valOfficerFax', $this->getSession('ps_vfax'));
					$this->setVar('valOfficerTel', $this->getSession('ps_vphone'));
					$this->setVar('valOfficerEmail', $this->getSession('ps_vemail'));
               $this->setVar('breed', $this->getSession('ps_breed')); 
              	                      				    
					
					return 'animal_population_tpl.php';
					
				  case "ajax_getbreed":
				  $speciesId = $this->getParam('classification');
				  echo json_encode($this->objBreed->getBreed($speciesId));
				  break;
				  
				  case "ajax_gettlu":
					$speciesId = $this->getSession('ps_species');
					$speciesno = $this->getParam('speciesno');
					echo json_encode(array('prod'=>$this->objTlu->getTlu($speciesId,$speciesno)));
			 		break; 
			 		 
					
					case 'animal_population1':
			      $this->setSession('ps_repOfficerId',$this->getParam('repOfficerId'));
			      $this->setSession('ps_dataoff',$this->getParam('dataOfficerId'));		
			      $this->setSession('ps_vetoff',$this->getParam('vetOfficerId'));	
			      $this->setSession('ps_repdate',$this->getParam('rDate'));	
			       $this->setSession('ps_rphone',$this->getParam('repOfficerTel'));
			      $this->setSession('ps_rfax',$this->getParam('repOfficerFax'));
			      $this->setSession('ps_remail',$this->getParam('repOfficerEmail'));
			      $this->setSession('ps_dphone',$this->getParam('dataOfficerTel'));
			      $this->setSession('ps_dfax',$this->getParam('dataOfficerFax'));
			      $this->setSession('ps_demail',$this->getParam('dataOfficerEmail'));
			      $this->setSession('ps_vphone',$this->getParam('vetOfficerTel'));
			      $this->setSession('ps_vfax',$this->getParam('vetOfficerFax'));
			      $this->setSession('ps_vemail',$this->getParam('vetOfficerEmail'));  
			      $this->setSession('ps_ibardate',$this->getParam('iDate'));
			      $this->setSession('ps_country',$this->getParam('countryId'));	
			      //$this->setSession('ps_month',$this->getParam('month'));	
			      $this->setSession('ps_year',$this->getParam('year'));	
			      $this->setSession('ps_admin1',$this->getParam('partitionTypeId'));
				   $this->setSession('ps_admin2',$this->getParam('partitionLevelId'));
			      $this->setSession('ps_admin3',$this->getParam('partitionId'));
			      
			      $this->setSession('ps_species',$this->getParam('classification'));
			      $this->setSession('ps_breed',$this->getParam('breedId'));	
			      $this->setSession('ps_prodname',$this->getParam('animal_production'));

			       return $this->nextAction('animal_population_screen2');
         
         case 'animal_population_screen2':         
               $this->setVar('userList', $this->objAhisUser->getList());                 		  
               $this->setVar('repoff', $this->getSession('ps_repOfficerId'));
               $animBreeds = $this->objBreed->getAll();
               $this->setVar('arraybreed', $animBreeds);     
               $this->setVar('arrayspecies', $this->objSpecies ->getAll("ORDER BY speciesname"));		
               $this->setVar('iDate',$this->getSession('ps_ibardate',date('Y-m-d')));
               $this->setVar('rDate', $this->getSession('ps_repdate', date('Y-m-d')));	
               $this->setVar('prodname',$this->getSession('ps_prodname'));	      			      			      			      
			      $this->setVar('species',$this->getSession('ps_species'));	
			      $this->setVar('breed', $this->getSession('ps_breed'));		 
               $this->setVar('totSpecies', '');
					$this->setVar('breedNumber', '');
					$speciesId= $this->getParam('speciesId', $this->getSession('ps_species')); 
               $animalcat = $this->objSpeciesAgeGroup->getUniqueGroups();//$this->objAnimCat->getCategory($speciesId);
              //print_r($animalcat);
					$this->setVar('arrayanimalCat', $animalcat);
					$totspecies=$this->getParam('totalNumSpecies');
					
					$this->setVar('tropicalLivestock', '');
					$this->setVar('crossBreed', '');
					$this->setVar('catNumber', '');
					$this->setVar('productionno', '');
				    
         return 'animal_population2_tpl.php';          
        
       
         				
			case 'animal_population2':
			   
	     		   $data['reporterid']= $this->getSession('ps_repOfficerId');
	     		   $data['dataentryid']= $this->getSession('ps_dataoff');
	     		   $data['vetofficerid']=$this->getSession('ps_vetoff');
	     		   $data['ibardate']=$this->getSession('ps_ibardate');
	     		   $data['repdate']=$this->getSession('ps_repdate');
	     		   $data['countryid']=$this->getSession('ps_country');

	     		   $data['year']=$this->getSession('ps_year');
	     		   $data['partitiontypeid']=$this->getSession('ps_admin1');
	     		   $data['partitionlevelid']=$this->getSession('ps_admin2');
	     		   $data['partitionnameid']=$this->getSession('ps_admin3');
	     		   $data['speciesid']=$this->getSession('ps_species');
	     		   $data['prodnameid']=$this->getSession('ps_prodname');
	     		   $data['breedid']=$this->getSession('ps_breed');
	     		   //$data['dphone']= $this->getSession('ps_dphone');
	     		   //$data['dfax']=$this->getSession('ps_dfax');
	     		   //$data['demail']= $this->getSession('ps_demail');
	     		   //$data['vphone']= $this->getSession('ps_vphone');
	     		  // $data['vfax']= $this->getSession('ps_vfax');
	     		   //$data['vemail']=$this->getSession('ps_vemail');
	     		   $data['totnumid']=$this->getParam('totalNumSpecies');
	     		   $data['troplivestockid']=$this->getParam('tropicalLivestock');
	     		   $data['prodnumid']=$this->getParam('productionno');
	     		   $data['breednoid']=$this->getParam('breedNumber');
	     		   $data['crossbreednumid']=$this->getParam('crossBreed');
	     		   $data['animalcatid']=$this->getParam('animalCat');
	     		   $data['catnumid']=$this->getParam('catNumber');
	     		   $data['comments']=$this->getParam('comments');
	     		   
			   	$result = $this->objAnimalPopCensus->insert($data);
			   	
			   	$this->unsetAnimalpopulation();	   			   						
			   return 'select_officer_tpl.php';  	
			   
			   
			       case 'animal_population_clear':
	      	   $this->unsetAnimalpopulation();
	           return $this->nextAction('animal_population_add');
	           
			case 'country_add':
			 		//$id=$this->getSession('ps_geo2Id');
			 		$this->setVar('languages',$this->objLanguages->getAll("ORDER BY language"));
			 	  	$this->setVar('currencies',$this->objCurrency->getAll());	
					$this->setVar('unitsOfArea',$this->objUnitOfArea->getAll());	
				return 'add_country_tpl.php';
				
			case 'addinspectiondata':
				$id=$this->getSession('ps_geo2Id');
				$output = $this->getParam('output');
			 	$this->setVar('dist',$this->objAnimalPopulation->getDistrict($id));
			 	$this->setVar('output',$output);

			return 'meat_inspection_tpl.php';
			
			case 'animal_slaughter_add':
				$id=$this->getSession('ps_geo2Id');
				 $this->setVar('dist',$this->objAnimalPopulation->getDistrict($id));
				 
			return 'slaughter_tpl.php';
		 		$this->setVar('arrayGeo2', $this->objGeo2->getAll("ORDER BY name"));				
				return 'slaughter_tpl.php';
			//sam	
			
		case 'add_language':
		$this->setVar('output', $this->getParam('output'));
			return 'add_language_tpl.php';
		case 'currency_add':
		$this->setVar('output', $this->getParam('output'));
			return 'add_currency_tpl.php';
		case 'unit_of_area_add':
		$this->setVar('output', $this->getParam('output'));
			return 'add_unitofarea_tpl.php';
		case 'locality_type_add':
		$this->setVar('output', $this->getParam('output'));
			return 'locality_types_tpl.php';
		case 'diagnostic_method_add':
		$this->setVar('output', $this->getParam('output'));
			return 'diagnostic_methods_tpl.php';	
		case 'other_control_measures_add':
		$this->setVar('output', $this->getParam('output'));
			return 'other_control_methods_tpl.php';
		case 'species_names_add':	
		$this->setVar('output', $this->getParam('output'));	
		$this->setVar('species',$this->objSpeciesNew->getAll());
			return 'species_names_tpl.php';
		case 'species_economic_function_add':
		$this->setVar('output', $this->getParam('output'));
		$this->setVar('species',$this->objSpeciesNames->getAll());
			return 'species_economic_function_tpl.php';
		case 'diseases_add':
		$this->setVar('output', $this->getParam('output'));
			return 'add_disease_tpl.php';
		case 'edit_language':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('language',$this->objLanguages->getRow('id',$id));
			 return 'edit_language_tpl.php';
		 case 'unit_of_area_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('unitOfArea',$this->objUnitOfArea->getRow('id',$id));
			 return 'edit_unitofarea_tpl.php';
		case 'edit_country':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			  $this->setVar('languages',$this->objLanguages->getAll("ORDER BY language"));
			 $this->setVar('currencies',$this->objCurrency->getAll());	
			$this->setVar('unitsOfArea',$this->objUnitOfArea->getAll());
			 $this->setVar('country',$this->objCountry->getRow('id',$id));
			return 'edit_country_tpl.php';
		case 'currency_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('currency',$this->objCurrency->getRow('id',$id));
			 return 'edit_currency_tpl.php';
		 case 'locality_type_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('localitytype',$this->objLocalityType->getRow('id',$id));
			 return 'edit_locality_type_tpl.php';
		case 'diagnostic_method_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('diagmethod',$this->objDiagnosticMethod->getRow('id',$id));
			 return 'edit_diagnostic_method_tpl.php';
		case 'other_control_measures_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('measures',$this->objOtherControlMeasures->getMeasures($id));
			 return 'edit_other_control_measures_tpl.php';	
			 
		case 'species_names_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			   $this->setVar('languages',$this->objLanguages->getAll("ORDER BY language"));
			 $this->setVar('species',$this->objSpeciesNames->getRow('id',$id));
			  $this->setVar('allspecies',$this->objSpeciesNew->getAll());
			 return 'edit_species_names_tpl.php';
			 
		case 'diseases_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			 $this->setVar('disease',$this->objDiseases->getRow('id',$id));
			 return 'edit_diseases_tpl.php';
			 
		case 'species_economic_function_edit':
			 $id= $this->getParam('id');
			  $this->setVar('id',$id);
			  $this->setVar('species',$this->objSpeciesNames->getAll());
			 $this->setVar('economic',$this->objSpeciesEconomicFunction->getRow('id',$id));
			 return 'edit_species_economic_function_tpl.php';
					
		case 'currency_update':
		$id = $this->getParam('id');
		 $isocurrencycode = $this->getParam('isocurrencycode');
		$currency = $this->getParam('currency');
		$symbol = $this->getParam('symbol');
		$remarks= $this->getParam('remarks');
		$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));
		$this->objCurrency->update('id', $id, array('iso_currency_code'=>		$isocurrencycode,'currency'=>$currency,'symbol'=>$symbol,'remarks'=>$remarks,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			 	return $this->nextAction('currency_admin',array('success'=>3));
	case 'unit_of_area_update':
		$id = $this->getParam('id');		
		$unit_of_area = $this->getParam('unit_of_area');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');		
		$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));
		$this->objUnitOfArea->update('id', $id, array('unit_of_area'=>		$unit_of_area,'abbreviation'=>$abbrev,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			 	return $this->nextAction('unit_of_area_admin',array('success'=>3));
	case 'language_update':
			   $id = $this->getParam('id');
			    $isolanguagecode = $this->getParam('isolanguagecode');
				$language = $this->getParam('language');
				$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));
			 $this->objLanguages->update('id', $id, array('iso_language_code'=>$isolanguagecode,'language'=>$language,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			 	return $this->nextAction('language_admin',array('success'=>3));
		case 'locality_type_update':
				  $id = $this->getParam('id');
		$locality_type= $this->getParam('locality_type');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));		
	 $this->objLocalityType->update('id', $id, array('locality_type'=>$locality_type,'abbreviation'=>$abbrev,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
		return $this->nextAction('locality_type_admin',array('success'=>3));
		case 'diagnostic_method_update':
			 $id = $this->getParam('id');
			$method= $this->getParam('diagnostic_method');
			$abbrev = $this->getParam('abbrev');
			$desc = $this->getParam('desc');
			$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));		
		 $this->objDiagnosticMethod->update('id', $id, array('diagnostic_method'=>$method,'abbreviation'=>$abbrev,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			return $this->nextAction('diagnostic_method_admin',array('success'=>3));
			
		case 'species_economic_function_update':
			 $id = $this->getParam('id');
			$species= $this->getParam('species');
			$economic= $this->getParam('economic_function');
			$abbrev = $this->getParam('abbrev');
			$desc = $this->getParam('desc');
			$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));		
		 $this->objSpeciesEconomicFunction->update('id', $id, array('species_id'=>$species,'economic_function'=>$economic,'abbreviation'=>$abbrev,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			return $this->nextAction('species_economic_function_admin',array('success'=>3));
		case 'other_control_measures_update':
			 $id = $this->getParam('id');
			$code= $this->getParam('code');
			$measure= $this->getParam('measure');
			$abbrev = $this->getParam('abbrev');
			$desc = $this->getParam('desc');
			$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));		
		 $this->objOtherControlMeasures->update('id', $id, array('control_measure_code'=>$code,'control_measure'=>$measure,'abbreviation'=>$abbrev,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			return $this->nextAction('other_control_measures_admin',array('success'=>3));
		
		case 'country_update':
		$id = $this->getParam('id');//echo $id;
		//$this->setVar('id',$id);
		$isocountrycode = $this->getParam('isocountrycode');
		$common_name =$this->getParam('commoname');
		$official_name = $this->getParam('officialname');
		$default_lang = $this->getParam('languages');
		$default_currency = $this->getParam('currencies');
		$countryidd= $this->getParam('countryidd');
		$northlat = $this->getParam('northlat');
		$southlat = $this->getParam('southlat');
		$westlong = $this->getParam('westlong');
		$eastlong = $this->getParam('eastlong');
		$area = $this->getParam('area');	
		$unit_of_area = $this->getParam('units_of_area');	
	   $date_modified = $this->getSession('ps_calendardate',date('Y-m-d'));	
	   $this->objAhisUser = $this->getObject('ahisuser');
		$this->objCountry->update('id', $id, array('iso_country_code'=>$isocountrycode,'common_name'=>$common_name,'official_name'=>$official_name,'default_language'=>$default_lang,'default_currency'=>$default_currency,'country_idd'=>$countryidd,'north_latitude'=>$northlat,'south_latitude'=>$southlat,'west_longitude'=>$westlong,'east_longitude'=>$eastlong,'area'=>$area,'unit_of_area_id'=>$unit_of_area,'date_modified'=>$date_modified,'modified_by'=>$this->objUser->UserName()));
			 	return $this->nextAction('country_admin',array('success'=>3));	
				
		case 'diseases_update':
		$id = $this->getParam('id');
		$code= $this->getParam('code');
		$name= $this->getParam('name');
		$shortname= $this->getParam('shortname');
		$ref_code = $this->getParam('ref_code');
		$desc = $this->getParam('desc');
		$inlist = $this->getParam('in_oie_list');
		$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));
			
		 $this->objDiseases->update('id', $id, array('disease_code'=>$code,'disease_name'=>$name,'short_name'=>$shortname,'reference_code'=>$ref_code,'in_OIE_list'=>$inlist,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			return $this->nextAction('diseases_admin',array('success'=>3));
		
		case 'species_names_update':
		$id = $this->getParam('id');
			$species= $this->getParam('species');
		$common_name= $this->getParam('common_name');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datemodified = $this->getSession('ps_calendardate',date('Y-m-d'));
			
		 $this->objSpeciesNames->update('id', $id, array('species_id'=>$species,'common_name'=>$common_name,'abbreviation'=>$abbrev,'description'=>$desc,'date_modified'=>$datemodified,'modified_by'=>$this->objUser->UserName()));
			return $this->nextAction('species_names_admin',array('success'=>3));
		
		case 'language_save':
			return $this->saveLanguageData();
			
			case 'country_save':
			return $this->saveCountryData();
			case 'currency_save':
			return $this->saveCurrencyData();
			case 'units_of_area_save':
			return $this->saveUnitOfAreaData();
			case 'locality_type_save':
			return $this->saveLocalityTypeData();
			case 'diagnostic_method_save':
			return $this->saveDiagnosticMethodData();
			case 'other_control_measures_save':
			return $this->saveOtherControlMeasuresData();
			case 'species_names_save':
			return $this->saveSpeciesNamesData();
			case 'species_economic_function_save':
			return $this->saveSpeciesEconomicFunctionData();
			case 'diseases_save':
			return $this->saveDiseasesData();
			case 'language_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objLanguages->getAll("WHERE language LIKE '%$searchStr%' ORDER BY language");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'add_language')));
                $this->setVar('addLinkText', "Add Language");
                $this->setVar('headingText', 'Languages');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'ISO Language Code');
				 $this->setVar('columnName2', 'Language');
				 $this->setVar('columnName3', 'Start Date');
				 $this->setVar('columnName4', 'End Date');
                $this->setVar('deleteAction', 'language_delete');
                $this->setVar('fieldName1', 'iso_language_code');
				$this->setVar('fieldName2', 'language');
				$this->setVar('fieldName3', 'start_date');
				$this->setVar('fieldName4', 'end_date');
                $this->setVar('searchStr', $searchStr);
				  $this->setVar('numoffields', '4');
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'edit_language');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
				
			case 'country_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objCountry->getAll("WHERE official_name LIKE '%$searchStr%' ORDER BY official_name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'country_add')));
                $this->setVar('addLinkText', "Add Country");
                $this->setVar('headingText', 'Countries');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'ISO Country Code');
				$this->setVar('columnName2', 'Common Name');
				$this->setVar('columnName3', 'Country IDD');
				$this->setVar('columnName4', 'North Latitude');
				$this->setVar('columnName5', 'South Latitude');
				$this->setVar('columnName6', 'West Longitude');
				$this->setVar('columnName7', 'East Latitude');
                $this->setVar('deleteAction', 'country_delete');
                $this->setVar('fieldName1', 'iso_country_code');
				$this->setVar('fieldName2', 'common_name');
				$this->setVar('fieldName3', 'country_idd');
				$this->setVar('fieldName4', 'north_latitude');
				$this->setVar('fieldName5', 'south_latitude');
				$this->setVar('fieldName6', 'west_longitude');
				$this->setVar('fieldName7', 'east_longitude');
				$this->setVar('numoffields', '7');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'edit_country');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			case 'currency_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objCurrency->getAll("WHERE currency LIKE '%$searchStr%' ORDER BY currency");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'currency_add')));
                $this->setVar('addLinkText', "Add Currency");
                $this->setVar('headingText', 'Currencies');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'ISO Currency Code');
				$this->setVar('columnName2', 'Currency');
				$this->setVar('columnName3', 'Symbol');
				$this->setVar('columnName4', 'Start Date');
				$this->setVar('columnName5', 'End Date');					
                $this->setVar('deleteAction', 'currency_delete');
				$this->setVar('fieldName1', 'iso_currency_code');
                $this->setVar('fieldName2', 'currency');
				$this->setVar('fieldName3', 'symbol');
				$this->setVar('fieldName4', 'start_date');
				$this->setVar('fieldName5', 'end_date');
				$this->setVar('numoffields', '5');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'currency_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
				
		case 'diseases_admin':
                $searchStr = $this->getParam('searchStr');
                $data = $this->objDiseases->getAll("WHERE disease_name LIKE '%$searchStr%' ORDER BY disease_name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'diseases_add')));
                $this->setVar('addLinkText', "Add Disease");
                $this->setVar('headingText', 'Disease Admin');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Disease Code');
			    $this->setVar('columnName2', 'Disease Name');
				$this->setVar('columnName3', 'Short Name');
				$this->setVar('columnName4', 'Ref. Code');
				$this->setVar('columnName5', 'In OIE List');
				$this->setVar('columnName6', 'Start Date');
				$this->setVar('columnName7', 'End Date');	
                $this->setVar('deleteAction', 'diseases_delete');
                $this->setVar('fieldName1', 'disease_code');
				$this->setVar('fieldName2', 'disease_name');
				$this->setVar('fieldName3', 'short_name');
				$this->setVar('fieldName4', 'reference_code');
				$this->setVar('fieldName5', 'in_OIE_list');
				$this->setVar('fieldName6', 'start_date');
				$this->setVar('fieldName7', 'end_date');
				$this->setVar('numoffields', '7');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'diseases_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			case 'unit_of_area_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objUnitOfArea->getAll("WHERE unit_of_area LIKE '%$searchStr%' ORDER BY unit_of_area");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'unit_of_area_add')));
                $this->setVar('addLinkText', "Add Unit Of area");
                $this->setVar('headingText', 'Unit Of Area');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Unit Of Area');
				$this->setVar('columnName2', 'Abbreviation');
				$this->setVar('columnName3', 'Start Date');
				$this->setVar('columnName4', 'End Date');
                $this->setVar('deleteAction', 'unit_of_area_delete');
                $this->setVar('fieldName1', 'unit_of_area');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'start_date');
				$this->setVar('fieldName4', 'end_date');
				$this->setVar('numoffields', '4');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'unit_of_area_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			case 'locality_type_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objLocalityType->getAll("WHERE locality_type LIKE '%$searchStr%' ORDER BY locality_type");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'locality_type_add')));
                $this->setVar('addLinkText', "Add Locality Type");
                $this->setVar('headingText', 'Locality Type');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Locality Type');
				$this->setVar('columnName2', 'Abbreviation');
				$this->setVar('columnName3', 'Start Date');
				$this->setVar('columnName4', 'End Date');
                $this->setVar('deleteAction', 'locality_type_delete');
                $this->setVar('fieldName1', 'locality_type');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'start_date');
				$this->setVar('fieldName4', 'end_date');
				$this->setVar('numoffields', '4');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'locality_type_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			case 'diagnostic_method_admin':
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objDiagnosticMethod->getAll("WHERE diagnostic_method LIKE '%$searchStr%' ORDER BY diagnostic_method");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'diagnostic_method_add')));
                $this->setVar('addLinkText', "Add Diagnostic Method");
                $this->setVar('headingText', 'Diagnostic Methods');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Diagnostic Method');
				$this->setVar('columnName2', 'Abbreviation');
				$this->setVar('columnName3', 'Start Date');
				$this->setVar('columnName4', 'End Date');
                $this->setVar('deleteAction', 'diagnostic_method_delete');
               $this->setVar('fieldName1', 'diagnostic_method');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'start_date');
				$this->setVar('fieldName4', 'end_date');
				$this->setVar('numoffields', '4');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'diagnostic_method_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
				case 'species_names_admin':
				if ($this->objSpeciesNew->getRecordCount() < 1) {
                    $this->setVar('message', 'No Species records have been entered yet. Please create a species first!');
                    $this->setVar('location', $this->uri(array('action'=>'newspecies_admin')));
                    return 'redirect_tpl.php';
                }
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objSpeciesNames->getAll("WHERE common_name LIKE '%$searchStr%' ORDER BY common_name");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'species_names_add')));
                $this->setVar('addLinkText', "Add Species Name");
                $this->setVar('headingText', 'Species Names');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Common Name');
				$this->setVar('columnName2', 'Abbreviation');
				$this->setVar('columnName3', 'Start Date');
				$this->setVar('columnName4', 'End Date');
				$this->setVar('numoffields', '4');
                $this->setVar('deleteAction', 'species_names_delete');
                $this->setVar('fieldName1', 'common_name');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'start_date');
				$this->setVar('fieldName4', 'end_date');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'species_names_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
				case 'species_economic_function_admin':
				 if ($this->objSpeciesNames->getRecordCount() < 1) {
                    $this->setVar('message', 'No Species Names records have been entered yet. Please create a species names first!');
                    $this->setVar('location', $this->uri(array('action'=>'species_names_admin')));
                    return 'redirect_tpl.php';
                }
			 $searchStr = $this->getParam('searchStr');
                $data = $this->objSpeciesEconomicFunction->getAll("WHERE economic_function LIKE '%$searchStr%' ORDER BY economic_function");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'species_economic_function_add')));
                $this->setVar('addLinkText', "Add Economic Function");
                $this->setVar('headingText', 'Species Economic Function');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Economic Function');
				$this->setVar('columnName2', 'Abbreviation');
				$this->setVar('columnName3', 'Start Date');
				$this->setVar('columnName4', 'End Date');
				$this->setVar('numoffields', '4');
                $this->setVar('deleteAction', 'species_economic_function_delete');
                $this->setVar('fieldName1', 'economic_function');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'start_date');
				$this->setVar('fieldName4', 'end_date');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'species_economic_function_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'genview_tpl.php';
				case 'other_control_measures_admin':
			$searchStr = $this->getParam('searchStr');
                $data = $this->objOtherControlMeasures->getAll("WHERE control_measure LIKE '%$searchStr%' ORDER BY control_measure");
                $this->setVar('addLinkUri', $this->uri(array('action'=>'other_control_measures_add')));
                $this->setVar('addLinkText', "Add Other Control Measures");
                $this->setVar('headingText', 'Other Control Measures');
                $this->setVar('action', $action);
                $this->setVar('columnName1', 'Other Control Measure Code');
				$this->setVar('columnName2', 'Other Control Measure');
				$this->setVar('columnName3', 'Abbreviation');
				$this->setVar('columnName4', 'Start Date');
				$this->setVar('columnName5', 'End Date');
				$this->setVar('numoffields', '5');
                $this->setVar('deleteAction', 'other_control_measures_delete');
                $this->setVar('fieldName1', 'control_measure_code');
				 $this->setVar('fieldName2', 'control_measure');
				$this->setVar('fieldName3', 'abbreviation');
				$this->setVar('fieldName4', 'start_date');
				$this->setVar('fieldName5', 'end_date');
                $this->setVar('searchStr', $searchStr);
                $this->setVar('data', $data);
                $this->setVar('allowEdit', TRUE);
                $this->setVar('editAction', 'other_control_measures_edit');
                $this->setVar('success', $this->getParam('success'));
                return 'admin_overviews_tpl.php';
			case 'language_update':
			   $id = $this->getParam('id');
			    $isolanguagecode = $this->getParam('isolanguagecode');
				$language = $this->getParam('language');
				 $date_modified = $this->getSession('ps_calendardate',date('Y-m-d'));
			 $this->objLanguages->update('id', $id, array('iso_language_code'=>$isolanguagecode,'language'=>$language,'modified_by'=>$this->objUser->UserName(),'date_modified'=>$date_modified));
			 	return $this->nextAction('language_admin',array('success'=>3));
			case 'language_delete':
                $id = $this->getParam('id');
                $this->objLanguages->delete('id', $id);
                return $this->nextAction('language_admin', array('success'=>'2'));
			case 'country_delete':
                $id = $this->getParam('id');
                $this->objCountry->delete('id', $id);
                return $this->nextAction('country_admin', array('success'=>'2'));
			case 'currency_delete':
                $id = $this->getParam('id');
                $this->objCurrency->delete('id', $id);
            	 return $this->nextAction('currency_admin', array('success'=>'2'));
			case 'unit_of_area_delete':
                $id = $this->getParam('id');
                $this->objUnitOfArea->delete('id', $id);
            	 return $this->nextAction('unit_of_area_admin', array('success'=>'2'));	  
			case 'language_delete':
				 $id = $this->getParam('id');
                $this->objLanguages->delete('id', $id);
                return $this->nextAction('language_admin', array('success'=>'2'));
				
			case 'locality_type_delete':
                $id = $this->getParam('id');
                $this->objLocalityType->delete('id', $id);
                return $this->nextAction('locality_type_admin', array('success'=>'2'));
			case 'diagnostic_method_delete':
                $id = $this->getParam('id');
                $this->objDiagnosticMethod->delete('id', $id);
                return $this->nextAction('diagnostic_method_admin', array('success'=>'2'));
				
			case 'other_control_measures_delete':
                $id = $this->getParam('id');
                $this->objOtherControlMeasures->delete('id', $id);
                return $this->nextAction('other_control_measures_admin', array('success'=>'2'));
				
			case 'species_economic_function_delete':
                $id = $this->getParam('id');
                $this->objSpeciesEconomicFunction->delete('id', $id);
                return $this->nextAction('species_economic_function_admin', array('success'=>'2'));
				
				
			case 'diseases_delete':
                $id = $this->getParam('id');
                $this->objDiseases->delete('id', $id);
                return $this->nextAction('diseases_admin', array('success'=>'2'));
				
			case 'species_names_delete':
                $id = $this->getParam('id');
                $this->objSpeciesNames->delete('id', $id);
                return $this->nextAction('species_names_admin', array('success'=>'2'));
		
		//end of sam
			case 'animalmovement_save':
				return $this->saveAnimalmovementData();
			case 'livestockimport_save':
				return $this->saveLivestockimportData();
			case 'livestockexport_save':
            	return $this->saveLivestockexportData();
			
			case 'animaldeworming_save':
				return $this->saveAnimaldewormingData();
				
			case 'animalvaccine_save':
				return $this->saveAnimalvaccineData();	
				
			case 'vaccine_inventory_add':
				return 'vaccine_inventory_tpl.php';
				
			case 'vaccine_inventory_save':
				return $this->saveVaccineInventoryData();
			case 'deworming_save':
				return $this->saveDewormingData();
				
			case 'exchangerate_add':
				$this->setVar('id', $this->getParam('id'));
				return 'ahis_exchangerate_tpl.php';
			
			case 'exchangeratedetails_add':
				$this->setVar('id', $this->getParam('id'));
				return 'ahis_exchangeratedetails_tpl.php';
				
			case 'infectionsources_add':
				$this->setVar('id', $this->getParam('id'));
				return 'ahis_infectionsources_tpl.php';
			
			case 'controlmeasures_add':
				$this->setVar('id', $this->getParam('id'));
				return 'ahis_controlmeasures_tpl.php';
				
			case 'speciesnew_add':
			    $this->setVar('id', $this->getParam('id'));
			    $this->setVar('speciestypes',$this->objSpeciesType->getAll());
				return 'ahis_species_tpl.php';
				
			case 'speciescategories_add':
			    $this->setVar('id', $this->getParam('id'));
			    $this->setVar('speciesnames',$this->objSpeciesNew->getAll());
				return 'ahis_speciescategories_tpl.php';
			
			case 'agents_add':
			    $this->setVar('id', $this->getParam('id'));
				return 'ahis_agents_tpl.php';
				
			case 'diseasespecies_add':
			    $this->setVar('id', $this->getParam('id'));
			     $diseases = $this->objDiseases->getAll();
				$this->setVar('diseases', $diseases);
				$this->setVar('speciestypes', $this->objSpeciesType->getAll());
				return 'ahis_diseasespecies_tpl.php';
				
			
			case 'exchangerate_save':
            	return $this->saveExchangeRateData();
				
			case 'exchangeratedetails_save':
            	return $this->saveExchangeRateDetailsData();
				
			case 'infectionsources_save':
            	return $this->saveInfectionSourcesData();
			
			case 'controlmeasures_save':
				return $this->saveControlMeasureData();
				
			case 'speciesnew_save':
				return $this->saveSpeciesNewData();
			
			case 'speciescategories_save':
				return $this->saveSpeciesCategoryData();
			
			case 'agents_save':
				return $this->saveAgentsData();
			
			case 'diseasespecies_save':
				return $this->saveDiseaseSpeciesData();
				
			//partition actions
			case 'partition_add':
				$this->setVar('output', $this->getParam('output'));
				$this->setVar('id', $this->getParam('id'));
				$this->setVar('partitionlevels', $this->objPartitionLevel->getAll());
				$this->setVar('partitions', $this->objPartition->getAll());
				return 'add_partitions_tpl.php';
			case 'partition_save':
				return $this->savePartition();
			case 'partition_view':
				$searchStr = $this->getParam('searchStr');
				$level=$this->getParam('level');
				if(empty($level))
				  $level='01';
				$parent=$this->getParam('parent');
				$dt = $this->objPartition->getRow('parentpartition',$parent);
				$this->setVar('parentname',$dt['partitionname']);
				$data = $this->objPartition->getAll();//getLevelPartitions($level,$searchStr,$parent);
				$this->setVar('addLinkUri', $this->uri(array('action'=>'partition_add','parent'=>$parent,'level'=>$level)));
				$this->setVar('addLinkText', "Add Partition");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_partitions','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_partitioncode','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('mod_ahis_partitionname','openaris'));
				$this->setVar('columnName3', $this->objLanguage->languageText('mod_ahis_partitionlevel','openaris'));
				$this->setVar('columnName4', $this->objLanguage->languageText('mod_ahis_parentpartition','openaris'));
				$this->setVar('columnName4', $this->objLanguage->languageText('phrase_startdate'));
				$this->setVar('columnName5', $this->objLanguage->languageText('phrase_enddate'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'partition_delete');
				$this->setVar('fieldName1', 'partitioncode');
				$this->setVar('fieldName2', 'partitionname');
				$this->setVar('fieldName3', 'partitionlevel');
				$this->setVar('fieldName4', 'parentpartition');
				$this->setVar('fieldName4', 'startdate');
				$this->setVar('fieldName5', 'enddate');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('numoffields', 5);
				$this->setVar('data', $data);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'partition_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'partitions_tpl.php';
			case 'partition_update':
				return $this->updatePartition();
			case 'partition_delete':
				return $this->delPartition($id);
				
			//partition level actions
			case 'partitionlevel_add':
				$this->setVar('id', $this->getParam('id'));
				$this->setVar('partitioncategories', $this->objPartitionCategory->getAll());
				return 'add_partitionlevels_tpl.php';
			case 'partitionlevel_save':
				return $this->savePartitionLevel();
			case 'partitionlevel_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objPartitionLevel->getAll("WHERE partitionlevel LIKE '%$searchStr%' ORDER BY partitionlevel");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'partitionlevel_add')));
				$this->setVar('addLinkText', "Add Partition Level");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_partitionlevel','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_partitionlevel','openaris'));
				//$this->setVar('columnName2', $this->objLanguage->languageText('mod_ahis_partitioncategory','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('word_description'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'partitionlevel_delete');
				$this->setVar('fieldName1', 'partitionlevel');
				//$this->setVar('fieldName2', 'partitioncategoryid');
				$this->setVar('fieldName2', 'description');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('data', $data);
				$this->setVar('numoffields', 2);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'partitionlevel_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'partitionlevel_update':
				return $this->updatePartitionLevel();
			case 'partitionlevel_delete':
				return $this->delPartitionLevel($id);
				
			//partition categories actions
			case 'partitioncategory_add':
				$this->setVar('id', $this->getParam('id'));
				
				return 'add_partitioncategories_tpl.php';
			case 'partitioncategory_save':				
				return $this->savePartitionCategory();
			case 'partitioncategory_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objPartitionCategory->getAll("WHERE partitioncategory LIKE '%$searchStr%' ORDER BY partitioncategory");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'partitioncategory_add')));
				$this->setVar('addLinkText', "Add Category");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_partitioncategory','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_partitioncategory','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('word_description'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'partitioncategory_delete');
				$this->setVar('fieldName1', 'partitioncategory');
				$this->setVar('fieldName2', 'description');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('data', $data);
				$this->setVar('numoffields', 2);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'partitioncategory_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'partitioncategory_update':
				return $this->updatePartitionCategory();
			case 'partitioncetegory_delete':
				return $this->delPartitionCategory($id);

			//occurence code actions
			case 'occurencecode_add':
				$this->setVar('id', $this->getParam('id'));				
				return 'add_occurencecodes_tpl.php';
			case 'occurencecode_save':
				return $this->saveOccurenceCode();
			case 'occurencecode_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objOccurenceCode->getAll("WHERE occurencecode LIKE '%$searchStr%' ORDER BY occurencecode");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'occurencecode_add')));
				$this->setVar('addLinkText', "Add Occurence Code");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_occurencecods','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_occurencecods','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('word_abbreviation'));
				$this->setVar('columnName3', $this->objLanguage->languageText('word_description'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'occurencecode_delete');
				$this->setVar('fieldName1', 'occurencecode');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'description');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('data', $data);
				$this->setVar('numoffields', 3);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'occurencecode_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'occurencecode_update':
				return $this->updateOccurenceCode();
			case 'occurencecode_delete':
				return $this->delOccurenceCode($id);
			
			//farming system actions
			case 'farmingsystem_add':
			$this->setVar('id', $this->getParam('id'));
			return 'add_farmingsystems_tpl.php'; 
				
			case 'farmingsystem_save':
			    return $this->saveFarmingSystem();

		case 'farmingsystem_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objFarmingSystem->getAll("WHERE farmingsystem LIKE '%$searchStr%' ORDER BY farmingsystem");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'farmingsystem_add')));
				$this->setVar('addLinkText', "Add Farming System");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_farmingsystems','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_farmingsystems','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('word_abbreviation'));
				$this->setVar('columnName3', $this->objLanguage->languageText('word_description'));
				$this->setVar('columnName4', $this->objLanguage->languageText('phrase_startdate'));
				$this->setVar('columnName5', $this->objLanguage->languageText('phrase_enddate'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'farmingsystem_delete');
				$this->setVar('fieldName1', 'farmingsystem');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'description');
				$this->setVar('fieldName4', 'startdate');
				$this->setVar('fieldName5', 'enddate');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('numoffields', 5);
				$this->setVar('data', $data);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'farmingsystem_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'farmingsystem_update':
				return $this->updateFarmingSystem();
			case 'farmingsystem_delete':
				return $this->delFarmingSystem($id);
				
			//species type actions
			case 'speciestype_add':
                //$this->setVar('id', $this->getParam('id'));
                return 'add_speciestypes_tpl.php';
						
	    	case 'speciestype_save':
				return $this->saveSpeciesType();
		
			case 'speciestype_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objSpeciesType->getAll("WHERE speciestype LIKE '%$searchStr%' ORDER BY speciestype");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'speciestype_add')));
				$this->setVar('addLinkText', "Add Species Type");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_speciestyps','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_speciestyps','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('word_abbreviation'));
				$this->setVar('columnName3', $this->objLanguage->languageText('word_description'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'speciestype_delete');
				$this->setVar('fieldName1', 'speciestype');
				$this->setVar('fieldName2', 'abbreviation');
				$this->setVar('fieldName3', 'description');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('data', $data);
				$this->setVar('numoffields', 4);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'speciestype_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'speciestype_update':
				return $this->updateSpeciesType();
			case 'speciestype_delete':
				return $this->delSpeciesType($id);

			//species age group actions
			case 'speciesagegroup_save':
			return $this->saveSpeciesAgeGroup();
	
			case 'speciesagegroup_add':
			      //$this->setVar('id', $this->getParam('id'));
			      $species = $this->objSpecies->getAll();
			      $this->setVar('species', $species);
			      return 'add_speciesagegroups_tpl.php';
			case 'speciesagegroup_view':
					$searchStr = $this->getParam('searchStr');
					$data = $this->objSpeciesAgeGroup->getAll("WHERE agegroup LIKE '%$searchStr%' ORDER BY agegroup");
					$this->setVar('addLinkUri', $this->uri(array('action'=>'speciesagegroup_add')));
					$this->setVar('addLinkText', "Add Species Age Group");
					$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_speciesagegrps','openaris'));
					$this->setVar('action', $action);
					$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_word_species','openaris'));
					$this->setVar('columnName2', $this->objLanguage->languageText('mod_ahis_speciesagegroup','openaris'));
					$this->setVar('columnName3', $this->objLanguage->languageText('word_abbreviation'));
					$this->setVar('columnName4', $this->objLanguage->languageText('word_description'));
					$this->setVar('columnName5', $this->objLanguage->languageText('mod_ahis_word_lowerlimit','openaris'));
					$this->setVar('columnName6', $this->objLanguage->languageText('mod_ahis_word_upperlimit','openaris'));
					$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
					$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
					$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
					$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
					$this->setVar('deleteAction', 'speciesagegroup_delete');
					$this->setVar('fieldName1', 'species');
					$this->setVar('fieldName2', 'agegroup');
					$this->setVar('fieldName3', 'abbreviation');
					$this->setVar('fieldName4', 'description');
					$this->setVar('fieldName5', 'lowerlimit');
					$this->setVar('fieldName6', 'upperlimit');
					$this->setVar('createdon', 'createdon');
					$this->setVar('createdby', 'createdby');
					$this->setVar('modifiedon', 'modifiedon');
					$this->setVar('modifiedby', 'modifiedby');
					$this->setVar('data', $data);
					$this->setVar('numoffields', 6);
					$this->setVar('searchStr', $searchStr);
					$this->setVar('editAction', 'speciesagegroup_add');
					$this->setVar('success', $this->getParam('success'));
					$this->setVar('allowEdit', TRUE);
					return 'genview_tpl.php';
			case 'speciesagegroup_update':
				return $this->updateSpeciesAgeGroup();
			case 'speciesagegroup_delete':
				return $this->delSpeciesAgeGroup($id);

			//species tropical livestock unit actions
			case 'speciestropicallivestockunit_add':
				  $this->setVar('id', $this->getParam('id'));
				  $this->setVar('species',$this->objSpeciesNew->getAll());
				  $this->setVar('speciescategories',$this->objSpeciescategories->getAll());
				  return 'add_speciestropicallivestockunit_tpl.php';
			case 'speciestropicallivestockunit_save':
				return $this->saveSpeciesTropicalLivestockUnit();
			case 'speciestropicallivestockunit_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objSpeciesTropicalLivestockUnit->getAll("WHERE tlufactor LIKE '%$searchStr%'");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'speciestropicallivestockunit_add')));
				$this->setVar('addLinkText', "Add Species Tropical Livestock Unit");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_speciestropicallivestockunit','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('word_species'));
				$this->setVar('columnName2', $this->objLanguage->languageText('word_speciescategories'));
				$this->setVar('columnName3', $this->objLanguage->languageText('word_tlufactor'));
				$this->setVar('columnName4', $this->objLanguage->languageText('word_remarks'));
				$this->setVar('createdon', $this->objLanguage->languageText('word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('word_modifiedby'));
				$this->setVar('deleteAction', 'speciestropicallivestockunit_delete');
				$this->setVar('fieldName1', 'speciesnameid');
				$this->setVar('fieldName2', 'speciescategoryid');
				$this->setVar('fieldName3', 'tlufactor');
				$this->setVar('fieldName4', 'remarks');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('data', $data);
				$this->setVar('numoffields', 6);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'speciestropicallivestockunit_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'speciestropicallivestockunit_update':
				return $this->updateSpeciesTropicalLivestockUnit();
			case 'speciestropicallivestockunit_delete':
				return $this->delSpeciesTropicalLivestockUnit($id);
			
			//disease agent actions
			case 'diseaseagent_add':
				$this->setVar('id', $this->getParam('id'));
				$diseases = $this->objDiseases->getAll();
				$this->setVar('diseases', $diseases);	
				$agents = $this->objAgents->getAll();
				$this->setVar('agents', $agents);				
				return 'add_diseaseagents_tpl.php';
			case 'diseaseagent_save':
				return $this->saveDiseaseAgent();
			case 'diseaseagent_view':
				$searchStr = $this->getParam('searchStr');
				$data = $this->objDiseaseAgent->getAll("WHERE description LIKE '%$searchStr%' ORDER BY agentid");
				$this->setVar('addLinkUri', $this->uri(array('action'=>'diseaseagent_add')));
				$this->setVar('addLinkText', "Add Disease Agent");
				$this->setVar('headingText', $this->objLanguage->languageText('mod_ahis_diseaseagnts','openaris'));
				$this->setVar('action', $action);
				$this->setVar('columnName1', $this->objLanguage->languageText('mod_ahis_disease','openaris'));
				$this->setVar('columnName2', $this->objLanguage->languageText('mod_ahis_diseaseagnts','openaris'));
				$this->setVar('columnName3', $this->objLanguage->languageText('word_description'));
				$this->setVar('createdon', $this->objLanguage->languageText('mod_ahis_word_createdon'));
				$this->setVar('createdby', $this->objLanguage->languageText('mod_ahis_word_createdby'));
				$this->setVar('modifiedon', $this->objLanguage->languageText('mod_ahis_word_modifiedon'));
				$this->setVar('modifiedby', $this->objLanguage->languageText('mod_ahis_word_modifiedby'));
				$this->setVar('deleteAction', 'diseaseagent_delete');				
				$this->setVar('fieldName1', 'diseaseid');
				$this->setVar('fieldName2', 'agentid');
				$this->setVar('fieldName3', 'description');
				$this->setVar('createdon', 'createdon');
				$this->setVar('createdby', 'createdby');
				$this->setVar('modifiedon', 'modifiedon');
				$this->setVar('modifiedby', 'modifiedby');
				$this->setVar('data', $data);
				$this->setVar('numoffields', 3);
				$this->setVar('searchStr', $searchStr);
				$this->setVar('editAction', 'diseaseagent_add');
				$this->setVar('success', $this->getParam('success'));
				$this->setVar('allowEdit', TRUE);
				return 'genview_tpl.php';
			case 'diseaseagent_update':
				return $this->updateDiseaseAgent();
			case 'diseaseagent_delete':
				return $this->delDiseaseAgent($id);
			//to hear
				
				
            case 'view_reports':
				
            case 'view_reports':
            
            default:
                return $this->nextAction('home');
            	
        }
    }
    
    /**
     * Method to unset the passive surveillance session variables
     *
     */
    private function unsetPassiveSession() {
        $this->unsetSession('ps_calendardate');
        $this->unsetSession('ps_oStatusId');
        $this->unsetSession('ps_qualityId');
        $this->unsetSession('ps_datePrepared');
        $this->unsetSession('ps_dateIBAR');
        $this->unsetSession('ps_dateReceived');
        $this->unsetSession('ps_dateIsReported');
        $this->unsetSession('ps_refNo');
        $this->unsetSession('ps_remarks');
        $this->unsetSession('ps_dateVet');
        $this->unsetSession('ps_dateOccurence');
        $this->unsetSession('ps_dateDiagnosis');
        $this->unsetSession('ps_dateInvestigation');
        $this->unsetSession('ps_locationId');
        $this->unsetSession('ps_longdeg');
        $this->unsetSession('ps_latdeg');
        $this->unsetSession('ps_longmin');
        $this->unsetSession('ps_latmin');
        $this->unsetSession('ps_longdirec');
        $this->unsetSession('ps_latdirec');
        $this->unsetSession('ps_diseaseId');
        $this->unsetSession('ps_causativeId');
        $this->unsetSession('ps_productionId');
        $this->unsetSession('ps_ageId');
        $this->unsetSession('ps_sexId');
        $this->unsetSession('ps_speciesId');
        $this->unsetSession('ps_controlId');
        $this->unsetSession('ps_basisId');
        $this->unsetSession('ps_cases');
        $this->unsetSession('ps_susceptible');
        $this->unsetSession('ps_deaths');
        $this->unsetSession('ps_vaccinated');
        $this->unsetSession('ps_slaughtered');
        $this->unsetSession('ps_destroyed');
        $this->unsetSession('ps_newcases');
        $this->unsetSession('ps_production');
        $this->unsetSession('ps_recovered');
        $this->unsetSession('ps_prophylactic');
        $this->unsetSession('ps_reportType');
    }
    
    
    /**
     * Method to unset the active surveillance session variables
     *
     */
    private function unsetActiveSession() {
    
        $this->unsetSession('ps_calendardate');
        $this->unsetSession('ps_officerId');
        $this->unsetSession('ps_disease');
        $this->unsetSession('ps_comments');
        $this->unsetSession('ps_surveyTypeId');
        $this->unsetSession('ps_campName');
        $this->unsetSession('ps_reportType');
		$this->unsetSession('ps_longdeg');
        $this->unsetSession('ps_latdeg');
        $this->unsetSession('ps_longmin');
        $this->unsetSession('ps_latmin');
        $this->unsetSession('ps_longdirec');
        $this->unsetSession('ps_latdirec');
        
       }
       
     private function unsetAnimalSession(){
        $this->unsetSession('ps_officerId');
        $this->unsetSession('ps_geo2Id');
        $this->unsetSession('ps_reportType');
        $this->unsetSession('ps_calendardate');
     }
	   private function AddData()
    {
        return 'add_data.php';
    }
	
	
	private function SaveData()
	{
	//capture input		
		$district = $this->getParam('district');
		$classification = $this->getParam('classification');
		$num_animals = $this->getParam('num_animals');
		$animal_production = $this->getParam('animal_production');
		$source = $this->getParam('source');
		$reportdate = $this->getSession('ps_calendardate',date('Y-m-d'));
		$data= $this->objAnimalPopulation ->addData($district, $classification, $num_animals, $animal_production,$source,$reportdate);
		
		return $this->nextAction('animal_feedback',array('success'=>1));
	
	}
	
	private function saveInspectionData()
	{
	//capture input		
		$district = $this->getParam('district');
		$date =$this->getParam('inspectiondate');
		$num_of_cases = $this->getParam('num_of_cases');
		$num_at_risk = $this->getParam('num_at_risk');
      $reportdate = $this->getSession('ps_calendardate',date('Y-m-d'));
      $currentyear = date('Y');
      $currentmonth = date('m');
      $currentday = date('d');
       $dist =split("-", $date);
      if($currentyear < $dist[0]){

 
      $output = 'yes';

      return $this->nextAction('addinspectiondata',array('output'=>$output));
      }else
      if($currentyear == $dist[0])
        {
        if($currentmonth<$dist[1]){
          
      $output = 'yes';

      return $this->nextAction('addinspectiondata',array('output'=>$output));
        
      }

      if($currentday < $dist[2]){
      $output = 'yes';

      return $this->nextAction('addinspectiondata',array('output'=>$output));
      }
      }
		$data= $this->objMeatInspect->addMeatInspectionData($district, $date, $num_of_cases, $num_at_risk,$reportdate);
		
		return $this->nextAction('animal_feedback',array('success'=>1));

		
	
	}
	private function saveSlaughterData()
	{
	//capture input		
		$district = $this->getParam('district');
		$num_cattle =$this->getParam('num_cattle');
		$num_sheep = $this->getParam('num_sheep');
		$num_goats = $this->getParam('num_goats');
		$num_pigs = $this->getParam('num_pigs');
		$num_poultry = $this->getParam('num_poultry');
		$other = $this->getParam('other');
		$name = $this->getParam('name');
		$remarks = $this->getParam('remarks');		
	   $reportdate = $this->getSession('ps_calendardate',date('Y-m-d'));			
		$data= $this->objSlaughter->addSlaughterData($district, $num_cattle, $num_sheep, $num_goats,$num_pigs,$num_poultry,$other,$name,$remarks,$reportdate);
		
		return $this->nextAction('animal_feedback',array('success'=>'1'));
	
	}
	private function saveLanguageData()
	{
		$isolanguagecode = $this->getParam('isolanguagecode');
		$language = $this->getParam('language');
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
		
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		 		 $this->setVar('output', $val);
		  		  $this->setVar('code', $isolanguagecode);
				  $this->setVar('lang', $language);

		   //return $this->nextAction('add_language',array('output'=>$val));
		   return 'add_language_tpl.php';

		 }	
		else
		{
		$data = $this->objLanguages->addData($isolanguagecode,$language,$datecreated,   $this->objUser->UserName(),$sdate,$edate);  
							
		return $this->nextAction('language_admin',array('success'=>'1'));
		}
	}
	private function saveCountryData()
	{
	//capture input		
		$isocountrycode = $this->getParam('isocountrycode');
		$common_name =$this->getParam('commoname');
		$official_name = $this->getParam('officialname');
		$default_lang = $this->getParam('languages');
		$default_currency = $this->getParam('currencies');
		$countryidd= $this->getParam('countryidd');
		$northlat = $this->getParam('northlat');
		$southlat = $this->getParam('southlat');
		$westlong = $this->getParam('westlong');
		$eastlong = $this->getParam('eastlong');
		$area = $this->getParam('area');	
		$unit_of_area = $this->getParam('units_of_area');	
	   $date_created = $this->getSession('ps_calendardate',date('Y-m-d'));	
	   $output="";
	  
	   if($northlat!=0) 
	   {
	   	if($northlat{0}=='+' or $northlat{0}=='-'){$output.='';} else { $output.='Enter +/- for north latitude<br>';}
	   }
	    if($southlat!=0) 
	   {
	   	if($southlat{0}=='+' or $southlat{0}=='-'){$output.='';}else{ $output.='Enter +/- for south latitude<br>';}
	   }
	    if($westlong!=0) 
	   {
	   		if($westlong{0}=='+' or $westlong{0}=='-'){$output.='';} else{ $output.='Enter +/- for west longitude<br>';}
	   }
	    if($eastlong!=0)
	   {
	   	if($eastlong{0}=='+' or $eastlong{0}=='-'){ $output.='';} else{ $output.='Enter +/- for east longitude<br>';}
	   }
	   if(!empty($output))
	   {
	    $this->setVar('output', $output);
		$this->setVar('code', $isocountrycode);
		$this->setVar('commonname', $common_name);
		$this->setVar('officialname', $official_name);
		$this->setVar('countryidd', $countryidd);
		$this->setVar('northlat', $northlat);
		$this->setVar('southlat', $southlat);
		$this->setVar('westlong', $westlong);
		$this->setVar('eastlong', $eastlong);
		$this->setVar('area', $area);
		$this->setVar('languages',$this->objLanguages->getAll("ORDER BY language"));
		$this->setVar('currencies',$this->objCurrency->getAll());	
		$this->setVar('unitsOfArea',$this->objUnitOfArea->getAll());
		return 'add_country_tpl.php';
	   }
	   else
	   	{			
		$data= $this->objCountry->addData($isocountrycode, $common_name, $official_name,$default_lang, $default_currency,$countryidd,$northlat,$southlat,$westlong,$eastlong,$area,$unit_of_area,$date_created,  $this->objUser->UserName());
		
		return $this->nextAction('country_admin',array('success'=>'1'));
		}
	
	}
	private function saveCurrencyData()
	{
		$isocurrencycode = $this->getParam('isocurrencycode');
		$currency = $this->getParam('currency');
		$symbol = $this->getParam('symbol');
		$remarks= $this->getParam('remarks');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
		
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		 			 $this->setVar('output', $val);
		  		  $this->setVar('code', $isocurrencycode);
				  $this->setVar('currency', $currency);
				  $this->setVar('symbol', $symbol);
				  $this->setVar('remark', $remarks);
				  return 'add_currency_tpl.php';

		  // return $this->nextAction('currency_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objCurrency->addData($isocurrencycode,$currency,$symbol,$remarks,$datecreated, $this->objUser->UserName(),$sdate,$edate);  
							
		return $this->nextAction('currency_admin',array('success'=>'1'));
		}
	}
	
	private function saveUnitOfAreaData()
	{
		$unit_of_area = $this->getParam('unit_of_area');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		  $this->setVar('output', $val);
		   $this->setVar('unit_of_area', $unit_of_area);
			$this->setVar('abbrev', $abbrev);
			$this->setVar('desc', $desc);
		  return 'add_unitofarea_tpl.php';
		   //return $this->nextAction('unit_of_area_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objUnitOfArea->addData($unit_of_area,$abbrev,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate);  
							
		return $this->nextAction('unit_of_area_admin',array('success'=>'1'));
		}
	}
	
	private function saveLocalityTypeData()
	{
		$locality_type= $this->getParam('locality_type');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
		
		 
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		  
		   $this->setVar('output', $val);
		   $this->setVar('locality_type', $locality_type);
			$this->setVar('abbrev', $abbrev);
			$this->setVar('desc', $desc);
		  return 'locality_types_tpl.php';
		   //return $this->nextAction('locality_type_add',array('output'=>$val));
		 }	
		 else
		 {	
		 $data = $this->objLocalityType->addData($locality_type,$abbrev,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate); 		
		return $this->nextAction('locality_type_admin',array('success'=>'1'));
		}
	}
	private function saveDiagnosticMethodData()
	{
		$diagnostic_method= $this->getParam('diagnostic_method');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		   $this->setVar('output', $val);
		   $this->setVar('method', $diagnostic_method);
			$this->setVar('abbrev', $abbrev);
			$this->setVar('desc', $desc);
		  return 'diagnostic_methods_tpl.php';
		   //return $this->nextAction('diagnostic_method_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objDiagnosticMethod->addData($diagnostic_method,$abbrev,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate);  
							
		return $this->nextAction('diagnostic_method_admin',array('success'=>'1'));
		}
	}
	private function saveOtherControlMeasuresData()
	{
		$code= $this->getParam('code');
		$measure= $this->getParam('measure');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		   $this->setVar('output', $val);
		   $this->setVar('code', $code);
		   $this->setVar('measure', $measure);
			$this->setVar('abbrev', $abbrev);
			$this->setVar('desc', $desc);
			return 'other_control_methods_tpl.php';
		  // return $this->nextAction('other_control_measures_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objOtherControlMeasures->addData($code,$measure,$abbrev,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate);  
							
		return $this->nextAction('other_control_measures_admin',array('success'=>'1'));
		}
	}	
	
	private function saveSpeciesNamesData()
	{
		$species= $this->getParam('species');
		$common_name= $this->getParam('common_name');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		 		$this->setVar('languages',$this->objSpeciesNew->getAll());

		   $this->setVar('output', $val);
		   $this->setVar('common_name', $common_name);
			$this->setVar('abbrev', $abbrev);
			$this->setVar('desc', $desc);
		$this->setVar('species',$this->objSpeciesNew->getAll());
			return 'species_names_tpl.php';
		   //return $this->nextAction('species_names_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objSpeciesNames->addData($species,$common_name,$abbrev,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate);  
							
		return $this->nextAction('species_names_admin',array('success'=>'1'));
		}
	}	
	private function saveSpeciesEconomicFunctionData()
	{
		$species= $this->getParam('species');
		$economic_function= $this->getParam('economic_function');
		$abbrev = $this->getParam('abbrev');
		$desc = $this->getParam('desc');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		 		$this->setVar('languages',$this->objLanguages->getAll("ORDER BY language"));

		  $this->setVar('output', $val);
		   $this->setVar('economic_function', $economic_function);
			$this->setVar('abbrev', $abbrev);
			$this->setVar('desc', $desc);
			return 'species_economic_function_tpl.php';
		   //return $this->nextAction('species_economic_function_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objSpeciesEconomicFunction->addData($species,$economic_funcion,$abbrev,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate); 							
		return $this->nextAction('species_economic_function_admin',array('success'=>'1'));
		}
	}
	private function saveDiseasesData()
	{
		$code= $this->getParam('code');
		$name= $this->getParam('name');
		$shortname= $this->getParam('shortname');
		$ref_code = $this->getParam('ref_code');
		$desc = $this->getParam('desc');
		$inlist = $this->getParam('in_oie_list');
		$datecreated = $this->getSession('ps_calendardate',date('Y-m-d'));
		$sdate = $this->getParam('startdate');
		$edate = $this->getParam('enddate');
			$val = $this->validateDates($sdate,$edate);
      
		 if($val=='yes'){
		   $this->setVar('output', $val);
		   $this->setVar('code', $code);
			$this->setVar('name', $name);
			 $this->setVar('shortname', $shortname);
		  	 $this->setVar('refcode', $ref_code);
			$this->setVar('desc', $desc);

			return 'add_disease_tpl.php';
		  // return $this->nextAction('diseases_add',array('output'=>$val));
		 }	
		 else
		 {
		$data = $this->objDiseases->addData($code,$name,$shortname,$ref_code,$inlist,$desc,$datecreated, $this->objUser->UserName(),$sdate,$edate); 							
		return $this->nextAction('diseases_admin',array('success'=>'1'));
		}
	}	
	
	//function to add animal movement data
	private function saveAnimalMovementData()
	{
		$district = $this->getParam('district');
		$classification = $this->getParam('classification');
		$purpose = $this->getParam('purpose');
		$origin = $this->getParam('origin');
		$destination = $this->getParam('destination');
		$remarks = $this->getParam('remarks');
		$reportdate = $this->getSession('ps_calendardate',date('Y-m-d'));
		$data = $this->objAnimalmovement->addAnimalMovementData($district,$classification,$purpose,$origin,$destination,$remarks,$reportdate);  
							
		return $this->nextAction('animal_feedback',array('success'=>'1'));
	}
	
	//function to add livestock import data
	private function saveLivestockimportData()
	{
		$district = $this->getParam('district');
		$entrypoint = $this->getParam('entrypoint');

		$destination = $this->getParam('destination');
		$classification = $this->getParam('classification');
		$origin = $this->getParam('origin');
		$eggs = $this->getParam('eggs');
		$milk = $this->getParam('milk');
		$cheese = $this->getParam('cheese');
		$poultry = $this->getParam('poultry');
		$beef = $this->getParam('beef');
		$count = $this->getParam('countspecies');
	
		
		$data= $this->objLivestockimport->addLivestockimportData($district,$entrypoint,$destination,$classification,$origin,$eggs,$milk,$cheese,$poultry,$beef,$count);
							
		return $this->nextAction('animal_feedback',array('success'=>'1'));
	}
	
	//function to add livestock export data
	private function saveLivestockexportData()
	{
		$district = $this->getParam('district');
		$entrypoint = $this->getParam('entrypoint');
		$destination = $this->getParam('destination');
		$classification = $this->getParam('classification');
		$origin = $this->getParam('origin');
		$eggs = $this->getParam('eggs');
		$milk = $this->getParam('milk');
		$cheese = $this->getParam('cheese');
		$poultry = $this->getParam('poultry');
		$beef = $this->getParam('beef');
		$count = $this->getParam('countspecies');
		
		
		
		$data= $this->objLivestockexport->addLivestockexportData($district,$entrypoint,$destination,$classification,$origin,$eggs,$milk,$cheese,$poultry,$beef,$count);
														
		return $this->nextAction('animal_feedback',array('success'=>'1'));
	}
    
	//function to add animal deworming data
	private function saveAnimaldewormingData()
	{
		$district = $this->getParam('district');
		$classification = $this->getParam('classification');
		$numberofanimals = $this->getParam('numberofanimals');
		$antiemitictype = $this->getParam('antiemitictype');
		$remarks = $this->getParam('remarks');
	   $reportdate = $this->getSession('ps_calendardate',date('Y-m-d'));
		
		$data = $this->objAnimaldeworming->addAnimalDewormingData($district,$classification,$numberofanimals,$antiemitictype,$remarks,$reportdate);  
							
		return $this->nextAction('animal_feedback', array('success'=>'1'));
	}
	
	
	//function to add animal vaccine data
	private function saveAnimalvaccineData()
	{
		$district = $this->getParam('district');
		$vaccinename = $this->getParam('vaccinename');
		$doses = $this->getParam('doses');
		$doses_start = $this->getParam('dosesstartofmonth');
		$datePicker = $this->getParam('startmonth');
		$doses_end = $this->getParam('dosesendofmonth');
		$datePickerOne = $this->getParam('endmonth');
		$doses_received = $this->getParam('dosesreceived');
		$doses_used = $this->getParam('dosesused');
		$doses_wasted= $this->getParam('doseswasted');
      $reportdate = $this->getSession('ps_calendardate',date('Y-m-d'));
		 /*$val = $this->validateDate($datePicker);
        
		 if($val=='yes'){
		   return $this->nextAction('animalvaccine_add',array('output'=>$val));
		 }
		 
		 $val1 = $this->validateDate($datePickerOne);
		  if($val1=='yes'){
		   return $this->nextAction('animalvaccine_add',array('output'=>$val1));
		 }*/
		$data = $this->objAnimalvaccine->addAnimalVaccineData($district,$vaccinename,$doses,$doses_start,$datePicker,$doses_end,$datePickerOne,$doses_received,$doses_used,$doses_wasted,$reportdate);  
							
		return $this->nextAction('animal_feedback', array('success'=>'1'));
	}
	
	//function to add exchange rate data
	private function saveExchangeRateData()
	{
		$default_currency = $this->getParam('defaultcurrencyid');
		$exchange_currency = $this->getParam('exchangecurrencyid');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objExchangerate->addExchangeRateData($default_currency,$exchange_currency,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('exchangerates_admin',array('success'=>'1'));
	}
	
	//function to add exchange rate details data
	private function saveExchangeRateDetailsData()
	{
		$first_currency = $this->getParam('firstcurrency');
		$second_currency = $this->getParam('secondcurrency');
		$conversion_factor = $this->getParam('conversionfactor');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objExchangeratedetail->addExchangeRateDetailsData($first_currency,$second_currency,$conversion_factor,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker, $this->objUser->UserName());
														
		return $this->nextAction('exchangeratedetails_admin',array('success'=>'1'));
	}
    
	//function to add infection sources data
	private function saveInfectionSourcesData()
	{
		$possible_source = $this->getParam('possiblesource');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objInfectionsources->addInfectionSourceData($possible_source,$abbreviation,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('infectionsource_admin',array('success'=>'1'));
	}
	
	
	//function to add control measures data
	private function saveControlMeasureData()
	{
		$control_measure = $this->getParam('controlmeasure');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objControlmeasures->addControlMeasureData($control_measure,$abbreviation,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('controlmeasure_admin',array('success'=>'1'));
	}
	
	//function to add species new data
	private function saveSpeciesNewData()
	{
		$species_type = $this->getParam('speciestypeid');
		$species_code = $this->getParam('speciescode');
		$species_name = $this->getParam('speciesname');
		$description = $this->getParam('description');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objSpeciesNew->addSpeciesNewData($species_type,$species_code,$species_name,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('newspecies_admin',array('success'=>'1'));
	}
	
	
	//function to add species categories data
	private function saveSpeciesCategoryData()
	{
		$species_name = $this->getParam('speciesnameid');
		$category = $this->getParam('category');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objSpeciescategories->addSpeciesCategoryData($species_name,$category,$abbreviation,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('speciescategory_admin',array('success'=>'1'));
	}
	
	//function to add agents data
	private function saveAgentsData()
	{
		$agent_code = $this->getParam('agentcode');
		$agent = $this->getParam('agent');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objAgents->addAgentData($agent_code,$agent,$abbreviation,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('newagent_admin',array('success'=>'1'));
	}
	
	//function to add disease species data
	private function saveDiseaseSpeciesData()
	{
		$disease = $this->getParam('diseaseid');
		$species = $this->getParam('speciestypeid');
		$description = $this->getParam('description');
		$dateStartPicker = $this->getParam('startdate');
		$dateEndPicker = $this->getParam('enddate');
		$dateCreatedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$createdby = $this->getParam('createdby');
		$dateModifiedPicker = $this->getSession('ps_calendardate',date('Y-m-d'));
		$modifiedby = $this->getParam('modifiedby');
		
		$data= $this->objDiseasespecies->addDiseaseSpeciesData($disease,$species,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$this->objUser->UserName(),$dateModifiedPicker,$this->objUser->UserName());
														
		return $this->nextAction('newdiseasespecies_admin',array('success'=>'1'));
	}
	
	//function to add partitions
	private function savePartition()
	{
		$partitioncode = $this->getParam('partitioncode');
		$partitionname = $this->getParam('partitionname');
		$partitionlevel = $this->getParam('partitionlevel');
		$parentpartition = $this->getParam('parentpartition');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
		$val = $this->validateDates($startdate,$enddate);
				 
		$data = $this->objPartition->addPartition($partitioncode,$partitionlevel,$partitionname,$parentpartition,$startdate,$enddate);  
			
		//$this->setVar('partitions', $this->objPartition->getAll());
		return $this->nextAction('partition_view',array('level'=>$level,'parent'=>$parent,'output'=>$val));
	}
	
	//function to edit partitions
	private function updatePartition()
	{
		$id=$this->getParam('id');
		$partitioncode = $this->getParam('partitioncode');
		$partitionname = $this->getParam('partitionname');
		$partitionlevel = $this->getParam('partitionlevel');
		$parentpartition = $this->getParam('parentpartition');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objPartition->editPartition($id,$partitioncode,$partitionlevel,$partitionname,$parentpartition,$startdate,$enddate);  
			
		//$this->setVar('partitions', $this->objPartition->getAll());
		return $this->nextAction('partition_view',array('level'=>$level,'parent'=>$parent));
	}

	//function to delete partitions
	private function delPartition()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objPartition->deletePartition($id);  
			
		//$this->setVar('partitions', $this->objPartition->getAll());
		return $this->nextAction('partition_view',array('level'=>$level,'parent'=>$parent));
	}

	//function to add partition levels
	private function savePartitionLevel()
	{
		$partitioncategory = $this->getParam('partitioncategory');
		$partitionlevel = $this->getParam('partitionlevel');
		$description = $this->getParam('description');
				 
		$data = $this->objPartitionLevel->addPartitionLevel($partitioncategory,$partitionlevel,$description);  
					  
		$this->setVar('partitionlevels', $this->objPartitionLevel->getAll());
		return $this->nextAction('partitionlevel_view');
	}
      
	//function to edit partition Levels
	private function updatePartitionLevel()
	{
		$id=$this->getParam('id');
		$partitionlevel = $this->getParam('partitionlevel');
		$description = $this->getParam('description');
				 
		$data = $this->objPartitionLevel->editPartitionLevel($id,$partitioncategory,$partitionlevel,$description);  

		$this->setVar('partitionlevels', $this->objPartitionLevel->getAll());				
		return $this->nextAction('partitionlevel_view');
	}

      //function to delete partition Levels
	private function delPartitionLevel()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objPartitionLevel->deletePartitionLevel($id);  

		$this->setVar('partitionlevels', $this->objPartitionLevel->getAll());				
		return $this->nextAction('partitionlevel_view');
	}

	//function to add partition Categories
	private function savePartitionCategory()
	{
		$partitioncategory = $this->getParam('partitioncategory');
		$description = $this->getParam('description');
				 
		$data = $this->objPartitionCategory->addPartitionCategory($partitioncategory,$description);  

		$this->setVar('partitioncategories', $this->objPartitionCategory->getAll());				
		return $this->nextAction('partitioncategory_view');
	}

	//function to edit partition Categories
	private function updatePartitionCategory()
	{
		$id=$this->getParam('id');
		$partitioncategory = $this->getParam('partitioncategory');
		$description = $this->getParam('description');
				 
		$data = $this->objPartitionCategory->editPartitionCategory($id,$partitioncategory,$description);  

		$this->setVar('partitioncategories', $this->objPartitionCategory->getAll());				
		return $this->nextAction('partitioncategory_view');
	}

	//function to delete partition Categories
	private function delPartitionCategory()
	{
		$id=$this->getParam('id');
		
		$data = $this->objPartitionCategory->deletePartitionCategory($id);  

		$this->setVar('partitioncategories', $this->objPartitionCategory->getAll());				
		return $this->nextAction('partitioncategory_view');
	}

	//function to add farming systems
	private function saveFarmingSystem()
	{
		$farmingsystem = $this->getParam('farmingsystem');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objFarmingSystem->addFarmingSystem($farmingsystem,$abbreviation,$description,$startdate,$enddate);  
			
		$this->setVar('farmingsystems', $this->objFarmingSystem->getAll());
		return $this->nextAction('farmingsystem_view');
	}

	//function to edit farming systems
	private function updateFarmingSystem()
	{
		$id=$this->getParam('id');
		$farmingsystem = $this->getParam('farmingsystem');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objFarmingSystem->editFarmingSystem($id,$farmingsystem,$abbreviation,$description,$startdate,$enddate);  
			
		$this->setVar('farmingsystems', $this->objFarmingSystem->getAll());
		return $this->nextAction('farmingsystem_view');
	}

	//function to delete farming systems
	private function delFarmingSystem()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objFarmingSystem->deleteFarmingSystem($id);  
			
		$this->setVar('farmingsystems', $this->objFarmingSystem->getAll());
		return $this->nextAction('farmingsystem_view');
	}

	//function to add species types
	private function saveSpeciesType()
	{
		$speciestype = $this->getParam('speciestype');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objSpeciesType->addSpeciesType($speciestype,$abbreviation,$description,$startdate,$enddate);  
			
		$this->setVar('speciestypes', $this->objSpeciesType->getAll());
		return $this->nextAction('speciestype_view');
	}
  
	//function to edit species types
	private function updateSpeciesType()
	{
		$id=$this->getParam('id');
		$speciestype = $this->getParam('speciestype');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objSpeciesType->editSpeciesType($id,$speciestype,$abbreviation,$description,$startdate,$enddate);  
			
		$this->setVar('speciestypes', $this->objSpeciesType->getAll());
		return $this->nextAction('speciestype_view');
	}

	//function to delete species types
	private function delSpeciesType()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objSpeciesType->deleteSpeciesType($id);  
			
		$this->setVar('speciestypes', $this->objSpeciesType->getAll());
		return $this->nextAction('speciestype_view');
	}

	//function to add species Age Groups
	private function saveSpeciesAgeGroup()
	{
		$speciesid = $this->getParam('speciesid');
		$agegroup = $this->getParam('agegroup');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$lowerlimit = $this->getParam('lowerlimit');
		$upperlimit = $this->getParam('upperlimit');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objSpeciesAgeGroup->addSpeciesAgeGroup($speciesid,$agegroup,$abbreviation,$description,$lowerlimit,$upperlimit,$startdate,$enddate);  
			
		$this->setVar('speciesagegroups', $this->objSpeciesAgeGroup->getAll());
		return $this->nextAction('speciesagegroup_view');
	}

	//function to edit species Age Groups
	private function updateSpeciesAgeGroup()
	{
		$id=$this->getParam('id');
		$speciesid = $this->getParam('speciesid');
		$agegroup = $this->getParam('agegroup');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
		$lowerlimit = $this->getParam('lowerlimit');
		$upperlimit = $this->getParam('upperlimit');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objSpeciesAgeGroup->editSpeciesAgeGroup($id,$speciesid,$agegroup,$abbreviation,$description,$lowerlimit,$upperlimit,$startdate,$enddate);  
			
		$this->setVar('speciesagegroups', $this->objSpeciesAgeGroup->getAll());
		return $this->nextAction('speciesagegroup_view');
	}

	//function to delete species Age Groups
	private function delSpeciesAgeGroup()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objSpeciesAgeGroup->deleteSpeciesAgeGroup($id);  
			
		$this->setVar('speciesagegroups', $this->objSpeciesAgeGroup->getAll());
		return $this->nextAction('speciesagegroup_view');
	}

	//function to add species tropical livestock unit
	private function saveSpeciesTropicalLivestockUnit()
	{
		$speciesname = $this->getParam('speciesname');
		$speciescategory = $this->getParam('speciescategory');
		$tlufactor = $this->getParam('tlufactor');
		$remarks = $this->getParam('remarks');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objSpeciesTropicalLivestockUnit->addSpeciesTropicalLivestockUnit($speciesname,$speciescategory,$tlufactor,$remarks,$startdate,$enddate);  
			
		$this->setVar('speciestropicallivestockunits', $this->objSpeciesTropicalLivestockUnit->getAll());
		return $this->nextAction('speciestropicallivestockunit_view');
	}
	
	//function to edit species tropical livestock unit
	private function updateSpeciesTropicalLivestockUnit()
	{
		$id=$this->getParam('id');
		$speciesname = $this->getParam('speciesname');
		$speciescategory = $this->getParam('speciescategory');
		$tlufactor = $this->getParam('tlufactor');
		$remarks = $this->getParam('remarks');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objSpeciesTropicalLivestockUnit->editSpeciesTropicalLivestockUnit($id,$speciesname,$speciescategory,$tlufactor,$remarks,$startdate,$enddate);  
			
		$this->setVar('speciestropicallivestockunits', $this->objSpeciesTropicalLivestockUnit->getAll());
		return $this->nextAction('speciestropicallivestockunit_view');
	}

	//function to delete species tropical livestock unit
	private function delSpeciesTropicalLivestockUnit()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objSpeciesAgeGroup->deleteSpeciesAgeGroup($id);  
			
		$this->setVar('speciestropicallivestockunits', $this->objSpeciesTropicalLivestockUnit->getAll());
		return $this->nextAction('speciestropicallivestockunit_view');
	}

	//function to add occurence codes
	private function saveOccurenceCode()
	{
		$occurencecode = $this->getParam('occurencecode');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
				 
		$data = $this->objOccurenceCode->addOccurenceCode($occurencecode,$abbreviation,$description);  

		$this->setVar('occurencecodes', $this->objOccurenceCode->getAll());				
		return $this->nextAction('occurencecode_view');
	}
	
	//function to edit occurence codes
	private function updateOccurenceCode()
	{
		$id=$this->getParam('id');
		$occurencecode = $this->getParam('occurencecode');
		$abbreviation = $this->getParam('abbreviation');
		$description = $this->getParam('description');
				 
		$data = $this->objOccurenceCode->editOccurenceCode($id,$occurencecode,$abbreviation,$description);  

		$this->setVar('occurencecodes', $this->objOccurenceCode->getAll());				
		return $this->nextAction('occurencecode_view');
	}

	//function to delete occurence codes
	private function delOccurenceCode()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objOccurenceCode->deleteOccurenceCode($id);  

		$this->setVar('occurencecodes', $this->objOccurenceCode->getAll());				
		return $this->nextAction('occurencecode_view');
	}

	//function to add disease agent
	private function saveDiseaseAgent()
	{
		$disease = $this->getParam('disease');
		$agent = $this->getParam('agent');
		$description = $this->getParam('description');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objDiseaseAgent->addDiseaseAgent($disease,$agent,$description,$startdate,$enddate);  

		$this->setVar('diseaseagents', $this->objDiseaseAgent->getAll());				
		return $this->nextAction('diseaseagent_view');
	}
      
	//function to edit disease agent
	private function updateDiseaseAgent()
	{
		$id=$this->getParam('id');
		$disease = $this->getParam('disease');
		$agent = $this->getParam('agent');
		$description = $this->getParam('description');
		$startdate = $this->getParam('startdate');
		$enddate = $this->getParam('enddate');
				 
		$data = $this->objDiseaseAgent->editDiseaseAgent($id,$disease,$agent,$description,$startdate,$enddate);  

		$this->setVar('diseaseagents', $this->objDiseaseAgent->getAll());				
		return $this->nextAction('diseaseagent_view');
	}

	//function to delete disease agent
	private function delDiseaseAgent()
	{
		$id=$this->getParam('id');
				 
		$data = $this->objDiseaseAgent->deleteDiseaseAgent($id);  

		$this->setVar('diseaseagents', $this->objDiseaseAgent->getAll());				
		return $this->nextAction('diseaseagent_view');
	}

	/**
	*Method to validate date to ensure that no future date is added
	*
	*/
	
	private function validateDate($dateval){

	 $currentyear = date('Y');
      $currentmonth = date('m');
      $currentday = date('d');
       $dist =split("-", $dateval);
      if($currentyear < $dist[0]){
	
 
      $next = 'yes';

      return $next;
      }else
      if($currentyear == $dist[0])
        {
        if($currentmonth<$dist[1]){
          
     
      $next = 'yes';

      return $next;
        
      }

      if($currentday < $dist[2]){
     
      $next = 'yes';

      return $next;
      }
      }
	
	}
	private function validateDates($sdate,$edate){

	
	  $d1=split("-", $sdate);
       $d2 =split("-", $edate);
      if($d2[0] < $d1[0]){
	
 
      $next = 'yes';

      return $next;
      }else
      if($d2[0] == $d1[0])
        {
        if($d2[1]<$d1[1]){
          
     
      $next = 'yes';

      return $next;
        
      }

      if($d2[2]< $d1[2]){
     
      $next = 'yes';

      return $next;
      }
      }
	
	}
	
	    private function unsetVaccineInventory1() {
                $this->unsetSession('ps_repOfficerId');
	             $this->unsetSession('ps_dataoff');
	             $this->unsetSession('ps_vetoff');
	             $this->unsetSession('ps_repdate');
	             $this->unsetSession('ps_ibardate');
	             $this->unsetSession('ps_country');
	             $this->unsetSession('ps_month');
	             $this->unsetSession('ps_year');
	             $this->unsetSession('ps_admin1');
	             $this->unsetSession('ps_admin2');
	             $this->unsetSession('ps_admin3');
	             $this->unsetSession('ps_loctype');
	             $this->unsetSession('ps_locname');
	             $this->unsetSession('ps_lattitude');
	             $this->unsetSession('ps_longitude');
	             $this->unsetSession('ps_dphone');
	             $this->unsetSession('ps_demail');
	             $this->unsetSession('ps_dfax');
	             $this->unsetSession('ps_vphone');
	             $this->unsetSession('ps_vemail');
	             $this->unsetSession('ps_vfax');	    
	             $this->unsetSession('ps_rphone');
	             $this->unsetSession('ps_remail');
	             $this->unsetSession('ps_rfax');	    
	    
	    
	    
	    }
	    
	     private function unsetVaccineInventory2() {
                $this->unsetSession('ps_outrefno');
	             $this->unsetSession('ps_disease');
	             $this->unsetSession('ps_species');
	             $this->unsetSession('ps_vacsource');
	             $this->unsetSession('ps_lotno');
	             $this->unsetSession('ps_mandate');
	             $this->unsetSession('ps_expdate');
	             $this->unsetSession('ps_planprovac');
	             $this->unsetSession('ps_condprovac');
	             $this->unsetSession('ps_cumprovac');
	             $this->unsetSession('ps_planconvac');
	             $this->unsetSession('ps_condconvac');
	             $this->unsetSession('ps_cumconvac');
	             $this->unsetSession('ps_comments');  
	    
	    
	    
	    }
  private function unsetAnimalpopulation() {
                $this->unsetSession('ps_repOfficerId');
	             $this->unsetSession('ps_dataoff');
	             $this->unsetSession('ps_vetoff');
	             $this->unsetSession('ps_repdate');
	             $this->unsetSession('ps_ibardate');
	             $this->unsetSession('ps_country');

	             $this->unsetSession('ps_year');
	             $this->unsetSession('ps_admin1');
	             $this->unsetSession('ps_admin2');
	             $this->unsetSession('ps_admin3');
	             $this->unsetSession('ps_loctype');
	             $this->unsetSession('ps_locname');
	             $this->unsetSession('ps_lattitude');
	             $this->unsetSession('ps_longitude');
	             $this->unsetSession('ps_rphone');
	             $this->unsetSession('ps_remail');
	             $this->unsetSession('ps_rfax');
	             $this->unsetSession('ps_dphone');
	             $this->unsetSession('ps_demail');
	             $this->unsetSession('ps_dfax');
	             $this->unsetSession('ps_vphone');
	             $this->unsetSession('ps_vemail');
	             $this->unsetSession('ps_vfax');	
	             $this->unsetSession('ps_species'); 
	             $this->unsetSession('ps_breed');
	             $this->unsetSession('ps_prodname');       
	    	           	    
	    }
    /**
     * Method to determine whether the user needs to be logged in
     * 
     * @return boolean TRUE|FALSE 
     * @access public 
     */
     
     public function requiresLogin() {
            return FALSE;
     }
}
