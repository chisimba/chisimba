<?php
/**
 * ahis passive surveillance feedback Template
 *
 * Template to alert user after finishing passive surveillance capture
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
 * @version   $Id: passive_feedback_tpl.php 13592 2009-06-02 14:00:40Z nic $
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
$objHeading->str = $this->objLanguage->languageText('mod_ahis_passivefinished','openaris');
$objHeading->type = 2;

$this->loadClass('layer','htmlelements');
if ($success) {
    $result = $this->objLanguage->languageText('mod_ahis_passivesuccess','openaris')."<br />";
    $morePhrase = $this->objLanguage->languageText('phrase_addmore');
    $addUri = $this->uri(array('action'=>'select_officer'));

} else {
    $result = "<span class='error'>".$this->objLanguage->languageText('mod_ahis_passivefail','openaris')."</span><br />";
    $morePhrase = $this->objLanguage->languageText('word_back');
    $addUri = $this->uri(array('action'=>'passive_vaccine'));
}

$reportUri = $this->uri(array('action'=>'view_reports'));
$buttonMore = new button('more', $morePhrase, "window.location='$addUri';");
$buttonMore->setCSS('captureMoreButton');
$buttonReport = new button('report',$this->objLanguage->languageText('mod_ahis_viewreports', 'openaris'), "window.location='$reportUri';");
$buttonReport->setCSS('viewReportsButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;

$objTable->startRow();
$objTable->addCell($result, NULL, NULL, NULL, NULL, "colspan=2");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($buttonMore->show());
$objTable->addCell($buttonReport->show(),NULL,'top','right');
$objTable->endRow();

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objTable->show());
$objLayer->align = 'center';

echo $objLayer->show();