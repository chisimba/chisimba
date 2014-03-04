<?php
/* -------------------- lrspostlogin class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check
/**
* Default class to handle what happens after the user logs in.
* The post login module can be set by changing the constant
* KEWL_POSTLOGIN_MODULE from 'postlogin' to the name of any other
* module.
*
* @author Nic Appleby
*/
class award extends controller
{
	var $objUser;
	var $objLanguage;
	var $objConfig;
	var $objLayers;
	var $objModule;
	var $objDbRegion;
	var $objTemplates;
	var $objDbJobCodes;

	var $adminactions = array('admin', 'continuity', 'start', 'ajax_updateunitlist',
							  'createbu', 'yes', 'no', 'createagreement', 'ajax_updatepblist',
							  'ajax_updatesicdivlist', 'ajax_updatesicmajgrplist' ,'ajax_updatesicgrplist',
							  'ajax_updatesicmajgrplist', 'ajax_updatesicgrplist', 'ajax_updatesicsubgrplist',
							  'add', 'addagreement', 'deletewage', 'addwage', 'insertwage', 'updatewage',
							  'startindex', 'addoreditindex', 'insertindexvalue', 'ajax_indexvalue',
							  'addindex', 'insertindex', 'setgooglesearch', 'submitgooglesettings', 'search',
							  'viewgender', 'editgendercat', 'editgenderrow', 'viewgender', 'viewbenefittype',
							  'addeditbenefitname', 'addeditbenefittype', 'savebenefittype', 'savebenefitname',
							  'viewtradeunion', 'viewbranch', 'deletetradeunion', 'addedittradeunion',
							  'addtradeunion', 'edittradeunion', 'deletebranch', 'confirmdeletetradeunion',
							  'edituser', 'deleteuser', 'viewuserlist', 'saveuser', 'editmenu',
							  'ajaxupdatemenuitem', 'viewblurb', 'editblurb', 'updateblurb', 'decentworkadmin',
							  'selectmajorgroup', 'savemajorgroup', 'editminorgroup', 'selectmajordiv',
							  'addeditcategory', 'editcategory', 'editadddecemtwork', 'editdecentwork',
							  'deletedecentrow', 'deletedecentcategory', 'editbargainingunit',
							  'updatebargainingunit', 'agreementoverview', 'bargainingunitoverview',
							  'deleteagreement', 'savegendercat', 'savegenderrow', 'savenewgenderrow',
							  'addnewgender', 'addeditmajordiv', 'adddiv', 'addeditdiv', 'editdiv',
							  'deletediv', 'selectsicmajorgroup', 'selectsicdiv', 'addeditmajorgroup',
							  'selectsicgroup', 'selectsubmajorgroup', 'saveminorgroup', 'selectminorgroup',
							  'saveunitgroup', 'selectunitgroup', 'savesocname', 'selectsocname',
							  'savesubmajorgroup', 'deletesubmajorgroup', 'editsubmajorgroup',
							  'editmajorgroup', 'addmajorgroup', 'addeditgroup', 'editSocmajorgroup',
							  'editunitgroup', 'selectsicsubgroup', 'editgroup', 'addeditsubgroup',
							  'addgroup', 'editmajdiv', 'addmajdiv', 'addeditsubgroup', 'addsubgroup',
							  'editsubgroup', 'addeditbranch', 'viewbargainingunit', 'editbranch', 'addbranch',
							  'confirmdeletebranch', 'add', 'edit', 'wage', 'deletesocname', 'editsocname',
							  'editagreement', 'editwage', 'conditions_admin', 'save_conditions', 'export',
							  'dataexport');

	/**
    * init method to instantiate the class
    */
	public function init() {
		/************* Utility Classes *****************/
	    $this->objUser = $this->getObject('user', 'security');
	    $this->objModule = $this->getObject('modulesadmin', 'modulecatalogue');
	    $this->objButtons = $this->getObject('navbuttons', 'navigation');
	    $this->objLanguage = $this->getObject('language', 'language');
	    $this->objConfig = $this->getObject('altconfig', 'config');
       	$this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

		/*********** Database Access Classes **************/
	    $this->objDbPayPeriodType = $this->getObject('dbpayperiodtypes', 'awardapi');
	    $this->objDbRegion = $this->getObject('dbregion','awardapi');
	    $this->objAgree = $this->getObject('dbagreement','awardapi');
	    $this->objDbSicMajorDivs = $this->getObject('dbsicmajordiv', 'awardapi');
        $this->objDbSicMajorGroups = $this->getObject('dbsicmajorgroup', 'awardapi');
       	$this->objDbSicDivs = $this->getObject('dbsicdiv', 'awardapi');
       	$this->objDbSicGroups = $this->getObject('dbsicgroup', 'awardapi');
       	$this->objDbSicSubGroups = $this->getObject('dbsicsubgroup', 'awardapi');
       	$this->objUnitSic = $this->getObject('dbunitsic','awardapi');
       	$this->objDbSocMajorGroup = $this->getObject('dbsocmajorgroup', 'awardapi');
        $this->objDbSubMajorGroups = $this->getObject('dbsocsubmajorgroup','awardapi');
	    $this->objDbUnitGroups = $this->getObject('dbsocunitgroup','awardapi');
       	$this->objDbUnit = $this->getObject('dbunit','awardapi');
	    $this->objDbWages = $this->getObject('dbwage', 'awardapi');
	    $this->objDbAgreeType = $this->getObject('dbagreetypes', 'awardapi');
	    $this->objdecentWorkValues = $this->getObject('dbdwvalues', 'awardapi');
	    $this->objdecentWorkCategory = $this->getObject('dbdwcategories', 'awardapi');
	    $this->objDbParty = $this->getObject('dbparty','awardapi');    
        $this->objDbBranch = $this->getObject('dbbranch','awardapi');
       	$this->objIndexes = $this->getObject('dbindex','awardapi');
   		$this->objIndexValues = $this->getObject('dbindexvalues', 'awardapi');
      	$this->objBenefitType = $this->getObject('dbbenefittypes','awardapi');
	    $this->objBenefit = $this->getObject('dbbenefits','awardapi');
   		$this->objBenefitNames = $this->getObject('dbbenefitnames','awardapi');
       	$this->objUnitBranch = $this->getObject('dbunitbranch','awardapi');
       	$this->objArea = $this->getObject('dbunitregion','awardapi');
   		$this->objDbUnitBranches = $this->getObject('dbunitbranch', 'awardapi');
		$this->objDistrict = $this->getObject('dbdistrict', 'awardapi');
       	$this->objRegion = $this->getObject('dbregion', 'awardapi');
       	$this->objDbSocNames = $this->getObject('dbsocname','awardapi');
		$this->objwageSocName = $this->getObject('dbwagesocname', 'awardapi');
        $this->objDbMinorGroups = $this->getObject('dbsocminorgroup','awardapi');
		
	    /************** Other Award Classes ***********/
	    $this->objBCEA = $this->getObject('dbbcea','award');
		$this->orgFacet = $this->getObject('awardorg_facet', 'award');
	    $this->indexFacet = $this->getObject('awardindex_facet', 'award'); 
        $this->objAjax = $this->getObject('ajax_methods','award');
	    $this->objTemplates = $this->getObject('awardtemplates', 'award');
	    $this->objSummary = $this->getObject('dbindexsummary','award');
		$this->lrsNav = $this->getObject('navigation','award');
		$this->objDataExport = $this->getObject('dataexport', 'award');
    
	    $this->userId = $this->objUser->userId();
	    
		$this->setLayoutTemplate('layout_tpl.php');
	    //$this->setPageTemplate('award_page_tpl.php');
		
	}

	/**
     * Overloaded method to determine whether or not the user must be logged in to use this module
     *
     * @return FALSE
     */
	public function requiresLogin() {
		$action = $this->getParam('action');
		if (in_array($action, $this->adminactions)) {
			return true;
		}
		
		switch ($action) {
			case 'ajaxtutree':
			case 'tradeunion':
				return TRUE;
			default:
				return FALSE;
		}
	}

