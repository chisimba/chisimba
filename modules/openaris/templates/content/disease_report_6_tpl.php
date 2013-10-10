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
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_outbreak_tpl.php 13733 2009-06-23 11:04:26Z nic $
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
$objHeading->str = $this->objLanguage->languageText('mod_ahis_diseasereport', 'openaris')." #6";
$objHeading->type = 2;

$this->loadClass('radio','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$radio = new radio('outbreakEnded');
$radio->addoption('0', $this->objLanguage->languageText('word_ended')." ");
$radio->addoption('1', $this->objLanguage->languageText('word_continuing'));

$confirmSave = $this->objLanguage->languageText('mod_ahis_confirmsave', 'openaris');
$sButton = new button('enter', $this->objLanguage->languageText('word_next'), "javascript: return confirm('$confirmSave');");
$sButton->setCSS('saveButton');
$sButton->setToSubmit();
$backUri = $this->uri(array('action'=>'select_officer', 'outbreakCode'=>$outbreakCode));
$confirm = $this->objLanguage->languageText('mod_ahis_confirmcancel', 'openaris');
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: if(confirm('$confirm')) {document.location='$backUri'}");
$bButton->setCSS('cancelButton');

$subHead = "<span>".$this->objLanguage->languageText('mod_ahis_outbreakendedhead', 'openaris').":</span>";
$question = $this->objLanguage->languageText('mod_ahis_outbreakendedquestion', 'openaris');
$buttons = $bButton->show().$tab.$tab.$tab.$sButton->show();

$objForm = new form('reportForm', $this->uri(array('action' => 'passive_feedback', 'success'=>1)));
$objForm->addToForm("$subHead <br /> $question <br />".$radio->show()." <br /> $buttons");
$objForm->addRule('outbreakEnded', $this->objLanguage->languageText('mod_ahis_valoutbreakended', 'openaris'), 'required');

echo $objHeading->show()."<br />".$objForm->show();