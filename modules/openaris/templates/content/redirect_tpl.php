<?php
/**
 * Ahis redirect Template
 *
 * template to redirect user when desired area not accessible for AHIS module
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
 * @version   $Id: redirect_tpl.php 12233 2009-01-28 10:57:26Z nic $
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

$objHeader = $this->getObject('htmlheading','htmlelements');
$objHeader->str = $message;
$link = new link($location);
$link->link = $this->objLanguage->languageText('word_here');
$body = $this->objLanguage->languageText('mod_ahis_redirect', 'openaris')." ".$link->show();
$redirectJS = "<script type='text/javascript'>setTimeout(\"document.location = '$location'\", 5000);</script>";
$this->appendArrayVar('headerParams', str_replace('&amp;','&',$redirectJS));
echo $objHeader->show().$body;
?>