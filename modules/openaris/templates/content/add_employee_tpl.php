<?php
/**
 * ahis Add Employee Template
 *
 * File containing the add/edit employee template
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
 * @version   $Id: add_employee_tpl.php 13706 2009-06-19 15:05:11Z nic $
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

$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('checkbox','htmlelements');
$this->loadClass('layer','htmlelements');

if ($id) {
    $hStr = $this->objLanguage->languageText('word_edit')." ".$this->objLanguage->languageText('word_employee');
    $formUri = $this->uri(array('action'=>'employee_insert', 'id'=>$id));
    $record = $this->objUser->getRow('id', $id);
    if ($this->objUser->inAdminGroup($record['userid'])) {
        $record['adminuser'] = 1;
    } else {
        $record['adminuser'] = 0;
    }
    $ahisRecord = $this->objAhisUser->getRow('id', $id);
    if (empty($ahisRecord)) {
        $ahisRecord['fax'] = $ahisRecord['phone'] = $ahisRecord['email'] = '';
        $ahisRecord['locationid'] = $ahisRecord['departmentid'] = $ahisRecord['roleid'] =
        $ahisRecord['titleid'] = $ahisRecord['statusid'] = $ahisRecord['retired'] = '';
        $ahisRecord['ahisuser'] = $ahisRecord['superuser'] = 0;
        $ahisRecord['dateofbirth'] = $ahisRecord['datehired'] = $ahisRecord['dateretired'] = date('Y-m-d');
    }
} else {
    $hStr = $this->objLanguage->languageText('word_add')." ".$this->objLanguage->languageText('word_employee');
    $formUri = $this->uri(array('action'=>'employee_insert'));
    $record['surname'] = $record['firstname'] = $ahisRecord['statusid'] = $ahisRecord['titleid'] = $ahisRecord['retired'] =
    $record['username'] = $ahisRecord['locationid'] = $ahisRecord['departmentid'] = $ahisRecord['roleid'] = '';
    $ahisRecord['ahisuser'] = $ahisRecord['superuser'] = $record['adminuser'] = 0;
    $ahisRecord['dateofbirth'] = $ahisRecord['datehired'] = $ahisRecord['dateretired'] = date('Y-m-d');
    $ahisRecord['fax'] = $ahisRecord['phone'] = $ahisRecord['email'] = '';
        
}

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->type = 2;
$objHeading->str = $hStr;

if ($error) {
    $objMsg = $this->getObject('timeoutmessage','htmlelements');
    $objMsg->setMessage("<span class='error'>".$this->objLanguage->languageText('mod_ahis_unameandpword', 'openaris')."</span><br />");
    $objMsg->setTimeOut('0');
    $msg = $objMsg->show();
    
} else {
    $msg ='';
}

$surnameInput = new textinput('surname',$record['surname']);
$surnameInput->extra = "onkeyup='boxLimiter(this)'";
$nameInput = new textinput('name',$record['firstname']);
$nameInput->extra = "onkeyup='boxLimiter(this)'";
$usernameInput = new textinput('username',$record['username']);
$usernameInput->extra = "onkeyup='boxLimiter(this)'";
$passwordInput = new textinput('password', NULL, 'password');
$confirmInput = new textinput('confirm', NULL, 'password');
$faxInput = new textinput('fax',$ahisRecord['fax']);
$phoneInput = new textinput('phone',$ahisRecord['phone']);
$emailInput = new textinput('email',$ahisRecord['email']);

$retiredBox = new checkbox('retired', NULL, $ahisRecord['retired']);
$retiredBox->extra = "onchange = 'toggleRetiredDate();'";
if (!$ahisRecord['retired']) {
    $this->appendArrayVar('bodyOnLoad','toggleRetiredDate();');
}

$ahisBox = new checkbox('ahisuser', NULL, $ahisRecord['ahisuser']);
$ahisBox->extra = "onchange = 'toggleAhisUser();'";
if (!$ahisRecord['ahisuser']) {
    $this->appendArrayVar('bodyOnLoad','toggleAhisUser();');
}

$superBox = new checkbox('superuser', NULL, $ahisRecord['superuser']);
$superBox->extra = "onchange = 'toggleSuperUser();'";
if ($superDisabled) {
    $superBox->extra .= " disabled='true'";
    $superHidden = new textinput('superuser', $ahisRecord['superuser'], 'hidden');
    $superBox = $superBox->show().$superHidden->show();
} else {
    $superBox = $superBox->show();
}

$adminBox = new checkbox('adminuser', NULL, $record['adminuser']);
$adminBox->extra = "onchange = 'toggleAdminUser();'";

$titleDrop = new dropdown('titleid');
$titleDrop->addFromDB($titles, 'name', 'id');
$titleDrop->setSelected($ahisRecord['titleid']);
$statusDrop = new dropdown('statusid');
$statusDrop->addFromDB($status, 'name', 'id');
$statusDrop->setSelected($ahisRecord['statusid']);
$locationDrop = new dropdown('locationid');
$locationDrop->addFromDB($locations, 'common_name', 'id');
$locationDrop->setSelected($ahisRecord['locationid']);
$departmentDrop = new dropdown('departmentid');
$departmentDrop->addFromDB($departments, 'name', 'id');
$departmentDrop->setSelected($ahisRecord['departmentid']);
$roleDrop = new dropdown('roleid');
$roleDrop->addFromDB($roles, 'name', 'id');
$roleDrop->setSelected($ahisRecord['roleid']);

$birthDate = $this->newObject('datepicker','htmlelements');
$birthDate->setName('datebirth');
$birthDate->setDefaultDate($ahisRecord['dateofbirth']);
$hiredDate = $this->newObject('datepicker','htmlelements');
$hiredDate->setName('hireddate');
$hiredDate->setDefaultDate($ahisRecord['datehired']);
$retiredDate = $this->newObject('datepicker','htmlelements');
$retiredDate->setName('retireddate');
if (!$ahisRecord['dateretired']) {
    $ahisRecord['dateretired'] = date('Y-m-d');
}
$retiredDate->setDefaultDate($ahisRecord['dateretired']);

$sButton = new button('enter', $this->objLanguage->languageText('word_enter'));
$sButton->setToSubmit();
$sButton->setCSS('saveButton');
$backUri = $this->uri(array('action' => 'employee_admin'));
$bButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backUri'");
$bButton->setCSS('backButton');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->cssClass = "min50";
$objTable->cellspacing = 2;

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_surname').": ");
$objTable->addCell($surnameInput->show());
$objTable->addCell($this->objLanguage->languageText('phrase_firstname').": ");
$objTable->addCell($nameInput->show(), NULL, NULL, NULL, NULL, 'colspan=2');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_title').": ");
$objTable->addCell($titleDrop->show());
$objTable->addCell($this->objLanguage->languageText('word_status').": ");
$objTable->addCell($statusDrop->show(), NULL, NULL, NULL, NULL, 'colspan=2');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_location').": ");
$objTable->addCell($locationDrop->show());
$objTable->addCell($this->objLanguage->languageText('phrase_dob').": ");
$objTable->addCell($birthDate->show(), NULL, NULL, NULL, NULL, 'colspan=2');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_datehired').": ");
$objTable->addCell($hiredDate->show());
$objTable->addCell($this->objLanguage->languageText('phrase_dateretired').": ");
$objTable->addCell($retiredBox->show().$retiredDate->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_department').": ");
$objTable->addCell($departmentDrop->show());
$objTable->addCell($this->objLanguage->languageText('word_role').": ");
$objTable->addCell($roleDrop->show(), NULL, NULL, NULL, NULL, 'colspan=2');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_word_fax', 'openaris').": ");
$objTable->addCell($faxInput->show());
$objTable->addCell($this->objLanguage->languageText('mod_ahis_word_phone', 'openaris').": ");
$objTable->addCell($phoneInput->show(), NULL, NULL, NULL, NULL, 'colspan=2');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_word_email', 'openaris').": ");
$objTable->addCell($emailInput->show());
$objTable->addCell('', NULL, NULL, NULL, NULL, 'colspan=3');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_ahis_ahisuser','openaris').": ");
$objTable->addCell($ahisBox->show());
$objTable->addCell($this->objLanguage->languageText('mod_ahis_adminuser','openaris').": ");
$objTable->addCell($adminBox->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_username').": ");
$objTable->addCell($usernameInput->show());
$objTable->addCell($this->objLanguage->languageText('mod_ahis_superuser', 'openaris').": ");
$objTable->addCell($superBox);
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_password').": ");
$objTable->addCell($passwordInput->show());
$objTable->addCell($this->objLanguage->languageText('phrase_confirmpassword').": ");
$objTable->addCell($confirmInput->show(), NULL, NULL, NULL, NULL, 'colspan=2');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell($bButton->show());
$objTable->addCell($sButton->show());
$objTable->endRow();

$objForm = new form('employeeadd', $formUri);
$objForm->id = 'form_employeeadd';
$objForm->addToForm($objTable->show());
$objForm->addRule('surname', $this->objLanguage->languageText('mod_ahis_surnamerequired', 'openaris'), 'required');
$objForm->addRule('name', $this->objLanguage->languageText('mod_ahis_namerequired', 'openaris'), 'required');
$objForm->addRule('datebirth', $this->objLanguage->languageText('mod_ahis_valdatebirth', 'openaris'), 'datenotfuture');
$objForm->addRule('hireddate', $this->objLanguage->languageText('mod_ahis_valdatehired', 'openaris'), 'datenotfuture');
$objForm->addRule('phone', $this->objLanguage->languageText('mod_ahis_phonerequired', 'openaris'), 'required');
$objForm->addRule(array('confirm', 'password'), $this->objLanguage->languageText('mod_ahis_pwordmismatch', 'openaris'), 'compare');

$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar("headerParams", "<script type='text/javascript' src='$scriptUri'></script>");

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr />".$msg.$objForm->show());
$objLayer->align = 'center';

echo $objLayer->show();