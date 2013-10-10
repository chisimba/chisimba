<?php
/**
 * ahis Active Survaillance search screen Template
 *
 * Template for searching data from active surveillance 
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
 * @author    Rosina Ntow <rntow@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: active_search_tpl.php 
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
$objHeading->str = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_search');
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');

$searchButton = new button('search', $this->objLanguage->languageText('word_search'));
$searchButton->setToSubmit();
$backUri = $this->uri(array('action'=>'active_surveillance'));
$backButton = new button('cancel', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");

$campNameDrop = new dropdown('campName');
$campNameDrop->addFromDB($arrayCamp, 'campname', 'id');
$campNameDrop->setSelected($campName);

$inputDate = $this->getObject('datepicker','htmlelements');
$inputDate->setDefaultDate($calendardate);

$testTypeDrop = new dropdown('testtype');
$testTypeDrop->addFromDB($arrayTest, 'name', 'id');
$testTypeDrop->setSelected($test);
$speciesDrop = new dropdown('speciesId');
$speciesDrop->addFromDB($arraySpecies, 'name', 'id');
$speciesDrop->setSelected($speciesId);
$testResultDrop = new dropdown('result');
$testResultDrop->addFromDB($arrayTestresult, 'name', 'id');
$testResultDrop->setSelected($testresult);
$territoryDrop = new dropdown('territory');
$territoryDrop->addFromDB($arrayTerritory, 'name', 'id');
$territoryDrop->setSelected($territory);
$searchDrop = new dropdown('search');
$searchDrop->addOption('Listing','Listing');
$searchDrop->setSelected($testtype);

$officerText = new textinput('officerId');
$sampText = new textinput('samplingId');
$reportText = new textinput('reportId');
$geo2Text = new textinput('geo2');
$geo3Text = new textinput('geo3');
$farmText = new textinput('farm');


$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_campaign').": $tab");
$objTable->addCell($campNameDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris').": $tab");
$objTable->addCell($officerText->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_dateofsampling').": $tab");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_before').": $tab");
$objTable->addCell($inputDate->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_after').": $tab");
$objTable->addCell($inputDate->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_samplingid').": $tab");
$objTable->addCell($sampText->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_reportid').": $tab");
$objTable->addCell($reportText->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_species').": $tab");
$objTable->addCell($speciesDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_testtype').": $tab");
$objTable->addCell($testTypeDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_testresult').": $tab");
$objTable->addCell($testResultDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_geolevel2').": $tab");
$objTable->addCell($geo2Text->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_geolevel3').": $tab");
$objTable->addCell($geo3Text->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_territory').": $tab");
$objTable->addCell($territoryDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_farm').": $tab");
$objTable->addCell($farmText->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_search').": $tab");
$objTable->addCell($searchDrop->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('&nbsp');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($backButton->show());
$objTable->addCell($searchButton->show());
$objTable->endRow();


$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $this->uri(array('action' => 'active_newherd')));
$objForm->addToForm($objTable->show());

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();








?>