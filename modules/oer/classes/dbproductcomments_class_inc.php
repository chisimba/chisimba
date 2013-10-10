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
 * @author     pwando paulwando@gmail.com
 */
/**
 * This class provides the interface to the database for managing product comments
 *
 * @author pwando
 */
class dbproductcomments extends dbTable {

    private $tableName = 'tbl_oer_productcomments';

    function init() {
        parent::init($this->tableName);
    }
    
    /**
     * gets all the product comments
     * 
     * @param type $productId
     * @return Array of the product comments
     */
    function getProductComments($productId) {
        $sql =
                "select * from $this->tableName where product_id = '$productId'";
        return $this->getArray($sql);
    }

    /**
     * returns comment as per specified id
     * 
     * @param string $Id comment id
     * @return array
     */

    function getComment($Id) {
        $sql = "select * from tbl_oer_productcomments where id = '$Id'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return null;
        }
    }
    /**
     * records user comments on a product
     * 
     * @param string $data - the comment details
     * @return string ID of the saved section
     */
    function addComment($data) {
        return $this->insert($data);
    }
}
?>