<?php
/**
 * ahis Active Survaillance add sampling screen Template
 *
 * Template for capturing active surveillance for new sampling 
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
 * @author    Rosina Ntow <rntow@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: active_samplingview_tpl.php 
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

if ($id) {
    $hstr = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_edit')."  ".$this->objLanguage->languageText('word_samples');
    $formUri = $this->uri(array('action'=>'herdsampling_insert', 'id'=>$id));
    $record = $this->objSampling->getRow('id', $id);
    $sentdate = $record['sentdate'];
    $sampledate = $record['sampledate'];
    $recieveddate = $record['recievddate'];
    
} else {
    $hstr = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_add')."  ".$this->objLanguage->languageText('word_samples');
    $formUri = $this->uri(array('action'=>'herdsampling_insert'));
    $record['number'] = '';
    $record['sentdate'] = '';
    $record['sampledate'] = '';
    $record['recievddate'] = '';
    
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $hstr;
$objHeading->type = 2;


$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');


$addButton = new button('add', $this->objLanguage->languageText('word_add'));
$addButton->setToSubmit();
$backUri = $this->uri(array('action'=>'active_herdsampling'));
$backButton = new button('cancel', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");


$sampleDate = $this->newObject('datepicker','htmlelements');
$sampleDate->setName('sampledate');
$sampleDate->setDefaultDate($sampledate);



$sentDate = $this->newObject('datepicker','htmlelements');
$sentDate->setName('sentdate');
$sentDate->setDefaultDate($sentdate);
	
$recievedDate = $this->newObject('datepicker','htmlelements');
$recievedDate->setName('recieveddate');
$recievedDate->setDefaultDate($recieveddate);

$numberBox = new textinput('number', $record['number']);

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();

$objTable->addCell($this->objLanguage->languageText('phrase_datesamplessent'));
$objTable->addCell($sentDate->show());

$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_samplestaken'));
$objTable->addCell($numberBox->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_datesamplesreceived').": ");
$objTable->addCell($recievedDate->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_samplingdate').": $tab");
$objTable->addCell($sampleDate->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($backButton->show());
$objTable->addCell($addButton->show());
$objTable->endRow();


$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $formUri);
$objForm->addToForm($objTable->show());
$objForm->addRule('number', $this->objLanguage->languageText('mod_ahis_valnum', 'openaris'), 'numeric');
$objForm->addRule('number', $this->objLanguage->languageText('mod_ahis_valreq', 'openaris'), 'required');

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();

?>