<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
/**
* class to generate HTML to pass back to the templates via ajax
*
* @author Nic Appleby
* @package LRS
*/
class awardtemplates extends object {

	/**
     * Init method to load required classes
     */
     function init() {


		$this->objDbAgreeType = $this->getObject('dbagreetypes','awardapi');
       	$this->objDbAgree = $this->getObject('dbagreement', 'awardapi');
        $this->objDbAgreeType = $this->getObject('dbagreetypes','awardapi');
		$this->objDbWages = $this->getObject('dbwage', 'awardapi');
		$this->objDbPayPeriodType = $this->getObject('dbpayperiodtypes', 'awardapi');
	   	$this->objDbUnit = $this->getObject('dbunit','awardapi');
		$this->objDbWages = $this->getObject('dbwage', 'awardapi');
		$this->objDbPayPeriodType = $this->getObject('dbpayperiodtypes', 'awardapi');
	    $this->objBenefitType = $this->getObject('dbbenefittypes', 'awardapi');
        $this->objBenefitName = $this->getObject('dbbenefitnames', 'awardapi');
        $this->objBenefit = $this->getObject('dbbenefits', 'awardapi');
        $this->objDbSocMajorGroup = $this->getObject('dbsocmajorgroup', 'awardapi');
		$this->objDbSicMajorDivs = $this->getObject('dbsicmajordiv', 'awardapi');
		$this->objDbSicDivs = $this->getObject('dbsicdiv', 'awardapi');
		$this->objDbIndexTypes = $this->getObject('dbindex','awardapi');
		$this->objDbIndexValues = $this->getObject('dbindexvalues','awardapi');

		$this->indexFacet = $this->getObject('awardindex_facet', 'award');
		$this->objSummary = $this->getObject('dbindexsummary','award');

       	$this->objLanguage = $this->getObject('language','language');
		$this->objConfig = $this->getObject('altconfig','config');
		$this->objSysConfig = $this->getObject('dbsysconfig','sysconfig');
		$this->objUser = $this->getObject('user','security');
		$this->objPop= $this->newObject('windowpop', 'htmlelements');
		
     }

