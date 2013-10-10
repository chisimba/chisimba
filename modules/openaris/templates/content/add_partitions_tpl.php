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

if (isset($output)) {
    $objMsg = $this->getObject('timeoutmessage','htmlelements');
    $objMsg->setHideTypeToNone();
    switch($output)
	{
		case 'yes':
		$objMsg->setMessage("Invalid Dates! Check your start and end dates.<br />");
		break;
	}
         
    $msg = $objMsg->show();

} else {
    $msg = '';
}

$parent=$this->getParam('parent');
$level=$this->getParam('level');
if ($id) {
    $hStr = $this->objLanguage->languageText('word_edit')." ".$this->objLanguage->languageText('mod_ahis_partitions','openaris');
    $objFormUri = $this->uri(array('action'=>'partition_update', 'id'=>$id,'level'=>$level,'parent'=>$parent));
    $record = $this->objPartition->getRow('id',$id);


    $sButton = new button('partition_update', 'Update');
    $sButton->setToSubmit();
    $sButton->setCSS('saveButton');
} else {
    $hStr = $this->objLanguage->languageText('word_add')." ".$this->objLanguage->languageText('mod_ahis_partitions','openaris');
    $objFormUri = $this->uri(array('action'=>'partition_save','level'=>$level,'parent'=>$parent));
    $record['name'] = '';

    $sButton = new button('partition_save', 'Save');
    $sButton->setToSubmit();
    $sButton->setCSS('saveButton');
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $hStr;

$backUri = $this->uri(array('action'=>'partition_view','level'=>$level,'parent'=>$parent));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;

//partition level
$label = new label ('Partition Level: ', 'partitionlevel');

$partitionlevel = new dropdown('partitionlevel');
$partitionlevel->setSelected('id');
$partitionlevel->addFromDB($partitionlevels, 'partitionlevel', 'id');

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($partitionlevel->show());
$objTable->endRow();

// partition code	
$label = new label ('Partition Code: ', 'partitioncode');
$partitioncode = new textinput('partitioncode',$record['partitioncode']);
$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($partitioncode->show());
$objTable->endRow();

//partition name
$label = new label ('Partition Name: ', 'partitionname');
$partitionname = new textinput('partitionname',$record['partitionname']);

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($partitionname->show());
$objTable->endRow();

//parent partition
$label = new label ('Parent Partition: ', 'parentpartition');
$parentpartition = new dropdown('parentpartition');
//$parentpartition->addFromDB($partitions, 'partitioncode', 'id');
$dt=$this->objPartition->getRow('parentpartition',$parent);
$parentpartition->addOption($parent,$dt['partitionname'],$parent);
$parentpartition->setSelected($dt['partitionname']);
$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($parentpartition->show());
$objTable->endRow();

//start date
$label = new label ('Start Date: ', 'startdate');
$startdate = $this->newObject('datepicker','htmlelements');
$startdate->name='startdate';

$objTable->startRow();
$objTable->addCell($label->show());
$objTable->addCell($startdate->show());
$objTable->endRow();
		
//end date
$label = new label ('End Date: ', 'enddate');
$enddate = $this->newObject('datepicker','htmlelements');
$enddate->name='enddate';

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
$objForm->addRule('partitionlevel', $this->objLanguage->languageText('mod_ahis_partitonleveliderror','openaris'),'required');
$objForm->addRule('partitioncode', $this->objLanguage->languageText('mod_ahis_partitioncodeerror','openaris'),'required');
$objForm->addRule('partitionname', $this->objLanguage->languageText('mod_ahis_partitionnameerror','openaris'),'required');

$objForm->addToForm($objTable->show());
$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();