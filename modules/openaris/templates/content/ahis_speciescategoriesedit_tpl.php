<?php
/**
 * ahis Edit species categories
 *
 * File containing the edit currency template
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
 * @author    Isaac Oteyo <ioteyo@icsit.jkuat.ac.ke>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: ahis_speciescategoriesedit_tpl.php 
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
 $title = 'Species category';
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('textarea','htmlelements');

$formAction = 'speciescategory_update';  
$buttonText = 'Save';
$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;

//species name
$species_name = new dropdown('speciesnameid');
$species_name->setSelected('id');
$species_name->addFromDB($speciesnames,'speciesname','id');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_speciesname'),NULL,NULL,'right');
$formTable->addCell($species_name->show());
$formTable->endRow();

//category
$category = new textinput('category',$speciescategories['category']);
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_category'),NULL,NULL,'right');
$formTable->addCell($category->show(),NULL,NULL,'left');
$formTable->endRow();

//abbreviation
$abbreviation = new textinput('abbreviation',$speciescategories['abbreviation']);
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_abbreviation'),NULL,NULL,'right');
$formTable->addCell($abbreviation->show(),NULL,NULL,'left');
$formTable->endRow();

//description
$description= new textarea('description',$speciescategories['description']);
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_description'),NULL,NULL,'right');
$formTable->addCell($description->show(),NULL,NULL,'left');
$formTable->endRow();

//start date
$dateStartPicker = $this->newObject('datepicker', 'htmlelements');
$dateStartPicker->name = 'startdate';
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_startdate'),NULL,NULL,'right');
$formTable->addCell($dateStartPicker->show(),NULL,NULL,'left');
$formTable->endRow();

//end date
$dateEndPicker = $this->newObject('datepicker', 'htmlelements');
$dateEndPicker->name = 'enddate';
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_enddate'),NULL,NULL,'right');
$formTable->addCell($dateEndPicker->show(),NULL,NULL,'left');
$formTable->endRow();

// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction,'id'=>$id)));
$form->addToForm($formTable->show());

//form validations
$form->addRule('speciesnameid', $this->objLanguage->languageText('mod_ahis_speciesnameerror','openaris'),'required');
$form->addRule('category', $this->objLanguage->languageText('mod_ahis_categoryerror','openaris'),'required');
$form->addRule('abbreviation', $this->objLanguage->languageText('mod_ahis_abbreviationerror','openaris'),'required');
$form->addRule('description', $this->objLanguage->languageText('mod_ahis_descriptionerror','openaris'),'required');
$form->addRule('startdate', $this->objLanguage->languageText('mod_ahis_startdateerror','openaris'),'datenotfuture');
if($dateStartPicker > $dateEndPicker)
{
$form->addRule('enddate', $this->objLanguage->languageText('mod_ahis_enddateerror','openaris'),'datenotpast');
}


//buttons
$button = new button ('speciescategories_save', 'Save');
$button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'speciescategory_admin'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());
$objLayer->align = 'center';

echo $objLayer->show();
?>