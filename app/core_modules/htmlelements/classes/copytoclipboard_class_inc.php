<?php
/**
 * Copy to Clipboard
 * 
 * This class generates javascript to enable a cross browser
 * copy to clipboard feature
 *
 * It only generates the JavaScript function for you.
 * Developers still need to code what they want copied
 *
 * Based on: http://www.jeffothy.com/weblog/clipboard-copy/
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
 * @package   htmlelements
 * @author  Tohir Solomons
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: tooltip_class_inc.php 2878 2007-08-14 13:42:57Z jsc $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* Copy to Clipboard
 * 
 * This class generates javascript to enable a cross browser
 * copy to clipboard feature
 *
 * It only generates the JavaScript function for you.
 * Developers still need to code what they want copied
 *
 * Based on: http://www.jeffothy.com/weblog/clipboard-copy/
 *
 * @author  Tohir Solomons
 * @package htmlelements
 */
class copytoclipboard extends object
{
    
    /**
    * The constructor.
    */
    public function init()
    { }
    
    public function show()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('copytoclipboard/copytoclipboard.js'));
    }


}

?>
