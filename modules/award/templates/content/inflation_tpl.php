
<?php

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('multitabbedbox','htmlelements');
$this->loadClass('textinput','htmlelements');

/**************** SUMMARY TAB *******************/
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_inflationheader', 'award');

$inflationContent = $this->objTemplates->getInflationSummary();
$inflationTab = "<div id='inflationDiv'>$inflationContent</div>";

$inflationSummary = $inflationTab;

/******************* DETAILED TAB ***************/

$shortName = $this->indexFacet->getIndexShortName($indexId);

$this->indexFacet->getIndexShortName($indexId);
$headerRow = array($this->objLanguage->languageText('word_year'));//,$this->objLanguage->languageText('word_index'));
for ($j=2;$j<14;$j++) {
	$headerRow[] = date('M',mktime(0,0,0,$j,0,0));
}

$headerRow[] = $this->objLanguage->languageText('word_average');
$indexTable = $this->newObject('htmltable','htmlelements');
$indexTable->border = 1;
$indexTable->cellpadding = $indexTable->cellspacing = 2;
$indexTable->startHeaderRow();
foreach ($headerRow as $head) {
	$indexTable->addHeaderCell($head);
}
$indexTable->endHeaderRow();

$year = date('Y');
for ($i=$startYear;$i<=$year;$i++) {
	$class = ($i % 2 == 0)? 'even' : 'odd';

	$indexTable->startRow($class);
	$indexTable->addCell($i,null,null,'center');
	//$indexTable->addCell('index<br/>%',null,null,'center');
	$gotAllValues = 0;
	$total = 0;
	for ($j=1;$j<13;$j++) {
		$value = $this->indexFacet->getPercentageDifference($j,$i,$indexId);
		if ($value != '--') {
			$total += $value;
			$gotAllValues++;
			$value .= '%';
		}
		$indexTable->addCell($value,null,null,'center');//round($this->indexFacet->getCurrentIndexValue($j,$i,$indexId),1).'<br/>'.
	}
	if ($gotAllValues != 0) {
		$aveIndex = round($total/$gotAllValues,1).'%';//$this->indexFacet->getIndexIncrease($indexId,$i)
	} else {
		$aveIndex = '--';
	}
	$indexTable->addCell($aveIndex,null,null,'center');//round($this->indexFacet->getIndexAverage($indexId,$i),1).'<br/>'.
	$indexTable->endRow();
}

/********************* GRAPH ***************************/
$width = 550;
$height = 350;
$imageUri = $this->uri(array('action'=>'plotinflationgraph','indexid'=>$indexId,'year'=>$year,'width'=>$width,'height'=>$height),'award');
$graph = "<img src='$imageUri' width='$width' height='$height' alt='inflation graph'/>";
$summary = $this->objSummary->getRow('indexid',$indexId);
$graphHeading = str_replace('{INDEX}',$shortName,$this->objLanguage->languageText('mod_lrs_graphheading', 'award'));
$graphSummary = "<br /><b><i>$graphHeading</i></b><br />".$summary['summary']."<br />$graph";


/************************ RESOURCES ********************/
$resources = "<div class='topright'>".$this->objLanguage->languageText("mod_award_stats","award")." <a href='http://www.statssa.gov.za'>Statistics South Africa</a></div>";


/******************* MAIN TABLE ************************/
$pageTable = $this->newObject('htmltable','htmlelements');
$pageTable->cellspacing=4;
$tTitle = str_replace('{INDEX}',$shortName,$this->objLanguage->languageText('mod_lrsindex_inflationtable', 'award'));
$pageTable->startRow();
$pageTable->addCell("<b><i>$tTitle</i></b>",null,null,'left');
$pageTable->endRow();
$pageTable->startRow();
$pageTable->addCell($this->objLanguage->languageText('mod_lrsindex_inflationtablehelp', 'award'),null,null,'left');
$pageTable->endRow();
$pageTable->startRow();
$pageTable->addCell($indexTable->show());
$pageTable->endRow();

$form = new form('inflation_form');

/*$years = new dropdown('length');
$years->addOption(5,'5');
$years->addOption(10,'10');
$years->addOption(15,'15');
$years->setSelected((int)$length);*/
$years = new textinput('startYear', $startYear, 'text', 4);

// Initiating the dropdown
$index = new dropdown('indexid');
$allData = $this->objIndexes->getAll();
$index->addFromDB($allData,'shortname','id',$indexId);

$default = new textinput('detailedview','true','hidden');

$submit = new button('sub',$this->objLanguage->languageText('mod_lrs_changetable', 'award'));
$submit->setToSubmit();

$change = $this->objLanguage->languageText('mod_lrs_changeindex', 'award').": ".$index->show()." "
			.$this->objLanguage->languageText('mod_lrspostlogin_startyear', 'award').": ".$years->show()." "
			.$submit->show().$default->show();
$form->addToForm($change);
$form->addRule(array('name'=>'startYear', 'maxnumber'=>$year), $this->objLanguage->languageText('mod_award_validyear', 'award'), 'maxnumber');

$pageTable->startRow();
$pageTable->addCell($form->show());
$pageTable->endRow();
$pageTable->startRow();
$pageTable->addCell($graphSummary,'50%',null,'left');
$pageTable->endRow();
$detailed = $pageTable->show();

/************** TABS ********************/
if ($this->getParam('detailedview')) {
	$sDefault = false;
	$dDefault = true;
} else {
	$sDefault = true;
	$dDefault = false;
}

$tabs = $this->newObject('tabcontent','htmlelements');
$tabs->addTab($this->objLanguage->languageText('mod_lrs_inflationsummary', 'award'),$inflationSummary,null,$sDefault);
$tabs->addTab($this->objLanguage->languageText('mod_lrs_inflationdetail', 'award'),$detailed,null,$dDefault);
$tabs->width = '835px';

$jsLib = $this->getResourceUri("postlogin.js");
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsLib'></script>");
echo $header->show()."$resources<br />".$tabs->show();
?>