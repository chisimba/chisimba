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

$formAction = 'country_update';  
$buttonText = 'Save';

$langDrop = new dropdown('languages');//echo $languages;
$langDrop->addFromDB($languages, 'language', 'language');

$currDrop = new dropdown('currencies');
$currDrop->addFromDB($currencies, 'currency', 'currency'); 

$uoaDrop = new dropdown('units_of_area');
$uoaDrop->addFromDB($unitsOfArea, 'unit_of_area', 'unit_of_area'); 

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;


$isocountrycode= new textinput('isocountrycode',$country['iso_country_code']);
//$isolanguagecode->extra='readonly';
$label = new label ('ISO Country Code:', 'isocountrycode');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($isocountrycode->show());
$formTable->endRow();

$commoname= new textinput('commoname',$country['common_name']);
//$isolanguagecode->extra='readonly';
$label = new label ('Common Name:', 'commoname');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($commoname->show());
$formTable->endRow();

$officialname= new textinput('officialname',$country['official_name']);
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
$countryidd = new textinput('countryidd',$country['country_idd']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($countryidd->show());
$formTable->endRow();

$label = new label ('North Latitude:', 'northlat');
$northlat = new textinput('northlat',$country['north_latitude']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($northlat->show());
$formTable->endRow();

$label = new label ('South Latitude:', 'southlat');
$southlat = new textinput('southlat',$country['south_latitude']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($southlat->show());
$formTable->endRow();

$label = new label ('West Longitude:', 'westlong');
$westlong = new textinput('westlong',$country['west_longitude']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($westlong->show());
$formTable->endRow();

$label = new label ('East Longitude:', 'eastlong');
$eastlong = new textinput('eastlong',$country['east_longitude']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($eastlong->show());
$formTable->endRow();

$label = new label ('Area:', 'area');
$area = new textinput('area',$country['area']);
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($area->show());
$formTable->endRow();

$label = new label ('Unit Of Area:', 'unit_of_area');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($uoaDrop->show());
$formTable->endRow();

// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction,'id'=>$id)));
$form->addToForm($formTable->show());
$form->addRule('isocountrycode', 'Please enter ISO Country Code', 'required');
$form->addRule('commoname', 'Please enter Country Common Name', 'required');
$form->addRule('officialname', 'Please enter Country Official Name', 'required');
$form->addRule('counryidd', 'Please enter  Country IDD', 'required');
$form->addRule('northlat', 'Please enter North Latitude', 'required');
$form->addRule('southlat', 'Please enter South Latitude', 'required');
$form->addRule('westlong', 'Please enter West Longitude', 'required');
$form->addRule('eastlong', 'Please enter East Longitude', 'required');
$form->addRule('area', 'Please enter Area', 'required');
$form->addRule('northlat', 'Please enter valid number for North Latitude', 'numeric');
$form->addRule('southlat', 'Please enter valid number for South Latitude', 'numeric');
$form->addRule('westlong', 'Please enter valid number for West Longitude', 'numeric');
$form->addRule('eastlong', 'Please enter valid number for East Longitude', 'numeric');
$form->addRule('area', 'Please enter source of animals', 'required');


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
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());
$objLayer->align='center';
echo $objLayer->show();
?>