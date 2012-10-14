<?php
/**
 * div_class_inc.php
 *
 * This file contains the div class
 *
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
 * @package   htmlelements
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Div Class
 *
 * Class displays a text input with no name or values
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:$
 * @link      http://avoir.uwc.ac.za
*/

class div {

    /**
     * init function for compatability
     *
     * @return void
     * @access public
     */
    function init(){

    }


    /**
     * Method to show a textinput referencing variables which
     * dont exist.
     *
     * @return string Return description (if any) ...
     * @access public
     */
    function show(){
        $str='<input type="something" value="'.$this->value.'"';
        $str.=' name="'.$this->name.'"';
        $str.=' size="'.$this->size.'"';
        $str.=' width="'.$this->width.'"';
        $str.=' class="'.$this->ccsclass.'"';
        $str.='>';
        return $str;
    }
}
?>