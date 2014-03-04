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
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('layer', 'htmlelements');

//title
$title = 'Vaccine Inventory';

// Header
$header = new htmlheading();
$header->type = 2;
$header->str = $title;

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;

//district name
$label_district = new label ('District name: ', 'district');
$district = new textinput('district',$dist);
$district->extra = 'readonly';
$formTable->startRow();
$formTable->addCell($label_district->show());
$formTable->addCell($district->show());

// vaccine name	
$label = new label ('Vaccine name: ', 'vaccinename');
$vaccinename = new dropdown('vaccinename');
$vaccinename->addFromDB($vaccination, 'name', 'name'); 

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($vaccinename->show());
$formTable->endRow();

$label_doses = new label ('Total doses in hand: ', 'doses');
$doses = new textinput('doses');

$formTable->startRow();
$formTable->addCell($label_doses->show());
$formTable->addCell($doses->show());
$formTable->endRow();

$label_start = new label ('Total doses at start of month: ', 'dosesstartofmonth');
$doses_start = new textinput('dosesstartofmonth');

$formTable->startRow();
$formTable->addCell($label_start->show());
$formTable->addCell($doses_start->show());
$formTable->endRow();

$datePicker = $this->newObject('datepicker', 'htmlelements');
$datePicker->name = 'startmonth';

$label_start = new label('Month start date: ','startmonth');
$formTable->startRow();
$formTable->addCell($label_start->show());
$formTable->addCell($datePicker->show());
$formTable->endRow();

$label_end = new label ('Total doses at end of month: ', 'dosesendofmonth');
$doses_end = new textinput('dosesendofmonth');
$formTable->startRow();
$formTable->addCell($label_end->show());
$formTable->addCell($doses_end->show());
$formTable->endRow();

$datePickerOne = $this->newObject('datepicker', 'htmlelements');
$datePickerOne->name = 'endmonth';
$label_end = new label('Month end date: ','endmonth');

$formTable->startRow();
$formTable->addCell($label_end->show());
$formTable->addCell($datePickerOne->show());
$formTable->endRow();

$label_received = new label ('Total doses received in month: ', 'dosesreceived');
$doses_received= new textinput('dosesreceived');
$formTable->startRow();
$formTable->addCell($label_received->show());
$formTable->addCell($doses_received->show());
$formTable->endRow();

$label_used = new label ('Doses used: ', 'dosesused');
$doses_used= new textinput('dosesused');
$formTable->startRow();
$formTable->addCell($label_used->show());
$formTable->addCell($doses_used->show());
$formTable->endRow();

$label_wasted = new label ('Doses wasted: ', 'doseswasted');
$doses_wasted= new textinput('doseswasted');
$formTable->startRow();
$formTable->addCell($label_wasted->show());
$formTable->addCell($doses_wasted->show());
$formTable->endRow();
		
$save = new button('animalvaccine_save', 'Save');
$save->setToSubmit();
$save->setCSS('saveButton');

$backUri = $this->uri(array('action' => 'select_officer'));
$bButton = new button('back', $this->objLanguage->languageText('word_cancel'), "javascript: document.location='$backUri'");
$bButton->setCSS('cancelButton');

$formTable->startRow();
$formTable->addCell($save->show());
$formTable->addCell($bButton->show());
$formTable->endRow();


$formAction = 'animalvaccine_save';  
$buttonText = 'Save';
	
// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('district', $this->objLanguage->languageText('mod_ahis_districterror','openaris'),'required');
$form->addRule('doses', $this->objLanguage->languageText('mod_ahis_doseserror','openaris'),'required');
$form->addRule('doses', $this->objLanguage->languageText('mod_ahis_dosesnumbererror','openaris'),'numeric');
$form->addRule('dosesstartofmonth', $this->objLanguage->languageText('mod_ahis_starterror','openaris'),'required');
$form->addRule('dosesstartofmonth', $this->objLanguage->languageText('mod_ahis_startnumbererror','openaris'),'numeric');
$form->addRule('startmonth', $this->objLanguage->languageText('mod_ahis_valdate','openaris'),'datenotfuture');
$form->addRule('dosesendofmonth', $this->objLanguage->languageText('mod_ahis_enderror','openaris'),'required');
$form->addRule('dosesendofmonth', $this->objLanguage->languageText('mod_ahis_endnumbererror','openaris'),'numeric');
$form->addRule('endmonth', $this->objLanguage->languageText('mod_ahis_valdate','openaris'),'datenotfuture');
$form->addRule('dosesreceived', $this->objLanguage->languageText('mod_ahis_receivederror', 'openaris'), 'required');
$form->addRule('dosesreceived', $this->objLanguage->languageText('mod_ahis_receivednumbererror', 'openaris'), 'numeric');
$form->addRule('dosesused', $this->objLanguage->languageText('mod_ahis_usederror', 'openaris'), 'required');
$form->addRule('dosesused', $this->objLanguage->languageText('mod_ahis_usednumbererror', 'openaris'), 'numeric');
$form->addRule('doseswasted', $this->objLanguage->languageText('mod_ahis_wastederror', 'openaris'), 'required');
$form->addRule('doseswasted', $this->objLanguage->languageText('mod_ahis_wastednumbererror', 'openaris'), 'numeric');

$form->addToForm($formTable->show());

$objLayer = new layer();
$objLayer->addToStr($header->show()."<hr />".$form->show());

echo $objLayer->show(); 
?>