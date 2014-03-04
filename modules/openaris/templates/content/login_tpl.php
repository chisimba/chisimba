<?php
/**
 * ahis Login Template
 *
 * Login template for Ahis module
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
 * @version   $Id: login_tpl.php 13821 2009-07-02 07:42:30Z nic $
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
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');

$objUsername = new textinput('username', NULL, 'text', 25);
$objPassword = new textinput('password', NULL, 'password', 25);
$objModule   = new textinput('mod', 'openaris', 'hidden');

$enterButton = new button('login',$this->objLanguage->languageText('word_enter'));
$enterButton->setToSubmit();
$enterButton->setCSS('goButton');
$clearButton = new button('clear',$this->objLanguage->languageText('word_clear'),
                          "javascript: $('input_username').value = $('input_password').value = '';");
$clearButton->setCSS('cancelButton');

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
$objTable = $this->getObject('htmltable','htmlelements');
$objTable->width = NULL;
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_username').": $tab");
$objTable->addCell($objUsername->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_password').": $tab");
$objTable->addCell($objPassword->show().$objModule->show());
$objTable->endRow();

$objTable->startRow();
$objTable->addCell("&nbsp;".$clearButton->show());
$objTable->addCell($enterButton->show(), NULL, 'top', 'right');
$objTable->endRow();

$objForm = new form('loginForm',$this->uri(array('action'=>'login'),'security'));
$objForm->addToForm($objTable->show());
$objForm->addRule('username',$this->objLanguage->languageText("mod_login_unrequired", "security"),'required');

$objLayer = new layer();
$objLayer->align = 'center';
$objLayer->addToStr("<br />".$objForm->show());

$versionString = $this->objLanguage->languageText('mod_ahis_version', 'openaris');
$version = "<br /><br /><br /><span class='admin'>$versionString</span>";

echo $objLayer->show().$version;