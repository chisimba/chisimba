<?php
/**
 * ahis Report Overview Admin Template
 *
 * overview template for report admin
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
 * @version   $Id: report_admin_tpl.php 13592 2009-06-02 14:00:40Z nic $
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

$imageFolder = $this->objConfig->getsiteRoot()."skins/ahisskin/images";
$icon = $this->newObject('geticon','htmlelements');
if ($superUser) {
    $addLink = new link($addLinkUri);
    $addLink->link = "<img src='$imageFolder/$addLinkText.jpg' />";
    $addLink = $addLink->show();
} else {
    $addLink = '';
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $headingText;

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
$button->setCSS('goButton');
$uri = $this->uri(array('action'=>$action));
$cButton = new button('clear', $this->objLanguage->languageText('word_clear'), "javascript: document.location='$uri'");
$cButton->setCSS('clearButton');
$search = $this->objLanguage->languageText('word_search').": ".$objSearchStr->show()." ".$button->show()." ".$cButton->show();

$formTable = $this->getObject('htmltable', 'htmlelements');
$formTable->width = '50%';
$formTable->cssClass = "min50";
$formTable->cellspacing = 2;
$formTable->startRow();
$formTable->addCell($addLink);
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
$objTable->addHeaderCell($columnName);
$objTable->addHeaderCell($this->objLanguage->languageText('word_action'),'10%');
$objTable->endHeaderRow();

$class = 'odd';
$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');
if (!empty($data)) {
    foreach ($data as $datum) {
        if ($superUser) {
            $link = $icon->getDeleteIconWithConfirm($datum['id'],array('action'=>$deleteAction,'id'=>$datum['id']), 'openaris', $message);
            $link = $icon->getEditIcon($this->uri(array('action'=>$editAction, 'id'=>$datum['id'])))." $link";
        } else {
            $link = ' -- ';
        }
        $objTable->startRow($class);
        $objTable->addCell($datum[$fieldName]);
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
$backLink->link = "<img src='$imageFolder/back.jpg' />";
$linkTable = $this->newObject('htmltable', 'htmlelements');
$linkTable->width = '50%';
$linkTable->startRow();
$linkTable->addCell($addLink);
$linkTable->addCell($backLink->show(), NULL, NULL, 'right');
$linkTable->endRow();

$heading = $objHeading->show()."<hr />".$msg;
$body = $objForm->show().$objTable->show().$linkTable->show();

$objLayer = new layer();
$objLayer->addToStr($heading.$body);
$objLayer->align = 'center';

echo $objLayer->show();