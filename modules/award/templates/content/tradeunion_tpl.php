<?php

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('multitabbedbox','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('button','htmlelements');
$currency = $this->objSysConfig->getValue('CURRENCY_ABREVIATION','award');
$payPeriod = $this->objDbPayPeriodType->getDefaultppType();
$decimals = ($payPeriod['factor'] == 0)? 2 : 0;

/****************** PARTY BRANCH *******************************************/
$head = $this->getObject('htmlheading','htmlelements');
$head->str = $this->objLanguage->languageText("mod_lrsorg_partybranchreport", 'award');
$head->type = 2;

$party_branches = $this->objDbBranch->getAll("WHERE partyid = '$tuIndex' ORDER BY name ASC");
$objTable = $this->newObject('htmltable','htmlelements');
$objTable->addHeader(array($this->objLanguage->languageText('mod_lrsorg_tupb', 'award'),
                           $this->objLanguage->languageText('word_sample'),
                           $this->objLanguage->languageText('mod_lrsorg_noworkers', 'award'),
                           $payPeriod['name']." ".$this->objLanguage->languageText('word_average')." ($currency)",
                           $this->objLanguage->languageText('phrase_increasepercentage'),
                           $this->objLanguage->languageText('phrase_actualincrease')." ".
                           $this->indexFacet->getIndexShortName($indexId)));  
$objTable->cellspacing="2";
$link = $this->newObject('link','htmlelements');
$class = 'odd';



foreach ($party_branches as $branch) {
    $objPop = $this->newObject('windowpop', 'htmlelements');
    $objPop->set('location',$this->uri(array('action'=>'viewbargainingunit','branchId'=>$branch['id'], 'unionId'=>$tuIndex, 'selected'=>'init_10'),'award'));
    $objPop->set('linktext',strtoupper($branch['name']));
    $objPop->set('width','1024');
    $objPop->set('height','600');
    $objPop->set('left','0');
    $objPop->set('top','0');   
    $objPop->set('resizable','yes');   
    $objPop->set('scrollbars','yes');
    $objPop->set('status','yes');
    $objPop->set('toolbar','yes');
    $objPop->set('menubar','yes');
    $objPop->putJs();          // you only need to do this once per page

	$class = ($class == 'odd')? 'even' : 'odd';
	switch ($mode) {
		case 'min':
			$total = $this->objDbBranch->getAverageMinWagesForBranch($branch['id'], $socId, $indexId, $agreeTypeId, $year);
			break;
		case 'med':
			$total = $this->objDbBranch->getMedianWagesForBranch($branch['id'], $socId, $indexId, $agreeTypeId, $year);
			break;
		default:
		case 'ave':
			$total = $this->objDbBranch->getAverageWagesForBranch($branch['id'], $socId, $indexId, $agreeTypeId, $year);
			break;
	}
	
	$average = ($total['average'] == 0)? '--' : number_format($total['average'], $decimals);
	$increase = ($total['increaseAve'] == 0)? '--' : round($total['increaseAve'], 2);
	$real = ($total['realAve'] == 0)? '--' : round($total['realAve'], 2);
	
	$objTable->startRow();
	$objTable->addCell($objPop->show(),null,null,null,$class);
	$objTable->addCell("<b>{$total['sample']}</b>",'10%',null,'center',$class);
	$objTable->addCell("<b>".number_format($total['workers'],0)."</b>",'10%',null,'center',$class);
	$objTable->addCell("<b>$average</b>",'10%',null,'center',$class);
	$objTable->addCell("<b>$increase</b>",'10%',null,'center',$class);
	$objTable->addCell("<b>$real</b>",'10%',null,'center',$class);
	$objTable->endRow();
	$industries = $this->objDbBranch->getIndustries($branch['id'],$socId,$year,$agreeTypeId);
    
	foreach($industries as $ind) { 
		switch ($mode) {
			case 'min':
				$indTotal = $this->objDbBranch->getAverageMinWagesForBranch($branch['id'], $socId, $indexId, $agreeTypeId, $year, $ind['id']);
				break;
			case 'med':
				$indTotal = $this->objDbBranch->getMedianWagesForBranch($branch['id'], $socId, $indexId, $agreeTypeId, $year, $ind['id']);
				break;
			default:
			case 'ave':
				$indTotal = $this->objDbBranch->getAverageWagesForBranch($branch['id'], $socId, $indexId, $agreeTypeId, $year, $ind['id']);
				break;
		}
		$indAverage = ($indTotal['average'] == 0)? '--' : number_format($indTotal['average'], $decimals);
		$indIncrease = ($indTotal['increaseAve'] == 0)? '--' : round($indTotal['increaseAve'], 2);
		$indReal = ($indTotal['realAve'] == 0)? '--' : round($indTotal['realAve'], 2);
		
		$objTable->startRow();
		$objTable->addCell("&nbsp;&nbsp;&nbsp;&nbsp;{$ind['industry']}",null,null,null,$class);
		$objTable->addCell($indTotal['sample'],'10%',null,'center',$class);
		$objTable->addCell(number_format($indTotal['workers']),'10%',null,'center',$class);
		$objTable->addCell($indAverage,'10%',null,'center',$class);
		$objTable->addCell($indIncrease,'10%',null,'center',$class);
		$objTable->addCell($indReal,'10%',null,'center',$class);
		$objTable->endRow();
	}
}

switch ($mode) {
	case 'min':
		$total = $this->objDbBranch->getAverageMinWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year);
		break;
	case 'med':
		$total = $this->objDbBranch->getMedianWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year);
		break;
	default:
	case 'ave':
		$total = $this->objDbBranch->getAverageWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year);
		break;
}
$total = $this->objDbBranch->getAverageWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year);
$average = ($total['average'] == 0)? '--' : number_format($total['average'], $decimals);
$increase = ($total['increaseAve'] == 0)? '--' : round($total['increaseAve'], 2);
$real = ($total['realAve'] == 0)? '--' : round($total['realAve'], 2);
	
