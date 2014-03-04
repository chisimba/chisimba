<?php
/**
 * ahis Add Animal Slaughter Template
 *
 * File containing the Add Animal Slaughter Statistics template
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
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: slaughter_tpl.php 
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
$title = 'Animal Slaughter';
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $title;
$objHeading->type = 2;

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('textarea','htmlelements');
$formAction = 'animal_slaughter_save';  
$buttonText = 'Save';

// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;

//district name
$district = new textinput('district',$dist);
$district->extra='readonly';
$label = new label ('District:','district');
$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;','right');
$formTable->addCell($district->show(),'left');
$formTable->endRow();

$label = new label ('Number of Cattle:', 'input_no_cattle');
$num_cattle = new textinput('num_cattle');
$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;','right');
$formTable->addCell($num_cattle->show(),'left');
$formTable->endRow();

$label = new label ('Number of Sheep:', 'input_no_sheep');
$num_sheep = new textinput('num_sheep');
$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;','right');
$formTable->addCell($num_sheep->show(),'left');
$formTable->endRow();

$label = new label ('Number of Goats:', 'input_no_goat');
$num_goats = new textinput('num_goats');
$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;','right');
$formTable->addCell($num_goats->show(),'left');
$formTable->endRow();

$label = new label ('Number of Pigs:', 'input_no_pigs');
$num_pigs = new textinput('num_pigs');
$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;','right');
$formTable->addCell($num_pigs->show(),'left');
$formTable->endRow();

$label = new label ('Number of Poultry:', 'input_no_poultry');
$num_poultry = new textinput('num_poultry');
$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;','right');
$formTable->addCell($num_poultry->show(),'left');
$formTable->endRow();

$label = new label ('Other:', 'input_no_other');
$other = new textinput('other');

$label1 = new label ('&nbsp;&nbsp;&nbsp;Number:&nbsp;&nbsp;&nbsp;', 'input_no');
$name = new textinput('name');

$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;');
$formTable->addCell($other->show());
$formTable->addCell($label1->show());
$formTable->addCell($name->show());

$formTable->endRow();

$label = new label ('Remarks:', 'remarks');
$remarksBox = new textarea('remarks', $remarks, 4, 40);

$formTable->startRow();
$formTable->addCell($label->show().'&nbsp;&nbsp;&nbsp;');
$formTable->addCell($remarksBox->show(),NULL, NULL, NULL, NULL, 'colspan="4"');
$formTable->endRow();


$form->addToForm($formTable->show());
$form->addRule('num_cattle', 'Please enter valid number ', 'required');
$form->addRule('num_sheep', 'Please enter valid number ', 'required');
$form->addRule('num_goats', 'Please enter valid number ', 'required');
$form->addRule('num_pigs', 'Please enter valid number ', 'required');
$form->addRule('num_poultry', 'Please enter valid number ', 'required');
$form->addRule('num_cattle', 'Please enter valid number ', 'numeric');
$form->addRule('num_sheep', 'Please enter valid number ', 'numeric');
$form->addRule('num_goats', 'Please enter valid number ', 'numeric');
$form->addRule('num_pigs', 'Please enter valid number ', 'numeric');
$form->addRule('num_poultry', 'Please enter valid number ', 'numeric');
$form->addRule('other', 'Please enter valid value ', 'nonnumeric');
$form->addRule('name', 'Please enter valid number ', 'numeric');
$form->addRule('name', 'Please enter valid number ', 'required');

//buttons
$button = new button ('animal_slaughter_save', 'Save');
$button->setToSubmit();
$button->setCSS('saveButton');

$backUri = $this->uri(array('action'=>'select_officer'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$form->show());

echo $objLayer->show();
?>