     /**
      * Method to generate the tab box containing the wage aggregate information
      *
      * @param string $industry the id of the SIC in question or 'all' for all industries
      * @param string $indexId the id of the index to use for comparison
      * @param string $mode the function to perform for aggregation:
      * 							'min' for average minimum wage
      * 							'max' for average maximum wage
      * 							'med' for median wage
      * @param string $soc the SOC to be searched
      * @param string $wageTypeId the id of the pament period type (per hour/pee week etc.)
      * @param integer $years the number of years to display in the table
      * @param string $minorSic the minor SIC division in question
      * @return the html code for the box containing the aggregation table
      */
    public function generateAggregates($industry,$indexId,$mode,$soc,$wageTypeId,$agreeId,$startYear = null,$years = 5,$minorSic = 'all',$subSic = 'all') {
    	$units = array();
    	$wageType = $this->objDbPayPeriodType->getRow('id',$wageTypeId);
		$decimals = ($wageType['factor'] == 0)? 2 : 0;
    	$wageTable = $this->newObject('htmltable','htmlelements');
    	$wageTable->cellspacing = '2';
    	//$wageTable->cellpadding = '2';
    	$wageTable->id = 'wageTable';
    	$currency = $this->objSysConfig->getValue('CURRENCY_ABREVIATION','award');
        $indexName = $this->indexFacet->getIndexShortName($indexId);
    	//,"$indexName ".$this->objLanguage->languageText('phrase_avgincrease')
        switch ($mode) {
            case 'weigh': $modeWord = $this->objLanguage->languageText('mod_lrspostlogin_weightedavgs', 'award'); break;
            case 'med': $modeWord = $this->objLanguage->languageText('word_median'); break;
            default: $modeWord = $this->objLanguage->languageText('word_average'); break;
        }

    	$wageTable->addHeader(array($this->objLanguage->languageText('word_year'),
									$this->objLanguage->languageText('word_sample'),
									"{$wageType['name']} ".$modeWord." ($currency)",
									$this->objLanguage->languageText('phrase_increasepercentage'),
									$this->objLanguage->languageText('phrase_actualincrease')." ".$this->indexFacet->getIndexShortName($indexId),
									$this->objLanguage->languageText('mod_lrspostlogin_agreeworkers','award' )));
    	$thisYear = ($startYear == null)? date('Y') : $startYear+($years-1);
    	if ($thisYear > date('Y')) {
    		$thisYear = date('Y');
    	}
		$this->loadClass('textinput','htmlelements');
		if ($this->objUser->isAdmin()) {
			$startYearInputType = 'text';
			$startYearLabel = $this->objLanguage->languageText("mod_lrspostlogin_startyear", 'award').": ";
			$defaultYear = $thisYear-($years-1);
		} else {
			$startYearInputType = 'hidden';
			$startYearLabel = '';
			$defaultYear = date('Y');
		}
		$startYear = new textinput('startYear',$defaultYear,$startYearInputType,4);

		$mDiv = $this->objDbSicDivs->getRow('id',$minorSic);
		if ($industry == 'all' || $industry != $mDiv['major_divid']) {
			$minorSic = $subSic = 'all';
		}

    	$class = 'even';
    	for ($i=$thisYear-($years-1);$i<=$thisYear;$i++) {
    		$class = ($class=='even')? 'odd' : 'even';
    		switch ($mode) {
    			case 'max':
    				$ave = $this->objDbWages->getAverageMaxWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSic);
    				$avgs = str_replace('[AVG]',$this->objLanguage->languageText('mod_lrspostlogin_maxavgs', 'award'),$this->objLanguage->languageText('mod_lrspostlogin_avgs', 'award'));
    				break;
    			case 'med':
    				$ave = $this->objDbWages->getMedianWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSic);
    				$avgs = str_replace('[AVG]',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'),$this->objLanguage->languageText('mod_lrspostlogin_avgs', 'award'));
    				break;
    			case 'weigh':
					$ave = $this->objDbWages->getWeightedAverageWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSic);
    				$avgs = str_replace('[AVG]',$this->objLanguage->languageText('mod_lrspostlogin_weightedavgs', 'award'),$this->objLanguage->languageText('mod_lrspostlogin_avgs', 'award'));
    				break;
				case 'min':
    				$avgs = str_replace('[AVG]',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'),$this->objLanguage->languageText('mod_lrspostlogin_avgs', 'award'));
    				$ave = $this->objDbWages->getAverageMinWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSic);
    				break;
				case 'ave':
    			default:
					$ave = $this->objDbWages->getAverageWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSic);
    				$avgs = str_replace('[AVG]',$this->objLanguage->languageText('word_average'),$this->objLanguage->languageText('mod_lrspostlogin_avgs', 'award'));
    				break;
    			
				
    		}
            $avgs = str_replace('[PERIOD]', strtolower($wageType['name']), $avgs);
    		
			$value = ($ave['average'] == 0)? '--' : number_format(($ave['average']),$decimals);
    		$pinc = ($ave['increaseAve'] == 0)? '--' : round(($ave['increaseAve']), 2);
    		$actualIncrease = ($ave['realAve'] == 0)? '--' : round(($ave['realAve']), 2);
    		
    		$wageTable->startRow($class);
    		$wageTable->addCell($i,null,null,'center');
    		$wageTable->addCell($ave['sample'],null,null,'center');
    		$wageTable->addCell($value,null,null,'center');
    		$wageTable->addCell($pinc,null,null,'center');
    		$wageTable->addCell($actualIncrease,null,null,'center');
    		$wageTable->addCell(number_format($ave['workers']),null,null,'center');
    		$wageTable->endRow();
    		$prevYear = $ave;
    		if (isset($ave['units'])) {
    			foreach ($ave['units'] as $unit) {
    				if (!isset($units[$unit])) {
    					$units[$unit] = 1;
    				} else {
    					$units[$unit]++;
    				}
    			}
    		}
    	}
    	$sum = array_sum($units);
    	if (count($units) > 0) {
    		$continuity = round(((($sum/count($units))/$years)*100),1);
    		if ($this->objUser->isAdmin()) {
    			$link = "<a href='".$this->uri(array('action'=>'continuity','selected'=>$this->getParam('selected'),
    			                                     'length'=>$years,'industry'=>$industry,'mode'=>$mode,'soc'=>$soc,
    			                                      'wageTypeId'=>$wageTypeId,'agreeId'=>$agreeId,'minorSic'=>$minorSic,
    			                                      'startYear'=>$defaultYear,'indexId'=>$indexId,'subsicid'=>$subSic),'award')
    			        ."'>". $this->objLanguage->languageText('word_continuous')."</a>";
    		} else {
    			$link = $this->objLanguage->languageText('word_continuous');
    		}
    		$cTable = $this->newObject('htmltable','htmlelements');
    		$cTable->startRow();
    		$cTable->addCell($this->objLanguage->languageText('mod_lrswages_datais', 'award')." <b>$continuity%</b> $link",null,null,'right');
    		$cTable->endRow();
    		$cTable = $cTable->show();
    	} else {
    		$cTable = '';
    	}

    	($soc == 'all')? $socName['description'] = $this->objLanguage->languageText('phrase_allsocmajgrps') : $socName = $this->objDbSocMajorGroup->getRow('id',$soc);
		($industry == 'all')? $industryName['description'] = $this->objLanguage->languageText('phrase_allindustries') : $industryName = $this->objDbSicMajorDivs->getRow('id',$industry);
		if ($minorSic != 'all') {
			$minSicRecord = $this->objDbSicDivs->getRow('id',$minorSic);
            $industryName['description'] .= " - {$minSicRecord['description']}";
		}
		
		switch ($agreeId) {
			case 'cb':
				$agreeType = $this->objLanguage->languageText('phrase_collectivebargaining');
				break;
			case 'all':
				$agreeType = $this->objLanguage->languageText('phrase_alltypes');
				break;
			default:
				$agreeRec = $this->objDbAgreeType->getRow('id', $agreeId);
				$agreeType = $agreeRec['name']." ".$this->objLanguage->languageText('word_agreements');
				break;
		}
    	$avgs = str_replace('[SOC]', $socName['description'], $avgs);
    	$avgs = str_replace('[SIC]', $industryName['description'], $avgs);
    	$avgs = str_replace('[YEARS]', $years, $avgs);
		$avgs = str_replace('[AGREE]', $agreeType, $avgs);
		
    	$refineTable = $this->newObject('htmltable','htmlelements');
    	$modeSelect = new dropdown('modeSelect');
    	if ($this->objUser->isAdmin()) {
			$modeSelect->addOption('min',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'));
		}
		$modeSelect->addOption('ave',$this->objLanguage->languageText('word_average'));
		$modeSelect->addOption('weigh',$this->objLanguage->languageText('mod_lrspostlogin_weightedavgs', 'award'));
		$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
		if ($this->objUser->isAdmin()) {
			$modeSelect->addOption('max',$this->objLanguage->languageText('mod_lrspostlogin_maxavgs', 'award'));
		}
		$modeSelect->setSelected($mode);

		$socMajGrps = $this->objDbSocMajorGroup->getAll("ORDER BY id DESC");
		$socSelect = new dropdown('socSelect');
    	if (is_array($socMajGrps)) {
			foreach ($socMajGrps as $grp) {
				$socSelect->addOption($grp['id'],$grp['description']);
			}
		}
		$socSelect->setSelected((int)$soc);

		$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
		$sicSelect = new dropdown('sicSelect');
    	$sicSelect->addOption('all',$this->objLanguage->languageText('phrase_allindustries'));
		if (is_array($industries)) {
			foreach ($industries as $ind) {
				if (strlen($ind['description']) > 45) {
					$ind['description'] = substr($ind['description'],0,42).'...';
				}
				$sicSelect->addOption($ind['id'],$ind['description']);
			}
		}
		$sicSelect->setSelected((int)$industry);

		$indexes = $this->indexFacet->getIndexes();
		$indexSelect = new dropdown('indexSelect');
    	if (is_array($indexes)) {
			foreach ($indexes as $index) {
				$indexSelect->addOption($index['id'],$index['shortname']);
			}
		}
		$indexSelect->setSelected((int)$indexId);


		$yearSelect = new dropdown('yearSelect');
    	$yearSelect->addOption(5,'5');
		$yearSelect->addOption(10,'10');
		$yearSelect->addOption(15,'15');
		$yearSelect->setSelected((int)$years);

    	$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
		$agreeSelect = new dropdown('agreeSelect');
    	$agreeSelect->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
		$agreeSelect->addOption('cb',$this->objLanguage->languageText('phrase_collectivebargaining'));
		if (is_array($agreeTypes)) {
			foreach ($agreeTypes as $type) {
				$typename = $type['name'];
				if (strlen($typename) > 40) {
					$typename = substr($typename,0,37)."...";
				}
				$agreeSelect->addOption($type['id'],$typename);
			}
		}
		$agreeSelect->setSelected($agreeId);


		$refineSubmit = new button("refineSubmit",$this->objLanguage->languageText('phrase_updatetable'),
			"javascript:showLoading(this,'wageTable');
			javascript:ajax_getAggregates(
                    document.getElementById('input_sicSelect').value,
					document.getElementById('input_indexSelect').value,
                    document.getElementById('input_modeSelect').value,
					document.getElementById('input_socSelect').value,
                    '$wageTypeId',
					document.getElementById('input_agreeSelect').value,
                    document.getElementById('input_startYear').value,
					document.getElementById('input_yearSelect').value,
                    '$minorSic',
                    '$subSic')");

		//populate table of options to refine statistics
		$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype", 'award').": ");
    	$refineTable->addCell($modeSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype", 'award').": ");
    	$refineTable->addCell($agreeSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_occupation", 'award').": ");
    	$refineTable->addCell($socSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_index", 'award').": ");
    	$refineTable->addCell($indexSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_industry", 'award')." ");
    	$refineTable->addCell($sicSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_noyears", 'award').": ");
    	$refineTable->addCell($yearSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('&nbsp;');
    	$refineTable->addCell('&nbsp;');
    	$refineTable->addCell($startYearLabel);
    	$refineTable->addCell($startYear->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('');
    	$refineTable->addCell($refineSubmit->show(),null,null,'right');
    	$refineTable->addCell('');
    	$refineTable->addCell('');
    	$refineTable->endRow();

    	//add links to refine table to SIC sub division level
    	if ($industry != 'all') {
    		if ($minorSic != 'all') {
    			$subDivs = $this->objDbSicMajorDivs->getSubDivs($industry,$minorSic);
                
    			$ind = $this->objDbSicMajorDivs->getRow('id',$industry);
    			$sicMinorLinks = "<p><b><i>".$this->objLanguage->languageText('mod_lrspostlogin_sicminorlinks', 'award')."</i></b></p>";
    			if (!empty($subDivs)) {
    				foreach ($subDivs as $div) {
    					if ($subSic == $div['id']) {
							$sicMinorLinks .= "<b>{$ind['code']}{$mDiv['code']}{$div['code']} {$div['description']}</b><br/>";
    					} else {
    						$link = "javascript:showLoading(this,'wageTable');
									javascript:ajax_getAggregates(document.getElementById('input_sicSelect').value,
									document.getElementById('input_indexSelect').value,document.getElementById('input_modeSelect').value,
									document.getElementById('input_socSelect').value,'$wageTypeId',
									document.getElementById('input_agreeSelect').value,document.getElementById('input_startYear').value,
									document.getElementById('input_yearSelect').value,'{$mDiv['id']}','{$div['id']}')";
    						$sicMinorLinks .= "<a href=\"javascript:void(0)\" onclick=\"$link\">{$ind['code']}{$mDiv['code']}{$div['code']} {$div['description']}</a><br/>";
    					}
    				}
					$link = "javascript:showLoading(this,'wageTable');
							 javascript:ajax_getAggregates(document.getElementById('input_sicSelect').value,
							 document.getElementById('input_indexSelect').value,document.getElementById('input_modeSelect').value,
							 document.getElementById('input_socSelect').value,'$wageTypeId',
							 document.getElementById('input_agreeSelect').value,document.getElementById('input_startYear').value,
							 document.getElementById('input_yearSelect').value,'all','all')";
    				$lDescription = $this->objLanguage->languageText('phrase_backto')." {$mDiv['description']}";
					$sicMinorLinks .= "<a href=\"javascript:void(0)\" onclick=\"$link\">{$ind['code']}{$mDiv['code']}0 $lDescription</a><br/>";
    			}
    		} else {
    			$sicMinorDivs = $this->objDbSicMajorDivs->getMinorDivs($industry);
    			$ind = $this->objDbSicMajorDivs->getRow('id',$industry);
    			$sicMinorLinks = "<p><b><i>".$this->objLanguage->languageText('mod_lrspostlogin_sicminorlinks', 'award')."</i></b></p>";
    			if (!empty($sicMinorDivs)) {
    				foreach ($sicMinorDivs as $div) {
    					$link = "javascript:showLoading(this,'wageTable');
						javascript:ajax_getAggregates(document.getElementById('input_sicSelect').value,
						document.getElementById('input_indexSelect').value,document.getElementById('input_modeSelect').value,
						document.getElementById('input_socSelect').value,'$wageTypeId',
						document.getElementById('input_agreeSelect').value,document.getElementById('input_startYear').value,
						document.getElementById('input_yearSelect').value,'{$div['id']}')";
    					$sicMinorLinks .= "<a href=\"javascript:void(0)\" onclick=\"$link\">{$ind['code']}{$div['code']} {$div['description']}</a><br/>";
    				}
    			}
    		}
    	} else {
    		$sicMinorLinks = '';
    	}

    	//link to print friendly version
    	$pLink = $this->newObject('link','htmlelements');
    	$wageTable->border = 1;
        $printHTML = "<p><b><i>$avgs</i></b></p>".$wageTable->show().$cTable;
        $printHTML = preg_replace('/thead/i','tr',$printHTML);
		$this->setSession('award_print_content', htmlentities($printHTML));
        $pLink->link($this->uri(array('action'=>'printpdf', 'title'=>$avgs),'award'));
        $pLink->link = $this->objLanguage->languageText('mod_lrs_printfriendly','award');
        $pLink->extra = "target = '_blank'";
        $printLink = $pLink->show();
        $wageTable->border = 0;
    	
    	$text = $this->objLanguage->languageText('mod_lrs_refineheading','award');
    	$avgContent = "<b><i>$avgs</i></b>"."<div align='right'>$printLink</div><div id='wageTable'>".$wageTable->show()."</div>".$cTable."<br /><b><i>".$this->objLanguage->languageText('mod_lrs_refineaggregates','award')."</i></b><br />$text<br />"
    					.$refineTable->show()."<br/>$sicMinorLinks";
        return $avgContent;
    }

    /**
      * Method to generate the tab box containing the default wage aggregate information
      *
      * @return the html code for the box containing the aggregation table
      */
    public function getDefaultAggregates() {
        
		$payPeriod = $this->objDbPayPeriodType->getDefaultPPType();
		$wageTypeId = $payPeriod['id'];
		$wageTable = $this->newObject('htmltable','htmlelements');
    	$wageTable->cellspacing = '2';
    	$wageTable->cellpadding = '2';
    	$wageTable->id = 'wageTable';
    	$currency = $this->objSysConfig->getValue('CURRENCY_ABREVIATION','award');
    	$wageTable->addHeader(array($this->objLanguage->languageText('word_year'),$this->objLanguage->languageText('word_sample'),
    			"{$payPeriod['pay_period_type_name']} ".$this->objLanguage->languageText('word_average')." ($currency)",
    			$this->objLanguage->languageText('phrase_increasepercentage'),
    			$this->objLanguage->languageText('phrase_actualincrease')." ".$this->indexFacet->getIndexShortName(1),
    			$this->objLanguage->languageText('mod_lrspostlogin_agreeworkers', 'award')));
    	$thisYear = date('Y');
    	$objWageDefault = $this->getObject('wagedefault','award');
    	$defaultData = $objWageDefault->getAll('ORDER BY year');
    	$class = 'even';
    	foreach ($defaultData as $row) {
    		$class = ($class=='even')? 'odd' : 'even';
    		$wageTable->startRow($class);
    		$wageTable->addCell($row['year'],null,null,'center');
    		$wageTable->addCell($row['sample'],null,null,'center');
    		$wageTable->addCell(round($row['value'],2),null,null,'center');
    		$wageTable->addCell($row['inc'].'%',null,null,'center');
    		$wageTable->addCell($row['actual'],null,null,'center');
    		$wageTable->addCell(number_format($row['workers']),null,null,'center');
    		$wageTable->endRow();
    	}

    	if ($this->objUser->isAdmin()) {
    		$link = "<a href='".$this->uri(array('action'=>'continuity','selected'=>$this->getParam('selected'),
    	                                     'length'=>5,'industry'=>'all','mode'=>'min','soc'=>'9',
    	                                      'wageTypeId'=>$wageTypeId,'agreeId'=>'all','minorSic'=>'all',
    	                                      'startYear'=>$thisYear,'indexId'=>'1','subsicid'=>'all'),'award')
    		        ."'>". $this->objLanguage->languageText('word_continuous','award')."</a>";
    	} else {
    		$link = $this->objLanguage->languageText('word_continuous');
    	}
    	
    	$continuity = $objWageDefault->getContinuity();
    	
    	$cTable = $this->newObject('htmltable','htmlelements');
    	$cTable->startRow();
    	$cTable->addCell($this->objLanguage->languageText('mod_lrswages_datais','award')." <b>{$continuity['continuity']}%</b> $link",null,null,'right');
    	$cTable->endRow();
    	$cTable = $cTable->show();

    	$socName = $this->objDbSocMajorGroup->getName('9');
        $industryName = $this->objLanguage->languageText('phrase_allindustries');
		
        $avgs = str_replace('[AVG]',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'),$this->objLanguage->languageText('mod_lrspostlogin_avgs', 'award'));
    	$avgs = str_replace('[SOC]',$socName,$avgs);
    	$avgs = str_replace('[SIC]',$industryName,$avgs);
    	$avgs = str_replace('[YEARS]',5,$avgs);

    	$refineTable = $this->newObject('htmltable','htmlelements');
    	$modeSelect = new dropdown('modeSelect');
    	$modeSelect->addOption('min',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'));
		$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
		$modeSelect->addOption('max',$this->objLanguage->languageText('mod_lrspostlogin_maxavgs', 'award'));
		$modeSelect->setSelected('min');

		$socMajGrps = $this->objDbSocMajorGroup->getAll("ORDER BY id DESC");
		$socSelect = new dropdown('socSelect');
    	if (is_array($socMajGrps)) {
			foreach ($socMajGrps as $grp) {
				$socSelect->addOption($grp['id'],$grp['description']);
			}
		}
		$socSelect->setSelected(9);

		$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
		$sicSelect = new dropdown('sicSelect');
    	$sicSelect->addOption('all',$this->objLanguage->languageText('phrase_allindustries'));
		if (is_array($industries)) {
			foreach ($industries as $ind) {
				if (strlen($ind['description']) > 45) {
					$ind['description'] = substr($ind['description'],0,42).'...';
				}
				$sicSelect->addOption($ind['id'],$ind['description']);
			}
		}
		$sicSelect->setSelected('all');

		$indexes = $this->indexFacet->getIndexes();
		$indexSelect = new dropdown('indexSelect');
    	if (is_array($indexes)) {
			foreach ($indexes as $index) {
				$indexSelect->addOption($index['id'],$index['shortname']);
			}
		}
		$indexSelect->setSelected(1);


		$yearSelect = new dropdown('yearSelect');
    	$yearSelect->addOption(5,'5');
		$yearSelect->addOption(10,'10');
		$yearSelect->addOption(15,'15');
		$yearSelect->setSelected(5);

		$this->loadClass('textinput','htmlelements');
		if ($this->objUser->isAdmin()) {
			$startYearInputType = 'text';
			$startYearLabel = $this->objLanguage->languageText("mod_lrspostlogin_startyear", 'award').": ";
			$defaultYear = $thisYear-(4);
		} else {
			$startYearInputType = 'hidden';
			$startYearLabel = '';
			$defaultYear = date('Y');
		}
		$startYear = new textinput('startYear',$defaultYear,$startYearInputType,4);

    	$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
		$agreeSelect = new dropdown('agreeSelect');
    	$agreeSelect->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
		$agreeSelect->addOption('cb',$this->objLanguage->languageText('phrase_collectivebargaining'));
		if (is_array($agreeTypes)) {
			foreach ($agreeTypes as $type) {
				$typename = $type['name'];
				if (strlen($typename) > 40) {
					$typename = substr($typename,0,37)."...";
				}
				$agreeSelect->addOption($type['id'],$typename);
			}
		}
		$agreeSelect->setSelected('all');


		$refineSubmit = new button("refineSubmit",$this->objLanguage->languageText('phrase_updatetable'),
			"javascript:showLoading(this,'wageTable');
			javascript:xajax_getAggregates(document.getElementById('input_sicSelect').value,
					document.getElementById('input_indexSelect').value,document.getElementById('input_modeSelect').value,
					document.getElementById('input_socSelect').value,'$wageTypeId',
					document.getElementById('input_agreeSelect').value,document.getElementById('input_startYear').value,
					document.getElementById('input_yearSelect').value)");

		//populate table of options to refine statistics
		$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype",'award').": ");
    	$refineTable->addCell($modeSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype",'award').": ");
    	$refineTable->addCell($agreeSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_occupation",'award').": ");
    	$refineTable->addCell($socSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_index",'award').": ");
    	$refineTable->addCell($indexSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_industry",'award').": ");
    	$refineTable->addCell($sicSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_noyears",'award').": ");
    	$refineTable->addCell($yearSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('&nbsp;');
    	$refineTable->addCell('&nbsp;');
    	$refineTable->addCell($startYearLabel);
    	$refineTable->addCell($startYear->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('');
    	$refineTable->addCell($refineSubmit->show(),null,null,'right');
    	$refineTable->addCell('');
    	$refineTable->addCell('');
    	$refineTable->endRow();

    	$pLink = $this->newObject('link','htmlelements');
    	$wageTable->border = 1;
        $printHTML = "<p><b><i>$avgs</i></b></p>".$wageTable->show().$cTable;
        $printHTML = preg_replace('/thead/i','tr',$printHTML);
        $pLink->link($this->uri(array('action'=>'printpdf','pdfcontent'=>$printHTML),'award'));
        $pLink->link = $this->objLanguage->languageText('mod_lrs_printfriendly', 'award');
        $pLink->extra = "target = '_blank'";
        $printLink = $pLink->show();
        $wageTable->border = 0;
    	
    	$text = $this->objLanguage->languageText('mod_lrs_refineheading','award');
    	$avgContent = "<p><b><i>$avgs</i></b></p>"."<div align='right'>$printLink</div>".$wageTable->show().$cTable."<p><b><i>".$this->objLanguage->languageText('mod_lrs_refineaggregates','award')."</i></b></p>$text<br/>"
    					.$refineTable->show();
    	return $avgContent;
    }

	function getInflationSummary($indexId=null){
		//Getting the Index type from the database
		$allData = $this->objDbIndexTypes->getAll();
		
		if ($indexId != null) {
			$data = $this->objDbIndexTypes->getRow('id',$indexId);
			$shortname = $data['shortname'];
		} else {
			
			$shortname = $allData[0]['shortname'];
			$indexId = $allData[0]['id'];
		}

		// Initiating the dropdown
		$indexSelect = new dropdown('indexSelect2');
        $indexSelect->addFromDB($allData,'shortname','id',$indexId);
        $indexSelect->extra = 'onchange = "javascript:changeInflationIndex(this.value)"';

		$month = date('m');
		$year = date('Y');
		$monthWord = date('F');

		$percentageDiff = $this->indexFacet->getPercentageDifference($month, $year, $indexId);
		$boxcontent = "<b><i>".$this->objLanguage->languageText('mod_lrs_inflationrate', 'award')." ($shortname): $percentageDiff% ($monthWord $year)<br /> </i></b> ";

		$linkTable2 = $this->newObject('htmltable','htmlelements');
		$linkTable2->startRow();
		$linkTable2->addCell('<b><i>'.$this->objLanguage->languageText('mod_lrs_changeindex', 'award').': </i></b> '.$indexSelect->show());
		$linkTable2->endRow();
		$boxcontent .= $linkTable2->show();
		$imageUri = $this->uri(array('action'=>'plotinflationgraph','indexId'=>$indexId,'year'=>$year, 'width'=>600, 'height'=>400),'award');

		// summary
		$summary = $this->objSummary->getRow('indexid',$indexId);
		if ($this->objUser->isAdmin()) {
            $link = $this->newObject('link','htmlelements');
            $link->link("javascript:;");
            $word_save = $this->objLanguage->languageText('word_save');
            $word_cancel = $this->objLanguage->languageText('word_cancel');
            $link->extra = "onclick='javascript:editSummary(\"$indexId\",\"{$summary['summary']}\",\"$word_save\",\"$word_cancel\")'";
            $link->link = $this->objLanguage->languageText('mod_award_editsummary','award');
			$summary['summary'] = ($summary['summary'] == '')? "<i>".$this->objLanguage->languageText('mod_award_nosummary','award')."</i>" : $summary['summary'];
			$summary['summary'] .= '<br />'.$link->show();
		}
        $boxcontent .= "<div id='inflationSummary'>";
		if ($summary['summary'] != '') {
			$boxcontent .= "{$summary['summary']} <br />";
		}
        $boxcontent .= "</div>";
		$graph = "<img src='$imageUri' alt='inflation graph'/>";  //width='450' height='300'
		$boxcontent .= $graph;
		return $boxcontent;
	}

	public function getGenderSummary($sic,$aggregate,$agreeType,$year=null) {
		$tab = $this->newObject('tabcontent','htmlelements');
        $tablabel = $this->objLanguage->languageText('mod_lrspostlogin_gendersummary', 'award');
		
		if (($year == null) || ($year > date('Y'))) {
			$year = date('Y')-1;
		}

		$objBCEA = $this->getObject('dbbcea','award');
		$headArray = array($this->objLanguage->languageText('word_category'),$this->objLanguage->languageText('mod_lrspostlogin_typebenefit', 'award'),
			$this->objLanguage->languageText('word_benefit'),$this->objLanguage->languageText('word_average'),
			$this->objLanguage->languageText('word_bcea'),$this->objLanguage->languageText('word_comment'),);
		$objTable = $this->newObject('htmltable','htmlelements');
		$objTable->cellspacing=2;
		$objTable->addHeader($headArray);	
		$bcea = $objBCEA->getArray('SELECT DISTINCT category FROM tbl_award_gender_bcea');
		$class = 'other';
		foreach ($bcea as $cat) {
			$cells = $objBCEA->getArray("SELECT * FROM tbl_award_gender_bcea WHERE category LIKE '{$cat['category']}'");
			$count = 0;
			$total = count($cells);
			$objTable->startRow($class);
			$objTable->addCell($cat['category'],null,null,'center',null,"rowspan=$total border=1");
			foreach ($cells as $cell) {
                $benefitName = $this->objBenefitName->getRow('id',$cell['nameid']);
				$average = $this->getGenderAverage($sic, $aggregate, $agreeType, $year, $cell['nameid']);
				$count++;
				$objTable->addCell($cell['type'],null,null,'center');
				$objTable->addCell($benefitName['name'],'30%',null,'center');
				$objTable->addCell($average,null,null,'center');
				$objTable->addCell($cell['bcea'],null,null,'center');
				$objTable->addCell($cell['comment'],null,null,'center');
				$objTable->endRow();
				if ($count < $total) $objTable->startRow($class);
			}
			
			$class = ($class == 'other')? 'even' : 'other';
		}
		
		//print friendly link
		$this->loadClass('form','htmlelements');
		$this->loadClass('button','htmlelements');
		$this->loadClass('dropdown','htmlelements');
		$this->loadClass('textinput','htmlelements');
		
		$objTable->border = 1;
		$pLink = $this->newObject('link','htmlelements');
		$heading = $this->getObject('htmlheading','htmlelements');
		$heading->str = $this->objLanguage->languageText('mod_lrspostlogin_gendersummary', 'award');
		$printHTML = "<p><b><i>".$heading->show()."</i></b></p>".$objTable->show();
        $printHTML = preg_replace('/thead/i','tr',$printHTML);
        $pLink->link('javascript:;');
        $pLink->link = $this->objLanguage->languageText('mod_lrs_printfriendly','award');
        $pLink->extra = "onclick = 'document.getElementById(\"form_pform\").submit()'";
        $pForm = new form('pform',$this->uri(array('action'=>'printpdf'),'award'));
        $pForm->extra = " target='_blank'";
		//$pButton = new button('psub','pfriend');
		//$pButton->setToSubmit();
		$pText = new textinput('pdfcontent',htmlentities($printHTML),'hidden');
		$pForm->addToForm($pText->show());
		$printLink = $pForm->show().$pLink->show();
		$objTable->border = 0;

		//TABLE TO REFINE SUMMARY

		$refineTable = $this->newObject('htmltable','htmlelements');
    	$modeSelect = new dropdown('modeSelect');
    	//$modeSelect->addOption('min',$this->objLanguage->languageText('word_minimum'));
		$modeSelect->addOption('avg',$this->objLanguage->languageText('word_average'));
		$modeSelect->addOption('med',$this->objLanguage->languageText('word_median'));
		$modeSelect->addOption('max',$this->objLanguage->languageText('word_maximum'));
		$modeSelect->setSelected($aggregate);

		$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
		$sicSelect = new dropdown('sicSelect');
    	$sicSelect->addOption('all',$this->objLanguage->languageText('phrase_allindustries'));
		if (is_array($industries)) {
			foreach ($industries as $ind) {
				if (strlen($ind['description']) > 45) {
					$ind['description'] = substr($ind['description'],0,42).'...';
				}
				$sicSelect->addOption($ind['id'],$ind['description']);
			}
		}
		$sicSelect->setSelected((int)$sic);

		$startYear = new textinput('startYear',$year,'text',4);


    	$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
		$agreeSelect = new dropdown('agreeSelect');
    	$agreeSelect->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
		if (is_array($agreeTypes)) {
			foreach ($agreeTypes as $type) {
				$typename = $type['name'];
				if (strlen($typename) > 40) {
					$typename = substr($typename,0,37)."...";
				}
				$agreeSelect->addOption($type['id'],$typename);
			}
		}
		$agreeSelect->setSelected((int)$agreeType);


		$refineSubmit = new button("refineSubmit",$this->objLanguage->languageText('phrase_updatetable'));
		$refineSubmit->setToSubmit();

		//populate table of options to refine statistics
		$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype",'award').": ");
    	$refineTable->addCell($modeSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype",'award').": ");
    	$refineTable->addCell($agreeSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_industry",'award').": ");
    	$refineTable->addCell($sicSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("word_year").": ");
    	$refineTable->addCell($startYear->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('');
    	$refineTable->addCell($refineSubmit->show(),null,null,'right');
    	$refineTable->addCell('');
    	$refineTable->addCell('');
    	$refineTable->endRow();
    	
    	$refineForm = new form('refineForm');
    	$refineForm->addToForm($refineTable->show());

    	$refineHeading = "<br /><b><i>".$this->objLanguage->languageText('mod_lrs_refineaggregates','award')."</i></b><br />";

		$content = "<div align='right'>$printLink</div>".$objTable->show().$refineHeading.$refineForm->show();
		$tab->addTab($tablabel,$content);
		$tab->width = '835px';
		return $tab->show();
	}	

	function getIndustryWageAggregates($selectedIndustries,$socId,$aggregateType,$agreeId,$wageTypeId,$period,$start_year = null) {
		//echo "socId:$socId, aggregate:$aggregateType, agreeId:$agreeId, wtId:$wageTypeId, period:$period, startyear:$start_year <br />";
        $this->loadClass('form', 'htmlelements');
   		$this->loadClass('dropdown', 'htmlelements');
   		$this->loadClass('htmltable','htmlelements');
   		$this->loadClass('textinput','htmlelements');
   		$this->loadClass('windowpop','htmlelements');
		$currency = $this->objSysConfig->getValue('CURRENCY_ABREVIATION','award');
    	$wageType = $this->objDbPayPeriodType->getRow('id',$wageTypeId);
		$decimals = ($wageType['factor'] == 0)? 2 : 0;
   		$onClick = "javascript:updateIndustryAggregates(document.getElementById('input_industries'),
   						document.getElementById('input_socSelect2').options[document.getElementById('input_socSelect2').selectedIndex].value,
   						document.getElementById('input_modeSelect2').options[document.getElementById('input_modeSelect2').selectedIndex].value,
   						document.getElementById('input_agreeSelect2').options[document.getElementById('input_agreeSelect2').selectedIndex].value,
   						'$wageTypeId',
   						document.getElementById('input_periodSelect').options[document.getElementById('input_periodSelect').selectedIndex].value,
   						document.getElementById('input_startYear2').value)";
   		
   		//date sanity check
   		if (($start_year+$period-1) > date('Y')) {
   			$start_year = null;
   		}
   		if ($start_year == null) {
   			$start_year = date('Y')-($period-1);
   		}
   		
   		$refineForm = new form('refineSubmit');
   		$selectTable = new htmltable();
   		$selectTable->cellspacing = '10';
   		$selectTable->cellpadding = '2';

   		//create the dropdown for the list of industries
		$objIndustries = new dropdown('industries');
		$objIndustries->size = 7;
		$objIndustries->multiple = TRUE;

		//Add industries to the dropdown menu
		$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
		$allSelect = array();
        $selectedIndustriesDrop = $selectedIndustries;
		if (is_array($industries)) {
			foreach ($industries as $ind) {
				if (strlen($ind['description']) > 45) {
					$ind['description'] = substr($ind['description'],0,42).'...';
				}
				$objIndustries->addOption($ind['id'],$ind['description']);
				$allSelect[] = $ind['id'];
			}
		}
		if ($selectedIndustries[0] == 'all') {
			$selectedIndustriesDrop = $allSelect;
		}
        $objIndustries->setMultiSelected($selectedIndustriesDrop);
		//Create button for table to select industries
		$button1 = new button('ind_update',$this->objLanguage->languageText('phrase_updatetable'),$onClick);

		//add dropdown to the table
		$selectTable->startRow();
		$selectTable->addCell($this->objLanguage->languageText('phrase_selectindustries').':', '75%', 'top', 'right', '', '');
		$selectTable->addCell($objIndustries->show(), '', '', 'right' ,'' ,'');
		$selectTable->endRow();
		$selectTable->startRow();
		$selectTable->addCell('');
		$selectTable->addCell($button1->show(),'','','center','','');
		$selectTable->endRow();

		 //Create a table for the percentage comparisons
		switch ($aggregateType) {
			case 'min':
				$tableHeading1 = $this->objLanguage->languageText('mod_lrspostlogin_vtablemin', 'award');
				$tableHeading2 = $this->objLanguage->languageText('mod_lrspostlogin_ptablemin', 'award');
				break;
			case 'max':
				$tableHeading1 = $this->objLanguage->languageText('mod_lrspostlogin_vtablemax', 'award');
				$tableHeading2 = $this->objLanguage->languageText('mod_lrspostlogin_ptablemax', 'award');
				break;
			case 'med':
				$tableHeading1 = $this->objLanguage->languageText('mod_lrspostlogin_vtablemed', 'award');
				$tableHeading2 = $this->objLanguage->languageText('mod_lrspostlogin_ptablemed', 'award');
				break;
			case 'ave':
				$tableHeading1 = $this->objLanguage->languageText('mod_lrspostlogin_vtableave', 'award');
				$tableHeading2 = $this->objLanguage->languageText('mod_lrspostlogin_ptableave', 'award');
				break;
			case 'weigh':
				$tableHeading1 = $this->objLanguage->languageText('mod_lrspostlogin_vtableweigh', 'award');
				$tableHeading2 = $this->objLanguage->languageText('mod_lrspostlogin_ptableweigh', 'award');
				break;
		}
    	$percentageTable = new htmltable();
    	$percentageTable->cellspacing = '2';
    	$percentageTable->cellpadding = '2';
    	$percentageTable->id = 'percentageTable';
		$percentageTable->startHeaderRow();
		$percentageTable->addHeaderCell($tableHeading2, '', '', '', '', 'colspan="21"');
		$percentageTable->endHeaderRow();
   		$thisYear = date('Y');

 		 //Create a table for the value comparisons
 		$valueTable = new htmltable();
    	$valueTable->cellspacing = '2';
    	$valueTable->cellpadding = '2';
    	$valueTable->id = 'valueTable';
		$valueTable->startHeaderRow();
		$valueTable->addHeaderCell($tableHeading1, '', '', '', '', 'colspan="21"');
		$valueTable->endHeaderRow();

   		//Add headings
		$percentageTable->startRow();
		$percentageTable->addCell($this->objLanguage->languageText('mod_lrswages_industry','award'),'','','','heading');
		for ($j = 0; $j < $period; $j++){
			$year = $start_year+$j;
			$line = $this->objLanguage->languageText('phrase_avgincrease')." $year";
			$percentageTable->addCell($line,'','','center','heading','colspan="2"');
		}
		$percentageTable->endRow();

		$valueTable->startRow();
		$valueTable->addCell($this->objLanguage->languageText('mod_lrswages_industry','award'),'','','','heading');
		for ($j = 0; $j < $period; $j++){
			$year = $start_year+$j;
			$line = "{$wageType['name']} ".$this->objLanguage->languageText('word_wage')." $year";
			$valueTable->addCell($line,'','','center','heading','colspan="2"');
		}
		$valueTable->endRow();


    	$class = 'even';
    	$graphI = 0;
  		foreach($selectedIndustries as $selectedIndustry){

  			$industry = $this->objDbSicMajorDivs->getRow('id',$selectedIndustry);
  			if ($selectedIndustry == 'all') {
  				$industryName = $this->objLanguage->languageText('mod_lrswages_aggregation','award');
  			} else {
                if (strlen($industry['description']) > 45) {
                    $industryName = substr($industry['description'],0,42).'...';
                }else{
                	$industryName = $industry['description'];
                }
            }
  			$class = ($class=='even')? 'odd' : 'even';
  			$percentageTable->startRow($class);
  			$percentageTable->addCell($industryName,'30%');
  			$valueTable->startRow($class);
  			$valueTable->addCell($industryName,'30%');

  			for ($i=$start_year; $i<=$start_year+($period-1); $i++) {
  				switch ($aggregateType) {
  					case 'max':
  						$avewage = $this->objDbWages->getAverageMaxWages($selectedIndustry,$socId,null,'all',$wageTypeId,$agreeId,$i,'all');
  						break;
					
  					case 'med':
  						$avewage = $this->objDbWages->getMedianWages($selectedIndustry,$socId,null,'all',$wageTypeId,$agreeId,$i,'all');
  						break;
					
  					case 'min':
						$avewage = $this->objDbWages->getAverageMinWages($selectedIndustry,$socId,null,'all',$wageTypeId,$agreeId,$i,'all');
  						break;
					
  					case 'weigh':
						$avewage = $this->objDbWages->getWeightedAverageWages($selectedIndustry,$socId,null,'all',$wageTypeId,$agreeId,$i,'all');
						
					case 'ave':
						$avewage = $this->objDbWages->getAverageWages($selectedIndustry,$socId,null,'all',$wageTypeId,$agreeId,$i,'all');
					default:
  						
  				}
  				
				if ($avewage['sample'] > 4) {
					$samplePopUp = $this->newObject('windowpop','htmlelements');
					$samplePopUp->set('location', $this->uri(array('action'=>'samplelist','year'=>$i,'wageTypeId'=>$wageTypeId,'sic'=>$selectedIndustry,'soc'=>$socId,'agreeTypeId'=>$agreeId,'mode'=>$aggregateType),'award'));
					$samplePopUp->set('window_name', 'sample');
					$samplePopUp->set('linktext', $avewage['sample']);
		            $samplePopUp->set('width', '1024');
					$samplePopUp->set('height', '600');
					$samplePopUp->set('left', '0');
					$samplePopUp->set('top', '0');
					$samplePopUp->set('scrollbars', TRUE);
					$samplePopUp->putJs();

					$sample = $samplePopUp->show();
  				
					$percentage = round($avewage['increaseAve'],2)."%";
					$value = number_format($avewage['average'],$decimals);
					
					$ValueGraphData[$industryName][$i] = (string)round($avewage['average'],$decimals);
					$PercentageGraphData[$industryName][$i] = (string)round($avewage['increaseAve'],1);
					
				} else {
					$percentage = $value = '--';
					$sample = $avewage['sample'];
					$ValueGraphData[$industryName][$i] = '';
					$PercentageGraphData[$industryName][$i] = '';
				
				}
				$percentageTable->addCell($percentage,null,null,'center');
  				$percentageTable->addCell($sample,null,null,'center','','style="font-size: 7pt;"');
  				$valueTable->addCell($value,null,null,'center');
  				$valueTable->addCell($sample,null,null,'center','','style="font-size: 7pt;"');
  				
  				
  				
  				
  			}
  			$valueTable->endRow();
  			$percentageTable->endRow();
  			$graphI++;

  		}//end of foreach

		//popup for percentage graph
		$pData = serialize($PercentageGraphData);
		$gTitle = $this->objLanguage->languageText('mod_lrs_percentagegraphtitle', 'award');
		$percentageGraph = $this->newObject('windowpop','htmlelements');
	    $percentagePop = $this->uri(array('action'=>'xmlgraph', 'title'=>$gTitle, 'data'=>$pData, 'period'=>$period, 'start'=>$start_year), 'award');
		$percentageGraph->set('location', $percentagePop);
		$percentageGraph->set('window_name', 'percentage');
		$percentageGraph->set('linktext',$this->objLanguage->languageText('mod_lrspostlogin_percentagegraph', 'award'));
        $percentageGraph->set('width','800');
        $percentageGraph->set('height','600');
        $percentageGraph->set('left','0');
        $percentageGraph->set('top','0');
        $percentageGraph->set('scrollbars','no');
	    $percentageWindow = $percentageGraph->show();
	    $percentageTable->addCell($percentageWindow, '', '', 'right' ,'' ,'colspan="21"');

	    //popup for value graph
		$gTitle = $this->objLanguage->languageText('mod_lrs_valuegraphtitle','award');
		$vData = serialize($ValueGraphData);
		$valueGraph= $this->newObject('windowpop','htmlelements');
	    $valuePop = $this->uri(array('action'=>'xmlgraph', 'title'=>$gTitle, 'data'=>$vData, 'period'=>$period, 'start'=>$start_year),'award');
		$valueGraph->set('location', $valuePop);
		$valueGraph->set('window_name', 'value');
		$valueGraph->set('linktext',$this->objLanguage->languageText('mod_lrspostlogin_valuegraph', 'award'));
        $valueGraph->set('width','800');
        $valueGraph->set('height','600');
        $valueGraph->set('left','1');
        $valueGraph->set('top','1');
        $valueGraph->set('scrollbars','no');
	    $valueWindow = $valueGraph->show();
	    $valueTable->addCell($valueWindow, '', '', 'right' ,'' ,'colspan="21"');

    	$refineTable = $this->newObject('htmltable','htmlelements');

		// Text field for start_date
		if ($this->objUser->isAdmin()) {
			$startYearInputType = 'text';
			$startYearLabel = $this->objLanguage->languageText("mod_lrspostlogin_startyear", 'award').": ";
			$defaultYear = (empty($start_year))? date('Y')-($period-1) : $start_year;
		} else {
			$startYearInputType = 'hidden';
			$startYearLabel = '';
			$defaultYear = date('Y');
		}
		$start_year = new textinput('startYear2',$defaultYear,$startYearInputType,4);


 		$button2 = new button('ind_update',$this->objLanguage->languageText('phrase_updatetable'),$onClick);

		//dropdown to select the number of years
		$periodSelect = new dropdown('periodSelect');
		$periodSelect->addOption(5);
		$periodSelect->addOption(10);
		$periodSelect->setSelected($period);

		//dropdown to select the mode
		$modeSelect = new dropdown('modeSelect2');
    	if ($this->objUser->isAdmin()) {
			$modeSelect->addOption('min',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'));
		}
		$modeSelect->addOption('ave',$this->objLanguage->languageText('word_average'));
		$modeSelect->addOption('weigh',$this->objLanguage->languageText('mod_lrspostlogin_weightedavgs', 'award'));
		$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
		if ($this->objUser->isAdmin()) {
			$modeSelect->addOption('max',$this->objLanguage->languageText('mod_lrspostlogin_maxavgs', 'award'));
		}
		$modeSelect->setSelected($aggregateType);
		//dropdown for occupation
		$socMajGrps = $this->objDbSocMajorGroup->getAll("ORDER BY id DESC");
		$socSelect = new dropdown('socSelect2');
    	if (is_array($socMajGrps)) {
			foreach ($socMajGrps as $grp) {
				$socSelect->addOption($grp['id'],$grp['description']);
			}
		}
		$socSelect->setSelected($socId);

		//dropdown for agreement type
		$agreeTypes = $this->objDbAgreeType->getAll("ORDER BY name ASC");
		$agreeSelect = new dropdown('agreeSelect2');
    	$agreeSelect->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
		$agreeSelect->addOption('cb',$this->objLanguage->languageText('phrase_collectivebargaining'));
		if (is_array($agreeTypes)) {
			foreach ($agreeTypes as $type) {
				$typename = $type['name'];
				if (strlen($typename) > 40) {
					$typename = substr($typename,0,37)."...";
				}
				$agreeSelect->addOption($type['id'],$typename);
			}
		}
		$agreeSelect->setSelected($agreeId);


		//populate table of options to refine statistics
		$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype",'award').": ",'','','right');
    	$refineTable->addCell($agreeSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrswages_aggregatetype",'award').": " ,'','','right');
    	$refineTable->addCell($modeSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrswages_period",'award').": ",'','','right');
    	$refineTable->addCell($periodSelect->show().'<br>');
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_occupation",'award').": ",'','','right');
    	$refineTable->addCell($socSelect->show());
    	$refineTable->addCell('&nbsp;');
    	$refineTable->addCell('&nbsp;');
    	$refineTable->addCell($startYearLabel,'','','right');
    	$refineTable->addCell($start_year->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('');
    	$refineTable->addCell($button2->show(),null,null,'center','','colspan="4"');
    	$refineTable->addCell('');
    	$refineTable->addCell('');
     	$refineTable->endRow();
        
		return $selectTable->show()."<div id='ind_tables' style='min-height:100px'>".$valueTable->show().$percentageTable->show()."</div>".$refineTable->show();
	}

	function getSocWageAggregates($aggregate,$agreeTypeId,$year,$socText=null, $industry='all', $subSic='all') {
		//echo "$aggregate,$agreeTypeId,$year,$socText=null, $industry='all', $subSic='all'";
		if ($year > date('Y')) {
			$year = date('Y');
		}
		$payPeriod = $this->objDbPayPeriodType->getDefaultPPType();
		$decimals = ($payPeriod['factor'] == 0)? 2 : 0;
		$heading = "<p><b><i>".str_replace('[PAYPERIOD]',$payPeriod['name'],$this->objLanguage->languageText('mod_lrs_socwages','award'))."</i></b></p>";
		$wageTable = $this->newObject('htmltable','htmlelements');
    	$wageTable->cellspacing = '2';
    	$wageTable->cellpadding = '2';
    	$wageTable->id = 'wageTable';
    	$currency = $this->objSysConfig->getValue('CURRENCY_ABREVIATION','award');
    	$wageTable->startHeaderRow();
    	$wageTable->addHeaderCell($this->objLanguage->languageText('word_occupation'),null,null,'left');
    	$wageTable->addHeaderCell($this->objLanguage->languageText('word_sample'),null,null,'center');
		$wageTable->addHeaderCell("{$payPeriod['name']} ".$this->objLanguage->languageText('word_average')." ($currency)",null,null,'center');
		$wageTable->addHeaderCell($this->objLanguage->languageText('phrase_increasepercentage'),null,null,'center');
		$wageTable->addHeaderCell($this->objLanguage->languageText('phrase_actualincrease')." CPI",null,null,'center');
		$wageTable->endHeaderRow();
		switch ($aggregate) {
		    case 'min':
		        $socWages = $this->objDbSocMajorGroup->getSocMinimum($agreeTypeId, $year, $socText, $industry, $subSic);
		        break;
		
		    case 'med':
		        $socWages = $this->objDbSocMajorGroup->getSocMedian($agreeTypeId, $year, $socText, $industry, $subSic);
			    break;
	
		    case 'max':
				$socWages = $this->objDbSocMajorGroup->getSocMaximum($agreeTypeId, $year, $socText, $industry, $subSic);
				break;
			
			case 'weigh':
				$socWages = $this->objDbSocMajorGroup->getSocWeightedAverage($agreeTypeId, $year, $socText, $industry, $subSic);
				break;
			
			case 'ave':
			default:
		        $socWages = $this->objDbSocMajorGroup->getSocAverage($agreeTypeId, $year, $socText, $industry, $subSic);
			    break;
		}
		
		$class = 'odd';
		
		if (empty($socWages)) { 
		  $wageTable->startRow();
		  $wageTable->addCell("<span class='noRecordsMessage'>".$this->objLanguage->languageText('phrase_norecords')."</span>",null,null,'center',null,"colspan = '5'");
		  $wageTable->endRow();
		} else { 
		foreach ($socWages as $socId => $wage) {
			
			$amount 	= ($wage['amount'] == 0)? '--' : number_format($wage['amount'], $decimals);
			$increase 	= ($wage['increase'] == 0)? '--' : number_format($wage['increase'], 2);
			$real 		= ($wage['real'] == 0)? '--' : number_format($wage['real'], 2);
			 
			$wageTable->startRow($class);
			$wageTable->addCell($this->objDbSocMajorGroup->getOccupationFromId($socId));
			$wageTable->addCell($wage['sample'],null,null,'center');
			$wageTable->addCell($amount,null,null,'center');
			$wageTable->addCell($increase,null,null,'center');
			$wageTable->addCell($real,null,null,'center');
			//$wageTable->addCell($wage['workers']);
			$wageTable->endRow();
			$class = ($class == 'odd')? 'even' : 'odd';
		 }
		}
		//print friendly link
		$this->loadClass('form','htmlelements');
		$this->loadClass('button','htmlelements');
		$this->loadClass('dropdown','htmlelements');
		$this->loadClass('textinput','htmlelements');
		
		$wageTable->border = 1;
		$pLink = $this->newObject('link','htmlelements');
		$printHTML = "<p><b><i>$heading</i></b></p>".$wageTable->show();
        $printHTML = preg_replace('/thead/i','tr',$printHTML);
        $pLink->link('javascript:;');
        $pLink->link = $this->objLanguage->languageText('mod_lrs_printfriendly','award');
        $pLink->extra = "onclick = 'document.getElementById(\"form_pform\").submit()'";
        $pForm = new form('pform',$this->uri(array('action'=>'printpdf'),'award'));
        $pForm->extra = " target='_blank'";
		$pText = new textinput('pdfcontent',htmlentities($printHTML),'hidden');
		$pForm->addToForm($pText->show());
		$printLink = $pForm->show().$pLink->show();
		$wageTable->border = 0;
		
		$wageTable->startRow();
		$wageTable->endRow();
		

		
		$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
		$agreeSelect = new dropdown('agreeSelect3');
    	$agreeSelect->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
		$agreeSelect->addOption('cb',$this->objLanguage->languageText('phrase_collectivebargaining'));
		if (is_array($agreeTypes)) {
			foreach ($agreeTypes as $type) {
				$typename = $type['name'];
				if (strlen($typename) > 40) {
					$typename = substr($typename,0,37)."...";
				}
				$agreeSelect->addOption($type['id'],$typename);
			}
		}
		$agreeSelect->setSelected($agreeTypeId);
		$modeSelect = new dropdown('modeSelect3');
    	$modeSelect->addOption('min',$this->objLanguage->languageText('word_minimum'));
		$modeSelect->addOption('ave',$this->objLanguage->languageText('word_average'));
		$modeSelect->addOption('weigh',$this->objLanguage->languageText('mod_lrspostlogin_weightedavgs', 'award'));
		$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
		$modeSelect->addOption('max',$this->objLanguage->languageText('word_maximum'));
		$modeSelect->setSelected($aggregate);
		
		$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
		$sicSelect = new dropdown("sicid3");
    	$sicSelect->addOption('all',$this->objLanguage->languageText('phrase_allindustries'));
		if (is_array($industries)) {
			foreach ($industries as $ind) {
				if (strlen($ind['description']) > 45) {
					$ind['description'] = substr($ind['description'],0,42).'...';
				}
				$sicSelect->addOption($ind['id'],$ind['description']);
			}
		}
		$sicSelect->setSelected((int)$industry);

		$sicDivSelect = new dropdown("subSic3");
    	$sicDivSelect->addOption('all',$this->objLanguage->languageText('phrase_alldivisions'));
		if ($industry != NULL && $industry != 'all') {
			$divs = $this->objDbSicDivs->getAll("WHERE major_divid = '$industry'");
			if (is_array($divs)) {
				foreach ($divs as $div) {
					if (strlen($div['description']) > 45) {
						$div['description'] = substr($div['description'],0,42).'...';
					}
					$sicDivSelect->addOption($div['id'],$div['description']);
				}
			}
		} else {
			$sicDivSelect->extra = "disabled='disabled'";
		}
		$sicDivSelect->setSelected($subSic);
		
		$socInput = new textinput('socInput',$socText);
		
		$refineTable = $this->newObject('htmltable','htmlelements');
		$refineTable->startRow();
		$refineTable->addCell($this->objLanguage->languageText("word_occupation").": ");
		$refineTable->addCell($socInput->show());
		$refineTable->addCell($this->objLanguage->languageText("mod_lrs_industry", "award")." ");
    	$refineTable->addCell($sicSelect->show());
		$refineTable->endRow();
		$refineTable->startRow();
		$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype", 'award').": ");
		$refineTable->addCell($modeSelect->show());
		$refineTable->addCell($this->objLanguage->languageText("mod_award_industrydiv", "award").": ");
    	$refineTable->addCell($sicDivSelect->show());
		$refineTable->endRow();
		$refineTable->startRow();
		$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype", 'award').": ");
		$refineTable->addCell($agreeSelect->show());
		$this->loadClass('textinput','htmlelements');
		$yearSelect = new textinput('year',$year,'text',4);
		$refineTable->addCell($this->objLanguage->languageText('word_year').": ");
		$refineTable->addCell($yearSelect->show());
		$refineTable->endRow();
		
		$onclick = "javascript:updateOccupationWages(document.getElementById('input_modeSelect3').value,
                                                     document.getElementById('input_agreeSelect3').value,
                                                     document.getElementById('input_year').value,
                                                     document.getElementById('input_socInput').value,
													 document.getElementById('input_sicid3').value,
                                                     document.getElementById('input_subSic3').value
                                                     )";
		
		$sub = new button('sub',$this->objLanguage->languageText('word_update'),$onclick);
        $sub->setId("sub_button");
		$refineTable->startRow();
		$refineTable->addCell(" ");
		$refineTable->addCell($sub->show(),null,null,'right');
		$refineTable->endRow(); 
		
		
		$refineForm = new form('refineForm');
		$refineForm->addToForm($refineTable->show());
		
		return "<p><b><i>".$this->objLanguage->languageText('mod_lrs_refineaggregates', 'award')."</i></b></p>".$refineForm->show().$heading."<div align='right'>$printLink</div><div id='socWageTable' style='min-height:100px'>".$wageTable->show()."</div>";
    	
	}

    function getConditions($benefitTypeId,$year,$agreeTypeId = 'all', $sicId = 'all',$aggregate = 'avg', $subSic = 'all') {
		$width1 = NULL;//'50%';
        $width2 = NULL;//'20%';
        $width3 = NULL;//'30%';
        
		if ($sicId == 'all') {
			$subSic = 'all';
		}
		
		$benefit = $this->objBenefitType->getRow('id',$benefitTypeId);
        $objHeading = $this->getObject('htmlheading','htmlelements');
        $objHeading->type=4;
        $objHeading->str = $benefit['name'];
		$objTable = $this->newObject('htmltable','htmlelements');
        $objTable->cellspacing = $objTable->cellpadding = 2;
        $totalSample = '';
        if ($this->objUser->isAdmin()) { 
        	$sql = "SELECT COUNT(a.id) AS sample
        		FROM tbl_award_agree AS a ";
        	if ($sicId != 'all') {
        		$sql .= ", tbl_award_unit_sic AS s ";
        	}
        	$sql .= "WHERE YEAR(implementation) = '$year'";
        	if ($agreeTypeId != 'all') {
        		$sql .= ($agreeTypeId == 'cb')? " AND (typeid = '2' || typeid = '3' || typeid = '4')" : " AND typeid = '$agreeTypeId'";
        	}
        	if ($sicId != 'all') {
        		$sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId' "; 
        	}
        	$rs = $this->objBenefit->getArray($sql);
        	$result = current($rs);
        	$totalSample = " ({$result['sample']})";
        }
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->objLanguage->languageText('phrase_condition'),null,null,'left');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_sample').$totalSample,null,null,'center');
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_unit', 'award'),null,null,'center');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_measurement'),null,null,'center');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_benchmark'),null,null,'center');
        $objTable->endHeaderRow();
        
        $conditions = $this->objBenefitName->getAll("WHERE typeid = '$benefitTypeId'");
        $class = 'even';
        foreach ($conditions as $cond) {
        	$class = ($class == 'even')? 'odd' : 'even';
        	if ($cond['aggregatetype'] == 'value') {
			switch ($aggregate) { 
        	    case 'med':
        	       $sql = "SELECT b.value +0 AS average, b.agreeid AS id
        	               FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
        	        if ($sicId != 'all') {
        	            $sql .= ", tbl_award_unit_sic AS s ";
        	        }
        	        $sql .= "WHERE b.nameid = '{$cond['id']}'
								AND b.agreeid = a.id
								AND YEAR(a.implementation) = '$year'";
        	        if ($agreeTypeId != 'all') {
        	            $sql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
        	        }
        	        if ($sicId != 'all') {
        	            $sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
        	        }
					if ($subSic != 'all') {
        	            $sql .= " AND s.divid = '$subSic'";
        	        }
					
        	        $sql .= " ORDER BY average";
        	        $data = $this->objBenefit->getArray($sql);
        	        $n = $average['sample'] = count($data);
					if ($n < 4) {
						$average['average'] = '--';
					} else {
						$average['average'] = ($n % 2 == 0)?
							round($data[(($n+1)/2)-1]['average'], 2) :
							round(($data[($n/2)-1]['average']+$data[$n/2]['average'])/2, 2);
					}
        	        break;
				
        	    case 'max':
        	       $sql = "SELECT MAX(b.value) AS average, COUNT(b.agreeid) AS sample
        	               FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
        	        if ($sicId != 'all') {
        	            $sql .= ", tbl_award_unit_sic AS s ";
        	        }
        	        $sql .= "WHERE b.nameid = '{$cond['id']}'
								AND b.agreeid = a.id
								AND YEAR(a.implementation) = '$year'";
        	        if ($agreeTypeId != 'all') {
        	            $sql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
        	        }
        	        if ($sicId != 'all') {
        	            $sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
        	        }
					if ($subSic != 'all') {
        	            $sql .= " AND s.divid = '$subSic'";
        	        }
					
        	        $sql .= " ORDER BY average DESC";
        	        $data = $this->objBenefit->getArray($sql);
					$average = current($data);
        	        $average['average'] = ($average['sample'] < 4)? '--' : round($average['average'], 2);
        	        break;
				
        	    default:
        	        $sql = "SELECT AVG(b.value) AS average, COUNT(b.agreeid) AS sample
        	               FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
        	        if ($sicId != 'all') {
        	            $sql .= ", tbl_award_unit_sic AS s ";
        	        }
        	        $sql .="WHERE b.nameid = '{$cond['id']}'
        				  AND b.agreeid = a.id
        				  AND YEAR(a.implementation) = '$year'";
        	        if ($agreeTypeId != 'all') {
        	            $sql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
        	        }
        	        if ($sicId != 'all') {
        	            $sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
        	        }
        	        if ($subSic != 'all') {
        	            $sql .= " AND s.divid = '$subSic'";
        	        }
        	        $data = $this->objBenefit->getArray($sql);
        	        $average = current($data);
        	        $average['average'] = ($average['sample'] < 4)? '--' : round($average['average'],2);
        	        break;
        	}
			} else {
				/*$sql = "SELECT COUNT(b.id) AS num
        	               FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
        	    if ($sicId != 'all') {
        	        $sql .= ", tbl_award_unit_sic AS s ";
        	    }
        	    $sql .="WHERE b.nameid = '{$cond['id']}'
        				AND b.agreeid = a.id AND b.value = 'yes'
        				AND YEAR(a.implementation) = '$year'";
        	    if ($agreeTypeId != 'all') {
        	        $sql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
        	    }
        	    if ($sicId != 'all') {
        	        $sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
        	    }
        	    if ($subSic != 'all') {
        	        $sql .= " AND s.divid = '$subSic'";
        	    }
        	    $data = $this->objBenefit->getArray($sql);
				$yes = $data[0]['num'];
				$sql = str_replace("'yes'", "'no'", $sql);
        	    $data = $this->objBenefit->getArray($sql);
				$no = $data[0]['num'];
				$average['sample'] = $yes+$no;
				$average['average'] = ($average['sample'] < 4)? '--' : round($yes/($average['sample']),2);*/
				$tSql = "SELECT COUNT(a.id) AS sample
        				 FROM tbl_award_agree AS a ";
    			if ($sicId != 'all') {
    				$tSql .= ", tbl_award_unit_sic AS s ";
    			}
    			$tSql .="WHERE YEAR(a.implementation) = '$year'";
    			if ($agreeTypeId != 'all') {
    				$tSql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
    			}
    			if ($sicId != 'all') {
    				$tSql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
    			}			
    			$sql = "SELECT COUNT(b.agreeid) AS sample
        			FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
    			if ($sicId != 'all') {
    				$sql .= ", tbl_award_unit_sic AS s ";
    			}
    			$sql .="WHERE b.nameid = '{$cond['id']}'
        				AND b.agreeid = a.id
        				AND YEAR(a.implementation) = '$year'";
    			if ($agreeTypeId != 'all') {
    				$sql .= ($agreeTypeId == 'cb')? " AND (a.atypeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
    			}
    			if ($sicId != 'all') {
    				$sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
    			}
    			$data = current($this->objBenefit->getArray($sql));
    			$total = current($this->objBenefit->getArray($tSql));
				$average['sample'] = $total['sample'];
				$average['average'] = ($total['sample'] < 4)? '--' : round(($data['sample']/$total['sample'])*100, 2)."%";
			}
			
			$objTable->startRow($class);
        	$objTable->addCell($cond['name'],$width1,null,null);
        	$objTable->addCell($average['sample'],$width2,null,'center');
        	$objTable->addCell($cond['measure'],$width2,null,'center');
        	$objTable->addCell($average['average'],$width3,null,'center');
        	$objTable->addCell($cond['benchmark'],$width3,null,'center');
        	$objTable->endRow();
        }
        $content =  $objHeading->show().$objTable->show().$this->getConditionsForm($benefitTypeId,$year,$agreeTypeId,$sicId,$aggregate,$subSic);
        return $content;
    }
    
    /**
     * Return the HTML for the form to refine the conditions tab
     *
     * @param integer $tab the number of the tab we're dealing with
     * @return string the HTML codde of the form
     */
    function getConditionsForm($benefitTypeId, $year, $agreeId=NULL, $industry = NULL, $mode = 'avg', $subSic = 'all') {
        $this->loadClass('textinput','htmlelements');
        
        $tabNo = new textinput('tab_number',$benefitTypeId,'hidden');
        $refineTable = $this->newObject('htmltable','htmlelements');
    	$modeSelect = new dropdown("aggregate");
    	$modeSelect->addOption('avg',$this->objLanguage->languageText('word_average'));
		$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
		$modeSelect->addOption('max',$this->objLanguage->languageText('word_maximum'));
		$modeSelect->setSelected($mode);

		$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
		$sicSelect = new dropdown("sicid");
    	$sicSelect->addOption('all',$this->objLanguage->languageText('phrase_allindustries'));
		if (is_array($industries)) {
			foreach ($industries as $ind) {
				if (strlen($ind['description']) > 45) {
					$ind['description'] = substr($ind['description'],0,42).'...';
				}
				$sicSelect->addOption($ind['id'],$ind['description']);
			}
		}
		$sicSelect->setSelected((int)$industry);

		$sicDivSelect = new dropdown("subSic");
    	$sicDivSelect->addOption('all',$this->objLanguage->languageText('phrase_alldivisions'));
		if ($industry != NULL && $industry != 'all') {
			$divs = $this->objDbSicDivs->getAll("WHERE major_divid = '$industry'");
			if (is_array($divs)) {
				foreach ($divs as $div) {
					if (strlen($div['description']) > 45) {
						$div['description'] = substr($div['description'],0,42).'...';
					}
					$sicDivSelect->addOption($div['id'],$div['description']);
				}
			}
		} else {
			$sicDivSelect->extra = "disabled='disabled'";
		}
		$sicDivSelect->setSelected($subSic);
		
		$startYear = new textinput("year",$year,'text',4);

    	$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
		$agreeSelect = new dropdown("agreetypeid");
    	$agreeSelect->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
		$agreeSelect->addOption('cb',$this->objLanguage->languageText('phrase_collectivebargaining'));
		if (is_array($agreeTypes)) {
			foreach ($agreeTypes as $type) {
				$typename = $type['name'];
				if (strlen($typename) > 40) {
					$typename = substr($typename,0,37)."...";
				}
				$agreeSelect->addOption($type['id'],$typename);
			}
		}
		$agreeSelect->setSelected($agreeId);


		$refineSubmit = new button("refineSubmit",$this->objLanguage->languageText('phrase_updatetable'));
        $refineSubmit->setToSubmit();
		//populate table of options to refine statistics
		$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype", "award").": ");
    	$refineTable->addCell($modeSelect->show().$tabNo->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_industry", "award")." ");
    	$refineTable->addCell($sicSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype", "award").": ");
    	$refineTable->addCell($agreeSelect->show());
    	$refineTable->addCell($this->objLanguage->languageText("mod_award_industrydiv", "award").": ");
    	$refineTable->addCell($sicDivSelect->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell($this->objLanguage->languageText("word_year").": ");
    	$refineTable->addCell($startYear->show());
    	$refineTable->endRow();
    	$refineTable->startRow();
    	$refineTable->addCell('');
    	$refineTable->addCell($refineSubmit->show(),null,null,'right');
    	$refineTable->addCell('');
    	$refineTable->addCell('');
    	$refineTable->endRow();

        $objForm = $this->newObject('form','htmlelements');
        $objForm->action = $this->uri(array('action'=>'conditions','selected'=>'init_04'),'award');
        $objForm->id = $benefitTypeId;
        $objForm->addToForm($refineTable->show());

    	$form = "<br / ><b>".$this->objLanguage->languageText('mod_lrs_refineaggregates', "award")."</b>".$objForm->show();
    	return $form;
    }
    
    function getAgreeConditions($agreeId, $selectedTab = 1) {
        $this->loadClass('textinput','htmlelements');
		$agree = $this->objDbAgree->getRow('id',$agreeId);
		$selected = new textinput('selectedTab',$selectedTab,'hidden');
		$benefitTypes = $this->objBenefitType->getAll('ORDER BY id');
        $tabs = $this->newObject('tabber','htmlelements');
        $tabNo = 1;
        $default = false;
        foreach ($benefitTypes as $benefit) { 
        	$objTable = $this->newObject('htmltable','htmlelements');
        	$objTable->cellspacing = $objTable->cellpadding = 2;
        	$objTable->addHeader(array(	$this->objLanguage->languageText('phrase_condition'),
										$this->objLanguage->languageText('mod_lrs_decent_work_unit', 'award'),
										$this->objLanguage->languageText('word_measurement'),
										$this->objLanguage->languageText('word_benchmark')
										),null,'align=left');
            $conditions = $this->objBenefitName->getAll("WHERE typeid = '{$benefit['id']}'");
        	$class = 'even';
        	foreach ($conditions as $cond) {
        		$class = ($class == 'even')? 'odd' : 'even';
        		$data = $this->objBenefit->getAll("WHERE nameid = '{$cond['id']}' AND agreeid = '$agreeId'");
        		if (empty($data)) {
        			$value = '--';
        		} else {
        			$single = current($data);
        			$value = round($single['value'],1);
        		}
        		$objTable->startRow($class);
        		$objTable->addCell($cond['name']);
        		$objTable->addCell($cond['measure']);
        		$objTable->addCell($value);
        		$objTable->addCell($cond['benchmark']);
        		$objTable->endRow();
        	}
        	if ($selectedTab == $tabNo) { 
        		$default = true;
        	} else {
        		$default = false;
        	}
        	$tabs->addTab(array('name'=>$benefit['name'],'content'=>$objTable->show(), null, 'default'=>$default));
        	$tabNo++;
        }
        $template = $tabs->show().$selected->show();
        return $template;
	}
	
	function getAgreeConditionsAdmin($agreeId, $selectedTab = 1) {
        $this->loadClass('textinput','htmlelements');
		$agree = $this->objDbAgree->getRow('id',$agreeId);
		$oldAgreeId = $this->objDbAgree->getPreviousAgreementId($agree['unitid'], $agree['implementation']);
		$selected = new textinput('selectedTab',$selectedTab,'hidden');
		$benefitTypes = $this->objBenefitType->getAll('ORDER BY id');
        $tabs = $this->newObject('tabber','htmlelements');
        $tabNo = 1;
        $default = false;
        foreach ($benefitTypes as $benefit) { 
        	$objTable = $this->newObject('htmltable','htmlelements');
        	$objTable->cellspacing = $objTable->cellpadding = 2;
        	$objTable->addHeader(array(	$this->objLanguage->languageText('phrase_condition'),
										$this->objLanguage->languageText('mod_lrs_decent_work_unit', 'award'),
										$this->objLanguage->languageText('word_measurement')." (".$this->objLanguage->languageText('word_previous').")",
										$this->objLanguage->languageText('word_benchmark')
										),null,'align=left');
            $conditions = $this->objBenefitName->getAll("WHERE typeid = '{$benefit['id']}'");
        	$class = 'even';
        	foreach ($conditions as $cond) {
        		$class = ($class == 'even')? 'odd' : 'even';
        		$data = $this->objBenefit->getAll("WHERE nameid = '{$cond['id']}' AND agreeid = '$agreeId'");
        		if (empty($data)) {
        			$value = '';
        		} else {
        			$single = current($data);
        			$value = round($single['value'],1);
        		}
				if ($oldAgreeId) {
					$oldData = $this->objBenefit->getAll("WHERE nameid = '{$cond['id']}' AND agreeid = '$oldAgreeId'");
					if (empty($oldData)) {
						$oldValue = '--';
					} else {
						$oldSingle = current($oldData);
						$oldValue = round($oldSingle['value'],1);
					}
				} else {
					$oldValue = '--';
				}
				$textInput = new textinput($cond['id'], $value, 'text', 10);
				$textInput->setCss('vibe');
        		$objTable->startRow($class);
        		$objTable->addCell($cond['name']);
        		$objTable->addCell($cond['measure']);
        		$objTable->addCell($textInput->show()." (<span id='old_input_{$cond['id']}'>$oldValue</span>)");
        		$objTable->addCell($cond['benchmark']);
        		$objTable->endRow();
        	}
        	if ($selectedTab == $tabNo) { 
        		$default = true;
        	} else {
        		$default = false;
        	}
        	$tabs->addTab(array('name'=>$benefit['name'],'content'=>$objTable->show(), null, 'default'=>$default));
        	$tabNo++;
        }
		$loadLink = "<a href='javascript:populateConditions();'>".$this->objLanguage->languageText('mod_award_loadconditions', 'award')."</a>";
        $template = $loadLink.$tabs->show().$selected->show();
		$idInput = new textinput('agreeId', $agreeId, 'hidden');
		$submit = new button('submit', $this->objLanguage->languageText('mod_award_savechanges','award'));
		$submit->setToSubmit();
		$backuri = $this->uri(array('action'=>'agreementoverview', 'id'=>$agreeId, 'unitId'=>$agree['unitid'], 'selected'=>'init_10'));
		$back = new button('back', $this->objLanguage->languageText('word_back'), "document.location='$backuri'");
		$objForm = new form('conditions_form', $this->uri(array('action'=>'save_conditions')));
		$objForm->addRule('init_7', $this->objLanguage->languageText('mod_award_hoursrequired', 'award'), 'required');
		$objForm->addToForm($idInput->show().$template.$submit->show()." ".$back->show());
        return $objForm->show();
	}
    
    function getGenderAverage($sicId,$aggregate,$agreeTypeId,$year,$id) {
	    $benefit = $this->objBenefitName->getRow('id',$id);
		switch ($aggregate) {
			case 'min':
				$mysqlFunction = 'MIN';
				break;
			case 'max':
				$mysqlFunction = 'MAX';
				break;
			case 'ave':
			default:
				$mysqlFunction = 'AVG';
				break;
			
		}
        switch ($benefit['aggregatetype']) {
	        case 'value':
				if ($aggregate == 'med') {
					
        	       $sql = "SELECT b.value +0 AS average, b.agreeid AS id
        	               FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
        	        if ($sicId != 'all') {
        	            $sql .= ", tbl_award_unit_sic AS s ";
        	        }
        	        $sql .= "WHERE b.nameid = '$id'
								AND b.agreeid = a.id
								AND YEAR(a.implementation) = '$year'";
        	        if ($agreeTypeId != 'all') {
        	            $sql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
        	        }
        	        if ($sicId != 'all') {
        	            $sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
        	        }
					
        	        $sql .= " ORDER BY average";
        	        $data = $this->objBenefit->getArray($sql);
        	        $n = count($data);
					if ($n < 4) {
						$ret = '--';
					} else {
						$ret = ($n % 2 == 0)? round($data[(($n+1)/2)-1]['average'], 2) :
							round(($data[($n/2)-1]['average']+$data[$n/2]['average'])/2, 2);
					}
					
				} else {
					$sql = "SELECT $mysqlFunction(b.value+0) AS average, COUNT(a.id) AS sample
						 	   FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
					if ($sicId != 'all') {
						$sql .= ", tbl_award_unit_sic AS s ";
					}
					$sql .="WHERE b.nameid = '$id'
								AND b.agreeid = a.id
								AND YEAR(a.implementation) = '$year'";
					if ($agreeTypeId != 'all') {
					    $sql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
					}
					if ($sicId != 'all') {
					    $sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
					}
					$data = current($this->objBenefit->getArray($sql));
					if ($data['sample'] < 4) {
					    $ret = '--';
					} else {
					    $ret = round($data['average'],2);
					}
				}
				break;
	           
	        default:           //percentage (show evidence)
	            $tSql = "SELECT COUNT(a.id) AS sample
        				 FROM tbl_award_agree AS a ";
    			if ($sicId != 'all') {
    				$tSql .= ", tbl_award_unit_sic AS s ";
    			}
    			$tSql .="WHERE YEAR(a.implementation) = '$year'";
    			if ($agreeTypeId != 'all') {
    				$tSql .= ($agreeTypeId == 'cb')? " AND (a.typeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
    			}
    			if ($sicId != 'all') {
    				$tSql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
    			}			
    			$sql = "SELECT COUNT(b.agreeid) AS sample
        			FROM tbl_award_benefits AS b, tbl_award_agree AS a ";
    			if ($sicId != 'all') {
    				$sql .= ", tbl_award_unit_sic AS s ";
    			}
    			$sql .="WHERE b.nameid = '$id'
        				AND b.agreeid = a.id
        				AND YEAR(a.implementation) = '$year'";
    			if ($agreeTypeId != 'all') {
    				$sql .= ($agreeTypeId == 'cb')? " AND (a.atypeid = '2' || a.typeid = '3' || a.typeid = '4')" : " AND a.typeid = '$agreeTypeId'";
    			}
    			if ($sicId != 'all') {
    				$sql .= " AND a.unitid = s.unitid AND s.major_divid = '$sicId'";
    			}
    			$data = current($this->objBenefit->getArray($sql));
    			$total = current($this->objBenefit->getArray($tSql));
				$ret = ($total['sample'] < 4)? '--' : round(($data['sample']/$total['sample'])*100, 2)."%";

	    }
	    return $ret;
	}
}
?>
