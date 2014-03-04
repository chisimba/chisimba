<?php
/**
 * ahis Active Survaillance Herd Details for a territory screen Template
 *
 * Template for capturing and displaying active surveillance herd data
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
 * @version   $Id: active_newherd_tpl.php 
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
$objHeading->str = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_new')." ".$this->objLanguage->languageText('word_farm');
$objHeading->type = 2;


$this->loadClass('layer','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('textinput','htmlelements');
$objConfirm = $this->loadClass('confirm', 'utilities');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objTable = $this->getObject('htmltable','htmlelements');
$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');
if (isset($success)) {

    $objMsg = $this->getObject('timeoutmessage','htmlelements');
    $objMsg->setHideTypeToNone();
    switch ($success) {
        case 1:
            $objMsg->setMessage($this->objLanguage->languageText('mod_ahis_added', 'openaris')."<br />");
            break;
        case 2:
            $objMsg->setMessage($this->objLanguage->languageText('mod_ahis_deleted', 'openaris')."<br />");
            break;
        case 3:
            $objMsg->setMessage($this->objLanguage->languageText('mod_ahis_updated', 'openaris')."<br />");

            break;
        case 4:
            $objMsg->setMessage("<span class='error'>".$this->objLanguage->languageText('mod_ahis_duplicate', 'openaris')."</span><br />");
            $objMsg->setTimeOut('0');
            break;
    }

    $msg = $objMsg->show();

} else {
    $msg = '';
}

$addUri = $this->uri(array('action'=>'active_addherd', 'activeid'=>$activeid));
$addButton = new button('add', $this->objLanguage->languageText('word_add'), "javascript: document.location='$addUri'");

$finUri = $this->uri(array('action'=>'active_feedback','success'=>1));
$finButton = new button('cancel', $this->objLanguage->languageText('word_finish'), "javascript: document.location='$finUri'");


$campBox = new dropdown('campName');
$campBox->addOption($campName);
$campBox->extra = 'disabled';


$diseaseBox = new dropdown('disease');
$diseaseBox->addFromDB($arraydisease, 'name', 'id');
$diseaseBox->setSelected($disease);
$diseaseBox->extra = 'disabled';

$officerDrop = new dropdown('officerId');
$officerDrop->addFromDb($userList,'name','userid');
$officerDrop->setSelected($officerId);
$officerDrop->extra = 'disabled';
 

$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_campaign')." ".$this->objLanguage->languageText('word_name').":");
$objTable->addCell($campBox->show());
$objTable->addCell('&nbsp');
$objTable->addCell('&nbsp');
$objTable->addCell($this->objLanguage->languageText('word_disease').":");
$objTable->addCell($diseaseBox->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris').": $tab");
$objTable->addCell($officerDrop->show());
$objTable->addCell('');
$objTable->addCell('');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('');
$objTable->addCell('');
$objTable->endRow();

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$msg."<hr class='openaris' />".$objTable->show());
$objLayer->align = 'center';

echo $objLayer->show();

$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = '60%';
$objTable->cssClass = 'min50';

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_farming')." ".$this->objLanguage->languageText('word_localities'),'','','','heading');

$objTable->addCell($this->objLanguage->languageText('phrase_geolevel2'), '', '', '', 'heading');

$objTable->addCell($this->objLanguage->languageText('phrase_geolevel3'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_farm')." ".$this->objLanguage->languageText('word_name'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_farming')." ".$this->objLanguage->languageText('word_system'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_action'), '', '', '', 'heading');
$objTable->endRow();


foreach($herd as $line){
$objTable->startRow();
$objTable->addCell($line['territory']);
$objTable->addCell($line['geolevel2']);
$objTable->addCell($line['geolevel3']);
$farmUri = $this->uri(array('action'=>'active_sampleview','id'=>$line['id']));
$objLink = new link($farmUri);
$objLink->link = $line['farmname'];
$objTable->addCell($objLink->show());
$objTable->addCell($line['farmingtype']);
$objIcon->title = $objLanguage->languageText('word_edit');
 $editUrl = $this->uri(array(
            'action' => 'active_addherd',
            'id' => $line['id'],
            'activeid' => $activeid
        ));
 $icons = $objIcon->getEditIcon($editUrl);
 $objIcon->title = $objLanguage->languageText('word_delete');
 $objIcon->setIcon('delete');
 $objConfirm = new confirm();
 $objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'newherd_delete',
            'id' => $line['id'],
        )) , $message);
$icons.= $objConfirm->show();
$objTable->addCell($icons);

}

$objTable->startRow();
$objTable->addCell('&nbsp');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($addButton->show());
$objTable->addCell($finButton->show());
$objTable->endRow(); 


 

$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $this->uri(array('action' => 'active_herddetails')));
$objForm->addToForm($objTable->show());

$objLayer = new layer();
$objLayer->addToStr($objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();

?>













