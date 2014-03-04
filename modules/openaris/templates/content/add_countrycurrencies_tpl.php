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
 * @author    Joseph Gatheru
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: 
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
$title = 'Country Currencies';

// Header
$header = new htmlheading();
$header->type = 2;
$header->str = $title;

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;

//CountryId
$label = new label ('Country: ', 'country');
$country = new dropdown('country');
$country->addFromDB($countries, 'name', 'name');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($country->show());
$formTable->endRow();

//LanguageId
$label = new label ('Currency: ', 'currency');
$currency = new dropdown('currency');
$currency->addFromDB($currencies, 'name', 'name');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($currency->show());
$formTable->endRow();

//start date
$label = new label ('Start Date: ', 'startdate');
$start = new textinput('startdate');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($startdate->show());
$formTable->endRow();
		
//end date
$label = new label ('End Date: ', 'enddate');
$start = new textinput('enddate');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($enddate->show());
$formTable->endRow();

$save = new button('countrylanguage_save', 'Save');
$save->setToSubmit();
$save->setCSS('saveButton');

$formTable->startRow();
$formTable->addCell($save->show());
$formTable->endRow();


$formAction = 'countrylanguage_save';  
$buttonText = 'Save';
	
// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('country', $this->objLanguage->languageText('mod_ahis_countryerror','openaris'),'required');
$form->addRule('language', $this->objLanguage->languageText('mod_ahis_languageerror','openaris'),'required');

$form->addToForm($formTable->show());

$objLayer = new layer();
$objLayer->addToStr($header->show()."<hr />".$form->show());

echo $objLayer->show(); 
?>