$totalStr = $this->objLanguage->languageText('mod_lrs_tutotal', 'award');
$objTable->startRow('odd');
$objTable->addCell("<b>$totalStr</b>");
$objTable->addCell("<b>{$total['sample']}</b>",'10%',null,'center');
$objTable->addCell("<b>".number_format($total['workers'])."</b>",'10%',null,'center');
$objTable->addCell("<b>$average</b>",'10%',null,'center');
$objTable->addCell("<b>$increase</b>",'10%',null,'center');
$objTable->addCell("<b>$real</b>",'10%',null,'center');
$objTable->endRow();

$refineTable = $this->newObject('htmltable','htmlelements');
$modeSelect = new dropdown('mode');
$modeSelect->addOption('min',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'));
$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
$modeSelect->addOption('ave',$this->objLanguage->languageText('word_average'));
$modeSelect->setSelected($mode);

$socMajGrps = $this->objDbSocMajorGroup->getAll("ORDER BY id DESC");
$socSelect = new dropdown('id');
if (is_array($socMajGrps)) {
	foreach ($socMajGrps as $grp) {
		$socSelect->addOption($grp['id'],$grp['description']);
	}
}
$socSelect->setSelected((int)$socId);

$indexes = $this->indexFacet->getIndexes();
$indexSelect = new dropdown('indexid');
if (is_array($indexes)) {
	foreach ($indexes as $index) {
		$indexSelect->addOption($index['id'],$index['shortname']);
	}
}
$indexSelect->setSelected((int)$indexId);

$startYear = new textinput('year',$year,'text',4);

$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
$agreeSelect = new dropdown('typeid');
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


$refineSubmit = new button("refineSubmit","Update Table");
$refineSubmit->setToSubmit();

//populate table of options to refine statistics
$refineTable->padding = 2;
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype", 'award').": ",'15%');
$refineTable->addCell($modeSelect->show(),'20%');
$refineTable->addCell(null,'5%');
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_index", 'award').": ");
$refineTable->addCell($indexSelect->show());
$refineTable->addCell(null,'25%');
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_occupation", 'award').": ");
$refineTable->addCell($socSelect->show());
$refineTable->addCell(null,'5%');
$refineTable->addCell($this->objLanguage->languageText("mod_lrspostlogin_startyear", 'award').": ");
$refineTable->addCell($startYear->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype", 'award').": ");
$refineTable->addCell($agreeSelect->show());

$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell('');
$refineTable->addCell($refineSubmit->show(),null,null,'right');
$refineTable->addCell('');
$refineTable->addCell('');
$refineTable->endRow();

$hiddenTu = new textinput('tuIndex',(int)$tuIndex,'hidden');
$objForm = new form('refine');
$objForm->addToForm($objTable->show()."<br/>".$refineTable->show());
$objForm->addToForm($hiddenTu->show());

$pBranch = $head->show().$objForm->show();


/****************** INDUSTRY VIEW *******************************/
$head = $this->getObject('htmlheading','htmlelements');
$head->str = $this->objLanguage->languageText("mod_lrsorg_industryreport", 'award');
$head->type = 2;


$objTable = $this->newObject('htmltable','htmlelements');
$objTable->addHeader(array($this->objLanguage->languageText('word_industry'),
                $this->objLanguage->languageText('word_agreements'),
				$this->objLanguage->languageText('mod_lrsorg_noworkers', 'award'), "{$payPeriod['name']}"." ".$this->objLanguage->languageText('word_average')." ($currency)",
				$this->objLanguage->languageText('phrase_increasepercentage'),
                $this->objLanguage->languageText('phrase_actualincrease'). $this->indexFacet->getIndexShortName($indexId)));

$objTable->cellspacing="2";
$link = $this->newObject('link','htmlelements');
$indexInc = $this->indexFacet->getIndexIncrease($indexId,$year);
$objSicDiv = $this->getObject('dbsicdiv','awardapi');
	
$industries = $this->objDbBranch->getIndustriesNoBranch($tuIndex,$socId,$year,$agreeTypeId);
foreach($industries as $ind) {
	switch ($mode) {
		case 'min':
			$total = $this->objDbBranch->getAverageMinWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year, $ind['id']);
			break;
		case 'med':
			$total = $this->objDbBranch->getMedianWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year, $ind['id']);
			break;
		default:
		case 'ave':
			$total = $this->objDbBranch->getAverageWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year, $ind['id']);
			break;
	}
	$indAverage = ($total['average'] == 0)? '--' : number_format($total['average'], $decimals);
	$indIncrease = ($total['increaseAve'] == 0)? '--' : round($total['increaseAve'], 2);
	$indReal = ($total['realAve'] == 0)? '--' : round($total['realAve'], 2);

	$objTable->startRow('even');
	$objTable->addCell("<b>".strtoupper($ind['industry'])."</b>",null,null,null);
	$objTable->addCell("<b>{$total['sample']}</b>",'10%',null,'center');
	$objTable->addCell("<b>".number_format($total['workers'])."</b>",'10%',null,'center');
	$objTable->addCell("<b>$indAverage</b>",'10%',null,'center');
	$objTable->addCell("<b>$indIncrease</b>",'10%',null,'center');
	$objTable->addCell("<b>$indReal</b>",'10%',null,'center');
	$objTable->endRow();
	$divs = $objSicDiv->getAll("WHERE major_divid = '{$ind['id']}'");
	foreach ($divs as $div) {
		
		switch ($mode) {
			case 'min':
				$data = $this->objDbBranch->getAverageMinWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year, $ind['id'], $div['id']);
				break;
			case 'med':
				$data = $this->objDbBranch->getMedianWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year, $ind['id'], $div['id']);
				break;
			default:
			case 'ave':
				$data = $this->objDbBranch->getAverageWagesForUnion($tuIndex, $socId, $indexId, $agreeTypeId, $year, $ind['id'], $div['id']);
				break;
		}
	
		$divAverage = ($data['average'] == 0)? '--' : number_format($data['average'], $decimals);
		$divIncrease = ($data['increaseAve'] == 0)? '--' : round($data['increaseAve'], 2);
		$divReal = ($data['realAve'] == 0)? '--' : round($data['realAve'], 2);

		$objTable->startRow();
		$objTable->addCell("&nbsp;&nbsp;&nbsp;&nbsp;{$div['description']}",null,null,null);
		$objTable->addCell($data['sample'],'10%',null,'center');
		$objTable->addCell(number_format($data['workers']),'10%',null,'center');
		$objTable->addCell($divAverage,'10%',null,'center');
		$objTable->addCell($divIncrease,'10%',null,'center');
		$objTable->addCell($divReal,'10%',null,'center');
		$objTable->endRow();
		//$class = 'odd';
		
	}
}

