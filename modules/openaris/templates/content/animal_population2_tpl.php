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
 $title =  $this->objLanguage->languageText('mod_ahis_animalpopulation2','openaris');
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

$classDrop = new dropdown('classification');

$classDrop->addFromDB($arrayspecies, 'speciesname', 'id'); 
$classDrop->setSelected($species);
$classDrop->extra = 'disabled'; 

//Breed dropdown
$breedDrop = new dropdown('breedId');
$breedDrop->addOption('null','select');
$breedDrop->addFromDB($arraybreed, 'name', 'id');
$breedDrop->setSelected($breed);
$breedDrop->extra='disabled';
//echo $breed;
//animal category dropdown
$animalCatDrop=new dropdown('animalCat');
$animalCatDrop->addFromDB($arrayanimalCat, 'agegroup', 'id');
//$animalCatDrop->setSelected($animalcat);


$monthBox = new textinput('month', date('F'), 'text', 23);
$dateBox = new textinput('reportdate', date('Y/m/d'),'text', 30);
$yearBox = new textinput('year', date('Y'), 'text', 4);

$repDate = new textinput('rDate',$rDate);
$repDate->extra = 'disabled';

$ibarDate= new textinput('iDate',$iDate);
$ibarDate->extra ='disabled';

//print_r($arrayanimalCat);
$reportOfficerDrop = new dropdown('repoff');
$reportOfficerDrop->addOption('null','Select');
$reportOfficerDrop->addFromDB($userList, 'name', 'userid');
$reportOfficerDrop->setSelected($repoff);
$reportOfficerDrop->extra='disabled';

$totalNumSpecies = new textinput('totalNumSpecies', $totSpecies);
$totalNumSpecies->extra = 'onblur="javascript:getTLU();"';

$breedNumber = new textinput('breedNumber', $breedNumber, 'text');
//$animalCat = new textinput('animalCat', $animalCat, 'text');
$breedNumber->extra = 'onchange="javascript:checkBreedSpecies();"';

$tropicalLivestock = new textinput('tropicalLivestock', $tropicalLivestock, 'text');
$tropicalLivestock->extra = 'disabled';

$crossBreed = new textinput('crossBreed', $crossBreed, 'text');

$catNumber = new textinput('catNumber', $catNumber, 'text');
$catNumber->extra = 'onchange="javascript:checkAnimCatSpecies();"';

$prodNumber = new textinput('productionno', $productionno, 'text');
$prodNumber->extra = 'onchange="javascript:checkProdNoSpecies();"';

$commentsBox = new textarea('comments', '' , 4, 40);
$commentsBox->extra = 'onkeyup="javascript:limitcomment();"';

//buttons
//checking max and min number of total species 
if ($totSpecies > 400000) {
    $message = $this->objLanguage->languageText('mod_ahis_totalspecieslimit', 'openaris');
    $valtot = "javascript: alert('$message')";
} else {
    $valtot = '';
}
$button = new button ('animal_population_save', 'Submit',$valtot);
$button->setCSS('submitButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'animal_population_add'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;

//Reporting Officer
$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
$tabs = "$tab$tab$tab";
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris').": $tab");
$objTable->addCell($reportOfficerDrop->show().$tab);

$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportdate','openaris').":$tab");
//$objTable->addCell($dateBox->show(),NULL,'center');
$objTable->addCell($repDate->show(),NULL,'center');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_word_species', 'openaris'));
$objTable->addCell($classDrop->show());
//IBAR date
$objTable->addCell($this->objLanguage->languageText('mod_ahis_ibarrecdate','openaris').": $tab");
$objTable->addCell($ibarDate->show(),NULL, 'center');
$objTable->endRow();

$objTable->addCell($this->objLanguage->languageText('word_breed'));
$objTable->addCell($breedDrop->show(),NULL,'center');
$objTable->endRow();

//animal production
$label = new label ('Production Name:', ' input_production');
$production = new textinput('animal_production',$prodname);
$production->extra = 'disabled';
$objTable->startRow();

$objTable->addCell($label->show());
$objTable->addCell($production->show());
$objTable->endRow();	

//2nd table
$middleTable= $this->newObject('htmltable','htmlelements');
$middleTable->cellspacing = 2;
$middleTable->width = NULL;


$middleTable->startRow();
$middleTable->addCell($this->objLanguage->languageText('mod_ahis_totalnumberspecies', 'openaris'));
$middleTable->addCell($tab.$totalNumSpecies->show());
$middleTable->addCell($tab.$this->objLanguage->languageText('mod_ahis_breedno','openaris'));
$middleTable->addCell($tab.$breedNumber->show());
$middleTable->addCell($tab.$this->objLanguage->languageText('mod_ahis_animalcat','openaris'));
$middleTable->addCell($tab.$animalCatDrop->show());
$middleTable->endRow();

$middleTable->startRow();
$middleTable->addCell($this->objLanguage->languageText('mod_ahis_tropicallivestock','openaris'));
$middleTable->addCell($tab.$tropicalLivestock->show());
$middleTable->addCell($tab.$this->objLanguage->languageText('mod_ahis_crossbreed','openaris'));
$middleTable->addCell($tab.$crossBreed->show());
$middleTable->addCell($tab.$this->objLanguage->languageText('mod_ahis_categoryno','openaris'));
$middleTable->addCell($tab.$catNumber->show());
$middleTable->endRow();

$middleTable->startRow();
$middleTable->addCell($this->objLanguage->languageText('mod_ahis_productionno','openaris'));
$middleTable->addCell($tab.$prodNumber->show());
$middleTable->endRow();

//bottom Table
$bottomTable= $this->newObject('htmltable','htmlelements');
$bottomTable->cellspacing=2;
$bottomTable->width=NULL;

$bottomTable->startRow();
$bottomTable->addCell($this->objLanguage->languageText('word_comments'));
$bottomTable->addCell($tab.$commentsBox->show());
$bottomTable->endRow();



$objButtonTable = $this->newObject('htmltable','htmlelements');
$objButtonTable->cellspacing = 2;
$objButtonTable->width = '99%';
$objButtonTable->startRow();
$objButtonTable->addCell($bButton->show().$tabs.$btcancel->show().$tabs.$button->show(), NULL, 'top', 'center');
$objButtonTable->endRow();

// Create Form
//$speciesMax=4000;
$form2 = new form ('add', $this->uri(array('action'=>'animal_population2')));
$content=$objTable->show()."<hr />".$middleTable->show()."<hr />".$bottomTable->show()."<br />".$objButtonTable->show();
$form2->addToForm($content);
$form2->addRule('totalNumSpecies',$this->objLanguage->languageText('mod_ahis_valtotnum', 'openaris'), 'numeric');
$form2->addRule('breedNumber', $this->objLanguage->languageText('mod_ahis_valbreedno', 'openaris'), 'required');
$form2->addRule('crossBreed', $this->objLanguage->languageText('mod_ahis_valcrossbreedno', 'openaris'), 'required');
$form2->addRule('crossBreed', $this->objLanguage->languageText('mod_ahis_valcrossbreedno', 'openaris'), 'numeric');
$form2->addRule('animalCat', $this->objLanguage->languageText('mod_ahis_valanimalcat', 'openaris'), 'select');
$form2->addRule('catNumber', $this->objLanguage->languageText('mod_ahis_valcatno', 'openaris'), 'numeric');



$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form2->show());

echo $objLayer->show();
?>