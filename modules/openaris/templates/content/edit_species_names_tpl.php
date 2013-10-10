<?php
/**
 * ahis Edit Species Names
 *
 * File containing the Edit Species Names template
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
 * @author    Samuel Onyach <sonyach@icsit.jkuat.ac.ke,onyachsamuel@yahoo.com>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: edit_species_names_tpl.php 
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
 $title = 'Species Names';
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('textarea','htmlelements');

$formAction = 'species_names_update';  
$buttonText = 'Save';

$speciesDrop = new dropdown('species');
$speciesDrop->setSelected('speciesname');
$speciesDrop->addFromDB($allspecies, 'speciesname', 'speciesname');


$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;


$label = new label ('Species:', 'species');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($speciesDrop->show());
$formTable->endRow();


$common_name= new textinput('common_name',$species['common_name']);
$label = new label ('Common Name:', 'common_name');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($common_name->show());
$formTable->endRow();


$label = new label ('Abbreviation:', 'abbrev');
$abbrev=new textinput('abbrev',$species['abbreviation']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($abbrev->show());
$formTable->endRow();

$label = new label ('Description:', 'desc');
$remarksBox = new textarea('desc', $species['description'], 4, 40);

$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;');
$formTable->addCell($remarksBox->show(),NULL, NULL, NULL, NULL, 'colspan="4"');
$formTable->endRow();

$dateStartPicker = $this->newObject('datepicker', 'htmlelements');
$dateStartPicker->name = 'startdate';

$label_start_date = new label('Start date: ','startdate');
$formTable->startRow();
$formTable->addCell($label_start_date->show(),NULL,NULL,'left');
$formTable->addCell($dateStartPicker->show(),NULL,NULL,'left');
$formTable->endRow();

//end date
$dateEndPicker = $this->newObject('datepicker', 'htmlelements');
$dateEndPicker->name = 'enddate';

$label_end_date = new label('End date: ','enddate');
$formTable->startRow();
$formTable->addCell($label_end_date->show(),NULL,NULL,'left');
$formTable->addCell($dateEndPicker->show(),NULL,NULL,'left');
$formTable->endRow();

// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction,'id'=>$id)));
$form->addToForm($formTable->show());
$form->addRule('common_name', 'Please enter common name', 'required');
$form->addRule('abbrev', 'Please enter Abbreviation', 'required');
$form->addRule('desc', 'Please enter Description', 'required');

//buttons
$button = new button ('species_names_update', 'Save');
$button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'select_officer'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());
$objLayer->align='center';
echo $objLayer->show();
?>