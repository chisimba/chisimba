<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Admin
*/

/**
* Google search template
* To enable or disable the search as well as set the values
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkBox', 'htmlelements');

$objSetGoogleForm = new form('lrsadmin', $this->uri(array('action'=>'submitgooglesettings')));

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_award_sitesettings', 'award');
$objSetGoogleForm->addToForm($header->show());

$enableGoogle = $this->objLanguage->languageText("mod_lrs_enable_Google",'award');
$googleKey = $this->objLanguage->languageText("mod_lrs_google_key", 'award');
$search = $this->objLanguage->languageText("mod_lrs_google_search", 'award');
$enable = $this->objLanguage->languageText("mod_lrs_google_enable", 'award');
$currencyAbbrev = $this->objLanguage->languageText("mod_award_currencyabreviation", 'award');
$defaultHours = $this->objLanguage->languageText("mod_award_defhoursperweek", 'award');
$defaultPayPeriod = $this->objLanguage->languageText("mod_award_defpptype", 'award');
$googleCode = $this->objLanguage->languageText("mod_award_analyticscode", 'award');

$setgoogle = ($setGoogle == '1')? true : false;

$setSearch = htmlentities($googleSearch);

$chkGoogleEnable = new checkBox('setGoogle', $enable, $setgoogle);
$txtGoogleKey = new textinput('apiKey', $apiKey,'text','90');
$txtSearch = new textinput('search', $setSearch,'text','90');
$txtCurr = new textinput('currencysymbol', $setSymbol,'text','4');
$txtHours = new textinput('hours', $setHours,'text','4');
$txtAnalytics = new textinput('analytics', $setAnalytics,'text','20');
$dropPP = new dropdown('payperiod');
$dropPP->addFromDB($payPeriods, 'name', 'id');
$dropPP->setSelected($setPeriod);

$tblSetGoogle = new htmlTable('google');
$tblSetGoogle->cellspacing = 2;

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("<i>".$this->objLanguage->languageText("mod_lrspostlogin_google_search", 'award')."</i>",'','','','','colspan="2"');
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$enableGoogle: ");
$tblSetGoogle->addCell($chkGoogleEnable->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$googleKey: ");
$tblSetGoogle->addCell($txtGoogleKey->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$search: ");
$tblSetGoogle->addCell($txtSearch->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("&nbsp;");
$tblSetGoogle->addCell("&nbsp;");
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("<i>".$this->objLanguage->languageText("mod_award_wagedefaults", 'award')."</i>",'','','','','colspan="2"');
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$currencyAbbrev: ");
$tblSetGoogle->addCell($txtCurr->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$defaultPayPeriod: ");
$tblSetGoogle->addCell($dropPP->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$defaultHours: ");
$tblSetGoogle->addCell($txtHours->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("&nbsp;");
$tblSetGoogle->addCell("&nbsp;");
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("<i>".$this->objLanguage->languageText("mod_award_googleanalytics", 'award')."</i>",'','','','','colspan="2"');
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("$googleCode: ");
$tblSetGoogle->addCell($txtAnalytics->show());
$tblSetGoogle->endRow();

$tblSetGoogle->startRow();
$tblSetGoogle->addCell("<br />");
$tblSetGoogle->addCell("<br />");
$tblSetGoogle->endRow();

$btnSubmit = new button('submit');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'admin'));
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_exit").' ');

$tblSetGoogle->startRow();
$tblSetGoogle->addCell($btnSubmit->show().'  '.$btnCancel->show());
$tblSetGoogle->addCell("<br />");
$tblSetGoogle->endRow();

$objSetGoogleForm->addToForm($tblSetGoogle->show());
echo $objSetGoogleForm->show();
?>