	/**
    * Dispatch method to return the template populated with
    * the output
    */
	public function dispatch() {
		$action = $this->getParam('action',$this->getParam('amp;action'));
		$this->lrsNav->selected = $this->getParam('selected','init_01');
		if (in_array($action, $this->adminactions) && !$this->objUser->isAdmin()) {
			return 'notadmin_tpl.php';
		}
		// Now the main switch statement to pass values for $action
		switch($action) {
			case 'tmp':
				set_time_limit(0);
				$tmp = $this->objTemplates->getSocWageAggregates('min','all','2002');
				echo $tmp;
				die;
				
				return 'xmlgraph_tpl.php';
				break;
			
			case 'populate_workers':
				$sql = "SELECT * FROM tbl_award_agree WHERE workers = 0 OR workers IS NULL";
				$agrees = $this->objAgree->getArray($sql);
				echo count($agrees)."<br />";
				$updates = 0;
				foreach ($agrees as $agree) {
					if ($oldId = $this->objAgree->getPreviousAgreementId($agree['id'],$agree['implementation'])) {
						$oldAgree = $this->objAgree->getRow('id', $oldId);
						if ($oldAgree['workers'] != 0) {
							$this->objAgree->update('id', $agree['id'], array('workers' => $oldAgree['workers']));
							$updates += 1;												  
						}
					}
				}
				echo $updates;
				
				break;
			
			case 'import_old_db':
				if (!$this->objImport->doImport($this->getParam('dbname'))) {
					echo "Error: ".$this->objImport->getError();
				} else {
					echo "Done";
				}
        		break;

				
			case 'default_hours':
				$sql = "SELECT *
						FROM `tbl_award_agree`
						WHERE id NOT IN (
							SELECT agree.id
							FROM tbl_award_agree AS agree, tbl_award_benefits AS benefits
							WHERE agree.id = benefits.agreeid AND nameid = 'init_7'
						)";
				$r = $this->objAgree->getArray($sql);
				echo count($r);
				echo "<br />";
				foreach ($r as $agree) {
					$imp = strtotime($agree['implementation']);
					$year = date('Y',$imp);
					switch ($year) {
						case '1995':
							$val = "44.6";
							break;
						case '1996':
							$val = "46";
							break;
						case '1997':
							$val = "43.4";
							break;
						case '1998':
							$val = "44.42";
							break;
						case '1999':
							$val = "42.26";
							break;
						case '2000':
							$val = "43.1";
							break;
						case '2001':
							$val = "44.15";
							break;
						case '2002':
							$val = "44.05";
							break;
						case '2003':
							$val = "43.96";
							break;
						case '2004':
							$val = "43.9";
							break;
						case '2005':
							$val = "43.45";
							break;
						case '2006':
							$val = "43";
							break;
						case '2007':
							$val = "44";
							break;
						case '2008':
							$val = "43.2";
							break;
						default:
							$val = "44";
							break;
					}
					echo "$year $val {$agree['name']} <br />";
					$record = array('agreeid' => $agree['id'],
									'nameid' => 'init_7',
									'value' => $val,
									'notes' => 'Default value for year used as data not available'
									);
					$this->objBenefit->insert($record);
				}
				
				break;
			
			case 'printpdf':
			    $banner = "<img src='".$this->objConfig->getskinRoot().$this->objConfig->getdefaultSkin()."/banners/largebanner.jpg' width='800' height='82'><br />";
			    $title = $this->getParam('title');
                $content = "<html><head><title>$title</title></head><body>$banner".$this->getSession('award_print_content')."</body></html>";
                echo html_entity_decode($content);
                break;

            case 'samplelist':
				$this->setVar('pageSuppressToolbar', TRUE);
        	    $this->setVar('pageSuppressNav', TRUE);
        	    $this->lrsNav->selected = 'init_10';
		        $year = $this->getParam('year');
        		$sicId = $this->getParam('sic');
        		$socId = $this->getParam('soc');
        		$agreeTypeId = $this->getParam('agreeTypeId');
        		$wageTypeId = $this->getParam('wageTypeId');
        		$mode = $this->getParam('mode');
        		$this->setVar('year',$year);
        		$this->setVar('wageTypeId',$wageTypeId);
        		$this->setVar('sicId',$sicId);
        		$this->setVar('socId',$socId);
        		$this->setVar('agreeId',$agreeTypeId);
        		$this->setVar('aggregate',$mode);
				return "unitlist_tpl.php";

			case 'feedback':
				$buid = $this->getParam('buid');
			    $this->setVar('buid',$buid);
			    return 'feedback_tpl.php';
			case 'submitfeedback':
			    $this->sendFeedbackEmail();
				return $this->nextAction('selectbu',array('selected'=>'init_02','unit'=>$this->getParam('buid'),'submitted'=>1));
				
			case 'selectpp':
				$this->setSession('pay_period_type_id', $this->getParam('ppTypeId'), 'award');
				return $this->nextAction('home', array('selected'=>'init_01'));
				
			case 'admin':
				if (!$this->objUser->isAdmin()) {
					return 'notadmin_tpl.php';
				} else {
					$this->lrsNav->selected='init_10';
					return 'admin_tpl.php';
				}
				break;

			case 'wages':
			    $defaultSoc = $this->objDbSocMajorGroup->getRow('description','Elementary Occupations');
    			$socMajGrpId = $this->getParam('socMajGrpId',$defaultSoc['id']);
    			$this->setVarByRef('socMajGrpId', $socMajGrpId);
    			return 'wages_tpl.php';

            case 'ajax_updateaggregates':
                $sicId = $this->getParam('sicId');
                $indexId = $this->getParam('indexId');
                $aggregate = $this->getParam('aggregate');
                $socId = $this->getParam('socId');
                $wageTypeId = $this->getParam('wageTypeId');
                $agreeTypeId = $this->getParam('agreeTypeId');
                $startYear = $this->getParam('startYear');
                $years = $this->getParam('years');
                $minorSic = $this->getParam('minorSic','all');
                $subSic = $this->getParam('subSic','all');
                echo $this->objTemplates->generateAggregates($sicId, $indexId, $aggregate, $socId, $wageTypeId, $agreeTypeId, $startYear, $years, $minorSic, $subSic);
                break;
            
            case 'ajax_updatesicaggregates':
                $sicArray = explode('|',$this->getParam('sicList'));
                $agreeTypeId = $this->getParam('agreeTypeId');
                $aggregate = $this->getParam('aggregate');
                $socId = $this->getParam('socId');
                $wageTypeId = $this->getParam('wageTypeId');
                $startYear = $this->getParam('startYear');
                $period = $this->getParam('period');
                echo $this->objTemplates->getIndustryWageAggregates($sicArray,$socId,$aggregate,$agreeTypeId,$wageTypeId,$period,$startYear);
                break;
    
	        case 'ajax_updatesocaggregates':
                $agreeTypeId = $this->getParam('agreeTypeId');
                $aggregate = $this->getParam('aggregate');
                $year = $this->getParam('year');
                $industry = $this->getParam('industry');
                $subSic = $this->getParam('subsic');
                $socText = $this->getParam('socText');
                echo $this->objTemplates->getSocWageAggregates($aggregate, $agreeTypeId, $year, $socText, $industry, $subSic);
                break;
            
			case 'xmlgraph':
				$this->setVar('pageSuppressToolbar', TRUE);
				$this->setVar('pageSuppressNav', TRUE);
				$this->setVar('title', $this->getParam('title'));
				$this->setVar('start', $this->getParam('start'));
				$this->setVar('period', $this->getParam('period'));
				$this->setVar('data', unserialize($this->getParam('data')));
				return 'xmlgraph_tpl.php';
			
			case 'graphcontent':
				$this->setLayoutTemplate(null);
				$this->setPageTemplate(null);
				$this->setVar('pageSuppressToolbar', TRUE);
				$this->setVar('pageSuppressNav', TRUE);
				$this->setVar('title', $this->getParam('title'));
				$this->setVar('data', $this->getParam('arrData'));
				return 'graphtemplate_tpl.php';
				
    		case 'gender':
    		    $defaultSoc = $this->objDbSocMajorGroup->getRow('description','Elementary Occupations');
    			$this->setVar('sicId',$this->getParam('sicSelect','all'));
			    $this->setVar('aggregate',$this->getParam('modeSelect','med'));
			    $this->setVar('agreeTypeId',$this->getParam('agreeSelect','all'));
			    $this->setVar('year',$this->getParam('startYear',date('Y')-1));
			    return 'gender_tpl.php';

			case 'inflation':
			    $indexId = $this->getParam('indexid',1);
            	$startYear = $this->getParam('startYear',date('Y')-5);
            	$this->setVar('startYear', $startYear);
            	$this->setVar('indexId', $indexId);
			    return 'inflation_tpl.php';

            case 'ajax_saveinflationsummary':
                // save summary to db
                $indexId = $this->getParam('indexId');
                $summary = $this->getParam('summary');
                if ($this->objSummary->valueExists('indexid',$indexId)) {
                    $this->objSummary->update('indexid',$indexId,array('summary'=>$summary));
                } else {
                    $this->objSummary->insert(array('indexid'=>$indexId,'summary'=>$summary));
                }
                // fall through to next action
    
	        case 'ajax_updateInflation':
                $id = $this->getParam('indexId');
                echo $this->objTemplates->getInflationSummary($id);
                break;

            case 'plotinflationgraph':
				$this->setLayoutTemplate(null);
				$this->setPageTemplate(null);
				$arrData = $this->indexFacet->getOrderedPairs($this->getParam('indexId'),$this->getParam('year'));
				$this->setVarByRef('arrData',$arrData);
            	return 'graph_tpl.php';

			case 'tradeunion': 
				if ($this->objUser->isAdmin()) {
					$unionIndex = $this->getParam('tuIndex');
					if (isset($unionIndex)) {
						$tu['id'] = $unionIndex;
					} else {
						$tu = current($this->objDbParty->getAll('ORDER BY abbreviation ASC'));
					}
				} else {
					$objDbUserReg = $this->getObject('dbuserreg','award');
					$userTu = $objDbUserReg->getRow('userid',$this->objUser->userId());
					if (is_array($userTu)) {
						$tu['id'] = $userTu['tuid'];
					} else {
						return 'notu_tpl.php';
					}
				}
				$defaultSoc = $this->objDbSocMajorGroup->getRow('description','Elementary Occupations');
                $this->setVar('tuIndex',$tu['id']);
                $this->setVar('threshold',$this->getParam('threshold'));
                $this->setVar('indexId',$this->getParam('indexId',1));
                $this->setVar('year',$this->getParam('year',date('Y')));
                $this->setVar('socId',$this->getParam('socId',$defaultSoc['id']));
                $this->setVar('agreeTypeId',$this->getParam('agreeTypeId','all'));
                $this->setVar('mode',$this->getParam('mode'));
                return 'tradeunion_tpl.php';
    
	        case 'ajaxtutree':
                $tuId = $this->getParam("tuId");
                $root = $this->getParam("root");
                echo $this->objAjax->lazyTree($tuId, $root);
                break;

			case 'continuity':
				$length = $this->getParam('length');
                $industry = $this->getParam('industry');
                $soc = $this->getParam('soc');
                $minorSic = $this->getParam('minorSic');
                $wageTypeId = $this->getParam('wageTypeId');
                $agreeId = $this->getParam('agreeId');
                $mode = $this->getParam('mode');
            	$subSicId = $this->getParam('subsicid');
            	$this->setVarByRef('mode',$mode);
            	$this->setVarByRef('length',$length);
            	$this->setVarByRef('startYear',$this->getParam('startYear',date('Y')-($length-1)));
            	$this->setVarByRef('industry',$industry);
            	$this->setVarByRef('soc',$soc);
            	$this->setVarByRef('minorSic',$minorSic);
            	$this->setVarByRef('wageTypeId',$wageTypeId);
            	$this->setVarByRef('subSicId',$subSicId);
            	$this->setVarByRef('agreeId',$agreeId);
            	$this->setVarByRef('indexId',$this->getParam('indexId'));
            	$this->setVarByRef('sortType',$this->getParam('sortType','count'));
       			return 'continuity_report_tpl.php';

       		case 'conditions':
                $defaultYear = $this->getParam('year', date('Y')-1);
                $agreeTypeId = $this->getParam('agreetypeid','all');
                $sicId = $this->getParam('sicid','all');
                $subSic = $this->getParam('subSic','all');
				$aggregate = $this->getParam('aggregate','avg');
                $benefitTypeId = $this->getParam("tab_number",null);
                $this->setVar('defaultYear',$defaultYear);
                $this->setVar('agreeTypeId',$agreeTypeId);
                $this->setVar('sicId',$sicId);
                $this->setVar('subSic',$subSic);
                $this->setVar('aggregate',$aggregate);
                $this->setVar('benefitTypeId',$benefitTypeId);
                return 'conditions_tpl.php';
			
			case 'export':
				return 'export_tpl.php'; 
			
			case 'dataexport':
				set_time_limit(0);
				$type = $this->getParam('type');
				$yearString = $this->getParam('years');
				$tmpYearString = str_replace(' ', '', $yearString);
				$years = explode(',', $tmpYearString);
				//$yearString = str_replace(',', '+', $tmpYearString);
				if ($type == 'conditions') {
					$data = $this->objDataExport->exportConditions($years);
					$fileName = "AWARD Conditions for {$yearString} on ".date('Y-m-d').".csv";
				} else {
					$indexId = $this->getParam('indexId');
					$data = $this->objDataExport->exportWages($years, $indexId);
					$fileName = "AWARD Wages for {$yearString} on ".date('Y-m-d').".csv";
				}
				$this->setVar('data', $data);
				$this->setVar('fileName', $fileName);
				$this->setLayoutTemplate('');
				$this->setPageTemplate('');
				$this->setVar('pageSuppressSkin', TRUE);
				$this->setVar('pageSuppressHeader', TRUE);
				$this->setVar('pageSuppressToolbar', TRUE);
				$this->setVar('pageSuppressNav', TRUE);
				return 'dataexport_tpl.php';

			case 'home':
			    $defaultSoc = $this->objDbSocMajorGroup->getRow('description','Elementary Occupations');
            	$year = $this->getParam('year',date('Y')-3);
				$agreeTypeId = $this->getParam('agreeTypeId','all');
				$socId = $this->getParam('socid',$defaultSoc['id']);
				$sampleTypeId = $this->getParam('sampleTypeId','3');
				$this->setVar('year',$year);
				$this->setVar('agreeTypeId',$agreeTypeId);
				$this->setVar('socId',$socId);
				$this->setVar('sampleTypeId',$sampleTypeId);
                return 'home_tpl.php';

			case 'agreement':
			    return 'agreement_tpl.php';
	
			case 'ajax_unitdropdowntext':
				echo $this->objAjax->getUnitDropdownText($this->getParam('search'));
				break;
	
			case 'ajax_unitdropdownsic':
				$sic = $this->getParam('sic');
				$sicDiv = $this->getParam('sicDiv');
				$agreeType = $this->getParam('agreeType');
				echo $this->objAjax->getUnitDropdownSic($sic, $sicDiv, $agreeType);
				break;
	
			case 'ajax_updatesicdiv':
				$sicId = $this->getParam('sicId');
				echo $this->objAjax->updateSicDiv($sicId);
				break;
	
			case 'selectbu':
				$this->setVar('unitId',$this->getParam('unit'));
				return "budata_tpl.php";
    
	        case 'ajax_updateconditions':
                $selectedTab = $this->getParam('selectedTab');
                $agreeId = $this->getParam('agreeId');
                echo $this->objTemplates->getAgreeConditions($agreeId,$selectedTab);
                break;
			    
			case 'decentwork':
				$this->lrsNav->selected='init_06';
				$currentYr = $this->getParam('updateYear');
				$currentYear = date('Y');
			  	if($currentYr > $currentYear) {
			  		$currentYr = $currentYear;
			  	}
		  		$this->setVarByRef('currentYr', $currentYr);
                return "decentwork_tpl.php";

            /******************* BEGIN CASES FOR ADMIN SECTION *********************/
            case "start":
                return "selectbargainingunit_tpl.php";

            case "ajax_updateunitlist":
                $val = $this->getParam('str');
                $content = $this->objDbUnit->getAll("WHERE name LIKE '$val%' ORDER BY name");
                $objselectUnits = new dropdown('id');
                if (count($content) > 5) {
                	$objselectUnits->size = 5;
                } else {
                	$objselectUnits->size = count($content);
                }
                $objselectUnits->addFromDB($content,'name','id');
                
                if(!empty($content)) {
                	$objselectUnits->setSelected($content[0]['id']);
                } else {
                	$objselectUnits->addOption('-1',$this->objLanguage->languageText('mod_lrs_select','award'));
                }
                echo $objselectUnits->show();
                break;

            case "createbu":
                $unit = $this->getParam('addUnit');
                //checks if the unit created has the same name as any units in the database, if it is an error msg is displayed
                $check = $this->objDbUnit->getAll("WHERE name LIKE '$unit'");
                if(!empty($check)) {
                    $msg = $this->objLanguage->languageText("mod_award_unitexists",'award');
                    return $this->nextAction('start', array('error'=>1, 'message'=>$msg, 'unitid'=>$check[0]['id'], 'name'=>$check[0]['name'])); 
                }
                //checks if the unit created has a similiar name to any units in the database, if it is the confimation template is returned
                $allorgUnit = $this->objDbUnit->getAll();
                foreach ($allorgUnit as $orgUnit) {
                    if(str_replace(" ","",strtolower($unit)) == str_replace(" ","",strtolower($orgUnit['name']))) {
                        //$this->setVarByRef('id', $id);
                        $this->setVarByRef('unit', $unit);
                        $this->setVarByRef('dbunit', $orgUnit['name']);
                        $this->setVarByRef('orgUnitId', $orgUnit['id']);
                        return "confirmation_tpl.php";
                    }
                }
                return $this->nextAction('createagreement', array('unit'=>$unit, 'selected'=>'init_10'));

            case "yes":
                //The yes button is selected from the confirmation template, the user decideds to select the unit from the database which is similar to the unit intended to be created
                $orgUnitId = $this->getParam('orgUnitId');
                return $this->nextAction('bargainingunitoverview', array('id'=>$orgUnitId,'selected'=>'init_10'));
        
            case "no":
                //The no button is selected from the confirmation template and the user creates the unit intended to be created
                $unitName = $this->getParam('unitName');
                return $this->nextAction('createagreement', array('unit'=>$unitName, 'selected'=>'init_10'));
    
            case "createagreement":
                //This case takes the user to the add bargaining unit template which creates a bargaining unit
                $unitName = $this->getparam('unit');
                $this->setVarByRef('unit', $unitName);
                return "addbargainingunit_tpl.php";

            case "ajax_updatepblist":
                $tuId = $this->getParam('tuId');
                $pBDrop = new dropdown('branch');
                $pBDrop->addOption('-1',$this->objLanguage->languageText('mod_lrs_default_drop', 'award'));
                $content = $this->objDbBranch->getAll("WHERE partyid = '$tuId' ORDER BY name ASC");
                $pBDrop->addFromDB($content, 'name', 'id');
                echo $pBDrop->show();
                break;

            case "ajax_updatesicdivlist":
                $id = $this->getParam('id');
                $objSicDiv = $this->getObject('dbsicdiv','awardapi');
                $dropSicDiv = new dropdown('div');
                $dropSicDiv->addOption('-1',$this->objLanguage->languageText('mod_lrs_default_drop','award'));
                $content = $objSicDiv->getAll("WHERE major_divid = '$id' ORDER BY description ASC");
                $dropSicDiv->extra = "onchange = \"javascript:populateSicMajGrp(this.value)\"";
                foreach ($content as $c) {
                    //The description in the dropdown cannot exceed 55 characters
                    if (strlen($c['description']) > 55)	{
                        $c['description'] = substr($c['description'],0,52).'...';
                    }
                    $dropSicDiv->addOption($c['id'],$c['description']);
                }
                $dropSicDiv->setSelected(-1);
                echo $dropSicDiv->show();
                break;

            case "ajax_updatesicmajgrplist":
                $id = $this->getParam('id');
                $dropSicMajGrp = new dropdown('majGrp');
                $dropSicMajGrp->addOption('-1',$this->objLanguage->languageText('mod_lrs_default_drop','award'));
                $objSicMajGrp = $this->getObject('dbsicmajorgroup','awardapi');
                $content = $objSicMajGrp->getAll("WHERE divid = '$id' ORDER BY description ASC");
                $dropSicMajGrp->extra = "onchange = \"javascript:populateSicGrp(this.value)\"";
                foreach ($content as $c) {
                    //The description in the dropdown cannot exceed 55 characters
                    if (strlen($c['description']) > 55) {
                        $c['description'] = substr($c['description'],0,52).'...';
                    }
                    $dropSicMajGrp->addOption($c['id'],$c['description']);
                }
                echo $dropSicMajGrp->show();
                break;

            case "ajax_updatesicgrplist":
                $id = $this->getParam('id');
                $dropSicGrp = new dropdown('grp');
                $dropSicGrp->addOption('-1',$this->objLanguage->languageText('mod_lrs_default_drop','award'));
                $objSicGrp = $this->getObject('dbsicgroup','awardapi');
                $content = $objSicGrp->getAll("WHERE major_groupid = '$id' ORDER BY description ASC");
                $dropSicGrp->extra = "onchange = \"javascript:populateSicSubGrp(this.value)\"";
                foreach ($content as $c) {
                    //The description in the dropdown cannot exceed 55 characters
                    if (strlen($c['description']) > 55) {
                        $c['description'] = substr($c['description'],0,52).'...';
                    }
                    $dropSicGrp->addOption($c['id'],$c['description']);
                }
                echo $dropSicGrp->show();
                break;

            case "ajax_updatesicsubgrplist":
                $id = $this->getParam('id');
                $dropSic = new dropdown('subGrp');
                $dropSic->addOption('-1',$this->objLanguage->languageText('mod_lrs_default_drop','award'));
                $objSic = $this->getObject('dbsicsubgroup','awardapi');
                $content = $objSic->getAll("WHERE groupid = '$id' ORDER BY description ASC");
                foreach ($content as $c) {
                    //The description in the dropdown cannot exceed 55 characters
                    if (strlen($c['description']) > 55) {
                        $c['description'] = substr($c['description'],0,52).'...';
                    }
                    $dropSic->addOption($c['id'],$c['description']);
                }
                echo $dropSic->show();
                break;
            
            case "add":
                //This action is reached when the add agreement link in the agreement overview is selected
                $id = $this->getParam('unitid');
                $unit = $this->objDbUnit->getRow('id', $id);
                $this->setVar('unitId', $id);
                $this->setVar('unitName', $unit['name']);

                return "agreementdetails_tpl.php";

			case 'ajax_updatesoclist':
				$str = $this->getParam('str');
				$socList = $this->objwageSocName->getSocList($str);

				$objsocName = new dropdown('drpsocName');
				$objsocName->addOption('-1',$this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
				foreach ($socList as $soc) {
					$objsocName->addOption($soc['id'], "{$soc['name']} ({$soc['sample']})");
				}
				$objsocName->setSelected('-1');
				echo $objsocName->show();
				break;


            case "addagreement":
                //All the data is collected and put in there respective tables, this is to add an agreement and wage from the bargaining unit overview
                $id = $this->getParam('unitId');
                $agreeDate = $this->getParam('calendardate');
                $agreeMonths = $this->getParam('months');
                $agreeNotes = $this->getParam('agreenotes');
                $wageNotes = $this->getParam('wagenotes');
                $agreeType = $this->getParam('agreeType');  
                $unit = $this->objDbUnit->getRow('id', $id);   
                $agreeNameDate = date("d M Y", strtotime($agreeDate));
                $wageRate = $this->getParam('wageRate');//insert into agree table
                $socNameId = $this->getParam('drpsocName');
                $gradeId = $this->getParam('grades');
                $jobCodeId = $this->getParam('jobcodes');

                $hours = $this->getParam('how');
                $workers = $this->getParam('now');
                $payPeriod = $this->getParam('payPeriod');
                $periodRow = $this->objDbPayPeriodType->getRow('id', $payPeriod);
                //Insert into respective tables
                $agreeId = $this->objAgree->insert(array('typeid'=>$agreeType, 'unitid'=>$id, 'name'=>"{$unit['name']}-$agreeNameDate", 'implementation'=>$agreeDate, 'length'=>$agreeMonths, 'workers'=>$workers, 'notes'=>$agreeNotes));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                $benefitId = $this->objBenefit->insert(array('agreeid'=>$agreeId, 'nameid'=>'init_7', 'value'=>$hours));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));

                $benefitRow = $this->objBenefit->getRow('id', $benefitId);

                $weekWageRate = ($periodRow['factor'] == '0')? $wageRate * $benefitRow['value'] : $wageRate / $periodRow['factor'];
                 
                $wageId = $this->objDbWages->insert(array('agreeid'=>$agreeId, 'payperiodtypeid'=>$periodRow['id'], 'weeklyrate'=>$weekWageRate, 'notes'=>$wageNotes));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                $wageSocNameId = $this->objwageSocName->insert(array('id'=>$wageId, 'socnameid'=>$socNameId, 'gradeid'=>$gradeId, 'jobcodeid'=>$jobCodeId));//, 'soc_oldId'=>$oldId['id']));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('agreementoverview', array('agreeid'=>$agreeId, 'id'=>$benefitId, 'selected'=>'init_10'));

			case 'conditions_admin':
				$this->setVar('agreeId', $this->getParam('agreeid'));
				$this->setVar('message', $this->getParam('message'));
				return 'conditions_admin_tpl.php';
			
			case 'save_conditions':
				$agreeId = $this->getParam('agreeId');
				$conditions = $this->objBenefitNames->getAll();
				foreach ($conditions as $cond) {
					$value = $this->getParam($cond['id']);
					if ($value != '') {
						$benefit = $this->objBenefit->getAll("WHERE nameid = '{$cond['id']}' AND agreeid = '$agreeId'");
						if (!empty($benefit[0])) {
							$this->objBenefit->update('id', $benefit[0]['id'], array('value'=>$value));
						} else {
							$this->objBenefit->insert(array('agreeid'=>$agreeId, 'nameid'=>$cond['id'], 'value'=>$value));
						}
					}
				}
				return $this->nextAction('conditions_admin', array('message'=>1, 'agreeid'=>$agreeId));

            case "deletewage":
                //If the user wishes to delete an agreement
                $id = $this->getParam('wageId');
                $unitId = $this->getParam('unitId');
                $agreeId = $this->getParam('agreeId');
                $this->objDbWages->deleteWage($id);
                return $this->nextAction('agreementoverview', array('id'=>$agreeId, 'unitId'=>$unitId, 'selected'=>'init_10'));

            case "addwage":
                //This case is reached when the user select the add wage from the agreementoverview template and returns the the edit wage template, which is used for adding and editing wages
                $agreeId = $this->getParam('agreeId');
                $unitId = $this->getParam('unitId');
                $addWage = "set";
                $this->setVarByRef('addWage', $addWage);
                $this->setVarByRef('unitId', $unitId);
                $this->setVarByRef('agreeId', $agreeId);
                //Helps to create drop down which is populated by a filter associated this the text input
                //$objXajax = new xajax($this->uri(array('action'=>'addwage')));
                //$objXajax->registerFunction(array($this,"updateSocList"));
                //$objXajax->processRequests(); // XAJAX method to be called
                //$this->appendArrayVar('headerParams', $objXajax->getJavascript());
                return "editwage_tpl.php";

            case "insertwage":
                //When adding a wage and the submit button is pushed this action is executed, the information for the new wage is recieved and inserted there respective table
                $unitId = $this->getParam('unitId');
                $wageId = $this->getParam('wageId');
                $agreeId = $this->getParam('agreeId');
                $wageRate = $this->getParam('wageRate');
                $wageNotes = $this->getParam('wageNotes');
                $socId = $this->getParam('drpsocName');
                $payPeriod = $this->getParam('payPeriod');
				
				$periodRow = $this->objDbPayPeriodType->getRow('id', $payPeriod);
                
                $benefitRow = $this->objBenefit->getRow('agreeid', $agreeId);
                $weekWageRate = ($periodRow['factor'] == '0')? $wageRate * $benefitRow['value'] : $wageRate / $periodRow['factor'];
                    
                $wageId = $this->objDbWages->insert(array('agreeid'=>$agreeId, 'payperiodtypeid'=>$payPeriod, 'weeklyrate'=>$weekWageRate, 'notes'=>$wageNotes));
                $wageSocNameId = $this->objwageSocName->insert(array('id'=>$wageId,'socnameid'=>$socId));//, 'gradeid'=>$grade, 'jobcodeid'=>$jobCode));

                return $this->nextAction('wage', array('agreeId'=>$agreeId, 'wageId'=>$wageId, 'selected'=>'init_10'));

            case "updatewage":
                //When editing a wage and the submit button is pushed this action is executed, the information for the updated wage is recieved and inserted there respective table
                $unitId = $this->getParam('unitId');
                $wageId = $this->getParam('wageId');
                $agreeId = $this->getParam('agreeId');
                //$benefitId = $this->getParam('benefitId');
                $benefit = $this->objBenefit->getRow('agreeid', $agreeId);
                $wageRate = $this->getParam('wageRate');
                $wageNotes = $this->getParam('wageNotes');
                $socId = $this->getParam('drpsocName');
                //$grade = $this->getParam('grades');
                //$jobCode = $this->getParam('jobcodes');
                //$oldId = $this->objold->getRow('old_name', 'none');

                //$hours = $this->getParam('how');
                $payPeriod = $this->getParam('payPeriod');
                $periodRow = $this->objDbPayPeriodType->getRow('id', $payPeriod);

                //$benefitfields = array('agreeId'=>$agreeId, 'benefit_nameId'=>'init_7', 'benefitValue'=>$hours, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser);
                //$this->objBenefit->update('id', $benefit['id'], $benefitfields);
                $benefitRow = $this->objBenefit->getRow('agreeid', $agreeId);

                $weekWageRate = ($periodRow['factor'] == '0')? $wageRate * $benefitRow['value'] : $wageRate / $periodRow['factor'];

                //insert updated info into the wage and wage soc name tables
                $wagefields = array('agreeid'=>$agreeId, 'payperiodtypeid'=>$periodRow['id'], 'weeklyrate'=>$weekWageRate, 'notes'=>$wageNotes);//, 'datemodified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objDbWages->update('id', $wageId, $wagefields);
                $socfields = array('socnameid'=>$socId);//, 'gradeid'=>$grade, 'jobcodeid'=>$jobCode);//, 'soc_oldId'=>$oldId['id'], 'datemodified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objwageSocName->update('id', $wageId, $socfields);
                return $this->nextAction('wage', array('agreeId'=>$agreeId, 'wageId'=>$wageId, 'selected'=>'init_10'));

            case "startindex":
                //return the index template
                return "indexoverview_tpl.php";

            case "addoreditindex":
                //returns the template to view, add and edit index values according to there specific date
                $this->setVarByRef('typeId', $this->getParam('typeId'));
                $this->setVarByRef('currentYr', $this->getParam('year'));
                return "addeditindex_tpl.php";

            case "insertindexvalue":
                //Inserts an index value to a specific date
                $typeId = $this->getParam('typeId');
                $display = $this->getParam('display');
                $currentYr = $this->getParam('currentYr');
                //The outer loop is used so that 5 rows can be displayed on the screen all showing the index value for a specific year
                for($j=0; $j<=4; $j++) {
                    $thisYear = $currentYr + $j;
                    //This loop runs through each value checking whether or not to update it
                    for($i=1; $i<=12; $i++) {
                        $value = $this->getParam("inputValue_{$thisYear}_$i");
                        if(isset($value)) {
                            $dateVal = mktime(null, null, null, $i, 1, $thisYear);
                            $indexDate = date('Y-m-d', $dateVal);
                            if($value != 0) {
                                $valueId = $this->objIndexValues->insert(array('typeid'=>$typeId, 'indexdate'=>$indexDate, 'value'=>$value));
                            }
                        }
                    }
                }
                /*if($display == true){
                    $displayValue = 1;
                }else{
                    $displayValue = 0;
                }
                $displayfields = array('display'=>$displayValue, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objIndexes->update('id', $typeId, $displayfields);*/
                return $this->nextAction('startindex', array('selected'=>'init_10'));

            case "ajax_indexvalue":
                $id = $this->getParam('id');
                $value = $this->getParam('value');
                $month = $this->getParam('month');
                $year = $this->getParam('year');
                $wordUpdate = ' '.$this->objLanguage->languageText("word_update").' ';
                $wordBack = ' '.$this->objLanguage->languageText("word_back").' ';
                $errorMsg = $this->objLanguage->languageText("phrase_required");
                $this->objIndexValues->update('id',$id,array('value'=>$value));
                $link = $this->newObject('link','htmlelements'); 
                $link->link = $value;
                $link->link('#');
                $link->extra = " onclick = 'javascript:updateIndexValue(\"$id\", \"$value\", $month, $year, \"$wordUpdate\", \"$wordBack\", \"$errorMsg\")'";
                echo $link->show();
                break;

            case "addindex":
                $this->setVar('indexId',$this->getParam('indexId'));
                return "addindex_tpl.php";
 
            case "insertindex":
                //Insert a new index type
                $indexName = $this->getParam('name');
                $indexAbbr = $this->getParam('abbr');
                $indexId = $this->getParam('indexId');
                if ($indexId) {
                    $this->objIndexes->update('id',$indexId,array('name'=>$indexName,'shortname'=>$indexAbbr));
                } else {
                    $this->objIndexes->insert(array('name'=>$indexName,'shortname'=>$indexAbbr));
                }
                return $this->nextAction('startindex', array('selected'=>'init_10'));

            case "setgooglesearch":
				$this->setVar('payPeriods', $this->objDbPayPeriodType->getAll("ORDER BY name"));
				$this->setVar('setGoogle', $this->objSysConfig->getValue('has_google_api_key', 'award'));
                $this->setVar('apiKey', $this->objSysConfig->getValue('google_api_key', 'award'));
                $this->setVar('googleSearch', $this->objSysConfig->getValue('google_search_terms', 'award'));
                $this->setVar('setPeriod', $this->objSysConfig->getValue('default_pp_type', 'award'));
                $this->setVar('setHours', $this->objSysConfig->getValue('default_hoursperweek', 'award'));
                $this->setVar('setAnalytics', $this->objSysConfig->getValue('google_analytics_code', 'award'));
                $this->setVar('setSymbol', $this->objSysConfig->getValue('CURRENCY_ABREVIATION', 'award'));
                return "setGoogleSearch_tpl.php";

            case "submitgooglesettings":
                $hasKey = ($this->getParam('setGoogle'))? '1' : '0';
                $this->objSysConfig->changeParam('google_api_key','award', $this->getParam('apiKey'));
                $this->objSysConfig->changeParam('google_search_terms','award', $this->getParam('search'));
                $this->objSysConfig->changeParam('has_google_api_key','award', $hasKey);
                $this->objSysConfig->changeParam('default_pp_type','award', $this->getParam('payperiod'));
                $this->objSysConfig->changeParam('default_hoursperweek','award', $this->getParam('hours'));
                $this->objSysConfig->changeParam('google_analytics_code','award', $this->getParam('analytics'));
                $this->objSysConfig->changeParam('CURRENCY_ABREVIATION','award', $this->getParam('currencysymbol'));
                return $this->nextAction('admin');

	        case 'search':
                //Get searchterm
                $searchTerm = $this->getParam('searchterm');
                //Send searchterm to the template
                $this->setVarByRef('searchTerm', $searchTerm);
                $socNames = $this->objDbSocNames->getAll("WHERE name LIKE '%$searchTerm%' ORDER BY name DESC");
                $this->setVarByRef('socNames', $socNames);
                return "results_tpl.php";         

            case "viewgender":
                return "admin_gender_tpl.php";

            case "editgendercat":
                $genderCat = $this->getParam('genderCat');
                $this->setVarByRef('genderCat', $genderCat);
                return "edit_gender_cat_tpl.php";

            case "editgenderrow":
                $genderId = $this->getParam('genderId');
                $this->setVarByRef('genderId', $genderId);
                return "edit_gender_row_tpl.php";

            case "viewgender":
                return "admin_gender_tpl.php";

            case "viewbenefittype":
                $benefitType = $this->objBenefitType->getAll("ORDER BY name");
                $this->setVarByRef('benefitType', $benefitType);
                return "benefit_type_tpl.php";

	        case "viewbenefitname":
                $benefitTypeId = $this->getParam('benefitTypeId');
                $benefitType = $this->objBenefitType->getRow('id', $benefitTypeId);
                //var_dump($benefitType);
                $benefitName = $this->objBenefitNames->getAll("WHERE typeid = '$benefitTypeId'");
                $this->setVarByRef('benefitName', $benefitName);
                $this->setVarByRef('benefitType', $benefitType);
                return "benefit_name_tpl.php";

            case "addeditbenefitname":
        		$benefitTypeId = $this->getParam('typeid');
        		$benefitNameId = $this->getParam('benefitNameId');
        		$this->setVar('benefitTypeId', $benefitTypeId);
        		$this->setVar('benefitNameId', $benefitNameId);
        		return "add_edit_benefit_name_tpl.php";	

            case "addeditbenefittype":
                $benefitTypeId = $this->getParam('benefitTypeId');
                $this->setVarByRef('benefitTypeId', $benefitTypeId);
                return "add_edit_benefit_type_tpl.php";

            case "savebenefittype":
                $benefitTypeId = $this->getParam('benefitTypeId');
                $benefitTypeName = $this->getParam('benefitTypeName');

                if($benefitTypeId){
                    $this->objBenefitType->update('id', $benefitTypeId, array('name' => $benefitTypeName));
                } else {
                    $this->objBenefitType->insert(array('name' => $benefitTypeName));
                }
                return $this->nextAction("viewbenefittype", array('selected'=>'init_10'));

			case "savebenefitname":
        		$benefitNameId = $this->getParam('benefitNameId');
        		$benefitTypeId = $this->getParam('benefitTypeId');
        		$benefitTypeName = $this->getParam('benefitTypeName');
        		$aggregateType = $this->getParam('aggregateType');
        		$benchmark = $this->getParam('benchmark');
        		$unit = $this->getParam('unit');
				$valuefields = array('typeid'=>$benefitTypeId, 'aggregatetype'=>$aggregateType, 'name'=>$benefitTypeName, 'measure'=>$unit, 'benchmark'=>$benchmark);
					
        		if(isset($benefitNameId)){
					$this->objBenefitNames->update('id', $benefitNameId, $valuefields);
        		} else {
        			$benefitNameId = $this->objBenefitNames->insert($valuefields);
        		}
        		return $this->nextAction("viewbenefitname", array('benefitTypeId'=>$benefitTypeId, 'selected'=>'init_10'));

            case 'viewtradeunion':
                $tradeUnions = $this->objDbParty->getAll("ORDER BY name");
                $this->setVarByRef('tradeUnions', $tradeUnions);
                return "view_trade_union_tpl.php";

            case 'viewbranch':
                $unionId = $this->getParam('unionId');
                $branches = $this->objDbBranch->getAll("WHERE partyid = '$unionId' ORDER BY name");
                $this->setVarByRef('branches', $branches);
                $this->setVarByRef('unionId', $unionId);
                return "view_branch_tpl.php";

            case 'deletetradeunion':
                $unionId = $this->getParam('unionId');
                $this->setVarByRef('unionId', $unionId);
                return "delete_trade_union_tpl.php";

            case 'addedittradeunion':
                $unionId = $this->getParam('unionId');
                $this->setVarByRef('unionId', $unionId);
                return "add_edit_trade_union_tpl.php";

            case 'addtradeunion':
                $abbreviation = $this->getParam('abbreviation');
                $name = $this->getParam('name');
                $unionId = $this->objDbParty->insert(array('name'=>$name, 'abbreviation'=>$abbreviation));
                return $this->nextAction('viewtradeunion', array('selected'=>'init_10'));

            case 'edittradeunion':
                $unionId = $this->getParam('unionId');
                $abbreviation = $this->getParam('abbreviation');
                $name = $this->getParam('name');
                
                if ($unionId) {
                    $valuefields = array('id'=>$unionId, 'name'=>$name, 'abbreviation'=>$abbreviation);
                    $this->objDbParty->update('id', $unionId, $valuefields);
                } else {
                    $unionId = $this->objDbParty->insert(array('name'=>$name, 'abbreviation'=>$abbreviation));
                }

                return $this->nextAction('viewtradeunion', array('selected'=>'init_10'));

            case 'deletebranch':
                $unionId = $this->getParam('unionId');
                $this->setVarByRef('unionId', $unionId);
                $branchId = $this->getParam('branchId');
                $this->setVarByRef('branchId', $branchId);
                return "delete_branch_tpl.php";
                break;

            case 'confirmdeletetradeunion':
                $unionId = $this->getParam('unionId');
                $this->objDbParty->delete('id', $unionId);
                return $this->nextAction('viewtradeunion');
                break;

            case 'edituser':
                $lrsUserId = $this->getParam('userId');
                $this->setVarByRef('lrsUserId', $lrsUserId);
                return "userdetails_tpl.php";   

            case 'deleteuser':
                $userId = $this->getParam('userid');
                $objDbUserReg = $this->getObject('dbuserreg');
                $objDbUserReg->delete('userid', $userId);
                $this->objUser->delete('userid', $userId);
                return $this->nextAction('viewuserlist', array('selected'=>'init_10'));

            case 'viewuserlist':
                case 'searchuserlist':
                $searchTerm = $this->getParam('searchterm');
                if ($searchTerm) {
                    $userList = $this->objUser->getAll("WHERE username LIKE '%$searchTerm%'
                                                              OR firstname LIKE '%$searchTerm%'
                                                              OR surname LIKE '%$searchTerm%'");
                } else {
                    $userList = $this->objUser->getAll();
                }
                $this->setVarByRef('searchTerm', $searchTerm);
                $this->setVarByRef('userList', $userList);
                return "user_list_tpl.php";

            case 'saveuser':
                $userId = $this->getParam('userId');
                $lrsid = $this->getParam('lrsId');
                $id = $this->getParam('id');
                $username = $this->getParam('username');
                $title = $this->getParam('title');
                $firstname = $this->getParam('firstname');
                $surname = $this->getParam('surname');
                $position = $this->getParam('position');
                $tradeunion = $this->getParam('tuId');
                $password = $this->getParam('password');
                $passwd = $this->getParam('passwd');
                $email = $this->getParam('email');
                $sex = $this->getParam('sex');
                $country = $this->getParam('country');
                $currUser = $this->objUser->userId();
                $currentDate = date('Y-m-d');
                $date = date('Y-m-d G:h:i');
                $objDbUserReg = $this->getObject('dbuserreg');
                
                if($password == $passwd)
                {
                    $valuefields = array('username'=>$username, 'title'=>$title, 'firstname'=>$firstname, 'surname'=>$surname, 'emailaddress'=>$email, 'sex'=>$sex, 'country'=>$country, 'isactive'=>1, 'updated'=>$date);
                    if ($password != '')  {
						$valuefields['pass'] = sha1($password);
					}
					if ($id) {
                        $valuefields['userid'] = $userId;
                        $this->objUser->update('id', $id, $valuefields);
                    } else {
                        $valuefields['howcreated'] = 'useradmin';
                        $userId = rand(1000000000,9999999999);
                        $valuefields['userid'] = $userId;
                        $id = $this->objUser->insert($valuefields);
                    }
                    if ($lrsid == '') {
                        $lrsid = $objDbUserReg->insert(array('userid'=>$userId, 'tuid'=>$tradeunion, 'position'=>$position));
                    } else {
                        $objDbUserReg->update('id', $lrsid, array('userid'=>$userId, 'tuid'=>$tradeunion, 'position'=>$position));
                    }
                    return $this->nextAction('viewuserlist',array('selected'=>'init_10'));
                }
                else {
                    $msg = $this->objLanguage->languageText("mod_lrs_password_error", 'award');
                    return $this->nextAction('edituser', array('error'=>1, 'message'=>$msg, 'userId'=>$userId, 'selected'=>'init_10')); 
                }

			case 'editmenu':
                return 'menuedit_tpl.php';

            case 'ajax_updatemenuitem':
                $id = $this->getParam('id');
                $name = $this->getParam('name');
                $this->lrsNav->update('id',$id,array('name'=>$name));
                echo $name;
                break;

            case "viewblurb":
				$blurbs = $this->objBlurb->getAll("ORDER BY module ASC");
                $this->setVarByRef('blurbs', $blurbs);
                return "reporting_blurb_tpl.php";

            case "editblurb":
                $blurbId = $this->getParam('blurbId');
                $this->setVarByRef('blurbId', $blurbId);
                return "edit_blurb_tpl.php";

            case "updateblurb":
                $blurbId = $this->getParam('blurbId');
                $text = $this->getParam('text');
                $currUser = $this->objUser->userId();
                $currentDate = date('Y-m-d');

                $blurbfields = array('text'=>$text, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objBlurb->update('id', $blurbId, $blurbfields);
                return $this->nextAction('viewblurb');

            case "decentworkadmin":
                $currentYr = $this->getParam('updateYear');
                $currentYear = date('Y');
                if($currentYr > $currentYear){
                    $currentYr = $currentYear;
                }
                $this->setVarByRef('currentYr', $currentYr);
                return "decentworkadmin_tpl.php";

            case 'selectmajorgroup':
                //Get aray of all major groups 
                $majorGroups = $this->objDbSocMajorGroup->getAll("ORDER BY id DESC");
  
                //Send major groups array to template
                $this->setVarByRef('majorGroups', $majorGroups);
                $majorGroupId = $this->getParam('majorGroupId',NULL);
                //echo 'id '.$majorGroupId;die();
                $this->setVarByRef('majorGroupId', $majorGroupId);
                return 'soc_majorgroup_tpl.php';
                break;

	        case 'savemajorgroup':
                $id = $this->getParam('id');
				$description = $this->getParam('description');
				if ($id != null) {
					$this->objDbSocMajorGroup->update('id',$id, array('description'=>$description));
				} else {
					$this->objDbSocMajorGroup->insert(array('description'=>$description));
				}
                return $this->nextAction('selectmajorgroup', array('selected'=>'init_10'));

			case 'editminorgroup':
                //Set group name
                $groupName = 'minor group';
                //Send group name to the template
                $this->setVarByRef('groupName', $groupName); 
                //Get group id(pk)
                $this->setVar('minorGroupId', $this->getParam('minorGroupId'));
                $this->setVar('subMajorGroupId', $this->getParam('subMajorGroupId'));
                $this->setVar('majorGroupId', $this->getParam('majorGroupId'));
				return 'add_edit_tpl.php';

            case 'selectmajordiv':
                //Get aray of all major divs 
                $sicMajorDivs = $this->objDbSicMajorDivs->getAll("ORDER BY code");
                //Send major groups array to template
                $this->setVarByRef('sicMajorDivs', $sicMajorDivs);
                $sicMajorDivId = $this->getParam('sicMajorDivId', NULL);
                $this->setVarByRef('sicMajorDivId', $sicMajorDivId);
                return 'sic_majordiv_tpl.php';
                break;

            case "addeditcategory":
                $catId = $this->getParam('catId');
                $this->setVarByRef('catId', $catId);
                return "addeditcategory_tpl.php";

            case "editcategory":
                $catId = $this->getParam('catId');
                $category = $this->getParam('category');
                if (!$catId) {
                    $this->objdecentWorkCategory->insert(array('category'=>$category));
                } else {
                    $this->objdecentWorkCategory->update('id', $catId, array('category'=>$category));
                }

                return $this->nextAction('decentworkadmin', array('selected'=>'init_10'));

            case "editadddecentwork":
                $valId = $this->getParam('valId');
                $catId = $this->getParam('catId');
                $this->setVarByRef('valId', $valId);
                $this->setVarByRef('catId', $catId);
                return "editadddecentwork_tpl.php";

            case "editdecentWork":
                $valId = $this->getParam('valId');
                $catId = $this->getParam('catId');
                $name = $this->getParam('name');
                $value = $this->getParam('value');
                $unit = $this->getParam('unit');
                $year = $this->getParam('year');
                $source = $this->getParam('source');
                $note = $this->getParam('note');
                $currUser = $this->objUser->userId();

                //The updates are inserted into the values table
                $valuefields = array('categoryid'=>$catId, 'label'=>$name, 'value'=>$value, 'unit'=>$unit, 'source'=>$source, 'notes'=>$note, 'year'=>$year);
                if ($valId) {
                    $this->objdecentWorkValues->update('id', $valId, $valuefields);
                } else {
                    $this->objdecentWorkValues->insert($valuefields);
                }
                return $this->nextAction('decentworkadmin', array('selected'=>'init_10'));

            case "deletedecentrow":
                $valId = $this->getParam('valId');
                $this->objdecentWorkValues->delete('id', $valId);
                return $this->nextAction('decentworkadmin', array('selected'=>'init_10'));

            case "deletedecentcategory":
                $catId = $this->getParam('catId');
                $this->objdecentWorkCategory->delete('id', $catId);
                return $this->nextAction('decentworkadmin', array('selected'=>'init_10'));

            case "editbargainingunit":
                $unitId = $this->getparam('unitid');
                $this->setVarByRef('unitId', $unitId);
                
                return "addbargainingunit_tpl.php";

            case "updatebargainingunit":
                //This case is triggered once the user enters the info required from the add bargaining unit template. Recieves the info and inserts them into there respective tables
                //Recieves info from dropdowns
                $unitId = $this->getParam('unitId');
                $unitName = $this->getParam('unit');
                $branch = $this->getParam('branch');
                $sicMajDiv = $this->getParam('majorDiv');
                $sicDiv = $this->getParam('div');
                $sicMajGrp = $this->getParam('majGrp');
                $sicGrp = $this->getParam('grp');
                $sicSubGrp = $this->getParam('subGrp');
                $region = $this->getParam('region');
                $unitNotes = $this->getParam('unitNotes');
                
                if (($sicMajDiv == -1) || ($sicMajDiv == null)){
                    $sicMajDiv = 'init_0';
                }
                if (($sicDiv == -1) || ($sicDiv == null)){
                    $sicDiv = 'init_0';
                }
                if (($sicMajGrp == -1) || ($sicMajGrp == null)){
                    $sicMajGrp = 'init_0';
                }
                if (($sicGrp == -1) || ($sicGrp == null)){
                    $sicGrp = 'init_0';
                }
                if (($sicSubGrp == -1) || ($sicSubGrp == null)){
                    $sicSubGrp = 'init_0';
                }
                
                //$active = ($expired == true)? 0 : 1;
                $active = 1;
                $unitFields = array('name'=>$unitName, 'notes'=>$unitNotes, 'active'=>$active);
                if ($unitId) {
                   $this->objDbUnit->update('id', $unitId, $unitFields);
                } else {
                    $unitId = $this->objDbUnit->insert($unitFields);
                }
                
                //updating into the respective tables to create a bargaining unit
                $branchFields = array('branchid'=>$branch, 'unitid'=>$unitId);
                if ($this->objUnitBranch->valueExists('unitid',$unitId)) {
                    $this->objUnitBranch->update('unitid',$unitId,$branchFields);
                } else {
                    $this->objUnitBranch->insert($branchFields);
                }
                
                $regionFields = array('unitid'=>$unitId, 'regionid'=>$region);
                if ($this->objArea->valueExists('unitid',$unitId)) {
                    $this->objArea->update('unitid', $unitId, $regionFields);
                } else {
                    $this->objArea->insert($regionFields);
                }
                
                $sicFields = array('unitid'=>$unitId, 'major_divid'=>$sicMajDiv, 'divid'=>$sicDiv, 'major_groupid'=>$sicMajGrp, 'groupid'=>$sicGrp, 'sub_groupid'=>$sicSubGrp);
                if ($this->objUnitSic->valueExists('unitid',$unitId)) {
                    $this->objUnitSic->update('unitid', $unitId, $sicFields);
                } else {
                    $this->objUnitSic->insert($sicFields);
                }
    
                return $this->nextAction('bargainingunitoverview', array('id'=>$unitId, 'selected'=>'init_10'));
                break;

            case "agreementoverview":
                //returns a overview the agreement selected and a list of the wages
                $unitId = $this->getParam('unitId');
                $id = $this->getParam('id');
                $this->setVar('id', $id);
                $this->setVar('unitId', $unitId);
                return "agreementoverview_tpl.php";

            case "bargainingunitoverview":
                //Returns the agreement details form with and overview of the bargaining unit and a list of agreements, this is reached when channeling through the select bargining unit path
                $id = $this->getParam('id');
                $this->setVarByRef('id', $id);
                return "bargainingunitoverview_tpl.php";

            case "deleteagreement":
                //If the user wishes to delete an agreement
                $id = $this->getParam('unitid');
                $agreeId = $this->getParam('agreeid');
                $this->objAgree->deleteAgree($agreeId);
                return $this->nextAction('bargainingunitoverview', array('id'=>$id, 'selected'=>'init_10'));

            case "savegendercat":
                $oldGenderCat = $this->getParam('genderCat');
                $genderCat = $this->getParam('category');
                $currUser = $this->objUser->userId();
                $currentDate = date('Y-m-d');
            
                $genderCatCase = ucwords(strtolower($genderCat));
            
                $genderArray = $this->objBCEA->getAll("WHERE category = '$oldGenderCat'");

                foreach ($genderArray as $gender)
                {
                    $categoryfields = array('category'=>$genderCatCase);//, 'datemodified'=>$currentDate, 'modifierId'=>$currUser);
                    $this->objBCEA->update('id', $gender['id'], $categoryfields);           
                }
                return $this->nextAction('viewgender');

            case "savegenderrow":
                $genderId = $this->getParam('genderId');
                $genderType = $this->getParam('type');
                $genderBenefit = $this->getParam('benefit');
                $genderbcea = $this->getParam('bcea');
                $genderComment = $this->getParam('comment');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');
                
                $genderfields = array('type'=>$genderType, 'nameid'=>$genderBenefit, 'bcea'=>$genderbcea, 'comment'=>$genderComment);//, 'datemodified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objBCEA->update('id', $genderId, $genderfields);
                return $this->nextAction('viewgender');

            case "savenewgenderrow":
                $category = $this->getParam('category');
                $genderType = $this->getParam('benefitType');
                $genderBenefit = $this->getParam('name');
                $genderbcea = $this->getParam('bcea');
                $genderComment = $this->getParam('comment');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');
                
                $categoryCase = ucwords(strtolower($category));
                
                $allRows = $this->objBCEA->getAll();
                $count = 0;
                foreach ($allRows as $row)
                {
                    if($categoryCase == $row['category'])
                    {
                        $count += 1;
                    }
                }
                
                if($count > 0)
                {
                    $genderId = $this->objBCEA->insert(array('category'=>$categoryCase, 'type'=>$genderType, 'nameId'=>$genderBenefit, 'bcea'=>$genderbcea, 'comment'=>$genderComment));//, 'datemodified'=>$currentDate, 'modifierId'=>$currUser));
                    return $this->nextAction('viewgender');
                }
                else 
                {
                    $msg = $this->objLanguage->languageText("mod_lrs_gender_cat_notexist", 'award'); 
                    return $this->nextAction('addnewgender', array('error'=>1, 'message'=>$msg)); 
                }
                break;
           

            case "addnewgender":
                return "add_new_gender_tpl.php";

            case 'addeditmajordiv':
                $majDivId = $this->getParam('sicMajorDivId');
                $this->setVarByRef('majDivId', $majDivId);
                return "sic_addedit_majordiv_tpl.php";
                break;

            case 'adddiv':
                $majDivId = $this->getParam('majorDiv');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                $otherDivs = $this->objDbSicDivs->getAll();
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                //insert into values table
                $sicdivid = $this->objDbSicDivs->insert(array('major_divid'=>$majDivId, 'description'=>$description, 'code'=>$code, 'notes'=>$notes));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('selectsicdiv', array('majorDivId'=>$majDivId, 'selected'=>'init_10'));

            case 'addeditdiv':
                $sicDivId = $this->getParam('sicDivId');
                $majorDiv = $this->getParam('majorDiv');
                $this->setVarByRef('sicDivId', $sicDivId);
                $this->setVarByRef('majorDiv', $majorDiv);
                return "sic_addedit_div_tpl.php";
                break;

            case 'editdiv':
                $sicDivId = $this->getParam('sicDivId');
                $majDivId = $this->getParam('majorDiv');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                $otherDivs = $this->objDbSicDivs->getAll("WHERE id != '$sicDivId'");
                $currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                $valuefields = array('description'=>$description, 'code'=>$code, 'notes'=>$notes);//, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objDbSicDivs->update('id', $sicDivId, $valuefields);

                return $this->nextAction('selectsicdiv', array('majorDivId'=>$majDivId,'selected'=>'init_10'));

            case 'deletediv':
                $sicDivId = $this->getParam('sicDivId');
                $majDivId = $this->getParam('majorDiv');
                $this->objDbSicDivs->delete('id', $sicDivId);
                return $this->nextAction('selectsicdiv', array('majorDiv'=>$majDivId,'selected'=>'init_10'));

            case 'selectsicmajorgroup':
                //Get sic Div Id(pk)
                $sicDivId = $this->getParam('sicDivId');
                //Get array of all siv major groups under div 
                $sicMajorGroups = $this->objDbSicMajorGroups->getAll("WHERE divid = '$sicDivId'");
                //Send sic major groups array to template
                $this->setVarByRef('sicMajorGroups', $sicMajorGroups);
                $sicMajorRow = $this->objDbSicMajorGroups->getRow('divid', $sicDivId);
                $this->setVarByRef('sicMajorRow', $sicMajorRow);

                //Get array of sic div data
                $sicDiv = $this->objDbSicDivs->getRow('id', $sicDivId);
                //Send sic div array to the template
                $this->setVarByRef('sicDiv', $sicDiv);
                //Get sic major div id
                $sicMajorDivId = $sicDiv['major_divid'];
                //Get array of major div data
                $sicMajorDiv = $this->objDbSicMajorDivs->getRow('id', $sicMajorDivId);
                //Send major div array to template
                $this->setVarByRef('sicMajorDiv', $sicMajorDiv);
                return 'sic_majorgroup_tpl.php';

            case 'selectsicdiv':
                //Get major div id(pk)
                $sicMajorDivId = $this->getParam('majorDivId');
                //Get array of all siv divs under major div
                $sicDivs = $this->objDbSicDivs->getAll("WHERE major_divid = '$sicMajorDivId'");
                //Send sic divs array to template
                $this->setVarByRef('sicDivs', $sicDivs);
                //Get array of major div data
                $sicMajorDiv = $this->objDbSicMajorDivs->getRow('id', $sicMajorDivId);
                //Send major div array to template
                $this->setVarByRef('sicMajorDiv', $sicMajorDiv);
                $this->setVarByRef('MajorDivId', $sicMajorDivId);
                return 'sic_div_tpl.php';

            case 'addeditmajorgroup':
                $sicDivId = $this->getParam('sicDivId');
                $sicMajorGroupId = $this->getParam('sicMajorGroupId');
                $this->setVarByRef('sicDivId', $sicDivId);
                $this->setVarByRef('sicMajorGroupId', $sicMajorGroupId);
                return "sic_addedit_majorgroup_tpl.php";

            case 'selectsicgroup':
                $sicMajorGroupId = $this->getParam('sicMajorGroupId');
                $sicMajorGroup = $this->objDbSicMajorGroups->getRow('id', $sicMajorGroupId);
                $sicGroups = $this->objDbSicGroups->getAll("WHERE major_groupid = '$sicMajorGroupId'");
				$sicDivId = $sicMajorGroup['divid'];
				$sicDiv = $this->objDbSicDivs->getRow('id', $sicDivId);
                               
                $this->setVar('sicGroups', $sicGroups);
                $this->setVar('sicMajorGroup', $sicMajorGroup);
				$this->setVar('sicDiv', $sicDiv);
				
                return 'sic_group_tpl.php';

	        case 'selectsubmajorgroup':
                $majorGroupId = $this->getParam('majorGroupId');
                $majorGroup = $this->objDbSocMajorGroup->getRow('id', $majorGroupId);
                $this->setVarByRef('majorGroup', $majorGroup);
                $subMajorGroups = $this->objDbSubMajorGroups->getAll("WHERE major_groupid = '$majorGroupId'");
                $this->setVarByRef('subMajorGroups', $subMajorGroups);
                return 'soc_submajorgroup_tpl.php';

			case 'saveminorgroup':
                $majorGroupId = $this->getParam('major_groupid');
                $subMajorGroupId = $this->getParam('submajor_groupid');
                $minorGroupId = $this->getParam('minor_groupid');
                $description = $this->getParam('description');
                //Save record to db
                $params = array('submajor_groupid'=>$subMajorGroupId, 'major_groupid'=>$majorGroupId,
								'description'=>$description);
				
				if ($minorGroupId != NULL) {
					$this->objDbMinorGroups->update('id', $minorGroupId, $params);
				} else {
					$this->objDbMinorGroups->insert($params);
				}
                return $this->nextAction('selectminorgroup', array('submajor_groupid'=>$subMajorGroupId, 'major_groupid'=>$majorGroupId, 'selected'=>'init_10'));
                
			case 'selectminorgroup':
                //Get the major group id(pk)
                $subMajorGroupId = $this->getParam('submajor_groupid');
                //Get array of sub major group data
                $subMajorGroup = $this->objDbSubMajorGroups->getRow('id', $subMajorGroupId);
                //Send sub major group array to template
                $this->setVar('subMajorGroup', $subMajorGroup);
                //Get major group id(pk)
                $majorGroupId = $subMajorGroup['major_groupid'];
                $this->setVar('majorGroupId', $majorGroupId);
                //Get array of associative arrays containing all minor groups under sub major group in question
                $minorGroups = $this->objDbMinorGroups->getAll("WHERE submajor_groupid = '$subMajorGroupId'");
                //Send sub major group array to the template
                $this->setVar('minorGroups', $minorGroups);
                return 'soc_minorgroup_tpl.php';

			case 'saveunitgroup':
                $majorGroupId = $this->getParam('major_groupid');
                $subMajorGroupId = $this->getParam('submajor_groupid');
                $minorGroupId = $this->getParam('minor_groupid');
                $unitGroupId = $this->getParam('unit_groupid');
                $description = $this->getParam('description');
                //Save record to db
                $params = array('submajor_groupid'=>$subMajorGroupId, 'major_groupid'=>$majorGroupId,
								'minor_groupid'=>$minorGroupId, 'description'=>$description);
				
				if ($unitGroupId != NULL) {
					$this->objDbUnitGroups->update('id', $unitGroupId, $params);
				} else {
					$this->objDbUnitGroups->insert($params);
				}
                return $this->nextAction('selectunitgroup', array('minor_groupid'=>$minorGroupId, 'major_groupid'=>$majorGroupId, 'selected'=>'init_10'));
                
			case 'selectunitgroup':
                //Get the major group id(pk)
                $minorGroupId = $this->getParam('minor_groupid');
                //Get array of minor group data
                $minorGroup = $this->objDbMinorGroups->getRow('id', $minorGroupId);
                //Send minor group array to template
                $this->setVar('minorGroup', $minorGroup);
                $majorGroupId = $minorGroup['major_groupid'];
                $this->setVar('majorGroupId', $majorGroupId);
                $subMajorGroupId = $minorGroup['submajor_groupid'];
                $this->setVar('subMajorGroupId', $subMajorGroupId);
                //Get array of associative arrays containing all unit under minor group in question
                $unitGroups = $this->objDbUnitGroups->getAll("WHERE minor_groupid = '$minorGroupId'");
                //Send sub major group array to the template
                $this->setVar('unitGroups', $unitGroups);
                return 'soc_unitgroup_tpl.php';
                
			case 'savesocname':
                $majorGroupId = $this->getParam('majorGroupId');
                $subMajorGroupId = $this->getParam('subMajorGroupId');
                $minorGroupId = $this->getParam('minorGroupId');
                $unitGroupId = $this->getParam('unitGroupId');
                $id = $this->getParam('socNameId');
                $searchterm = $this->getParam('searchterm');
                
				$params = array('name'=>$this->getParam('description'),
								'major_groupid'=>$majorGroupId, 'submajor_groupid'=>$subMajorGroupId,
								'minor_groupid'=>$minorGroupId, 'unit_groupid'=>$unitGroupId);
                
				if ($id != NULL) {
					$this->objDbSocNames->update('id', $id, $params);
				} else {
					$this->objDbSocNames->insert($params);
				}
				
				if($searchterm != NULL) {
                	$nAction = 'search';
					$params = array('searchterm'=>$searchterm);
                	
                } else {
					$nAction = 'selectsocname';
					$params = array('unit_groupid'=>$unitGroupId);
                	
                }
				$params['selected'] = 'init_10';
				return $this->nextAction($nAction, $params);

			case 'selectsocname':
                $unitGroupId = $this->getParam('unit_groupid');
                //Get array of unit group data
                $unitGroup = $this->objDbUnitGroups->getRow('id', $unitGroupId);
                //Send minor group array to template
                $this->setVar('unitGroup', $unitGroup);
                //Get major group id(pk)
                $majorGroupId = $unitGroup['major_groupid'];
                $this->setVar('majorGroupId', $majorGroupId);
                $subMajorGroupId = $unitGroup['submajor_groupid'];
                $this->setVar('subMajorGroupId', $subMajorGroupId);
                $minorGroupId = $unitGroup['minor_groupid'];
                $this->setVar('minorGroupId', $minorGroupId);
                //Get array of associative arrays containing all soc names under unit group in question
                $socNames = $this->objDbSocNames->getAll("WHERE unit_groupid = '$unitGroupId'");
                //Send sub major group array to the template
                $this->setVar('socNames', $socNames);
                return 'soc_name_tpl.php';


            case 'savesubmajorgroup':
                //Get mode add/edit
                $id = $this->getParam('id');
				$majorGroupId = $this->getParam('majorGroupId');
                //Save record to db
				$params = array('description'=>$this->getParam('description'),
								'major_groupid'=>$majorGroupId);
                if ($id != NULL) {
					$this->objDbSubMajorGroups->update('id', $id, $params);
				} else {
					$this->objDbSubMajorGroups->insert($params);
				}

                return $this->nextAction('selectsubmajorgroup',
										 array('majorGroupId'=>$majorGroupId,
											   'selected'=>'init_10'));

            case 'deletesubmajorgroup':

                //Get group id(pk)
                $subMajorGroupId = $this->getParam('subMajorGroupId');
                //Get major group id(pk)
                $majorGroupId = $this->getParam('majorGroupId');
                //Delete all soc names under sub major group
                $this->objDbSocNames->delete('major_groupid', $subMajorGroupId);
                //Delete all unit groups under sub major group
                $this->objDbUnitGroups->delete('major_groupid', $subMajorGroupId);
                //Delete all minor groups under sub major group
                $this->objDbMinorGroups->delete('major_groupid', $subMajorGroupId);
                //Delete sub major group
                $this->objDbSubMajorGroups->delete('id', $subMajorGroupId);
                //Set next action params
                $params = array('majorGroupId'=>$majorGroupId);
                //Set next action
                return $this->nextAction('selectsubmajorgroup', $params);
                break;

            case 'editsubmajorgroup':
                //Set group name
                $groupName = 'sub major group';
                //Send group name to the template
                $this->setVarByRef('groupName', $groupName); 
                //Get group id(pk)
                $subMajorGroupId = $this->getParam('subMajorGroupId');
                $majorGroupId = $this->getParam('majorGroupId');
                $this->setVarByRef('subMajorGroupId', $subMajorGroupId);
                $this->setVarByRef('majorGroupId', $majorGroupId);
                return 'add_edit_tpl.php';

            case 'editmajorgroup':
                $sicDivId = $this->getParam('sicDivId');
                $sicMajorGroupId = $this->getParam('sicMajorGroupId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                $valuefields = array('description'=>$description, 'code'=>$code, 'notes'=>$notes);//, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objDbSicMajorGroups->update('id', $sicMajorGroupId, $valuefields);
                return $this->nextAction('selectsicmajorgroup', array('sicDivId'=>$sicDivId,'selected'=>'init_10'));

            case 'addmajorgroup':
                $sicDivId = $this->getParam('sicDivId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');
    
                //insert into values table
                $sicmajorgroupid = $this->objDbSicMajorGroups->insert(array('divid'=>$sicDivId, 'description'=>$description, 'code'=>$code, 'notes'=>$notes));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('selectsicmajorgroup', array('sicDivId'=>$sicDivId,'selected'=>'init_10'));

            case 'addeditgroup':
                $sicGroupId = $this->getParam('sicGroupId');
                $sicMajorGroupId = $this->getParam('sicMajorGroupId');
                $this->setVarByRef('sicGroupId', $sicGroupId);
                $this->setVarByRef('sicMajorGroupId', $sicMajorGroupId);

                return "sic_addedit_group_tpl.php";

	        case 'editSocmajorgroup':
                $groupName = 'major group';
                $this->setVarByRef('groupName', $groupName); 
                $majorGroupId = $this->getParam('majorGroupId');
                $this->setVarByRef('majorGroupId', $majorGroupId);
                return 'add_edit_tpl.php';

			case 'editunitgroup':
                $groupName = 'unit group';
                $this->setVar('groupName', $groupName); 
                $unitGroupId = $this->getParam('unitGroupId');
                $minorGroupId = $this->getParam('minorGroupId');
                $subMajorGroupId = $this->getParam('subMajorGroupId');
                $majorGroupId = $this->getParam('majorGroupId');
                $this->setVar('unitGroupId', $unitGroupId);
                $this->setVar('minorGroupId', $minorGroupId);
                $this->setVar('subMajorGroupId', $subMajorGroupId);
                $this->setVar('majorGroupId', $majorGroupId);
                return 'add_edit_tpl.php';

            case 'selectsicsubgroup':
                //Get sic group Id(pk)
                $sicGroupId = $this->getParam('sicGroupId');
                //Get array of all sic sub groups under group 
                $sicSubGroups = $this->objDbSicSubGroups->getAll("WHERE groupid = '$sicGroupId'");
                //Send sic sub groups array to template
                $this->setVarByRef('sicSubGroups', $sicSubGroups);
                $sicSubGroupRow = $this->objDbSicSubGroups->getRow('groupid', $sicGroupId);
                $this->setVarByRef('sicSubGroupRow', $sicSubGroupRow);
                //Get array of sic group data 
                $sicGroup = $this->objDbSicGroups->getRow('id', $sicGroupId);
                //Send sic group array to the template
                $this->setVarByRef('sicGroup', $sicGroup);
                //Get sic major group id
                $sicMajorGroupId = $sicGroup['major_groupid'];
                //Get array of sic major group data
                $sicMajorGroup = $this->objDbSicMajorGroups->getRow('id', $sicMajorGroupId);
                //Send sic major group array to the template
                $this->setVarByRef('sicMajorGroup', $sicMajorGroup);
                //Get sic div id
                $sicDivId = $sicMajorGroup['divid'];
                //Get array of div data
                $sicDiv = $this->objDbSicDivs->getRow('id', $sicDivId);
                //Send div array to template
                $this->setVarByRef('sicDiv', $sicDiv);
                //Get sic major div id
                $sicMajorDivId = $sicDiv['major_divid'];
                //Get array of major div data
                $sicMajorDiv = $this->objDbSicMajorDivs->getRow('id', $sicMajorDivId);
                //Send major div array to template
                $this->setVarByRef('sicMajorDiv', $sicMajorDiv);
                return 'sic_subgroup_tpl.php';
                break;

            case 'editgroup':
                $sicGroupId = $this->getParam('sicGroupId');
                $sicMajorGroupId = $this->getParam('sicMajorGroupId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                $valuefields = array('description'=>$description, 'code'=>$code, 'notes'=>$notes);//, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objDbSicGroups->update('id', $sicGroupId, $valuefields);
                return $this->nextAction('selectsicgroup', array('sicMajorGroupId'=>$sicMajorGroupId,'selected'=>'init_10'));

            case 'addeditsubgroup':
                $sicGroupId = $this->getParam('sicGroupId');
                $sicSubGroupId = $this->getParam('sicSubGroupId');
                $this->setVarByRef('sicGroupId', $sicGroupId);
                $this->setVarByRef('sicSubGroupId', $sicSubGroupId);
                return "sic_addedit_subgroup_tpl.php";
                break;

            case 'addgroup':
                $sicMajorGroupId = $this->getParam('sicMajorGroupId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                //insert into values table
                $sicgroupid = $this->objDbSicGroups->insert(array('major_groupid'=>$sicMajorGroupId, 'description'=>$description, 'code'=>$code, 'notes'=>$notes));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('selectsicgroup', array('sicMajorGroupId'=>$sicMajorGroupId,'selected'=>'init_10'));

            case 'editmajdiv':
                $majDivId = $this->getParam('majDivId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                $otherMajorDivs = $this->objDbSicMajorDivs->getAll("WHERE id != '$majDivId'");
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                foreach ($otherMajorDivs as $others)
                {
                    if($code == $others['code'])
                    {
                        $msg = $this->objLanguage->languageText("mod_lrssic_code_exists_err", 'award');

                        return $this->nextAction('addeditmajordiv', array('error'=>1, 'message'=>$msg, 'majDivId'=>$majDivId, 'selected'=>'init_10')); 

                    }
                }
                $valuefields = array('description'=>$description, 'code'=>$code, 'notes'=>$notes);//, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objDbSicMajorDivs->update('id', $majDivId, $valuefields);
                return $this->nextAction('selectmajordiv',array('selected'=>'init_10'));

            case 'addmajdiv':
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                $otherMajorDivs = $this->objDbSicMajorDivs->getAll();
                //$currUser = $this->objUser->userId();
                // $currentDate = date('Y-m-d');

                foreach ($otherMajorDivs as $others)
                {
                    if($code == $others['code'])
                    {
                        $msg = $this->objLanguage->languageText("mod_lrssic_code_exists_err", 'award');
                        return $this->nextAction('addeditmajordiv', array('error'=>1, 'message'=>$msg, 'selected'=>'init_10')); 
                    }
                }
                //insert into values table
                $majdivid = $this->objDbSicMajorDivs->insert(array('description'=>$description, 'code'=>$code, 'notes'=>$notes));//;, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('selectmajordiv',array('selected'=>'init_10'));

            case 'addeditsubgroup':
                $sicGroupId = $this->getParam('sicGroupId');
                $sicSubGroupId = $this->getParam('sicSubGroupId');
                $this->setVarByRef('sicGroupId', $sicGroupId);
                $this->setVarByRef('sicSubGroupId', $sicSubGroupId);
                return "sic_addedit_subgroup_tpl.php";
                break;

            case 'addsubgroup':
                $sicGroupId = $this->getParam('sicGroupId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');
    
                //insert into values table
                $sicgroupid = $this->objDbSicSubGroups->insert(array('groupId'=>$sicGroupId, 'description'=>$description, 'code'=>$code, 'notes'=>$notes));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('selectsicsubgroup', array('sicGroupId'=>$sicGroupId, 'selected'=>'init_10'));

            case 'editsubgroup':
                $sicGroupId = $this->getParam('sicGroupId');
                $sicSubGroupId = $this->getParam('sicSubGroupId');
                $description = $this->getParam('description');
                $code = $this->getParam('code');
                $notes = $this->getParam('notes');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                $valuefields = array('description'=>$description, 'code'=>$code, 'notes'=>$notes);//, 'dateModified'=>$currentDate, 'modifierId'=>$currUser);
                $this->objDbSicSubGroups->update('id', $sicSubGroupId, $valuefields);
                return $this->nextAction('selectsicsubgroup', array('sicGroupId'=>$sicGroupId, 'selected'=>'init_10'));

            case 'addeditbranch':
                 $unionId = $this->getParam('unionId');
                 $this->setVarByRef('unionId', $unionId);
                 $branchId = $this->getParam('branchId');
                 $this->setVarByRef('branchId', $branchId);
                 return "add_edit_branch_tpl.php";
                 break;

            case 'viewbargainingunit':
                $unionId = $this->getParam('unionId');
                $branchId = $this->getParam('branchId');
                $bargainingUnits = $this->objDbUnitBranches->getAll("WHERE branchid = '$branchId'");
                $this->setVarByRef('unionId', $unionId);
                $this->setVarByRef('branchId', $branchId);
                $this->setVarByRef('bargainingUnits', $bargainingUnits);
                return "view_bargaining_unit_tpl.php";
                break; 

            case 'editbranch':
                $branchId = $this->getParam('branchId');
                $unionId = $this->getParam('unionId');
                $name = $this->getParam('name');
                $region = $this->getParam('drpRegion');
                $telephone = $this->getParam('telephone');
                $fax = $this->getParam('fax');
                $website = $this->getParam('website');
                $email = $this->getParam('email');
                $address1 = $this->getParam('address1');
                $address2 = $this->getParam('address2');
                $town = $this->getParam('town');
                $code = $this->getParam('code');
                //$currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');
                
                $districtArray = $this->objDistrict->getAll("WHERE id = '$region'");
                $districtRow = current($districtArray);
                $district = $districtRow['id'];
                if($district == NULL)
                {
                    $district = $this->objDistrict->insert(array('id'=>$region, 'name'=>'Unknown', 'urbanIndicator'=>''));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                }

                $valuefields = array('partyid'=>$unionId, 'districtid'=>$district, 'name'=>$name, 'telephone'=>$telephone, 'fax'=>$fax, 'url'=>$website, 'email'=>$email, 'addressline1'=>$address1, 'addressline2'=>$address2, 'postaltown'=>$town, 'postalcode'=>$code);//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser);
                $this->objDbBranch->update('id', $branchId, $valuefields);
                return $this->nextAction('viewbranch', array('unionId'=>$unionId, 'selected'=>'init_10'));
                break; 

            case 'addbranch':
                $unionId = $this->getParam('unionId');
                $name = $this->getParam('name');
                $region = $this->getParam('drpRegion');
                $telephone = $this->getParam('telephone');
                $fax = $this->getParam('fax');
                $website = $this->getParam('website');
                $email = $this->getParam('email');
                $address1 = $this->getParam('address1');
                $address2 = $this->getParam('address2');
                $town = $this->getParam('town');
                $code = $this->getParam('code');
                $currUser = $this->objUser->userId();
                //$currentDate = date('Y-m-d');

                $districtArray = $this->objDistrict->getAll("WHERE regionid = '$region'");
                $districtRow = current($districtArray);
                $district = $districtRow['id'];
                if($district == NULL)
                {
                    $district = $this->objDistrict->insert(array('regionid'=>$region, 'name'=>'Unknown', 'urbanIndicator'=>''));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                }

                $branchId = $this->objDbBranch->insert(array('partyid'=>$unionId, 'districtid'=>$district, 'name'=>$name, 'telephone'=>$telephone, 'fax'=>$fax, 'url'=>$website, 'email'=>$email, 'addressline1'=>$address1, 'addressline2'=>$address2, 'postaltown'=>$town, 'postalcode'=>$code));//, 'dateCreated'=>$currentDate, 'creatorId'=>$currUser));
                return $this->nextAction('viewbranch', array('unionId'=>$unionId, 'selected'=>'init_10'));
                break;

            case 'confirmdeletebranch':
                $unionId = $this->getParam('unionId');
                $branchId = $this->getParam('branchId');
                $this->objDbBranch->delete('id', $branchId);
                return $this->nextAction('viewbranch', array('unionId'=>$unionId, 'selected'=>'init_10'));
                break;

            case "edit":
                //Editing an agreement
                $agreeId = $this->getParam('agreeid');
                $agree = $this->objAgree->getRow('id',$agreeId);
                $unitId = $agree['unitid'];
                $this->setVarByRef('agreeId', $agreeId);
                $this->setVarByRef('unitId', $unitId);
                $this->setVarByRef('agreeName', $agree['name']);  
                return "editagreement_tpl.php";

			case "wage":
				//This case is reached when the user selects a wage from the list of occupation names in the agreementoverview template, and takes the user to an overview of the wage selected
				$wageId = $this->getParam('wageId');
				$agreeId = $this->getParam('agreeId');
				//$benefitId = $this->getParam('benefitId');
				//$unitId = $this->getParam('unitId');
				$this->setVarByRef('wageId', $wageId);
				$this->setVarByRef('agreeId', $agreeId);
				//$this->setVarByRef('benefitId', $benefitId);
				//$this->setVarByRef('unitId', $unitId);
				return "viewwage_tpl.php";

			case 'deletesocname':
                //Get group id(pk)
                $socNameId = $this->getParam('socNameId'); 
                //Get unit group id(pk)
                $unitGroupId = $this->getParam('unitGroupId');
                //Delete soc name
                $this->objDbSocNames->delete('id', $socNameId);
                //Set next action params
                $params = array('unitGroupId'=>$unitGroupId);
                //Set next action
                return $this->nextAction('selectsocname', $params);

			case 'editsocname':
                //Set group name
                $groupName = 'socname';
                //Send group name to the template
                $this->setVarByRef('groupName', $groupName); 
                //$socName = $this->objDbSocNames->getRow('id', $socNameId);
                //Send soc name array to template
                $socNameId = $this->getParam('socNameId');
                $unitGroupId = $this->getParam('unitGroupId');
                $minorGroupId = $this->getParam('minorGroupId');
                $subMajorGroupId = $this->getParam('subMajorGroupId');
                $majorGroupId = $this->getParam('majorGroupId');
                $results = $this->getParam('results');
                $group = $this->getParam('group');
                $searchterm = $this->getParam('searchterm');
                $groupId = $this->getParam('groupId');

                $this->setVarByRef('socNameId', $socNameId);
                $this->setVarByRef('unitGroupId', $unitGroupId);
                $this->setVarByRef('minorGroupId', $minorGroupId);
                $this->setVarByRef('subMajorGroupId', $subMajorGroupId);
                $this->setVarByRef('majorGroupId', $majorGroupId);
                $this->setVarByRef('results', $results); 
                $this->setVarByRef('group', $group);
                $this->setVarByRef('searchterm', $searchterm);
                $this->setVarByRef('groupId', $groupId);
                return 'add_edit_tpl.php';

			case "editagreement":
				//This case is reached when the submit button is pressed in the edit agreement template and is edited
				$id 			= $this->getParam('unitId');
				$agreeDate 		= $this->getParam('calendardate');
				$agreeMonths 	= $this->getParam('months');
				$workers 		= $this->getParam('now');
				$agreeNotes 	= $this->getParam('agreenotes');
				$agreeType 		= $this->getParam('agreeType');
				$hours 			= $this->getParam('how');
				$agreeId 		= $this->getParam('agreeId');
				$agreeNameDate 	= date("d M Y", strtotime($agreeDate));
				
				$unit 			= $this->objDbUnit->getRow('id', $id);   
				$benefit 		= $this->objBenefit->getRow('agreeid', $agreeId);
				$benefitfields 	= array('agreeid'=>$agreeId, 'nameid'=>'init_7', 'value'=>$hours);
				$agreefields 	= array('typeid'=>$agreeType, 'unitid'=>$id, 'name'=>"{$unit['name']}-$agreeNameDate", 'implementation'=>$agreeDate, 'length'=>$agreeMonths, 'workers'=>$workers, 'notes'=>$agreeNotes);
				
				$this->objBenefit->update('id', $benefit['id'], $benefitfields);
				$this->objAgree->update('id', $agreeId, $agreefields);
				
				return $this->nextAction('agreementoverview', array('id'=>$agreeId, 'unitId'=>$id, 'selected'=>'init_10'));

			case "editwage":
				// This case is reached when the user selects the link edit wage in the viewwage template
				$wageId = $this->getParam('wageId');
				$agreeId = $this->getParam('agreeId');
				$benefitId = $this->getParam('benefitId');
				$this->setVarByRef('wageId', $wageId);
				$this->setVarByRef('agreeId', $agreeId);

				// Helps to create drop down which is populated by a filter associated this the text input//             $objXajax = new xajax($this->uri(array('action'=>'editwage')));
				// $objXajax->registerFunction(array($this,"updateSocList"));
				// $objXajax->processRequests(); // XAJAX method to be called
				// $this->appendArrayVar('headerParams', $objXajax->getJavascript());
	            return "editwage_tpl.php";

            default:
                return $this->nextAction('home');
		}
    }

    public function sendFeedbackEmail() {
        $mailer = $this->newObject('kngemail','utilities');
        $name = $this->getParam('name');
		$telephone = $this->getParam('telephone');
		$email = $this->getParam('email');
        $hours = $this->getParam('hours');
		$mailer->setup($email,$name);
        $currency = $this->objSysConfig->getValue('CURRENCY_ABREVIATION','award');
		$subject = $this->objLanguage->languageText('mod_lrs_feedbacksubject', 'award');
		$objTable = $this->newObject('htmltable','htmlelements');
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('word_name').": ",'20%');
		$objTable->addCell($name,'80%');
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('word_telephone').": ",'20%');
		$objTable->addCell($telephone,'80%');
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('word_email').": ",'20%');
		$objTable->addCell($email,'80%');
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('phrase_bucomp').": ",'20%');
		$objTable->addCell($this->getParam('buname'),'80%');
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('word_occupation').": ");
		$objTable->addCell($this->getParam('socname'));
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('phrase_startdate').": ");
		$objTable->addCell($this->getParam('agreedate'));
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('phrase_oldwagerate').": ");
		$objTable->addCell($currency.number_format($this->getParam('oldwage'),2)." ".
		                   $this->objDbPayPeriodType->getPayPeriodName($this->getParam('oldpptype')));
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('word_increase').": ");
		$objTable->addCell(round($this->getParam('increase'),1).'%');
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('phrase_newwagerate').": ");
		$objTable->addCell($currency.number_format($this->getParam('wage'),2)." ".
		$this->objDbPayPeriodType->getPayPeriodName($this->getParam('pptype')));
		$objTable->endRow();
		$objTable->addCell($this->objLanguage->languageText('phrase_hours').": ");
		$objTable->addCell($hours);
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell($this->objLanguage->languageText('word_comments').": ");
		$objTable->addCell($this->getParam('comment'));
		$objTable->endRow();
		$body = $this->objLanguage->languageText('mod_lrs_emailheader', 'award')."<br /><br />".$objTable->show();
		$name = $this->objUser->fullname(1);
		$email = $this->objUser->email(1);
		$mailer->sendMail($name, $subject, $email, $body);
    }
}
?>