$refineTable = $this->newObject('htmltable','htmlelements');
$modeSelect = new dropdown('mode');
$modeSelect->addOption('min',$this->objLanguage->languageText('mod_lrspostlogin_minavgs', 'award'));
$modeSelect->addOption('med',$this->objLanguage->languageText('mod_lrspostlogin_medavgs', 'award'));
$modeSelect->addOption('ave',$this->objLanguage->languageText('word_average'));
$modeSelect->setSelected($mode);

$socMajGrps = $this->objDbSocMajorGroup->getAll("ORDER BY id DESC");
$socSelect = new dropdown('socId');
if (is_array($socMajGrps)) {
	foreach ($socMajGrps as $grp) {
		$socSelect->addOption($grp['id'],$grp['description']);
	}
}
$socSelect->setSelected((int)$socId);

$indexes = $this->indexFacet->getIndexes();
$indexSelect = new dropdown('indexId');
if (is_array($indexes)) {
	foreach ($indexes as $index) {
		$indexSelect->addOption($index['id'],$index['shortname']);
	}
}
$indexSelect->setSelected((int)$indexId);

$startYear = new textinput('year',$year,'text',4);

$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
$agreeSelect = new dropdown('typeid');
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
$agreeSelect->setSelected((int)$agreeTypeId);


