<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/***************** SAMPLE STATISCTICS **********************/
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;

$header->str = $this->objLanguage->languageText('mod_lrs_statsheader', 'award');


$buCount = $this->objDbUnit->getAll();

$explanation = $this->objLanguage->languageText('mod_lrs_statsexp', 'award')." <br /><br />".$this->objLanguage->languageText('mod_lrs_statsbu', 'award')."<br /><br />";

$explanation = str_replace('[BUCOUNT]',"<b>".count($buCount)."</b>",$explanation);

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('word_industry'));
for ($i=0;$i<4;$i++) { 
	$objTable->addHeaderCell($year + $i,null,null,'center');
	$total[$i] = 0;
}
$objTable->endHeaderRow();

$class = 'odd';
$industries = $this->objDbSicMajorDivs->getAll();
foreach ($industries as $ind) {
	$objTable->startRow($class);
	$objTable->addCell($ind['description']);
	for ($i=0;$i<4;$i++) { 
		$currentYear = $year+$i;
		switch ($sampleTypeId) {
		    case '1':
		     	$value = $this->objAgree->getAgreementCount($ind['id'],$socId,$agreeTypeId,$currentYear);
		        break;
		    case '2':
		        $value = $this->objAgree->getWageCount($ind['id'],$socId,$agreeTypeId,$currentYear);
		        break;
		    case '3':
		        $value = $this->objAgree->getBUCount($ind['id'],$socId,$agreeTypeId,$currentYear);
		        break;
	    }
		$objTable->addCell($value,null,null,'center');
		$total[$i] += $value;
	}
	$objTable->endRow();
	
	$class = ($class == 'odd')? 'even' : 'odd';
}

$objTable->startRow($class);
$objTable->addCell("<b>".$this->objLanguage->languageText('word_total')."</b>");
for ($i=0;$i<4;$i++) { 
	$objTable->addCell("<b>{$total[$i]}</b>",null,null,'center');
}
$objTable->endRow();

$this->loadClass('dropdown','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('button','htmlelements');

$socs = $this->objDbSocMajorGroup->getAll("ORDER BY id +0 DESC");
$socDrop = new dropdown('socid');
$socDrop->addFromDB($socs,'description','id',$socId);

$agreeTypes = $this->objDbAgreeType->getAll("ORDER BY name");
$agreeTypeDrop = new dropdown('agreeTypeId');
$agreeTypeDrop->addOption('all',$this->objLanguage->languageText('phrase_alltypes'));
$agreeTypeDrop->addOption('cb',$this->objLanguage->languageText('phrase_collectivebargaining'));
$agreeTypeDrop->addFromDB($agreeTypes,'name','id',$agreeTypeId);
//$agreeTypeDrop->setSelected($agreeTypeId);

$sampleDrop = new dropdown('sampleTypeId');
$sampleDrop->addOption('1',$this->objLanguage->languageText('word_agreements'));
$sampleDrop->addOption('2',$this->objLanguage->languageText('word_wages'));
$sampleDrop->addOption('3',$this->objLanguage->languageText('phrase_bargainingunits'));
$sampleDrop->setSelected($sampleTypeId);

$years = new textinput('year',$year,'text',4);
$statsFlag = new textinput('stats','1','hidden');

$sButton = new button('sub',$this->objLanguage->languageText('word_update'));
$sButton->setToSubmit();

$refineTable = $this->newObject('htmltable','htmlelements');
$refineTable->startRow();

$refineTable->addCell($this->objLanguage->languageText('mod_lrs_sampletype', 'award').': ');

$refineTable->addCell($sampleDrop->show());

$refineTable->addCell($this->objLanguage->languageText('mod_lrs_agreementtype','award'));

$refineTable->addCell($agreeTypeDrop->show());
$refineTable->endRow();
$refineTable->startRow();

$refineTable->addCell($this->objLanguage->languageText('mod_lrs_occupation', 'award'));

$refineTable->addCell($socDrop->show());

$refineTable->addCell($this->objLanguage->languageText('mod_lrspostlogin_startyear','award'));

$refineTable->addCell($years->show().$statsFlag->show());
$refineTable->endRow();
$refineTable->startRow();
$refineTable->addCell('');
$refineTable->addCell($sButton->show(),null,null,'right');
$refineTable->addCell('');
$refineTable->addCell('');
$refineTable->endRow();

$objForm = new form('refine');
$objForm->addToForm($refineTable->show());

$stats = $header->show().$explanation.$objTable->show()."<br />".$objForm->show();



/********************** NEWS TAB *************************/
$header = $this->newObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_defaultheader','award');

$block = $this->getObject('blocks','blocks');
$objGoogle = $this->getObject('googlenews');

if ($this->objSysConfig->getValue('has_google_api_key','award') == '1') {
	$loadingPhrase = $this->objLanguage->languageText('mod_lrspostlogin_loadinggoogle','award');
} else {
	$loadingPhrase = '';
	}

$objTable = $this->newObject('htmltable','htmlelements');
$ppTable = $this->newObject('htmltable','htmlelements');
$objTable->cellpadding = $objTable->cellspacing = 2;


$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('dropdown','htmlelements');


$defaultPP = $this->getSession('pay_period_type_id',$this->objSysConfig->getValue('default_pp_type','award'),'award');
$ppTypes = $this->objDbPayPeriodType->getAll("ORDER BY name DESC");
$ppDrop = new dropdown('ppTypeId');
$ppDrop->addFromDB($ppTypes,'name','id',$defaultPP);

$ppButton = new button('ppSub',$this->objLanguage->languageText('phrase_changepp'));
$ppButton->setToSubmit();

$ppForm = new form('ppform',$this->uri(array('action'=>'selectpp')));
//$ppForm->extra = ' class="negativemarginbottom"';
$ppForm->addToForm($this->objLanguage->languageText('mod_lrs_changepp','award').": ".$ppDrop->show()." ".$ppButton->show());


$ppTable->startRow();
$ppTable->addCell($ppForm->show(),null,null,'right');
$ppTable->endRow();

/*$objTable->startRow();
$objTable->addCell("<div id = 'newsTab'>".$block->showBlock('cms_latestnews','cms'),'58%');
$objTable->addCell('&nbsp;','4%');
$objTable->addCell("</div><br /><div id='googleNews'>$loadingPhrase</div>",'38%');
$objTable->endRow();*/
$cmsNews = "<div id = 'newsTab'>".$block->showBlock('stories','stories')."</div>";
$googleNews = "<div id='googleNews'>$loadingPhrase</div>";

$defaultSearch = $this->objSysConfig->getValue('google_search_terms','award');
$this->appendArrayVar('headerParams',$objGoogle->getScript('googleNews',$defaultSearch));
//$this->appendArrayVar('headerParams',"<script type='text/javascript' src='modules/lrspostlogin/resources/postlogin.js'></script>".$objGoogle->getScript('googleNews',$defaultSearch));
$news = $this->objLanguage->languageText('mod_lrs_homeintro','award')."$cmsNews$googleNews";

/****************** CONTENT TABS **************************/
$homeTabs = $this->newObject('tabcontent','htmlelements');
if ($this->getParam('stats')) {
    $nDefault = false;
    $sDefault = true;
} else {
    $nDefault = true;
    $sDefault = false;
}

$homeTabs->addTab($this->objLanguage->languageText('word_home'),$news,null,$nDefault);
$homeTabs->addTab($this->objLanguage->languageText('mod_lrs_statsheader','award'),$stats,null,$sDefault);
$homeTabs->width = '835px';
echo $header->show().$ppTable->show().$homeTabs->show();
?>