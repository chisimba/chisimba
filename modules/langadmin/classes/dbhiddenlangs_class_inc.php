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
class dbhiddenlangs extends dbtable {

    function init() {
        parent::init('tbl_hiddenlangs');
    }

    function isHidden($langid) {
        $sql = "select * from tbl_hiddenlangs where langid = '".$langid."'";
        $data= $this->getArray($sql);
        return count($data) > 0 ? true: false;
    }

    function unhideLang($langid) {
        $sql = "delete from tbl_hiddenlangs where langid = '".$langid."'";
        return $this->getArray($sql);
    }

    function hideLang($langid){
        $this->unhideLang($langid);
        $data=array("langid"=>$langid);
        $this->insert($data);
    }
}

?>
