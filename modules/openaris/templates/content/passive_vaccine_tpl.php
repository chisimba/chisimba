<?php
/**
 * ahis Passive Surveillance Vaccine Template
 *
 * Template for capturing passive surveillance vaccine data
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
 * @version   $Id: passive_vaccine_tpl.php 13733 2009-06-23 11:04:26Z nic $
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
$objHeading->str = $this->objLanguage->languageText('phrase_passive')." ".$this->objLanguage->languageText('word_vaccine');
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('checkbox','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('form','htmlelements');

$sButton = new button('enter', $this->objLanguage->languageText('word_finish'));
$sButton->setToSubmit();
$sButton->setCSS('submitButton');
$backUri = $this->uri(array('action'=>'passive_species'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: clearPassiveVaccine()");
$cButton->setCSS('clearButton');

$refNoBox = new textinput('refNo', $refNo, 20);
$monthBox = new textinput('month', date('F', strtotime($calendardate)), 'text', 13);
$yearBox = new textinput('year', date('Y', strtotime($calendardate)), 'text', 4);
$yearBox->extra = $monthBox->extra = $refNoBox->extra = "readonly";

$geo2Drop = new dropdown('geo2Id');
$geo2Drop->addFromDB($arrayGeo2, 'name', 'id');
$geo2Drop->setSelected($geo2Id);
$geo2Drop->extra = 'disabled';
$geo2Drop->cssClass = "passive_vaccine";

$manufactureDate = $this->newObject('datepicker','htmlelements');
$manufactureDate->setName('dateManufactured');
$manufactureDate->setDefaultDate(date('Y-m-d'));
$expireDate = $this->newObject('datepicker','htmlelements');
$expireDate->setName('dateExpire');
$expireDate->setDefaultDate(date('Y-m-d'));

$sourceBox = new textinput('source', NULL, 'text', 20);
$batchBox = new textinput('batch', NULL, 'text', 20);

$panvacCheck = new checkbox('panvac');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;
//$objTable->cssClass = 'min50';

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_outbreakref').":$tab");
$objTable->addCell($refNoBox->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_geolevel2').":$tab");
$objTable->addCell($geo2Drop->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_monthandyear', 'openaris').":$tab");
$objTable->addCell($monthBox->show()."&nbsp; ".$yearBox->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_vacsource', 'openaris').":$tab");
$objTable->addCell($sourceBox->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_batch', 'openaris').":$tab");
$objTable->addCell($batchBox->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_manufacturedate', 'openaris').":$tab");
$objTable->addCell($manufactureDate->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_expiredate', 'openaris').":$tab");
$objTable->addCell($expireDate->show(), NULL, 'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_panvactested', 'openaris').":$tab");
$objTable->addCell($panvacCheck->show(), NULL, 'center');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('');
$objTable->addCell("&nbsp;".$bButton->show().$tab.$cButton->show().$tab.$sButton->show());
$objTable->endRow();

$objForm = new form('reportForm', $this->uri(array('action' => 'passive_save')));
$objForm->addToForm($objTable->show());
$objForm->addRule('dateManufactured', $this->objLanguage->languageText('mod_ahis_valdatemanufactured', 'openaris'), 'datenotfuture');
$objForm->addRule('source', $this->objLanguage->languageText('mod_ahis_valvacsourcerequired', 'openaris'), 'required');
$objForm->addRule('source', $this->objLanguage->languageText('mod_ahis_valvacsource', 'openaris'), 'nonnumeric');
$objForm->addRule('batch', $this->objLanguage->languageText('mod_ahis_valvacbatchrequired', 'openaris'), 'numeric');

//$objLayer = new layer();
//$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
//$objLayer->align = 'center';

$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");

//echo $objLayer->show();
echo $objHeading->show()."<br />".$objForm->show();