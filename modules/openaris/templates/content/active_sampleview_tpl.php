<?php
/**
 * ahis Active Survaillance Samples screen Template
 *
 * Template for capturing active surveillance sample data
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
 * @version   $Id: active_herdview_tpl.php 
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
$objHeading->str = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_samples');
$objHeading->type = 2;


$this->loadClass('layer','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('textinput','htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objConfirm = $this->loadClass('confirm', 'utilities');

$objTable = $this->getObject('htmltable','htmlelements');
$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');


$addButton = new button('add', $this->objLanguage->languageText('word_add')." ".$this->objLanguage->languageText('word_another')." ".$this->objLanguage->languageText('word_sample'));
$addButton->setToSubmit();

$finUri = $this->uri(array('action'=>'active_feedback','success'=>1));
$finButton = new button('finish', $this->objLanguage->languageText('word_finish'), "javascript: document.location='$finUri'");


$numberBox = new textinput('number',$number);
$numberBox->extra = "readonly";
$newherdidBox = new textinput('newherdid',$newherdid,'hidden');
//$dataBox = new textinput('data',$data,'hidden');
//$idBox = new textinput('samplingid',$samplingid,'hidden');
$inputDate = $this->getObject('datepicker','htmlelements');
$inputDate->setDefaultDate($calendardate);


$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_samplingdate').": ");
$objTable->addCell($inputDate->show());


$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_samples').":");

$objTable->endRow();

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objTable->show());
$objLayer->align = 'center';

echo $objLayer->show();


$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = '60%';
$objTable->cssClass = 'min50';

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_sampleid'),'','','','heading');

$objTable->addCell($this->objLanguage->languageText('phrase_dateoftesting'), '', '', '', 'heading');

$objTable->addCell($this->objLanguage->languageText('word_species'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_age'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_sampletype'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_typeoftest'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_number'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_vaccinationhistory'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_action'), '', '', '', 'heading');

$objTable->endRow();
//print_r($data);
foreach($data as $var){
foreach($datan as $line){
if($var['id']==$line['newherdid']){
$objTable->startRow();
$objTable->addCell($line['sampleid']);
$objTable->addCell($line['testdate']);
$objTable->addCell($line['species']);
$objTable->addCell($line['age']);
$objTable->addCell($line['sampletype']);
$objTable->addCell($line['testtype']);
$objTable->addCell($line['number']);
$objTable->addCell($line['vachist']);

 $editUrl = $this->uri(array(
            'action' => 'active_addsample',
            'id' => $line['id'],
            'newherdid' => $newherdid
        ));
 $icons = $objIcon->getEditIcon($editUrl);
 $objIcon->title = $objLanguage->languageText('word_delete');
 $objIcon->setIcon('delete');
 $objConfirm = new confirm();
 $objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'sampleview_delete',
            'id' => $line['id'],
        )) , $message);
$icons.= $objConfirm->show();
$objTable->addCell($icons);
$objTable->endRow();
}
}
}
$objTable->startRow();
$objTable->addCell($addButton->show());
$objTable->addCell($finButton->show());
$objTable->addCell($newherdidBox->show());
//$objTable->addCell($dataBox->show());
$objTable->endRow();
$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $this->uri(array('action' => 'active_addsample')));
$objForm->addToForm($objTable->show());

$objLayer = new layer();
$objLayer->addToStr($objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();

?>
