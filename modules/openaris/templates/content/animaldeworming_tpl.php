<?php
/**
 * 
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
 * @author    Isaac N. Oteyo <ioteyo@jkuat.ac.ke, isaacoteyo@gmail.com>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: add_animalmovement_tpl.php 12780 2009-03-11 10:46:10Z rosina $
 * @link      http://avoir.uwc.ac.za, http://www.jkuat.ac.ke
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

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('layer', 'htmlelements');
$this->loadClass('label', 'htmlelements');

//title
$title = 'Deworming';

// Header
$header = new htmlheading();
$header->type = 2;
$header->str = $title;

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;
$formTable->cssClass = 'min50';

$label_district = new label ('District: ', 'district');
$district = new textinput('district',$dist);
$district->extra = 'readonly';

$formTable->startRow();
$formTable->addCell($label_district->show());
$formTable->addCell($district->show());
$formTable->endRow();

// animal classification	
$label = new label ('Animal Classification: ', 'classification');
$classification = new dropdown('classification');
$classification->addFromDB($species, 'name', 'name'); 
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($classification->show());
$formTable->endRow();

// animal origin	
$label = new label ('Number of animals dewormed: ', 'numberofanimals');
$number_animals = new textinput('numberofanimals');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($number_animals->show());
$formTable->endRow();

// antiemitic type
$label = new label ('Control Measure: ', 'antiemitictype');
$antiemitictype = new dropdown('antiemitictype');
$antiemitictype->addFromDB($control, 'name', 'name'); 
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($antiemitictype->show());
$formTable->endRow();

$label_remarks = new label('<div class="labels">'.$this->objLanguage->languageText('mod_ahis_remarks', 'openaris', 'Remarks: '), 'remarks');

$remarks = new textarea('remarks');
$formTable->startRow();
$formTable->addCell($label_remarks->show());
$formTable->addCell($remarks->show());
$formTable->endRow();

if (isset($error)) {
    $formTable->startRow();
    $formTable->addCell($error, NULL, NULL, NULL, NULL, "colspan=2");
    $formTable->endRow();
}
$save = new button('animaldeworming_save', 'Save');
$save->setCSS('saveButton');
$save->setToSubmit();
 
$backUri = $this->uri(array('action'=>'select_officer'));
$bButton = new button('back', $this->objLanguage->languageText('word_cancel'), "javascript: document.location='$backUri'");
$bButton->setCSS('cancelButton');

$formTable->startRow();
$formTable->addCell($save->show());
$formTable->addCell($bButton->show());
$formTable->endRow();

$formAction = 'animaldeworming_save';  
$buttonText = 'Save';
	
// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('district', $this->objLanguage->languageText('mod_ahis_districterror','openaris'),'required');
$form->addRule('classification', $this->objLanguage->languageText('mod_ahis_classificationerror','openaris'),'required');
$form->addRule('numberofanimals', $this->objLanguage->languageText('mod_ahis_numberofanimalserror','openaris'),'required');
$form->addRule('numberofanimals', $this->objLanguage->languageText('mod_ahis_numberofanimalserrorone','openaris'),'numeric');
//$form->addRule('remarks', $this->objLanguage->languageText('mod_ahis_remarkserror','openaris'),'required');
//$form->addRule('remarks', $this->objLanguage->languageText('mod_ahis_remarkserrorone', 'openaris'), 'letteronly');

$form->addToForm($formTable->show());

$objLayer = new layer();
$objLayer->addToStr($header->show()."<hr />".$form->show());

echo $objLayer->show(); 
?>