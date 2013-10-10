<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS
* @author Nic Appleby
* @version $id$
*/


function countSort($a,$b) {
	if (count($a) == count($b)) {
		return strcasecmp($a['unitName'],$b['unitName']);
	} else {
		return (count($a) > count($b))? 1 : -1;
	}
}

function unionSort($a,$b) {
	$ret = strcasecmp($a['unionName'],$b['unionName']);
	return ($ret == 0)? countSort($a,$b) : $ret;
}

/********************************************************************/
if (($startYear+($length-1)) > date('Y')) {
	$startYear = date('Y') - ($length-1);
}
for ($i=$startYear+($length-1);$i>=$startYear;$i--) {
	switch ($mode) {
		case 'max':
			$ave = $this->objDbWages->getAverageMaxWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSicId);
			break;
		case 'med':
			$ave = $this->objDbWages->getMedianWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSicId);
			break;
		case 'min':
		default:
			$ave = $this->objDbWages->getAverageMinWages($industry,$soc,$indexId,$minorSic,$wageTypeId,$agreeId,$i,$subSicId);
			break;
	}

	if (isset($ave['units'])) {
		foreach ($ave['units'] as $unit) {
			$unitYears[$unit][] = $i;
            $unitRecord = $this->objDbUnit->getRow('id',$unit);
			$unitYears[$unit]['unitName'] = $unitRecord['name'];
			$union = $this->orgFacet->getUnitTradeUnion($unit);
			$unitYears[$unit]['unionName'] = $union['abbrev'];
			if (!isset($units[$unit])) {
				$units[$unit] = 1;
			} else {
				$units[$unit]++;
			}
		}
	}
}
$unitCount = count($units);
if ($unitCount != 0) {
	$countArray = array_count_values($units);
	if (isset($countArray[$length])) {
		$coverage = round((($countArray[$length]/$unitCount)*100),1);
	} else {
		$coverage = 0;
	}
	$sum = array_sum($units);
	$continuity = round(((($sum/count($units))/$length)*100),1);
	uasort($unitYears,"{$sortType}Sort");
} else {
	$continuity = 0;
}

$countSortArray = $unionSortArray = array('action'=>'continuity','length'=>$length,'industry'=>$industry,
					'soc'=>$soc,'minorSic'=>$minorSic,'wageTypeId'=>$wageTypeId,'selected'=>'init_03',
					'agreeId'=>$agreeId,'mode'=>$mode,'subsicid'=>$subSicId,'startYear'=>$startYear,'indexId'=>$indexId);
$countSortArray['sortType'] = 'count';
$unionSortArray['sortType'] = 'union';

/****************************************************************
 * Table of htmlelements to refine the search criteria
 ***************************************************************/
$this->loadClass('textinput','htmlelements');
$refineTable = $this->newObject('htmltable','htmlelements');
$modeSelect = new textinput('mode',$mode,'hidden');

$socMajGrps = $this->objDbSocMajorGroup->getAll("ORDER BY description ASC");
$socSelect = new dropdown('soc');
if (is_array($socMajGrps)) {
	foreach ($socMajGrps as $grp) {
		$socSelect->addOption($grp['id'],$grp['description']);
	}
}
$socSelect->setSelected($soc);

$industries = $this->objDbSicMajorDivs->getAll("ORDER BY description ASC");
$sicSelect = new dropdown('industry');
$sicSelect->addOption('all',$this->objLanguage->languageText('phrase_allindustries'));
if (is_array($industries)) {
	foreach ($industries as $ind) {
		if (strlen($ind['description']) > 45) {
			$ind['description'] = substr($ind['description'],0,42).'...';
		}
		$sicSelect->addOption($ind['id'],$ind['description']);
	}
}
$sicSelect->setSelected($industry);


$yearSelect = new dropdown('length');
$yearSelect->addOption(5,'5');
$yearSelect->addOption(10,'10');
$yearSelect->setSelected($length);

$firstYear = new textinput('startYear',$startYear,'text',4);
$wageSelect = new textinput('wageTypeId',$wageTypeId,'hidden');
$minorSicSelect = new textinput('minorSic',$minorSic,'hidden');
$indexSelect = new textinput('indexId',$indexId,'hidden');
$subSicSelect = new textinput('subsicid',$subSicId,'hidden');
$hiddenSel = new textinput('selected','init_03','hidden');

