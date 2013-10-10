<?php
/**
 * ahis Add Animal Population
 *
 * File containing the Add Animal Population template
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Patrick Kuti <pkuti@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: animal_population_tpl.php 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
$title = $this->objLanguage->languageText('mod_ahis_animalpopulation1','openaris');
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('label', 'htmlelements');

$formAction = 'animal_population_save';  
$buttonText = 'Save';

$sButton = new button('enter', $this->objLanguage->languageText('word_next'));
$sButton->setCSS('nextButton');
$sButton->setToSubmit();
$backUri = $this->uri(array('action'=>'select_officer'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('cancelButton');
//clear button
$cButton = $this->uri(array('action'=>'animal_population_clear'));
//$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: clearAnimalPopulation()");
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: document.location=''");
$cButton->setCSS('clearButton');

//buttons

$backUri = $this->uri(array('action'=>'select_officer'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$classDrop = new dropdown('classification');
$classDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$classDrop->addFromDB($arrayspecies, 'speciesname', 'id');
$classDrop->setSelected($species); 
$classDrop->extra = 'onchange="javascript:changeBreed();"';


$breedDrop = new dropdown('breedId');
$breedDrop->addOption('null', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$breedDrop->addFromDB($arraybreed, 'name', 'id');
$breedDrop->setSelected($breed);

//drop down for country
$countryDrop = new dropdown('countryId');
$countryDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$countryDrop->addFromDB($arrayCountry, 'common_name', 'id');
$countryDrop->setSelected($count);
$countryDrop->cssClass = 'animal_population_add';
$countryDrop->extra = 'onchange="javascript:changeNames();"';

$admin1Drop = new dropdown('partitionTypeId');
$admin1Drop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$admin1Drop->addFromDB($arrayAdmin1, 'partitioncategory', 'id');
$admin1Drop->setSelected($ptype);
$admin1Drop->extra = 'onchange="javascript:changePartitionType();"';



$partitionLDrop = new dropdown('partitionLevelId');
$partitionLDrop ->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$partitionLDrop->addFromDB($arrayAdmin2, 'partitionlevel', 'id');
$partitionLDrop->setSelected($plevel);
$partitionLDrop->extra = 'onchange="javascript:changeNames();"';

$partitionNDrop = new dropdown('partitionId');
$partitionNDrop ->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$partitionNDrop->addFromDB($arrayAdmin3, 'partitionname', 'id');
$partitionNDrop->setSelected($pname);

//create year dropdown
$year = date('Y',strtotime($yearBox));
$yearBox = new dropdown('year');

for($i=$year;$i>=$year-10;$i--){
$date = strtotime("01-01-$i");
$yearBox->addOption(date('y',$date),date('Y',$date));
}
$yearBox->setSelected($dyear);

$repDate = $this->newObject('datepicker','htmlelements');
$repDate->setName('rDate');
$repDate->setDefaultDate($rDate);

$ibarDate=$this->newObject('datepicker', 'htmlelements');
$ibarDate->setName('iDate');
$ibarDate->setDefaultDate($iDate);


$reportOfficerDrop = new dropdown('repOfficerId');
$reportOfficerDrop->addOption('-1',$this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$reportOfficerDrop->addFromDB($arrayrepoff, 'name', 'userid');
$reportOfficerDrop->setSelected($repoff);
$reportOfficerDrop->extra = 'onchange = \'javascript:getOfficerInfo("rep");\'';
//Data entry officer
$dataEntryOfficerDrop = new dropdown('dataOfficerId');
$dataEntryOfficerDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$dataEntryOfficerDrop->addFromDB($arraydataoff, 'name', 'userid');
$dataEntryOfficerDrop->setSelected($dataoff);
$dataEntryOfficerDrop->extra = 'onchange = \'javascript:getOfficerInfo("data");\'';
//Vet officer
$valOfficerDrop = new dropdown('vetOfficerId');
$valOfficerDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$valOfficerDrop->addFromDB($arrayvetoff, 'name', 'userid');
$valOfficerDrop->setSelected($vetoff);
$valOfficerDrop->extra = 'onchange = \'javascript:getOfficerInfo("vet");\'';


$rphone = new textinput('repOfficerTel', $rphone);
$rphone->extra = 'disabled';
$rfax = new textinput('repOfficerFax', $rfax);
$rfax->extra = 'disabled';
$remail = new textinput('repOfficerEmail', $remail);
$remail->extra = 'disabled';

$dphone = new textinput('dataOfficerTel', $dphone);
$dphone->extra = 'disabled';
$dfax = new textinput('dataOfficerFax', $dfax);
$dfax->extra = 'disabled';
$demail = new textinput('dataOfficerEmail', $demail);
$demail->extra = 'disabled';

$vphone = new textinput('vetOfficerTel', $vphone);
$vphone->extra = 'disabled';
$vfax = new textinput('vetOfficerFax', $vfax);
$vfax->extra = 'disabled';
$vemail = new textinput('vetOfficerEmail', $vemail);
$vemail->extra = 'disabled';


$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
$tabs = $tab.$tab.$tab;
 
$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;

//Reporting Date 
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportdate','openaris').$tab,NULL,'centre');
$objTable->addCell($repDate->show());
$objTable->endRow();

//IBAR date
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_ibarrecdate','openaris').$tab);
$objTable->addCell($ibarDate->show());
$objTable->endRow();

$objTable2 = $this->newObject('htmltable', 'htmlelements');
$objTable2->cellspacing = 2;
$objTable2->width = NULL;

//Reporting Officer
$objTable2->startRow();
$objTable2->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris').": "."&nbsp;");
$objTable2->addCell($reportOfficerDrop->show());

$objTable2->startRow();
$objTable2->addCell($this->objLanguage->languageText('mod_ahis_phone', 'openaris').": ");
$objTable2->addCell($rphone->show());
$objTable2->addCell($tab.$this->objLanguage->languageText('mod_ahis_faxn','openaris').": ");
$objTable2->addCell($rfax->show());
$objTable2->addCell($tab.$this->objLanguage->languageText('mod_ahis_email','openaris').": ");
$objTable2->addCell($remail->show());
$objTable2->endRow();

//Data entry officer
$objTable2->startRow();
$objTable2->addCell($this->objLanguage->languageText('mod_ahis_entryofficer','openaris').": "."&nbsp;");
$objTable2->addCell($dataEntryOfficerDrop->show());
$objTable2->endRow();

$objTable2->startRow();
$objTable2->addCell($this->objLanguage->languageText('mod_ahis_phone', 'openaris').": ");
$objTable2->addCell($dphone->show());
$objTable2->addCell($tab.$this->objLanguage->languageText('mod_ahis_faxn','openaris').": ");
$objTable2->addCell($dfax->show());
$objTable2->addCell($tab.$this->objLanguage->languageText('mod_ahis_email','openaris').": ");
$objTable2->addCell($demail->show());
$objTable2->endRow();

//vet officer
$objTable2->startRow();
$objTable2->addCell($this->objLanguage->languageText('mod_ahis_vofficer','openaris').": ".$tabs);
$objTable2->addCell($valOfficerDrop->show());
$objTable2->endRow();

$objTable2->startRow();
$objTable2->addCell($this->objLanguage->languageText('mod_ahis_phone', 'openaris').": ");
$objTable2->addCell($vphone->show());
$objTable2->addCell($tab.$this->objLanguage->languageText('mod_ahis_faxn','openaris').": ");
$objTable2->addCell($vfax->show());
$objTable2->addCell($tab.$this->objLanguage->languageText('mod_ahis_email','openaris').": ");
$objTable2->addCell($vemail->show());
$objTable2->endRow();

$objTable3 = $this->newObject('htmltable', 'htmlelements');
$objTable3->cellspacing = 2;
$objTable3->width = NULL;

$objTable3->startRow();
$objTable3->addCell($this->objLanguage->languageText('word_country').": ");
$objTable3->addCell($tab.$countryDrop->show(),NULL,'center');
$objTable3->addCell($tab.$this->objLanguage->languageText('mod_ahis_productiontype', 'openaris'));
$objTable3->addCell($tab.$admin1Drop->show(),NULL,'center');
$objTable3->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_species', 'openaris'));
$objTable3->addCell($tab.$classDrop->show());
$objTable3->endRow();

$objTable3->startRow();
$objTable3->addCell($this->objLanguage->languageText('word_year').": ");
$objTable3->addCell($tab.$yearBox->show(),NULL,'center');
$objTable3->addCell($tab.$this->objLanguage->languageText('mod_ahis_partitionlevel', 'openaris'));
$objTable3->addCell($tab.$partitionLDrop->show(),NULL,'center');
$objTable3->addCell($tab.$this->objLanguage->languageText('word_breed'));
$objTable3->addCell($tab.$breedDrop->show(),NULL,'center');
$objTable3->endRow();

$objTable3->startRow();
$objTable3->addCell('&nbsp;');
$objTable3->addCell('&nbsp;');
$objTable3->addCell($tab.$this->objLanguage->languageText('mod_ahis_partitionname', 'openaris'));
$objTable3->addCell($tab.$partitionNDrop->show(),NULL,'center');
//animal production
$objTable3->addCell($tab.$this->objLanguage->LanguageText('mod_ahis_prodname','openaris'));
$production = new textinput('animal_production',$prodname);
$objTable3->addCell($tab.$production->show());
$objTable3->endRow();	

$objTable3->startRow();
$objTable3->addCell("<br />".$bButton->show().$tabs.$cButton->show().$tabs.$sButton->show(), NULL, 'top', 'center', NULL, 'colspan="6"');
$objTable3->endRow();

// Create Form
$content=$objTable->show()."<hr />".$objTable2->show()."<hr />".$objTable3->show();
$form = new form ('add', $this->uri(array('action'=>'animal_population1')));
$form->addToForm($content);
$form->addRule('repOfficerId', $this->objLanguage->languageText('mod_ahis_valreportofficer', 'openaris'), 'select');
$form->addRule('dataOfficerId', $this->objLanguage->languageText('mod_ahis_valentryofficer', 'openaris'), 'select');
//$form->addRule('vetOfficerId', $this->objLanguage->languageText('mod_ahis_valvalidationofficer', 'openaris'), 'select');
$form->addRule('year', $this->objLanguage->languageText('mod_ahis_promptyear', 'openaris'), 'required');
$form->addRule('rDate', $this->objLanguage->languageText('mod_ahis_valdateprepared', 'openaris'), 'datenotfuture');
$form->addRule('iDate', $this->objLanguage->languageText('mod_ahis_valdateibar', 'openaris'), 'datenotfuture');
$form->addRule(array('rDate','iDate'), $this->objLanguage->languageText('mod_ahis_valdateibarafterprepared', 'openaris'), 'datenotbefore');
$form->addRule('countryId', $this->objLanguage->languageText('mod_ahis_valcountry', 'openaris'), 'select');
$form->addRule('partitionTypeId', $this->objLanguage->languageText('mod_ahis_valparttype', 'openaris'), 'select');
$form->addRule('classification', $this->objLanguage->languageText('mod_ahis_valspecies', 'openaris'), 'select');
$form->addRule('breedId', $this->objLanguage->languageText('mod_ahis_valbreed', 'openaris'), 'select');
$form->addRule('animal_production', $this->objLanguage->languageText('mod_ahis_valprodname', 'openaris'), 'required');

$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());

echo $objLayer->show();
?>