<?php
/**
 *
 * dsfdsafds
 *
 * dsafdsafdsfsa asdf dsafds fsdaf dsa
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
 * @package   jqeditable
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbjqeditable.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Interface to the jQuery Jeditable plugin for saving data
 *
 * Interface to the jQuery Jeditable plugin for building an editable area
 * or table cell. It allows a user to click and edit the content of different
 * xhtml elements. User clicks text on web page. Block of text becomes a form.
 * User edits contents and presses submit button. New text is sent to webserver
 * and saved. Form becomes normal text again. It is based on Jeditable by
 * Mika Tuupola available at:
 *   http://www.appelsiini.net/projects/jeditable
*
* @author Derek Keats
* @package jqeditable
*
*/
class dbjqeditable extends dbtable
{

    /**
    *
    * Intialiser for the jqeditable database connector
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here normally but not in this case
    }

    public function setTable($tableName, $moduleName)
    {

    }

    public function save()
    {
        $id = $this->getParam('id', FALSE);
        if ($id) {
            $arId = explode('|', $id);
            $parameter = $arId[0];
            $key = $arId[1];
        }
        $value = $this->getParam('value', NULL);
    }

}
?>
