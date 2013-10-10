<?php

/**
 * ahis Active Survaillance Sero Surveillance screen Template
 *
 * Template for active surveillance Sero Surveillance select screen
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
 * @version   $Id: sero_surveillance_tpl.php 
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

$this->loadClass('layer','htmlelements');
$this->loadClass('link', 'htmlelements');

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('word_sero')." ".$this->objLanguage->languageText('word_surveillance');
$objHeading->type = 2;

$backUri = $this->uri(array('action'=>'select_officer'));
$backButton = new button('cancel', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");


$searchUri = $this->uri(array('action'=>'active_search'));


$objLink = new link($searchUri);
$objLink->link = $this->objLanguage->languageText('word_search');
$searchLink = '<p>'.$objLink->show() .'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';



$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';


$objTable->startRow();
$objTable->addCell($searchLink);
$objTable->endRow();

$objTable->startRow();

$objTable->addCell($backButton->show());
$objTable->endRow();



$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$objTable->show());
$objLayer->align = 'center';

echo $objLayer->show();



















?>