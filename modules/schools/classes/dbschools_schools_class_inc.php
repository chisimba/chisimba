<?php
/**
 *
 * Database access for schools detail
 *
 * Database access for schools. This is a sample database model class
 * that you will need to edit in order for it to work.
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
 * @package   schools
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
* Database access for schools
*
* Database access for schools. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   schools
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbschools_schools extends dbtable
{

    /**
    *
    * Intialiser for the schools database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_schools_schools');
        $this->table = 'tbl_schools_schools';
        $this->objUser = $this->getObject('useradmin_model2', 'security');
    }
    
    /**
     *
     * MEthod to get the number of records in the table
     * 
     * @access public
     * @return integer $count The number of records in the table 
     */
    public function getCount()
    {
        return $this->getRecordCount();
    }

    /**
     *
     * Method to get a list of schools for the autocompleter
     * 
     * @access public
     * @param string $search The seach term
     * @return array|boolean $array The array of school data | FALSE if empty
     */
    public function autocomplete($search)
    {
        $sql = "SELECT id, name FROM `$this->table` WHERE `name` LIKE '%$search%'";
        $data = $this->getArray($sql);
        
        $array = array();
        if ($this->getRecordCount() > 0)
        {
            foreach ($data as $line)
            {
                $list['label'] = $line['name'];
                $list['value'] = $line['id'];
                $array[] = $list;
            }
            return $array;
        }
        return FALSE;
    }
    
    /**
     * Method to a school details to the database
     * 
     * @access public
     * @param array @data The array of school detail data
     * @return string $id The id of the school detail added
     */
    public function insertSchool($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Method to return the school detail
     * 
     * @access public
     * @param integer $id The id of the school to get the detail for
     * @return array The array of school detail data
     */
    public function getSchool($id)
    {
        return $this->getRow('id', $id);
    }

    /**
     *
     * Method to delete school details
     * 
     * @access public
     * @param string $sid The id of the school to delete
     * return boolean 
     */
    public function deleteSchool($sid)
    {
        return $this->delete('id', $sid);
    }

    /**
     * Method to add school details to the database
     * 
     * @access public
     * @param array @data The array of school detail data
     * @return string $id The id of the school detail edited
     */
    public function updateSchool($id, $data)
    {
        return $this->update('id', $id, $data);
    }
    
    /**
     *
     * Method to return all schools by district
     * 
     * @access public
     * @param string $districtId The id of the district
     * @return array The array of school data 
     */
    public function getSchoolsByDistrict($districtId)
    {
        return $this->fetchAll(" WHERE `district_id` = '$districtId'" );        
    }
    
    /**
     *
     * Method to get all schools by province
     * 
     * @access public
     * @param string @provinceId The id of the province to get schools for
     * @return array 
     */
    public function getSchoolsByProvince($provinceId)
    {
        $sql = "SELECT *, s.id AS id, d.id AS districtId, p.id AS provinceId FROM $this->table AS s";
        $sql .= ' LEFT JOIN tbl_schools_districts AS d ON (s.district_id = d.id)';
        $sql .= ' LEFT JOIN tbl_schools_provinces AS p ON (d.province_id = p.id)';
        $sql .= " WHERE p.id = $provinceId";

        return $this->getArray($sql);       
    }    
}
?>