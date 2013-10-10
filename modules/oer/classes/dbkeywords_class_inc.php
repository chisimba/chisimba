<?php
/**
 * This class contains util methods for displaying full original product details
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
 * @version    0.001
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     davidwaf davidwaf@gmail.com
 */
/**
 * This is a DB layer that manages keywords
 *
 * @author davidwaf
 */
class dbkeywords extends dbtable {

    private $tableName = 'tbl_oer_product_keywords';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * this returns the key words
     */
    function getKeyWords() {
        $sql = "select * from $this->tableName";
        return $this->getArray($sql);
    }

    /**
     * inserts new key word into database
     * @param type $keyword
     * @return type 
     */
    function addKeyWord($keyword) {
        $data = array("keyword"=>$keyword);
        return $this->insert($data);
    }

}

?>
