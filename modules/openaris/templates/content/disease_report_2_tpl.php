<?php
/**
 * ahis Passive Surveillance Outbreak Template
 *
 * Template for capturing passive surveillance outbreak data
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
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_outbreak_tpl.php 13733 2009-06-23 11:04:26Z nic $
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

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('mod_ahis_diseasereport', 'openaris')." #2";
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('fieldset','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('link', 'htmlelements');

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objTableArea1 = $this->getObject('htmltable','htmlelements');
$objTableArea1->cellspacing = 2;
$objTableArea1->width = NULL;
$objTableArea1->cssClass = 'areatable';

$objTableArea1->startHeaderRow();
$objTableArea1->addHeaderCell($this->objLanguage->languageText('mod_ahis_outbreakcode', 'openaris')." ", NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('phrase_partitiontype')." ", NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('mod_ahis_partitionlevel', 'openaris')." ", NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('mod_ahis_partitionname', 'openaris')." ", NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('word_month')." ", NULL, NULL, 'center');
$objTableArea1->addHeaderCell($this->objLanguage->languageText('word_year')." &nbsp;", NULL, NULL, 'center');
$objTableArea1->endHeaderRow();

$class = 'odd';
foreach ($outbreaks as $outbreak) {
    $objTableArea1->startRow($class);
    $outbreakcode= $outbreak['outbreakCode'];
    $LinkUri = $this->uri(array('action'=>'disease_report_screen_2','outbreakCode1'=>$outbreakcode));
    $objLink = new link($LinkUri);
    $objLink->link = $outbreak['outbreakCode'];

    $objTableArea1->addCell($objLink->show(), NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['partitionType'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['partitionLevel'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['partitionName'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['month'], NULL, NULL, 'center');
    $objTableArea1->addCell($outbreak['year'], NULL, NULL, 'center');
    $objTableArea1->endRow();
    //$class = ($class == 'odd')? 'even' : 'odd';
}

$countryBox = new textinput('countryId',$countryId,'hidden');
$outbreakCodeBox = new textinput('outbreakCode', $outbreakCode);
$outbreakCodeBox->extra = 'readonly';
$outbreakCodeBox->setCss('passive_surveillance outbreakcode');

$latitudeBox = new textinput('lattitude', $latitude);
$latitudeBox->setCss('geo');
$latitudeBox->extra = 'onchange = \'javascript:valdirection("latt");\'';
$longitudeBox = new textinput('longitude', $longitude);
$longitudeBox->setCss('geo');
$longitudeBox->extra = 'onchange = \'javascript:valdirection("long");\'';

$latDirecDrop = new dropdown('latDirec');
$latDirecDrop->addOption('N', 'N');
$latDirecDrop->addOption('S', 'S');
$latDirecDrop->setSelected($latDirec);
$latDirecDrop->cssClass = 'geodrop';

$longDirecDrop = new dropdown('longDirec');
$longDirecDrop->addOption('E', 'E');
$longDirecDrop->addOption('W', 'W');
$longDirecDrop->setSelected($longDirec);
$longDirecDrop->cssClass = 'geodrop';

$localityNameBox = new textinput('localityName', $localityName);
$localityNameBox->setCss('passive_surveillance');

$localityTypeDrop = new dropdown('localityTypeId');
$localityTypeDrop->addFromDB($arrayLocalityType, 'locality_type', 'id');
$localityTypeDrop->setSelected($localityTypeId);
$localityTypeDrop->cssClass = 'passive_surveillance';

$farmingSystemDrop = new dropdown('farmingSystemId');
$farmingSystemDrop->addFromDB($arrayFarmingSystem, 'farmingsystem', 'id');
$farmingSystemDrop->setSelected($farmingSystemId);
$farmingSystemDrop->cssClass = 'passive_surveillance';

$createdBox = new textinput('createdBy', $createdBy, 'text');
$createdBox->setCss('passive_surveillance');
$createdDateBox = new textinput('createdDate', $createdDate, 'text');
$createdDateBox->setCss('passive_surveillance');
$modifiedBox = new textinput('modifiedBy', $modifiedBy, 'text');
$modifiedBox->setCss('passive_surveillance');
$modifiedDateBox = new textinput('modifiedDate', $modifiedDate, 'text');
$modifiedDateBox->setCss('passive_surveillance');
$createdBox->extra = $createdDateBox->extra = $modifiedBox->extra = $modifiedDateBox->extra = 'disabled';

$objTableArea2 = $this->newObject('htmltable','htmlelements');
$objTableArea2->cellspacing = 2;
$objTableArea2->width = '99%';

$nextUri = $this->uri(array('action'=>'disease_report_screen_3', 'outbreakCode'=>$outbreakCode));
if (count($numloc) > 0) {
    $function = "javascript: document.location='$nextUri'";
} else {
    $message = $this->objLanguage->languageText('mod_ahis_mustaddlocality', 'openaris');
    $function = "javascript: alert('$message')";
}
$sButton = new button('enter', $this->objLanguage->languageText('word_next'), $function);
$sButton->setCSS('nextButton');
$backUri = $this->uri(array('action'=>'passive_surveillance', 'outbreakCode'=>$outbreakCode));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: clearDiseaseLocality()");
$cButton->setCSS('clearButton');
$aButton = new button('add', $this->objLanguage->languageText('word_add'));
$aButton->setCSS('addButton');
$aButton->setToSubmit();


$objTableArea2->startRow();
$objTableArea2->addCell("<span class='outbreakcode'>".$this->objLanguage->languageText('mod_ahis_outbreakcode', 'openaris')."</span>");
$objTableArea2->addCell($outbreakCodeBox->show());
$objTableArea2->addCell($this->objLanguage->languageText('word_latitude'));
$objTableArea2->addCell($latitudeBox->show()." ".$latDirecDrop->show());
$objTableArea2->addCell($this->objLanguage->languageText('word_createdby'));
$objTableArea2->addCell($createdBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell($this->objLanguage->languageText('mod_ahis_localitytype', 'openaris'));
$objTableArea2->addCell($localityTypeDrop->show());
$objTableArea2->addCell($this->objLanguage->languageText('word_longitude'));
$objTableArea2->addCell($longitudeBox->show()." ".$longDirecDrop->show());
$objTableArea2->addCell($this->objLanguage->languageText('phrase_createddate'));
$objTableArea2->addCell($createdDateBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell($this->objLanguage->languageText('mod_ahis_localityname', 'openaris'));
$objTableArea2->addCell($localityNameBox->show());
$objTableArea2->addCell($this->objLanguage->languageText('phrase_farmingsystem'));
$objTableArea2->addCell($farmingSystemDrop->show());
$objTableArea2->addCell($this->objLanguage->languageText('word_modifiedby'));
$objTableArea2->addCell($modifiedBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell(' ');
$objTableArea2->addCell(' ');
$objTableArea2->addCell(' ');
$objTableArea2->addCell(' ');
$objTableArea2->addCell($this->objLanguage->languageText('phrase_modifieddate'));
$objTableArea2->addCell($modifiedDateBox->show());
$objTableArea2->addCell($countryBox->show());
$objTableArea2->endRow();
$objTableArea2->startRow();
$objTableArea2->addCell($cButton->show().$tab.$bButton->show().$tab.$aButton->show().$tab.$sButton->show(), NULL, 'top', 'center', NULL, 'colspan="6"');
$objTableArea2->endRow();


$localitySet = new fieldset('localitySet');
$localitySet->setExtra('class="diseasereport"');
$localitySet->setLegend($this->objLanguage->languageText('mod_ahis_localitydataentry', 'openaris'));
$localitySet->addContent($objTableArea2->show());

$objForm = new form('reportForm', $this->uri(array('action' => 'add_diseaselocality')));
$objForm->addToForm($localitySet->show());
$objForm->addRule('localityName', $this->objLanguage->languageText('mod_ahis_vallocalityname', 'openaris'), 'required');
$objForm->addRule('lattitude', $this->objLanguage->languageText('mod_ahis_vallatitude', 'openaris'), 'numeric');
$objForm->addRule('longitude', $this->objLanguage->languageText('mod_ahis_vallongitude', 'openaris'), 'numeric');

$objTableArea3 = $this->newObject('htmltable','htmlelements');
$objTableArea3->cellspacing = 2;
$objTableArea3->width = NULL;
$objTableArea3->cssClass = 'areatable widetable';

$objTableArea3->startHeaderRow();
$objTableArea3->addHeaderCell($this->objLanguage->languageText('mod_ahis_outbreakcode', 'openaris')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('mod_ahis_localitytype', 'openaris')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('mod_ahis_localityname', 'openaris')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('word_latitude')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('word_direction')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('word_longitude')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('word_direction')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_farmingsystem')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_createdby')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_createddate')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_modifiedby')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->addHeaderCell($this->objLanguage->languageText('phrase_modifieddate')." &nbsp;", NULL, NULL, 'center');
$objTableArea3->endHeaderRow();

$class = 'odd';
if (!empty($diseaseLocalities)) {
    foreach ($diseaseLocalities as $locality) {
        $localityType  = $this->objLocalityType->getRow('id', $locality['localitytypeid']);
        $farmingSystem = $this->objFarmingSystem->getRow('id', $locality['farmingsystemid']);
        $objTableArea3->startRow($class);
        $objTableArea3->addCell($locality['outbreakcode'], NULL, NULL, 'center');
        $objTableArea3->addCell($localityType['locality_type'], NULL, NULL, 'center');
        $objTableArea3->addCell($locality['name'], NULL, NULL, 'center');
        $objTableArea3->addCell($locality['latitude'], NULL, NULL, 'center');
        $objTableArea3->addCell($locality['latdirection'], NULL, NULL, 'center');
        $objTableArea3->addCell($locality['longitude'], NULL, NULL, 'center');
        $objTableArea3->addCell($locality['longdirection'], NULL, NULL, 'center');
        $objTableArea3->addCell($farmingSystem['farmingsystem'], NULL, NULL, 'center');
        $objTableArea3->addCell($this->objUser->Username($locality['created_by']), NULL, NULL, 'center');
        $objTableArea3->addCell($locality['date_created'], NULL, NULL, 'center');
        $modifier = ($locality['modified_by'] == NULL)? '' : $this->objUser->Username($locality['modified_by']);
        $objTableArea3->addCell($modifier, NULL, NULL, 'center');
        $objTableArea3->addCell($locality['date_modified'], NULL, NULL, 'center');
        $objTableArea3->endRow();
        //$class = ($class == 'odd')? 'even' : 'odd';
    }
} else {
    $objTableArea3->startRow();
    $objTableArea3->addCell("<i>".$this->objLanguage->languageText('phrase_norecords')."</i>", NULL, NULL, 'left', NULL, 'colspan="6"');
    $objTableArea3->endRow();         
}

$bSet = new fieldset('bSet');
$bSet->setExtra('class="diseasereport"');
$bSet->setLegend($this->objLanguage->languageText('mod_ahis_outbreakhistory', 'openaris'));
$bSet->addContent("<div class='scroll5'>".$objTableArea1->show()."</div>");
$cSet = new fieldset('cSet');
$cSet->setExtra('class="diseasereport"');
$cSet->setLegend($this->objLanguage->languageText('mod_ahis_localitydataview', 'openaris'));
$cSet->addContent("<div class='scroll5'>".$objTableArea3->show()."</div>");

$this->objJquery = $this->getObject('jquery', 'jquery');
$this->objJquery->loadTablesorterPlugin();
$scriptUri = $this->getResourceURI('util.js');
$script = "jQuery(document).ready(function() { jQuery('.areatable').tablesorter(); });";
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");
$this->appendArrayVar('headerParams', "<script type='text/javascript'>$script</script>");
				
$content = $objForm->show()."<!--hr class='blue' /-->".$bSet->show().$cSet->show();
echo $objHeading->show().$content;