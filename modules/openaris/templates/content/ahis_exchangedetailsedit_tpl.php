<?php
/**
 * ahis Edit Exchange rate
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
 * @version   $Id: ahis_exchangerateedit_tpl.php 
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
 $title = 'Exchange rate details';
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('textarea','htmlelements');

$formAction = 'exchangeratedetails_update';  
$buttonText = 'Save';
$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;
$exchange_rate= new textinput('exchangerateid',$exchangeratedetails['0']['exchangerateid']);
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_exchangecurrency'));
$formTable->addCell($exchange_rate->show());
$formTable->endRow();

$conversion_factor= new textinput('conversionfactor',$exchangeratedetails['0']['conversionfactor']);
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_conversionfactor'));
$formTable->addCell($conversion_factor->show());
$formTable->endRow();

//start date
$dateStartPicker = $this->newObject('datepicker', 'htmlelements');
$dateStartPicker->name = 'startdate';
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_startdate',NULL,NULL,'right'));
$formTable->addCell($dateStartPicker->show(),NULL,NULL,'left');
$formTable->endRow();

//end date
$dateEndPicker = $this->newObject('datepicker', 'htmlelements');
$dateEndPicker->name = 'enddate';
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_enddate',NULL,NULL,'right'));
$formTable->addCell($dateEndPicker->show(),NULL,NULL,'left');
$formTable->endRow();

//date created
$dateCreatedPicker = $this->newObject('datepicker', 'htmlelements');
$dateCreatedPicker->name = 'datecreated';

$label_date_created = new label('Date created: ','datecreated');
$formTable->startRow();
$formTable->addCell($label_date_created->show(),NULL,NULL,'right');
$formTable->addCell($dateCreatedPicker->show(),NULL,NULL,'left');
$formTable->endRow();


$label = new label ('Created by:', 'createdby');
$createdby = new textinput('createby',$exchangeratedetails['0']['createdby']);
$formTable->startRow();
$formTable->addCell($label->show(),NULL,NULL,'right');
$formTable->addCell($createdby->show(),NULL,NULL,'left');
$formTable->endRow();

//date modified
$dateModifiedPicker = $this->newObject('datepicker', 'htmlelements');
$dateModifiedPicker->name = 'datemodified';

$label_date_created = new label('Date modified: ','datemodified');
$formTable->startRow();
$formTable->addCell($label_date_created->show(),NULL,NULL,'right');
$formTable->addCell($dateModifiedPicker->show(),NULL,NULL,'left');
$formTable->endRow();

$label = new label ('Modified by:', 'modifiedby');
$modifiedby = new textinput('modifiedby',$exchangeratedetails['0']['modifiedby']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($modifiedby->show());
$formTable->endRow();

// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction,'id'=>$id)));
$form->addToForm($formTable->show());

//form validations
$form->addRule('defaultcurrencyid', $this->objLanguage->languageText('mod_ahis_defaultcurrencyiderror','openaris'),'required');
$form->addRule('exchangecurrencyid', $this->objLanguage->languageText('mod_ahis_exchangecurrencyiderror','openaris'),'required');
$form->addRule('startdate', $this->objLanguage->languageText('mod_ahis_startdateerror','openaris'),'datenotfuture');
$form->addRule('enddate', $this->objLanguage->languageText('mod_ahis_enddateerror','openaris'),'required');
$form->addRule('datecreated', $this->objLanguage->languageText('mod_ahis_datecreatederror','openaris'),'required');
$form->addRule('createdby', $this->objLanguage->languageText('mod_ahis_createdbyerror','openaris'),'required');
$form->addRule('datemodified', $this->objLanguage->languageText('mod_ahis_datemodifiederror','openaris'),'required');

//buttons
$button = new button ('exchangeratedetails_save', 'Save');
$button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'exchangerates_admin'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());

echo $objLayer->show();
?>
