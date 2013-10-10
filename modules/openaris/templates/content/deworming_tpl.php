<?php
/**
 * ahis Passive Surveillance Outbreak Template
 *
 * Template for capturing passive surveillance outbreak data
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
 * @author    Joseph Gatheru<jgatheru@icsit.jkuat.ac.ke>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_outbreak_tpl.php 12903 2009-03-17 14:17:34Z nic $
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
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');

$header = new htmlHeading();
$header->type = 1;

$header->str = 'Deworming';
echo $header->show();
echo '<hr>';
$form = new form ('deworming', $this->uri(array('action'=>'deworming')));

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$label=new label('District','district');
$table->addCell($label->show(),120);

$input=new textinput('district');
$input->size=20;
$table->addCell($input->show());
$table->endRow();

$table->startRow();
$label=new label('Animal Classification','animal_classification');
$table->addCell($label->show(),120);

$dropdown=new dropdown('animal_classification');
$dropdown->addOption('Animal Class...');
$dropdown->addOption('...');
$dropdown->addOption('...');
$table->addCell($dropdown->show());
$table->endRow();

$table->startRow();
$label=new label('Number of Animals Dewormed','no_of_animals_dewormed');
$table->addCell($label->show(),120);


$input=new textinput('no_of_animals_dewormed');
$input->size=5;
$table->addCell($input->show());
$table->endRow();

$table->startRow();
$label=new label('Type of Antiemetic','type_of_antiemetic');
$table->addCell($label->show(),120);

$dropdown=new dropdown('type_of_antiemetic');
$dropdown->addOption('Antiemetic...');
$dropdown->addOption('...');
$dropdown->addOption('...');
$table->addCell($dropdown->show());
$table->endRow();

$table->startRow();

$label = new label ('Remarks', 'remarks');
$dose = new textinput('remarks');
$dose->size = 15;
$table->addCell($label->show(), 120);
$table->addCell($dose->show());
$table->endRow();

$save = new button ('save', 'Save');
$save->setToSubmit();
$cancel = new button ('reset', 'Cancel');
$cancel->setToReset();

$table->addCell('&nbsp;');
$table->addCell($save->show());
$table->addCell($cancel->show());
$table->endRow();


$form->addToForm($table->show());

echo $form->show();

?> 
