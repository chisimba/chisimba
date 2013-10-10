<?php
/**
 * ahis Active Survaillance Herd Details screen  Template
 *
 * Template for capturing active surveillance herd data
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
 * @version   $Id: active_herddetails_tpl.php 
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
$objHeading->str = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_herd')." ".$this->objLanguage->languageText('word_details');
$objHeading->type = 2;


$this->loadClass('layer','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('textinput','htmlelements');

$objTable = $this->getObject('htmltable','htmlelements');

$backUri = $this->uri(array('action'=>'active_herdview'));
$backButton = new button('back', $this->objLanguage->languageText('word_back'),"javascript: document.location='$backUri'");

$nextButton = new button('next', $this->objLanguage->languageText('word_next'));
$nextButton->setToSubmit();


$campBox = new dropdown('campName');
$campBox->addFromDB($arrayCamp, 'campname', 'id');
$campBox->setSelected($campName);
$campBox->extra = 'disabled';

$diseaseBox = new dropdown('disease');
$diseaseBox->addFromDB($arraydisease, 'disease', 'id');
$diseaseBox->setSelected($disease);
$diseaseBox->extra = 'disabled';


$officerDrop = new dropdown('officerId');
$officerDrop->addFromDB($arraydisease, 'reporterid', 'id');
$officerDrop->setSelected($officerId);
$officerDrop->extra = 'disabled';


$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();
$objTable->addCell("<h6>".$this->objLanguage->languageText('word_campaign')." ".$this->objLanguage->languageText('word_name').": </h6>");
$objTable->addCell($campBox->show());
$objTable->addCell('&nbsp');
$objTable->addCell('&nbsp');
$objTable->addCell("<h6>".$this->objLanguage->languageText('word_disease').": </h6>");
$objTable->addCell($diseaseBox->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris').": $tab");
$objTable->addCell($officerDrop->show());
$objTable->addCell('');
$objTable->addCell('');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_herd').": $tab");
$objTable->addCell('');
$objTable->addCell('');
$objTable->endRow();

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objTable->show());
$objLayer->align = 'center';

echo $objLayer->show();

$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = '80%';
$objTable->cssClass = 'min50';

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_species'),'','','','heading');

$objTable->addCell($this->objLanguage->languageText('word_number'),'', '', '', 'heading');

$objTable->addCell($this->objLanguage->languageText('word_age'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_sex'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_vaccinationhistory'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_disease')." ".$this->objLanguage->languageText('word_past')." ".$this->objLanguage->languageText('word_year'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_clinicalsigns'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_animalsmovedfrom').":", '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_animalsmovedto'),'', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_comments')." ".$this->objLanguage->languageText('word_past')." ".$this->objLanguage->languageText('word_year'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_grazingyard'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_action'), '', '', '', 'heading');

$objTable->endRow();
$objTable->startRow();
$objTable->addCell('');
$objTable->addCell('');
$objTable->addCell('');
$objTable->addCell('');
$objTable->addCell('');
$objTable->addCell('');
$objTable->startRow();
$objTable->addCell('&nbsp');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($backButton->show());
$objTable->addCell($nextButton->show());
$objTable->endRow();
$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $this->uri(array('action' => 'active_herdsampling')));
$objForm->addToForm($objTable->show());

$objLayer = new layer();
$objLayer->addToStr($objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();

?>













