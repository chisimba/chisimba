<?php
/**
 * ahis Passive Surveillance Outbreak Template
 *
 * Template for capturing passive surveillance outbreak data
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
 * @version   $Id: passive_outbreak_tpl.php 13733 2009-06-23 11:04:26Z nic $
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
$objHeading->str = $this->objLanguage->languageText('mod_ahis_diseasereport', 'openaris')." #5";
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('fieldset','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('link', 'htmlelements');

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objTableArea1 = $this->getObject('htmltable','htmlelements');
$objTableArea1->cellspacing = 2;
$objTableArea1->width = NULL;
$objTableArea1->cssClass = 'areatable';

$objTableArea1->startHeaderRow();
$objTableArea1->addHeaderCell($this->objLanguage->languageText('mod_ahis_outbreakcode', 'openaris'), NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('phrase_partitiontype'), NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('mod_ahis_partitionlevel', 'openaris'), NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('mod_ahis_partitionname', 'openaris'), NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('word_month'), NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('word_year'), NULL, NULL, 'center');
$objTableArea1->endHeaderRow();

foreach ($outbreaks as $outbreak) {
    $objTableArea1->startRow();
    $outbreakcode= $outbreak['outbreakCode'];
    $LinkUri = $this->uri(array('action'=>'disease_report_screen_5','outbreakCode1'=>$outbreakcode));

    $objLink = new link($LinkUri);
    $objLink->link = $outbreak['outbreakCode'];
    $objTableArea1->addCell($objLink->show(), NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['partitionType'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['partitionLevel'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['partitionName'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['month'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['year'], NULL, NULL, 'center');
    $objTableArea1->endRow();
}

$outbreakCodeBox = new textinput('outbreakCode', $outbreakCode);
$outbreakCodeBox->extra = 'readonly';
$outbreakCodeBox->setCss('passive_surveillance outbreakcode');

$controlDrop = new dropdown('controlId');
$controlDrop->addFromDB($arrayControlMeasure, 'controlmeasure', 'id');
$controlDrop->setSelected($controlId);
$controlDrop->cssClass = 'passive_surveillance';

$otherControlDrop = new dropdown('otherControlId');
$otherControlDrop->addFromDB($arrayOtherMeasure, 'control_measure', 'id');
$otherControlDrop->setSelected($otherId);
$otherControlDrop->cssClass = 'passive_surveillance';

$createdBox = new textinput('createdBy', $createdBy, 'text');
$createdBox->setCss('passive_surveillance');
$createdDateBox = new textinput('createdDate', $createdDate, 'text');
$createdDateBox->setCss('passive_surveillance');
$modifiedBox = new textinput('modifiedBy', $modifiedBy, 'text');
$modifiedBox->setCss('passive_surveillance');
$modifiedDateBox = new textinput('modifiedDate', $modifiedDate, 'text');
$modifiedDateBox->setCss('passive_surveillance');
$createdBox->extra = $createdDateBox->extra = $modifiedBox->extra = $modifiedDateBox->extra = 'disabled';

$objTableArea2 = $this->newObject('htmltable','htmlelements');
$objTableArea2->cellspacing = 2;
$objTableArea2->width = '99%';

$nextUri = $this->uri(array('action'=>'disease_report_screen_6', 'outbreakCode'=>$outbreakCode));
if (count($numcontrol) > 0) {
    $function = "javascript: document.location='$nextUri'";
} else {
    $message = $this->objLanguage->languageText('mod_ahis_mustaddcontrolmeasure', 'openaris');
    $function = "javascript: alert('$message')";
}
$sButton = new button('enter', $this->objLanguage->languageText('word_next'), $function);
$sButton->setCSS('nextButton');
$backUri = $this->uri(array('action'=>'disease_report_screen_4', 'outbreakCode'=>$outbreakCode));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: clearControlMeasures()");
$cButton->setCSS('clearButton');
$aButton = new button('add', $this->objLanguage->languageText('word_add'));
$aButton->setCSS('addButton');
$aButton->setToSubmit();

$objTableArea2->startRow();
$objTableArea2->addCell("<span class='outbreakcode'>".$this->objLanguage->languageText('mod_ahis_outbreakcode', 'openaris')."</span>&nbsp;");
$objTableArea2->addCell($outbreakCodeBox->show());
$objTableArea2->addCell($this->objLanguage->languageText('word_createdby'));
$objTableArea2->addCell($createdBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell($this->objLanguage->languageText('phrase_control'));
$objTableArea2->addCell($controlDrop->show());
$objTableArea2->addCell($this->objLanguage->languageText('phrase_createddate'));
$objTableArea2->addCell($createdDateBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell($this->objLanguage->languageText('mod_ahis_othermeasure', 'openaris'));
$objTableArea2->addCell($otherControlDrop->show());
$objTableArea2->addCell($this->objLanguage->languageText('word_modifiedby'));
$objTableArea2->addCell($modifiedBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell(' ');
$objTableArea2->addCell(' ');
$objTableArea2->addCell($this->objLanguage->languageText('phrase_modifieddate')."&nbsp;");
$objTableArea2->addCell($modifiedDateBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell($cButton->show().$tab.$bButton->show().$tab.$aButton->show().$tab.$sButton->show(), NULL, 'top', 'center', NULL, 'colspan="4"');
$objTableArea2->endRow();

$diagnosisSet = new fieldset('diagnosisSet');
$diagnosisSet->setExtra('class="diseasereport"');
$diagnosisSet->setLegend($this->objLanguage->languageText('mod_ahis_controlmeasureentry', 'openaris'));
$diagnosisSet->addContent($objTableArea2->show());

$objForm = new form('reportForm', $this->uri(array('action' => 'add_diseasecontrolmeasure')));
$objForm->addToForm($diagnosisSet->show());

$objTableArea3 = $this->newObject('htmltable','htmlelements');
$objTableArea3->cellspacing = 2;
$objTableArea3->width = NULL;
$objTableArea3->cssClass = 'areatable widetable';

$objTableArea3->startHeaderRow();
$objTableArea3->addHeaderCell($this->objLanguage->languageText('mod_ahis_outbreakcode', 'openaris'), NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_control'), NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('mod_ahis_othermeasure', 'openaris'), NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_createdby'), NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_createddate'), NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_modifiedby'), NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_modifieddate'), NULL, NULL, 'center');
$objTableArea3->endHeaderRow();

if (!empty($diseaseControlMeasures)) {
    foreach ($diseaseControlMeasures as $measure) {
        $controlMeasure = $this->objControlmeasures->getRow('id', $measure['controlmeasureid']);
        $otherMeasure = $this->objOtherControlMeasures->getRow('id', $measure['othermeasureid']);
        $objTableArea3->startRow();
        $objTableArea3->addCell($measure['outbreakcode'], NULL, NULL, 'center');
        $objTableArea3->addCell($controlMeasure['controlmeasure'], NULL, NULL, 'center');
        $objTableArea3->addCell($otherMeasure['control_measure'], NULL, NULL, 'center');
        $objTableArea3->addCell($this->objUser->Username($measure['created_by']), NULL, NULL, 'center');
        $objTableArea3->addCell($measure['date_created'], NULL, NULL, 'center');
        $modifier = ($measure['modified_by'] == NULL)? '' : $this->objUser->Username($measure['modified_by']);
        $objTableArea3->addCell($modifier, NULL, NULL, 'center');
        $objTableArea3->addCell($measure['date_modified'], NULL, NULL, 'center');
        $objTableArea3->endRow();
    }
} else {
    $objTableArea3->startRow();
    $objTableArea3->addCell("<i>".$this->objLanguage->languageText('phrase_norecords')."</i>", NULL, NULL, 'left', NULL, 'colspan="6"');
    $objTableArea3->endRow();         
}

$bSet = new fieldset('bSet');
$bSet->setExtra('class="diseasereport"');
$bSet->setLegend($this->objLanguage->languageText('mod_ahis_outbreakhistory', 'openaris'));
$bSet->addContent("<div class='scroll5'>".$objTableArea1->show()."</div>");
$cSet = new fieldset('cSet');
$cSet->setExtra('class="diseasereport"');
$cSet->setLegend($this->objLanguage->languageText('mod_ahis_controlmeasureview', 'openaris'));
$cSet->addContent("<div class='scroll5'>".$objTableArea3->show()."</div>");

$this->objJquery = $this->getObject('jquery', 'jquery');
$this->objJquery->loadTablesorterPlugin();
$scriptUri = $this->getResourceURI('util.js');
$script = "jQuery(document).ready(function() { jQuery('.areatable').tablesorter(); });";
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");
$this->appendArrayVar('headerParams', "<script type='text/javascript'>$script</script>");
				
$content = $objForm->show()."<!--hr class='blue' /-->".$bSet->show().$cSet->show();
echo $objHeading->show().$content;