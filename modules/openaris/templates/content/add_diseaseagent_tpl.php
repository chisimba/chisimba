<?php
/**
 * ahis partitions add Template
 *
 * Template to add partitions
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
$this->loadClass('label','htmlelements');

if ($id) {
    $hStr = $this->objLanguage->languageText('mod_ahis_word_edit')." ".$this->objLanguage->languageText('mod_ahis_word_diseaseagent');
    $objFormUri = $this->uri(array('action'=>'diseaseagent_save', 'id'=>$id));
    $record = $this->objPartition->getRow('id', $id);
} else {
    $hStr = $this->objLanguage->languageText('mod_ahis_word_add')." ".$this->objLanguage->languageText('mod_ahis_word_diseaseagent');
    $objFormUri = $this->uri(array('action'=>'diseaseagent_save'));
    $record['name'] = '';
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $hStr;


$sButton = new button('enter', $this->objLanguage->languageText('word_enter'));
$sButton->setToSubmit();
$backUri = $this->uri(array('action'=>'diseaseagent_admin'));
$bButton = new button('back', $this->objLanguage->languageText('mod_ahis_word_back'), "javascript: document.location='$backUri'");
$sButton->setCSS('saveButton');
$bButton->setCSS('backButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;

//disease id
$label = new label ('Disease: ', 'disease');

$disease = new dropdown('disease');
$disease->addFromDB($diseases, 'diseasename', 'id');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($disease->show());
$objTable->endRow();

//agent id
$label = new label ('Agent: ', 'agent');

$agent = new dropdown('agent');
$agent->addFromDB($agents, 'agent', 'id');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($agent->show());
$objTable->endRow();

//description
$label = new label ('Description: ', 'description');
$description = new textinput('description');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($description->show());
$objTable->endRow();

//start date
$label = new label ('Start Date: ', 'startdate');
$startdate = new textinput('startdate');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($startdate->show());
$objTable->endRow();


//end date
$label = new label ('End Date: ', 'enddate');
$enddate = new textinput('enddate');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($enddate->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($bButton->show());
$objTable->addCell($sButton->show());
$objTable->endRow();

$objForm = new form('farmingsystemadd', $objFormUri);

//form validations
$objForm->addRule('partitionlevel', $this->objLanguage->languageText('mod_ahis_partitonlevelerror','openaris'),'required');
$objForm->addRule('partitioncode', $this->objLanguage->languageText('mod_ahis_partitioncodeerror','openaris'),'required');
$objForm->addRule('partitionname', $this->objLanguage->languageText('mod_ahis_partitionerror','openaris'),'required');

$objForm->addToForm($objTable->show());
$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();