$refineSubmit = new button("refineSubmitInd","Update Table");
$refineSubmit->setToSubmit();

//populate table of options to refine statistics
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_aggregatetype", 'award').": ",'15%');
$refineTable->addCell($modeSelect->show(),'20%');
$refineTable->addCell(null,'5%');
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_index", 'award').": ");
$refineTable->addCell($indexSelect->show());
$refineTable->addCell(null,'25%');
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_occupation", 'award').": ");
$refineTable->addCell($socSelect->show());
$refineTable->addCell(null,'5%');
$refineTable->addCell($this->objLanguage->languageText("mod_lrspostlogin_startyear", 'award').": ");
$refineTable->addCell($startYear->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype", 'award').": ");
$refineTable->addCell($agreeSelect->show());

$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell('');
$refineTable->addCell($refineSubmit->show(),'15%',null,'right');
$refineTable->addCell('');
$refineTable->addCell('');
$refineTable->endRow();
$hiddenTab = new textinput('default_tab_no','4','hidden');
$hiddenTu = new textinput('tuIndex',(int)$tuIndex,'hidden');
$objForm = new form('refineInd');
$objForm->addToForm($objTable->show()."<br/>".$refineTable->show().$hiddenTab->show());
$objForm->addToForm($hiddenTu->show());

$pIndustry = $head->show().$objForm->show();


/****************** EXPIRED AGREE *********************************************/

$head = $this->getObject('htmlheading','htmlelements');
$head->str = $this->objLanguage->languageText("mod_lrsorg_pbstructurereport",'award');
$head->type = 2;
$tree = "<ul id='expired_list'></ul>";
$pStructure = $head->show().$tree;


/****************** LOWEST WAGE **********************************************/

$head = $this->getObject('htmlheading','htmlelements');
$head->str =$this->objLanguage->languageText('mod_lrsorg_tulowestwage', 'award');
$head->type = 2;

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->addHeader(array($this->objLanguage->languageText('mod_lrsorg_agreename', 'award'),
							$this->objLanguage->languageText("word_occupation"),
							$this->objLanguage->languageText("word_industry"),
							$this->objLanguage->languageText('mod_lrsorg_wagerate', 'award')." ({$payPeriod['name']})"));
$objTable->cellspacing = 2;
$year = $this->getParam('year');
if ($year == null) {
    $year = date('Y');
}
$data = $this->objDbWages->getWageThreshold($tuIndex,$threshold,$year);
$class = 'odd';
$refSub = new button('threshsub',$this->objLanguage->languageText('word_go'));
$refSub->setToSubmit();
$thresh = new textinput('threshold',$threshold,'text',5);
$yearBox = new textinput('year',$year,'text',4);
$hiddenTab = new textinput('default_tab_no','3','hidden');
$refine = new form('refinemin');
$formContent = $this->objLanguage->languageText('mod_lrsorg_threshold', 'award')." ".$currency.$thresh->show()." {$payPeriod['name']}".
			$hiddenTu->show()."  ".$this->objLanguage->languageText('phrase_fortheyear').": ".$yearBox->show()." &nbsp;&nbsp; ".
			$refSub->show().$hiddenTab->show();
$refine->addToForm($formContent);

$decimals = ($this->objDbWages->ppCalc == 0)? 2 : 0;

foreach ($data as $datum) {
	if ($this->objUser->isAdmin()) { 
		$link->link($this->uri(array('action'=>'agreementoverview','id'=>$datum['agreeid'],
									 'unitId'=>$datum['unitid'], 'selected'=>'init_10'),'award'));
		$link->link = $datum['agreename'];
		$name = $link->show();
	} else {
		$name = $datum['agreename'];
	}
	$class = ($class == 'odd')? 'even' : 'odd';
	$objTable->startRow();
	$objTable->addCell($name,'35%',null,null,$class);
	$objTable->addCell($datum['occupation'],'20%',null,'center',$class);
	$objTable->addCell($datum['industry'],'30%',null,'center',$class);
	$objTable->addCell(number_format($datum['wagerate'], $decimals),'15%',null,'center',$class);
	$objTable->endRow();
}

$pLowest = $head->show().$refine->show().$objTable->show();


if (!isset($tuIndex)) {
	$msg = $this->objLanguage->languageText('mod_lrsorg_notradeunion', 'award');
	$pBranch = $pLowest = $pStructure = "<span class='error'>$msg</span><br><br>";
} 

/****************** DRAW TABS **********************************************/

$userInfo = $this->newObject('htmlheading','htmlelements');
$userInfo->type = 3;
$userInfo->str = ucwords(strtolower($this->objUser->fullname()));

/********* Get Trade Union Section ***************/

	$tab = $this->getParam('default_tab_no');
	$tbl = $this->newObject('htmltable','htmlelements');
	if ($this->objUser->isAdmin()) {
		$tuPhrase = $this->objLanguage->languageText('mod_lrsorg_selecttu', 'award');
		$tuSelect = new dropdown('tuIndex');
		$unions = $this->objDbParty->getAll("ORDER BY abbreviation ASC");
		foreach ($unions as $union) {
			$tuSelect->addOption($union['id'],$union['abbreviation']);
		}
		$tuSelect->setSelected((int)$tuIndex);
		$tuSelect->extra = "onchange='document.tuform.submit()'";
		$tuBox = "$tuPhrase: ".$tuSelect->show();
		$tuForm = new form('tuform');
		$tuForm->addToForm($tuBox);
		$tuForm->addToForm(new textinput('default_tab_no',$tab,'hidden'));
		$tbl->startRow();
		$tbl->addCell($tuForm->show());
		$tbl->addCell('',null,null,'right');
		$tbl->endRow();

		
	} else {
		$userInfo->str .= " - ".$this->objDbParty->getAbbrev($tuIndex);
		$tuForm = new form('tuform');
		$tuForm->addToForm(new textinput('default_tab_no',$tab,'hidden'));
		$tbl->startRow();
		$tbl->addCell($tuForm->show());
		$tbl->addCell('',null,null,'right');
		$tbl->endRow();
	}


$uInfo = $userInfo->show().$tbl->show();

$objH = $this->getObject('htmlheading','htmlelements');
//Set the header string
$objH->str = $this->objLanguage->languageText('mod_lrs_tuheading', 'award');
$objH->type = 2;
$homeHeader = $objH->show();

$tuTabs = $this->newObject('tabcontent','htmlelements');
switch ($tab) {
	case '2':
		$default1 = false;
		$default2 = true;
		$default3 = false;
		$default4 = false;
		break;
	case '3':
		$default1 = false;
		$default2 = false;
		$default3 = true;
		$default4 = false;
		break;
	case '4':
		$default1 = false;
		$default2 = false;
		$default3 = false;
		$default4 = true;
		break;
	default:
		$default1 = true;
		$default2 = false;
		$default3 = false;
		$default4 = false;
		break;
}

$tuTabs->addTab($this->objLanguage->languageText('mod_lrspostlogin_tubranch', 'award'), $pBranch, null, $default1);
$tuTabs->addTab($this->objLanguage->languageText('mod_lrspostlogin_tuexpired', 'award'), $pStructure, null, $default2);
$tuTabs->addTab($this->objLanguage->languageText('mod_lrspostlogin_tulowestwage', 'award'), $pLowest, null, $default3);
$tuTabs->addTab($this->objLanguage->languageText('word_industry'), $pIndustry, null, $default4 );
$tuTabs->width = '835px';

$content = $homeHeader.$uInfo.$tuTabs->show();

$jsInclude = $this->getResourceUri('jquery.treeview.pack.js');
$cssUri = $this->getResourceUri('jquery.treeview.css');
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsInclude'></script>");
$jsInclude = $this->getResourceUri('jquery.treeview.async.js');
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsInclude'></script>");
$this->appendArrayVar('headerParams',"<link href='$cssUri' rel='stylesheet' />");
$this->appendArrayVar('bodyOnLoad',"jQuery('#expired_list').treeview({url: 'index.php?module=award&action=ajaxtutree&tuId=$tuIndex', collapsed: true, unique: true});");

echo $content;
?>