<?php
/**
 * ahis Passive Survaillance main screen Template
 *
 * Template for passive surveillance main capture screen
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
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_surveillance_tpl.php 13592 2009-06-02 14:00:40Z nic $
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

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('mod_ahis_diseasereport', 'openaris')." ".$this->objLanguage->languageText('word_main');
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('radio','htmlelements');

$sButton = new button('enter', $this->objLanguage->languageText('word_next'));
$sButton->setCSS('nextButton');
$sButton->setToSubmit();
$backUri = $this->uri(array('action'=>'select_officer'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('cancelButton');
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: clearPassiveSurveillance()");
$cButton->setCSS('clearButton');

$countryDrop = new dropdown('countryId');
$countryDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$countryDrop->addFromDB($arrayCountry, 'common_name', 'id');
$countryDrop->setSelected(-1);
$countryDrop->cssClass = 'passive_surveillance';
$countryDrop->extra = 'onchange="javascript:changeCountry();"';

$admin1Drop = new dropdown('partitionTypeId');
$admin1Drop->addFromDB($arrayAdmin1, 'partitioncategory', 'id');
$admin1Drop->cssClass = 'passive_surveillance';
$admin1Drop->extra = 'onchange="javascript:changePartitionType();"';

$admin2Drop = new dropdown('partitionLevelId');
$admin2Drop->addFromDB($arrayAdmin2, 'partitionlevel', 'id');
$admin2Drop->cssClass = 'passive_surveillance';
$admin2Drop->extra = 'onchange="javascript:changeNames();"';

$admin3Drop = new dropdown('partitionId');
//$admin3Drop->addFromDB($arrayAdmin3, 'partitionname', 'id');
$admin3Drop->cssClass = 'passive_surveillance';
$admin3Drop->extra = 'disabled';
$monthDrop = new dropdown('month');
for ($i=1; $i<13; $i++) {
    $monthDrop->addOption($i, date('F', mktime(0,0,0,$i)));
}
$monthDrop->setSelected(date('n'));
$monthDrop->cssClass = 'passive_surveillance';

$yearBox = new textinput('year', date('Y'), 'text', 4);
$yearBox->extra = 'onchange="javascript:changeOutbreakCode();"';

$preparedDate = $this->newObject('datepicker','htmlelements');
$preparedDate->setName('datePrepared');
$preparedDate->setDefaultDate($datePrepared);
$IBARSubDate = $this->newObject('datepicker','htmlelements');
$IBARSubDate->setName('dateIBARSub');
$IBARSubDate->setDefaultDate($dateIBARSub);
$IBARRecDate = $this->newObject('datepicker','htmlelements');
$IBARRecDate->setName('dateIBARRec');
$IBARRecDate->setDefaultDate($dateIBARRec);

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_country').": ");
$objTable->addCell($countryDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_productiontype', 'openaris').": ");
$objTable->addCell($admin1Drop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_productionlevel', 'openaris').": ");
$objTable->addCell($admin2Drop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_production', 'openaris').": ");
$objTable->addCell($admin3Drop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_month').": ");
$objTable->addCell($monthDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_year').": ");
$objTable->addCell($yearBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_dateprepared', 'openaris').": ");
$objTable->addCell($preparedDate->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_ibarsubdate', 'openaris').": ");
$objTable->addCell($IBARSubDate->show(),NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_ibarrecdate', 'openaris').": ");
$objTable->addCell($IBARRecDate->show(),NULL,'center');
$objTable->endRow();

$reportOfficerDrop = new dropdown('reportOfficerId');
$reportOfficerDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$reportOfficerDrop->addFromDB($arrayROfficer, 'name', 'userid');
//$reportOfficerDrop->setSelected($reportOfficerId);
$reportOfficerDrop->cssClass = 'passive_surveillance';
$reportOfficerDrop->extra = 'onchange = \'javascript:getOfficerInfo("report");\'';
$reportOfficerFaxBox = new textinput('reportOfficerFax', $reportOfficerFax, 'text');
$reportOfficerFaxBox->setCss('passive_surveillance');
$reportOfficerTelBox = new textinput('reportOfficerTel', $reportOfficerTel, 'text');
$reportOfficerTelBox->setCss('passive_surveillance');
$reportOfficerEmailBox = new textinput('reportOfficerEmail', $reportOfficerEmail, 'text');
$reportOfficerEmailBox->setCss('passive_surveillance');
$reportOfficerFaxBox->extra =
    $reportOfficerTelBox->extra =
    $reportOfficerEmailBox->extra = 'disabled';

$dataEntryOfficerDrop = new dropdown('dataEntryOfficerId');
$dataEntryOfficerDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$dataEntryOfficerDrop->addFromDB($arrayDEOfficer, 'name', 'userid');
//$dataEntryOfficerDrop->setSelected($dataEntryOfficerId);
$dataEntryOfficerDrop->cssClass = 'passive_surveillance';
$dataEntryOfficerDrop->extra = 'onchange = \'javascript:getOfficerInfo("dataEntry");\'';
$dataEntryOfficerFaxBox = new textinput('dataEntryOfficerFax', $dataEntryOfficerFax, 'text');
$dataEntryOfficerFaxBox->setCss('passive_surveillance');
$dataEntryOfficerTelBox = new textinput('dataEntryOfficerTel', $dataEntryOfficerTel, 'text');
$dataEntryOfficerTelBox->setCss('passive_surveillance');
$dataEntryOfficerEmailBox = new textinput('dataEntryOfficerEmail', $dataEntryOfficerEmail, 'text');
$dataEntryOfficerEmailBox->setCss('passive_surveillance');
$dataEntryOfficerFaxBox->extra =
    $dataEntryOfficerTelBox->extra =
    $dataEntryOfficerEmailBox->extra = 'disabled';

$valOfficerDrop = new dropdown('valOfficerId');
$valOfficerDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$valOfficerDrop->addFromDB($arrayVOfficer, 'name', 'userid');
//$valOfficerDrop->setSelected($valOfficerId);
$valOfficerDrop->cssClass = 'passive_surveillance';
$valOfficerDrop->extra = 'onchange = \'javascript:getOfficerInfo("val");\'';
$valOfficerFaxBox = new textinput('valOfficerFax', $valOfficerFax, 'text');
$valOfficerFaxBox->setCss('passive_surveillance');
$valOfficerTelBox = new textinput('valOfficerTel', $valOfficerTel, 'text');
$valOfficerTelBox->setCss('passive_surveillance');
$valOfficerEmailBox = new textinput('valOfficerEmail', $valOfficerEmail, 'text');
$valOfficerEmailBox->setCss('passive_surveillance');
$valOfficerFaxBox->extra =
    $valOfficerTelBox->extra =
    $valOfficerEmailBox->extra = 'disabled';

$objOfficerTable = $this->newObject('htmltable','htmlelements');
$objOfficerTable->cellspacing = 2;
$objOfficerTable->width = NULL;
$objOfficerTable->startRow();
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_reportofficer', 'openaris').": ");
$objOfficerTable->addCell($reportOfficerDrop->show(),NULL,'center');
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_fax', 'openaris').": ");
$objOfficerTable->addCell($reportOfficerFaxBox->show(),NULL,'center');
$objOfficerTable->endRow();
$objOfficerTable->startRow();
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_phone', 'openaris').": ");
$objOfficerTable->addCell($reportOfficerTelBox->show(),NULL,'center');
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_email', 'openaris').": ");
$objOfficerTable->addCell($reportOfficerEmailBox->show(),NULL,'center');
$objOfficerTable->endRow();
$objOfficerTable->startRow();
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_dataofficer', 'openaris').": ");
$objOfficerTable->addCell($dataEntryOfficerDrop->show(),NULL,'center');
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_fax', 'openaris').": ");
$objOfficerTable->addCell($dataEntryOfficerFaxBox->show(),NULL,'center');
$objOfficerTable->endRow();
$objOfficerTable->startRow();
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_phone', 'openaris').": ");
$objOfficerTable->addCell($dataEntryOfficerTelBox->show(),NULL,'center');
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_email', 'openaris').": ");
$objOfficerTable->addCell($dataEntryOfficerEmailBox->show(),NULL,'center');
$objOfficerTable->endRow();
$objOfficerTable->startRow();
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_valofficer', 'openaris').": ");
$objOfficerTable->addCell($valOfficerDrop->show(),NULL,'center');
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_fax', 'openaris').": ");
$objOfficerTable->addCell($valOfficerFaxBox->show(),NULL,'center');
$objOfficerTable->endRow();
$objOfficerTable->startRow();
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_phone', 'openaris').": ");
$objOfficerTable->addCell($valOfficerTelBox->show(),NULL,'center');
$objOfficerTable->addCell($this->objLanguage->languageText('mod_ahis_word_email', 'openaris').": ");
$objOfficerTable->addCell($valOfficerEmailBox->show(),NULL,'center');
$objOfficerTable->endRow();

$officerSet = $this->newObject('fieldset', 'htmlelements');
$officerSet->addContent($objOfficerTable->show());
$officerSet->setLegend($this->objLanguage->languageText('mod_ahis_officerinfo', 'openaris'));

$outbreakRadio = new radio('outbreak');
$outbreakRadio->addOption(1, $this->objLanguage->languageText('word_yes')." ");
$outbreakRadio->addOption(0, $this->objLanguage->languageText('word_no'));
//$outbreakRadio->setSelected($outbreakReported);
$outbreakRadio->extra = 'onclick="javascript:toggleOutbreak(this.value);"';

$validatedRadio = new radio('validated');
$validatedRadio->addOption(1, $this->objLanguage->languageText('word_yes')." ");
$validatedRadio->addOption(0, $this->objLanguage->languageText('word_no'));
//$validatedRadio->setSelected($validated);

$commentBox = new textarea('comment', $comment, 4, 42);
$commentBox->extra = 'onkeyup="javascript:limitcomment();"';


$radioTable = $this->newObject('htmltable', 'htmlelements');
$radioTable->cellspacing = 2;
$radioTable->width = NULL;
$radioTable->startRow();
$radioTable->addCell($this->objLanguage->languageText('mod_ahis_outbreaktoreport', 'openaris')."&nbsp ");
$radioTable->addCell($outbreakRadio->show());
$radioTable->endRow();
$radioTable->startRow();
$radioTable->addCell($this->objLanguage->languageText('mod_ahis_validated', 'openaris')." ");
$radioTable->addCell($validatedRadio->show());
$radioTable->endRow();
$radioTable->startRow();
$radioTable->addCell($this->objLanguage->languageText('word_comments')." ");
$radioTable->addCell($commentBox->show());
$radioTable->endRow();

$objTopTable = $this->newObject('htmltable','htmlelements');
$objTopTable->cellspacing = 2;
$objTopTable->width = NULL;
$objTopTable->startRow();
$objTopTable->addCell($objTable->show(), NULL, 'top', NULL, 'layout');
$objTopTable->addCell($officerSet->show().$radioTable->show(), NULL, 'top', NULL, 'layout');
$objTopTable->endRow();

$reportTypeDrop = new dropdown('reportTypeId');
$reportTypeDrop->addOption('0', $this->objLanguage->languageText('word_new'));
$reportTypeDrop->addOption('1', $this->objLanguage->languageText('phrase_followup'));
//$reportTypeDrop->setSelected($reportTypeId);
$reportTypeDrop->cssClass = 'passive_surveillance';
$reportTypeDrop->extra = 'onchange="javascript:toggleReportType();"';

$outbreakDrop = new dropdown('outbreakId');
//$outbreakDrop->addFromDB($arrayOutbreak, 'outbreakcode', 'id');
//$outbreakDrop->setSelected($outbreakCode);
$outbreakDrop->cssClass = 'passive_surveillance';
$outbreakDrop->extra = 'style="display: none;"';

$outbreakBox = new textinput('outbreakCode');
$outbreakBox->setCss('passive_surveillance');
$outbreakBox->extra = 'readonly';

$diseaseDrop = new dropdown('diseaseId');
$diseaseDrop->addFromDB($arrayDisease, 'short_name', 'id');
//$diseaseDrop->setSelected($diseaseId);
$diseaseDrop->cssClass = 'passive_surveillance';
$diseaseDrop->extra = 'onchange="javascript:changeOutbreakCode();"';

$occurenceDrop = new dropdown('occurenceId');
$occurenceDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$occurenceDrop->addFromDB($arrayOccurence, 'occurencecode', 'id');
//$occurenceDrop->setSelected($occurenceId);
$occurenceDrop->cssClass = 'passive_surveillance';

$infectionDrop = new dropdown('infectionId');
$infectionDrop->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$infectionDrop->addFromDB($arrayInfection, 'possiblesource', 'id');
//$infectionDrop->setSelected($infectionId);
$infectionDrop->cssClass = 'passive_surveillance';

$diseaseTable = $this->newObject('htmltable','htmlelements');
$diseaseTable->cellspacing = 2;
$diseaseTable->startRow();
$diseaseTable->addCell($this->objLanguage->languageText('phrase_report'));
$diseaseTable->addCell($reportTypeDrop->show());
$diseaseTable->endRow();
$diseaseTable->startRow();
$diseaseTable->addCell($this->objLanguage->languageText('mod_ahis_outbreakno', 'openaris').": ");
$diseaseTable->addCell($outbreakDrop->show().$outbreakBox->show());
$diseaseTable->endRow();
$diseaseTable->startRow();
$diseaseTable->addCell($this->objLanguage->languageText('word_disease').": ");
$diseaseTable->addCell($diseaseDrop->show());
$diseaseTable->endRow();
$diseaseTable->startRow();
$diseaseTable->addCell($this->objLanguage->languageText('mod_ahis_occurence', 'openaris').": ");
$diseaseTable->addCell($occurenceDrop->show());
$diseaseTable->endRow();
$diseaseTable->startRow();
$diseaseTable->addCell($this->objLanguage->languageText('mod_ahis_infectionsource', 'openaris').": ");
$diseaseTable->addCell($infectionDrop->show());
$diseaseTable->endRow();

$diseaseSet = $this->newObject('fieldset', 'htmlelements');
$diseaseSet->addContent($diseaseTable->show());
$diseaseSet->setLegend($this->objLanguage->languageText('mod_ahis_diseaseinfo', 'openaris'));

$observationDatePick = $this->newObject('datepicker', 'htmlelements');
$observationDatePick->setName('observationDate');
$observationDatePick->setDefaultDate($observationDate);
$vetDatePick = $this->newObject('datepicker','htmlelements');
$vetDatePick->setName('vetDate');
$vetDatePick->setDefaultDate($vetDate);
$investigationDatePick = $this->newObject('datepicker','htmlelements');
$investigationDatePick->setName('investigationDate');
$investigationDatePick->setDefaultDate($investigationDate);
$sampleDatePick = $this->newObject('datepicker','htmlelements');
$sampleDatePick->setName('sampleDate');
$sampleDatePick->setDefaultDate($sampleDate);
$diagnosisDatePick = $this->newObject('datepicker','htmlelements');
$diagnosisDatePick->setName('diagnosisDate');
$diagnosisDatePick->setDefaultDate($diagnosisDate);
$interventionDatePick = $this->newObject('datepicker','htmlelements');
$interventionDatePick->setName('interventionDate');
$interventionDatePick->setDefaultDate($interventionDate);

$periodTable = $this->newObject('htmltable','htmlelements');
$periodTable->cellspacing = 2;
$periodTable->width = NULL;
$periodTable->startRow();
$periodTable->addCell($this->objLanguage->languageText('mod_ahis_observationdate', 'openaris').': ', NULL, 'middle');
$periodTable->addCell($observationDatePick->show(), NULL, 'center');
$periodTable->endRow();
$periodTable->startRow();
$periodTable->addCell($this->objLanguage->languageText('mod_ahis_vetdate', 'openaris').': ', NULL, 'center');
$periodTable->addCell($vetDatePick->show(), NULL, 'center');
$periodTable->endRow();
$periodTable->startRow();
$periodTable->addCell($this->objLanguage->languageText('mod_ahis_investigationdate', 'openaris').': ', NULL, 'center');
$periodTable->addCell($investigationDatePick->show(), NULL, 'center');
$periodTable->endRow();
$periodTable->startRow();
$periodTable->addCell($this->objLanguage->languageText('mod_ahis_sampledate', 'openaris').': ', NULL, 'center');
$periodTable->addCell($sampleDatePick->show(), NULL, 'center');
$periodTable->endRow();
$periodTable->startRow();
$periodTable->addCell($this->objLanguage->languageText('mod_ahis_diagnosisdate', 'openaris').': ', NULL, 'center');
$periodTable->addCell($diagnosisDatePick->show(), NULL, 'center');
$periodTable->endRow();
$periodTable->startRow();
$periodTable->addCell($this->objLanguage->languageText('mod_ahis_interventiondate', 'openaris').': ', NULL, 'center');
$periodTable->addCell($interventionDatePick->show(), NULL, 'center');
$periodTable->endRow();

$periodSet = $this->newObject('fieldset', 'htmlelements');
$periodSet->addContent($periodTable->show());
$periodSet->setLegend($this->objLanguage->languageText('mod_ahis_periodinfo', 'openaris'));

$createdBox = new textinput('createdBy', $createdBy, 'text');
$createdBox->setCss('passive_surveillance');
$createdDateBox = new textinput('createdDate', $createdDate, 'text');
$createdDateBox->setCss('passive_surveillance');
$modifiedBox = new textinput('modifiedBy', $modifiedBy, 'text');
$modifiedBox->setCss('passive_surveillance');
$modifiedDateBox = new textinput('modifiedDate', $modifiedDate, 'text');
$modifiedDateBox->setCss('passive_surveillance');
$createdBox->extra = $createdDateBox->extra = $modifiedBox->extra = $modifiedDateBox->extra = 'disabled';

$createdTable = $this->newObject('htmltable','htmlelements');
$createdTable->cellspacing = 2;
$createdTable->startRow();
$createdTable->addCell($this->objLanguage->languageText('word_createdby').': ');
$createdTable->addCell($createdBox->show());
$createdTable->endRow();
$createdTable->startRow();
$createdTable->addCell($this->objLanguage->languageText('phrase_createddate').': ');
$createdTable->addCell($createdDateBox->show());
$createdTable->endRow();
$createdTable->startRow();
$createdTable->addCell($this->objLanguage->languageText('word_modifiedby').': ');
$createdTable->addCell($modifiedDateBox->show());
$createdTable->endRow();
$createdTable->startRow();
$createdTable->addCell($this->objLanguage->languageText('phrase_modifieddate').': ');
$createdTable->addCell($modifiedBox->show());
$createdTable->endRow();

$objBottomTable = $this->newObject('htmltable','htmlelements');
$objBottomTable->cellspacing = 2;
$objBottomTable->startRow();
$objBottomTable->addCell($diseaseSet->show(), NULL, 'top', NULL, 'layout');
$objBottomTable->addCell($periodSet->show(), NULL, 'top', NULL, 'layout');
$objBottomTable->addCell($createdTable->show());
$objBottomTable->endRow();

$objButtonTable = $this->newObject('htmltable','htmlelements');
$objButtonTable->cellspacing = 2;
$objButtonTable->width = '99%';
$objButtonTable->startRow();
$objButtonTable->addCell($bButton->show(), NULL, 'top', 'center');
$objButtonTable->addCell($sButton->show(), NULL, 'top', 'center');
$objButtonTable->endRow();

$content = $objTopTable->show()."<hr />".$objBottomTable->show().$objButtonTable->show();

$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $this->uri(array('action' => 'save_disease_1')));
$objForm->addToForm($content);
$objForm->addRule('countryId', $this->objLanguage->languageText('mod_ahis_valcountry', 'openaris'), 'select');
$objForm->addRule('datePrepared', $this->objLanguage->languageText('mod_ahis_valdateprepared', 'openaris'), 'datenotfuture');
$objForm->addRule('dateIBARSub', $this->objLanguage->languageText('mod_ahis_valdateibar', 'openaris'), 'datenotfuture');
$objForm->addRule(array('datePrepared','dateIBARSub'), $this->objLanguage->languageText('mod_ahis_valdateibarafterprepared', 'openaris'), 'datenotbefore');
$objForm->addRule('dateIBARRec', $this->objLanguage->languageText('mod_ahis_valdateibarsub', 'openaris'), 'datenotfuture');
$objForm->addRule(array('dateIBARSub', 'dateIBARRec'), $this->objLanguage->languageText('mod_ahis_valdateibarafteribar', 'openaris'), 'datenotbefore');
$objForm->addRule(array('month'=>'month','year'=>'year'), $this->objLanguage->languageText('mod_ahis_valdate', 'openaris'), 'twofielddate');
$objForm->addRule('reportOfficerId', $this->objLanguage->languageText('mod_ahis_valreportofficer', 'openaris'), 'select');
$objForm->addRule('dataEntryOfficerId', $this->objLanguage->languageText('mod_ahis_valentryofficer', 'openaris'), 'select');
$objForm->addRule('valOfficerId', $this->objLanguage->languageText('mod_ahis_valvalidationofficer', 'openaris'), 'select');
$objForm->addRule('outbreak', $this->objLanguage->languageText('mod_ahis_valoutbreak', 'openaris'), 'required');
$objForm->addRule('validated', $this->objLanguage->languageText('mod_ahis_valvalidated', 'openaris'), 'required');

$objForm->addRule('observationDate', $this->objLanguage->languageText('mod_ahis_valobservationdate', 'openaris'), 'datenotfuture');
$objForm->addRule('vetDate', $this->objLanguage->languageText('mod_ahis_valvetdate', 'openaris'), 'datenotfuture');
$objForm->addRule(array('observationDate', 'vetDate'), $this->objLanguage->languageText('mod_ahis_valvetafter', 'openaris'), 'datenotbefore');
$objForm->addRule('investigationDate', $this->objLanguage->languageText('mod_ahis_valinvestigationdate', 'openaris'), 'datenotfuture');
$objForm->addRule(array('vetDate', 'investigationDate'), $this->objLanguage->languageText('mod_ahis_valinvestigationafter', 'openaris'), 'datenotbefore');
$objForm->addRule('sampleDate', $this->objLanguage->languageText('mod_ahis_valsampledate', 'openaris'), 'datenotfuture');
$objForm->addRule(array('investigationDate', 'sampleDate'), $this->objLanguage->languageText('mod_ahis_valsampleafter', 'openaris'), 'datenotbefore');
$objForm->addRule('diagnosisDate', $this->objLanguage->languageText('mod_ahis_valdiagnosisdate', 'openaris'), 'datenotfuture');
$objForm->addRule(array('sampleDate', 'diagnosisDate'), $this->objLanguage->languageText('mod_ahis_valdiagnosisafter', 'openaris'), 'datenotbefore');
$objForm->addRule('interventionDate', $this->objLanguage->languageText('mod_ahis_valinterventiondate', 'openaris'), 'datenotfuture');
$objForm->addRule(array('diagnosisDate', 'interventionDate'), $this->objLanguage->languageText('mod_ahis_valinterventionafter', 'openaris'), 'datenotbefore');

$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");

echo $objHeading->show()."<br />".$objForm->show();