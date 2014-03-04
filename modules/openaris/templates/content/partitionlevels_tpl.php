<?php
/**
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
 * @author    Joseph Gatheru
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: add_animalmovement_tpl.php 12780 2009-03-11 10:46:10Z rosina $
 * @link      http://avoir.uwc.ac.za, http://www.jkuat.ac.ke
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
// Create header object
$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
if($searchField=='listall'){
$pgTitle->str = $objLanguage->languageText('mod_ahis_listall', 'openaris');
} else {
$pgTitle->str = $objLanguage->languageText('mod_ahis_listall', 'openaris')."&nbsp;"./*'-'."&nbsp;".$objLanguage->languageText('mod_buddies_letter')."&nbsp;".*/ '"' . $searchField . '"' ;
}
// Create alphabet display object
$objAlphabet = &$this->getObject('alphabet', 'navigation');
$linkarray = array('action' => 'ListPartitionLevels', 'how' => 'partitionlevel', 'searchField' => 'LETTER');
$url = $this->uri($linkarray, 'partitionlevel');
// Create a table
$objTableClass = $this->newObject('htmltable', 'htmlelements');
$objTableClass->cellspacing = "2";
$objTableClass->cellpadding = "2";
$objTableClass->width = "70%";
$objTableClass->attributes = "border='0'";

$index = 0;
$rowcount = 0;
foreach ($partitionlevels as $partition) {
    $rowcount++; 
    // Set odd even colour scheme
    $class = ($rowcount % 2 == 0)?'odd':'even'; 
    
    // Get partition level
    $partitionlevel = $partition["partitionlevel"]; 

    // Get partition category
    $partitioncategory = $partition["partitioncategory"];

    // Get partition description
    $description = $partition["description"]; 

    // Get Date Created
    $createdon = $partition["createdon"]; 

    // Get Created By
    $createdby = $partition["createdby"];

    // Get Date Modified
    $modifiedon = $partition["modifiedon"]; 

    // Get Modified By
    $modifiedby = $partition["modifiedby"]; 

    // Add data to table
    $objTableClass->startRow();
    $objTableClass->addCell($index+1, '', '', '', $class);
    $objTableClass->addCell($partitionlevel, '', '', '', $class);
    $objTableClass->addCell($parentcategory, '', '', '', $class);
    $objTableClass->addCell($createdon, '', '', '', $class);
    $objTableClass->addCell($createdby, '', '', '', $class);
    $objTableClass->addCell($modifiedon, '', '', '', $class);
    $objTableClass->addCell($modifiedby, '', '', '', $class);
    $objTableClass->endRow();
    $index++;
} 
echo $objTableClass->show();
?>
