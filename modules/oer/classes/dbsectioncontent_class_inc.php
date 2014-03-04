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
class dbsectioncontent extends dbTable {

    private $tableName = 'tbl_oer_sectioncontent';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * Returns content of a section given a node id
     * 
     * @param string $nodeId
     * @return string
     */
    function getSectionContent($nodeId) {
        $sql =
                "select * from tbl_oer_sectioncontent where node_id = '$nodeId'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return FALSE;
        }
    }

    /**
     * creates a new section record for a product
     * @param string $data - the section details
     * @return string ID of the saved section
     */
    function addSectionContent($data) {

        $id = $this->insert($data);

        return $id;
    }

    /**
     * updates the section data
     * @param string $data
     * @param string $id
     * @return string
     */
    function updateSectionContent($data, $id) {
        $results = $this->update("id", $id, $data);
        return $results;
    }

}

?>
