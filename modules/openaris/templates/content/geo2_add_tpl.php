<?php
/**
 * ahis Geography level 2 add Template
 *
 * Template to add Geography Segment Level 2
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
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: geo2_add_tpl.php 13717 2009-06-22 08:16:22Z nic $
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

$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');

if ($id) {
    $hStr = $this->objLanguage->languageText('word_edit')." ".$this->objLanguage->languageText('mod_ahis_geolevel','openaris');
    $formUri = $this->uri(array('action'=>'geography_level2_insert', 'id'=>$id));
    $record = $this->objGeo2->getRow('id', $id);
} else {
    $hStr = $this->objLanguage->languageText('mod_ahis_geo2add','openaris');
    $formUri = $this->uri(array('action'=>'geography_level2_insert'));
    $record['geo3id'] = $record['name'] = '';
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $hStr;

$nameInput = new textinput('name',$record['name']);
$geo3Drop = new dropdown('geo3id');
$geo3Drop->addFromDB($geo3, 'name', 'id');
$geo3Drop->setSelected($record['geo3id']);

$sButton = new button('enter', $this->objLanguage->languageText('word_enter'));
$sButton->setToSubmit();
$backUri = $this->uri(array('action'=>'geography_level2_admin'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$sButton->setCSS('saveButton');
$bButton->setCSS('backButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_geolevelname','openaris').": ");
$objTable->addCell($nameInput->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_geolevel','openaris')." 3: ");
$objTable->addCell($geo3Drop->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($bButton->show());
$objTable->addCell($sButton->show());
$objTable->endRow();

$objForm = new form('geo2add', $formUri);
$objForm->addToForm($objTable->show());
$errorMsg = str_replace('name', $this->objLanguage->languageText('mod_ahis_geolevelname', 'openaris'), $this->objLanguage->languageText('mod_ahis_namerequired', 'openaris'));
$objForm->addRule('name', $errorMsg, 'nonnumeric');

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();