<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
 * Agreement level template for lrspostlogin
 *
 * @package LRS
 * @author Nic Appleby
 * @license GNU/GPL
 * @copyright UWC
 * @version $Id: budata_tpl.php 123 2008-08-15 13:27:36Z nonqaba $
 *
 */

$heading = $this->getObject('htmlheading','htmlelements');
$heading->type = 2;
//$heading->cssClass = "negativebottom";
$heading->str = $this->objLanguage->languageText('mod_lrspostlogin_budataheading','award');


//General info
$unit = $this->objDbUnit->getRow('id',$unitId);
$agree_types = $this->objDbAgreeType->getTypesByUnit($unitId);
$aTypes = array();
$partyArray = array();

foreach ($agree_types as $type) {
	$aTypes[] = $type['name'];
}
$agree_type_names = implode(', ',$aTypes);

//Bargaining unit data
$name = $unit['name'];
$unitParties = $this->objAgree->getUnitParties($unitId);
foreach ($unitParties as $type) {
	$partyArray[] = $type['name'];
}
$parties = implode(', ',$partyArray);

$objUnitSic = $this->getObject('dbunitsic','awardapi');
$sicData = $objUnitSic->getSicStr($unitId);
$sicInfo = implode("<br />",$sicData);

$latestAgree = $this->objAgree->getAll("WHERE unitid = '$unitId' ORDER BY implementation DESC");
$latestAgree = current($latestAgree);
$lastYear = date('Y',strtotime($latestAgree['implementation']));
$region = $this->objAgree->getArray("SELECT region.name AS name FROM tbl_award_region AS region, tbl_award_unit_region AS org
				WHERE org.unitid = '$unitId' AND region.id = org.regionid");
$region = current($region);

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('phrase_generalinfo'), "25%", null, 'left', '', '');
$objTable->addHeaderCell('', "60%", null, 'left', '', '');
$objTable->endHeaderRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('word_name'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($name, "60%", null, 'left', 'even', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_lrswages_agreement','award'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($agree_type_names, "60%", null, 'left', 'even', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('phrase_agreeparties'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($parties, "60%", null, 'left', 'even', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('phrase_indcategory'). '</b>' .':', "20%", 'top', 'left', 'odd', '');
$objTable->addCell($sicInfo, "60%", null, 'left', 'even', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('word_region'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($region['name'], "60%", null, 'left', 'even', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('mod_lrspostlogin_agreeworkers','award'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($latestAgree['workers']." ($lastYear)", "60%", null, 'left', 'even', '');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('phrase_length'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($latestAgree['length']." ($lastYear)", "60%", null, 'left', 'even', '');
$objTable->endRow();

/*$objTable->startRow();
$objTable->addCell('<b>' . $this->objLanguage->languageText('word_notes'). '</b>' .':', "20%", null, 'left', 'odd', '');
$objTable->addCell($notes, "60%", null, 'left', 'even', '');
$objTable->endRow();
*/

$ppType = $this->objDbPayPeriodType->getDefaultPPType();

$wages = $this->objDbWages->getUnitWages($unitId);
$wageTable = $this->newObject('htmltable','htmlelements');
$wageTable->cellpadding = $wageTable->cellspacing = 2;
$wageTable->addHeader(array($this->objLanguage->languageText('word_occupation'),
				$this->objLanguage->languageText('phrase_startdate'),
				//$this->objLanguage->languageText('phrase_length'),
				$this->objLanguage->languageText('phrase_wagerate')." ({$ppType['name']})",
				$this->objLanguage->languageText('phrase_increasepercentage'),
				$this->objLanguage->languageText('phrase_actualincrease')." ".$this->indexFacet->getIndexShortName(1)));
$class = 'odd';
$previous = 0;
foreach ($wages as $soc => $agree) {
	foreach ($agree as $wage) {
		if ($previous != 0) {
			$inc = round((($wage['rate'] - $previous)/$previous)*100,1)."%";
			$indexInc = $this->indexFacet->getIndexIncreasePeriod($wage['date'],$wage['months'],1);
			//echo "$indexInc<br>";
			$pinc = round($inc - $indexInc,1).'%'; 	
		} else { 
			$indexInc = $inc = $pinc = '--';
		}
		$wageTable->startRow($class);
		$wageTable->addCell($soc);
		$wageTable->addCell(date('d M Y',strtotime($wage['date'])),null,null,'center');
		$suffix = $note = '';
		if ($ppType['factor'] == 0) {
			$decimals = 2;
			$objBenefits = $this->getObject('dbbenefits','awardapi');
			$benefit = $objBenefits->getArray("SELECT value FROM tbl_award_benefits WHERE nameid = 'init_7' AND agreeid = '{$wage['agreeid']}'");
			$hoursPerWeek = current($benefit);
			if ($hoursPerWeek['value'] == 0) {
				$defaultHours = $this->objSysConfig->getValue('default_hoursperweek', 'award');
				$hoursPerWeek['value'] = $defaultHours;
				$suffix = "<span class='error'>*</span>";
				$defaultPhrase = str_replace("[HOURS]", $defaultHours, $this->objLanguage->languageText('mod_award_defaulthoursused', 'award'));
				$note = "<span class='error'>$defaultPhrase</span>";
			} 
			$wageRate = $wage['rate']/$hoursPerWeek['value'];
		} else {
			$decimals = 0;
			$wageRate = $wage['rate']*$ppType['factor'];
		}
		//$wageTable->addCell($wage['months']);
		$wageTable->addCell(number_format($wageRate, $decimals).$suffix,null,null,'center');
		$wageTable->addCell($inc,null,null,'center');
		$wageTable->addCell($pinc,null,null,'center');
		//$wageTable->addCell($indexInc);
		$wageTable->endRow();
		$previous = $wage['rate'];
	
	}
	$previous = 0;
	$class = ($class == 'even')? 'odd' : 'even';
}

$agreeList = $this->objAgree->getAll("WHERE unitid = '$unitId' ORDER BY implementation DESC");
$agree = current($agreeList);

/***************** Conditions Tab *********************************/

$benefits = "<div id='conditionsTab'>".$this->objTemplates->getAgreeConditions($agree['id'])."</div>";
$agreeSelect = new dropdown('agreeSelect');
$agreeSelect->addFromDB($agreeList,'name','id');
$agreeSelect->extra = "onchange='javascript: updateConditions(this);'";
$selector = $this->objLanguage->languageText('phrase_selectagreement').": ".$agreeSelect->show();
$conditions = $selector.$benefits;


/********************** Tabs *************************************/
$tabs = $this->getObject('tabcontent','htmlelements');
$tabs->addTab($objLanguage->languageText('phrase_wagedata'), $wageTable->show().$note, null, true);
$tabs->addTab($objLanguage->languageText('word_conditions'), $conditions);
$tabs->width = '835px';

$link = $this->getObject('link','htmlelements');
$link->link = $this->objLanguage->languageText('word_back');
$link->link($this->uri(array('action'=>'agreement','selected'=>$this->getParam('selected'),'unitId'=>$unitId)));
$backLink = $link->show();
$topBackLink = "<div align='right'>$backLink</div>";

$link->link($this->uri(array('action'=>'feedback','selected'=>$this->getParam('selected'),'buid'=>$unitId)));
$link->link = $this->objLanguage->languageText('mod_lrs_feedback','award');
$feedbackLink = $link->show();
$bottomLinks = "<div align='right'>$feedbackLink / $backLink<br /></div>";

$message = '';
$submitted = $this->getParam('submitted');
if (isset($submitted) && $submitted) {
    $message = $this->getObject('timeoutmessage','htmlelements');
    $message->setMessage($this->objLanguage->languageText('mod_lrs_submitsuccess','award'));
    $message = $message->show();
}
/*
if ($this->objUser->isAdmin()) {
    //$objFile = $this->getObject('fileupload','filestore');
    $agreements = $this->objAgree->getAll("WHERE unitId = '$unitId'");
    $agreeTable = $this->newObject('htmltable','htmlelements');
    $agreeTable->cellpadding = $agreeTable->cellspacing = '2';
    $agreeTable->width = '50%';
    $agreeTable->addHeader(array($this->objLanguage->languageText('word_agreement'),$this->objLanguage->languageText('word_documents')));
    foreach ($agreements as $agree) {
        $files = array();//$objFile->getAll("WHERE context_id = '{$agree['id']}'");
        $docLink = '';
        if (!empty($files)) {
            foreach ($files as $file) {
                $link->link($this->uri(array('action'=>'downloaddoc','fileId'=>$file['id']),'postlogin'));
                $link->link = $file['filename'];
                $docLink .= $link->show()." ";
            }
        } else {
            $docLink = '&nbsp;';
        }
        $link->link($this->uri(array('action'=>'agreementoverview','agreeId'=>$agree['id']),'lrsadmin'));
        $link->link = $agree['name'];
        
        $agreeTable->startRow();
        $agreeTable->addCell($link->show());
        $agreeTable->addCell($docLink);
        $agreeTable->endRow();
    }
    $agreeTableHTML = $agreeTable->show();
} else {*/
    $agreeTableHTML = '';
//}

$precache = "<img src='skins/_common/icons/loading_bar.gif' width=1 height=1 />";
$loadingPhrase = "<b>".$this->objLanguage->languageText('phrase_loading')."</b>";
$jsLib = $this->getResourceUri("postlogin.js");
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsLib'></script>");
echo $precache.$heading->show().$message.$topBackLink.$objTable->show().
	 "<br />$bottomLinks$agreeTableHTML<div class='boxcont'>".$tabs->show()."</div>";
?>