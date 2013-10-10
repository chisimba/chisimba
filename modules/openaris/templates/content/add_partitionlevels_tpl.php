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
$this->loadClass('textarea','htmlelements');

if ($id) {
    $hStr = $this->objLanguage->languageText('word_edit')." ".$this->objLanguage->languageText('mod_ahis_partitionlevel','openaris');
    $objFormUri = $this->uri(array('action'=>'partitionlevel_update', 'id'=>$id));
    $record = $this->objPartitionLevel->getRow('id',$id);

    $sButton = new button('partitionlevel_update', 'Update');
    $sButton->setToSubmit();
    $sButton->setCSS('saveButton');
} else {
    $hStr = $this->objLanguage->languageText('word_add')." ".$this->objLanguage->languageText('mod_ahis_partitionlevel','openaris');
    $objFormUri = $this->uri(array('action'=>'partitionlevel_save'));
    $record['name'] = '';

    $sButton = new button('partitioncategory_save', 'Save');
    $sButton->setToSubmit();
    $sButton->setCSS('saveButton');
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $hStr;

$backUri = $this->uri(array('action'=>'partitionlevel_view'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;

//partition level
$label = new label ('Partition Level: ', 'partitionlevel');
$partitionlevel = new textinput('partitionlevel',$record['partitionlevel']);
$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($partitionlevel->show());
$objTable->endRow();

// partition category	
$label = new label ('Partition Category: ', 'partitioncategory');
$partitioncategory = new dropdown('partitioncategory');
$partitioncategory->setSelected($record['partitioncategory']);
$partitioncategory->addFromDB($partitioncategories, 'partitioncategory', 'id'); 

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($partitioncategory->show());
$objTable->endRow();

//Description
$label = new label ('Description: ', 'description');
$description = new textarea('description',$record['description']);

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($description->show());
$objTable->endRow();
		
$objTable->startRow();
$objTable->addCell($bButton->show());
$objTable->addCell($sButton->show());
$objTable->endRow();

$objForm = new form('partitionleveladd', $objFormUri);

//form validations
$objForm->addRule('partitionlevel', $this->objLanguage->languageText('mod_ahis_partitonlevelerror','openaris'),'required');
$objForm->addRule('partitioncategory', $this->objLanguage->languageText('mod_ahis_partitioncategoryiderror','openaris'),'required');

$objForm->addToForm($objTable->show());
$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();