$agreeTypes = $this->objDbAgreeType->getAll('ORDER BY name ASC');
$agreeSelect = new dropdown('agreeId');
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
$agreeSelect->setSelected($agreeId);


$refineSubmit = new button("refineSubmit","Update Table");
$refineSubmit->setToSubmit();

//populate table of options to refine statistics
$refineTable->startRow();
$refineTable->addCell('');
$refineTable->addCell($modeSelect->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_occupation",'award').": ");
$refineTable->addCell($socSelect->show());
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_noyears",'award').": ");
$refineTable->addCell($yearSelect->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_industry",'award').": ");
$refineTable->addCell($sicSelect->show());
$refineTable->addCell($this->objLanguage->languageText("mod_lrspostlogin_startyear",'award').": ");
$refineTable->addCell($firstYear->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell($this->objLanguage->languageText("mod_lrs_agreementtype",'award').": ");
$refineTable->addCell($agreeSelect->show().$subSicSelect->show().$hiddenSel->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell('');
$refineTable->addCell($refineSubmit->show(),null,null,'right');
$refineTable->addCell($minorSicSelect->show());
$refineTable->addCell($wageSelect->show().$indexSelect->show());
$refineTable->endRow();

$refineForm = new form('refineForm',$this->uri(array('action'=>'continuity','selected'=>$this->getParam('selected'))));
$refineForm->addToForm($refineTable->show());


/******************************************************************/
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrswages_continuityheader','award');

$text = str_replace('{PERC}',"$continuity%",$this->objLanguage->languageText('mod_lrswages_continuity','award')."<br/>");
$text .= "$coverage% ".$this->objLanguage->languageText('mod_lrswages_coverage','award');

$conTable = $this->newObject('htmltable','htmlelements');
$conTable->cellspacing=2;
$link = $this->getObject('link','htmlelements');
$link->link($this->uri($countSortArray,'award'));
$link->link = $this->objLanguage->languageText('phrase_bargunit');
$link->style = "color: white; background: none";
$countLink = $link->show();
$link->link($this->uri($unionSortArray,'award'));
$link->link = $this->objLanguage->languageText('phrase_tradeunion');
$unionLink = $link->show();
$link->style ='';
$head = array($countLink,$unionLink);
for ($i=$startYear+($length-1);$i>=$startYear;$i--) {
	$head[] = $i;
}
$icon = $this->newObject('geticon','htmlelements');
$icon->setIcon('ok','png');
$tick = $icon->show();
$icon->setIcon('failed','png');
$cross = $icon->show();
$class = 'even';
$conTable->addHeader($head);
if (is_array($unitYears)) {
	foreach ($unitYears as $unitId => $year) {
	($class == 'odd')? $class = 'even' : $class = 'odd';
	$conTable->startRow($class);
	$unit = $this->objDbUnit->getRow('id',$unitId);
	$link = $this->getObject('link','htmlelements');
	$link->link($this->uri(array('action'=>'bargainingunitoverview','id'=>$unitId)));
	$link->link = $unit['name'];
	$unitName = $link->show();
	$union = $this->orgFacet->getUnitTradeUnion($unitId);
	$link->link($this->uri(array('action'=>'viewbranch','unionId'=>$union['id'])));
	$link->link = $union['abbrev'];
	$unionName = $link->show();
	$conTable->addCell($unitName,null,null,'center');
	$conTable->addCell($unionName,null,null,'center');
	for ($i=$startYear+($length-1);$i>=$startYear;$i--) {
		if (in_array($i,$year)) {
			$conTable->addCell($tick,null,null,'center');
		} else {
			$conTable->addCell($cross,null,null,'center');
		}
	}
	$conTable->endRow();
	}
}

$backLink = $this->getObject('link','htmlelements');
$backLink->link($this->uri(array('action'=>'wages','selected'=>$this->getParam('selected')),'award'));
$backLink->link = $this->objLanguage->languageText('word_back');


/*********************************************************************
 * Main content table
 ********************************************************************/

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->startRow();
$objTable->addCell($header->show());
$objTable->addCell($backLink->show(),null,"right");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<b><i>$text</i></b>");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($conTable->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($refineForm->show());
$objTable->endRow();

$content = $objTable->show();
echo $content;

?>