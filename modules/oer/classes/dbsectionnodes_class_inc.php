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
 * This class provides the interface to the database for managing product sections
 *
 * @author davidwaf
 */
class dbsectionnodes extends dbTable {

    private $tableName = 'tbl_oer_sectionnodes';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * this determines if a product has any sections created
     * @param type $productid The product to check sections for
     * @return type Boolean: True if sectiond exist, FALSE otherwise
     */
    function sectionsExist($productId) {
        $sql =
                "select count(id) as totalnodes from tbl_oer_sectionnodes where product_id = '$productId'";
        $results = $this->getArray($sql);

        if (count($results) > 0) {
            if (array_key_exists('first', $results)) {
                return $results['totalnodes'] > 0 ? TRUE : FALSE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * returns the nodes of the sections
     * @param type $productId
     * @return type 
     */
    function getSectionNodes($productId) {
        $sql =
                "select * from tbl_oer_sectionnodes where product_id = '$productId' order by level";
       
        return $this->getArray($sql);
    }

    function getSectionNode($sectionId) {
        $sql =
                "select * from tbl_oer_sectionnodes where id = '$sectionId'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return null;
        }
    }

    /**
     * creates a new section record for a product
     * @param type $data - the section details
     * @return type ID of the saved section
     */
    function addSectionNode($data) {
        return $this->insert($data);
    }

    /**
     *  Updates the section info using the supplied id
     * @param type $data
     * @param type $sectionId
     * @return type 
     */
    function updateSectionNode($data,$sectionId){
        return $this->update("id", $sectionId, $data);
    }
}

?>
