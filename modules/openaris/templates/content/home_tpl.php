<?php
/**
 * ahis home_tpl Template
 *
 * the home template for aris
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
 * @version   $Id: home_tpl.php 13826 2009-07-02 12:45:17Z nic $
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

$this->loadClass('link', 'htmlelements');

$objHeading = $this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 2;
$objHeading->str = $this->objLanguage->languageText('mod_ahis_homeheading', 'openaris');

$deLink = new link($this->uri(array('action'=>'select_officer')));
$deLink->link = $this->objLanguage->languageText('mod_ahis_enterdata', 'openaris');
$repLink = new link($this->uri(array('action'=>'view_reports')));
$repLink->link = $this->objLanguage->languageText('mod_ahis_viewreports', 'openaris');

$versionString = $this->objLanguage->languageText('mod_ahis_version', 'openaris');

$content = $objHeading->show()."<span class='admin'>".$this->objLanguage->languageText('mod_ahis_hometext', 'openaris').
            "</span><br /><br />".$deLink->show()."<br />".$repLink->show()."<br /><br /><br /><br />
            <span class='admin'>$versionString</span>";

echo $content;