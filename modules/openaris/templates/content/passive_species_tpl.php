<?php
/**
 * ahis Passive Surveillance Species Template
 *
 * Template for capturing passive surveillance species data
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
 * @version   $Id: passive_species_tpl.php 14485 2009-08-21 18:13:03Z rosina $
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
$objHeading->str = $this->objLanguage->languageText('phrase_passive')." ".$this->objLanguage->languageText('word_species');
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('form','htmlelements');

$sButton = new button('enter', $this->objLanguage->languageText('word_next'));
$sButton->setToSubmit();
$sButton->setCSS('nextButton');
$backUri = $this->uri(array('action'=>'passive_outbreak'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: clearPassiveSpecies()");
$cButton->setCSS('clearButton');

$refNoBox = new textinput('refNo', $refNo, 'text', 15);
$monthBox = new textinput('month', date('F', strtotime($calendardate)), 'text', 8);
$yearBox = new textinput('year', date('Y', strtotime($calendardate)), 'text', 4);
$yearBox->extra = $monthBox->extra = $refNoBox->extra = "readonly";

$geo2Drop = new dropdown('geo2Id');
$geo2Drop->addFromDB($arrayGeo2, 'name', 'id');
$geo2Drop->setSelected($geo2Id);
$geo2Drop->extra = 'disabled';
$geo2Drop->cssClass = "passive_species";

$speciesDrop = new dropdown('speciesId');
$speciesDrop->addFromDB($arraySpecies, 'name', 'id');
$speciesDrop->setSelected($speciesId);
$speciesDrop->cssClass = "passive_species";
$ageDrop = new dropdown('ageId');
$ageDrop->addFromDB($arrayAge, 'name', 'id');
$ageDrop->setSelected($ageId);
$ageDrop->cssClass = "passive_species";
$sexDrop = new dropdown('sexId');
$sexDrop->addFromDB($arraySex, 'name', 'id');
$sexDrop->setSelected($sexId);
$sexDrop->cssClass = "passive_species";
$productionDrop = new dropdown('productionId');
$productionDrop->addFromDB($arrayProduction, 'name', 'id');
$productionDrop->setSelected($productionId);
$productionDrop->cssClass = "passive_species";
$controlDrop = new dropdown('controlId');
$controlDrop->addFromDB($arrayControl, 'name', 'id');
$controlDrop->setSelected($controlId);
$controlDrop->cssClass = "passive_species";
$basisDrop = new dropdown('basisId');
$basisDrop->addFromDB($arrayBasis, 'name', 'id');
$basisDrop->setSelected($basisId);
$basisDrop->cssClass = "passive_species";
//$oStatusDrop = new dropdown('oStatusId');
//$oStatusDrop->addFromDB($arrayOStatus, 'name', 'id');
//$oStatusDrop->setSelected($oStatusId);

$susceptibleBox = new textinput('susceptible', $susceptible, 'text', 15);
$casesBox = new textinput('cases', $cases, 'text', 15);
$deathsBox = new textinput('deaths', $deaths, 'text', 15);
$vaccinatedBox = new textinput('vaccinated', $vaccinated, 'text', 15);
$slaughteredBox = new textinput('slaughtered', $slaughtered, 'text', 15);
$destroyedBox = new textinput('destroyed', $destroyed, 'text', 15);
$productionBox = new textinput('herdtotal', $production, 'text', 15);
$newcasesBox = new textinput('newcases', $newcases, 'text', 15);
$recoveredBox = new textinput('recovered', $recovered, 'text', 15);
$prophylacticBox = new textinput('prophylactic', $prophylactic, 'text', 15);

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;
//$objTable->cssClass = 'min50';

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_outbreakref').":$tab");
$objTable->addCell($refNoBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_geolevel2').":$tab");
$objTable->addCell($geo2Drop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_monthandyear', 'openaris').":$tab");
$objTable->addCell($monthBox->show()."&nbsp; ".$yearBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_species').":$tab");
$objTable->addCell($speciesDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_age').":$tab");
$objTable->addCell($ageDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_sex').":$tab");
$objTable->addCell($sexDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_production').":$tab");
$objTable->addCell($productionDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_control').":$tab");
$objTable->addCell($controlDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_diagnosis').":$tab");
$objTable->addCell($basisDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_susceptible').":$tab");
$objTable->addCell($susceptibleBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_cases').":$tab");
$objTable->addCell($casesBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_deaths').":$tab");
$objTable->addCell($deathsBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_vaccinated').":$tab");
$objTable->addCell($vaccinatedBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_slaughtered').":$tab");
$objTable->addCell($slaughteredBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_destroyed').":$tab");
$objTable->addCell($destroyedBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_herdtotal').":$tab");
$objTable->addCell($productionBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_newcases').":$tab");
$objTable->addCell($newcasesBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_recovered').":$tab");
$objTable->addCell($recoveredBox->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_prophylactic').":$tab");
$objTable->addCell($prophylacticBox->show(),NULL,'center');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('');
$objTable->addCell("&nbsp;".$bButton->show().$tab.$cButton->show().$tab.$sButton->show());
$objTable->addCell('');
$objTable->endRow();

$valStr = $this->objLanguage->languageText('mod_ahis_valnumeric', 'openaris');

$objForm = new form('reportForm', $this->uri(array('action' => 'passive_vaccine')));
$objForm->addToForm($objTable->show());
$objForm->addRule('susceptible', $valStr." ".$objLanguage->languageText('word_susceptible'), 'numeric');
$objForm->addRule('cases', $valStr." ".$objLanguage->languageText('word_cases'), 'numeric');
$objForm->addRule('deaths', $valStr." ".$objLanguage->languageText('word_deaths'), 'numeric');
$objForm->addRule('vaccinated', $valStr." ".$objLanguage->languageText('word_vaccinated'), 'numeric');
$objForm->addRule('slaughtered', $valStr." ".$objLanguage->languageText('word_slaughtered'), 'numeric');
$objForm->addRule('destroyed', $valStr." ".$objLanguage->languageText('word_destroyed'), 'numeric');
$objForm->addRule('production', $valStr." ".$objLanguage->languageText('word_production'), 'numeric');
$objForm->addRule('newcases', $valStr." ".$objLanguage->languageText('phrase_newcases'), 'numeric');
$objForm->addRule('recovered', $valStr." ".$objLanguage->languageText('word_recovered'), 'numeric');
$objForm->addRule('prophylactic', $valStr." ".$objLanguage->languageText('word_prophylactic'), 'numeric');

//$objLayer = new layer();
//$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
//$objLayer->align = 'center';

$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");

//echo $objLayer->show();
echo $objHeading->show()."<br />".$objForm->show();