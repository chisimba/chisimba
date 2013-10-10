<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('multitabbedbox','htmlelements');

/******************* WAGE AGGREGATES TAB *******************/

$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_wageheader','award');

$avgTab = '<div id="aggregatesTab">';
$indexId = $this->indexFacet->getIndexId('CPI');
$wageTypeId = $this->objDbPayPeriodType->getDefaultPPId();
$start = microtime(true);
$avgTab .= $this->objTemplates->generateAggregates('all',$indexId,'ave',$socMajGrpId,$wageTypeId,'all');
//$avgTab .= $this->objTemplates->getDefaultAggregates();
$avgTab .= '</div>';
$end = microtime(true);
$timer = round($end - $start,3);
$wageTab = $avgTab."$timer sec";



/******************* COMPARE INDUSTRIES TAB *************************/

$start = microtime(true);
$indTab = "<div id='industryAggregates'>".
          $this->objTemplates->getIndustryWageAggregates(array('all'),$socMajGrpId,'ave','all',$wageTypeId,5,date('Y')-4).
		  "</div>";
$timer = round(microtime(true) - $start,3);

/******************* COMPARE OCCUPATIONS TAB ************************/

$start = microtime(true);
$socTab = "<div id='socAggregates'>".
			$this->objTemplates->getSocWageAggregates('avg',$wageTypeId, date('Y')).
			"</div>";
$timer2 = round(microtime(true) - $start,3);


/****************** TAB LAYOUT ****************************/
$wageTabs = $this->newObject('tabcontent','htmlelements');

$wageTabs->addTab($this->objLanguage->languageText('mod_lrswages_wage','award'),$wageTab);
$wageTabs->addTab($this->objLanguage->languageText('phrase_industryagg'),$indTab.$timer);
$wageTabs->addTab($this->objLanguage->languageText('phrase_socwages'), $socTab.$timer2);
$wageTabs->width = '835px';
$jsPath = $this->getResourceUri('postlogin.js');
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsPath'></script>");
echo $header->show().$wageTabs->show();
?>
