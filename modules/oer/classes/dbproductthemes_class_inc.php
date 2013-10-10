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
 * This provides a db layer that links themes to a  product
 *
 * @author davidwaf
 */
class dbproductthemes extends dbtable {

    function init() {
        parent::init("tbl_oer_product_themes");
    }

    /**
     * updates selected themes for this product. If the product has existing 
     * themes, they are deleted first and new entry inserted
     * 
     * @param string $data
     * @param string $contextcode
     */
    function updateProductThemes($themes, $productid) {

        $this->delete("productid", $productid);

        foreach ($themes as $theme) {
            $data = array(
                "productid" => $productid,
                "themeid" => $theme
            );
            $this->insert($data);
        }
    }

    /**
     * get all the institutions that belong to this group
     * 
     * @param string $contextcode
     * @return string product themes
     */
    function getProductThemes($producttheme) {
        $sql = "select * from tbl_oer_group_institutions
            where group_id = '" . $contextcode . "'";
        return $this->getArray($sql);
    }

}

?>
