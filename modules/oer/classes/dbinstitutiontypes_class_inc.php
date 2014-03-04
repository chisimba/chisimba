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

class dbinstitutiontypes extends dbtable {

    function init() {
        parent::init("tbl_oer_institution_types");
    }

    function getInstitutionTypes() {
        $sql = "select * from tbl_oer_institution_types";
        return $this->getArray($sql);
    }

    function addType($type) {
        $data = array(
            'type' => $type,
        );
        return $this->insert($data);
    }
    
    /**
     *
     * Save a type when coming from edit
     * 
     * @param string $id The record id
     * @param string $type The institution type
     * @access public
     * @return boolean TRUE|FALSE
     * 
     */
    public function editType($id, $type)
    {
        $result = $this->update(
          'id', $id, array(
          'type' => $type)
        );
        return $result;
    }
    
    

   /*
    * This function takes a type Id an returns the type type
    * @param $typeId
    * return type
    */
    function getType($typeID){
        $sql = "SELECT * FROM tbl_oer_institution_types WHERE id='".$typeID."'";
        $type=$this->getArray($sql);
        return $type[0]['type'];
    }
    /*
     * Function to get the name of an institution-type
     * @param id id of the institution-type record
     * @return string institution-type name
     */

    function getInstitutionTypeName($id) {
        $sql = "SELECT type FROM tbl_oer_institution_types WHERE id='".$id."'";
        $institutionType = $this->getArray($sql);
        if (count($institutionType) > 0) {
            return $institutionType[0]['type'];
        } else {
            return Null;
        }
    }
    /*
     * Function to get the data of an institution-type
     * @param id id of the institution-type record
     * @return array of institution-type data
     */

    function getInstitutionTypeData($id) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE id='".$id."'";
        $institutiontype = $this->getArray($sql);
        if (count($institutiontype) > 0) {
            return $institutiontype[0];
        } else {
            return Null;
        }
    }

    /*
    * This function takes a type type an returns the first type ID if found
    * @param $typetype
    * return type
    */
    function findTypeID($type){
        $sql = "SELECT * FROM tbl_oer_institution_types WHERE type='".$type."'";
        $typeID=$this->getArray($sql);
        return $typeID[0]['id'];
    }
}
?>