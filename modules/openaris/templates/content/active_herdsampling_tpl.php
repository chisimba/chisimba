<?php
/**
 * ahis Active Survaillance Herd sampling Template
 *
 * Template for capturing and displaying sampling details of active surveillance of herd 
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
$objHeading->str = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_herd')." ".$this->objLanguage->languageText('word_sampling');
$objHeading->type = 2;


$this->loadClass('layer','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('textinput','htmlelements');
$objConfirm = $this->loadClass('confirm', 'utilities');
$objIcon = $this->newObject('geticon', 'htmlelements');

$objTable = $this->getObject('htmltable','htmlelements');
$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');

$addButton = new button('add', $this->objLanguage->languageText('word_add'));
$addButton->setToSubmit();
$backUri = $this->uri(array('action'=>'active_newherd'));
$backButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");




$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();
$objTable->addCell('<h6>'.$this->objLanguage->languageText('word_sampling')." $tab");
$objTable->addCell('&nbsp');
$objTable->addCell('&nbsp');
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


$newherdidBox = new textinput('newherdid',$newherdid,'hidden');

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_dateofsampling'),'','','','heading');

$objTable->addCell($this->objLanguage->languageText('phrase_samplestaken'), '', '', '', 'heading');

$objTable->addCell($this->objLanguage->languageText('phrase_datesamplessent'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_datesamplesreceived'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_action'), '', '', '', 'heading');
$objTable->addCell($newherdidBox->show());
$objTable->endRow();
foreach($data as $line){
$objTable->startRow();

$farmUri = $this->uri(array('action'=>'active_sampleview','newherdid'=>$newherdid,'number'=>$line['number'],'id'=>$line['id']));
$objLink = new link($farmUri);
$objLink->link = $line['sampledate'];
$objTable->addCell($objLink->show());
$objTable->addCell($line['number']);
$objTable->addCell($line['sentdate']);
$objTable->addCell($line['recievddate']);
$editUrl = $this->uri(array(
            'action' => 'active_addsampling',
            'id' => $line['id'],
            'activeid' => $activeid
        ));
 $icons = $objIcon->getEditIcon($editUrl);
 $objIcon->title = $objLanguage->languageText('word_delete');
 $objIcon->setIcon('delete');
 $objConfirm = new confirm();
 $objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'herdsampling_delete',
            'id' => $line['id'],
        )) , $message);
$icons.= $objConfirm->show();
$objTable->addCell($icons);
$objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($backButton->show());
$objTable->addCell($addButton->show());
$objTable->endRow();
$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $this->uri(array('action' => 'active_addsampling')));
$objForm->addToForm($objTable->show());

$objLayer = new layer();
$objLayer->addToStr($objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();

?>













