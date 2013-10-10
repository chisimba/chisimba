<?php

/*
 * A block class to produce a language chooser
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
 */

/**

 * @author davidwaf
 */
class dblanguagetext extends dbtable {

    function init() {
        parent::init('tbl_languagetext');
    }

    function getLanguageTextItems() {
        $sql = "select * from tbl_languagetext";
        return $this->getArray($sql);
    }

    function getLanguageTextItem($code) {
        $sql = "select * from tbl_languagetext where code = '$code'";
        return $this->getArray($sql);
    }

}

?>
