<?php
/**
 * ahis control measure Admin Template
 *
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
 * @author    Isaac Oteyo <ioteyo@icsit.jkuat.ac.ke, isaacoteyo@gmail.com>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: ahis_controlmeasureadmin_tpl.php 12233 2009-10-09 10:57:26Z nic $
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

$this->loadClass('link','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('layer','htmlelements');

$icon = $this->newObject('geticon','htmlelements');
$addLink = new link($this->uri(array('action'=>'controlmeasures_add')));
$addLink->link = 'Control measure add';

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = 'Control measures admin';

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

$objSearchStr = new textinput('searchStr',$searchStr);
$button = new button('search', $this->objLanguage->languageText('word_go'));
$button->setToSubmit();
$uri = $this->uri(array('action'=>'controlmeasure_admin'));
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: document.location='$uri'");
$search = $this->objLanguage->languageText('word_search').": ".$objSearchStr->show()." ".$button->show()." ".$cButton->show();

$formTable = $this->getObject('htmltable', 'htmlelements');
$formTable->width = NULL;
$formTable->cssClass = "min50";
$formTable->cellspacing = 2;
$formTable->startRow();
$formTable->addCell($addLink->show());
$formTable->addCell($search, NULL, 'top', 'right');
$formTable->endRow();
$objForm = new form('searchForm', $uri);
$objForm->addToForm($formTable);
$objForm->extra = "style='margin: 0px;'";

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;
$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('phrase_controlmeasure'));
$objTable->addHeaderCell($this->objLanguage->languageText('phrase_abbreviation'));
$objTable->addHeaderCell($this->objLanguage->languageText('phrase_description'));
$objTable->addHeaderCell($this->objLanguage->languageText('word_action'),'20%');
$objTable->endHeaderRow();

$class = 'odd';
$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');
$objStatus = $this->getObject('status');
if (!empty($data)) {
    foreach ($data as $datum) {
		$link = $icon->getDeleteIconWithConfirm($datum['id'],array('action'=>$deleteAction,'id'=>$datum['id']), 'openaris', $message);
       		if ($allowEdit) {
            $link = $icon->getEditIcon($this->uri(array('action'=>$editAction, 'id'=>$datum['id'])))." $link";
        }
		
        $ahisUser = $this->objAhisUser->getRow('id', $datum['id']);
        $objTable->startRow($class);
        $objTable->addCell($datum['controlmeasure']);
        $objTable->addCell($datum['abbreviation']);
        $objTable->addCell($datum['description']);
        $objTable->addCell($link);
        $objTable->endRow();
        $class = ($class == 'odd')? 'even' : 'odd';
    }
} else {
    $objTable->startRow();
    $objTable->addCell("<span class='noRecordsMessage'>".$this->objLanguage->languageText('phrase_norecords')."</span>", NULL, 'top', NULL, NULL, "colspan='2'");
    $objTable->endRow();    
}

$backLink = new link($this->uri(array('action' => 'admin')));
$backLink->link = "  | ".$this->objLanguage->languageText('word_back');
$linkTable = $this->newObject('htmltable', 'htmlelements');
$linkTable->width = NULL;
$linkTable->cssClass = "min50";
$linkTable->startRow();
$linkTable->addCell($addLink->show());
$linkTable->addCell($backLink->show(), NULL, NULL, 'right');
$linkTable->endRow();

$heading = $objHeading->show()."<hr />".$msg;
$body = $objForm->show().$objTable->show().$linkTable->show();

$objLayer = new layer();
$objLayer->addToStr($heading.$body);
$objLayer->align = 'center';

echo $objLayer->show();