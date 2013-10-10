<?php
/**
 * ahis Meat Inspection Template
 *
 * Template to select passive outbreak reporting officer
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
 * @author    Samuel Onyach <sonyach@icsit.jkuat.ac.ke>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: meat_inspection_tpl.php
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
$title = 'Meat Inspection';
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label', 'htmlelements');

$msg = '';


$formAction = 'saveinspectiondata';
  
$buttonText = 'Save';

$form = new form ('add', $this->uri(array('action'=>$formAction)));

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;

//district name
$district = new textinput('district',$dist);
$district->extra='readonly';

$label = new label ('District:', 'district');
$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($district->show());
$formTable->endRow();
//date of inspection
$datePicker = $this->newObject('datepicker', 'htmlelements');
$datePicker->setName('inspectiondate');
$formTable->startRow();
$formTable->addCell('Inspection Date:');
$formTable->addCell($datePicker->show());
$formTable->endRow();

//number of cases
$label = new label ('Number of Cases:', 'input_no_of_cases');
$num_of_cases= new textinput('num_of_cases');

//number at risk
$label2 = new label ('Number at Risk:', 'input_no_at_risk');
$num_at_risk = new textinput('num_at_risk');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($num_of_cases->show()."&nbsp;");
$formTable->endRow();
$formTable->startRow();
$formTable->addCell($label2->show());
$formTable->addCell($num_at_risk->show());
$formTable->endRow();
$form->addToForm($formTable->show());
$form->addRule('inspectiondate', 'Please enter valid source', 'datenotfuture');
$form->addRule('num_of_cases', 'Please enter number of cases', 'required');
$form->addRule('num_of_cases', 'Please enter a valid number', 'numeric');
$form->addRule('num_at_risk', 'Please enter number at risk', 'required');
$form->addRule('num_at_risk', 'Please enter a valid number', 'numeric');
//buttons
$button = new button ('saveinspectiondata', 'Save');
$button->setToSubmit();
$button->setCSS('saveButton');

$backUri = $this->uri(array('action'=>'select_officer'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());
$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$msg.$form->show());

echo $objLayer->show();
if(($output=='yes')) {
        
        echo "<script type=\"text/javascript\">
        alert(\"Please enter valid inspection date\");

              </script> ";

}
?>