<?php
/**
 * ahis Add Country
 *
 * File containing the Add Country template
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
 * @version   $Id: add_country_tpl.php 
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
 $title = 'Countries';
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label', 'htmlelements');
if (isset($output)) {
    $objMsg = $this->getObject('timeoutmessage','htmlelements');
    $objMsg->setHideTypeToNone();
  
		$objMsg->setMessage($output);

    $msg = $objMsg->show();

} else {
    $msg = '';
}

$formAction = 'country_save';  
$buttonText = 'Save';

$langDrop = new dropdown('languages');//echo $languages;
$langDrop->addOption('','select');
$langDrop->addFromDB($languages, 'language', 'language');

$currDrop = new dropdown('currencies');
$currDrop->addOption('','select');
$currDrop->addFromDB($currencies, 'currency', 'currency'); 

$uoaDrop = new dropdown('units_of_area');
$uoaDrop->addOption('','select');
$uoaDrop->addFromDB($unitsOfArea, 'unit_of_area', 'unit_of_area'); 

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;


$isocountrycode= new textinput('isocountrycode',$code);
//$isolanguagecode->extra='readonly';
$label = new label ('ISO Country Code:', 'isocountrycode');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($isocountrycode->show());
$formTable->endRow();

$commoname= new textinput('commoname',$commonname);
//$isolanguagecode->extra='readonly';
$label = new label ('Common Name:', 'commoname');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($commoname->show());
$formTable->endRow();

$officialname= new textinput('officialname',$officialname);
//$isolanguagecode->extra='readonly';
$label = new label ('Official Name:', 'officialname');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($officialname->show());
$formTable->endRow();

$label = new label ('Default Language:', 'defaultlanguage');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($langDrop->show());
$formTable->endRow();

$label = new label ('Default Currency:', 'defaultcurrency');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($currDrop->show());
$formTable->endRow();


$label = new label ('Country IDD:', 'countryidd');
$countryidd = new textinput('countryidd',$countryidd);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($countryidd->show());
$formTable->endRow();

$label = new label ('North Latitude(prefix +/- for non zero values):', 'northlat');
$northlat = new textinput('northlat',$northlat);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($northlat->show());
$formTable->endRow();

$label = new label ('South Latitude(prefix +/- for non zero values):', 'southlat');
$southlat = new textinput('southlat',$southlat);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($southlat->show());
$formTable->endRow();

$label = new label ('West Longitude(prefix +/- for non zero values):', 'westlong');
$westlong = new textinput('westlong',$westlong);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($westlong->show());
$formTable->endRow();

$label = new label ('East Longitude(prefix +/- for non zero values):', 'eastlong');
$eastlong = new textinput('eastlong',$eastlong);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($eastlong->show());
$formTable->endRow();

$label = new label ('Area:', 'area');
$area = new textinput('area',$area);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($area->show());
$formTable->endRow();

$label = new label ('Unit Of Area:', 'unit_of_area');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($uoaDrop->show());
$formTable->endRow();

/*$dateStartPicker = $this->newObject('datepicker', 'htmlelements');
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
$formTable->endRow();*/



// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));
$form->addToForm($formTable->show());
$form->addRule('isocountrycode', 'Please enter ISO Country Code', 'required');
$form->addRule('commoname', 'Please enter Country Common Name', 'required');
$form->addRule('officialname', 'Please enter Country Official Name', 'required');
$form->addRule('languages', 'Please select default language', 'required');
$form->addRule('currencies', 'Please select default currency', 'required');



$form->addRule('countryidd', 'Please enter  Country IDD', 'required');
$form->addRule('northlat', 'Please enter North Latitude', 'required');
$form->addRule('northlat', 'Please enter valid number for North Latitude', 'numeric');
$form->addRule('southlat', 'Please enter South Latitude', 'required');
$form->addRule('southlat', 'Please enter valid number for South Latitude', 'numeric');

$form->addRule('westlong', 'Please enter West Longitude', 'required');
$form->addRule('westlong', 'Please enter valid number for West Longitude', 'numeric');

$form->addRule('eastlong', 'Please enter East Longitude', 'required');
$form->addRule('eastlong', 'Please enter valid number for East Longitude', 'numeric');
$form->addRule('area', 'Please enter Area', 'required');
$form->addRule('area', 'Please enter  valid Area', 'numeric');
$form->addRule('units_of_area', 'Please select unit of area', 'required');



//buttons
$button = new button ('country_save', 'Save');
$button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'select_officer'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());

$objLayer = new layer();
$heading =$msg;
$objLayer->addToStr($heading);

$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());
$objLayer->align = 'center';

echo $objLayer->show();
?>