<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Index Management
*/

//Create an instance of the geticon class
$objIcon = $this->newObject('geticon', 'htmlelements');
$this->loadClass('link','htmlelements');

/*********************************** DATA ADMIN ******************************/

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_wageadmin','award');
$objIcon->setModuleIcon('lrsagreements');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'start', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable = $this->newObject('htmltable','htmlelements');
$dataTable->startRow();
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_indexadmin','award');
$objIcon->setModuleIcon('lrsindex');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'startindex', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_trade_union_admin','award');
$objIcon->setModuleIcon('lrsorg');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewtradeunion', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());
$dataTable->endRow();

$dataTable->startRow();
$linkText = $this->objLanguage->languageText('mod_lrspostlogin_sicadmin','award');
$objIcon->setModuleIcon('lrssic');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'selectmajordiv', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_socadmin','award');
$objIcon->setModuleIcon('lrssoc');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'selectmajorgroup', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_decentwork_admin','award');
$objIcon->setModuleIcon('worksheetadmin');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'decentworkadmin', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());
$dataTable->endRow();

/*$dataTable->startRow();
$linkText = $this->objLanguage->languageText('mod_lrspostlogin_grade','award');
$objIcon->setModuleIcon('order');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewgrades'),'lrssoc'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_jobcode','award');
$objIcon->setModuleIcon('lrsuserreg');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewjobcodes'),'lrssoc'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());
$dataTable->endRow();*/
//('mod_lrspostlogin_wageadmin','award')
$dataTable->startRow();
$linkText = $this->objLanguage->languageText('mod_lrspostlogin_gender_admin','award');
$objIcon->setModuleIcon('buddies');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewgender', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrspostlogin_benefit_admin','award');
$objIcon->setModuleIcon('lrsbenefittypes');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewbenefittype', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_award_dataexport','award');
$objIcon->setModuleIcon('cache');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'export', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$dataTable->addCell($objIcon->show().' '.$link->show());
$dataTable->endRow();

$dataAdmin = $dataTable->show();

/***************************** SITE ADMIN ******************************/

$siteTable = $this->newObject('htmltable','htmlelements');

$siteTable->startRow();
$linkText = $this->objLanguage->languageText('mod_lrs_useradmin','award');
$objIcon->setModuleIcon('lrscontacts');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewuserlist', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$siteTable->addCell($objIcon->show().' '.$link->show());

/*$linkText = $this->objLanguage->languageText('mod_lrs_newsadmin','award');
$objIcon->setModuleIcon('cmsadmin');
$objIcon->alt = $linkText;
//$link = new link($this->uri(array(),'cmsadmin'));
$link->link = $linkText;
//$siteTable->addCell($objIcon->show().' '.$link->show());*/

$linkText = $this->objLanguage->languageText('mod_lrs_newsadmin','award');
$objIcon->setModuleIcon('storycategoryadmin');
$objIcon->alt = $linkText;
$link = new link($this->uri(array(),'stories'));
$link->link = $linkText;
$siteTable->addCell($objIcon->show().' '.$link->show());

$linkText = $this->objLanguage->languageText('mod_lrs_menuadmin','award');
$objIcon->setIcon('options');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'editmenu', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$siteTable->addCell($objIcon->show().' '.$link->show());
$siteTable->endRow();

$siteTable->startRow();
$linkText = $this->objLanguage->languageText('mod_award_sitesettings','award');
$objIcon->setModuleIcon('gmaps');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'setgooglesearch', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$siteTable->addCell($objIcon->show().' '.$link->show());

/*$linkText = $this->objLanguage->languageText('mod_lrspostlogin_blurb_admin','award');
$objIcon->setModuleIcon('languagetext');
$objIcon->alt = $linkText;
$link = new link($this->uri(array('action'=>'viewblurb', 'selected'=>'init_10'),'award'));
$link->link = $linkText;
$siteTable->addCell($objIcon->show().' '.$link->show());
$siteTable->endRow();*/

$siteAdmin = $siteTable->show();

/******************************************* TABS SETUP ************************************************/
//Create header for home tab
//Create htmlheading for page header
$objH = $this->getObject('htmlheading', 'htmlelements');
$objH->type = '2';
//Get user name
$userFullname = $this->objUser->fullname();
//Set the header string
$objH->str = $this->objLanguage->code2Txt('mod_lrspostlogin_welcome','award', array('USERFULLNAME'=>$userFullname));

//$adminTabs = new multitabbedbox();
$adminTabs = $this->newObject('tabcontent','htmlelements');
$adminTabs->addTab($this->objLanguage->languageText('mod_lrspostlogin_dataadmin','award'),$dataAdmin);
$adminTabs->addTab($this->objLanguage->languageText('mod_lrspostlogin_siteadmin','award'),$siteAdmin);
$adminTabs->width = '835px';

echo $objH->show().$adminTabs->show();

?>